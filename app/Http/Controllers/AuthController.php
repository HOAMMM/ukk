<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // 👈 TAMBAHKAN INI

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Username wajib diisi!!',
            'password.required' => 'Password wajib diisi!!',
        ]);

        // 👇 CEK APAKAH USERNAME ADA DI DATABASE
        $user = User::where('username', $request->username)->first();

        if (!$user) {
            // 👇 USERNAME TIDAK DITEMUKAN
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Username tidak ditemukan!');
        }

        // 👇 USERNAME ADA, CEK PASSWORD
        if (Auth::attempt($request->only('username', 'password'))) {
            $request->session()->regenerate();
            $request->session()->put('clear_history', true); // 👈 INI PENTING!

            $request->session()->put('prevent_back', true);

            return redirect()->route('dashboard');
        }

        // 👇 USERNAME BENAR, PASSWORD SALAH
        return back()
            ->withInput($request->only('username'))
            ->with('error', 'Password salah!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget('prevent_back');

        return redirect('/login')->with('logout', true);
    }
}
