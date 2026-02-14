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

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(234, 88, 12, 0.15);
    }

    /* Table Styling */
    .table-wrapper {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .table-header {
        background: #f8fafc;
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-responsive {
        border-radius: 0 0 16px 16px;
    }

    .data-table {
        width: 100%;
        margin-bottom: 0;
        background: white;
    }

    .data-table thead {
        background: #5a6c7d;
        color: white;
    }

    .data-table thead th {
        padding: 1rem 1.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: none;
        border: none;
        vertical-align: middle;
    }

    .data-table thead th:first-child {
        padding-left: 2rem;
    }

    .data-table tbody tr {
        border-bottom: 1px solid #e2e8f0;
        transition: background-color 0.2s ease;
    }

    .data-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .data-table tbody tr:last-child {
        border-bottom: none;
    }

    .data-table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        font-size: 0.95rem;
        color: #1e293b;
    }

    .data-table tbody td:first-child {
        padding-left: 2rem;
        font-weight: 600;
        color: #64748b;
    }

    .status-badge {
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: inline-block;
    }

    .status-badge.bg-success {
        background-color: #10b981 !important;
        color: white;
    }

    .status-badge.bg-danger {
        background-color: #ef4444 !important;
        color: white;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 24px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }

    input:checked+.toggle-slider {
        background-color: #ef4444;
    }

    input:checked+.toggle-slider:before {
        transform: translateX(24px);
    }

    .btn-action {
        width: 36px;
        height: 36px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s ease;
        border: none;
        margin: 0 0.25rem;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-action.btn-edit {
        background-color: #3b82f6;
        color: white;
    }

    .btn-action.btn-delete {
        background-color: #ef4444;
        color: white;
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

    .capacity-badge {
        background-color: #64748b;
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }

    /* Pagination Styling */
    .pagination-wrapper {
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .pagination {
        margin: 0;
        gap: 0.25rem;
    }

    .pagination .page-item .page-link {
        border: 1px solid #e2e8f0;
        color: #64748b;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .pagination .page-item .page-link:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #334155;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-color: var(--primary);
        color: white;
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.3);
    }

    .pagination .page-item.disabled .page-link {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #cbd5e1;
    }

    /* Search & Filter Styling */
    .input-group .form-control:focus {
        border-color: var(--primary);
        box-shadow: none;
    }

    .input-group-text {
        border-right: 0;
    }

    .input-group .form-control.border-start-0 {
        border-left: 0;
    }

    .input-group .form-control.border-start-0:focus {
        border-left: 0;
    }

    .input-group:focus-within .input-group-text {
        border-color: var(--primary);
    }

    .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(234, 88, 12, 0.15);
    }

    .input-group .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border: none;
    }

    .input-group .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
    }

    .input-group .btn-secondary {
        background-color: #64748b;
        border: none;
    }

    .input-group .btn-secondary:hover {
        background-color: #475569;
    }

    /* Loading State */
    .table-loading {
        position: relative;
        opacity: 0.6;
        pointer-events: none;
    }

    .table-loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 40px;
        border: 4px solid #f3f4f6;
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }

    @media (max-width: 767.98px) {
        .page-header {
            padding: 1.5rem;
        }

        .form-card {
            padding: 1.2rem;
        }

        .data-table thead th,
        .data-table tbody td {
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
        }

        .data-table thead th:first-child,
        .data-table tbody td:first-child {
            padding-left: 1rem;
        }

        .btn-action {
            width: 32px;
            height: 32px;
        }

        .table-header {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .input-group {
            width: 100% !important;
        }

        .pagination-wrapper .d-flex {
            flex-direction: column;
            gap: 1rem;
        }

        .pagination {
            justify-content: center;
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
                @if(\App\Models\Meja::count() > 0)
                <div class="header-badge">
                    <i class="fas fa-check-circle me-2"></i>{{ \App\Models\Meja::count() }} Meja Terdaftar
                </div>
                @endif
            </div>
            @if(\App\Models\Meja::where('meja_status', 'terisi')->count() > 0)
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
    @if(\App\Models\Meja::count() > 0)
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stats-card success">
                <div class="stats-label">
                    <i class="fas fa-check-circle me-2"></i>
                    Meja Tersedia
                </div>
                <h2 class="stats-number text-success mb-0">
                    {{ \App\Models\Meja::where('meja_status', 'kosong')->count() }}
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
                    {{ \App\Models\Meja::where('meja_status', 'terisi')->count() }}
                </h2>
            </div>
        </div>
    </div>
    @endif

    <!-- Table Data Meja -->
    <div class="table-wrapper">
        <div class="table-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-table me-2 text-primary"></i>
                Data Meja
            </h5>

            <!-- Search & Filter -->
            <div class="input-group" style="width:280px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input
                    type="search"
                    id="searchMeja"
                    class="form-control form-control-sm border-start-0"
                    placeholder="Cari meja..."
                    autocomplete="off">
            </div>
        </div>

        <div id="mejaTableWrapper">
            @if($mejas->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Meja</th>
                        <th>Status</th>
                        <th>Kapasitas</th>
                        <th>Toggle Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="mejaTableBody">
                    @foreach($mejas as $index => $meja)
                    <tr class="meja-row">
                        <td>{{ ($mejas->currentPage()-1)*$mejas->perPage()+$index+1 }}</td>
                        <td><strong>{{ $meja->meja_nama }}</strong></td>
                        <td>
                            <span class="status-badge {{ $meja->meja_status == 'terisi' ? 'bg-danger' : 'bg-success' }}">
                                {{ strtoupper($meja->meja_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="capacity-badge">
                                {{ $meja->meja_kapasitas }} Orang
                            </span>
                        </td>
                        <td>
                            <label class="toggle-switch">
                                <input type="checkbox"
                                    class="toggle-meja"
                                    data-id="{{ $meja->meja_id }}"
                                    {{ $meja->meja_status == 'terisi' ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </td>
                        <td>
                            <button class="btn-action btn-delete"
                                data-id="{{ $meja->meja_id }}"
                                data-nama="{{ $meja->meja_nama }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination-wrapper">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3">
                    <div class="text-muted">
                        Menampilkan {{ $mejas->count() }} dari {{ $mejas->total() }} meja
                    </div>
                    <div>
                        {{ $mejas->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h5 class="mt-3 mb-2">Tidak Ada Data</h5>
                <p class="text-muted mb-0">
                    @if(request('search'))
                    Tidak ada meja yang sesuai dengan pencarian "<strong>{{ request('search') }}</strong>"
                    @else
                    Belum ada meja yang terdaftar
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    const searchInput = document.getElementById('searchMeja');
    const tableWrapper = document.getElementById('mejaTableWrapper');

    let controller;
    let debounceTimer;

    // Debounce function
    function debounce(func, delay = 300) {
        return function(...args) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Load Meja dengan AJAX
    function loadMeja(url) {
        if (controller) controller.abort();
        controller = new AbortController();

        tableWrapper.classList.add('table-loading');

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                signal: controller.signal
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(html => {
                tableWrapper.innerHTML = html;
                tableWrapper.classList.remove('table-loading');
                bindPagination();
                bindActions();
            })
            .catch(err => {
                if (err.name !== 'AbortError') {
                    console.error('Error:', err);
                    tableWrapper.classList.remove('table-loading');
                }
            });
    }

    // Live Search
    searchInput.addEventListener('input', debounce(function() {
        const searchValue = this.value.trim();
        const url = `{{ route('dashboard.meja.index') }}?search=${encodeURIComponent(searchValue)}`;
        loadMeja(url);
    }, 300));

    // Bind Pagination Links
    function bindPagination() {
        document.querySelectorAll('#mejaTableWrapper .pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.href;

                const searchValue = searchInput.value.trim();
                if (searchValue) {
                    const separator = url.includes('?') ? '&' : '?';
                    loadMeja(url + separator + `search=${encodeURIComponent(searchValue)}`);
                } else {
                    loadMeja(url);
                }
            });
        });
    }

    // Bind Actions (Toggle & Delete)
    function bindActions() {
        // Toggle Status
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
                        const searchValue = searchInput.value.trim();
                        const url = `{{ route('dashboard.meja.index') }}?search=${encodeURIComponent(searchValue)}`;
                        loadMeja(url);
                    });
            });
        });

        // Delete
        document.querySelectorAll('.btn-delete').forEach(btn => {
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
                                    }).then(() => {
                                        const searchValue = searchInput.value.trim();
                                        const url = `{{ route('dashboard.meja.index') }}?search=${encodeURIComponent(searchValue)}`;
                                        loadMeja(url);
                                    });
                                } else {
                                    Swal.fire('Gagal', res.message, 'error');
                                }
                            });
                    }
                });
            });
        });
    }

    // Initial bind
    bindPagination();
    bindActions();

    // Reset All Tables
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