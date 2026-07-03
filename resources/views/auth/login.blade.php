@extends('layouts.app')

@section('title', 'Masuk - MEDCAMPUS')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-12 bg-gradient-to-br from-primary-50 via-white to-blue-50">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 px-8 py-10 border border-gray-100">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary-50 mb-4">
                    <svg class="w-7 h-7 text-primary-600" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4v6H4v4h6v6h4v-6h6v-4h-6V4h-4z"/></svg>
                </div>
                <h2 class="font-display font-extrabold text-2xl text-gray-900">MEDCAMPUS</h2>
                <p class="text-gray-500 mt-1">Selamat Datang Kembali</p>
            </div>

            @if (session('success'))
                <div class="alert-success mb-4">{{ session('success') }}</div>
            @endif
            @if (session('status'))
                <div class="alert-success mb-4">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input-custom @error('email') ring-2 ring-red-400 @enderror" value="{{ old('email') }}" required autofocus placeholder="Masukkan email Anda">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input-custom @error('password') ring-2 ring-red-400 @enderror" required placeholder="Masukkan password Anda">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" {{ old('remember') ? 'checked' : '' }}>
                        <span class="text-sm text-gray-600 select-none">Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary w-full">Masuk</button>
            </form>

            <p class="text-center mt-6 text-sm text-gray-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-primary-600 hover:text-primary-700 transition">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
