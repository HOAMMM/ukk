<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    // Tampilkan semua transaksi
    public function index()
    {
        $transaksi = Transaksi::with('details', 'order')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('dashboard.transaksi', compact('transaksi'));
    }

    // Form buat transaksi baru
    public function create()
    {
        // Ambil data produk/menu untuk dipilih
        $menu = DB::table('tb_menu')->get();

        return view('dashboard.transaksi.create', compact('menu'));
    }

    // Simpan transaksi baru
    public function store(Request $request)
    {
        $request->validate([
            'transaksi_name' => 'required',
            'kategori_name' => 'required',
            'items' => 'required|array',
            'items.*.name' => 'required',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Generate kode transaksi
            $code = 'TRX-' . date('YmdHis') . rand(100, 999);

            // Hitung total amount
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['qty'] * $item['price'];
            }

            // Simpan transaksi utama
            $transaksi = Transaksi::create([
                'transaksi_name' => $request->transaksi_name,
                'kategori_name' => $request->kategori_name,
                'transaksi_amount' => $totalAmount,
                'transaksi_status' => $request->transaksi_status ?? 'pending',
                'transaksi_code' => $code,
                'created_at' => now(),
            ]);

            // Simpan detail transaksi
            foreach ($request->items as $item) {
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->transaksi_id,
                    'trans_name' => $item['name'],
                    'trans_qty' => $item['qty'],
                    'trans_price' => $item['price'],
                    'trans_subtotal' => $item['qty'] * $item['price'],
                    'trans_code' => $code,
                ]);
            }

            // Jika ada data order, simpan juga
            if ($request->has('order_table')) {
                Order::create([
                    'order_cname' => $request->transaksi_name,
                    'order_table' => $request->order_table,
                    'order_amount' => $totalAmount,
                    'order_qty' => array_sum(array_column($request->items, 'qty')),
                    'order_change' => $request->order_change ?? 0,
                    'order_status' => $request->order_status ?? 'pending',
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('transaksi.index')
                ->with('success', 'Transaksi berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Detail transaksi
    public function show($id)
    {
        $transaksi = Transaksi::with('details', 'order')
            ->findOrFail($id);

        return view('dashboard.transaksi.show', compact('transaksi'));
    }

    // Update status transaksi
    public function updateStatus(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);


        $transaksi->update([
            'transaksi_status' => $request->status
        ]);

        return redirect()->back()
            ->with('success', 'Status transaksi berhasil diupdate!');
    }

    // Hapus transaksi
    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // hapus detail transaksi
        TransaksiDetail::where('trans_id', $id)->delete();

        // hapus transaksi utama
        $transaksi->delete();

        return redirect()->route('dashboard.transaksi')
            ->with('success', 'Transaksi berhasil dihapus');
    }
}
