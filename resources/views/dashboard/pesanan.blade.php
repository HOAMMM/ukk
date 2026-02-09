@extends('dashboard.template')

@section('content')
<style>
    .invoice-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
    }

    .invoice-tabs button {
        border: none;
        background: transparent;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 14px;
        color: #6c757d;
        transition: .2s;
    }

    .invoice-tabs button.active {
        background: #0d6efd;
        color: #fff;
    }

    .search-input {
        max-width: 220px;
        border-radius: 20px;
        font-size: 14px;
    }

    .btn-success-soft {
        background: #e7f6ee;
        color: #198754;
        border-radius: 20px;
        border: none;
    }

    .btn-warning-soft {
        background: #fff3cd;
        color: #b58105;
        border-radius: 20px;
        border: none;
    }

    .invoice-table thead th {
        font-size: 13px;
        text-transform: uppercase;
        color: #6c757d;
        border-bottom: none;
    }

    .invoice-table tbody tr:hover {
        background: #f8f9fa;
    }

    .badge-paid {
        background: #e6f4ea;
        color: #1e7e34;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
    }

    .badge-unpaid {
        background: #fdecea;
        color: #dc3545;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
    }

    .badge-pending {
        background: #fff3cd;
        color: #856404;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
    }

    .action-icons i {
        margin-left: 10px;
        cursor: pointer;
        color: #6c757d;
        transition: .2s;
    }

    .action-icons i:hover {
        color: #0d6efd;
    }

    /* Modal Styling */
    .modal-header {
        background: #9e4400;
        color: white;
        border-radius: 0.5rem 0.5rem 0 0;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    .info-row {
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 14px;
    }

    .info-value {
        color: #212529;
        font-weight: 500;
    }

    .detail-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }

    .detail-section h6 {
        color: #495057;
        margin-bottom: 12px;
        font-weight: 600;
    }

    .total-section {
        background: linear-gradient(135deg, #9e4400 0%, #fb923c 100%);
        color: white;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }

    .total-label {
        font-size: 14px;
        opacity: 0.9;
    }

    .total-value {
        font-size: 20px;
        font-weight: 700;
    }
</style>

<div class="container mt-4">

    <div class="card invoice-card">
        <div class="card-body">

            {{-- FILTER TAB --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="invoice-tabs">
                    <button class="filter-tab active" data-status="all">
                        All <span>{{ $orders->count() }}</span>
                    </button>
                    <button class="filter-tab" data-status="pending">
                        Pending <span>{{ $orders->where('order_status','pending')->count() }}</span>
                    </button>
                    <button class="filter-tab" data-status="paid">
                        Paid <span>{{ $orders->where('order_status','paid')->count() }}</span>
                    </button>
                    <button class="filter-tab" data-status="archived">
                        Archived <span>{{ $orders->where('order_status','archived')->count() }}</span>
                    </button>
                </div>

                <input type="text" id="searchInput" class="form-control search-input" placeholder="Search order...">
            </div>

            {{-- ACTION --}}
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <button class="btn btn-success-soft" id="markAsPaid">✔ Mark as paid</button>
                <button class="btn btn-warning-soft" id="markAsPending">⏱ Mark as pending</button>
                <button class="btn btn-light">🖨 Print</button>
                <button class="btn btn-light text-danger" id="deleteSelected">🗑 Delete</button>
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table invoice-table align-middle">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>No</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="orderTableBody">

                        @forelse ($orders as $index => $order)
                        <tr data-status="{{ $order->order_status }}"
                            data-customer="{{ strtolower($order->order_csname) }}"
                            data-order-id="{{ $order->order_id }}">
                            <td>
                                <input type="checkbox" class="order-checkbox" value="{{ $order->order_id }}">
                            </td>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $order->order_csname }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>
                                @if ($order->order_status == 'paid')
                                <span class="badge badge-paid">Paid</span>
                                @elseif ($order->order_status == 'pending')
                                <span class="badge badge-pending">Pending</span>
                                @else
                                <span class="badge bg-secondary">{{ ucfirst($order->order_status) }}</span>
                                @endif
                            </td>
                            <td>
                                Rp {{ number_format($order->order_total,0,',','.') }}
                            </td>
                            <td class="text-end action-icons">
                                <i class="bi bi-eye btn-detail"
                                    data-bs-toggle="modal"
                                    data-bs-target="#detailModal"
                                    data-id="{{ $order->order_id }}"></i>
                                <i class="bi bi-pencil"></i>
                                <i class="bi bi-trash text-danger" onclick="deleteOrder({{ $order->order_id }})"></i>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="7" class="text-center text-muted">
                                Tidak ada data pesanan
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- MODAL DETAIL ORDER --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="bi bi-receipt me-2"></i>Detail Transaksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterTabs = document.querySelectorAll('.filter-tab');
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('orderTableBody');
        const selectAllCheckbox = document.getElementById('selectAll');

        let currentFilter = 'all';
        let currentSearch = '';

        // Filter by status
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.getAttribute('data-status');
                filterTable();
            });
        });

        // Search functionality
        searchInput.addEventListener('input', function() {
            currentSearch = this.value.toLowerCase();
            filterTable();
        });

        // Filter table function
        function filterTable() {
            const rows = tableBody.querySelectorAll('tr:not(#emptyRow)');
            let visibleCount = 0;

            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                const customer = row.getAttribute('data-customer');
                const orderId = row.getAttribute('data-order-id');

                let matchesFilter = currentFilter === 'all' || status === currentFilter;
                let matchesSearch = currentSearch === '' ||
                    customer.includes(currentSearch) ||
                    orderId.includes(currentSearch);

                if (matchesFilter && matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide empty message
            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) {
                emptyRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        }

        // Select all checkbox
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.order-checkbox');
            checkboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                if (row.style.display !== 'none') {
                    checkbox.checked = this.checked;
                }
            });
        });

        // Mark as paid
        document.getElementById('markAsPaid').addEventListener('click', function() {
            const selectedIds = getSelectedIds();

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih minimal satu pesanan terlebih dahulu',
                    confirmButtonColor: '#0d6efd',
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Tandai ${selectedIds.length} pesanan sebagai Paid?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tandai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/dashboard/pesanan/mark-paid', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ids: selectedIds
                            })
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        });
                }
            });
        });

        // Mark as pending
        document.getElementById('markAsPending').addEventListener('click', function() {
            const selectedIds = getSelectedIds();

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih minimal satu pesanan terlebih dahulu',
                    confirmButtonColor: '#0d6efd',
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Tandai ${selectedIds.length} pesanan sebagai Pending?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#b58105',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tandai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/dashboard/pesanan/mark-pending', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ids: selectedIds
                            })
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        });
                }
            });
        });

        // Delete selected (bulk)
        document.getElementById('deleteSelected').addEventListener('click', function() {
            const selectedIds = getSelectedIds();

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih minimal satu pesanan terlebih dahulu',
                    confirmButtonColor: '#0d6efd',
                });
                return;
            }

            Swal.fire({
                title: 'Hapus Pesanan?',
                html: `Anda akan menghapus <strong>${selectedIds.length} pesanan</strong>.<br>Tindakan ini tidak dapat dibatalkan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/dashboard/pesanan/bulk-delete', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ids: selectedIds
                            })
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        });
                }
            });
        });

        // Helper function to get selected IDs
        function getSelectedIds() {
            const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
            return Array.from(selectedCheckboxes).map(cb => cb.value);
        }

        // Detail modal
        document.querySelectorAll('.btn-detail').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                loadOrderDetail(orderId);
            });
        });

        function loadOrderDetail(orderId) {
            const modalContent = document.getElementById('modalContent');

            // Show loading
            modalContent.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            // Fetch detail
            fetch(`/dashboard/pesanan/${orderId}/detail`)
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        modalContent.innerHTML = generateDetailHTML(data.order);
                    } else {
                        modalContent.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                ${data.message}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    modalContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Terjadi kesalahan saat memuat data
                        </div>
                    `;
                });
        }

        function generateDetailHTML(order) {
            const statusBadge = order.order_status === 'paid' ?
                '<span class="badge badge-paid">Paid</span>' :
                order.order_status === 'pending' ?
                '<span class="badge badge-pending">Pending</span>' :
                '<span class="badge bg-secondary">' + order.order_status + '</span>';

            let detailItems = '';
            if (order.details && order.details.length > 0) {
                order.details.forEach(item => {
                    detailItems += `
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <strong>${item.trans_name}</strong>
                                <br>
                                <small class="text-muted">${item.trans_qty} x Rp ${formatRupiah(item.trans_price)}</small>
                            </div>
                            <div class="text-end">
                                <strong>Rp ${formatRupiah(item.trans_subtotal)}</strong>
                            </div>
                        </div>
                    `;
                });
            }

            return `
                <div class="info-row">
                    <div class="row">
                        <div class="col-5 info-label">Nama Pelanggan</div>
                        <div class="col-7 info-value">${order.order_csname}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="row">
                        <div class="col-5 info-label">No. Meja</div>
                        <div class="col-7 info-value">${order.meja ? order.meja.meja_nama : '-'}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="row">
                        <div class="col-5 info-label">Tanggal</div>
                        <div class="col-7 info-value">${formatDate(order.created_at)}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="row">
                        <div class="col-5 info-label">Status</div>
                        <div class="col-7 info-value">${statusBadge}</div>
                    </div>
                </div>

                ${detailItems ? `
                <div class="detail-section">
                    <h6><i class="bi bi-cart3 me-2"></i>Detail Pesanan</h6>
                    ${detailItems}
                </div>
                ` : ''}

                <div class="total-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="total-label">Total Pembayaran</div>
                        <div class="total-value">Rp ${formatRupiah(order.order_total)}</div>
                    </div>
                    ${order.order_change > 0 ? `
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="total-label">Kembalian</div>
                        <div class="total-value">Rp ${formatRupiah(order.order_change)}</div>
                    </div>
                    ` : ''}
                </div>
            `;
        }

        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return date.toLocaleDateString('id-ID', options);
        }
    });

    // Delete single order
    function deleteOrder(orderId) {
        Swal.fire({
            title: 'Hapus Pesanan?',
            text: 'Tindakan ini tidak dapat dibatalkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/dashboard/pesanan/${orderId}/hapus`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: res.message || 'Terjadi kesalahan'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus pesanan'
                        });
                        console.error('Error:', error);
                    });
            }
        });
    }
</script>
@endsection