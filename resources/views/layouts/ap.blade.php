{{-- resources/views/layouts/ap.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @hasSection('title')
            @yield('title') - {{ config('app.name', 'PHARMA-ONLINE') }}
        @else
            {{ config('app.name', 'PHARMA-ONLINE') }}
        @endif
    </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50">
<div class="min-h-screen flex flex-col">
    <!-- Top bar -->
    <header class="border-b border-slate-200 bg-white/80 backdrop-blur">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                    PO
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="text-sm font-semibold tracking-tight text-slate-900">PHARMA-ONLINE</span>
                    <span class="text-[11px] text-slate-500 uppercase tracking-[0.16em]">Produits &amp; commandes</span>
                </div>
            </a>

            <div class="flex items-center gap-3 text-xs sm:text-sm">
                @auth
                    <span class="hidden sm:inline text-slate-500">
                        Connecté en tant que
                        <span class="font-medium text-slate-900">{{ auth()->user()->name }}</span>
                    </span>
                    @if (Route::has('dashboard'))
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-3 py-1.5 text-slate-700 hover:border-emerald-500 hover:text-emerald-700 transition text-xs sm:text-sm">
                            <span aria-hidden="true">←</span>
                            <span>Tableau de bord</span>
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="flex-1 py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @hasSection('header')
                <div>
                    @yield('header')
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-xs text-slate-400">
                &copy; {{ date('Y') }} PHARMA-ONLINE. Tous droits réservés.
            </p>
            <p class="text-[11px] text-slate-400">
                Optimisé pour la gestion des produits et des commandes en pharmacie.
            </p>
        </div>
    </footer>
</div>
</body>
</html>
