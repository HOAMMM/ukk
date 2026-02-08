@extends('dashboard.template')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Daftar Pesanan</h2>

    <table class="table table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID Pesanan</th>
                <th>Nama Pelanggan</th>
                <th>Meja</th>
                <th>Status</th>
                <th width="120">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>{{ $order->order_id }}</td>
                <td>{{ $order->order_csname }}</td>
                <td>{{ $order->meja->meja_nama ?? 'Takeaway' }}</td>
                <td>
                    <span class="badge bg-info">
                        {{ $order->order_status }}
                    </span>
                </td>
                <td>
                    <button
                        type="button"
                        class="btn btn-primary btn-sm btn-detail"
                        data-id="{{ $order->order_id }}">
                        Lihat Detail
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- MODAL DETAIL (SATU AJA) --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="detail-content" class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
    const detailUrl = "{{ route('dashboard.detail.pesanan', ':id') }}";
    const modalEl = document.getElementById('detailModal');
    const modal = new bootstrap.Modal(modalEl);

    document.querySelectorAll('.btn-detail').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.dataset.id;

            document.getElementById('detail-content').innerHTML = `
                <div class="spinner-border text-primary"></div>
            `;

            fetch(detailUrl.replace(':id', orderId))
                .then(res => {
                    if (!res.ok) throw new Error('Not Found');
                    return res.text();
                })
                .then(html => {
                    document.getElementById('detail-content').innerHTML = html;
                    modal.show();
                })
                .catch(() => {
                    document.getElementById('detail-content').innerHTML = `
                        <div class="alert alert-danger text-center">
                            Detail pesanan tidak ditemukan
                        </div>
                    `;
                    modal.show();
                });
        });
    });
</script>
@endsection