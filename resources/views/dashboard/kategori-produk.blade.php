@extends('dashboard.template')
@section('content')

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Kategori</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalKategori">
            <i class="fas fa-plus"></i> Tambah Kategori
        </button>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Kategori</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kategori as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->kategori_name }}</td>
                        <td>
                            <!-- EDIT -->
                            <button
                                class="btn btn-warning btn-sm btn-edit"
                                data-id="{{ $item->kategori_id }}"
                                data-name="{{ $item->kategori_name }}"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditKategori">
                                <i class="fas fa-edit"></i>
                            </button>
                            <!-- Modal Edit Kategori -->
                            <div class="modal fade" id="modalEditKategori" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" class="modal-content" id="formEditKategori">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Kategori</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Kategori</label>
                                                <input
                                                    type="text"
                                                    name="kategori_name"
                                                    id="edit_kategori_name"
                                                    class="form-control"
                                                    placeholder="Contoh: Makanan, Minuman, Snack" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Batal
                                            </button>
                                            <button type="submit" class="btn btn-warning">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- HAPUS -->
                            <form id="form-delete-{{ $item->kategori_id }}"
                                action="{{ route('dashboard.kategori.destroy', $item->kategori_id) }}"
                                method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    class="btn btn-danger btn-sm btn-delete"
                                    data-id="{{ $item->kategori_id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Belum ada data kategori
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalKategori" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('dashboard.store.kategori') }}" method="POST" class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input
                        type="text"
                        name="kategori_name"
                        class="form-control"
                        placeholder="Contoh: Makanan, Minuman, Snack" autocomplete="off">
                    <small class="text-muted">
                        Digunakan untuk mengelompokkan menu pesanan seperti makanan, minuman, atau snack.
                    </small>
                </div>
            </div>

            <div class="modal-footer">
                <!-- ⬇️ INI WAJIB type="button" -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>

                <!-- ⬇️ CUMA INI yang submit -->
                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit');
        const formEdit = document.getElementById('formEditKategori');
        const inputName = document.getElementById('edit_kategori_name');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;

                inputName.value = name;
                formEdit.action = `/dashboard/kategori-produk/${id}`;
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {

        // DELETE SWEETALERT
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;

                Swal.fire({
                    title: 'Hapus kategori?',
                    text: 'Data yang dihapus tidak bisa dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('form-delete-' + id).submit();
                    }
                });
            });
        });

    });
</script>


@endsection