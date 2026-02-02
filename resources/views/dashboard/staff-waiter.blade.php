@extends('dashboard.template')
@section('content')
<style>
    .title-waiter {
        font-size: 1.2rem;
    }
</style>
<div class="card mb-4 shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between">
        <p class="card-title title-waiter">Staff Waiter</p>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <i class="fas fa-plus"></i> Tambah Waiter
        </button>
    </div>

    <!-- MODAL -->
    <div class="modal fade" id="exampleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formwaiter" action="{{ route('dashboard.waiter.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Staff waiter</h5>
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
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
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
                                            <h5 class="modal-title">Detail Staff waiter</h5>
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

                            <button class="btn btn-sm btn-warning"
                                onclick="showEdit({{ $item }})">
                                <i class="fas fa-pencil"></i>
                            </button>
                            <div class="modal fade" id="editModal" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" id="editForm">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Staff waiter</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <input type="hidden" id="e_id">

                                                <div class="mb-3">
                                                    <label>Username</label>
                                                    <input type="text" class="form-control" name="username" id="e_username">
                                                </div>

                                                <div class="mb-3">
                                                    <label>Nama Lengkap</label>
                                                    <input type="text" class="form-control" name="namleng" id="e_namleng">
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label>Email</label>
                                                        <input type="email" class="form-control" name="email" id="e_email">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label>No HP</label>
                                                        <input type="text" class="form-control" name="user_phone" id="e_phone">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Password Saat Ini</label>
                                                    <div class="input-group">
                                                        <input type="password"
                                                            class="form-control"
                                                            id="current_password"
                                                            name="current_password"
                                                            placeholder="Password lama">
                                                        <span class="input-group-text"
                                                            style="cursor:pointer"
                                                            onclick="togglePassword('current_password', this)">
                                                            <i class="fas fa-eye"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <small class="text-muted">
                                                        *Isi jika ingin mengganti password
                                                    </small>
                                                    <div class="col-md-6 mb-3">
                                                        <label>Password Baru</label>
                                                        <div class="input-group">
                                                            <input type="password"
                                                                class="form-control"
                                                                id="password"
                                                                name="password"
                                                                placeholder="Password baru">
                                                            <span class="input-group-text"
                                                                style="cursor:pointer"
                                                                onclick="togglePassword('password', this)">
                                                                <i class="fas fa-eye"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label>Konfirmasi Password Baru</label>
                                                        <div class="input-group">
                                                            <input type="password"
                                                                class="form-control"
                                                                id="password_confirmation"
                                                                name="password_confirmation"
                                                                placeholder="Ulangi password baru">
                                                            <span class="input-group-text"
                                                                style="cursor:pointer"
                                                                onclick="togglePassword('password_confirmation', this)">
                                                                <i class="fas fa-eye"></i>
                                                            </span>
                                                        </div>
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

                            <form id="formHapus{{ $item->id }}"
                                action="{{ route('dashboard.waiter.destroy', $item->id) }}"
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
                            Data staff waiter belum ada
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
            text: 'Pastikan data staff waiter sudah benar',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formwaiter').submit();
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

        document.getElementById('editForm').action =
            `/dashboard/staff-waiter/${data.id}`;

        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
</script>


@endsection