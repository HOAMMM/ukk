@extends('dashboard.template')

@section('content')
<style>
    .transaction-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }

    .info-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 1.5rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #6c757d;
        font-weight: 500;
    }

    .info-value {
        color: #212529;
        font-weight: 600;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-success {
        background: #d4edda;
        color: #155724;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .item-table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .summary-card {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 10px;
        padding: 1.5rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        font-size: 1rem;
    }

    .summary-row.total {
        border-top: 2px solid #667eea;
        margin-top: 1rem;
        padding-top: 1rem;
        font-size: 1.25rem;
        font-weight: 700;
        color: #667eea;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
        margin-top: 2rem;
    }

    .btn-print {
        background: #667eea;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s;
    }

    .btn-print:hover {
        background: #5568d3;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .transaction-header,
        .info-card,
        .item-table {
            box-shadow: none !important;
            page-break-inside: avoid;
        }
    }
</style>

<div class="container-fluid">

    {{-- BACK BUTTON --}}
    <div class="mb-3 no-print">
        <a href="{{ route('dashboard.transaksi') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    {{-- TRANSACTION HEADER --}}
    <div class="transaction-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="mb-2 fw-bold">{{ $transaksi->transaksi_code }}</h2>
                <p class="mb-0 opacity-90">
                    <i class="far fa-calendar me-2"></i>
                    {{ date('d F Y, H:i', strtotime($transaksi->created_at)) }}
                </p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                @if($transaksi->transaksi_status == 'success')
                <span class="status-badge status-success">
                    <i class="fas fa-check-circle"></i> SUCCESS
                </span>
                @else
                <span class="status-badge status-pending">
                    <i class="fas fa-clock"></i> PENDING
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        {{-- LEFT COLUMN - CUSTOMER & TABLE INFO --}}
        <div class="col-md-4">
            <div class="info-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-user-circle text-primary me-2"></i>
                    Informasi Pelanggan
                </h5>

                <div class="info-row">
                    <span class="info-label">Nama Pelanggan</span>
                    <span class="info-value">{{ $transaksi->transaksi_csname ?? 'Umum' }}</span>
                </div>

                @if($transaksi->order && $transaksi->order->meja)
                <div class="info-row">
                    <span class="info-label">Nomor Meja</span>
                    <span class="info-value">
                        <i class="fas fa-chair text-primary me-1"></i>
                        Meja {{ $transaksi->order->meja->meja_no }}
                    </span>
                </div>
                @endif

                <!-- <div class="info-row">
                    <span class="info-label">Kasir</span>
                    <span class="info-value">{{ $transaksi->user->name ?? '-' }}</span>
                </div> -->

                <div class="info-row">
                    <span class="info-label">Metode Pembayaran</span>
                    <span class="info-value text-uppercase">
                        @if(stripos($transaksi->transaksi_channel, 'cash') !== false)
                        <i class="fas fa-money-bill-wave text-success me-1"></i> TUNAI
                        @elseif(stripos($transaksi->transaksi_channel, 'qris') !== false)
                        <i class="fas fa-qrcode text-primary me-1"></i> QRIS
                        @elseif(stripos($transaksi->transaksi_channel, 'ewallet') !== false)
                        <i class="fas fa-wallet text-info me-1"></i> E-WALLET
                        @else
                        {{ $transaksi->transaksi_channel }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN - ITEMS & SUMMARY --}}
        <div class="col-md-8">
            {{-- ITEMS TABLE --}}
            <div class="item-table mb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Menu</th>
                            <th width="100" class="text-center">Qty</th>
                            <th width="150" class="text-end">Harga</th>
                            <th width="150" class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($transaksi->details as $item)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>
                                <div class="fw-semibold">{{ $item->menu->menu_nama ?? 'Item Terhapus' }}</div>
                                @if($item->trans_note)
                                <small class="text-muted">
                                    <i class="fas fa-sticky-note me-1"></i>
                                    {{ $item->trans_note }}
                                </small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $item->trans_qty }}</span>
                            </td>
                            <td class="text-end">
                                Rp {{ number_format($item->trans_price, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-semibold">
                                Rp {{ number_format($item->trans_subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                                <p class="mb-0">Tidak ada item</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAYMENT SUMMARY --}}
            <div class="summary-card">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-calculator text-primary me-2"></i>
                    Ringkasan Pembayaran
                </h5>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span class="fw-semibold">Rp {{ number_format($transaksi->transaksi_total, 0, ',', '.') }}</span>
                </div>

                @if($transaksi->transaksi_discount > 0)
                <div class="summary-row">
                    <span>Diskon</span>
                    <span class="text-danger fw-semibold">
                        - Rp {{ number_format($transaksi->transaksi_discount, 0, ',', '.') }}
                    </span>
                </div>
                @endif

                @if($transaksi->transaksi_tax > 0)
                <div class="summary-row">
                    <span>Pajak ({{ $transaksi->transaksi_tax_percent ?? 10 }}%)</span>
                    <span class="fw-semibold">Rp {{ number_format($transaksi->transaksi_tax, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="summary-row total">
                    <span>TOTAL BAYAR</span>
                    <span>Rp {{ number_format($transaksi->transaksi_total, 0, ',', '.') }}</span>
                </div>

                <div class="summary-row mt-2">
                    <span>Dibayar</span>
                    <span class="fw-semibold">Rp {{ number_format($transaksi->transaksi_amount, 0, ',', '.') }}</span>
                </div>

                <div class="summary-row">
                    <span>Kembalian</span>
                    <span class="fw-semibold text-success">
                        Rp {{ number_format($transaksi->transaksi_change, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="action-buttons no-print">
        <!-- <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i>
            Cetak Struk
        </button> -->

        <button onclick="window.location.href='{{ route('dashboard.transaksi') }}'" class="btn btn-outline-secondary">
            <i class="fas fa-list me-2"></i>
            Lihat Semua Transaksi
        </button>

        @if($transaksi->transaksi_status == 'pending')
        <button onclick="updateStatus('{{ $transaksi->transaksi_id }}', 'success')" class="btn btn-success">
            <i class="fas fa-check me-2"></i>
            Tandai Sukses
        </button>
        @endif

        <button onclick="hapusTransaksi({{ $transaksi->transaksi_id }})" class="btn btn-danger">
            <i class="fas fa-trash me-2"></i>
            Hapus
        </button>
    </div>

</div>

{{-- FORM DELETE --}}
<form id="deleteForm" method="POST">
    @csrf
    @method('DELETE')
</form>

{{-- FORM UPDATE STATUS --}}
<form id="updateStatusForm" method="POST">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="statusInput">
</form>

@endsection

@push('scripts')
<script>
    /* =============================
       UPDATE STATUS
    ============================= */
    function updateStatus(id, status) {
        Swal.fire({
            title: 'Update status transaksi?',
            text: `Ubah status menjadi ${status.toUpperCase()}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, update',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('updateStatusForm');
                document.getElementById('statusInput').value = status;
                form.action = "{{ route('dashboard.transaksi.update.status', '__id__') }}".replace('__id__', id);
                form.submit();
            }
        });
    }

    /* =============================
       DELETE TRANSAKSI
    ============================= */
    function hapusTransaksi(id) {
        Swal.fire({
            title: 'Hapus transaksi?',
            text: 'Data transaksi akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = "{{ route('dashboard.transaksi.destroy', '__id__') }}".replace('__id__', id);
                form.submit();
            }
        });
    }

    /* =============================
       PRINT STYLING
    ============================= */
    window.onbeforeprint = function() {
        document.title = "Transaksi {{ $transaksi->transaksi_code }}";
    };

    window.onafterprint = function() {
        document.title = "Detail Transaksi - Restaurant HQ";
    };
</script>

{{-- SWEETALERT RESPONSE --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('
        success ') }}'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '{{ session('
        error ') }}'
    });
</script>
@endif
@endpush