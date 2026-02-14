@extends('dashboard.template')

@section('content')
<div class="container-fluid">
    <div class="row">

        {{-- LIST MENU --}}
        <div class="col-12 col-md-7 col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <input type="search" class="form-control w-100" id="searchMenu" placeholder="Cari menu...">
            </div>

            {{-- KATEGORI FILTER --}}
            <div class="category-filter mb-3">
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm btn-outline-secondary category-btn active" data-category="all">
                        <i class="bi bi-grid-fill me-1"></i>Semua
                    </button>
                    @foreach ($kategori as $kat)
                    <button class="btn btn-sm btn-outline-secondary category-btn" data-category="{{ strtolower($kat->kategori_name) }}">
                        {{ $kat->kategori_name }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="row g-3" id="menuContainer">
                @foreach ($menus as $menu)
                <div class="col-6 col-lg-3 menu-item"
                    data-name="{{ strtolower($menu->menu_name) }}"
                    data-category="{{ strtolower($menu->menu_kategori) }}">
                    <div class="card h-100 shadow-sm border-0 menu-card open-drawer"
                        data-id="{{ $menu->menu_id }}"
                        data-name="{{ $menu->menu_name }}"
                        data-desc="{{ $menu->menu_desc }}"
                        data-price="{{ $menu->menu_price }}"
                        data-image="{{ asset('uploads/menu/' . $menu->menu_image) }}">

                        <div class="position-relative">
                            <img src="{{ asset('uploads/menu/' . $menu->menu_image) }}"
                                class="card-img-top"
                                alt="{{ $menu->menu_name }}"
                                loading="lazy">
                        </div>

                        <div class="card-body p-2 text-center">
                            <h6 class="fw-semibold mb-1 text-truncate">{{ $menu->menu_name }}</h6>
                            <small class="text-danger fw-bold">
                                Rp {{ number_format($menu->menu_price, 0, ',', '.') }}
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- PANEL ORDER --}}
        <div class="col-12 col-md-5 col-lg-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 80px">
                <div class="card-header fw-bold bg-gradient text-white d-flex justify-content-between align-items-center">
                    <span>Keranjang</span>
                    <button class="btn btn-sm btn-outline-light" id="clearCart" style="display: none;">
                        <i class="bi bi-trash"></i> Clear
                    </button>
                </div>

                <div class="order-wrapper">
                    <ul class="list-group list-group-flush" id="order-list">
                        <li class="list-group-item text-center text-muted py-4">
                            <i class="bi bi-cart3 fs-1 opacity-25"></i>
                            <p class="mb-0 mt-2">Belum ada pesanan</p>
                        </li>
                    </ul>
                </div>

                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between mb-2 align-items-center">
                        <span class="text-muted">Subtotal</span>
                        <strong id="total-harga" class="fs-5 text-primary">Rp 0</strong>
                    </div>

                    <button class="btn btn-warning w-100 fw-bold shadow-sm" id="checkoutBtn" disabled>
                        <i class="bi bi-check-circle me-2"></i>Checkout
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- FLOATING CART BUTTON (MOBILE ONLY) --}}
<div class="floating-cart-btn" id="floatingCartBtn">
    <button class="btn-cart-toggle" id="openCartSheet">
        <i class="bi bi-cart3"></i>
        <span class="cart-badge" id="cartBadge">0</span>
        <div class="cart-info">
            <small class="d-block">Keranjang</small>
            <strong id="floating-total">Rp 0</strong>
        </div>
    </button>
</div>

