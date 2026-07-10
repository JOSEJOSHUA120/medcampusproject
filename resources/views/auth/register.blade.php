@extends('layouts.app')

@section('title', 'Daftar - MEDCAMPUS')

@section('content')
<div class="min-h-screen relative flex items-center justify-center px-4 py-12 overflow-hidden">
    <div class="fixed inset-0 z-0">
        <img src="{{ asset('background%20login%20dan%20register.png') }}" class="w-full h-full object-cover" alt="">
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent"></div>
    </div>

    <div class="w-full max-w-2xl relative z-10">
        <div class="bg-black/50 backdrop-blur-xl rounded-3xl shadow-2xl shadow-black/40 px-8 py-10 border border-white/20">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-300 to-blue-500 shadow-lg shadow-blue-500/40 mb-4">
                    <svg class="w-7 h-7 text-white drop-shadow" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4v6H4v4h6v6h4v-6h6v-4h-6V4h-4z"/></svg>
                </div>
                <h2 class="font-display font-extrabold text-2xl text-white">MEDCAMPUS</h2>
                <p class="text-white/80 mt-1">Buat Akun Baru</p>
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 border border-white/20 text-white/90">Pendaftaran untuk Pasien</span>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400/70 focus:border-blue-400/70 transition-all @error('name') ring-2 ring-red-400/70 @enderror" value="{{ old('name') }}" required placeholder="Nama lengkap Anda">
                        @error('name')
                            <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1.5">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400/70 focus:border-blue-400/70 transition-all @error('email') ring-2 ring-red-400/70 @enderror" value="{{ old('email') }}" required placeholder="contoh@email.com">
                        @error('email')
                            <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1.5">No. Telepon</label>
                        <input type="text" name="no_telp" class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400/70 focus:border-blue-400/70 transition-all @error('no_telp') ring-2 ring-red-400/70 @enderror" value="{{ old('no_telp') }}" placeholder="08xxxxxxxxxx">
                        @error('no_telp')
                            <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1.5">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400/70 focus:border-blue-400/70 transition-all @error('tempat_lahir') ring-2 ring-red-400/70 @enderror" value="{{ old('tempat_lahir') }}" placeholder="Kota lahir">
                        @error('tempat_lahir')
                            <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1.5">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" max="{{ date('Y-m-d') }}" class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-blue-400/70 focus:border-blue-400/70 transition-all" value="{{ old('tanggal_lahir') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1.5">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-blue-400/70 focus:border-blue-400/70 transition-all">
                            <option value="" class="bg-gray-800 text-white/70">-- Pilih --</option>
                            <option value="L" @selected(old('jenis_kelamin')=='L') class="bg-gray-800 text-white">Laki-Laki</option>
                            <option value="P" @selected(old('jenis_kelamin')=='P') class="bg-gray-800 text-white">Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1.5">Password</label>
                        <input type="password" name="password" class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400/70 focus:border-blue-400/70 transition-all @error('password') ring-2 ring-red-400/70 @enderror" required placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-1.5">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400/70 focus:border-blue-400/70 transition-all" required placeholder="Ulangi password">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-white/90 mb-1.5">Alamat</label>
                        <textarea name="alamat" class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400/70 focus:border-blue-400/70 transition-all" rows="2" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="w-full mt-6 py-3 rounded-xl font-bold text-white transition-all duration-200 shadow-lg shadow-blue-500/50 bg-gradient-to-br from-blue-400 to-blue-600 hover:from-blue-300 hover:to-blue-500">Daftar</button>
            </form>

            <p class="text-center mt-6 text-sm text-white/70">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-blue-300 hover:text-white transition">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
