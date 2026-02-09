@extends('dashboard.template')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Transactions</h3>
            <small class="text-muted">Riwayat transaksi kasir</small>
        </div>
    </div>

    {{-- FILTER --}}
    <form id="filterForm" method="GET" class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="success" {{ request('status')=='success'?'selected':'' }}>Success</option>
                        <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>

                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
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
                                <span class="text-success fw-semibold">● Success</span>
                                @else
                                <span class="text-warning fw-semibold">● Pending</span>
                                @endif
                            </td>

                            <td class="text-end fw-semibold">
                                Rp {{ number_format($item->transaksi_total,0,',','.') }}
                            </td>

                            <td class="text-end">
                                Rp {{ number_format($item->transaksi_amount,0,',','.') }}
                            </td>

                            <td class="text-end text-muted">
                                Rp {{ number_format($item->transaksi_change,0,',','.') }}
                            </td>

                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('dashboard.transaksi.show',$item->transaksi_id) }}"
                                        class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <button class="btn btn-outline-danger"
                                        onclick="hapusTransaksi({{ $item->transaksi_id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Belum ada transaksi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Menampilkan {{ $transaksi->count() }} data
            </small>
            {{ $transaksi->links() }}
        </div>
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
        const from = this.querySelector('[name="from"]').value;
        const to = this.querySelector('[name="to"]').value;

        if (from && to && from > to) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Tanggal tidak valid',
                text: 'Tanggal awal tidak boleh melebihi tanggal akhir'
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
                form.action = "{{ route('dashboard.transaksi.destroy','__id__') }}".replace('__id__', id);
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