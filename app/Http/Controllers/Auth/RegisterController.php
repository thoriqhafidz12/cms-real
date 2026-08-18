<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class RegisterController extends BaseController
{
    public function __construct()
    {
        $this->route = 'register';
        $this->titlePage = 'Buat Akun Baru !';
        $this->model = User::class;
        $this->form = [
            [
                'name' => 'name',
                'label' => 'Nama Lengkap',
                'placeholder' => 'Masukkan nama lengkap',
                'type' => 'text',
                'col' => 'col-md-12',
                'required' => true,
            ],
            [
                'name' => 'email',
                'label' => 'Email',
                'placeholder' => 'Masukkan email',
                'type' => 'email',
                'col' => 'col-md-12',
                'required' => true,
                'unique' => 'users,email',
            ],
            [
                'name' => 'password',
                'label' => 'Password',
                'placeholder' => 'Minimal 4 karakter',
                'type' => 'password',
                'col' => 'col-md-12',
                'required' => true,
            ]
        ];
        $this->primaryKey = 'id';
        $this->validation = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:4',
        ];

    }
    public function index(): View
    {
        return view('register', [
            'route' => $this->route,
            'titlePage' => $this->titlePage,
            'form' => $this->form,
        ]);
    }

    /**
     * Simpan pendaftaran akun baru.
     * Berhasil -> redirect ke halaman login dengan notifikasi sukses.
     * Gagal -> kembali ke form pendaftaran dengan notifikasi error.
     */
    public function store(Request $request): RedirectResponse
    {
        $modelClass = $this->model;

        $validated = $request->validate(
            $this->buildValidationRules()
        );

        try {
            $modelClass::create($this->beforeSave($validated, null));
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Pendaftaran gagal. Silakan coba lagi.');
        }

        return redirect()
            ->route('login')
            ->with('status', 'Pendaftaran berhasil! Silakan login dengan akun Anda.');
    }

    protected function beforeSave(array $data, $record = null): array
    {
        // Hash password before saving
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        Validator::validate($data, $this->validation);


        $data['role'] = 2; // Set role to 2 (user) for new registrations

        return $data;
    }
}
