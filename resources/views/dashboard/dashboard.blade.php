@extends('dashboard.template')
@section('content')

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --info-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .stat-card {
        border-radius: 16px;
        padding: 1.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        font-weight: 500;
    }

    .stat-change {
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
    }

    .card-custom {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: box-shadow 0.3s ease;
    }

    .card-custom:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .card-header-custom {
        background: transparent;
        border-bottom: 2px solid #f0f0f0;
        padding: 1.25rem 1.5rem;
    }

    .table-custom {
        font-size: 0.95rem;
    }

    .table-custom thead th {
        border-bottom: 2px solid #e5e7eb;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem;
    }

    .table-custom tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .progress-custom {
        height: 8px;
        border-radius: 10px;
        overflow: hidden;
    }

    .meja-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 1rem;
    }

    .meja-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .meja-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .meja-card.tersedia {
        border-color: #22c55e;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }

    .meja-card.terisi {
        border-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }

    .chart-container {
        position: relative;
        height: 300px;
        padding: 1rem;
    }

    @media (max-width: 768px) {
        .stat-value {
            font-size: 1.5rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }

        .meja-grid {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 0.75rem;
        }
    }
</style>

@php
$user = Auth::user();
$level = $user->id_level;
@endphp

{{-- ADMIN DASHBOARD --}}
@if ($level == 1)
<!-- STAT CARDS -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--success-gradient);">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-label">Pendapatan Hari Ini</div>
            <div class="stat-value">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</div>
            <div class="stat-change">
                <i class="fas fa-arrow-up"></i> Aktif
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--info-gradient);">
            <div class="stat-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-label">Transaksi Hari Ini</div>
            <div class="stat-value">{{ $transaksiHariIni }}</div>
            <div class="stat-change">
                <i class="fas fa-chart-line"></i> Transaksi
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--warning-gradient);">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-label">Total Staff</div>
            <div class="stat-value">{{ $totalKasir + $totalWaiter }}</div>
            <div class="stat-change">
                Kasir: {{ $totalKasir }} | Waiter: {{ $totalWaiter }}
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--primary-gradient);">
            <div class="stat-icon">
                <i class="fas fa-chair"></i>
            </div>
            <div class="stat-label">Meja Aktif</div>
            <div class="stat-value">{{ $mejaAktif }}/{{ $totalMeja }}</div>
            <div class="stat-change">
                {{ $totalMeja > 0 ? round(($mejaAktif / $totalMeja) * 100) : 0 }}% Terisi
            </div>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="row g-4">
    <!-- TRANSAKSI TERBARU -->
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header card-header-custom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-list text-primary me-2"></i>
                    Transaksi Terbaru
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>ORDER ID</th>
                                <th>CUSTOMER</th>
                                <th>MEJA</th>
                                <th>TOTAL</th>
                                <th>STATUS</th>
                                <th>WAKTU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksiTerbaru as $order)
                            <tr>
                                <td><strong>#{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>{{ $order->order_csname }}</td>
                                <td>
                                    <span class="badge badge-custom bg-secondary">
                                        {{ $order->meja?->meja_nama ?? 'Takeaway' }}
                                    </span>
                                </td>
                                <td class="fw-bold text-success">Rp {{ number_format($order->order_total, 0, ',', '.') }}</td>
                                <td>
                                    @if($order->order_status == 'success')
                                    <span class="badge badge-custom bg-success">Selesai</span>
                                    @elseif($order->order_status == 'pending')
                                    <span class="badge badge-custom bg-warning">Pending</span>
                                    @else
                                    <span class="badge badge-custom bg-danger">{{ $order->order_status }}</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $order->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Belum ada transaksi hari ini</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MENU POPULER -->
    <div class="col-lg-4">
        <div class="card card-custom">
            <div class="card-header card-header-custom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-fire text-danger me-2"></i>
                    Menu Populer
                </h5>
            </div>
            <div class="card-body">
                @forelse($menuPopuler as $menu)
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <h6 class="mb-1 fw-semibold">{{ $menu->trans_name }}</h6>
                        <small class="text-muted">{{ $menu->total_terjual }} terjual</small>
                    </div>
                    <div class="badge badge-custom bg-primary">
                        Top {{ $loop->iteration }}
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-utensils fa-2x mb-3 opacity-25"></i>
                    <p class="mb-0">Belum ada data</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- WAITER DASHBOARD --}}
