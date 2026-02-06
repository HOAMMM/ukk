@extends('dashboard.template')

@section('content')
<style>
    /* ... (Gunakan style yang sudah Anda buat) ... */
    .filter-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #edf2f7;
    }

    /* Custom Pagination Style */
    .pagination {
        margin-bottom: 0;
        gap: 5px;
    }

    .page-item .page-link {
        border-radius: 8px !important;
        border: 1px solid #edf2f7;
        color: #4a5568;
        padding: 0.5rem 0.85rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
        color: #fff;
        box-shadow: 0 4px 6px rgba(78, 115, 223, 0.25);
    }

    .page-item .page-link:hover:not(.active) {
        background-color: #f8f9fc;
        border-color: #d1d3e2;
        color: #2e59d9;
    }

    .page-item.disabled .page-link {
        background-color: #f8f9fa;
        border-color: #edf2f7;
        color: #cbd5e0;
    }

    /* Card Footer Adjustment */
    .card-footer {
        background-color: #fff !important;
        border-top: 1px solid #edf2f7 !important;
        padding: 1.25rem !important;
    }

    .pagination-info {
        font-size: 0.875rem;
        color: #718096;
    }
</style>

<div class="container-fluid">
    {{-- HEADER --}}
    <div class="page-header">
        <div class="page-title-wrapper">
            <h1 class="page-title">Generate Laporan</h1>
        </div>


    </div>

    {{-- FILTER SYSTEM --}}
    <div class="filter-card shadow-sm">
        <div class="d-flex gap-2 mb-2">
            <!-- <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-print me-1"></i> Cetak PDF
            </button> -->
            <a href="{{ route('laporan.export.excel', request()->all()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel me-1"></i> Download Excel
            </a>
        </div>
        <form action="{{ route('dashboard.laporan') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Mulai Tanggal</label>
                <input type="date" name="tgl_mulai" class="form-control" value="{{ request('tgl_mulai') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Sampai Tanggal</label>
                <input type="date" name="tgl_selesai" class="form-control" value="{{ request('tgl_selesai') }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sync-alt me-1"></i> Generate Laporan
                </button>
                <a href="{{ route('dashboard.laporan') }}" class="btn btn-light border">Reset</a>
            </div>
        </form>
    </div>

    {{-- SUMMARY CARDS (Gunakan kode Anda sebelumnya) --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card report-card shadow-sm text-center p-4">
                <div class="summary-label">Total Omzet Periode Ini</div>
                <div class="summary-value text-primary">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card report-card shadow-sm text-center p-4">
                <div class="summary-label">Jumlah Transaksi</div>
                <div class="summary-value text-success">{{ $total_orderan }} Pesanan</div>
            </div>
        </div>
    </div>

    {{-- CHART & TABLE (Gunakan kode Anda sebelumnya) --}}
    {{-- CONTENT --}}
    <div class="row g-4">
        {{-- CHART --}}

        {{-- TRANSAKSI TABLE --}}
        <div class="col-lg-12">
            {{-- Logika: Hanya tampilkan Card Tabel jika data tidak kosong --}}
            <div class="card report-card shadow-sm">
                <div class="card-header bg-white d-flex align-items-center">
                    <i class="fas fa-clipboard-list me-2 text-primary"></i>
                    <span class="fw-semibold">Detail Transaksi Periode Ini</span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Meja</th>
                                    <th>Total</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orderan_terbaru as $order)
                                <tr>
                                    <td>{{ $order->order_csname }}</td>
                                    <td>{{ $order->order_meja ?? 'T/A' }}</td>
                                    <td>Rp {{ number_format($order->order_total, 0, ',', '.') }}</td>
                                    <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox me-1"></i>
                                        Tidak ada data transaksi pada periode ini
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- FOOTER PAGINATION --}}
                @if($orderan_terbaru->total() > 0)
                <div class="card-footer">
                    <div class="row align-items-center">
                        {{-- Info Status --}}
                        <div class="col-12 col-md-6 mb-3 mb-md-0 text-center text-md-start">
                            <span class="pagination-info">
                                Menampilkan <strong>{{ $orderan_terbaru->firstItem() }}</strong> - <strong>{{ $orderan_terbaru->lastItem() }}</strong>
                                dari <strong>{{ $orderan_terbaru->total() }}</strong> transaksi
                            </span>
                        </div>

                        {{-- Link Navigasi --}}
                        <div class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                            {{ $orderan_terbaru->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT CHART.JS (Gunakan kode Anda sebelumnya) --}}
@endsection