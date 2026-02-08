<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Meja;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderCustomerController extends Controller
{
    /**
     * CREATE ORDER (Customer Order dari Web)
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'      => 'required|string|max:100',
            'order_type'         => 'required|in:dine_in,takeaway',
            'table_number'       => $request->order_type === 'dine_in' ? 'required|numeric' : 'nullable',
            'items'              => 'required|array|min:1',
            'items.*.menu_id'    => 'required|numeric',
            'items.*.menu_name'  => 'required|string',
            'items.*.menu_price' => 'required|numeric',
            'items.*.qty'        => 'required|numeric|min:1',
            'subtotal'           => 'required|numeric|min:1',
            'tax'                => 'required|numeric|min:0',
            'total'              => 'required|numeric|min:1',
            'notes'              => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // VALIDASI MEJA
            $meja = null;

            if ($request->order_type === 'dine_in') {
                $meja = Meja::where('meja_id', $request->table_number)->first();

                if (!$meja) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Meja tidak ditemukan'
                    ], 422);
                }

                if ($meja->meja_status === 'terisi') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Meja sedang digunakan'
                    ], 422);
                }
            }

            // SIMPAN ORDER (status pending sampai pembayaran berhasil)
            $order = Order::create([
                'order_csname'  => $request->customer_name,
                'order_meja'    => $meja ? $meja->meja_id : null,
                'order_total'   => $request->total,
                'order_qty'     => collect($request->items)->sum('qty'),
                'order_change'  => 0,
                'order_type'    => $request->order_type,
                'order_message' => $request->notes,
                'order_metode'  => 'MIDTRANS',
                'order_channel' => null, // akan diisi setelah payment
                'order_status'  => 'pending',
                'created_at'    => now()
            ]);

            // GENERATE KODE TRANSAKSI
            $kodeTransaksi = 'TRX-' . now()->format('Ymd') . '-' . str_pad($order->order_id, 4, '0', STR_PAD_LEFT);

            // SIMPAN TRANSAKSI
            $transaksi = Transaksi::create([
                'transaksi_orderid' => $order->order_id,
                'transaksi_csname'  => $request->customer_name,
                'transaksi_total'   => $request->total,
                'transaksi_amount'  => 0,
                'transaksi_change'  => 0,
                'transaksi_message' => $request->notes,
                'transaksi_status'  => 'pending',
                'transaksi_channel' => null, // akan diisi setelah payment
                'transaksi_code'    => $kodeTransaksi,
                'created_at'        => now()
            ]);

            // DETAIL TRANSAKSI
            foreach ($request->items as $item) {
                TransaksiDetail::create([
                    'trans_name'     => $item['menu_name'],
                    'trans_qty'      => $item['qty'],
                    'trans_price'    => $item['menu_price'],
                    'trans_subtotal' => $item['qty'] * $item['menu_price'],
                    'trans_code'     => $kodeTransaksi
                ]);
            }

            DB::commit();

            // GENERATE SNAP TOKEN
            $snapToken = $this->generateSnapToken($order, $transaksi);

            return response()->json([
                'success'        => true,
                'message'        => 'Pesanan berhasil dibuat',
                'order_number'   => 'ORD-' . str_pad($order->order_id, 5, '0', STR_PAD_LEFT),
                'order_id'       => $order->order_id,
                'transaksi_code' => $kodeTransaksi,
                'snap_token'     => $snapToken,
                'client_key'     => config('midtrans.client_key'),
                'total'          => $request->total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GENERATE SNAP TOKEN
     */
    private function generateSnapToken($order, $transaksi)
    {
        $details = TransaksiDetail::where('trans_code', $transaksi->transaksi_code)->get();

        if ($details->isEmpty()) {
            throw new \Exception('Item transaksi kosong');
        }

        $item_details = [];
        $grossAmount  = 0;

        foreach ($details as $item) {
            $price = (int) $item->trans_price;
            $qty   = (int) $item->trans_qty;

            $item_details[] = [
                'id'       => (string) $item->id,
                'price'    => $price,
                'quantity' => $qty,
                'name'     => substr($item->trans_name, 0, 50),
            ];

            $grossAmount += ($price * $qty);
        }

        if ($grossAmount <= 0) {
            throw new \Exception('Gross amount tidak valid');
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $transaksi->transaksi_code,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $order->order_csname,
            ],
            'item_details' => $item_details,
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            Log::error('MIDTRANS SNAP ERROR', [
                'message' => $e->getMessage(),
                'params'  => $params,
            ]);
            throw $e;
        }
    }

    /**
     * PAYMENT CALLBACK (dari Midtrans)
     */
    public function paymentCallback(Request $request)
    {
        try {
            $notif = new Notification();

            DB::beginTransaction();

            $transactionStatus = $notif->transaction_status;
            $orderId = $notif->order_id;
            $fraudStatus = $notif->fraud_status ?? null;
            $paymentType = $notif->payment_type ?? null;

            // Ambil payment channel dari notification
            $paymentChannel = $this->getPaymentChannel($notif);

            Log::info('Midtrans Callback', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'payment_channel' => $paymentChannel,
                'fraud_status' => $fraudStatus,
            ]);

            $transaksi = Transaksi::where('transaksi_code', $orderId)->firstOrFail();
            $order = Order::where('order_id', $transaksi->transaksi_orderid)->firstOrFail();

            if (in_array($transactionStatus, ['capture', 'settlement'])) {

                // ✅ PAYMENT SUCCESS
                if ($fraudStatus == 'accept' || $fraudStatus === null) {
                    $order->update([
                        'order_status' => 'paid',
                        'order_channel' => $paymentChannel,
                    ]);

                    $transaksi->update([
                        'transaksi_status' => 'success',
                        'transaksi_channel' => $paymentChannel,
                    ]);

                    // ✅ UPDATE MEJA JADI TERISI (hanya untuk dine_in)
                    if ($order->order_type === 'dine_in' && $order->order_meja) {
                        Meja::where('meja_id', $order->order_meja)
                            ->update(['meja_status' => 'terisi']);
                    }

                    Log::info('Payment SUCCESS', [
                        'order_id' => $orderId,
                        'channel' => $paymentChannel
                    ]);
                }
            } elseif ($transactionStatus === 'pending') {

                // ⏳ PAYMENT PENDING
                $order->update([
                    'order_channel' => $paymentChannel,
                ]);

                $transaksi->update([
                    'transaksi_status' => 'pending',
                    'transaksi_channel' => $paymentChannel,
                ]);

                Log::info('Payment PENDING', [
                    'order_id' => $orderId,
                    'channel' => $paymentChannel
                ]);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {

                // ❌ PAYMENT FAILED
                $order->update([
                    'order_status' => 'payment_failed',
                    'order_channel' => $paymentChannel,
                ]);

                $transaksi->update([
                    'transaksi_status' => 'failed',
                    'transaksi_channel' => $paymentChannel,
                ]);

                Log::info('Payment FAILED', [
                    'order_id' => $orderId,
                    'status' => $transactionStatus,
                    'channel' => $paymentChannel
                ]);
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment Callback Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false], 500);
        }
    }

    public function paymentFinish(Request $request)
    {
        return redirect('/pesanan')
            ->with('success', 'Pembayaran berhasil, pesanan sedang diproses');
    }
    /**
     * Get Payment Channel dari Midtrans Notification
     */
    private function getPaymentChannel($notif)
    {
        $paymentType = $notif->payment_type ?? 'unknown';

        // Map payment type ke channel yang lebih readable
        $channelMap = [
            'credit_card' => 'Credit Card',
            'bank_transfer' => $this->getBankName($notif),
            'echannel' => 'Mandiri Bill',
            'gopay' => 'GoPay',
            'qris' => 'QRIS',
            'shopeepay' => 'ShopeePay',
            'cstore' => $this->getConvenienceStoreName($notif),
            'akulaku' => 'Akulaku',
        ];

        return $channelMap[$paymentType] ?? ucfirst(str_replace('_', ' ', $paymentType));
    }

    /**
     * Get Bank Name untuk bank transfer
     */
    private function getBankName($notif)
    {
        if (isset($notif->va_numbers) && !empty($notif->va_numbers)) {
            $bank = $notif->va_numbers[0]->bank ?? 'Unknown Bank';
            return strtoupper($bank) . ' Virtual Account';
        }

        if (isset($notif->permata_va_number)) {
            return 'Permata Virtual Account';
        }

        return 'Bank Transfer';
    }

    /**
     * Get Convenience Store Name
     */
    private function getConvenienceStoreName($notif)
    {
        $store = $notif->store ?? 'Unknown';
        return strtoupper($store);
    }

    /**
     * CHECK ORDER STATUS
     */
    public function checkStatus($orderId)
    {
        try {
            $order = Order::with(['transaksi', 'meja'])->findOrFail($orderId);

            return response()->json([
                'success' => true,
                'data'    => [
                    'order_id'       => $order->order_id,
                    'order_number'   => 'ORD-' . str_pad($order->order_id, 5, '0', STR_PAD_LEFT),
                    'order_status'   => $order->order_status,
                    'payment_status' => $order->transaksi->transaksi_status ?? 'pending',
                    'payment_channel' => $order->order_channel ?? '-',
                    'table'          => $order->meja->meja_nama ?? '-',
                    'total'          => $order->order_total,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }
    }

    /**
     * GET ORDER DETAILS
     */
    public function show($orderId)
    {
        try {
            $order = Order::with(['meja'])
                ->where('order_id', $orderId)
                ->firstOrFail();

            $transaksi = Transaksi::with('details')
                ->where('transaksi_orderid', $orderId)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data'    => [
                    'order' => [
                        'id'            => $order->order_id,
                        'number'        => 'ORD-' . str_pad($order->order_id, 5, '0', STR_PAD_LEFT),
                        'customer_name' => $order->order_csname,
                        'table'         => $order->meja->meja_nama ?? '-',
                        'total'         => $order->order_total,
                        'status'        => $order->order_status,
                        'payment_method' => $order->order_metode,
                        'payment_channel' => $order->order_channel ?? '-',
                        'created_at'    => $order->created_at,
                    ],
                    'transaction' => [
                        'code'   => $transaksi->transaksi_code,
                        'status' => $transaksi->transaksi_status,
                        'channel' => $transaksi->transaksi_channel ?? '-',
                        'items'  => $transaksi->details->map(function ($detail) {
                            return [
                                'name'     => $detail->trans_name,
                                'qty'      => $detail->trans_qty,
                                'price'    => $detail->trans_price,
                                'subtotal' => $detail->trans_subtotal,
                            ];
                        }),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }
    }

    /**
     * CANCEL ORDER (Customer)
     */
    public function cancel($orderId)
    {
        DB::beginTransaction();

        try {
            $order = Order::where('order_id', $orderId)
                ->where('order_status', 'pending')
                ->firstOrFail();

            $transaksi = Transaksi::where('transaksi_orderid', $orderId)->first();

            // Update status
            $order->update(['order_status' => 'cancelled']);

            if ($transaksi) {
                $transaksi->update(['transaksi_status' => 'cancelled']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan pesanan'
            ], 500);
        }
    }
}
