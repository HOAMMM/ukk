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
     * ========================================
     * CREATE ORDER (Customer Order dari Web)
     * ========================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:100',
            'order_type'     => 'required|in:dine_in,takeaway',

            'table_number' => $request->order_type === 'dine_in'
                ? 'required|numeric'
                : 'nullable',

            'items'            => 'required|array|min:1',
            'items.*.menu_id'  => 'required|numeric',
            'items.*.menu_name' => 'required|string',
            'items.*.menu_price' => 'required|numeric',
            'items.*.qty'      => 'required|numeric|min:1',
            'subtotal'         => 'required|numeric|min:1',
            'tax'              => 'required|numeric|min:0',
            'total'            => 'required|numeric|min:1',
            'payment_method'   => 'required|in:cash,qris,debit,ewallet',
            'notes'            => 'nullable|string|max:500',
        ]);


        DB::beginTransaction();

        try {
            // ===============================
            // VALIDASI MEJA
            // ===============================
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


            // ===============================
            // SIMPAN ORDER
            // ===============================
            $order = Order::create([
                'order_csname'  => $request->customer_name,
                'order_meja'    => $meja ? $meja->meja_id : null,
                'order_total'   => $request->total,
                'order_qty'     => collect($request->items)->sum('qty'),
                'order_change'  => 0,
                'order_type'    => $request->order_type,
                'order_message' => $request->notes,
                'order_metode' => strtoupper($request->payment_method),
                'order_status'  => 'pending',
                'created_at'    => now()
            ]);


            // ===============================
            // GENERATE KODE TRANSAKSI
            // ===============================
            $kodeTransaksi = 'TRX-' . now()->format('Ymd') . '-' . str_pad($order->order_id, 4, '0', STR_PAD_LEFT);

            // ===============================
            // SIMPAN TRANSAKSI
            // ===============================
            $transaksi = Transaksi::create([
                'transaksi_orderid' => $order->order_id,
                'transaksi_csname'  => $request->customer_name,
                'transaksi_total'   => $request->total,
                'transaksi_amount'  => 0, // akan diisi saat payment
                'transaksi_change'  => 0, // akan diisi saat payment
                'transaksi_message' => $request->notes,
                'transaksi_status'  => 'pending', // pending → success → failed
                'transaksi_code'    => $kodeTransaksi,
                'created_at'        => now()
            ]);

            // ===============================
            // DETAIL TRANSAKSI
            // ===============================
            foreach ($request->items as $item) {
                TransaksiDetail::create([
                    'trans_name'     => $item['menu_name'],
                    'trans_qty'      => $item['qty'],
                    'trans_price'    => $item['menu_price'],
                    'trans_subtotal' => $item['qty'] * $item['menu_price'],
                    'trans_code'     => $kodeTransaksi
                ]);
            }

            // ===============================
            // UPDATE STATUS MEJA
            // ===============================
            if ($meja) {
                $meja->update(['meja_status' => 'terisi']);
            }

            DB::commit();

            // ===============================
            // RESPONSE DENGAN DATA PAYMENT
            // ===============================
            $response = [
                'success'        => true,
                'message'        => 'Pesanan berhasil dibuat',
                'order_number'   => 'ORD-' . str_pad($order->order_id, 5, '0', STR_PAD_LEFT),
                'order_id'       => $order->order_id,
                'transaksi_code' => $kodeTransaksi,
                'payment_method' => $request->payment_method,
                'total'          => $request->total,
            ];

            // Jika bukan cash, generate payment data
            if ($request->payment_method !== 'cash') {
                $response['payment_data'] = $this->generatePaymentData(
                    $request->payment_method,
                    $order,
                    $transaksi
                );
            }

            return response()->json($response);
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
     * ========================================
     * GENERATE PAYMENT DATA
     * ========================================
     */

    private function generatePaymentData($method, $order, $transaksi)
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
                'id'       => (string) $item->id, // ✅ STRING
                'price'    => $price,              // ✅ INTEGER
                'quantity' => $qty,                // ✅ INTEGER
                'name'     => substr($item->trans_name, 0, 50), // ✅ MAX 50 CHAR
            ];

            $grossAmount += ($price * $qty);
        }

        if ($grossAmount <= 0) {
            throw new \Exception('Gross amount tidak valid');
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $transaksi->transaksi_code,
                'gross_amount' => $grossAmount, // ✅ HARUS SAMA DENGAN ITEM
            ],
            'customer_details' => [
                'first_name' => $order->order_csname,
            ],
            'item_details' => $item_details,
        ];

        try {
            return [
                'type'       => 'midtrans',
                'snap_token' => Snap::getSnapToken($params),
                'client_key' => config('midtrans.client_key'),
            ];
        } catch (\Exception $e) {
            Log::error('MIDTRANS SNAP ERROR', [
                'message' => $e->getMessage(),
                'params'  => $params,
            ]);
            throw $e;
        }
    }



    /**
     * ========================================
     * GENERATE QRIS
     * ========================================
     */
    private function generateQRIS($order, $transaksi)
    {
        // TODO: Integrate dengan payment gateway (contoh: Midtrans, Xendit, dll)
        // Untuk demo, return dummy data

        return [
            'type'       => 'qris',
            'qr_string'  => '00020101021226660014ID.LINKAJA.WWW011893600915133772745502041234530336054041000.005802ID5914Warung Nusantara6011Jakarta Timur61051234062150811INVOICE123630418B0',
            'qr_url'     => 'https://api.sandbox.midtrans.com/qr/generate/' . $transaksi->transaksi_code,
            'expired_at' => now()->addMinutes(15)->toIso8601String(),
        ];
    }

    /**
     * ========================================
     * GENERATE DEBIT PAYMENT
     * ========================================
     */
    private function generateDebitPayment($order, $transaksi)
    {
        // TODO: Integrate dengan payment gateway

        return [
            'type'         => 'debit',
            'redirect_url' => url('/payment/debit/' . $transaksi->transaksi_code),
            'expired_at'   => now()->addMinutes(30)->toIso8601String(),
        ];
    }

    /**
     * ========================================
     * GENERATE E-WALLET PAYMENT
     * ========================================
     */
    private function generateEwalletPayment($order, $transaksi)
    {
        // TODO: Integrate dengan payment gateway (GoPay, OVO, Dana, dll)

        return [
            'type'         => 'ewallet',
            'actions'      => [
                'deeplink_redirect' => 'gojek://gopay/payment?amount=' . $order->order_total,
                'mobile_web_url'    => url('/payment/ewallet/' . $transaksi->transaksi_code),
            ],
            'expired_at'   => now()->addMinutes(15)->toIso8601String(),
        ];
    }

    /**
     * ========================================
     * CHECK ORDER STATUS
     * ========================================
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
     * ========================================
     * PAYMENT CALLBACK (dari Payment Gateway)
     * ========================================
     */

    public function paymentCallback(Request $request)
    {
        $notif = new Notification();

        DB::beginTransaction();
        try {
            $transactionStatus = $notif->transaction_status;
            $orderId = $notif->order_id;

            $transaksi = Transaksi::where('transaksi_code', $orderId)->firstOrFail();
            $order = Order::where('order_id', $transaksi->transaksi_orderid)->firstOrFail();

            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                $order->update(['order_status' => 'paid']);
                $transaksi->update(['transaksi_status' => 'success']);
            } elseif ($transactionStatus === 'pending') {
                $transaksi->update(['transaksi_status' => 'pending']);
            } else {
                $order->update(['order_status' => 'payment_failed']);
                $transaksi->update(['transaksi_status' => 'failed']);
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['success' => false], 500);
        }
    }


    /**
     * ========================================
     * GET ORDER DETAILS
     * ========================================
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
                        'created_at'    => $order->created_at,
                    ],
                    'transaction' => [
                        'code'   => $transaksi->transaksi_code,
                        'status' => $transaksi->transaksi_status,
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
     * ========================================
     * CANCEL ORDER (Customer)
     * ========================================
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

            // Release meja
            $meja = Meja::where('meja_id', $order->order_meja)->first();
            if ($meja) {
                $meja->update(['meja_status' => 'kosong']);
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
