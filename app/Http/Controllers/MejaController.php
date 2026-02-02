<?php

namespace App\Http\Controllers;


use App\Models\Meja;
use Illuminate\Http\Request;


class MejaController extends Controller
{
    public function index()
    {
        $mejas = Meja::orderBy('meja_nama')->get();
        return view('dashboard.meja.index', compact('mejas'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'meja_nama' => 'required|unique:tb_meja,meja_nama',
            'meja_kapasitas' => 'required|integer|min:1'
        ]);


        Meja::create([
            'meja_nama' => $request->meja_nama,
            'meja_kapasitas' => $request->meja_kapasitas,
            'meja_status' => 'kosong',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return redirect()->back()->with('success', 'Meja berhasil ditambahkan!');
    }


    public function update(Request $request, $id)
    {
        Meja::where('meja_id', $id)->update([
            'meja_status' => $request->meja_status
        ]);


        return back();
    }

    public function toggle($id)
    {
        $meja = Meja::findOrFail($id);

        $meja->update([
            'meja_status' => $meja->meja_status === 'kosong'
                ? 'terisi'
                : 'kosong'
        ]);

        return response()->json([
            'status' => true,
            'meja_status' => $meja->meja_status
        ]);
    }




    public function destroy($id)
    {
        $meja = Meja::findOrFail($id);

        // Optional: larang hapus meja terisi
        if ($meja->meja_status === 'terisi') {
            return response()->json([
                'status' => false,
                'message' => 'Meja sedang digunakan'
            ], 422);
        }

        $meja->delete();

        return response()->json([
            'status' => true,
            'message' => 'Meja berhasil dihapus'
        ]);
    }
}
