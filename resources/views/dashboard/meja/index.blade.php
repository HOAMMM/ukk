    @extends('dashboard.template')

    @section('content')
    <style>
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
        }

        .form-card {
            background: white;
            padding: 1.8rem;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
            border: 1px solid #e2e8f0;
            transition: var(--transition);
        }

        .form-card:hover {
            box-shadow: var(--shadow-md);
        }

        .meja-card {
            position: relative;
            transition: all 0.3s ease;
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            height: 100%;
            background: white;
        }

        .meja-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .meja-card .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .meja-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            margin: 0.5rem auto 1rem;
            position: relative;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .meja-kosong .meja-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
        }

        .meja-terisi .meja-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
        }

        .meja-kosong .meja-icon::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.3);
            animation: pulse-green 2s infinite;
        }

        .meja-terisi .meja-icon::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.3);
            animation: pulse-red 2s infinite;
        }

        @keyframes pulse-green {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.5;
            }
        }

        @keyframes pulse-red {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.5;
            }
        }

        .meja-card h4 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0.5rem 0;
            color: #1e293b;
        }

        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-add {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(234, 88, 12, 0.4);
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        }

        .stats-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid;
            transition: var(--transition);
        }

        .stats-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-4px);
        }

        .stats-card.success {
            border-left-color: #10b981;
        }

        .stats-card.danger {
            border-left-color: #ef4444;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }

        .stats-label {
            color: #64748b;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.3rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            opacity: 0.4;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(234, 88, 12, 0.15);
        }

        .btn-delete-meja {
            position: absolute;
            top: 1px;
            right: 1px;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            border: 2px solid white;
            transition: all 0.2s ease;
        }

        .btn-delete-meja:hover {
            transform: scale(1.15);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .btn-selesai-meja {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 20px;
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
        }

        .btn-selesai-meja:hover {
            transform: translateX(-50%) translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .toggle-status-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .form-check-input.toggle-meja {
            width: 48px;
            height: 24px;
            cursor: pointer;
        }

        .form-check-input.toggle-meja:checked {
            background-color: #ef4444;
            border-color: #ef4444;
        }

        @media (max-width: 767.98px) {
            .meja-icon {
                width: 70px;
                height: 70px;
                font-size: 1.75rem;
            }

            .page-header {
                padding: 1.5rem;
            }

            .form-card {
                padding: 1.2rem;
            }

            .meja-card h4 {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 575.98px) {
            .meja-card .card-body {
                padding: 1rem;
            }

            .meja-icon {
                width: 60px;
                height: 60px;
                font-size: 1.4rem;
            }

            .meja-card h4 {
                font-size: 1rem;
            }

            .status-badge {
                font-size: 0.65rem;
                padding: 0.3rem 0.6rem;
            }
        }
    </style>

    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header">
            <div class="page-header-content d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h2>
                        <i class="fas fa-chair"></i>
                        Manajemen Meja
                    </h2>
                    <p>Kelola dan pantau status meja restoran secara real-time</p>
                    @if($mejas->count() > 0)
                    <div class="header-badge">
                        <i class="fas fa-check-circle me-2"></i>{{ $mejas->count() }} Meja Terdaftar
                    </div>
                    @endif
                </div>
                @if($mejas->where('meja_status', 'terisi')->count() > 0)
                <button class="btn btn-reset-all btn-secondary" onclick="resetAllTables()">
                    <i class="fas fa-redo me-2"></i>Reset Semua Meja
                </button>
                @endif
            </div>
        </div>

        <!-- Form Tambah Meja -->
        <div class="form-card">
            <h5 class="mb-3 fw-bold">
                <i class="fas fa-plus-circle me-2 text-primary"></i>
                Tambah Meja Baru
            </h5>
            <form method="POST" id="formTambahMeja">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold mb-2">Nama Meja</label>
                        <input
                            type="text"
                            name="meja_nama"
                            id="meja_nama"
                            class="form-control form-control-lg"
                            placeholder="Contoh: M1, VIP-1"
                            autocomplete="off"
                            required>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Maksimal 10 karakter (huruf, angka, -)
                        </small>
                    </div>

                    <!-- INPUT KAPASITAS -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold mb-2">Kapasitas</label>
                        <input
                            type="number"
                            name="meja_kapasitas"
                            id="meja_kapasitas"
                            class="form-control form-control-lg"
                            placeholder="Jumlah orang"
                            min="1"
                            max="20"
                            required>
                        <small class="text-muted">
                            <i class="fas fa-users me-1"></i>
                            Jumlah orang
                        </small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold mb-2 d-none d-md-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-add btn-lg w-100">
                            <i class="fas fa-plus-circle me-2"></i> Tambah Meja
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <!-- Statistik -->
        @if($mejas->count() > 0)
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="stats-card success">
                    <div class="stats-label">
                        <i class="fas fa-check-circle me-2"></i>
                        Meja Tersedia
                    </div>
                    <h2 class="stats-number text-success mb-0">
                        {{ $mejas->where('meja_status', 'kosong')->count() }}
                    </h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stats-card danger">
                    <div class="stats-label">
                        <i class="fas fa-users me-2"></i>
                        Meja Terisi
                    </div>
                    <h2 class="stats-number text-danger mb-0">
                        {{ $mejas->where('meja_status', 'terisi')->count() }}
                    </h2>
                </div>
            </div>
        </div>
        @endif

        <!-- Grid Meja -->
        <div class="row g-4">
            @forelse($mejas as $meja)
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="card meja-card {{ $meja->meja_status == 'kosong' ? 'meja-kosong' : 'meja-terisi' }}">

                    <!-- Tombol Hapus -->
                    <button
                        class="btn btn-sm btn-danger btn-delete-meja"
                        data-id="{{ $meja->meja_id }}"
                        data-nama="{{ $meja->meja_nama }}"
                        title="Hapus Meja">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="card-body">
                        <!-- Toggle + Status Badge -->
                        <div class="toggle-status-wrapper">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input toggle-meja"
                                    type="checkbox"
                                    data-id="{{ $meja->meja_id }}"
                                    {{ $meja->meja_status == 'terisi' ? 'checked' : '' }}>
                            </div>
                            <span class="badge status-badge {{ $meja->meja_status == 'terisi' ? 'bg-danger' : 'bg-success' }}">
                                {{ $meja->meja_status == 'terisi' ? 'TERISI' : 'TERSEDIA' }}
                            </span>
                        </div>

                        <!-- Icon Meja -->
                        <div class="meja-icon">
                            <i class="fas {{ $meja->meja_status == 'kosong' ? 'fa-check' : 'fa-user-friends' }}"></i>
                        </div>

                        <!-- Nama Meja -->
                        <h4>{{ $meja->meja_nama }}</h4>
                        <span class="badge bg-secondary mt-1">
                            <i class="fas fa-user me-1"></i>
                            {{ $meja->meja_kapasitas }} Orang
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="empty-state">
                            <i class="fas fa-chair"></i>
                            <h5 class="mt-3 mb-2">Belum Ada Meja</h5>
                            <p class="text-muted mb-0">Silakan tambahkan meja baru menggunakan form di atas</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        function resetAllTables() {
            Swal.fire({
                title: 'Reset Semua Meja?',
                html: 'Semua meja akan diubah menjadi status <strong>KOSONG</strong>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#64748b',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: '<i class="fas fa-redo me-2"></i>Ya, Reset Semua',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang mereset semua meja',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('/dashboard/meja/reset-all', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        })
                        .catch(err => {
                            Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
                            console.error(err);
                        });
                }
            });
        }
        // Hapus Meja
        document.querySelectorAll('.btn-delete-meja').forEach(btn => {
            btn.addEventListener('click', function() {
                const mejaId = this.dataset.id;
                const mejaNama = this.dataset.nama;

                Swal.fire({
                    title: 'Hapus Meja?',
                    html: `Meja <strong>${mejaNama}</strong> akan dihapus permanen`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/dashboard/meja/${mejaId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(res => {
                                if (res.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: res.message,
                                        timer: 1200,
                                        showConfirmButton: false
                                    }).then(() => location.reload());
                                } else {
                                    Swal.fire('Gagal', res.message, 'error');
                                }
                            });
                    }
                });
            });
        });

        // Toggle Status Meja
        document.querySelectorAll('.toggle-meja').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const id = this.dataset.id;

                fetch(`/dashboard/meja/${id}/toggle`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(() => {
                        location.reload();
                    });
            });
        });

        // Form Tambah Meja
        document.getElementById('formTambahMeja').addEventListener('submit', function(e) {
            e.preventDefault();

            const mejaNama = document.getElementById('meja_nama').value.trim();
            const mejaKapasitas = document.getElementById('meja_kapasitas').value;


            if (mejaNama === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Nama meja tidak boleh kosong!',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }
            if (mejaKapasitas === '' || mejaKapasitas < 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kapasitas Tidak Valid',
                    text: 'Jumlah tempat duduk minimal 1 orang!',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }


            if (mejaNama.length > 10) {
                Swal.fire({
                    icon: 'error',
                    title: 'Nama Terlalu Panjang',
                    text: 'Nama meja maksimal 10 karakter!',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }

            const pattern = /^[a-zA-Z0-9-]+$/;
            if (!pattern.test(mejaNama)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Tidak Valid',
                    text: 'Nama meja hanya boleh berisi huruf, angka, dan tanda hubung (-)!',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Tambah Meja',
                html: `Apakah Anda yakin ingin menambahkan meja <strong>"${mejaNama}"</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-check me-2"></i>Ya, Tambah!',
                cancelButtonText: '<i class="fas fa-times me-2"></i>Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang menambahkan meja baru',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    this.submit();
                }
            });
        });

        // Validasi real-time
        document.getElementById('meja_nama').addEventListener('input', function(e) {
            let value = e.target.value;
            value = value.replace(/[^a-zA-Z0-9-]/g, '');
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            e.target.value = value;
        });
        document.getElementById('meja_kapasitas').addEventListener('input', function(e) {
            if (e.target.value > 20) {
                e.target.value = 20;
            }
        });


        // Auto focus
        document.addEventListener('DOMContentLoaded', function() {
            const inputMeja = document.getElementById('meja_nama');
            if (inputMeja) {
                inputMeja.focus();
            }
        });
    </script>
    @endpush
    @endsection