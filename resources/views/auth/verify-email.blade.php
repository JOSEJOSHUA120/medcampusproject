@extends('layouts.guest')

@section('title', 'Verifikasi Email - MEDCAMPUS')

@section('content')
<div class="text-center mb-6">
    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-green-50 mb-3">
        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
    </div>
    <h2 class="font-display font-bold text-xl text-gray-900">Verifikasi Email</h2>
    <p class="text-gray-500 text-sm mt-2 leading-relaxed">
        Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang telah kami kirimkan. Jika tidak menerima email, kami akan dengan senang hati mengirimkan ulang.
    </p>
</div>

@if (session('status') == 'verification-link-sent')
    <div class="alert-success mb-4">
        Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
    </div>
@endif

<div class="flex flex-col sm:flex-row items-center justify-between gap-4">
    <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
        @csrf
        <button type="submit" class="btn-primary w-full sm:w-auto">Kirim Ulang Email Verifikasi</button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline transition">Keluar</button>
    </form>
</div>
@endsection