@elseif ($level == 2)
<!-- STAT CARDS -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--success-gradient);">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-label">Meja Tersedia</div>
            <div class="stat-value">{{ $mejaTersedia }}</div>
            <div class="stat-change">Siap Digunakan</div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--danger-gradient);">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-label">Meja Terisi</div>
            <div class="stat-value">{{ $mejaTerisi }}</div>
            <div class="stat-change">Sedang Digunakan</div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--warning-gradient);">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-label">Order Pending</div>
            <div class="stat-value">{{ $orderPending->count() }}</div>
            <div class="stat-change">Perlu Perhatian</div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--info-gradient);">
            <div class="stat-icon">
                <i class="fas fa-chair"></i>
            </div>
            <div class="stat-label">Total Meja</div>
            <div class="stat-value">{{ $totalMeja }}</div>
            <div class="stat-change">Kapasitas Penuh</div>
        </div>
    </div>
</div>

<!-- STATUS MEJA -->
<div class="row g-4">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header card-header-custom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                    Status Meja Real-Time
                </h5>
            </div>
            <div class="card-body">
                <div class="meja-grid">
                    @foreach($daftarMeja as $meja)
                    <div class="meja-card {{ $meja->meja_status == 'kosong' ? 'tersedia' : 'terisi' }}">
                        <div class="fs-3 mb-2">
                            @if($meja->meja_status == 'kosong')
                            <i class="fas fa-check-circle text-success"></i>
                            @else
                            <i class="fas fa-user-friends text-danger"></i>
                            @endif
                        </div>
                        <h6 class="mb-1 fw-bold">{{ $meja->meja_nama }}</h6>
                        <small class="text-muted">{{ $meja->meja_kapasitas }} orang</small>
                        <div class="mt-2">
                            <span class="badge badge-custom {{ $meja->meja_status == 'kosong' ? 'bg-success' : 'bg-danger' }}">
                                {{ $meja->meja_status == 'kosong' ? 'Tersedia' : 'Terisi' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- ORDER PENDING -->
    @if($orderPending->count() > 0)
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header card-header-custom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-hourglass-half text-warning me-2"></i>
                    Order Pending
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>ORDER ID</th>
                                <th>CUSTOMER</th>
                                <th>MEJA</th>
                                <th>TOTAL</th>
                                <th>WAKTU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderPending as $order)
                            <tr>
                                <td><strong>#{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>{{ $order->order_csname }}</td>
                                <td>
                                    <span class="badge badge-custom bg-secondary">
                                        {{ $order->order_meja ?? 'Takeaway' }}
                                    </span>
                                </td>
                                <td class="fw-bold">Rp {{ number_format($order->order_total, 0, ',', '.') }}</td>
                                <td class="text-muted">{{ $order->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- KASIR DASHBOARD --}}
@elseif ($level == 3)
<!-- STAT CARDS -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--success-gradient);">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-label">Pendapatan Hari Ini</div>
            <div class="stat-value">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--info-gradient);">
            <div class="stat-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-label">Transaksi Hari Ini</div>
            <div class="stat-value">{{ $transaksiHariIni }}</div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--warning-gradient);">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-label">Item Terjual</div>
            <div class="stat-value">{{ $itemTerjualHariIni }}</div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--primary-gradient);">
            <div class="stat-icon">
                <i class="fas fa-chair"></i>
            </div>
            <div class="stat-label">Meja Aktif</div>
            <div class="stat-value">{{ $mejaAktif }}/{{ $totalMeja }}</div>
        </div>
    </div>
</div>

