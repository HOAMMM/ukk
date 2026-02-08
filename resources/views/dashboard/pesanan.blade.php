@extends('dashboard.template')
@section(section: 'content')
<div class="container mt-4">
    <h2>Daftar Pesanan</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Nama Pelanggan</th>
                <th>Meja</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>{{ $order->order_id }}</td>
                <td>{{ $order->order_csname }}</td>
                <td>{{ $order->meja->meja_nama ?? 'Meja tidak tersedia' }}</td>
                <td>{{ $order->order_status }}</td>
                <td>
                    <a href="javascript:void(0)"
                        class="btn btn-primary btn-sm btn-detail"
                        data-id="{{ $order->order_id }}">
                        Lihat Detail
                    </a>
                    <div class="modal fade" id="detailModal" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detail Pesanan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div id="detail-content" class="text-center">
                                        <div class="spinner-border text-primary"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    const detailUrl = "{{ route('dashboard.detail.pesanan', ':id') }}";

    document.querySelectorAll('.btn-detail').forEach(btn => {
        btn.addEventListener('click', function() {
            let orderId = this.dataset.id;

            document.getElementById('detail-content').innerHTML =
                '<div class="spinner-border text-primary"></div>';

            fetch(detailUrl.replace(':id', orderId))
                .then(res => {
                    if (!res.ok) throw new Error('404');
                    return res.text();
                })
                .then(html => {
                    document.getElementById('detail-content').innerHTML = html;
                    new bootstrap.Modal(
                        document.getElementById('detailModal')
                    ).show();
                })
                .catch(() => {
                    document.getElementById('detail-content').innerHTML =
                        '<div class="text-danger text-center">Detail pesanan tidak ditemukan</div>';
                });
        });
    });
</script>
@endsection