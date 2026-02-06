<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaksi;
use App\Models\Menu;
use App\Models\Meja;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $level = $user->id_level;

        // Data untuk semua user
        $data = $this->getCommonData();

        // Data spesifik berdasarkan level
        if ($level == 1) {
            // Admin
            $data = array_merge($data, $this->getAdminData());
        } elseif ($level == 2) {
            // Waiter
            $data = array_merge($data, $this->getWaiterData());
        } elseif ($level == 3) {
            // Kasir
            $data = array_merge($data, $this->getKasirData());
        } else {
            // Owner
            $data = array_merge($data, $this->getOwnerData());
        }

        return view('dashboard.dashboard', $data);
    }

    public function getCommonData()
    {
        // Pendapatan hari ini
        $pendapatanHariIni = Order::whereDate('created_at', today())
            ->where('order_status', 'success')
            ->sum('order_total');

        // Total transaksi hari ini
        $transaksiHariIni = Order::whereDate('created_at', today())
            ->count();

        // Total menu
        $totalMenu = Menu::count();

        // Meja aktif
        $mejaAktif = Meja::where('meja_status', 'terisi')->count();
        $totalMeja = Meja::count();

        return [
            'pendapatanHariIni' => $pendapatanHariIni,
            'transaksiHariIni' => $transaksiHariIni,
            'totalMenu' => $totalMenu,
            'mejaAktif' => $mejaAktif,
            'totalMeja' => $totalMeja,
        ];
    }

    public function getAdminData()
    {
        // Total staff
        $totalKasir = User::where('id_level', 3)->count();
        $totalWaiter = User::where('id_level', 2)->count();

        // Transaksi terbaru
        $transaksiTerbaru = Order::with('meja')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Menu populer
        $menuPopuler = DB::table('tb_transaksi_detail')
            ->select('trans_name', DB::raw('SUM(trans_qty) as total_terjual'))
            ->groupBy('trans_name')
            ->orderBy('total_terjual', 'desc')
            ->take(5)
            ->get();

        // Pendapatan 7 hari terakhir
        $pendapatan7Hari = Order::where('order_status', 'success')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('SUM(order_total) as total')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        return [
            'totalKasir' => $totalKasir,
            'totalWaiter' => $totalWaiter,
            'transaksiTerbaru' => $transaksiTerbaru,
            'menuPopuler' => $menuPopuler,
            'pendapatan7Hari' => $pendapatan7Hari,
        ];
    }

    public function getWaiterData()
    {
        // Status meja
        $mejaTersedia = Meja::where('meja_status', 'kosong')->count();
        $mejaTerisi = Meja::where('meja_status', 'terisi')->count();

        // Daftar meja
        $daftarMeja = Meja::orderBy('meja_nama')->get();

        // Order pending
        $orderPending = Order::where('order_status', 'pending')
            ->with('meja')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return [
            'mejaTersedia' => $mejaTersedia,
            'mejaTerisi' => $mejaTerisi,
            'daftarMeja' => $daftarMeja,
            'orderPending' => $orderPending,
        ];
    }

    public function getKasirData()
    {
        // Order hari ini
        $orderHariIni = Order::whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Total item terjual hari ini
        $itemTerjualHariIni = DB::table('tb_transaksi_detail')
            ->join('tb_transaksi', 'tb_transaksi_detail.trans_code', '=', 'tb_transaksi.transaksi_code')
            ->whereDate('tb_transaksi.created_at', today())
            ->sum('tb_transaksi_detail.trans_qty');

        // Menu populer hari ini
        $menuPopulerHariIni = DB::table('tb_transaksi_detail')
            ->join('tb_transaksi', 'tb_transaksi_detail.trans_code', '=', 'tb_transaksi.transaksi_code')
            ->whereDate('tb_transaksi.created_at', today())
            ->select('trans_name', DB::raw('SUM(trans_qty) as total_terjual'))
            ->groupBy('trans_name')
            ->orderBy('total_terjual', 'desc')
            ->take(5)
            ->get();

        return [
            'orderHariIni' => $orderHariIni,
            'itemTerjualHariIni' => $itemTerjualHariIni,
            'menuPopulerHariIni' => $menuPopulerHariIni,
        ];
    }

    public function getOwnerData()
    {
        // Pendapatan bulan ini
        $pendapatanBulanIni = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('order_status', 'success')
            ->sum('order_total');

        // Pendapatan bulan lalu
        $pendapatanBulanLalu = Order::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->where('order_status', 'success')
            ->sum('order_total');

        // Persentase perubahan
        $perubahanPendapatan = 0;
        if ($pendapatanBulanLalu > 0) {
            $perubahanPendapatan = (($pendapatanBulanIni - $pendapatanBulanLalu) / $pendapatanBulanLalu) * 100;
        }

        // Pendapatan per bulan (12 bulan terakhir)
        $pendapatanPerBulan = Order::where('order_status', 'success')
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('SUM(order_total) as total')
            )
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();

        // Menu terlaris
        $menuTerlaris = DB::table('tb_transaksi_detail')
            ->select('trans_name', DB::raw('SUM(trans_qty) as total_terjual'), DB::raw('SUM(trans_subtotal) as total_pendapatan'))
            ->groupBy('trans_name')
            ->orderBy('total_terjual', 'desc')
            ->take(10)
            ->get();

        // Total transaksi bulan ini
        $transaksiBulanIni = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'pendapatanBulanIni' => $pendapatanBulanIni,
            'pendapatanBulanLalu' => $pendapatanBulanLalu,
            'perubahanPendapatan' => $perubahanPendapatan,
            'pendapatanPerBulan' => $pendapatanPerBulan,
            'menuTerlaris' => $menuTerlaris,
            'transaksiBulanIni' => $transaksiBulanIni,
        ];
    }
}