<!-- QUICK ACTIONS -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <a href="{{ route('dashboard.order') }}" class="btn btn-lg w-100 py-3" style="background: var(--primary-gradient); color: white; border: none; border-radius: 12px;">
            <i class="fas fa-plus-circle fa-2x mb-2"></i>
            <br><strong>Order Baru</strong>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('dashboard.transaksi') }}" class="btn btn-lg w-100 py-3" style="background: var(--info-gradient); color: white; border: none; border-radius: 12px;">
            <i class="fas fa-history fa-2x mb-2"></i>
            <br><strong>Riwayat Transaksi</strong>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('dashboard.laporan') }}" class="btn btn-lg w-100 py-3" style="background: var(--success-gradient); color: white; border: none; border-radius: 12px;">
            <i class="fas fa-chart-bar fa-2x mb-2"></i>
            <br><strong>Laporan</strong>
        </a>
    </div>
</div>

<!-- ORDER HARI INI & MENU POPULER -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header card-header-custom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-shopping-cart text-primary me-2"></i>
                    Order Hari Ini
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>ORDER ID</th>
                                <th>CUSTOMER</th>
                                <th>TOTAL</th>
                                <th>STATUS</th>
                                <th>WAKTU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orderHariIni as $order)
                            <tr>
                                <td><strong>#{{ str_pad($order->order_id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>{{ $order->order_csname }}</td>
                                <td class="fw-bold text-success">Rp {{ number_format($order->order_total, 0, ',', '.') }}</td>
                                <td>
                                    @if($order->order_status == 'success')
                                    <span class="badge badge-custom bg-success">Selesai</span>
                                    @elseif($order->order_status == 'pending')
                                    <span class="badge badge-custom bg-warning">Pending</span>
                                    @else
                                    <span class="badge badge-custom bg-info">{{ $order->order_status }}</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $order->created_at->format('H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Belum ada order hari ini</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-custom">
            <div class="card-header card-header-custom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-fire text-danger me-2"></i>
                    Menu Populer Hari Ini
                </h5>
            </div>
            <div class="card-body">
                @forelse($menuPopulerHariIni as $menu)
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <h6 class="mb-1 fw-semibold">{{ $menu->trans_name }}</h6>
                        <small class="text-muted">{{ $menu->total_terjual }} terjual</small>
                    </div>
                    <div class="badge badge-custom bg-primary">
                        {{ $loop->iteration }}
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-utensils fa-2x mb-3 opacity-25"></i>
                    <p class="mb-0">Belum ada data</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- OWNER DASHBOARD --}}
@else
<!-- STAT CARDS -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--success-gradient);">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-label">Pendapatan Bulan Ini</div>
            <div class="stat-value">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</div>
            <div class="stat-change">
                @if($perubahanPendapatan > 0)
                <i class="fas fa-arrow-up"></i> +{{ number_format($perubahanPendapatan, 1) }}%
                @elseif($perubahanPendapatan < 0)
                    <i class="fas fa-arrow-down"></i> {{ number_format($perubahanPendapatan, 1) }}%
                    @else
                    <i class="fas fa-minus"></i> 0%
                    @endif
                    dari bulan lalu
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--info-gradient);">
            <div class="stat-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-label">Transaksi Bulan Ini</div>
            <div class="stat-value">{{ $transaksiBulanIni }}</div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--warning-gradient);">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-label">Pendapatan Hari Ini</div>
            <div class="stat-value">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="background: var(--primary-gradient);">
            <div class="stat-icon">
                <i class="fas fa-hamburger"></i>
            </div>
            <div class="stat-label">Total Menu</div>
            <div class="stat-value">{{ $totalMenu }}</div>
        </div>
    </div>
</div>

<!-- MENU TERLARIS -->
<div class="row g-4">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header card-header-custom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-trophy text-warning me-2"></i>
                    Top 10 Menu Terlaris
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>RANK</th>
                                <th>NAMA MENU</th>
                                <th>TOTAL TERJUAL</th>
                                <th>TOTAL PENDAPATAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menuTerlaris as $menu)
                            <tr>
                                <td>
                                    <div class="badge badge-custom {{ $loop->iteration <= 3 ? 'bg-warning' : 'bg-secondary' }}">
                                        #{{ $loop->iteration }}
                                    </div>
                                </td>
                                <td><strong>{{ $menu->trans_name }}</strong></td>
                                <td>{{ $menu->total_terjual }} item</td>
                                <td class="fw-bold text-success">Rp {{ number_format($menu->total_pendapatan, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Belum ada data penjualan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection