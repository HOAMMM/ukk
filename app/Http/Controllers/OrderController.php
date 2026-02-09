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

    public function indexwaiter()
    {
        $kategori = Kategori::orderBy('kategori_name')->get();
        $mejas = Meja::orderBy('meja_id')->get();

        // Urutkan dari yang terbaru (data baru di atas)
        $orders = Order::orderBy('created_at', 'desc')->get();

        $menus = Menu::orderBy('menu_kategori')
            ->orderBy('menu_name')
            ->get();

        return view('dashboard.pesanan', compact('kategori', 'mejas', 'menus', 'orders'));
    }

    public function detailpesanan($order_id)
    {
        $order = Order::with('detail.pesanan')->findOrFail($order_id);
        return view('dashboard.detail.pesanan', compact('order'));
    }

    // API endpoint untuk modal detail
    public function getOrderDetail($order_id)
    {
        try {
            $order = Order::with(['meja', 'transaksi.details'])
                ->findOrFail($order_id);

            // Ambil detail dari transaksi
            $transaksi = Transaksi::where('transaksi_orderid', $order_id)->first();
            $details = [];

            if ($transaksi) {
                $details = TransaksiDetail::where('trans_code', $transaksi->transaksi_code)->get();
            }

            return response()->json([
                'status' => true,
                'order' => [
                    'order_id' => $order->order_id,
                    'order_csname' => $order->order_csname,
                    'order_meja' => $order->order_meja,
                    'order_total' => $order->order_total,
                    'order_change' => $order->order_change,
                    'order_status' => $order->order_status,
                    'created_at' => $order->created_at,
                    'meja' => $order->meja,
                    'details' => $details
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function hapuspesanan($id)
    {
        try {
            DB::beginTransaction();

            // Hapus transaksi detail terlebih dahulu
            $transaksi = Transaksi::where('transaksi_orderid', $id)->first();
            if ($transaksi) {
                TransaksiDetail::where('trans_code', $transaksi->transaksi_code)->delete();
                $transaksi->delete();
            }

            // Hapus order
            Order::where('order_id', $id)->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Bulk delete orders
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->ids;

            if (empty($ids)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada pesanan yang dipilih'
                ], 422);
            }

            DB::beginTransaction();

            // Hapus transaksi detail dan transaksi
            $transaksis = Transaksi::whereIn('transaksi_orderid', $ids)->get();
            foreach ($transaksis as $transaksi) {
                TransaksiDetail::where('trans_code', $transaksi->transaksi_code)->delete();
            }
            Transaksi::whereIn('transaksi_orderid', $ids)->delete();

            // Hapus orders
            Order::whereIn('order_id', $ids)->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => count($ids) . ' pesanan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Mark orders as paid
    public function markAsPaid(Request $request)
    {
        try {
            $ids = $request->ids;

            if (empty($ids)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada pesanan yang dipilih'
                ], 422);
            }

            DB::beginTransaction();

            // Update menggunakan DB query builder (tidak trigger Eloquent timestamps)
            DB::table('tb_order')
                ->whereIn('order_id', $ids)
                ->update(['order_status' => 'paid']);

            DB::table('tb_transaksi')
                ->whereIn('transaksi_orderid', $ids)
                ->update(['transaksi_status' => 'success']);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => count($ids) . ' pesanan berhasil ditandai sebagai Paid'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Mark orders as pending
    public function markAsPending(Request $request)
    {
        try {
            $ids = $request->ids;

            if (empty($ids)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada pesanan yang dipilih'
                ], 422);
            }

            DB::beginTransaction();

            // Update menggunakan DB query builder (tidak trigger Eloquent timestamps)
            DB::table('tb_order')
                ->whereIn('order_id', $ids)
                ->update(['order_status' => 'pending']);

            DB::table('tb_transaksi')
                ->whereIn('transaksi_orderid', $ids)
                ->update(['transaksi_status' => 'pending']);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => count($ids) . ' pesanan berhasil ditandai sebagai Pending'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
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
            'bayar' => 'required|numeric|min:1',
            'csname' => 'required|string|max:255',
            'order_type' => 'required|in:dine_in,takeaway',
            'meja_id' => 'nullable|exists:tb_meja,meja_id'
        ]);

        DB::beginTransaction();

        try {
            // Ambil order
            $order = Order::where('order_id', $request->order_id)
                ->where('order_status', 'pending')
                ->firstOrFail();

            // Ambil transaksi pending
            $transaksi = Transaksi::where('transaksi_orderid', $order->order_id)
                ->where('transaksi_status', 'pending')
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
                'transaksi_csname' => $request->csname,
                'transaksi_amount' => $request->bayar,
                'transaksi_change' => $kembalian,
                'transaksi_status' => 'success',
                'transaksi_channel' => 'cash'
            ]);

            /* ===============================
         * UPDATE ORDER
         * =============================== */
            $order->update([
                'order_csname' => $request->csname,
                'order_type'   => $request->order_type,

                // 🔥 INI FIX UTAMANYA
                'order_meja'   => $request->order_type === 'dine_in'
                    ? $request->meja_id
                    : 'T/A',

                'order_total'  => $request->bayar,
                'order_change' => $kembalian,
                'order_status' => 'paid',
                'order_channel' => 'cash'
            ]);


            /* ===============================
                    * UPDATE MEJA (hanya untuk dine_in)
            * =============================== */
            if ($request->order_type === 'dine_in' && $request->meja_id) {
                $meja = Meja::where('meja_id', $request->meja_id)->firstOrFail();

                $meja->update([
                    'meja_status' => 'terisi'
                ]);
            }

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
                ->where('order_status', 'paid')
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
