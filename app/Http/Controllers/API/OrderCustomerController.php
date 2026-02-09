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
use Illuminate\Support\Str;
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
            'customer_name'      => 'required|string|min:3|max:100',
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
        ], [
            'table_number.required' => 'Nomor meja wajib diisi untuk tipe pesanan dine-in.',
            'customer_name.required' => 'Nama pelanggan wajib diisi.',
            'customer_name.min' => 'Nama pelanggan minimal 3 karakter.',
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
                'order_channel' => null,
                'order_status'  => 'pending',
                'created_at'    => now()
            ]);

            // GENERATE KODE TRANSAKSI
            $kodeTransaksi = 'TRX-' . strtoupper(Str::random(6));

            // SIMPAN TRANSAKSI
            $transaksi = Transaksi::create([
                'transaksi_orderid' => $order->order_id,
                'transaksi_csname'  => $request->customer_name,
                'transaksi_total'   => $request->total,
                'transaksi_amount'  => 0,
                'transaksi_change'  => 0,
                'transaksi_message' => $request->notes,
                'transaksi_status'  => 'pending',
                'transaksi_channel' => null,
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
            'callbacks' => [
                'finish' => url('/payment/finish'),
            ],
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
            $paidAmount = (int) $notif->gross_amount;


            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                if ($fraudStatus == 'accept' || $fraudStatus === null) {
                    $order->update([
                        'order_status' => 'paid',
                        'order_channel' => $paymentChannel,
                    ]);

                    $transaksi->update([
                        'transaksi_status'  => 'success',
                        'transaksi_channel' => $paymentChannel,
                        'transaksi_amount'  => $paidAmount,
                        'transaksi_change'  => 0
                    ]);

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
                $order->update([
                    'order_status' => 'cancelled',
                    'order_channel' => $paymentChannel,
                ]);

                $transaksi->update([
                    'transaksi_status' => 'cancelled',
                    'transaksi_channel' => $paymentChannel,
                ]);

                Log::info('Payment FAILED/CANCELLED', [
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

    /**
     * PAYMENT FINISH (Redirect setelah payment)
     */
    public function paymentFinish(Request $request)
    {
        return redirect('/')->with('payment_status', 'completed');
    }

    /**
     * Get Payment Channel dari Midtrans Notification
     */
    private function getPaymentChannel($notif)
    {
        $paymentType = $notif->payment_type ?? 'unknown';

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
     * CANCEL ORDER (Customer - dipanggil saat user close snap popup)
     * FIXED: Tambah try-catch dan logging yang lebih detail
     */
    public function cancel($orderId)
    {
        try {
            // Log incoming request
            Log::info('Cancel order request received', [
                'order_id' => $orderId,
                'timestamp' => now()
            ]);

            // Validasi order_id
            if (!is_numeric($orderId)) {
                Log::warning('Invalid order_id format', ['order_id' => $orderId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Order ID tidak valid'
                ], 400);
            }

            DB::beginTransaction();

            // Cek apakah order ada
            $order = Order::find($orderId);

            if (!$order) {
                Log::warning('Order not found', ['order_id' => $orderId]);
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Order tidak ditemukan'
                ], 404);
            }

            // Cek status order - hanya cancel jika pending
            if ($order->order_status !== 'pending') {
                Log::info('Order already processed', [
                    'order_id' => $orderId,
                    'current_status' => $order->order_status
                ]);
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Order sudah diproses dan tidak bisa dibatalkan',
                    'current_status' => $order->order_status
                ], 422);
            }

            // Update order status
            $order->update(['order_status' => 'cancelled']);

            // Update transaksi jika ada
            $transaksi = Transaksi::where('transaksi_orderid', $orderId)->first();
            if ($transaksi) {
                $transaksi->update(['transaksi_status' => 'cancelled']);
            }

            DB::commit();

            Log::info('Order cancelled successfully', [
                'order_id' => $orderId,
                'transaksi_code' => $transaksi->transaksi_code ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan',
                'order_id' => $orderId,
                'new_status' => 'cancelled'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Cancel order failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
}
