<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MEDCAMPUS') - Klinik Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans">
    <nav class="bg-primary-900 text-white sticky top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center gap-2 font-display font-extrabold text-xl tracking-tight">
                    <svg class="w-8 h-8 text-primary-300" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4v6H4v4h6v6h4v-6h6v-4h-6V4h-4z"/></svg>
                    MEDCAMPUS
                </a>
                <div class="flex items-center gap-1 sm:gap-2">
                    <a href="/" class="px-3 py-2 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition">Beranda</a>
                    @guest
                    <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg text-sm font-semibold bg-white text-primary-700 hover:bg-primary-50 transition shadow-sm">Daftar</a>
                    @endguest
                    @auth
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-2 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition">Keluar</button>
                    </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>@yield('content')</main>

    <footer class="bg-gray-900 text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 font-display font-extrabold text-xl text-white mb-3">
                        <svg class="w-7 h-7 text-primary-400" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4v6H4v4h6v6h4v-6h6v-4h-6V4h-4z"/></svg>
                        MEDCAMPUS
                    </div>
                    <p class="text-sm leading-relaxed">Klinik Digital - Solusi Kesehatan Modern untuk Anda. Kelola kesehatan dengan mudah melalui platform digital kami.</p>
                </div>
                <div>
                    <h5 class="font-semibold text-white mb-3">Menu</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Masuk</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Daftar</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold text-white mb-3">Kontak</h5>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> info@medcampus.com</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> +62 123 4567 890</li>
                    </ul>
                </div>
            </div>
            <hr class="border-gray-800 my-8">
            <p class="text-center text-sm">&copy; {{ date('Y') }} MEDCAMPUS. All rights reserved.</p>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
