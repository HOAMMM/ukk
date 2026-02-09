@extends('dashboard.template')

@section('content')
<style>
    .filter-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        padding: 1rem 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .filter-info i {
        font-size: 1.5rem;
        opacity: 0.8;
    }

    .filter-info .filter-text {
        flex: 1;
        margin-left: 1rem;
    }

    .filter-info .filter-label {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 0.25rem;
    }

    .filter-info .filter-value {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .clear-filter-btn {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .clear-filter-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }
</style>

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Transactions</h3>
            <small class="text-muted">Riwayat transaksi kasir</small>
        </div>
    </div>

    {{-- FILTER INFO (Tampilkan jika ada filter aktif) --}}
    @if(request()->hasAny(['status', 'date_from', 'date_to']))
    <div class="filter-info shadow-sm">
        <i class="fas fa-filter"></i>
        <div class="filter-text">
            <div class="filter-label">Filter Aktif:</div>
            <div class="filter-value">
                @if(request('status'))
                Status: <strong>{{ ucfirst(request('status')) }}</strong>
                @endif

                @if(request('date_from') && request('date_to'))
                @if(request('status')) | @endif
                Periode: <strong>{{ date('d/m/Y', strtotime(request('date_from'))) }}</strong>
                s/d
                <strong>{{ date('d/m/Y', strtotime(request('date_to'))) }}</strong>
                @elseif(request('date_from'))
                @if(request('status')) | @endif
                Dari: <strong>{{ date('d/m/Y', strtotime(request('date_from'))) }}</strong>
                @elseif(request('date_to'))
                @if(request('status')) | @endif
                Sampai: <strong>{{ date('d/m/Y', strtotime(request('date_to'))) }}</strong>
                @endif
            </div>
        </div>
        <a href="{{ route('dashboard.transaksi') }}" class="clear-filter-btn">
            <i class="fas fa-times me-1"></i> Hapus Filter
        </a>
    </div>
    @endif

    {{-- FILTER FORM --}}
    <form id="filterForm" method="GET" action="{{ route('dashboard.transaksi') }}" class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-check-circle me-1 text-primary"></i> Status
                    </label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="far fa-calendar me-1 text-primary"></i> Dari Tanggal
                    </label>
                    <input type="date"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="form-control"
                        max="{{ date('Y-m-d') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="far fa-calendar-check me-1 text-primary"></i> Sampai Tanggal
                    </label>
                    <input type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="form-control"
                        max="{{ date('Y-m-d') }}">
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>

                        @if(request()->hasAny(['status', 'date_from', 'date_to']))
                        <a href="{{ route('dashboard.transaksi') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </form>

    {{-- TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Kasir</th>
                            <th>Status</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Bayar</th>
                            <th class="text-end">Kembali</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($transaksi as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ date('d M Y', strtotime($item->created_at)) }}</div>
                                <small class="text-muted">{{ date('H:i', strtotime($item->created_at)) }}</small>
                            </td>

                            <td class="fw-semibold text-primary">
                                {{ $item->transaksi_code }}
                            </td>

                            <td>{{ $item->transaksi_cname ?? '-' }}</td>

                            <td>
                                @if($item->transaksi_status == 'success')
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i> Success
                                </span>
                                @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i> Pending
                                </span>
                                @endif
                            </td>

                            <td class="text-end fw-semibold">
                                Rp {{ number_format($item->transaksi_total, 0, ',', '.') }}
                            </td>

                            <td class="text-end">
                                Rp {{ number_format($item->transaksi_amount, 0, ',', '.') }}
                            </td>

                            <td class="text-end text-muted">
                                Rp {{ number_format($item->transaksi_change, 0, ',', '.') }}
                            </td>

                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('dashboard.transaksi.show', $item->transaksi_id) }}"
                                        class="btn btn-outline-primary"
                                        title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <button class="btn btn-outline-danger"
                                        onclick="hapusTransaksi({{ $item->transaksi_id }})"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <h5>Tidak Ada Transaksi</h5>
                                    <p class="mb-0">
                                        @if(request()->hasAny(['status', 'date_from', 'date_to']))
                                        Tidak ada transaksi yang sesuai dengan filter
                                        @else
                                        Belum ada transaksi yang tercatat
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        @if($transaksi->total() > 0)
        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <small class="text-muted">
                        Menampilkan
                        <strong>{{ $transaksi->firstItem() }}</strong> -
                        <strong>{{ $transaksi->lastItem() }}</strong>
                        dari
                        <strong>{{ $transaksi->total() }}</strong> transaksi
                    </small>
                </div>
                <div class="col-md-6 d-flex justify-content-md-end">
                    {{ $transaksi->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>

</div>

{{-- FORM DELETE --}}
<form id="deleteForm" method="POST">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    /* =============================
       VALIDASI FILTER
    ============================= */
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        const dateFrom = this.querySelector('[name="date_from"]').value;
        const dateTo = this.querySelector('[name="date_to"]').value;

        if (dateFrom && dateTo && dateFrom > dateTo) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Tanggal tidak valid',
                text: 'Tanggal awal tidak boleh melebihi tanggal akhir',
                confirmButtonText: 'OK'
            });
        }
    });

    /* =============================
       DELETE TRANSAKSI
    ============================= */
    function hapusTransaksi(id) {
        Swal.fire({
            title: 'Hapus transaksi?',
            text: 'Data transaksi akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = "{{ route('dashboard.transaksi.destroy', '__id__') }}".replace('__id__', id);
                form.submit();
            }
        });
    }
</script>

{{-- SWEETALERT RESPONSE --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('
        success ') }}'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '{{ session('
        error ') }}'
    });
</script>
@endif
@endpush