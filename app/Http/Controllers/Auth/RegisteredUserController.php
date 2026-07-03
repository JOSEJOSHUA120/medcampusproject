<?php

namespace App\Http\Controllers\Auth;

// MVC Pattern: Controller adalah "C" dalam MVC — menangani logika request/response
// Inheritance: RegisteredUserController mewarisi class Controller (Base Controller)
use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan form registrasi (HTTP GET)
     * OOP: Method ini mengembalikan View object (Polymorphism via Laravel View)
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Memproses pendaftaran user baru (HTTP POST)
     * OOP Concepts:
     * - Dependency Injection: Request object di-inject langsung ke method
     * - Encapsulation: Validasi data sebelum diproses
     * - Inheritance: User::create() menggunakan metode dari Eloquent Model
     * - Composition: User dan Pasien adalah komposisi terpisah (satu user = satu pasien)
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation: Memastikan data sesuai aturan sebelum diproses
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'no_telp' => 'nullable|numeric|digits_between:10,15',
            'alamat' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);

        // Encapsulation: Hash::make() meng-enkripsi password (one-way hashing)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pasien',
        ]);

        // Composition: Membuat data Pasien yang terhubung dengan User baru
        Pasien::create([
            'user_id' => $user->id,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        // Event: Memicu event Registered untuk fungsionalitas tambahan (e.g., verifikasi email)
        event(new Registered($user));

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Anda berhasil membuat akun baru, silakan login.');
    }
}
