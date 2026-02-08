<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaksi;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;


class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tgl_mulai   = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;

        // ===============================
        // QUERY UTAMA (SATU SUMBER DATA)
        // ===============================
        $baseQuery = Order::query();

        if ($tgl_mulai && $tgl_selesai) {
            $baseQuery->whereBetween('created_at', [
                $tgl_mulai . ' 00:00:00',
                $tgl_selesai . ' 23:59:59'
            ]);
        }

        // ===============================
        // SUMMARY
        // ===============================
        $total_pendapatan = (clone $baseQuery)
            ->where('order_status', 'paid')
            ->sum('order_total');

        $total_orderan = (clone $baseQuery)->count();

        // ===============================
        // TABLE TRANSAKSI
        // ===============================
        $orderan_terbaru = (clone $baseQuery)
            ->leftJoin('tb_meja', 'tb_meja.meja_id', '=', 'tb_order.order_meja')
            ->select(
                'tb_order.*',
                'tb_meja.meja_nama'
            )
            ->orderBy('tb_order.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // ===============================
        // GRAFIK
        // ===============================
        $query_grafik = (clone $baseQuery)
            ->where('order_status', 'paid')
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('SUM(order_total) as total')
            )
            ->groupBy('bulan')
            ->orderBy('bulan');

        $pendapatan_bulanan = $query_grafik->get();

        // ===============================
        // MAPPING BULAN
        // ===============================
        $nama_bulan = [
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember"
        ];

        $labels = [];
        $data_pendapatan = [];

        if ($tgl_mulai && $tgl_selesai) {
            foreach ($pendapatan_bulanan as $item) {
                $labels[] = $nama_bulan[$item->bulan - 1];
                $data_pendapatan[] = $item->total;
            }
        } else {
            $labels = $nama_bulan;
            $data_pendapatan = array_fill(0, 12, 0);

            foreach ($pendapatan_bulanan as $item) {
                $data_pendapatan[$item->bulan - 1] = $item->total;
            }
        }

        return view('dashboard.laporan', compact(
            'total_pendapatan',
            'total_orderan',
            'orderan_terbaru',
            'labels',
            'data_pendapatan'
        ));
    }


    public function exportExcel(Request $request)
    {
        // Kirim request ke LaporanExport agar data yang di-download sesuai dengan filter yang aktif
        return Excel::download(
            new LaporanExport($request->tgl_mulai, $request->tgl_selesai),
            'laporan-kasir-' . date('Y-m-d') . '.xlsx'
        );
    }
}
