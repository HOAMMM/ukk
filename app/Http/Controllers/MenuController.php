<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Kategori;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Carbon\Carbon;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('menu_id', 'desc')->get();
        $kategori = Kategori::orderBy('kategori_name', 'asc')->get();

        return view('dashboard.menu', compact('menus', 'kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu_name' => 'required',
            'menu_price' => 'required|numeric',
            'menu_kategori' => 'required',
            'menu_desc' => 'required|string|max:500',
            'menu_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'menu_name.required' => 'Nama menu wajib diisi',
            'menu_desc.required' => 'Deskripsi menu wajib diisi',
            'menu_price.required' => 'Harga menu wajib diisi',
            'menu_price.numeric' => 'Harga menu harus berupa angka',
            'menu_kategori.required' => 'Kategori menu wajib diisi',
            'menu_desc.max' => 'Deskripsi maksimal 500 karakter',
            'menu_image.required' => 'Gambar menu wajib diisi',
            'menu_image.image' => 'File harus berupa gambar',
            'menu_image.mimes' => 'Format gambar harus jpg, jpeg, atau png',
            'menu_image.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        $imageName = null;

        if ($request->hasFile('menu_image')) {
            $image = $request->file('menu_image');
            $imageName = 'menu_' . time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('uploads/menu');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $manager = new ImageManager(new Driver());
            $manager->read($image)
                ->cover(400, 300)
                ->save($path . '/' . $imageName);
        }

        Menu::create([
            'menu_name' => $request->menu_name,
            'menu_price' => $request->menu_price,
            'menu_kategori' => $request->menu_kategori,
            'menu_desc' => $request->menu_desc,
            'menu_code' => 'MN-' . rand(1000, 9999),
            'menu_image' => $imageName,
            'created_at' => now(),
            'update_at' => now(),
        ]);

        return back()->with('success', 'Menu berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $data = $request->validate([
            'menu_name'     => 'required',
            'menu_kategori' => 'required',
            'menu_price'    => 'required|numeric',
            'menu_desc'     => 'nullable|string|max:500',
            'menu_image'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // upload gambar baru
        if ($request->hasFile('menu_image')) {

            // hapus gambar lama
            if ($menu->menu_image && file_exists(public_path('uploads/menu/' . $menu->menu_image))) {
                unlink(public_path('uploads/menu/' . $menu->menu_image));
            }

            $image = $request->file('menu_image');
            $imageName = 'menu_' . time() . '.' . $image->extension();

            $image->move(public_path('uploads/menu'), $imageName);

            $data['menu_image'] = $imageName;
        }

        $menu->update($data);

        return back()->with('success', '✅ Menu berhasil diperbarui');
    }

    public function destroy($id)
    {
        $menu = Menu::where('menu_id', $id)->first();

        if (!$menu) {
            return back()->with('error', '❌ Menu tidak ditemukan');
        }

        // Hapus gambar jika ada
        if ($menu->menu_image) {
            $imagePath = public_path('uploads/menu/' . $menu->menu_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $menu->delete();

        return back()->with('success', '✅ Menu berhasil dihapus!');
    }
}
