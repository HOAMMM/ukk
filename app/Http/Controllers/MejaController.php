<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Http\Request;

class MejaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $mejas = Meja::query()
            ->when($search, function ($query, $search) {
                return $query->where('meja_nama', 'like', "%{$search}%");
                // ->orWhere('meja_kapasitas', 'like', "%{$search}%")
                // ->orWhere('meja_status', 'like', "%{$search}%");
            })
            ->orderBy('meja_nama')
            ->paginate($perPage)
            ->withQueryString();

        // Jika request AJAX, return HTML table saja
        if ($request->ajax()) {
            $html = '';

            if ($mejas->count() > 0) {
                $html .= '<table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Meja</th>
                            <th>Status</th>
                            <th>Kapasitas</th>
                            <th>Toggle Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="mejaTableBody">';

                foreach ($mejas as $index => $meja) {
                    $no = ($mejas->currentPage() - 1) * $mejas->perPage() + $index + 1;
                    $statusBadge = $meja->meja_status == 'terisi' ? 'bg-danger' : 'bg-success';
                    $checked = $meja->meja_status == 'terisi' ? 'checked' : '';

                    $html .= '<tr class="meja-row">
                        <td>' . $no . '</td>
                        <td><strong>' . e($meja->meja_nama) . '</strong></td>
                        <td>
                            <span class="status-badge ' . $statusBadge . '">
                                ' . strtoupper($meja->meja_status) . '
                            </span>
                        </td>
                        <td>
                            <span class="capacity-badge">
                                ' . $meja->meja_kapasitas . ' Orang
                            </span>
                        </td>
                        <td>
                            <label class="toggle-switch">
                                <input type="checkbox"
                                    class="toggle-meja"
                                    data-id="' . $meja->meja_id . '"
                                    ' . $checked . '>
                                <span class="toggle-slider"></span>
                            </label>
                        </td>
                        <td>
                            <button class="btn-action btn-delete"
                                data-id="' . $meja->meja_id . '"
                                data-nama="' . e($meja->meja_nama) . '">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>';
                }

                $html .= '</tbody></table>';

                // Pagination
                $html .= '<div class="pagination-wrapper">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3">
                        <div class="text-muted">
                            Menampilkan ' . $mejas->count() . ' dari ' . $mejas->total() . ' meja
                        </div>
                        <div>
                            ' . $mejas->links('pagination::bootstrap-5')->toHtml() . '
                        </div>
                    </div>
                </div>';
            } else {
                // Empty state
                $searchText = $search ? 'Tidak ada meja yang sesuai dengan pencarian "<strong>' . e($search) . '</strong>"' : 'Belum ada meja yang terdaftar';

                $html .= '<div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h5 class="mt-3 mb-2">Tidak Ada Data</h5>
                    <p class="text-muted mb-0">' . $searchText . '</p>
                </div>';
            }

            return response($html);
        }

        // Jika request normal, return full page
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
