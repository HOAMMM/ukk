@extends('dashboard.template')

@section('content')
<style>
    .filter-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #edf2f7;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        border: none;
        transition: transform 0.2s;
    }

    .summary-card:hover {
        transform: translateY(-5px);
    }

    .summary-card.revenue {
        background: linear-gradient(135deg, #eb6410 0%, #c5655f 100%);
    }

    .summary-card.transaction {
        background: linear-gradient(135deg, #c7403b 0%, #f5576c 100%);
    }

    .summary-card.average {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .summary-label {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }

    .summary-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }

    .report-card {
        border: 1px solid #edf2f7;
        border-radius: 12px;
        transition: box-shadow 0.2s;
    }

    .report-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .table thead th {
        background-color: #f8f9fc;
        color: #5a5c69;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e3e6f0;
        padding: 1rem 0.75rem;
    }

    .table tbody tr {
        transition: background-color 0.15s;
    }

    .table tbody tr:hover {
        background-color: #f8f9fc;
    }

    .pagination {
        margin-bottom: 0;
        gap: 5px;
    }

    .page-item .page-link {
        border-radius: 8px;
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

    .card-footer {
        background-color: #fff !important;
        border-top: 1px solid #edf2f7 !important;
        padding: 1.25rem !important;
    }

    .pagination-info {
        font-size: 0.875rem;
        color: #718096;
    }

    .btn {
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0.5rem 0.75rem;
        transition: border-color 0.15s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
    }

    .badge-status {
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: #a0aec0;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .filter-badge {
        display: inline-block;
        background: #e6f4ea;
        color: #137333;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
        margin-left: 0.5rem;
    }
</style>

<div class="container-fluid">
    {{-- HEADER --}}
    <div class="page-header">
        <div class="page-title-wrapper">
            <h1 class="page-title">
                <i class="fas fa-chart-line me-2 text-primary"></i>
                Laporan Keuangan
                <!-- <span class="filter-badge">
                    <i class="fas fa-check-circle me-1"></i> Status: PAID Only
                </span> -->
            </h1>
            <p class="text-muted mb-0">
                Rekap pendapatan & transaksi berdasarkan periode (hanya transaksi yang sudah dibayar)
            </p>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="summary-card revenue shadow">
                <div class="summary-label">
                    <i class="fas fa-wallet me-1"></i> Total Pendapatan
                </div>
                <div class="summary-value">
                    Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card transaction shadow">
                <div class="summary-label">
                    <i class="fas fa-shopping-cart me-1"></i> Total Transaksi
                </div>
                <div class="summary-value">
                    {{ number_format($total_orderan) }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card average shadow">
                <div class="summary-label">
                    <i class="fas fa-chart-bar me-1"></i> Rata-rata Transaksi
                </div>
                <div class="summary-value">
                    Rp {{ $total_orderan ? number_format($total_pendapatan / $total_orderan, 0, ',', '.') : 0 }}
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER SYSTEM --}}
    <div class="filter-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filter Laporan
            </h5>
            <a href="{{ route('laporan.export.excel', request()->all()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel me-1"></i> Download Excel
            </a>
        </div>

        <form action="{{ route('dashboard.laporan') }}" method="GET" id="filterForm">
            <div class="row g-3 align-items-end">
                {{-- BULAN --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">
                        <i class="far fa-calendar me-1"></i> Bulan
                    </label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                            @endfor
                    </select>
                </div>

                {{-- TAHUN --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">
                        <i class="far fa-calendar-alt me-1"></i> Tahun
                    </label>
                    <select name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                        @endfor
                    </select>
                </div>

                {{-- MULAI TANGGAL --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">
                        <i class="far fa-calendar-check me-1"></i> Dari Tanggal
                    </label>
                    <input type="date" name="tgl_mulai" class="form-control"
                        value="{{ request('tgl_mulai') }}"
                        max="{{ date('Y-m-d') }}">
                </div>

                {{-- SAMPAI TANGGAL --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">
                        <i class="far fa-calendar-times me-1"></i> Sampai Tanggal
                    </label>
                    <input type="date" name="tgl_selesai" class="form-control"
                        value="{{ request('tgl_selesai') }}"
                        max="{{ date('Y-m-d') }}">
                </div>

                {{-- BUTTONS --}}
                <div class="col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('dashboard.laporan') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- DETAIL TRANSAKSI TABLE --}}
    <div class="row g-4">
        <div class="col-12">
            <div class="card report-card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-list-ul me-2 text-primary"></i>
                        <span class="fw-semibold">Detail Transaksi</span>
                    </div>
                    @if($orderan_terbaru->total() > 0)
                    <span class="badge bg-primary">{{ $orderan_terbaru->total() }} transaksi</span>
                    @endif
                </div>

                <div class="card-body p-0">
                    @if($orderan_terbaru->total() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 30%">Customer</th>
                                    <th style="width: 15%">Meja</th>
                                    <th style="width: 25%">Total</th>
                                    <th style="width: 25%">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderan_terbaru as $index => $order)
                                <tr>
                                    <td class="text-center text-muted">
                                        {{ $orderan_terbaru->firstItem() + $index }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $order->order_csname }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-status bg-light text-dark">
                                            {{ $order->meja_nama ?? 'Takeaway' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-success">
                                            Rp {{ number_format($order->order_total, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <i class="far fa-clock me-1 text-muted"></i>
                                        {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5 class="mt-2">Tidak Ada Data</h5>
                        <p class="text-muted">Tidak ada transaksi PAID pada periode yang dipilih</p>
                    </div>
                    @endif
                </div>

                {{-- FOOTER PAGINATION --}}
                @if($orderan_terbaru->total() > 0)
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 mb-3 mb-md-0 text-center text-md-start">
                            <span class="pagination-info">
                                Menampilkan
                                <strong>{{ $orderan_terbaru->firstItem() }}</strong> -
                                <strong>{{ $orderan_terbaru->lastItem() }}</strong>
                                dari
                                <strong>{{ $orderan_terbaru->total() }}</strong> transaksi
                            </span>
                        </div>

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

@push('scripts')
<script>
    /* =============================
       VALIDASI TANGGAL SEBELUM SUBMIT
    ============================= */
    document.getElementById('filterForm')?.addEventListener('submit', function(e) {
        const tglMulai = this.querySelector('[name="tgl_mulai"]').value;
        const tglSelesai = this.querySelector('[name="tgl_selesai"]').value;

        // Validasi: tanggal awal tidak boleh lebih besar dari tanggal akhir
        if (tglMulai && tglSelesai && tglMulai > tglSelesai) {
            e.preventDefault();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Tanggal tidak valid',
                    text: 'Tanggal awal tidak boleh melebihi tanggal akhir',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4e73df'
                });
            } else {
                alert('Tanggal awal tidak boleh melebihi tanggal akhir');
            }
        }
    });
</script>
@endpush

@endsection