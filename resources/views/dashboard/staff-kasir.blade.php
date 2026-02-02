@extends('dashboard.template')
@section('content')
<style>
    .title-kasir {
        font-size: 1.2rem;
    }
</style>
<div class="card mb-4 shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between">
        <p class="card-title title-kasir">Staff Kasir</p>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <i class="fas fa-plus"></i> Tambah Kasir
        </button>
    </div>

    <!-- MODAL -->
    <div class="modal fade" id="exampleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formkasir" action="{{ route('dashboard.kasir.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Staff Kasir</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Username">
                        </div>

                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="namleng" class="form-control" placeholder="Nama Lengkap">
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label>No HP / WA</label>
                                <input type="number" min="0" name="user_phone" class="form-control" placeholder="No Hp/Wa">
                            </div>
                            <div class="col-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Email">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="password">
                        </div>

                        <div class="mb-3">
                            <label>Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="konfirmasi password">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" onclick="confirmSubmit()">
                            Simpan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle    ">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Staff</th>
                        <th>Gmail</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($staff as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->namleng }}</td>
                        <td>{{ $item->email }}</td>
                        <td>
                            <button class="btn btn-sm btn-info"
                                onclick="showDetail({{ $item }})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="modal fade" id="detailModal" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Staff Kasir</h5>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <p><b>Username:</b> <span id="d_username"></span></p>
                                            <p><b>Nama:</b> <span id="d_namleng"></span></p>
                                            <p><b>Email:</b> <span id="d_email"></span></p>
                                            <p><b>No HP:</b> <span id="d_phone"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @foreach ($staff as $item)
                            <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $item->id }}">
                                <i class="fas fa-pencil"></i>
                            </button>

                            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST"
                                        action="{{ route('dashboard.kasir.update', $item->id) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Staff Kasir</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">

                                                <div class="mb-3">
                                                    <label>Username</label>
                                                    <input type="text" name="username" class="form-control"
                                                        value="{{ session('edit_id') == $item->id
                                ? old('username', $item->username)
                                : $item->username }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label>Nama Lengkap</label>
                                                    <input type="text" name="namleng" class="form-control"
                                                        value="{{ session('edit_id') == $item->id
                                ? old('namleng', $item->namleng)
                                : $item->namleng }}">
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label>Email</label>
                                                        <input type="email" name="email" class="form-control"
                                                            value="{{ session('edit_id') == $item->id
                                    ? old('email', $item->email)
                                    : $item->email }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label>No HP</label>
                                                        <input type="text" name="user_phone" class="form-control"
                                                            value="{{ session('edit_id') == $item->id
                                    ? old('user_phone', $item->user_phone)
                                    : $item->user_phone }}">
                                                    </div>
                                                </div>

                                                <hr>
                                                <small class="text-muted">*Isi jika ingin mengganti password</small>

                                                <div class="mb-3">
                                                    <label>Password Saat Ini</label>
                                                    <input type="password" name="current_password" class="form-control" placeholder="password lama">
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label>Password Baru</label>
                                                        <input type="password" name="password" class="form-control" placeholder="password baru">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label>Konfirmasi Password</label>
                                                        <input type="password" name="password_confirmation" class="form-control" placeholder="konfirmasi password baru">
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button class="btn btn-primary" type="submit">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endforeach



                            <form id="formHapus{{ $item->id }}"
                                action="{{ route('dashboard.kasir.destroy', $item->id) }}"
                                method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="hapusData(event, {{ $item->id }})"
                                    class="btn btn-danger btn-sm mb-2 mt-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Data staff kasir belum ada
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>
<script>
    function confirmSubmit() {
        Swal.fire({
            title: 'Simpan data?',
            text: 'Pastikan data staff kasir sudah benar',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formkasir').submit();
            }
        });
    }

    function hapusData(event, id) {
        event.preventDefault(); // cegah submit langsung

        Swal.fire({
            title: 'Yakin?',
            text: "Data ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formHapus' + id).submit();
            }
        })
    }

    function togglePassword(inputId, el) {
        const input = document.getElementById(inputId);
        const icon = el.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function showDetail(data) {
        document.getElementById('d_username').innerText = data.username;
        document.getElementById('d_namleng').innerText = data.namleng;
        document.getElementById('d_email').innerText = data.email;
        document.getElementById('d_phone').innerText = data.user_phone;

        new bootstrap.Modal(document.getElementById('detailModal')).show();
    }

    function showEdit(data) {
        document.getElementById('e_id').value = data.id;
        document.getElementById('e_username').value = data.username;
        document.getElementById('e_namleng').value = data.namleng;
        document.getElementById('e_email').value = data.email;
        document.getElementById('e_phone').value = data.user_phone;
        // document.getElementById('current_password').value = data.user_passtext;

        document.getElementById('edit_id').action =
            `/dashboard/staff-kasir/${data.id}`;

        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
</script>


@endsection