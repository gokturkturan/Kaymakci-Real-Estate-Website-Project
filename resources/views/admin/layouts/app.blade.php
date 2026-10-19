<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - Kaymakci Real Estate</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        {{-- Fallback while assets aren't built yet (no `npm run build` / `npm run dev`): Tailwind Play CDN with the same theme tokens --}}
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                            serif: ['Playfair Display', 'Iowan Old Style', 'Georgia', 'ui-serif', 'serif'],
                        },
                        colors: {
                            ivory: { 50:'#fdfbf7', 100:'#faf6ee', 200:'#f3ead7', 300:'#e6d6b8', 400:'#cdb690', 500:'#ab9273', 600:'#8a7560', 700:'#6b5c4d', 800:'#46392f', 900:'#241d19', 950:'#14100d' },
                            gold: { 50:'#fbf1e6', 100:'#f5e1c6', 200:'#eac28c', 300:'#dda35a', 400:'#cb8a3e', 500:'#b8732e', 600:'#995f26', 700:'#7a4b1f', 800:'#5c3917', 900:'#3e270f' },
                            navy: { 50:'#eff1fb', 100:'#dee4f7', 200:'#b9c5ee', 300:'#8499e1', 400:'#4766d2', 500:'#2a47ac', 600:'#203683', 700:'#192b66', 800:'#121f4a', 900:'#0d1635', 950:'#080e21' },
                        },
                        boxShadow: {
                            soft: '0 1px 2px rgba(13,22,53,0.05), 0 8px 24px -8px rgba(13,22,53,0.14)',
                            lift: '0 2px 4px rgba(13,22,53,0.06), 0 20px 48px -14px rgba(13,22,53,0.22)',
                        },
                    },
                },
            };
        </script>
    @endif
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important;}</style>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>
<body class="bg-ivory-100 text-ivory-900 font-sans min-h-screen antialiased" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
    <div class="flex min-h-screen">
        <!-- Mobile overlay -->
        <div x-show="sidebarOpen" x-cloak x-transition.opacity
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-navy-950/60 z-30 lg:hidden"></div>

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-navy-900 to-navy-950 text-ivory-300 flex-shrink-0 z-40 transform transition-transform duration-200 -translate-x-full lg:static lg:translate-x-0 overflow-y-auto"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="p-4 border-b border-navy-800/60 flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-auto bg-ivory-50 rounded p-1">
                    <span class="font-serif font-semibold text-ivory-50 tracking-tight">Admin Panel</span>
                </a>
                <button type="button" @click="sidebarOpen = false" class="lg:hidden p-1 text-ivory-400 hover:text-ivory-50" aria-label="Menü schließen">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <nav class="p-4 space-y-1.5">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-gold-600 text-ivory-50' : 'text-ivory-300 hover:bg-navy-800 hover:text-ivory-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.properties.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.properties.*') ? 'bg-gold-600 text-ivory-50' : 'text-ivory-300 hover:bg-navy-800 hover:text-ivory-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Immobilien
                </a>
                <a href="{{ route('admin.calendar.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.calendar.*') ? 'bg-gold-600 text-ivory-50' : 'text-ivory-300 hover:bg-navy-800 hover:text-ivory-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2zM9 15h.01M12 15h.01M15 15h.01M9 17.5h.01"/>
                    </svg>
                    Kalender
                </a>
                <a href="{{ route('admin.bookings.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors relative {{ request()->routeIs('admin.bookings.*') ? 'bg-gold-600 text-ivory-50' : 'text-ivory-300 hover:bg-navy-800 hover:text-ivory-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Buchungen
                    @php
                        $pendingCount = \App\Models\Booking::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 bg-gold-400 text-navy-950 text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
                <hr class="border-navy-800/60 my-4">
                <a href="{{ route('properties.index') }}" target="_blank"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-ivory-300 hover:bg-navy-800 hover:text-ivory-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Website ansehen
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Bar -->
            <header class="bg-ivory-50 border-b border-ivory-200 px-4 sm:px-6 py-4 flex justify-between items-center gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-lg text-ivory-600 hover:bg-ivory-100 flex-shrink-0" aria-label="Menü öffnen">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h1 class="font-serif text-lg sm:text-xl font-semibold text-ivory-900 truncate">@yield('header', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
                    <span class="text-ivory-600 hidden sm:inline text-sm">{{ Auth::user()->name }}</span>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm sm:text-base text-gold-700 hover:text-gold-800 font-medium transition-colors">
                            Abmelden
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6">
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
