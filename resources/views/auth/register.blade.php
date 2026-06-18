@extends('layouts.app')

@section('title', 'Daftar - MEDCAMPUS')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-12 bg-gradient-to-br from-primary-50 via-white to-blue-50">
    <div class="w-full max-w-2xl">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 px-8 py-10 border border-gray-100">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary-50 mb-4">
                    <svg class="w-7 h-7 text-primary-600" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4v6H4v4h6v6h4v-6h6v-4h-6V4h-4z"/></svg>
                </div>
                <h2 class="font-display font-extrabold text-2xl text-gray-900">MEDCAMPUS</h2>
                <p class="text-gray-500 mt-1">Buat Akun Baru</p>
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-primary-50 text-primary-700">Pendaftaran untuk Pasien</span>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-input-custom @error('name') ring-2 ring-red-400 @enderror" value="{{ old('name') }}" required placeholder="Nama lengkap Anda">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input-custom @error('email') ring-2 ring-red-400 @enderror" value="{{ old('email') }}" required placeholder="contoh@email.com">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="no_telp" class="form-input-custom" value="{{ old('no_telp') }}" placeholder="08xxxxxxxxxx">
                    </div>

                    <div>
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-input-custom" value="{{ old('tanggal_lahir') }}">
                    </div>

                    <div>
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select-custom">
                            <option value="">-- Pilih --</option>
                            <option value="L" @selected(old('jenis_kelamin')=='L')>Laki-Laki</option>
                            <option value="P" @selected(old('jenis_kelamin')=='P')>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input-custom @error('password') ring-2 ring-red-400 @enderror" required placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-input-custom" required placeholder="Ulangi password">
                    </div>

                    <div class="md:col-span-2">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-input-custom" rows="2" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full mt-6">Daftar</button>
            </form>

            <p class="text-center mt-6 text-sm text-gray-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-primary-600 hover:text-primary-700 transition">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
