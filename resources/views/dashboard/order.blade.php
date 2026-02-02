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

            <div class=" row g-3" id="menuContainer">
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
                            <!-- <div class="badge-overlay">
                                <span class="badge bg-success">Ready</span>
                            </div> -->
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

    .drawer-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9998;
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
        z-index: 9999;
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

    @media (max-width: 768px) {
        .cart-drawer {
            width: 100%;
            right: -100%;
        }

        .order-wrapper {
            max-height: 40vh;
        }
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

    @media (min-width: 768px) and (max-width: 1024px) {
        .menu-card img {
            height: 170px;
        }

        .order-wrapper {
            max-height: 60vh;
        }
    }

    /* =============================== */
    /* POS GRID LAYOUT – FINAL FIX */
    /* =============================== */
    @media (min-width: 900px) {

        .container-fluid>.row {
            display: grid !important;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
            align-items: start;
        }

        /* reset bootstrap column */
        .container-fluid>.row>[class*="col-"] {
            max-width: 100% !important;
            width: 100% !important;
        }

        /* order panel sticky */
        .container-fluid>.row>.col-md-5 {
            position: sticky;
            top: 80px;
            height: fit-content;
        }
    }

    /* ============================= */
    /* TABLET / IPAD MODE */
    /* ============================= */
    @media (min-width: 768px) and (max-width: 1024px) {

        /* Menu grid jadi 2 kolom besar */
        .menu-item {
            width: 50%;
        }

        /* Override bootstrap column */
        .col-lg-8 {
            flex: 0 0 65%;
            max-width: 65%;
        }

        .col-lg-4 {
            flex: 0 0 35%;
            max-width: 35%;
            position: sticky;
            top: 80px;
            height: fit-content;
        }

        /* Card menu lebih besar & rapi */
        .menu-card img {
            height: 160px;
        }

        /* Keranjang lebih compact */
        .order-wrapper {
            max-height: 45vh;
        }

        #checkoutBtn {
            font-size: 14px;
            padding: 10px;
        }

        .card-header {
            font-size: 14px;
        }

        #drawerDesc {
            line-height: 1.4;
            max-height: 3em;
            overflow: hidden;
        }

    }

    /* TABLET MODE (IPAD) */
    /* ============================= */
    @media (min-width: 768px) and (max-width: 1024px) {

        /* MENU KIRI */
        .col-md-7 {
            flex: 0 0 65%;
            max-width: 65%;
        }

        /* ORDER KANAN */
        .col-md-5 {
            flex: 0 0 35%;
            max-width: 35%;
            position: sticky;
            top: 80px;
            height: fit-content;
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
        clearCartBtn: document.getElementById('clearCart')
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
        document.addEventListener('keydown', handleKeyboard);

        // Category Filter
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', () => filterByCategory(btn));
        });
    }

    // function filterByCategory(btn) {
    //     const category = btn.dataset.category;
    //     state.activeCategory = category;

    //     document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
    //     btn.classList.add('active');

    //     filterMenu();
    // }

    function filterMenu() {
        const keyword = elements.searchInput.value.toLowerCase();
        const menuItems = document.querySelectorAll('.menu-item');

        menuItems.forEach(item => {
            const menuName = item.dataset.name;
            const menuCategory = String(item.dataset.category);
            const activeCategory = String(state.activeCategory);

            const matchesSearch = menuName.includes(keyword);
            const matchesCategory =
                activeCategory === 'all' || menuCategory === activeCategory;

            if (matchesSearch && matchesCategory) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
            console.log({
                activeCategory: state.activeCategory,
                menuCategory
            });


        });
    }

    function filterByCategory(btn) {
        state.activeCategory = String(btn.dataset.category);

        document.querySelectorAll('.category-btn')
            .forEach(b => b.classList.remove('active'));

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
            elements.clearCartBtn.style.display = 'none';
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
        elements.clearCartBtn.style.display = 'block';
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
                            window.location.href =
                                "{{ url('/dashboard/order') }}/" + res.order_id + "/payment";
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
        if (e.key === 'Escape' && elements.drawer.classList.contains('show')) {
            closeDrawer();
        }
    }

    document.addEventListener('DOMContentLoaded', initEventListeners);

    window.removeFromCart = removeFromCart;
    window.updateQty = updateQty;
</script>
@endsection