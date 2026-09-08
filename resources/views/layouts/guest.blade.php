<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $globalCompanyName ?? config('app.name') }} - HRMS Portal</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        </style>
    </head>
    <body class="font-sans text-slate-900 antialiased bg-[#0B0F19] selection:bg-brand-500 selection:text-white min-h-screen">
        <div class="min-h-screen flex flex-col justify-between relative overflow-hidden">
            
            <!-- Subtle Mesh Ambient Background -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-[25%] -left-[10%] w-[650px] h-[650px] rounded-full bg-gradient-to-br from-brand-600/20 via-orange-600/10 to-transparent blur-3xl"></div>
                <div class="absolute -bottom-[20%] -right-[10%] w-[600px] h-[600px] rounded-full bg-gradient-to-tl from-slate-800/40 via-brand-500/10 to-transparent blur-3xl"></div>
                <!-- Subtle grid overlay -->
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:32px_32px]"></div>
            </div>

            <!-- Top Minimal Navbar Brand -->
            <header class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ $globalCompanyName ?? config('app.name') }} Logo" class="w-10 h-10 rounded-xl shadow-lg shadow-brand-500/20 object-contain border border-white/10 bg-black">
                    <div>
                        <span class="text-white font-black text-lg tracking-tight">{{ $globalCompanyName ?? config('app.name') }}</span>
                        <span class="hidden sm:inline-block text-[10px] text-slate-400 uppercase tracking-widest ml-2 px-2 py-0.5 rounded-full bg-white/5 border border-white/10 font-semibold">HRMS Portal</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5 text-xs text-slate-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="hidden sm:inline font-medium">Enterprise System Online</span>
                    </div>
                </div>
            </header>

            <!-- Main Content Container -->
            <main class="flex-1 flex items-center justify-center px-4 sm:px-6 py-8 relative z-10">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </main>
            
            <!-- Clean Footer -->
            <footer class="w-full py-5 text-center text-xs text-slate-500 relative z-10 border-t border-white/5 bg-[#0B0F19]/40 backdrop-blur-xs">
                <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <p>&copy; {{ date('Y') }} <a href="{{ $globalCompanyWebsite ?: '#' }}" target="_blank" rel="noopener noreferrer" class="text-brand-500 hover:text-brand-400 font-semibold transition underline decoration-brand-500/30 underline-offset-2">{{ $globalCompanyName ?? config('app.name') }}</a>. Secure Human Capital Management.</p>
                    <div class="flex items-center gap-4 text-[11px] text-slate-400">
                        @if (!empty($globalCompanyWebsite))
                        <a href="{{ $globalCompanyWebsite }}" target="_blank" rel="noopener noreferrer" class="hover:text-white transition flex items-center gap-1">
                            <span>{{ str_replace(['https://', 'http://', '/'], '', $globalCompanyWebsite) }}</span>
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                        @endif
                        <span>•</span>
                        <span>Encrypted SSL</span>
                        <span>•</span>
                        <span>Role-Based Access</span>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
