<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Meja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderCustomerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:100',
            'table_number'   => 'required|numeric',
            'items'          => 'required|array|min:1',
            'total'          => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            // ===============================
            // VALIDASI MEJA
            // ===============================
            $meja = Meja::where('meja_id', $request->table_number)->first();

            if (!$meja) {
                return response()->json([
                    'success' => false,
                    'message' => 'Meja tidak ditemukan'
                ], 422);
            }

            // ===============================
            // SIMPAN ORDER
            // ===============================
            $order = Order::create([
                'order_csname'  => $request->customer_name,
                'order_meja'    => $meja->meja_id,
                'order_total'   => $request->total,
                'order_qty'     => collect($request->items)->sum('qty'),
                'order_change'  => 0,
                'order_methode' => strtoupper($request->payment_method ?? 'CASH'),
                'order_status'  => 'pending',
                'created_at'    => now()
            ]);

            // ===============================
            // SIMPAN TRANSAKSI
            // ===============================
            $kodeTransaksi = 'TRX-' . now()->format('Ymd') . '-' . rand(1000, 9999);

            Transaksi::create([
                'transaksi_orderid' => $order->order_id,
                'transaksi_csname'  => $request->customer_name,
                'transaksi_total'   => $request->total,
                'transaksi_amount'  => 0,
                'transaksi_change'  => 0,
                'transaksi_status'  => 'pending',
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

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Pesanan berhasil dibuat',
                'order_number' => 'ORD-' . str_pad($order->order_id, 5, '0', STR_PAD_LEFT)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
