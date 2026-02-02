<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::orderBy('kategori_id', 'desc')->get();
        return view('dashboard.kategori-produk', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_name' => 'required',
        ], [
            'kategori_name.required' => 'Nama kategori wajib diisi',
        ]);

        Kategori::create([
            'kategori_name' => $request->kategori_name,
            'kategori_code' => strtoupper(substr($request->kategori_name, 0, 3)) . rand(100, 999),
            'created_at' => now(),
            'update_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');
    }
}
