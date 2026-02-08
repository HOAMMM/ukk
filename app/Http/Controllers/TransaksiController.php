<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    /**
     * Tampilkan semua transaksi dengan filter
     */
    public function index(Request $request)
    {
        $query = Transaksi::with('details', 'order');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('transaksi_status', $request->status);
        }

        // Filter by payment channel
        if ($request->filled('channel')) {
            $query->where('transaksi_channel', 'LIKE', '%' . $request->channel . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transaksi = $query->orderBy('created_at', 'DESC')
            ->paginate(15)
            ->withQueryString(); // ⬅️ Penting untuk maintain filter di pagination

        return view('dashboard.transaksi', compact('transaksi'));
    }

    /**
     * Detail transaksi (untuk modal)
     */
    public function show($id)
    {
        $transaksi = Transaksi::with('details', 'order.meja')
            ->findOrFail($id);

        return view('dashboard.transaksi.show', compact('transaksi'));
    }

    /**
     * Update status transaksi
     */
    public function updateStatus(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $transaksi->update([
            'transaksi_status' => $request->status
        ]);

        return redirect()->back()
            ->with('success', 'Status transaksi berhasil diupdate!');
    }

    /**
     * Hapus transaksi
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $transaksi = Transaksi::findOrFail($id);

            // Hapus detail transaksi
            TransaksiDetail::where('trans_code', $transaksi->transaksi_code)->delete();

            // Hapus transaksi utama
            $transaksi->delete();

            DB::commit();

            return redirect()->route('dashboard.transaksi')
                ->with('success', 'Transaksi berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('dashboard.transaksi')
                ->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}
