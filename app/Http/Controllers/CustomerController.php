<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Kategori;
use App\Models\Meja;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * HALAMAN CUSTOMER (LIST MENU)
     */

    public function index()
    {
        $kategori = Kategori::orderBy('kategori_name')->get();
        $mejas = Meja::orderBy('meja_id')->get();

        $menus = Menu::orderBy('menu_kategori')
            ->orderBy('menu_name')
            ->get();

        return view('home', compact('kategori', 'mejas', 'menus'));
    }

    /**
     * TAMBAH KE KERANJANG
     */
    public function addCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required',
        ]);

        $menu = Menu::findOrFail($request->menu_id);

        $cart = session()->get('cart', []);

        if (isset($cart[$menu->menu_id])) {
            $cart[$menu->menu_id]['qty'] += 1;
        } else {
            $cart[$menu->menu_id] = [
                'menu_id' => $menu->menu_id,
                'nama'    => $menu->menu_name,
                'harga'  => $menu->menu_price,
                'gambar' => $menu->menu_image,
                'qty'    => 1
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Menu ditambahkan ke keranjang');
    }

    /**
     * HALAMAN KERANJANG
     */
    public function cart()
    {
        $cart = session()->get('cart', []);
        return view('pesanan', compact('cart'));
    }

    /**
     * CHECKOUT
     */
    public function checkout(Request $request)
    {
        // NANTI DI SINI SIMPAN KE:
        // orders
        // order_details

        session()->forget('cart');

        return redirect('/')
            ->with('success', 'Pesanan berhasil dikirim ke kasir');
    }
}
