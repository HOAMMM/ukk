@extends('dashboard.template')
@section('content')

<!-- STAT CARDS -->
<div class="row g-4 mb-4">

    <!-- Pendapatan -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="p-4 rounded text-white shadow"
            style="background: linear-gradient(135deg,#22c55e,#16a34a);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="opacity-75">Pendapatan Hari Ini</small>
                    <h3 class="fw-bold mt-2">Rp {{ number_format(5250000,0,',','.') }}</h3>
                    <small class="opacity-75">
                        <i class="fas fa-arrow-up"></i> 12.5% dari kemarin
                    </small>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="fas fa-money-bill-wave fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="p-4 rounded text-white shadow"
            style="background: linear-gradient(135deg,#3b82f6,#2563eb);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="opacity-75">Total Transaksi</small>
                    <h3 class="fw-bold mt-2">156</h3>
                    <small class="opacity-75">
                        <i class="fas fa-arrow-up"></i> 8 transaksi/jam
                    </small>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="fas fa-receipt fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Terjual -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="p-4 rounded text-white shadow"
            style="background: linear-gradient(135deg,#a855f7,#7c3aed);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="opacity-75">Menu Terjual</small>
                    <h3 class="fw-bold mt-2">342</h3>
                    <small class="opacity-75">
                        <i class="fas fa-arrow-up"></i> Nasi Goreng
                    </small>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="fas fa-hamburger fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Meja -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="p-4 rounded text-white shadow"
            style="background: linear-gradient(135deg,#fb923c,#ea580c);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="opacity-75">Meja Aktif</small>
                    <h3 class="fw-bold mt-2">12 / 20</h3>
                    <small class="opacity-75">
                        <i class="fas fa-chart-line"></i> Okupansi 60%
                    </small>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="fas fa-chair fs-3"></i>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- MAIN CONTENT -->
<div class="row g-4">

    <!-- TRANSAKSI TERBARU -->
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between">
                <h5 class="mb-0 fw-semibold">Transaksi Terbaru</h5>
                <a href="#" class="text-orange text-decoration-none">Lihat Semua</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice</th>
                            <th>Meja</th>
                            <th>Waktu</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach([1,2,3,4,5] as $i)
                        <tr>
                            <td class="fw-semibold">#INV-2024-00{{ $i }}</td>
                            <td>Meja {{ rand(1,15) }}</td>
                            <td>14:{{ rand(10,59) }}</td>
                            <td class="fw-semibold">Rp {{ number_format(rand(150,450)*1000,0,',','.') }}</td>
                            <td>
                                <span class="badge bg-success">Selesai</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light">
                                    <i class="fas fa-eye text-primary"></i>
                                </button>
                                <button class="btn btn-sm btn-light ms-1">
                                    <i class="fas fa-print text-warning"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- SIDE RIGHT -->
    <div class="col-lg-4">

        <!-- QUICK ACTION -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-semibold mb-3">Quick Actions</h5>

                <a href="#" class="d-flex align-items-center p-3 rounded bg-light mb-2 text-decoration-none">
                    <div class="bg-orange text-white rounded p-2">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span class="ms-3 fw-semibold text-dark">Transaksi Baru</span>
                </a>

                <a href="#" class="d-flex align-items-center p-3 rounded bg-light mb-2 text-decoration-none">
                    <div class="bg-primary text-white rounded p-2">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <span class="ms-3 fw-semibold text-dark">Cetak Laporan</span>
                </a>

                <a href="#" class="d-flex align-items-center p-3 rounded bg-light text-decoration-none">
                    <div class="bg-success text-white rounded p-2">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <span class="ms-3 fw-semibold text-dark">Kelola Stok</span>
                </a>
            </div>
        </div>

        <!-- MENU POPULER -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="fw-semibold mb-3">Menu Populer Hari Ini</h5>

                @php
                $menus = [
                ['Nasi Goreng',42,'fire'],
                ['Es Teh',68,'coffee'],
                ['Ayam Geprek',38,'drumstick-bite'],
                ['Mie Goreng',31,'pizza-slice'],
                ];
                @endphp

                @foreach($menus as $m)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning-subtle rounded p-2">
                            <i class="fas fa-{{ $m[2] }} text-warning"></i>
                        </div>
                        <div class="ms-3">
                            <div class="fw-semibold">{{ $m[0] }}</div>
                            <small class="text-muted">{{ $m[1] }} terjual</small>
                        </div>
                    </div>
                    <span class="fw-semibold">Rp {{ rand(20,40) }}K</span>
                </div>
                @endforeach

            </div>
        </div>

    </div>
</div>

@endsection