<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Kaymakci Real Estate</title>

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
</head>
<body class="relative overflow-hidden bg-gradient-to-br from-navy-950 via-navy-900 to-navy-800 text-ivory-900 font-sans min-h-screen flex items-center justify-center p-4 antialiased">
    <div class="pointer-events-none absolute -top-32 right-[-6rem] w-[32rem] h-[32rem] rounded-full bg-gold-500/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-40 -left-24 w-[26rem] h-[26rem] rounded-full bg-navy-500/20 blur-3xl"></div>
    <div class="relative w-full max-w-md">
        <div class="bg-ivory-50 rounded-2xl shadow-lift p-8 sm:p-10">
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo-mark.png') }}" alt="Logo" class="h-16 w-auto mx-auto mb-5">
                <h1 class="font-serif text-2xl font-semibold text-navy-900">Admin Login</h1>
                <p class="text-ivory-600 mt-1 text-sm">Kaymakci Real Estate GmbH</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 text-sm">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-ivory-700 mb-1.5">E-Mail</label>
                    <input type="email" id="email" name="email" required value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                           placeholder="admin@example.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-ivory-700 mb-1.5">Passwort</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-3 border border-ivory-300 rounded-lg bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-500 outline-none transition"
                           placeholder="********">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember"
                           class="w-4 h-4 text-gold-600 border-ivory-300 rounded focus:ring-gold-400">
                    <label for="remember" class="ml-2 text-sm text-ivory-600">Angemeldet bleiben</label>
                </div>

                <button type="submit"
                        class="w-full bg-gold-600 text-ivory-50 px-6 py-3 rounded-lg font-medium hover:bg-gold-700 transition-colors shadow-soft">
                    Anmelden
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('properties.index') }}" class="text-sm text-ivory-500 hover:text-gold-600 transition-colors">
                    Zurück zur Website
                </a>
            </div>
        </div>
    </div>
</body>
</html>
