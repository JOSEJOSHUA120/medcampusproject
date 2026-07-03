@extends('layouts.guest')

@section('title', 'Reset Password - MEDCAMPUS')

@section('content')
<div class="text-center mb-6">
    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary-50 mb-3">
        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a4 4 0 11-8 0 4 4 0 018 0zm-4 6h4m-4 0v4m0-4h-4"/></svg>
    </div>
    <h2 class="font-display font-bold text-xl text-gray-900">Reset Password</h2>
    <p class="text-gray-500 text-sm mt-1">Buat password baru untuk akun Anda.</p>
</div>

<form method="POST" action="{{ route('password.store') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="mb-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-input-custom @error('email') ring-2 ring-red-400 @enderror" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="contoh@email.com">
        @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="form-label">Password Baru</label>
        <input type="password" name="password" class="form-input-custom @error('password') ring-2 ring-red-400 @enderror" required autocomplete="new-password" placeholder="Minimal 8 karakter">
        @error('password')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-input-custom" required autocomplete="new-password" placeholder="Ulangi password baru">
    </div>

    <button type="submit" class="btn-primary w-full">Reset Password</button>
</form>
@endsection
