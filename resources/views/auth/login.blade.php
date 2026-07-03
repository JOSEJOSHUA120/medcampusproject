@extends('layouts.app')

@section('title', 'Masuk - MEDCAMPUS')

@section('content')
<div class="min-h-screen relative flex items-center justify-center px-4 py-12 overflow-hidden">
    <div class="fixed inset-0 z-0">
        <img src="{{ asset('background%20login%20dan%20register.png') }}" class="w-full h-full object-cover" alt="">
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        <div class="bg-black/50 backdrop-blur-xl rounded-3xl shadow-2xl shadow-black/40 px-8 py-10 border border-white/20">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-300 to-blue-500 shadow-lg shadow-blue-400/40 mb-4">
                    <svg class="w-7 h-7 text-white drop-shadow" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4v6H4v4h6v6h4v-6h6v-4h-6V4h-4z"/></svg>
                </div>
                <h2 class="font-display font-extrabold text-2xl text-white">MEDCAMPUS</h2>
                <p class="text-white/80 mt-1">Selamat Datang Kembali</p>
            </div>

            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded-xl bg-green-500/20 border border-green-400/30 text-green-200 text-sm">{{ session('success') }}</div>
            @endif
            @if (session('status'))
                <div class="mb-4 px-4 py-3 rounded-xl bg-blue-500/20 border border-blue-400/30 text-blue-200 text-sm">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-white/90 mb-1.5">Email</label>
                    <input type="email" name="email" class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400/70 focus:border-blue-400/70 transition-all @error('email') ring-2 ring-red-400/70 @enderror" value="{{ old('email') }}" required autofocus placeholder="Masukkan email Anda">
                    @error('email')
                        <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-white/90 mb-1.5">Password</label>
                    <input type="password" name="password" class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400/70 focus:border-blue-400/70 transition-all @error('password') ring-2 ring-red-400/70 @enderror" required placeholder="Masukkan password Anda">
                    @error('password')
                        <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" class="h-4 w-4 rounded border-white/30 bg-white/10 text-blue-400 focus:ring-blue-400/60">
                        <span class="text-sm text-white/80 select-none">Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl font-bold text-white transition-all duration-200 shadow-lg shadow-blue-500/50 bg-gradient-to-br from-blue-400 to-blue-600 hover:from-blue-300 hover:to-blue-500">Masuk</button>
            </form>

            <p class="text-center mt-6 text-sm text-white/70">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-blue-300 hover:text-white transition">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
