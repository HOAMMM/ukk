@extends('dashboard.template')
@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Pengaturan</h2>
            <p class="text-muted mb-0">Kelola pengaturan akun dan preferensi Anda</p>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Menu -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="list-group list-group-flush">
                    <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                        <i class="bi bi-person me-2"></i>Profil Saya
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-shield-lock me-2"></i>Keamanan Akun
                    </a>
                    <!-- <a href="#preferences" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-moon-stars me-2"></i>Tema Tampilan
                    </a> -->
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="col-lg-9">
            <div class="tab-content">
                <!-- Profile Settings -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Informasi Profil</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('pengaturan.update.profile') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Profile Photo -->
                                <div class="mb-4 text-center">
                                    <div class="position-relative d-inline-block">
                                        @if($user->avatar)
                                        <img src="{{ asset('storage/avatars/' . $user->avatar) }}"
                                            alt="Avatar"
                                            class="rounded-circle mb-3"
                                            width="120"
                                            height="120"
                                            id="avatar-preview"
                                            style="object-fit: cover;">
                                        @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->namleng ?? $user->username) }}&size=120&background=667eea&color=fff"
                                            alt="Avatar"
                                            class="rounded-circle mb-3"
                                            width="120"
                                            height="120"
                                            id="avatar-preview">
                                        @endif
                                        <label for="avatar" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle" style="width: 35px; height: 35px;">
                                            <i class="bi bi-camera"></i>
                                        </label>
                                        <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*">
                                    </div>
                                    @error('avatar')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="namleng" class="form-control @error('namleng') is-invalid @enderror"
                                            placeholder="Masukkan nama lengkap"
                                            value="{{ old('namleng', $user->namleng) }}"
                                            required>
                                        @error('namleng')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                            placeholder="email@example.com"
                                            value="{{ old('email', $user->email) }}"
                                            required>
                                        @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control"
                                            value="{{ $user->username }}"
                                            disabled>
                                        <small class="text-muted">Username tidak dapat diubah</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nomor Telepon</label>
                                        <input type="text" name="user_phone" class="form-control @error('user_phone') is-invalid @enderror"
                                            placeholder="08xxxxxxxxxx"
                                            value="{{ old('user_phone', $user->user_phone) }}">
                                        @error('user_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Kode User</label>
                                        <input type="text" class="form-control"
                                            value="{{ $user->user_code }}"
                                            disabled>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Level</label>
                                        <input type="text" class="form-control"
                                            value="@if($user->id_level == 1) Administrator @elseif($user->id_level == 2) Waiter @elseif($user->id_level == 3) Kasir @else Owner @endif"
                                            disabled>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Terdaftar Sejak</label>
                                    <input type="text" class="form-control"
                                        value="{{ $user->created_at ? $user->created_at->format('d F Y H:i') : '-' }}"
                                        disabled>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="tab-pane fade" id="security">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Ubah Password</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('pengaturan.update.password') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="Masukkan password lama" required>
                                    @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Masukkan password baru" required>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Minimal 6 karakter</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                        placeholder="Ulangi password baru" required>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-shield-check me-2"></i>Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Informasi Akun</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong>Kode User:</strong>
                                    <p class="mb-0">{{ $user->user_code }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Level Akses:</strong>
                                    <p class="mb-0">
                                        @if($user->id_level == 1)
                                        <span class="badge bg-danger">Administrator</span>
                                        @elseif($user->id_level == 2)
                                        <span class="badge bg-warning">Waiter</span>
                                        @elseif($user->id_level == 3)
                                        <span class="badge bg-success">Kasir</span>
                                        @else
                                        <span class="badge bg-secondary">Owner</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <strong>Terdaftar Sejak:</strong>
                                    <p class="mb-0">{{ $user->created_at ? $user->created_at->format('d F Y H:i') : '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="tab-pane fade" id="preferences">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Pengaturan Tema</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('pengaturan.update.preferences') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Pilih Tema Tampilan</label>
                                    <p class="text-muted small">Sesuaikan tampilan dashboard sesuai preferensi Anda</p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <input type="radio" class="btn-check" name="theme" id="theme-light" value="light"
                                            {{ ($user->theme ?? 'light') == 'light' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary w-100 p-4 theme-option" for="theme-light">
                                            <div class="text-center">
                                                <i class="bi bi-sun fs-1 mb-3 d-block"></i>
                                                <h5 class="mb-2">Mode Terang</h5>
                                                <p class="text-muted small mb-0">Tampilan terang untuk penggunaan di siang hari</p>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="col-md-6">
                                        <input type="radio" class="btn-check" name="theme" id="theme-dark" value="dark"
                                            {{ ($user->theme ?? 'light') == 'dark' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary w-100 p-4 theme-option" for="theme-dark">
                                            <div class="text-center">
                                                <i class="bi bi-moon-stars fs-1 mb-3 d-block"></i>
                                                <h5 class="mb-2">Mode Gelap</h5>
                                                <p class="text-muted small mb-0">Tampilan gelap untuk mengurangi kelelahan mata</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Simpan Tema
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>

<style>
    .list-group-item {
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .list-group-item.active {
        background-color: #f8f9fa;
        border-left-color: #0d6efd;
        color: #0d6efd;
        font-weight: 500;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }

    .card {
        border: none;
        border-radius: 10px;
    }

    .card-header {
        border-bottom: 1px solid #e9ecef;
        border-radius: 10px 10px 0 0 !important;
    }

    .theme-option {
        height: 100%;
        transition: all 0.3s ease;
        border: 2px solid #dee2e6;
    }

    .theme-option:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-check:checked+.theme-option {
        background-color: #0d6efd;
        color: white !important;
        border-color: #0d6efd;
    }

    .btn-check:checked+.theme-option .text-muted {
        color: rgba(255, 255, 255, 0.8) !important;
    }
</style>

<script>
    // Preview avatar before upload
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection