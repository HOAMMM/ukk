<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman laporan keuangan dengan filter
     */
    public function index(Request $request)
    {
        // Validasi input filter
        $request->validate([
            'bulan' => 'nullable|integer|between:1,12',
            'tahun' => 'nullable|integer|min:2000',
            'tgl_mulai' => 'nullable|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
        ]);

        $filters = $this->getFilters($request);

        // Build query dengan filter
        $baseQuery = $this->buildFilteredQuery($filters);

        // Hitung ringkasan keuangan
        $total_pendapatan = (clone $baseQuery)->sum('tb_order.order_total');
        $total_orderan = (clone $baseQuery)->count();

        // Ambil detail transaksi dengan pagination
        $orderan_terbaru = (clone $baseQuery)
            ->leftJoin('tb_meja', 'tb_meja.meja_id', '=', 'tb_order.order_meja')
            ->select('tb_order.*', 'tb_meja.meja_nama')
            ->orderBy('tb_order.created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // Penting untuk mempertahankan filter di pagination

        return view('dashboard.laporan', compact(
            'total_pendapatan',
            'total_orderan',
            'orderan_terbaru'
        ));
    }

    /**
     * Export laporan ke Excel
     */
    public function exportExcel(Request $request)
    {
        $filters = $this->getFilters($request);

        $filename = 'laporan-keuangan-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(
            new LaporanExport($filters),
            $filename
        );
    }

    /**
     * Ambil filter dari request
     */
    private function getFilters(Request $request): array
    {
        return [
            'bulan' => $request->input('bulan'),
            'tahun' => $request->input('tahun'),
            'tgl_mulai' => $request->input('tgl_mulai'),
            'tgl_selesai' => $request->input('tgl_selesai'),
        ];
    }

    /**
     * Build query dengan filter yang diterapkan
     */
    private function buildFilteredQuery(array $filters)
    {
        // Gunakan prefix tabel untuk menghindari ambiguitas
        $query = Order::where('tb_order.order_status', 'paid');

        // Filter berdasarkan range tanggal (prioritas tertinggi)
        if (!empty($filters['tgl_mulai']) && !empty($filters['tgl_selesai'])) {
            $query->whereBetween('tb_order.created_at', [
                Carbon::parse($filters['tgl_mulai'])->startOfDay(),
                Carbon::parse($filters['tgl_selesai'])->endOfDay(),
            ]);
        }
        // Jika tidak ada range tanggal, baru cek bulan & tahun
        else {
            // Filter berdasarkan bulan
            if (!empty($filters['bulan'])) {
                $query->whereMonth('tb_order.created_at', $filters['bulan']);
            }

            // Filter berdasarkan tahun
            if (!empty($filters['tahun'])) {
                $query->whereYear('tb_order.created_at', $filters['tahun']);
            }
        }

        return $query;
    }
}
