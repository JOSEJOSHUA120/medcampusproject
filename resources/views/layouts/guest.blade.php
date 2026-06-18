<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', config('app.name', 'MEDCAMPUS'))</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-primary-50 via-white to-blue-50 min-h-screen">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <div class="mb-6">
                <a href="/" class="flex items-center gap-2 font-display font-extrabold text-2xl text-primary-700">
                    <svg class="w-10 h-10 text-primary-600" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4v6H4v4h6v6h4v-6h6v-4h-6V4h-4z"/></svg>
                    MEDCAMPUS
                </a>
            </div>
            <div class="w-full sm:max-w-lg">
                <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 px-8 py-8 border border-gray-100">
                    @yield('content')
                </div>
            </div>
            <p class="mt-6 text-xs text-gray-400">&copy; {{ date('Y') }} MEDCAMPUS. All rights reserved.</p>
        </div>
    </body>
</html>
