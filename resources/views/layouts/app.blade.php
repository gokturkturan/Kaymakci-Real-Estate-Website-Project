@php
    // Locales offered in the language switcher: native name + ISO country code for the flag icon.
    $siteLocales = [
        'de' => ['name' => 'Deutsch', 'flag' => 'de', 'og' => 'de_DE'],
        'en' => ['name' => 'English', 'flag' => 'gb', 'og' => 'en_US'],
        'pl' => ['name' => 'Polski', 'flag' => 'pl', 'og' => 'pl_PL'],
        'sk' => ['name' => 'Slovenčina', 'flag' => 'sk', 'og' => 'sk_SK'],
        'ro' => ['name' => 'Română', 'flag' => 'ro', 'og' => 'ro_RO'],
    ];
    $currentLocale = array_key_exists(app()->getLocale(), $siteLocales) ? app()->getLocale() : 'de';
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    {{-- Primary Meta Tags --}}
    <title>@yield('title', __('layout.meta_default_title'))</title>
    <meta name="description" content="@yield('meta_description', __('layout.meta_default_description'))">
    <meta name="keywords" content="@yield('meta_keywords', __('layout.meta_default_keywords'))">
    <meta name="author" content="Kaymakci Real Estate GmbH">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', __('layout.og_default_title'))">
    <meta property="og:description" content="@yield('og_description', __('layout.og_default_description'))">
    <meta property="og:image" content="@yield('og_image', asset('images/logo.png'))">
    <meta property="og:locale" content="{{ $siteLocales[$currentLocale]['og'] }}">
    <meta property="og:site_name" content="Kaymakci Real Estate GmbH">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('twitter_title', 'Kaymakci Real Estate GmbH - Ihr Immobilienmakler in Deutschland')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Kaymakci Real Estate GmbH - Ihr zuverlässiger Partner für Immobilien in Deutschland.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/logo.png'))">

    {{-- Geo Tags --}}
    <meta name="geo.region" content="DE">
    <meta name="geo.placename" content="Deutschland">

    {{-- Structured Data --}}
    @yield('structured_data')

    {{-- Fonts --}}
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
    <style>
        [x-cloak]{display:none !important;}
        /* Never allow accidental horizontal scrolling on small screens */
        html, body { max-width: 100%; overflow-x: clip; }
        /* Rich text from the admin editor (Quill): readable on every screen size.
           Plain CSS on purpose so it also works with the Tailwind CDN fallback. */
        .prose { overflow-wrap: anywhere; }
        .prose p + p, .prose p + ul, .prose p + ol, .prose ul + p, .prose ol + p { margin-top: 1em; }
        .prose ul { list-style: disc; padding-left: 1.4em; margin: .75em 0; }
        .prose ol { list-style: decimal; padding-left: 1.4em; margin: .75em 0; }
        .prose li + li { margin-top: .25em; }
        .prose h1, .prose h2, .prose h3 { font-weight: 600; margin: 1.25em 0 .5em; line-height: 1.3; }
        .prose a { text-decoration: underline; }
        .prose img, .prose video, .prose iframe { max-width: 100%; height: auto; border-radius: .5rem; }
        .prose .ql-align-center { text-align: center; } .prose .ql-align-right { text-align: right; }
    </style>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>
