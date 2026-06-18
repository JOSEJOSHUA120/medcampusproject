@extends('layouts.app')

@section('title', 'Selamat Datang - MEDCAMPUS')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-gradient-to-br from-primary-50 to-blue-50">
    <div class="text-center max-w-lg mx-auto px-4">
        <div class="bg-white rounded-3xl shadow-xl p-10 border border-gray-100">
            <div class="w-20 h-20 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-primary-600" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4v6H4v4h6v6h4v-6h6v-4h-6V4h-4z"/></svg>
            </div>
            <h1 class="text-3xl font-extrabold font-display text-gray-900">MEDCAMPUS</h1>
            <p class="text-gray-500 mt-3">Klinik Digital - Solusi Kesehatan Modern</p>
            <hr class="my-6 border-gray-100">
            <div class="flex justify-center gap-3">
                <a href="{{ route('login') }}" class="btn-primary">Masuk</a>
                <a href="{{ route('register') }}" class="btn-secondary">Daftar</a>
            </div>
        </div>
    </div>
</div>
@endsection
