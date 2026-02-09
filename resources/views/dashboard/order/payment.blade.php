@extends('dashboard.template')

@section('content')
<style>
    .payment-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .card-payment {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 24px;
        margin-bottom: 20px;
    }

    .order-header {
        background: linear-gradient(135deg, #9e4400 0%, #fb923c 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 24px;
    }

    .order-header h4 {
        margin: 0;
        font-weight: 600;
    }

    .order-info {
        display: flex;
        justify-content: space-between;
        margin-top: 12px;
        font-size: 14px;
    }

    .table-items {
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .table-items table {
        margin: 0;
    }

    .table-items thead {
        background: #e9ecef;
    }

    .table-items th {
        font-weight: 600;
        padding: 12px;
        border: none;
    }

    .table-items td {
        padding: 12px;
        border-top: 1px solid #dee2e6;
    }

    .total-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 24px;
    }

    .total-section h5 {
        margin: 0;
        font-size: 24px;
        color: #2d3748;
    }

    .total-amount {
        color: #9e4400;
        font-weight: 700;
    }

    .form-section {
        background: white;
    }

    .form-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        padding: 12px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #fb923c;
        box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.1);
    }

    /* ===============================
   FORCE ORANGE ORDER TYPE BUTTON
   =============================== */

    /* DEFAULT */
    .order-type-btn.btn-outline-primary,
    .order-type-btn.btn-outline-success {
        color: #1f1a16 !important;
        border-color: #050505 !important;
        background-color: transparent;
    }

    /* HOVER */
    .order-type-btn.btn-outline-primary:hover,
    .order-type-btn.btn-outline-success:hover {
        color: #ffffff !important;
        background-color: #fb923c !important;
        border-color: #fb923c !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(251, 146, 60, 0.35);
    }

    /* ACTIVE */
    .order-type-btn.btn-outline-primary.active,
    .order-type-btn.btn-outline-success.active {
        color: #ffffff !important;
        background-color: #fb923c !important;
        border-color: #fb923c !important;
    }

    /* FOCUS */
    .order-type-btn.btn-outline-primary:focus,
    .order-type-btn.btn-outline-success:focus {
        box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.35) !important;
        border-color: #fb923c !important;
    }

    /* REMOVE BOOTSTRAP ACTIVE OUTLINE */
    .order-type-btn.btn-outline-primary:active,
    .order-type-btn.btn-outline-success:active {
        background-color: #fb923c !important;
        border-color: #fb923c !important;
    }


    .meja-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .meja-section strong {
        display: block;
        margin-bottom: 12px;
        color: #2d3748;
    }

    .meja-btn {
        border-radius: 8px;
        padding: 16px 8px;
        font-weight: 600;
        transition: all 0.3s;
        border: 2px solid #e2e8f0;
    }

    .meja-btn.btn-outline-primary {
        background: white;
        color: #fb923c;
        border-color: #fb923c;
    }

    .meja-btn.btn-outline-primary:hover:not(.disabled) {
        background: #fb923c;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(251, 146, 60, 0.3);
    }

    .meja-btn.btn-outline-primary.active {
        background: #fb923c;
        color: white;
        border-color: #fb923c;
    }

    .meja-btn.btn-danger {
        background: #fee;
        color: #c53030;
        border-color: #feb2b2;
        cursor: not-allowed;
    }

    .kembalian-display {
        background: #f0fff4;
        border: 2px solid #9ae6b4;
        border-radius: 8px;
        padding: 16px;
        margin-top: 12px;
        display: none;
    }

    .kembalian-display.show {
        display: block;
    }

    .kembalian-display h6 {
        margin: 0 0 8px 0;
        color: #2f855a;
        font-size: 14px;
    }

    .kembalian-display .amount {
        font-size: 24px;
        font-weight: 700;
        color: #2f855a;
    }

    .btn-payment {
        background: linear-gradient(135deg, #9e4400 0%, #fb923c 100%);
        border: none;
        border-radius: 8px;
        padding: 16px;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s;
    }

    .btn-payment:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(251, 146, 60, 0.4);
    }

    .icon-check {
        display: inline-block;
        margin-right: 8px;
    }

    @media (max-width: 768px) {
        .card-payment {
            padding: 16px;
        }

        .order-header {
            padding: 16px;
        }

        .total-section h5 {
            font-size: 20px;
        }
    }