<body class="bg-ivory-100 text-ivory-900 min-h-screen flex flex-col font-sans antialiased">

    <header class="sticky top-0 z-50 bg-ivory-50/95 backdrop-blur-sm border-b border-ivory-200" role="banner" x-data="{ mobileOpen: false }" @keydown.escape.window="mobileOpen = false">
        <div class="max-w-7xl 2xl:max-w-[96rem] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20 gap-3">
                <a href="{{ route('properties.index') }}" class="flex items-center gap-2 sm:gap-3 min-w-0" aria-label="{{ __('layout.home_aria') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Kaymakci Real Estate Logo" class="h-10 sm:h-14 w-auto flex-shrink-0">
                    <span class="font-serif text-base sm:text-xl font-semibold text-navy-900 truncate tracking-tight">Kaymakci Real Estate</span>
                </a>

                {{-- Desktop navigation --}}
                <div class="hidden md:flex items-center gap-8">
                    <nav class="flex gap-8 text-sm font-medium text-ivory-700" role="navigation" aria-label="{{ __('layout.main_nav_aria') }}">
                        <a href="{{ route('properties.index') }}" class="hover:text-gold-600 transition-colors">{{ __('layout.nav_properties') }}</a>
                        <a href="{{ route('pages.about') }}" class="hover:text-gold-600 transition-colors">{{ __('layout.nav_about') }}</a>
                        <a href="{{ route('pages.contact') }}" class="hover:text-gold-600 transition-colors">{{ __('layout.nav_contact') }}</a>
                    </nav>
                    <div class="relative border-l border-ivory-200 pl-6" x-data="{ langOpen: false }" @click.away="langOpen = false" @keydown.escape.window="langOpen = false">
                        <button type="button" @click="langOpen = !langOpen"
                                class="flex items-center gap-2 text-sm font-medium text-ivory-700 hover:text-gold-600 transition-colors"
                                :aria-expanded="langOpen" aria-haspopup="listbox" aria-label="Sprache wählen / Select language">
                            <img src="https://flagcdn.com/{{ $siteLocales[$currentLocale]['flag'] }}.svg" width="20" height="15" alt="" class="w-5 h-auto rounded-sm shadow-sm" aria-hidden="true">
                            <span>{{ strtoupper($currentLocale) }}</span>
                            <svg class="w-3.5 h-3.5 text-ivory-400 transition-transform" :class="langOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="langOpen" x-cloak x-transition
                             class="absolute right-0 mt-2 w-48 bg-ivory-50 border border-ivory-200 rounded-xl shadow-lift overflow-hidden z-20"
                             role="listbox" aria-label="Sprache wählen / Select language">
                            @foreach($siteLocales as $code => $info)
                                <a href="{{ route('locale.switch', $code) }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors {{ $currentLocale === $code ? 'bg-gold-50 text-gold-700 font-semibold' : 'text-ivory-700 hover:bg-ivory-100' }}"
                                   role="option" @if($currentLocale === $code) aria-selected="true" @endif>
                                    <img src="https://flagcdn.com/{{ $info['flag'] }}.svg" width="20" height="15" alt="" class="w-5 h-auto rounded-sm shadow-sm flex-shrink-0" aria-hidden="true">
                                    <span>{{ $info['name'] }}</span>
                                    @if($currentLocale === $code)
                                        <svg class="w-4 h-4 ml-auto text-gold-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Mobile hamburger --}}
                <button type="button" @click="mobileOpen = !mobileOpen"
                        class="md:hidden inline-flex items-center justify-center p-2 -mr-2 rounded-lg text-ivory-700 hover:bg-ivory-100 flex-shrink-0"
                        :aria-expanded="mobileOpen" aria-label="{{ __('layout.main_nav_aria') }}">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Mobile navigation panel --}}
            <div x-show="mobileOpen" x-cloak x-transition class="md:hidden border-t border-ivory-200 py-3">
                <nav class="flex flex-col text-sm font-medium text-ivory-700" role="navigation" aria-label="{{ __('layout.main_nav_aria') }}">
                    <a href="{{ route('properties.index') }}" class="py-3 hover:text-gold-600 transition-colors">{{ __('layout.nav_properties') }}</a>
                    <a href="{{ route('pages.about') }}" class="py-3 hover:text-gold-600 transition-colors">{{ __('layout.nav_about') }}</a>
                    <a href="{{ route('pages.contact') }}" class="py-3 hover:text-gold-600 transition-colors">{{ __('layout.nav_contact') }}</a>
                </nav>
                <div class="mt-2 pt-3 border-t border-ivory-200">
                    <span class="block text-xs font-semibold text-ivory-400 uppercase tracking-wide mb-2">Sprache / Language</span>
                    <div class="grid grid-cols-2 min-[480px]:grid-cols-3 gap-2" role="navigation" aria-label="Sprachauswahl / Language selection">
                        @foreach($siteLocales as $code => $info)
                            <a href="{{ route('locale.switch', $code) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ $currentLocale === $code ? 'bg-gold-50 text-gold-700 font-semibold' : 'text-ivory-700 hover:bg-ivory-100' }}"
                               @if($currentLocale === $code) aria-current="true" @endif>
                                <img src="https://flagcdn.com/{{ $info['flag'] }}.svg" width="20" height="15" alt="" class="w-5 h-auto rounded-sm shadow-sm flex-shrink-0" aria-hidden="true">
                                <span>{{ $info['name'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1" role="main">
        @yield('content')
    </main>

    <footer class="relative overflow-hidden bg-gradient-to-b from-navy-900 to-navy-950 text-ivory-300 mt-12" role="contentinfo">
        {{-- Gold accent line --}}
        <div class="h-px w-full bg-gradient-to-r from-transparent via-gold-500/50 to-transparent"></div>

        {{-- Ambient glow, consistent with hero sections --}}
        <div class="pointer-events-none absolute -top-24 right-[-4rem] w-80 h-80 rounded-full bg-gold-500/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -left-16 w-96 h-96 rounded-full bg-navy-500/20 blur-3xl"></div>

        <div class="relative max-w-7xl 2xl:max-w-[96rem] mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.3fr_1fr_1.2fr] gap-10 lg:gap-12">

                {{-- Brand column --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-3 mb-4" aria-label="{{ __('layout.home_aria') }}">
                        <span class="bg-ivory-50 rounded-xl p-2 shadow-lift inline-flex flex-shrink-0">
                            <img src="{{ asset('images/logo.png') }}" alt="Kaymakci Real Estate Logo" class="h-10 w-auto">
                        </span>
                        <span class="font-serif text-lg font-semibold text-ivory-50 tracking-tight">Kaymakci Real Estate</span>
                    </a>
                    <p class="text-sm text-ivory-400 leading-relaxed max-w-xs">{{ __('layout.footer_tagline') }}</p>

                    <div class="mt-6 flex items-center gap-3">
                        <a href="https://www.instagram.com/kaymakci_realestate" target="_blank" rel="noopener noreferrer" aria-label="Instagram"
                           class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-ivory-50/5 border border-ivory-50/10 text-ivory-300 hover:bg-gold-500 hover:text-navy-950 hover:border-gold-500 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        <a href="mailto:ali@kaymakci-real-estate.de" aria-label="E-Mail"
                           class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-ivory-50/5 border border-ivory-50/10 text-ivory-300 hover:bg-gold-500 hover:text-navy-950 hover:border-gold-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Quick links column --}}
                <div>
                    <h2 class="font-serif text-sm font-semibold text-ivory-50 tracking-wide uppercase mb-4">{{ __('layout.footer_quicklinks') }}</h2>
                    <nav class="flex flex-col gap-2.5 text-sm text-ivory-400" aria-label="{{ __('layout.footer_quicklinks') }}">
                        <a href="{{ route('properties.index') }}" class="group inline-flex items-center gap-2 hover:text-gold-400 transition-colors">
                            <span class="w-1 h-1 rounded-full bg-gold-500/70 group-hover:bg-gold-400 transition-colors"></span>{{ __('layout.nav_properties') }}
                        </a>
                        <a href="{{ route('pages.about') }}" class="group inline-flex items-center gap-2 hover:text-gold-400 transition-colors">
                            <span class="w-1 h-1 rounded-full bg-gold-500/70 group-hover:bg-gold-400 transition-colors"></span>{{ __('layout.nav_about') }}
                        </a>
                        <a href="{{ route('pages.contact') }}" class="group inline-flex items-center gap-2 hover:text-gold-400 transition-colors">
                            <span class="w-1 h-1 rounded-full bg-gold-500/70 group-hover:bg-gold-400 transition-colors"></span>{{ __('layout.nav_contact') }}
                        </a>
                    </nav>
                </div>

                {{-- Contact column --}}
                <div>
                    <h2 class="font-serif text-sm font-semibold text-ivory-50 tracking-wide uppercase mb-4">{{ __('layout.footer_contact') }}</h2>
                    <ul class="flex flex-col gap-3 text-sm text-ivory-400">
                        <li class="flex items-start gap-3">
                            <svg class="w-4 h-4 mt-0.5 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Riedhofweg 23<br>60596 Frankfurt am Main</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="tel:+4917624821040" class="hover:text-gold-400 transition-colors">+49 176 24821040</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:ali@kaymakci-real-estate.de" class="hover:text-gold-400 transition-colors break-all">ali@kaymakci-real-estate.de</a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="mt-12 pt-6 border-t border-ivory-50/10 flex flex-col-reverse sm:flex-row items-center justify-between gap-3 text-xs text-ivory-500">
                <p class="text-center sm:text-left">&copy; {{ date('Y') }} Kaymakci Real Estate GmbH. {{ __('layout.footer_rights') }}</p>
                <p class="text-ivory-600">Frankfurt am Main, Deutschland</p>
            </div>
        </div>
    </footer>

    {{-- Organization Schema --}}
    @php
    $organization = [
        '@context' => 'https://schema.org',
        '@type' => 'RealEstateAgent',
        'name' => 'Kaymakci Real Estate GmbH',
        'description' => __('layout.org_description'),
        'url' => url('/'),
        'logo' => asset('images/logo.png'),
        'image' => asset('images/logo.png'),
        'telephone' => '+49 176 24821040',
        'email' => 'ali@kaymakci-real-estate.de',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Riedhofweg 23',
            'postalCode' => '60596',
            'addressLocality' => 'Frankfurt am Main',
            'addressCountry' => 'DE'
        ],
        'openingHoursSpecification' => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'opens' => '09:00',
            'closes' => '18:00'
        ],
        'areaServed' => [
            '@type' => 'Country',
            'name' => __('layout.org_area_served')
        ]
    ];
    @endphp
    <script type="application/ld+json">{!! json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>

</body>
</html>
