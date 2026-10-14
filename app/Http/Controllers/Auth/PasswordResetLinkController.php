<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Tampilkan form "lupa kata sandi".
     */
    public function create(): View
    {
        return view('auth.lupa-sandi');
    }

    /**
     * Kirim link reset password ke email.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        // Akan selalu menampilkan pesan sukses (tidak membocorkan email terdaftar/tidak)
        Password::sendResetLink($request->only('email'));

        return back()->with(
            'sukses',
            'Jika email Anda terdaftar, tautan reset kata sandi telah dikirim. Silakan cek inbox.'
        );
    }
}
