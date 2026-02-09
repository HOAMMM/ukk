<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $user = Auth::user();
        return view('dashboard.pengaturan', compact('user'));
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'namleng' => 'required|string|max:255',
            'email' => 'required|email|unique:tb_users,email,' . $user->id,
            'user_phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'namleng.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'avatar.image' => 'File harus berupa gambar',
            'avatar.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'avatar.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        $data = [
            'namleng' => $request->namleng,
            'email' => $request->email,
            'user_phone' => $request->user_phone,
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            $avatar = $request->file('avatar');
            $avatarName = 'avatar_' . $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->storeAs('avatars', $avatarName, 'public');
            $data['avatar'] = $avatarName;
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi',
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai'])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
            'user_passtext' => $request->password, // Store plain text if needed
        ]);

        return back()->with('success', 'Password berhasil diperbarui');
    }

    /**
     * Update theme preferences
     */
    // public function updatePreferences(Request $request)
    // {
    //     $user = Auth::user();

    //     $request->validate([
    //         'theme' => 'required|in:light,dark',
    //     ]);

    //     $user->update([
    //         'theme' => $request->theme,
    //     ]);

    //     return back()->with('success', 'Tema berhasil diperbarui');
    // }
}
