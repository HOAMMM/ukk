<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Meja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kategori;

class OrderController extends Controller
{
    public function index()
    {
        $menus = Menu::select(
            'tb_menu.*',
            'tb_kategori.kategori_name'
        )
            ->leftJoin(
                'tb_kategori',
                'tb_kategori.kategori_name',
                '=',
                'tb_menu.menu_kategori'
            )
            ->orderBy('tb_menu.menu_name', 'asc')
            ->get();

        $kategori = Kategori::orderBy('kategori_name', 'asc')->get();

        return view('dashboard.order', compact('menus', 'kategori'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'total' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {

            // 🔎 AMBIL 1 MEJA KOSONG
            $meja = Meja::where('meja_status', 'kosong')->lockForUpdate()->first();

            if (!$meja) {
                return response()->json([
                    'status' => false,
                    'message' => 'Semua meja sedang terisi'
                ], 422);
            }

            // ===============================
            // SIMPAN ORDER
            // ===============================
            $order = Order::create([
                'order_csname' => $request->csname,
                'order_meja'   => $meja->meja_id,
                'order_total'  => $request->total,
                'order_qty'    => collect($request->items)->sum('qty'),
                'order_change' => 0,
                'order_status' => 'pending',
                'created_at'   => now()
            ]);

            // ===============================
            // SIMPAN TRANSAKSI
            // ===============================
            $kodeTransaksi = 'TRX-' . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);

            Transaksi::create([
                'transaksi_orderid' => $order->order_id,
                'transaksi_csname' => $order->order_csname,
                'transaksi_total'  => $request->total,
                'transaksi_amount' => 0,
                'transaksi_change' => 0,
                'transaksi_status' => 'pending',
                'transaksi_code'   => $kodeTransaksi,
                'created_at'       => now()
            ]);

            foreach ($request->items as $item) {
                TransaksiDetail::create([
                    'trans_name'     => $item['name'],
                    'trans_qty'      => $item['qty'],
                    'trans_price'    => $item['price'],
                    'trans_subtotal' => $item['qty'] * $item['price'],
                    'trans_code'     => $kodeTransaksi
                ]);
            }

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => 'Checkout berhasil',
                'order_id' => $order->order_id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function payment($order_id)
    {
        $order = Order::with('meja')->findOrFail($order_id);
        $mejas = Meja::orderBy('meja_nama')->get();


        $transaksi = Transaksi::where('transaksi_orderid', $order_id)
            ->where('transaksi_status', 'pending')
            ->firstOrFail();


        $details = TransaksiDetail::where(
            'trans_code',
            $transaksi->transaksi_code
        )->get();

        return view('dashboard.order.payment', compact(
            'order',
            'transaksi',
            'details',
            'mejas'
        ));
    }


    public function processPayment(Request $request)
    {
        $request->validate([
            'bayar' => 'required|numeric|min:1'
        ]);

        DB::beginTransaction();

        try {
            // Ambil order
            $order = Order::where('order_id', $request->order_id)
                ->where('order_status', 'pending')
                ->firstOrFail();

            // Ambil transaksi pending
            $transaksi = Transaksi::where('transaksi_status', 'pending')
                ->orderBy('transaksi_id', 'desc')
                ->firstOrFail();

            if ($request->bayar < $transaksi->transaksi_total) {
                return response()->json([
                    'status' => false,
                    'message' => 'Uang bayar kurang'
                ], 422);
            }

            $kembalian = $request->bayar - $transaksi->transaksi_total;

            /* ===============================
         * UPDATE TRANSAKSI
         * =============================== */
            $transaksi->update([
                'transaksi_amount' => $request->bayar,
                'transaksi_change' => $kembalian,
                'transaksi_status' => 'success'
            ]);

            /* ===============================
         * UPDATE ORDER
         * =============================== */
            $order->update([
                'order_total'  => $request->bayar,
                'order_change' => $kembalian,
                'order_status' => 'success'
            ]);

            /* ===============================
                    * UPDATE MEJA
            * =============================== */
            $meja = Meja::where('meja_id', $order->order_meja)->firstOrFail();

            $meja->update([
                'meja_status' => 'terisi'
            ]);

            DB::commit();

            session()->flash('print_auto', true);

            return response()->json([
                'status'    => true,
                'message'   => 'Pembayaran berhasil',
                'kembalian' => $kembalian,
                'order_id'  => $order->order_id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function struk($order_id)
    {
        try {
            // Ambil data order
            $order = Order::where('order_id', $order_id)
                ->where('order_status', 'success')
                ->firstOrFail();

            // Ambil transaksi
            $transaksi = Transaksi::where('transaksi_orderid', $order_id)
                ->where('transaksi_status', 'success')
                ->firstOrFail();

            // Ambil detail transaksi
            $details = TransaksiDetail::where('trans_code', $transaksi->transaksi_code)
                ->get();

            $meja = Meja::where('meja_id', $order->order_meja)->firstOrFail();


            return view('dashboard.order.struk', compact(
                'order',
                'transaksi',
                'details',
                'meja'
            ));
        } catch (\Exception $e) {
            return redirect()->route('dashboard.order')
                ->with('error', 'Struk tidak ditemukan: ' . $e->getMessage());
        }
    }
}
