@extends('layouts.app')

@section('title', 'Dashboard - MEDCAMPUS')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-gray-50">
    <div class="max-w-lg w-full mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h1 class="text-2xl font-bold font-display text-gray-900">Selamat Datang!</h1>
            <p class="text-gray-500 mt-2">{{ __("You're logged in!") }}</p>
            <hr class="my-6 border-gray-100">
            <div class="space-y-3">
                @php
                    $role = Auth::user()->role;
                @endphp
                @if($role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn-primary block text-center">Ke Dashboard Admin</a>
                @elseif($role === 'dokter')
                    <a href="{{ route('dokter.dashboard') }}" class="btn-primary block text-center">Ke Dashboard Dokter</a>
                @elseif($role === 'pasien')
                    <a href="{{ route('pasien.dashboard') }}" class="btn-primary block text-center">Ke Dashboard Pasien</a>
                @else
                    <a href="{{ route('landing') }}" class="btn-primary block text-center">Ke Halaman Utama</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
