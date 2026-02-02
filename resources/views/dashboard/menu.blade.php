@extends('dashboard.template')

@section('content')
<div class="container-fluid">

    <h4 class="fw-bold mb-3">Data Menu</h4>

    {{-- FORM TAMBAH MENU --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('dashboard.store.menu') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="menu_name" class="form-control" placeholder="Nama Menu" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <select name="menu_kategori" class="form-select">
                            <option value="">Pilih Kategori</option>
                            @foreach ($kategori as $item)
                            <option value="{{ $item->kategori_name }}">
                                {{ $item->kategori_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="menu_price" class="form-control" placeholder="Harga" autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <input type="file" name="menu_image" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100"><i class="fas fa-plus"></i> Tambah</button>
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-12">
                        <textarea name="menu_desc" class="form-control" placeholder="Deskripsi Menu" rows="2" autocomplete="off"></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE MENU --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Menu</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Deskripsi</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $menu)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($menu->menu_image)
                                <img src="{{ asset('uploads/menu/'.$menu->menu_image) }}"
                                    width="80" class="rounded shadow">
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $menu->menu_name }}</td>
                            <td>{{ $menu->menu_kategori }}</td>
                            <td>Rp {{ number_format($menu->menu_price) }}</td>
                            <td>{{ $menu->menu_desc ?? '-' }}</td>
                            <td class="text-center align-middle">
                                <button class="btn btn-sm btn-warning"
                                    onclick="showEdit({{ $menu }})">
                                    <i class="fas fa-pencil"></i>
                                </button>

                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="confirmDelete({{ $menu->menu_id }}, '{{ $menu->menu_name }}')"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Form tersembunyi untuk submit delete -->
                                <form id="delete-form-{{ $menu->menu_id }}"
                                    method="POST"
                                    action="{{ route('dashboard.menu.destroy', $menu->menu_id) }}"
                                    style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Data menu belum ada
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT MENU --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="editForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">✏️ Edit Menu</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="e_id">

                        <div class="mb-3">
                            <label>Nama Menu</label>
                            <input type="text" class="form-control" name="menu_name" id="e_menu_name">
                        </div>

                        <div class="mb-3">
                            <label>Kategori</label>
                            <select name="menu_kategori" id="e_menu_kategori" class="form-select">
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategori as $item)
                                <option value="{{ $item->kategori_name }}">
                                    {{ $item->kategori_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Harga</label>
                            <input type="number" class="form-control" name="menu_price" id="e_menu_price">
                        </div>

                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea class="form-control" name="menu_desc" id="e_menu_desc" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Gambar Menu</label>
                            <input type="file" class="form-control" name="menu_image">
                            <small class="text-muted">*Kosongkan jika tidak ingin mengubah gambar</small>
                            <div id="current_image" class="mt-2"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-success" type="submit">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(menuId, menuName) {
        Swal.fire({
            title: 'Hapus Menu?',
            html: `Apakah Anda yakin ingin menghapus menu <br><strong>${menuName}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + menuId).submit();
            }
        });
    }

    function showEdit(menu) {
        // Isi data ke form
        document.getElementById('e_id').value = menu.menu_id;
        document.getElementById('e_menu_name').value = menu.menu_name;
        document.getElementById('e_menu_kategori').value = menu.menu_kategori;
        document.getElementById('e_menu_price').value = menu.menu_price;
        document.getElementById('e_menu_desc').value = menu.menu_desc || '';

        // Tampilkan gambar saat ini jika ada
        if (menu.menu_image) {
            document.getElementById('current_image').innerHTML = `
                <label class="form-label">Gambar Saat Ini:</label><br>
                <img src="/uploads/menu/${menu.menu_image}" width="100" class="rounded shadow">
            `;
        } else {
            document.getElementById('current_image').innerHTML = '';
        }

        // Set action form untuk update
        document.getElementById('editForm').action = `/dashboard/update/menu/${menu.menu_id}`;

        // Tampilkan modal
        var modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    }
</script>
@endpush