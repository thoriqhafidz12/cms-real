<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GantiPassController extends Controller
{
    public function __construct()
    {
        $this->route = 'ganti-password';
        $this->titlePage = 'Ganti Password';

        $this->form = [
            [
                'name' => 'name',
                'label' => 'Nama Lengkap',
                'type' => 'text',
                'col' => 'col-md-12',
                'readonly' => true,
            ],
            [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'col' => 'col-md-12',
                'readonly' => true,
            ],
            [
                'name' => 'current_password',
                'label' => 'Password Saat Ini',
                'placeholder' => 'Masukkan password saat ini',
                'type' => 'password',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'new_password',
                'label' => 'Password Baru',
                'placeholder' => 'Minimal 4 karakter',
                'type' => 'password',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'new_password_confirmation',
                'label' => 'Konfirmasi Password Baru',
                'placeholder' => 'Ulangi password baru',
                'type' => 'password',
                'col' => 'col-md-12',
                'required' => true,
            ],
        ];
    }

    /**
     * Tampilkan form ganti password dengan data user yang sedang login.
     */
    public function index(): View
    {
        $editData = auth()->user();

        return view('ganti-password', [
            'editData'  => $editData,
            'form'      => $this->form,
            'route'     => $this->route,
            'primaryKey' => 'id',
            'titlePage' => $this->titlePage,
            'action'    => route('ganti-password.update'),
            'submitLabel' => 'Ganti Password',
        ]);
    }

    /**
     * Proses ganti password.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password'          => 'required',
            'new_password'              => 'required|min:4|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required'     => 'Password baru wajib diisi.',
            'new_password.min'          => 'Password baru minimal 4 karakter.',
            'new_password.confirmed'    => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password saat ini tidak cocok.');
        }

        $user->password = $request->new_password;
        $user->save();

        return back()->with('success', 'Password berhasil diubah.');
    }
}
