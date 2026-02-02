@extends('dashboard.template')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Daftar Transaksi</h2>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="transaksiTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Kode Transaksi</th>
                            <th>Kasir</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Kembalian</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $item->transaksi_code }}</strong></td>
                            <td>{{ $item->transaksi_cname ?? '-' }}</td>

                            <td class="text-end">
                                Rp {{ number_format($item->transaksi_total,0,',','.') }}
                            </td>

                            <td class="text-end">
                                Rp {{ number_format($item->transaksi_amount,0,',','.') }}
                            </td>

                            <td class="text-end">
                                Rp {{ number_format($item->transaksi_change,0,',','.') }}
                            </td>

                            <td>
                                @if($item->transaksi_status == 'success')
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> SUCCESS
                                </span>
                                @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock"></i> PENDING
                                </span>
                                @endif
                            </td>

                            <td>{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</td>

                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('dashboard.transaksi.show', $item->transaksi_id) }}"
                                        class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- @if($item->transaksi_status == 'pending')
                                    <button type="button" class="btn btn-sm btn-success"
                                        onclick="updateStatus({{ $item->transaksi_id }})">
                                        <i class="fas fa-money-bill"></i>
                                    </button>
                                    @endif -->

                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="hapus({{ $item->transaksi_id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                Belum ada transaksi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- FORM --}}
<form id="statusForm" method="POST">
    @csrf
    @method('PUT')
</form>
<form id="deleteForm" method="POST">
    @csrf
    @method('DELETE')
</form>



@push('scripts')
<script>
    function updateStatus(id) {
        Swal.fire({
            title: 'Selesaikan transaksi?',
            text: 'Status transaksi akan diubah menjadi SUCCESS',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Selesaikan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#198754',
            cancelButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById('statusForm');
                form.action = `/dashboard/transaksi/${id}/success`;
                form.submit();
            }
        });
    }

    function hapus(id) {
        Swal.fire({
            title: 'Hapus transaksi?',
            text: 'Data transaksi akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = "{{ route('dashboard.transaksi.destroy', ['id' => '__id__']) }}".replace('__id__', id);
                form.submit();
            }
        });
    }


    // $(function() {
    //     $('#transaksiTable').DataTable({
    //         order: [
    //             [7, 'desc']
    //         ],
    //         language: {
    //             url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
    //         }
    //     });
    // });
</script>
@endpush


@endsection