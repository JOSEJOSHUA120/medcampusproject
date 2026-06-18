@extends('layouts.app')

@section('title', 'MEDCAMPUS - Klinik Digital')

@section('content')
<section class="bg-gradient-to-r from-primary-600 via-primary-700 to-primary-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div>
                <h1 class="text-4xl lg:text-5xl font-extrabold font-display leading-tight">Solusi Kesehatan Digital untuk Anda</h1>
                <p class="text-lg lg:text-xl text-white/80 mt-4 mb-8 leading-relaxed">Daftar antrian online, konsultasi dengan dokter, dan kelola rekam medis Anda dengan mudah.</p>
                @guest
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="bg-white text-primary-700 hover:bg-primary-50 font-semibold rounded-xl px-6 py-3 transition-all duration-200 shadow-lg hover:shadow-xl">Daftar Sekarang</a>
                    <a href="{{ route('login') }}" class="border-2 border-white/30 text-white hover:bg-white/10 rounded-xl px-6 py-3 font-semibold transition-all duration-200">Masuk</a>
                </div>
                @else
                <a href="{{ route('dashboard') }}" class="bg-white text-primary-700 hover:bg-primary-50 font-semibold rounded-xl px-6 py-3 transition-all duration-200 shadow-lg hover:shadow-xl inline-block">Ke Dashboard</a>
                @endguest
            </div>
            <div class="text-center mt-8 lg:mt-0">
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl border border-white/20 p-6 inline-block">
                    <h5 class="font-bold font-display text-lg">Antrian Hari Ini</h5>
                    <div class="flex justify-center gap-6 mt-4">
                        <div class="text-center">
                            <div class="text-4xl font-extrabold">{{ \App\Models\Antrian::whereDate('tanggal_antrian', today())->count() }}</div>
                            <small class="text-white/70 text-xs font-medium uppercase tracking-wider">Total</small>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-extrabold">{{ \App\Models\Antrian::whereDate('tanggal_antrian', today())->where('status','menunggu')->count() }}</div>
                            <small class="text-white/70 text-xs font-medium uppercase tracking-wider">Menunggu</small>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-extrabold">{{ \App\Models\Dokter::count() }}</div>
                            <small class="text-white/70 text-xs font-medium uppercase tracking-wider">Dokter</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center hover:shadow-md transition-all duration-200">
                <div class="text-3xl font-bold text-primary-600 font-display">{{ \App\Models\Pasien::count() }}</div>
                <div class="text-sm text-gray-500 font-semibold uppercase tracking-wider mt-1">Pasien Terdaftar</div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center hover:shadow-md transition-all duration-200">
                <div class="text-3xl font-bold text-primary-600 font-display">{{ \App\Models\Dokter::count() }}</div>
                <div class="text-sm text-gray-500 font-semibold uppercase tracking-wider mt-1">Dokter Siap</div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center hover:shadow-md transition-all duration-200">
                <div class="text-3xl font-bold text-primary-600 font-display">{{ \App\Models\Antrian::count() }}</div>
                <div class="text-sm text-gray-500 font-semibold uppercase tracking-wider mt-1">Antrian Diproses</div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center hover:shadow-md transition-all duration-200">
                <div class="text-3xl font-bold text-primary-600 font-display">{{ \App\Models\RekamMedis::count() }}</div>
                <div class="text-sm text-gray-500 font-semibold uppercase tracking-wider mt-1">Rekam Medis</div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-extrabold font-display mb-12">Fitur Kami</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100 hover:shadow-lg transition-all duration-200">
                <div class="text-5xl mb-4">📋</div>
                <h5 class="text-lg font-bold font-display mb-2">Antrian Online</h5>
                <p class="text-gray-500 text-sm leading-relaxed mb-0">Ambil nomor antrian dari mana saja tanpa perlu datang langsung.</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100 hover:shadow-lg transition-all duration-200">
                <div class="text-5xl mb-4">💊</div>
                <h5 class="text-lg font-bold font-display mb-2">Rekam Medis Digital</h5>
                <p class="text-gray-500 text-sm leading-relaxed mb-0">Akses riwayat kesehatan Anda kapan saja secara digital.</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100 hover:shadow-lg transition-all duration-200">
                <div class="text-5xl mb-4">💳</div>
                <h5 class="text-lg font-bold font-display mb-2">Pembayaran Mudah</h5>
                <p class="text-gray-500 text-sm leading-relaxed mb-0">Bayar biaya pemeriksaan dengan berbagai metode pembayaran.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-gradient-to-r from-primary-600 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-extrabold font-display">Siap Menggunakan MEDCAMPUS?</h2>
        <p class="text-white/80 text-lg mt-3 mb-6">Daftar sekarang dan nikmati kemudahan layanan kesehatan digital.</p>
        @guest
        <a href="{{ route('register') }}" class="bg-white text-primary-700 hover:bg-primary-50 font-semibold rounded-xl px-8 py-3.5 inline-block transition-all duration-200 shadow-lg hover:shadow-xl">Daftar Gratis</a>
        @endguest
    </div>
</section>
@endsection
