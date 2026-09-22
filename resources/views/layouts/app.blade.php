<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
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
            <div class="text-sm font-extrabold tracking-wide text-white">{{ config('app.name', 'Laravel') }}</div>
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

