<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower($request->email)]);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Password::min(8)->max(100)],
            'no_telp' => ['nullable', 'numeric', 'digits_between:10,15', 'regex:/^[0-9]+$/'],
            'alamat' => 'nullable|string|max:500',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'tempat_lahir' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'jenis_kelamin' => 'nullable|in:L,P',
        ], [
            'name.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi, tidak boleh mengandung angka.',
            'no_telp.regex' => 'Nomor telepon hanya boleh berisi angka, tidak boleh mengandung huruf.',
            'tempat_lahir.regex' => 'Tempat lahir tidak boleh mengandung angka.',
            'password.confirmed' => 'Password harus sama.',
            'password.min' => 'Password harus memiliki minimal 8 karakter.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pasien',
            'foto' => '/images/user.jpg',
        ]);

        Pasien::create([
            'user_id' => $user->id,
            'no_telp' => $request->no_telp ?: null,
            'alamat' => $request->alamat ?: null,
            'tanggal_lahir' => $request->tanggal_lahir ?: null,
            'tempat_lahir' => $request->tempat_lahir ?: null,
            'jenis_kelamin' => $request->jenis_kelamin ?: null,
            'foto' => '/images/user.jpg',
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('success', 'Anda berhasil membuat akun baru, silakan login.');
    }
}
