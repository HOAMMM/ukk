<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class StaffWaiterController extends Controller
{
    public function index()
    {
        $staff = User::where('id_level', 2)->get(); // 2 = waiter
        return view('dashboard.staff-waiter', compact('staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:tb_users',
            'namleng'  => 'required',
            'user_phone'  => 'required|numeric',
            'email'    => 'required|email|unique:tb_users',
            'password' => 'required|min:6|confirmed'
        ], [
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'namleng.required' => 'Nama lengkap wajib diisi',
            'user_phone.required' => 'No HP / WA wajib diisi',
            'user_phone.numeric' => 'No HP / WA harus berupa angka',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);

        User::create([
            'username' => $request->username,
            'namleng'  => $request->namleng,
            'email'    => $request->email,
            'user_phone' => $request->user_phone,
            'password' => Hash::make($request->password),
            'user_code' => 'WTR' . strtoupper(uniqid()),
            'user_passtext' => $request->password,
            'remember_token' => null,
            'id_level' => 2,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Staff waiter berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        // =========================
        // VALIDASI DATA UTAMA
        // =========================
        $request->validate([
            'username'    => 'required|unique:tb_users,username,' . $id,
            'namleng'     => 'required',
            'user_phone'  => 'required|numeric',
            'email'       => 'required|email|unique:tb_users,email,' . $id,
            'password'    => 'nullable|min:6|confirmed',
        ], [
            'username.unique' => 'Username sudah digunakan',
            'email.unique'    => 'Email sudah digunakan',
            'password.min'    => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = User::findOrFail($id);

        // =========================
        // DATA YANG DIUPDATE
        // =========================
        $data = [
            'username'   => $request->username,
            'namleng'    => $request->namleng,
            'email'      => $request->email,
            'user_phone' => $request->user_phone,
        ];

        // =========================
        // JIKA PASSWORD DIISI
        // =========================
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);

            // ⚠️ SANGAT TIDAK DISARANKAN
            // $data['user_passtext'] = $request->password;
        }

        // =========================
        // UPDATE KE DATABASE
        // =========================
        $user->update($data);

        return back()->with('success', 'Data staff berhasil diperbarui');
    }



    public function show($id)
    {
        $staff = User::where('id', $id)->where('id_level', 3)->firstOrFail();
        return view('dashboard.staff-waiter-show', compact('staff'));
    }

    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return back()->with('success', 'Staff berhasil dihapus');
    }
}