{{-- BOTTOM SHEET CART (MOBILE ONLY) --}}
<div class="bottom-sheet-overlay" id="bottomSheetOverlay"></div>
<div class="bottom-sheet-cart" id="bottomSheetCart">
    <div class="bottom-sheet-handle">
        <div class="handle-bar"></div>
    </div>

    <div class="bottom-sheet-header">
        <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>Keranjang</h5>
        <button class="btn-close-sheet" id="closeCartSheet">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="bottom-sheet-body" id="bottomSheetBody">
        <div class="empty-cart-message">
            <i class="bi bi-cart3 fs-1 opacity-25"></i>
            <p class="mb-0 mt-2">Belum ada pesanan</p>
        </div>
    </div>

    <div class="bottom-sheet-footer">
        <div class="summary-row">
            <span class="text-muted">Subtotal</span>
            <strong id="sheet-subtotal">Rp 0</strong>
        </div>
        <div class="summary-row">
            <span class="text-muted">Pajak (10%)</span>
            <strong id="sheet-tax">Rp 0</strong>
        </div>
        <div class="summary-row total-row">
            <span class="fw-bold">Total</span>
            <strong class="text-danger" id="sheet-total">Rp 0</strong>
        </div>
        <button class="btn btn-warning w-100 fw-bold btn-checkout-sheet" id="checkoutSheetBtn" disabled>
            <i class="bi bi-wallet2 me-2"></i>Checkout
        </button>
    </div>
</div>

{{-- BACKDROP OVERLAY --}}
<div id="drawerBackdrop" class="drawer-backdrop"></div>

{{-- DRAWER KANAN --}}
<div id="cartDrawer" class="cart-drawer">
    <div class="cart-header">
        <span>Tambah Pesanan</span>
        <button id="closeDrawer" class="btn-close-drawer">✕</button>
    </div>

    <div class="cart-body">
        <img id="drawerImage" class="cart-image" alt="Menu Image">

        <h5 id="drawerName" class="fw-bold mt-3 mb-2"></h5>
        <p id="drawerDesc" class="text-muted small mb-2"></p>
        <p id="drawerPrice" class="text-danger fw-bold fs-5 mb-0"></p>

        <div class="qty-section mt-4">
            <label class="form-label fw-semibold">Jumlah</label>
            <div class="qty-control">
                <button id="qtyMinus" class="qty-btn">−</button>
                <input type="number" id="drawerQty" value="1" min="1" class="form-control">
                <button id="qtyPlus" class="qty-btn">+</button>
            </div>
        </div>

        <div class="subtotal-section mt-3">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Subtotal:</span>
                <strong id="drawerSubtotal" class="text-primary">Rp 0</strong>
            </div>
        </div>
    </div>

    <div class="cart-footer">
        <button class="btn btn-danger w-100 fw-bold shadow" id="confirmAdd">
            <i class="bi bi-cart-plus me-2"></i>Tambah ke Keranjang
        </button>
    </div>
</div>

