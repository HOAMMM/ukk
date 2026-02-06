<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Restaurant HQ</title>
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link href="{{ asset('bootstrap/icon/bootstrap-icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, sans-serif;
            background: #f5f5f5;
            overflow-x: hidden;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #ff6b35, #ff8c61);
            color: white;
            padding: 12px 16px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .logo {
            font-size: 1.4rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-icons {
            display: flex;
            gap: 12px;
        }

        .icon-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
        }

        .badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ff3838;
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .search-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-box input {
            border: none;
            background: none;
            flex: 1;
            font-size: 0.9rem;
            outline: none;
        }

        /* Categories */
        .categories {
            padding: 12px 16px;
            overflow-x: auto;
            white-space: nowrap;
        }

        .categories::-webkit-scrollbar {
            display: none;
        }

        .category-item {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            margin-right: 20px;
            cursor: pointer;
        }

        .category-icon {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
        }

        .category-item.active .category-icon {
            background: linear-gradient(135deg, #ff6b35, #ff8c61);
            color: white;
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(255, 107, 53, 0.4);
        }

        .category-name {
            font-size: 0.8rem;
            color: #666;
            font-weight: 600;
        }

        .category-item.active .category-name {
            color: #ff6b35;
        }

        /* Menu */
        .main-content {
            min-height: 100vh;
            padding-bottom: 110px;
        }

        .section-title {
            padding: 20px 16px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title h2 {
            font-size: 1.3rem;
            color: #333;
        }

        .menu-grid {
            padding: 0 16px 16px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        @media (max-width: 576px) {
            .menu-grid {
                gap: 12px;
            }
        }

        .menu-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .menu-img-wrapper {
            position: relative;
            width: 100%;
            height: 140px;
            overflow: hidden;
        }

        .menu-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .menu-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: rgba(255, 255, 255, 0.95);
            color: #ff6b35;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
        }

        .favorite-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(255, 255, 255, 0.95);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            cursor: pointer;
        }

        .favorite-btn.active {
            color: #ff3838;
        }

        .menu-body {
            padding: 12px;
        }

        .menu-name {
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 38px;
        }

        .menu-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .menu-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #ff6b35;
        }

        .btn-add {
            background: linear-gradient(135deg, #ff6b35, #ff8c61);
            color: white;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
            cursor: pointer;
        }

        /* Modal */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
            backdrop-filter: blur(4px);
        }

        .modal.active {
            display: flex;
            align-items: flex-end;
        }

        .modal-content {
            background: white;
            width: 100%;
            max-height: 90vh;
            border-radius: 24px 24px 0 0;
            display: flex;
            flex-direction: column;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }

            to {
                transform: translateY(0);
            }
        }

        .modal-handle {
            width: 40px;
            height: 4px;
            background: #ddd;
            border-radius: 2px;
            margin: 12px auto;
        }

        .modal-header {
            padding: 0 20px 16px;
            border-bottom: 1px solid #f0f0f0;
        }

        .modal-header h3 {
            font-size: 1.3rem;
            color: #333;
        }

        .modal-body {
            flex: 1;
            overflow-y: auto;
            padding: 16px 20px;
        }

        /* Cart */
        .cart-item {
            display: flex;
            gap: 12px;
            background: #f8f8f8;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .cart-item-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .cart-item-price {
            font-size: 0.9rem;
            color: #ff6b35;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 8px;
            background: #ff6b35;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        .qty-text {
            min-width: 30px;
            text-align: center;
            font-weight: 600;
        }

        .btn-remove {
            background: none;
            border: none;
            color: #ff3838;
            cursor: pointer;
            padding: 8px;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 16px;
            opacity: 0.3;
        }

        /* Footer */
        .modal-footer {
            padding: 16px 20px 20px;
            border-top: 1px solid #f0f0f0;
            background: white;
        }

        .subtotal-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: #666;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .total-row .price {
            color: #ff6b35;
        }

        .btn-checkout {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #ff6b35, #ff8c61);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }

        /* Form */
        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            outline: none;
            border-color: #ff6b35;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 8px;
        }

        .payment-method {
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            /* icon di atas */
            align-items: center;
            /* center horizontal */
            justify-content: center;
            /* center vertical */
            text-align: center;
        }

        .payment-method.active {
            border-color: #ff6b35;
            background: #fff3ed;
        }

        .payment-method i {
            font-size: 1.5rem;
            margin-bottom: 6px;
        }

        /* Order Type */
        .order-type-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 8px;
        }

        .order-type-option {
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            /* icon di atas */
            align-items: center;
            /* tengah horizontal */
            justify-content: center;
            /* tengah vertikal */
            text-align: center;
        }

        .order-type-option.active {
            border-color: #ff6b35;
            background: #fff3ed;
        }

        .order-type-option i {
            font-size: 2rem;
            margin-bottom: 8px;
            color: #ff6b35;
        }

        .order-type-option strong {
            font-size: 1rem;
        }

        /* Success */
        .success-modal {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }

        .order-number {
            background: #f0f0f0;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            color: #ff6b35;
            margin: 16px 0 24px;
        }

        .btn-done {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 14px 24px;
            border-radius: 25px;
            font-size: 0.9rem;
            z-index: 3000;
            display: none;
        }

        .toast.show {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Bottom Cart */
        .bottom-cart {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #ff6b35, #ff8c61);
            color: white;
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 99;
            cursor: pointer;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.15);
        }

        .cart-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .cart-qty {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .cart-total {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .cart-action {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .meja-btn.active {
            background: #ff6b35 !important;
            color: #fff !important;
            border-color: #ff6b35 !important;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-top">
            <div class="logo"><i class="fas fa-utensils"></i> Restaurant HQ</div>
            <div class="header-icons">
                <button class="icon-btn" onclick="toggleFavorites()"><i class="fas fa-heart"></i></button>
                <button class="icon-btn" onclick="toggleCart()"><i class="fas fa-shopping-cart"></i><span
                        class="badge" id="cart-badge">0</span></button>
            </div>
        </div>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Cari menu favorit..." id="search-input">
        </div>
    </div>

    <div class="main-content">
        <div class="categories mt-2">
            <div class="category-item active" data-category="all">
                <div class="category-icon">🍽️</div>
                <div class="category-name">Semua</div>
            </div>
            <div class="category-item" data-category="Makanan">
                <div class="category-icon">🍛</div>
                <div class="category-name">Makanan</div>
            </div>
            <div class="category-item" data-category="Minuman">
                <div class="category-icon">🥤</div>
                <div class="category-name">Minuman</div>
            </div>
            <div class="category-item" data-category="Snack">
                <div class="category-icon">🍟</div>
                <div class="category-name">Snack</div>
            </div>
            <div class="category-item" data-category="Dessert">
                <div class="category-icon">🍰</div>
                <div class="category-name">Dessert</div>
            </div>
        </div>

        <div class="section-title">
            <h2>Menu Populer</h2>
        </div>

        <div class="menu-grid">
            @forelse ($menus as $menu)
            <div class="menu-card" data-category="{{ $menu->menu_kategori }}">
                <div class="menu-img-wrapper">
                    <img src="{{ asset('uploads/menu/' . $menu->menu_image) }}" class="menu-img"
                        alt="{{ $menu->menu_name }}">
                    <div class="menu-badge">{{ $menu->menu_kategori }}</div>
                    <button class="favorite-btn" onclick="toggleFavorite(this, {{ $menu->menu_id }})"><i
                            class="fas fa-heart"></i></button>
                </div>
                <div class="menu-body">
                    <div class="menu-name">{{ $menu->menu_name }}</div>
                    <div class="menu-footer">
                        <div class="menu-price">Rp {{ number_format($menu->menu_price, 0, ',', '.') }}</div>
                        <button class="btn-add" onclick='addToCart(@json($menu))'><i
                                class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: span 2; text-align:center; color:#999; padding: 40px;">Menu belum tersedia</div>
            @endforelse
        </div>
    </div>

    <div class="bottom-cart" id="bottom-cart" onclick="toggleCart()">
        <div class="cart-info">
            <div class="cart-qty">
                <i class="fas fa-shopping-cart"></i>
                <span id="bottom-cart-qty">0</span> item
            </div>
            <div class="cart-total">
                Total: <strong id="bottom-cart-total">Rp 0</strong>
            </div>
        </div>
        <div class="cart-action">
            <span>Lihat Pesanan</span>
            <i class="fas fa-chevron-up"></i>
        </div>
    </div>


    <div class="modal" id="cart-modal" onclick="closeModal('cart-modal')">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-handle"></div>
            <div class="modal-header">
                <h3><i class="fas fa-shopping-cart"></i> Keranjang</h3>
            </div>
            <div class="modal-body" id="cart-body">
                <div class="empty-cart"><i class="fas fa-shopping-cart"></i>
                    <h4>Keranjang Kosong</h4>
                </div>
            </div>
            <div class="modal-footer" id="cart-footer" style="display:none;">
                <div class="subtotal-row"><span>Subtotal</span><span id="subtotal">Rp 0</span></div>
                <div class="subtotal-row"><span>Pajak (10%)</span><span id="tax">Rp 0</span></div>
                <div class="total-row"><span>Total</span><span class="price" id="total">Rp 0</span></div>
                <button class="btn-checkout" onclick="showCheckout()"><i class="fas fa-credit-card"></i>
                    Checkout</button>
            </div>
        </div>
    </div>

    <div class="modal" id="checkout-modal" onclick="closeModal('checkout-modal')">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-handle"></div>
            <div class="modal-header">
                <h3><i class="fas fa-receipt"></i> Checkout</h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama </label>
                    <input type="text" class="form-control" id="customer-name" required placeholder="Masukan Nama Anda">
                </div>

                <!-- ORDER TYPE: Dine In / Takeaway -->
                <div class="form-group">
                    <label class="form-label"><strong>Tipe Pesanan *</strong></label>
                    <div class="order-type-options">
                        <div class="order-type-option active" data-type="dine_in" onclick="selectOrderType(this)">
                            <i class="fas fa-utensils"></i>
                            <strong>Makan di Sini</strong>
                        </div>
                        <div class="order-type-option" data-type="takeaway" onclick="selectOrderType(this)">
                            <i class="fas fa-shopping-bag"></i>
                            <strong>Bawa Pulang</strong>
                        </div>
                    </div>
                </div>

                <!-- TABLE SELECTION (Only for Dine In) -->
                <div class="form-group meja-section" id="meja-section">
                    <label class="form-label"><strong>Pilih Meja *</strong></label>

                    <input type="hidden" id="table-number">

                    <div class="row g-2">
                        @foreach ($mejas as $meja)
                        @php
                        $isAvailable = $meja->meja_status === 'kosong';
                        @endphp

                        <div class="col-4 col-md-3">
                            <button type="button"
                                class="btn meja-btn w-100 {{ $isAvailable ? 'btn-outline-primary' : 'btn-danger disabled' }}"
                                data-id="{{ $meja->meja_id }}" data-name="{{ $meja->meja_nama }}"
                                data-kapasitas="{{ $meja->meja_kapasitas }}" {{ $isAvailable ? '' : 'disabled' }}
                                onclick="selectMeja(this)">

                                <strong>{{ $meja->meja_nama }}</strong><br>
                                <small>{{ $meja->meja_kapasitas }} orang</small>
                            </button>
                        </div>
                        @endforeach
                    </div>

                    <small class="text-muted mt-2 d-block" id="meja-info">
                        Belum memilih meja
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Pembayaran *</label>
                    <div class="payment-methods">
                        <div class="payment-method active" data-method="cash" onclick="selectPayment(this)"><i
                                class="fas fa-money-bill-wave"></i><span>Tunai</span></div>
                        <div class="payment-method" data-method="qris" onclick="selectPayment(this)"><i
                                class="fas fa-qrcode"></i><span>QRIS</span></div>
                        <div class="payment-method" data-method="debit" onclick="selectPayment(this)"><i
                                class="fas fa-credit-card"></i><span>Debit</span></div>
                        <div class="payment-method" data-method="ewallet" onclick="selectPayment(this)"><i
                                class="fas fa-wallet"></i><span>E-Wallet</span></div>
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Catatan (optional)</label>
                    <textarea
                        class="form-control"
                        id="order-notes"
                        name="notes"
                        placeholder="Tambahkan catatan jika ada (opsional)"></textarea>
                </div>
            </div>
            <div class="modal-footer"><button class="btn-checkout" onclick="submitOrder()"><i
                        class="fas fa-check-circle"></i> Konfirmasi</button></div>
        </div>
    </div>

    <div class="modal" id="success-modal">
        <div class="modal-content">
            <div class="modal-body success-modal">
                <div class="success-icon"><i class="fas fa-check"></i></div>
                <h3>Pesanan Berhasil!</h3>
                <p id="success-message">Pesanan Anda sedang diproses</p>
                <div class="order-number" id="order-number">#ORD-001</div>
                <button class="btn-done" onclick="closeSuccess()"><i class="fas fa-home"></i> Kembali</button>
            </div>
        </div>
    </div>

    <div class="toast" id="toast"></div>
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    <script>
        let cart = [];
        let favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
        let selectedPayment = 'cash';
        let selectedMeja = null;
        let selectedOrderType = 'dine_in';

        // ORDER TYPE SELECTION
        function selectOrderType(el) {
            document.querySelectorAll('.order-type-option')
                .forEach(opt => opt.classList.remove('active'));

            el.classList.add('active');
            selectedOrderType = el.dataset.type;

            const mejaSection = document.getElementById('meja-section');

            if (selectedOrderType === 'takeaway') {
                mejaSection.style.display = 'none';

                // 🔥 RESET MEJA TOTAL
                selectedMeja = null;
                document.getElementById('table-number').value = '';
                document.getElementById('meja-info').textContent = 'Belum memilih meja';
                document.querySelectorAll('.meja-btn')
                    .forEach(b => b.classList.remove('active'));

            } else {
                mejaSection.style.display = 'block';
            }
        }


        function selectMeja(btn) {
            document.querySelectorAll('.meja-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            selectedMeja = {
                id: btn.dataset.id,
                name: btn.dataset.name,
                kapasitas: btn.dataset.kapasitas
            };

            document.getElementById('table-number').value = selectedMeja.id;

            document.getElementById('meja-info').innerHTML =
                `Meja dipilih: <strong>${selectedMeja.name}</strong> (Kapasitas ${selectedMeja.kapasitas} orang)`;
        }


        // Category Filter
        document.querySelectorAll('.category-item').forEach(item => {
            item.onclick = function() {
                document.querySelectorAll('.category-item').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const cat = this.dataset.category;
                document.querySelectorAll('.menu-card').forEach(card => {
                    card.style.display = (cat === 'all' || card.dataset.category === cat) ? 'block' :
                        'none';
                });
            };
        });

        // Search
        document.getElementById('search-input').oninput = function(e) {
            const query = e.target.value.toLowerCase();
            document.querySelectorAll('.menu-card').forEach(card => {
                const name = card.querySelector('.menu-name').textContent.toLowerCase();
                card.style.display = name.includes(query) ? 'block' : 'none';
            });
        };

        // Add to Cart
        function addToCart(menu) {
            const exist = cart.find(item => item.menu_id === menu.menu_id);
            if (exist) {
                exist.qty++;
            } else {
                cart.push({
                    ...menu,
                    menu_price: Number(menu.menu_price),
                    qty: 1
                });
            }
            updateCart();
            showToast('✓ Ditambahkan ke keranjang');
        }

        // Update Cart
        function updateCart() {
            document.getElementById('cart-badge').textContent = cart.reduce((sum, item) => sum + item.qty, 0);

            if (cart.length === 0) {
                document.getElementById('cart-body').innerHTML =
                    '<div class="empty-cart"><i class="fas fa-shopping-cart"></i><h4>Keranjang Kosong</h4></div>';
                document.getElementById('cart-footer').style.display = 'none';
                return;
            }

            let html = '';
            cart.forEach(item => {
                html += `
                    <div class="cart-item">
                        <img src="/uploads/menu/${item.menu_image}" class="cart-item-img">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.menu_name}</div>
                            <div class="cart-item-price">Rp ${formatPrice(item.menu_price)}</div>
                            <div class="qty-control">
                                <button class="qty-btn" onclick="updateQty(${item.menu_id}, -1)">-</button>
                                <span class="qty-text">${item.qty}</span>
                                <button class="qty-btn" onclick="updateQty(${item.menu_id}, 1)">+</button>
                            </div>
                        </div>
                        <button class="btn-remove" onclick="removeItem(${item.menu_id})"><i class="fas fa-trash"></i></button>
                    </div>
                `;
            });

            document.getElementById('cart-body').innerHTML = html;

            const subtotal = cart.reduce((sum, item) => {
                return sum + (Number(item.menu_price) * Number(item.qty));
            }, 0);

            const tax = Math.round(subtotal * 0.1);
            const total = subtotal + tax;


            document.getElementById('subtotal').textContent = 'Rp ' + formatPrice(subtotal);
            document.getElementById('tax').textContent = 'Rp ' + formatPrice(tax);
            document.getElementById('total').textContent = 'Rp ' + formatPrice(total);
            // Bottom cart update
            const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
            const rawTotal = cart.reduce((sum, item) => {
                return sum + (Number(item.menu_price) * Number(item.qty));
            }, 0);

            const totalPrice = Math.round(rawTotal * 1.1);

            document.getElementById('bottom-cart-qty').textContent = totalQty;
            document.getElementById('bottom-cart-total').textContent = 'Rp ' + formatPrice(totalPrice);

            document.getElementById('cart-footer').style.display = 'block';
        }


        function updateQty(id, delta) {
            const item = cart.find(i => i.menu_id === id);
            if (item) {
                item.qty += delta;
                if (item.qty <= 0) removeItem(id);
                else updateCart();
            }
        }

        function removeItem(id) {
            cart = cart.filter(i => i.menu_id !== id);
            updateCart();
            showToast('Item dihapus');
        }

        // Modals
        function toggleCart() {
            document.getElementById('cart-modal').classList.toggle('active');
        }

        function showCheckout() {
            if (cart.length === 0) return;
            document.getElementById('cart-modal').classList.remove('active');
            document.getElementById('checkout-modal').classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Payment
        function selectPayment(el) {
            document.querySelectorAll('.payment-method').forEach(p => p.classList.remove('active'));
            el.classList.add('active');
            selectedPayment = el.dataset.method;
        }

        // Submit Order
        async function submitOrder() {
            const name = document.getElementById('customer-name').value;
            const table = document.getElementById('table-number').value;
            const notes = document.getElementById('order-notes').value;

            // Validation
            if (!name) {
                showToast('⚠ Harap isi nama');
                return;
            }

            if (selectedOrderType === 'dine_in' && !table) {
                showToast('⚠ Harap pilih meja untuk Makan di Sini');
                return;
            }

            const subtotal = cart.reduce((sum, item) => sum + item.menu_price * item.qty, 0);
            const tax = Math.round(subtotal * 0.1);
            const total = subtotal + tax;

            // Adjust order data to match controller expectations
            const orderData = {
                customer_name: name,
                table_number: selectedOrderType === 'dine_in' ? parseInt(table) : null,
                order_type: selectedOrderType,
                payment_method: selectedPayment.toLowerCase(), // convert to lowercase
                notes: notes || '',
                items: cart.map(item => ({
                    menu_id: item.menu_id,
                    menu_name: item.menu_name,
                    menu_price: parseFloat(item.menu_price),
                    qty: parseInt(item.qty)
                })),
                subtotal: parseFloat(subtotal),
                tax: parseFloat(tax),
                total: parseFloat(total)
            };

            try {
                const response = await fetch('/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(orderData)
                });

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Non-JSON response:', text);
                    showToast('❌ Server error - Invalid response format');
                    return;
                }

                const result = await response.json();

                // Check HTTP status
                if (!response.ok) {
                    showToast('❌ ' + (result.message || 'Terjadi kesalahan'));
                    return;
                }

                if (result.payment_data?.snap_token) {
                    window.snap.pay(result.payment_data.snap_token, {
                        onSuccess: () => showSuccessMessage(result.order_number, 'Pembayaran berhasil'),
                        onPending: () => showSuccessMessage(result.order_number, 'Menunggu pembayaran'),
                        onError: () => showToast('❌ Pembayaran gagal'),
                        onClose: () => showToast('⚠ Pembayaran dibatalkan'),
                    });
                } else {
                    showSuccessMessage(result.order_number, 'Pesanan berhasil dibuat');
                }

            } catch (error) {
                console.error('Submit order error:', error);
                showToast('❌ Terjadi kesalahan server: ' + error.message);
            }
        }

        async function updatePaymentStatus(orderId, status, paymentData) {
            try {
                await fetch(`/api/orders/${orderId}/payment-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        status: status,
                        payment_data: paymentData
                    })
                });
            } catch (error) {
                console.error('Error updating payment status:', error);
            }
        }

        function showSuccessMessage(orderNumber, message) {
            document.getElementById('order-number').textContent = orderNumber;
            document.getElementById('success-message').textContent = message;
            document.getElementById('checkout-modal').classList.remove('active');
            document.getElementById('success-modal').classList.add('active');
        }


        function closeSuccess() {
            document.getElementById('success-modal').classList.remove('active');

            document.getElementById('customer-name').value = '';
            document.getElementById('table-number').value = '';
            document.getElementById('order-notes').value = '';

            // Reset order type
            document.querySelectorAll('.order-type-option').forEach(opt => opt.classList.remove('active'));
            document.querySelector('.order-type-option[data-type="dine_in"]').classList.add('active');
            selectedOrderType = 'dine_in';
            document.getElementById('meja-section').style.display = 'block';

            // Reset meja
            document.querySelectorAll('.meja-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('meja-info').textContent = 'Belum memilih meja';
            selectedMeja = null;

            // Reset payment ke cash
            document.querySelectorAll('.payment-method').forEach(p => p.classList.remove('active'));
            document.querySelector('.payment-method[data-method="cash"]').classList.add('active');
            selectedPayment = 'cash';
            resetCart(); // ⬅️ INI YANG KURANG
        }


        // Favorites
        function toggleFavorite(btn, id) {
            btn.classList.toggle('active');
            if (favorites.includes(id)) {
                favorites = favorites.filter(f => f !== id);
            } else {
                favorites.push(id);
            }
            localStorage.setItem('favorites', JSON.stringify(favorites));
        }

        function toggleFavorites() {
            showToast('ℹ Fitur favorit dalam pengembangan');
        }

        // Utils
        function formatPrice(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2000);
        }

        function resetCart() {
            cart = [];

            // Reset badge & bottom cart
            document.getElementById('cart-badge').textContent = 0;
            document.getElementById('bottom-cart-qty').textContent = 0;
            document.getElementById('bottom-cart-total').textContent = 'Rp 0';

            // Reset cart modal
            document.getElementById('cart-body').innerHTML = `
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h4>Keranjang Kosong</h4>
        </div>
    `;
            document.getElementById('cart-footer').style.display = 'none';

            // Tutup modal cart
            document.getElementById('cart-modal').classList.remove('active');
        }
    </script>
</body>

</html>