<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class StaffWaiterController extends Controller
{
    public function index()
    {
        $staff = User::where('id_level', operator: 2)->get(); // 3 = Waiter
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
            'user_code' => 'KSR' . strtoupper(uniqid()),
            'user_passtext' => $request->password,
            'remember_token' => null,
            'id_level' => 3,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Staff Waiter berhasil ditambahkan');
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Buat validator manual
        $validator = Validator::make($request->all(), [
            'username'   => 'required|unique:tb_users,username,' . $id,
            'namleng'    => 'required',
            'user_phone' => 'required|numeric',
            'email'      => 'required|email|unique:tb_users,email,' . $id,
            'password'   => 'nullable|min:6|confirmed',
        ], [
            'username.unique' => 'Username sudah digunakan',
            'email.unique'    => 'Email sudah digunakan',
            'password.min'    => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Validasi tambahan password lama
        $validator->after(function ($validator) use ($request, $user) {

            // Jika user mengisi password lama
            if ($request->filled('current_password')) {

                // Password lama salah
                if (!Hash::check($request->current_password, $user->password)) {
                    $validator->errors()->add('current_password', 'Password lama tidak cocok.');
                }

                // Password baru wajib diisi
                if (!$request->filled('password')) {
                    $validator->errors()->add('password', 'Password baru wajib diisi.');
                }

                // Konfirmasi password wajib diisi
                if (!$request->filled('password_confirmation')) {
                    $validator->errors()->add('password_confirmation', 'Konfirmasi password wajib diisi.');
                }
            }

            // Jika password baru diisi tapi password lama kosong
            if ($request->filled('password') && !$request->filled('current_password')) {
                $validator->errors()->add('current_password', 'Password lama wajib diisi.');
            }
        });


        // Jika validasi gagal, redirect kembali + input + edit_id
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal', 'edit')
                ->with('edit_id', $id);
            // ⚡ ini dipakai di blade untuk buka modal edit otomatis
        }

        // =========================
        // DATA YANG DIUPDATE
        // =========================
        $data = [
            'username'   => $request->username,
            'namleng'    => $request->namleng,
            'email'      => $request->email,
            'user_phone' => $request->user_phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);  // password hash
            $data['user_passtext'] = $request->password;         // simpan password asli
        }

        $user->update($data);


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
