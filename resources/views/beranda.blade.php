@extends('template.home')
@section('content_customer')

<!-- Categories -->
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

<!-- Section Title -->
<div class="section-title">
    <h2>Menu Populer</h2>
    <a href="#" class="view-all">Lihat Semua</a>
</div>

<!-- Menu Grid -->
<div class="menu-grid">
    @forelse ($menus as $menu)
    <div class="menu-card" data-category="{{ $menu->menu_kategori }}">
        <div class="menu-img-wrapper">
            <img
                src="{{ asset('uploads/menu/' . $menu->menu_image) }}"
                class="menu-img"
                alt="{{ $menu->menu_name }}">

            <div class="menu-badge">
                {{ $menu->menu_kategori }}
            </div>

            <button class="favorite-btn">
                <i class="fas fa-heart"></i>
            </button>
        </div>

        <div class="menu-body">
            <div class="menu-name">
                {{ $menu->menu_name }}
            </div>

            <div class="menu-footer">
                <div class="menu-price">
                    Rp {{ number_format($menu->menu_price, 0, ',', '.') }}
                </div>

                <button class="btn-add" onclick="addToCart({{ $menu->menu_id }})">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: span 2; text-align:center; color:#999;">
        Menu belum tersedia
    </div>
    @endforelse
</div>

@endsection