{{-- STYLE --}}
<style>
    :root {
        --primary-color: #ff7a45;
        --hover-shadow: 0 10px 20px rgba(0, 0, 0, .15);
    }

    .category-filter {
        overflow-x: auto;
        white-space: nowrap;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .category-filter::-webkit-scrollbar {
        display: none;
    }

    .category-btn {
        transition: all .3s ease;
        border-radius: 20px;
        padding: 8px 16px;
    }

    .category-btn.active {
        background: linear-gradient(135deg, #ff7a0e 0%, #c85706 100%);
        border-color: transparent;
        color: white;
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
    }

    .category-btn:hover:not(.active) {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .menu-card {
        cursor: pointer;
        transition: all .3s ease;
        border-radius: 12px;
        overflow: hidden;
    }

    .menu-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--hover-shadow);
    }

    .menu-card:active {
        transform: translateY(-2px);
    }

    .menu-card img {
        height: 140px;
        object-fit: cover;
        transition: transform .3s ease;
    }

    .menu-card:hover img {
        transform: scale(1.05);
    }

    .badge-overlay {
        position: absolute;
        top: 8px;
        right: 8px;
    }

    .order-wrapper {
        max-height: 50vh;
        overflow-y: auto;
    }

    .order-wrapper::-webkit-scrollbar {
        width: 6px;
    }

    .order-wrapper::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }

    .bg-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* ============================= */
    /* FLOATING CART BUTTON */
    /* ============================= */
    .floating-cart-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        display: none;
    }

    .floating-cart-btn.show {
        display: block;
        animation: slideUpRight 0.3s ease;
    }

    .btn-cart-toggle {
        background: linear-gradient(135deg, #ff7a0e 0%, #c85706 100%);
        border: none;
        border-radius: 50px;
        padding: 12px 20px;
        color: white;
        box-shadow: 0 4px 20px rgba(255, 122, 14, 0.4);
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        min-width: 140px;
    }

    .btn-cart-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(255, 122, 14, 0.5);
    }

    .btn-cart-toggle:active {
        transform: translateY(0);
    }

    .btn-cart-toggle i {
        font-size: 22px;
    }

    .cart-badge {
        position: absolute;
        top: -5px;
        left: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .cart-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        flex: 1;
    }

    .cart-info small {
        font-size: 11px;
        opacity: 0.9;
        line-height: 1;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .cart-info strong {
        font-size: 15px;
        line-height: 1.3;
        font-weight: 700;
    }

    @keyframes slideUpRight {
        from {
            opacity: 0;
            transform: translate(20px, 20px);
        }

        to {
            opacity: 1;
            transform: translate(0, 0);
        }
    }

    /* ============================= */
    /* BOTTOM SHEET CART */
    /* ============================= */
    .bottom-sheet-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .bottom-sheet-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .bottom-sheet-cart {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-radius: 24px 24px 0 0;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
        z-index: 1041;
        transform: translateY(100%);
        transition: transform 0.3s ease;
        max-height: 75vh;
        display: flex;
        flex-direction: column;
    }

    .bottom-sheet-cart.show {
        transform: translateY(0);
    }

    /* Ensure SweetAlert appears above bottom sheet */
    .swal2-container {
        z-index: 10000 !important;
    }

    .bottom-sheet-handle {
        padding: 12px 0;
        display: flex;
        justify-content: center;
        cursor: pointer;
    }

    .handle-bar {
        width: 40px;
        height: 4px;
        background: #ddd;
        border-radius: 2px;
    }

    .bottom-sheet-header {
        padding: 0 20px 16px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-close-sheet {
        background: #f0f0f0;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-close-sheet:active {
        transform: scale(0.9);
    }

    .bottom-sheet-body {
        flex: 1;
        overflow-y: auto;
        padding: 16px 20px;
    }

    .bottom-sheet-body::-webkit-scrollbar {
        width: 4px;
    }

    .bottom-sheet-body::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 2px;
    }

    .empty-cart-message {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }

    .cart-item-sheet {
        display: flex;
        gap: 12px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 12px;
        margin-bottom: 12px;
        animation: slideIn 0.3s ease;
    }

    .cart-item-image {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        object-fit: cover;
    }

    .cart-item-details {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .cart-item-name {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .cart-item-price {
        color: #ff6b35;
        font-weight: 600;
        font-size: 13px;
    }

    .cart-item-controls {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 4px;
    }

    .qty-btn-small {
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 50%;
        background: white;
        color: #ff7a0e;
        font-size: 16px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s;
    }

    .qty-btn-small:active {
        transform: scale(0.9);
    }

    .qty-display {
        font-weight: 600;
        min-width: 25px;
        text-align: center;
    }

    .btn-remove-item {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 18px;
        padding: 4px;
        cursor: pointer;
        margin-left: auto;
    }

    .bottom-sheet-footer {
        padding: 20px;
        border-top: 1px solid #eee;
        background: white;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .summary-row.total-row {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 2px dashed #ddd;
        font-size: 16px;
    }

    .btn-checkout-sheet {
        margin-top: 16px;
        padding: 14px;
        font-size: 16px;
        border-radius: 12px;
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
        }

        to {
            transform: translateY(0);
        }
    }

    /* ============================= */
    /* DRAWER STYLES */
    /* ============================= */
    .drawer-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: all .3s ease;
    }

    .drawer-backdrop.show {
        opacity: 1;
        visibility: visible;
    }

    .cart-drawer {
        position: fixed;
        top: 0;
        right: -400px;
        width: 400px;
        height: 100%;
        background: #fff;
        box-shadow: -4px 0 20px rgba(0, 0, 0, .3);
        z-index: 1041;
        transition: right .3s ease;
        display: flex;
        flex-direction: column;
    }

    .cart-drawer.show {
        right: 0;
    }

    .cart-header {
        padding: 20px;
        font-weight: bold;
        font-size: 1.1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #eee;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-close-drawer {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        font-size: 20px;
        cursor: pointer;
        transition: all .2s;
    }

    .btn-close-drawer:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .cart-body {
        padding: 20px;
        text-align: center;
        flex: 1;
        overflow-y: auto;
    }

    .cart-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
    }

    .qty-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 12px;
    }

    .qty-control {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        margin-top: 10px;
    }

    .qty-btn {
        width: 45px;
        height: 45px;
        border: none;
        border-radius: 50%;
        background: var(--primary-color);
        color: #fff;
        font-size: 20px;
        cursor: pointer;
        transition: all .2s;
        box-shadow: 0 2px 8px rgba(255, 122, 69, 0.3);
    }

    .qty-btn:hover {
        background: #ff6433;
        transform: scale(1.1);
    }

    .qty-btn:active {
        transform: scale(0.95);
    }

    .qty-control input {
        width: 80px;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 8px;
    }

    .qty-control input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(255, 122, 69, 0.25);
    }

    .subtotal-section {
        background: #fff3e0;
        padding: 12px;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color);
    }

    .cart-footer {
        padding: 20px;
        border-top: 1px solid #eee;
    }

    .order-item {
        transition: all .2s;
        border-radius: 8px;
        margin-bottom: 8px;
    }

    .order-item:hover {
        background: #f8f9fa;
    }

    .btn-remove {
        padding: 2px 8px;
        font-size: 12px;
        border-radius: 4px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .menu-item {
        animation: fadeIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .order-item {
        animation: slideIn 0.3s ease;
    }

    /* ============================= */
    /* DESKTOP - GRID LAYOUT */
    /* ============================= */
    @media (min-width: 992px) {
        .container-fluid>.row {
            display: grid !important;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
            align-items: start;
        }

        .container-fluid>.row>[class*="col-"] {
            max-width: 100% !important;
            width: 100% !important;
        }

        .container-fluid>.row>.col-lg-4 {
            position: sticky;
            top: 80px;
            height: fit-content;
        }
    }

    /* ============================= */
    /* TABLET MODE (768px - 991px) */
    /* ============================= */
    @media (min-width: 768px) and (max-width: 991px) {

        /* Menu jadi 3 kolom di tablet */
        .menu-item {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }

        .menu-card img {
            height: 150px;
        }

        /* Sidebar keranjang tetap visible */
        .col-md-7 {
            flex: 0 0 60%;
            max-width: 60%;
        }

        .col-md-5 {
            flex: 0 0 40%;
            max-width: 40%;
            position: sticky;
            top: 80px;
            height: fit-content;
        }

        .order-wrapper {
            max-height: 45vh;
        }

        /* Hide floating button and bottom sheet on tablet */
        .floating-cart-btn {
            display: none !important;
        }

        .bottom-sheet-cart {
            display: none !important;
        }
    }

    /* ============================= */
    /* MOBILE MODE (< 768px) */
    /* ============================= */
    @media (max-width: 767px) {

        /* Hide desktop cart panel */
        .col-md-5 {
            display: none !important;
        }

        /* Menu full width */
        .col-md-7 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        /* Show floating cart button */
        .floating-cart-btn.show {
            display: block !important;
        }

        /* Menu grid 2 kolom di mobile */
        .menu-item {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .menu-card img {
            height: 120px;
        }

        .menu-card .card-body h6 {
            font-size: 0.85rem;
        }

        .menu-card .card-body small {
            font-size: 0.75rem;
        }

        /* Drawer full width di mobile */
        .cart-drawer {
            width: 100%;
            right: -100%;
        }

        /* Add padding bottom to prevent content hidden by floating button */
        .container-fluid {
            padding-bottom: 90px;
        }

        /* Category buttons smaller */
        .category-btn {
            font-size: 0.85rem;
            padding: 6px 12px;
        }

        /* Drawer adjustments */
        .cart-image {
            height: 180px;
        }

        .qty-btn {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }

        .qty-control input {
            width: 60px;
            font-size: 16px;
        }
    }

    /* ============================= */
    /* SMALL MOBILE (< 576px) */
    /* ============================= */
    @media (max-width: 575px) {
        .menu-card img {
            height: 100px;
        }

        .btn-cart-toggle {
            padding: 10px 16px;
            min-width: 120px;
        }

        .btn-cart-toggle i {
            font-size: 20px;
        }

        .cart-info small {
            font-size: 10px;
        }

        .cart-info strong {
            font-size: 13px;
        }

        .cart-badge {
            width: 20px;
            height: 20px;
            font-size: 10px;
        }

        .floating-cart-btn {
            bottom: 16px;
            right: 16px;
        }
    }
</style>

{{-- SCRIPT --}}
<script>
    const state = {
        cart: {},
        selectedMenu: {},
        activeCategory: 'all'
    };

    const elements = {
        drawer: document.getElementById('cartDrawer'),
        backdrop: document.getElementById('drawerBackdrop'),
        orderList: document.getElementById('order-list'),
        totalHarga: document.getElementById('total-harga'),
        drawerName: document.getElementById('drawerName'),
        drawerDesc: document.getElementById('drawerDesc'),
        drawerPrice: document.getElementById('drawerPrice'),
        drawerImage: document.getElementById('drawerImage'),
        drawerQty: document.getElementById('drawerQty'),
        drawerSubtotal: document.getElementById('drawerSubtotal'),
        searchInput: document.getElementById('searchMenu'),
        checkoutBtn: document.getElementById('checkoutBtn'),
        clearCartBtn: document.getElementById('clearCart'),
        // Bottom Sheet Elements
        floatingCartBtn: document.getElementById('floatingCartBtn'),
        bottomSheetOverlay: document.getElementById('bottomSheetOverlay'),
        bottomSheetCart: document.getElementById('bottomSheetCart'),
        bottomSheetBody: document.getElementById('bottomSheetBody'),
        openCartSheet: document.getElementById('openCartSheet'),
        closeCartSheet: document.getElementById('closeCartSheet'),
        cartBadge: document.getElementById('cartBadge'),
        floatingTotal: document.getElementById('floating-total'),
        sheetSubtotal: document.getElementById('sheet-subtotal'),
        sheetTax: document.getElementById('sheet-tax'),
        sheetTotal: document.getElementById('sheet-total'),
        checkoutSheetBtn: document.getElementById('checkoutSheetBtn')
    };

    function initEventListeners() {
        document.querySelectorAll('.open-drawer').forEach(card => {
            card.addEventListener('click', () => openDrawer(card));
        });
        document.getElementById('qtyPlus').addEventListener('click', incrementQty);
        document.getElementById('qtyMinus').addEventListener('click', decrementQty);
        document.getElementById('closeDrawer').addEventListener('click', closeDrawer);
        elements.backdrop.addEventListener('click', closeDrawer);
        document.getElementById('confirmAdd').addEventListener('click', addToCart);
        elements.drawerQty.addEventListener('input', updateDrawerSubtotal);
        elements.searchInput.addEventListener('input', debounce(filterMenu, 300));
        elements.clearCartBtn.addEventListener('click', confirmClearCart);
        elements.checkoutBtn.addEventListener('click', handleCheckout);

        // Bottom Sheet Events
        elements.openCartSheet.addEventListener('click', openBottomSheet);
        elements.closeCartSheet.addEventListener('click', closeBottomSheet);
        elements.bottomSheetOverlay.addEventListener('click', closeBottomSheet);
        elements.checkoutSheetBtn.addEventListener('click', handleCheckout);

        // Handle swipe down to close
        let startY = 0;
        elements.bottomSheetCart.addEventListener('touchstart', (e) => {
            startY = e.touches[0].clientY;
        });
        elements.bottomSheetCart.addEventListener('touchmove', (e) => {
            const currentY = e.touches[0].clientY;
            const diff = currentY - startY;
            if (diff > 50 && e.target.closest('.bottom-sheet-handle')) {
                closeBottomSheet();
            }
        });

        document.addEventListener('keydown', handleKeyboard);

        // Category Filter
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', () => filterByCategory(btn));
        });

        // Update floating checkout on window resize
        window.addEventListener('resize', updateFloatingUI);
    }

    function filterMenu() {
        const keyword = elements.searchInput.value.toLowerCase();
        const menuItems = document.querySelectorAll('.menu-item');
        menuItems.forEach(item => {
            const menuName = item.dataset.name;
            const menuCategory = String(item.dataset.category);
            const activeCategory = String(state.activeCategory);
            const matchesSearch = menuName.includes(keyword);
            const matchesCategory = activeCategory === 'all' || menuCategory === activeCategory;
            item.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
        });
    }

    function filterByCategory(btn) {
        state.activeCategory = String(btn.dataset.category);
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        filterMenu();
    }

    function openDrawer(card) {
        state.selectedMenu = {
            id: card.dataset.id,
            name: card.dataset.name,
            desc: card.dataset.desc,
            price: parseInt(card.dataset.price),
            image: card.dataset.image
        };
        elements.drawerName.innerText = state.selectedMenu.name;
        elements.drawerDesc.innerText = state.selectedMenu.desc || '-';
        elements.drawerPrice.innerText = formatRupiah(state.selectedMenu.price);
        elements.drawerImage.src = state.selectedMenu.image;
        elements.drawerQty.value = 1;
        updateDrawerSubtotal();
        elements.drawer.classList.add('show');
        elements.backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        elements.drawer.classList.remove('show');
        elements.backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    function incrementQty() {
        elements.drawerQty.value = parseInt(elements.drawerQty.value) + 1;
        updateDrawerSubtotal();
    }

    function decrementQty() {
        const currentQty = parseInt(elements.drawerQty.value);
        if (currentQty > 1) {
            elements.drawerQty.value = currentQty - 1;
            updateDrawerSubtotal();
        }
    }

    function updateDrawerSubtotal() {
        const qty = parseInt(elements.drawerQty.value) || 1;
        const subtotal = qty * state.selectedMenu.price;
        elements.drawerSubtotal.innerText = formatRupiah(subtotal);
    }

    function addToCart() {
        const qty = parseInt(elements.drawerQty.value);
        if (state.cart[state.selectedMenu.id]) {
            state.cart[state.selectedMenu.id].qty += qty;
        } else {
            state.cart[state.selectedMenu.id] = {
                ...state.selectedMenu,
                qty
            };
        }
        renderOrder();
        closeDrawer();
        showToast('Item berhasil ditambahkan ke keranjang');
    }

    function removeFromCart(id) {
        delete state.cart[id];
        renderOrder();
        showToast('Item berhasil dihapus dari keranjang');
    }

    function updateQty(id, change) {
        if (state.cart[id]) {
            state.cart[id].qty += change;
            if (state.cart[id].qty <= 0) {
                removeFromCart(id);
            } else {
                renderOrder();
            }
        }
    }

    function openBottomSheet() {
        elements.bottomSheetOverlay.classList.add('show');
        elements.bottomSheetCart.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeBottomSheet() {
        elements.bottomSheetOverlay.classList.remove('show');
        elements.bottomSheetCart.classList.remove('show');
        document.body.style.overflow = '';
    }

    function updateFloatingUI() {
        const isMobile = window.innerWidth < 768;
        const items = Object.values(state.cart);
        const itemCount = items.reduce((count, item) => count + item.qty, 0);

        if (isMobile && items.length > 0) {
            elements.floatingCartBtn.classList.add('show');
            elements.cartBadge.innerText = itemCount;

            const subtotal = items.reduce((t, i) => t + (i.qty * i.price), 0);
            const tax = subtotal * 0.1;
            const total = subtotal + tax;

            elements.floatingTotal.innerText = formatRupiah(total);
            elements.sheetSubtotal.innerText = formatRupiah(subtotal);
            elements.sheetTax.innerText = formatRupiah(tax);
            elements.sheetTotal.innerText = formatRupiah(total);
            elements.checkoutSheetBtn.disabled = false;

            renderBottomSheetItems(items);
        } else {
            elements.floatingCartBtn.classList.remove('show');
            closeBottomSheet();
        }
    }

    function renderBottomSheetItems(items) {
        if (!items.length) {
            elements.bottomSheetBody.innerHTML = `
                <div class="empty-cart-message">
                    <i class="bi bi-cart3 fs-1 opacity-25"></i>
                    <p class="mb-0 mt-2">Belum ada pesanan</p>
                </div>`;
            return;
        }

        elements.bottomSheetBody.innerHTML = items.map(item => `
            <div class="cart-item-sheet">
                <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                <div class="cart-item-details">
                    <div>
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">${formatRupiah(item.price)}</div>
                    </div>
                    <div class="cart-item-controls">
                        <button class="qty-btn-small" onclick="updateQty('${item.id}', -1)">−</button>
                        <span class="qty-display">${item.qty}</span>
                        <button class="qty-btn-small" onclick="updateQty('${item.id}', 1)">+</button>
                        <span class="ms-2 text-muted" style="font-size: 13px;">= ${formatRupiah(item.qty * item.price)}</span>
                    </div>
                </div>
                <button class="btn-remove-item" onclick="removeFromCart('${item.id}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `).join('');
    }

    function renderOrder() {
        elements.orderList.innerHTML = '';
        let total = 0;
        const items = Object.values(state.cart);

        if (!items.length) {
            elements.orderList.innerHTML = `
                <li class="list-group-item text-center text-muted py-4">
                    <i class="bi bi-cart3 fs-1 opacity-25"></i>
                    <p class="mb-0 mt-2">Belum ada pesanan</p>
                </li>`;
            elements.totalHarga.innerText = 'Rp 0';
            elements.checkoutBtn.disabled = true;
            elements.checkoutSheetBtn.disabled = true;
            elements.clearCartBtn.style.display = 'none';
            updateFloatingUI();
            return;
        }

        items.forEach(item => {
            const subtotal = item.qty * item.price;
            total += subtotal;
            elements.orderList.innerHTML += `
                <li class="list-group-item order-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <strong class="d-block mb-1">${item.name}</strong>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-outline-secondary btn-remove" onclick="updateQty('${item.id}', -1)">−</button>
                                <small class="fw-semibold">${item.qty}</small>
                                <button class="btn btn-sm btn-outline-secondary btn-remove" onclick="updateQty('${item.id}', 1)">+</button>
                                <small class="text-muted">× ${formatRupiah(item.price)}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-danger d-block">${formatRupiah(subtotal)}</span>
                            <button class="btn btn-sm btn-outline-danger mt-1" onclick="removeFromCart('${item.id}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </li>`;
        });

        elements.totalHarga.innerText = formatRupiah(total);
        elements.checkoutBtn.disabled = false;
        elements.checkoutSheetBtn.disabled = false;
        elements.clearCartBtn.style.display = 'block';
        updateFloatingUI();
    }

    function confirmClearCart() {
        if (confirm('Hapus semua item dari keranjang?')) {
            state.cart = {};
            renderOrder();
            showToast('Keranjang berhasil dikosongkan');
        }
    }

    function handleCheckout() {
        const items = Object.values(state.cart);
        if (!items.length) return;

        // Close bottom sheet before showing SweetAlert
        closeBottomSheet();

        Swal.fire({
            title: 'Konfirmasi Checkout',
            text: 'Yakin ingin memproses pesanan ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Checkout',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch("{{ route('dashboard.order.checkout') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        items: items,
                        total: items.reduce((t, i) => t + (i.qty * i.price), 0)
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ url('/dashboard/order') }}/" + res.order_id + "/payment";
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Gagal',
                            text: res.message
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan pada server'
                    });
                });
        });
    }

    function formatRupiah(num) {
        return 'Rp ' + num.toLocaleString('id-ID');
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function showToast(message) {
        console.log('Toast:', message);
    }

    function handleKeyboard(e) {
        if (e.key === 'Escape') {
            if (elements.drawer.classList.contains('show')) {
                closeDrawer();
            }
            if (elements.bottomSheetCart.classList.contains('show')) {
                closeBottomSheet();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initEventListeners();
        updateFloatingUI();
    });

    window.removeFromCart = removeFromCart;
    window.updateQty = updateQty;
</script>
@endsection