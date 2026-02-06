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
            'meja_kapasitas' => 'required|integer|min:1|max:20'
        ], [
            'meja_nama.required' => 'Nama meja wajib diisi',
            'meja_nama.unique' => 'Nama meja sudah digunakan',
            'meja_kapasitas.required' => 'Kapasitas meja wajib diisi',
            'meja_kapasitas.min' => 'Kapasitas minimal 1 orang',
            'meja_kapasitas.max' => 'Kapasitas maksimal 20 orang'
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
            'meja_status' => $request->meja_status,
            'updated_at' => now()
        ]);

        return back();
    }

    public function toggle($id)
    {
        $meja = Meja::findOrFail($id);

        $newStatus = $meja->meja_status === 'kosong' ? 'terisi' : 'kosong';

        $meja->update([
            'meja_status' => $newStatus,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => true,
            'meja_status' => $newStatus
        ]);
    }

    public function destroy($id)
    {
        $meja = Meja::findOrFail($id);

        // Optional: larang hapus meja terisi
        if ($meja->meja_status === 'terisi') {
            return response()->json([
                'status' => false,
                'message' => 'Tidak dapat menghapus meja yang sedang digunakan'
            ], 422);
        }

        $meja->delete();

        return response()->json([
            'status' => true,
            'message' => 'Meja berhasil dihapus'
        ]);
    }

    /**
     * Reset semua meja menjadi kosong
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetAll()
    {
        try {
            // Hitung berapa meja yang terisi
            $terisiCount = Meja::where('meja_status', 'terisi')->count();

            if ($terisiCount === 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada meja yang perlu direset'
                ]);
            }

            // Update semua meja terisi menjadi kosong
            $updated = Meja::where('meja_status', 'terisi')
                ->update([
                    'meja_status' => 'kosong',
                    'updated_at' => now()
                ]);

            return response()->json([
                'status' => true,
                'message' => "Berhasil mereset {$updated} meja menjadi kosong",
                'updated_count' => $updated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mereset meja: ' . $e->getMessage()
            ], 500);
        }
    }
}
