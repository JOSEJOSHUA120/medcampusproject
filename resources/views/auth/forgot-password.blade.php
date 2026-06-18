@extends('layouts.guest')

@section('title', 'Lupa Password - MEDCAMPUS')

@section('content')
<div class="text-center mb-6">
    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-amber-50 mb-3">
        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m9.364-7.364A9 9 0 1112 3a9 9 0 017.364 4.636z"/></svg>
    </div>
    <h2 class="font-display font-bold text-xl text-gray-900">Lupa Password?</h2>
    <p class="text-gray-500 text-sm mt-1">Masukkan email Anda dan kami akan mengirimkan tautan reset password.</p>
</div>

@if (session('status'))
    <div class="alert-success mb-4">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-input-custom @error('email') ring-2 ring-red-400 @enderror" value="{{ old('email') }}" required autofocus placeholder="contoh@email.com">
        @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn-primary w-full">Kirim Tautan Reset Password</button>
</form>

<p class="text-center mt-6 text-sm text-gray-500">
    <a href="{{ route('login') }}" class="font-semibold text-primary-600 hover:text-primary-700 transition">Kembali ke halaman masuk</a>
</p>
@endsection