</style>

<div class="payment-container">
    <div class="order-header">
        <h4>Pembayaran Order</h4>
        <div class="order-info">
            <span><strong>Order ID:</strong> {{ $order->order_id }}</span>
            <span><strong>Kasir:</strong> {{ auth()->user()->name }}</span>
        </div>
    </div>

    {{-- LIST ITEM --}}
    <div class="card-payment">
        <h6 class="mb-3" style="color: #2d3748; font-weight: 600;">Detail Pesanan</h6>
        <div class="table-items">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th width="80" class="text-center">Qty</th>
                        <th width="120" class="text-end">Harga</th>
                        <th width="140" class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $d)
                    <tr>
                        <td>{{ $d->trans_name }}</td>
                        <td class="text-center">{{ $d->trans_qty }}</td>
                        <td class="text-end">Rp {{ number_format($d->trans_price, 0, ',', '.') }}</td>
                        <td class="text-end"><strong>Rp {{ number_format($d->trans_subtotal, 0, ',', '.') }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="total-section text-end">
            <h5>Total Pembayaran: <span class="total-amount">Rp {{ number_format($transaksi->transaksi_total, 0, ',', '.') }}</span></h5>
        </div>
    </div>

    {{-- FORM PEMBAYARAN --}}
    <div class="card-payment">
        <h6 class="mb-3" style="color: #2d3748; font-weight: 600;">Informasi Pembayaran</h6>

        <form id="formPayment">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
            <input type="hidden" name="meja_id" id="selected_meja" value="">
            <input type="hidden" id="total" value="{{ $transaksi->transaksi_total }}">

            {{-- NAMA PELANGGAN --}}
            <div class="mb-3">
                <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                <input type="text"
                    name="csname"
                    id="csname"
                    class="form-control"
                    placeholder="Masukkan nama pelanggan"
                    required
                    autocomplete="off">
            </div>

            {{-- TIPE ORDER --}}
            <div class="mb-3">
                <label class="form-label">Tipe Order <span class="text-danger">*</span></label>
                <div class="d-flex gap-2">
                    <button type="button"
                        class="btn btn-outline-primary flex-fill order-type-btn active"
                        data-type="dine_in">
                        Dine In
                    </button>
                    <button type="button"
                        class="btn btn-outline-success flex-fill order-type-btn"
                        data-type="takeaway">
                        Takeaway
                    </button>
                </div>
                <input type="hidden" name="order_type" id="order_type" value="dine_in">
            </div>

            {{-- PILIH MEJA --}}
            <div class="meja-section" id="mejaSection">
                <strong>Pilih Meja <span class="text-danger">*</span></strong>

                <div class="row g-2">
                    @foreach ($mejas as $meja)
                    @php
                    $isAvailable = $meja->meja_status === 'kosong';
                    @endphp

                    <div class="col-3 col-md-2">
                        <button
                            type="button"
                            class="btn w-100 meja-btn
                        {{ $isAvailable ? 'btn-outline-primary' : 'btn-danger disabled' }}"
                            data-id="{{ $meja->meja_id }}"
                            {{ $isAvailable ? '' : 'disabled' }}>

                            {{ $meja->meja_nama }}
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>


            {{-- UANG BAYAR --}}
            <div class="mb-3">
                <label class="form-label">Uang Bayar <span class="text-danger">*</span></label>
                <input type="number"
                    name="bayar"
                    id="bayar"
                    class="form-control"
                    placeholder="Masukkan jumlah uang bayar"
                    min="1"
                    required
                    autocomplete="off">
            </div>

            {{-- KEMBALIAN --}}
            <div class="kembalian-display" id="kembalianDisplay">
                <h6>Kembalian</h6>
                <div class="amount" id="kembalian">Rp 0</div>
            </div>

            <button type="submit" class="btn btn-success btn-payment w-100 mt-3">
                <span class="icon-check">✓</span> Proses Pembayaran
            </button>
        </form>
    </div>
</div>

<script>
    // ===============================
    // TIPE ORDER
    // ===============================
    const orderTypeButtons = document.querySelectorAll('.order-type-btn');
    const orderTypeInput = document.getElementById('order_type');
    const mejaSection = document.getElementById('mejaSection');
    const selectedMejaInput = document.getElementById('selected_meja');

    orderTypeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active from all buttons
            orderTypeButtons.forEach(b => b.classList.remove('active'));

            // Add active to clicked button
            this.classList.add('active');

            // Set hidden input value
            const orderType = this.dataset.type;
            orderTypeInput.value = orderType;

            // Show/hide meja section based on order type
            if (orderType === 'takeaway') {
                mejaSection.style.display = 'none';
                selectedMejaInput.value = ''; // Clear meja selection
                // Remove active from all meja buttons
                document.querySelectorAll('.meja-btn').forEach(b => b.classList.remove('active'));
            } else {
                mejaSection.style.display = 'block';
            }
        });
    });

    // ===============================
    // PILIH MEJA
    // ===============================
    document.querySelectorAll('.meja-btn:not(.disabled)').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.meja-btn')
                .forEach(b => b.classList.remove('active'));

            this.classList.add('active');
            selectedMejaInput.value = this.dataset.id;
        });
    });

    // ===============================
    // HITUNG KEMBALIAN
    // ===============================
    const bayarInput = document.getElementById('bayar');
    const total = parseInt(document.getElementById('total').value);
    const kembalianDisplay = document.getElementById('kembalianDisplay');
    const kembalianAmount = document.getElementById('kembalian');

    bayarInput.addEventListener('input', function() {
        const bayar = parseInt(this.value || 0);
        const kembali = bayar - total;

        if (bayar >= total) {
            kembalianDisplay.classList.add('show');
            kembalianAmount.textContent =
                'Rp ' + kembali.toLocaleString('id-ID');
        } else {
            kembalianDisplay.classList.remove('show');
        }
    });

    // ===============================
    // SUBMIT PEMBAYARAN
    // ===============================
    document.getElementById('formPayment')
        .addEventListener('submit', function(e) {

            e.preventDefault();

            const bayar = parseInt(bayarInput.value);
            const mejaId = selectedMejaInput.value;
            const csname = document.getElementById('csname').value.trim();
            const orderType = orderTypeInput.value;
            const orderId = document.querySelector('input[name="order_id"]').value;

            // VALIDASI NAMA PELANGGAN
            if (!csname) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validasi Gagal',
                    text: 'Nama pelanggan harus diisi'
                });
                return;
            }

            // VALIDASI MEJA (hanya untuk dine_in)
            if (orderType === 'dine_in' && !mejaId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validasi Gagal',
                    text: 'Silakan pilih meja terlebih dahulu'
                });
                return;
            }

            // VALIDASI BAYAR
            if (!bayar || bayar < total) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validasi Gagal',
                    text: 'Uang bayar kurang dari total pembayaran'
                });
                return;
            }

            // KONFIRMASI
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                html: `
                    <div class="text-start">
                        <p><strong>Nama:</strong> ${csname}</p>
                        <p><strong>Tipe:</strong> ${orderType === 'dine_in' ? 'Dine In' : 'Takeaway'}</p>
                        <p><strong>Total:</strong> Rp ${total.toLocaleString('id-ID')}</p>
                        <p><strong>Bayar:</strong> Rp ${bayar.toLocaleString('id-ID')}</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Proses',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch("{{ route('dashboard.order.payment.process') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            order_id: orderId,
                            bayar: bayar,
                            meja_id: mejaId || null,
                            csname: csname,
                            order_type: orderType
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status) {
                            const kembalian = bayar - total;

                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil',
                                html: `
                                    <div style="font-size: 16px; margin: 10px 0;">
                                        <strong>Kembalian:</strong><br>
                                        <span style="font-size: 24px; color: #2f855a;">
                                            Rp ${kembalian.toLocaleString('id-ID')}
                                        </span>
                                    </div>
                                `,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // 🎯 REDIRECT KE STRUK
                                window.location.href =
                                    `/dashboard/order/struk/${res.order_id}`;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Terjadi kesalahan saat memproses pembayaran'
                        });
                        console.error(err);
                    });
            });
        });
</script>

@endsection