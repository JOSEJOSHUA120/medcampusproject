<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Pasien') - MEDCAMPUS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <aside class="hidden lg:flex lg:flex-shrink-0">
            <div class="w-64 bg-gradient-to-b from-sky-800 via-sky-700 to-gray-900 flex flex-col">
                <div class="flex items-center gap-2 px-6 py-5 border-b border-white/10">
                    <svg class="w-8 h-8 text-sky-300 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4v6H4v4h6v6h4v-6h6v-4h-6V4h-4z"/></svg>
                    <div>
                        <h5 class="font-display font-bold text-white text-sm leading-tight">MEDCAMPUS</h5>
                        <small class="text-white/50 text-xs">Pasien Panel</small>
                    </div>
                </div>
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-thin">
                    <a href="{{ route('pasien.dashboard') }}" class="sidebar-link {{ request()->routeIs('pasien.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('pasien.booking') }}" class="sidebar-link {{ request()->routeIs('pasien.booking*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Booking Dokter
                    </a>
                    <a href="{{ route('pasien.riwayat-booking') }}" class="sidebar-link {{ request()->routeIs('pasien.riwayat-booking*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        Riwayat Booking
                    </a>
                    <a href="{{ route('pasien.rekam-medis') }}" class="sidebar-link {{ request()->routeIs('pasien.rekam-medis*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Rekam Medis
                    </a>
                    <a href="{{ route('pasien.pembayaran') }}" class="sidebar-link {{ request()->routeIs('pasien.pembayaran*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Pembayaran
                    </a>
                </nav>
                    <div class="px-3 py-4 border-t border-white/10">
                        <div class="px-3 mb-3 flex items-center gap-3">
                            <img src="{{ Auth::user()->foto ?? (Auth::user()->pasien->foto ?? 'https://i.pravatar.cc/300?u=' . urlencode(Auth::user()->email)) }}" alt="foto" class="w-10 h-10 rounded-full object-cover border-2 border-white/30">
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-white/50 truncate">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="sidebar-link w-full">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between lg:justify-end">
                <button class="lg:hidden p-2 rounded-lg hover:bg-gray-100" onclick="document.querySelector('aside').classList.toggle('hidden')">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-3">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-2 rounded-lg hover:bg-gray-100 transition">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @php $unread = Auth::user()->unreadNotifications->count(); @endphp
                            @if($unread > 0)
                            <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">{{ $unread > 9 ? '9+' : $unread }}</span>
                            @endif
                        </button>
                        <div x-show="open" @click.outside="open = false" class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto" style="display: none;">
                            <div class="p-3 border-b border-gray-100">
                                <h6 class="font-bold text-gray-800 text-sm">Notifikasi</h6>
                            </div>
                            @forelse(Auth::user()->notifications->take(10) as $notif)
                            <a href="{{ route('notifications.read', $notif->id) }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50 {{ is_null($notif->read_at) ? 'bg-primary-50/50' : '' }}">
                                <p class="text-sm text-gray-800">{{ $notif->data['message'] ?? 'Pesan baru' }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                            </a>
                            @empty
                            <p class="text-sm text-gray-400 text-center py-6">Belum ada notifikasi</p>
                            @endforelse
                            @if(Auth::user()->notifications->count() > 0)
                            <form action="{{ route('notifications.read-all') }}" method="POST" class="p-2">
                                @csrf
                                <button class="w-full text-center text-xs text-primary-600 font-semibold py-2 hover:bg-primary-50 rounded-lg">Tandai semua telah dibaca</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    <span class="text-sm text-gray-500 hidden sm:block">{{ Auth::user()->name }}</span>
                    <span class="w-8 h-8 rounded-full overflow-hidden border-2 border-sky-200 inline-block flex-shrink-0">
                        <img src="{{ Auth::user()->foto ?? (Auth::user()->pasien->foto ?? 'https://i.pravatar.cc/300?u=' . urlencode(Auth::user()->email)) }}" alt="foto" class="w-full h-full object-cover">
                    </span>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                @if (session('success'))
                <div class="alert-success flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
                @endif
                @if (session('error'))
                <div class="alert-error flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>
