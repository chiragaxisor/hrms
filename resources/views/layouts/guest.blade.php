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

<!-- Full System Global Loader Overlay -->
<div id="global-page-loader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-950/80 backdrop-blur-md transition-opacity duration-300 loader-hidden" aria-hidden="true">
    <div class="relative flex flex-col items-center justify-center p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-2xl">
        <!-- Glowing Ambient Ring -->
        <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-brand-500 to-orange-500 opacity-30 blur-xl animate-pulse"></div>
        
        <!-- Spinner Icon & Logo -->
        <div class="relative flex items-center justify-center w-16 h-16 mb-4">
            <div class="absolute inset-0 rounded-full border-4 border-brand-500/20 border-t-brand-500 animate-spin"></div>
            
        </div>
        
        <div class="relative text-center">
            <div class="text-sm font-extrabold tracking-wide text-white">{{ $globalCompanyName ?? config('app.name') }}</div>
            <div class="text-xs font-semibold text-slate-400 mt-1 flex items-center gap-1.5 justify-center">
                <span>Loading...</span>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const loader = document.getElementById('global-page-loader');

        function showLoader() {
            if (loader) {
                loader.classList.remove('loader-hidden');
            }
        }

        function hideLoader() {
            if (loader) {
                loader.classList.add('loader-hidden');
            }
        }

        window.addEventListener('pageshow', function () {
            hideLoader();
            document.querySelectorAll('button[data-btn-loading="true"]').forEach(btn => {
                btn.removeAttribute('data-btn-loading');
                btn.disabled = false;
                btn.classList.remove('is-loading');
                const spinner = btn.querySelector('.btn-spinner-icon');
                if (spinner) spinner.remove();
            });
        });

        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.getAttribute('target') === '_blank' || form.hasAttribute('data-no-loader')) {
                return;
            }

            const submitBtn = e.submitter || form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('is-loading');
                submitBtn.setAttribute('data-btn-loading', 'true');
                
                if (!submitBtn.querySelector('.btn-spinner-icon')) {
                    const spinnerSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                    spinnerSvg.setAttribute('class', 'animate-spin -ml-1 mr-2 h-4 w-4 text-current inline-block btn-spinner-icon');
                    spinnerSvg.setAttribute('fill', 'none');
                    spinnerSvg.setAttribute('viewBox', '0 0 24 24');
                    spinnerSvg.innerHTML = '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>';
                    submitBtn.prepend(spinnerSvg);
                }
            }

            showLoader();
        });

        document.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            const target = link.getAttribute('target');

            if (!href || 
                href.startsWith('#') || 
                href.startsWith('javascript:') || 
                href.startsWith('mailto:') || 
                href.startsWith('tel:') || 
                target === '_blank' || 
                link.hasAttribute('download') || 
                link.hasAttribute('data-no-loader') || 
                e.ctrlKey || e.metaKey || e.shiftKey
            ) {
                return;
            }

            showLoader();
        });
    })();
</script>

    </body>
</html>

