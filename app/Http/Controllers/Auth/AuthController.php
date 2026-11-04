<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('kasir.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $authAttempt = [
            $loginType => $credentials['login'],
            'password' => $credentials['password'],
            'is_active' => true,
        ];

        if (Auth::attempt($authAttempt, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            AktivitasLog::catat(
                'LOGIN',
                "Pengguna {$user->name} ({$user->role}) berhasil masuk ke sistem.",
                $user
            );

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'))->with('success', "Selamat datang kembali, {$user->name}!");
            }
            return redirect()->intended(route('kasir.dashboard'))->with('success', "Selamat bertugas, {$user->name}!");
        }

        return back()->withErrors([
            'login' => 'Kombinasi nama pengguna/email dan kata sandi salah, atau akun nonaktif.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            AktivitasLog::catat(
                'LOGOUT',
                "Pengguna {$user->name} keluar dari sistem.",
                $user
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
