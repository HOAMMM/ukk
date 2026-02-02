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
                    <a href="#account" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-shield-lock me-2"></i>Keamanan Akun
                    </a>
                    <a href="#notification" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-bell me-2"></i>Notifikasi
                    </a>
                    <a href="#preferences" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-sliders me-2"></i>Preferensi
                    </a>
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
                            <!-- Profile Photo -->
                            <div class="mb-4 text-center">
                                <div class="position-relative d-inline-block">
                                    <img src="https://ui-avatars.com/api/?name=User&size=120"
                                        alt="Avatar"
                                        class="rounded-circle mb-3"
                                        width="120"
                                        height="120"
                                        id="avatar-preview">
                                    <label for="avatar" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle" style="width: 35px; height: 35px;">
                                        <i class="bi bi-camera"></i>
                                    </label>
                                    <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="birth_date" class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Masukkan alamat lengkap"></textarea>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Security -->
                <div class="tab-pane fade" id="account">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Ubah Password</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                                <input type="password" name="current_password" class="form-control" placeholder="Masukkan password lama" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="Masukkan password baru" required>
                                <small class="text-muted">Minimal 8 karakter</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" required>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-primary">
                                    <i class="bi bi-shield-check me-2"></i>Update Password
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Two Factor Authentication -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Autentikasi Dua Faktor</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Aktifkan 2FA</h6>
                                    <p class="text-muted small mb-0">Tambahkan lapisan keamanan ekstra pada akun Anda</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="toggle2fa">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="tab-pane fade" id="notification">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Pengaturan Notifikasi</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <h6 class="mb-1">Email Notifikasi</h6>
                                    <p class="text-muted small mb-0">Terima notifikasi melalui email</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotif" checked>
                                </div>
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <h6 class="mb-1">Push Notifikasi</h6>
                                    <p class="text-muted small mb-0">Terima notifikasi push di browser</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="push_notifications" id="pushNotif" checked>
                                </div>
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <h6 class="mb-1">Notifikasi SMS</h6>
                                    <p class="text-muted small mb-0">Terima notifikasi melalui SMS</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="sms_notifications" id="smsNotif">
                                </div>
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <h6 class="mb-1">Newsletter</h6>
                                    <p class="text-muted small mb-0">Terima berita dan update terbaru</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="newsletter" id="newsletter" checked>
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>Simpan Pengaturan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preferences -->
                <div class="tab-pane fade" id="preferences">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Preferensi Umum</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label">Bahasa</label>
                                <select name="language" class="form-select">
                                    <option value="id" selected>Bahasa Indonesia</option>
                                    <option value="en">English</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Zona Waktu</label>
                                <select name="timezone" class="form-select">
                                    <option value="Asia/Jakarta" selected>WIB - Jakarta</option>
                                    <option value="Asia/Makassar">WITA - Makassar</option>
                                    <option value="Asia/Jayapura">WIT - Jayapura</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Tema</label>
                                <select name="theme" class="form-select">
                                    <option value="light" selected>Terang</option>
                                    <option value="dark">Gelap</option>
                                    <option value="auto">Otomatis</option>
                                </select>
                            </div>

                            <div class="mb-4 d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <h6 class="mb-1">Mode Kompak</h6>
                                    <p class="text-muted small mb-0">Tampilkan lebih banyak konten di layar</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="compact_mode">
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>Simpan Preferensi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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

    // 2FA Toggle
    document.getElementById('toggle2fa')?.addEventListener('change', function() {
        if (this.checked) {
            alert('Fitur 2FA akan segera diaktifkan');
        } else {
            if (confirm('Apakah Anda yakin ingin menonaktifkan 2FA?')) {
                // Process disable 2FA
            } else {
                this.checked = true;
            }
        }
    });
</script>
@endsection