<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $globalCompanyName ?? config('app.name') }} - @yield('title', 'HRMS Portal')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full antialiased text-slate-800 selection:bg-brand-500 selection:text-white" x-data="{ sidebarOpen: false }">

<div class="min-h-full flex">
    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 lg:hidden"></div>

    <!-- Sidebar Component -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 w-72 bg-[#0B0F19] text-white flex flex-col transition-transform duration-300 ease-in-out border-r border-slate-800/80 shadow-2xl overflow-hidden">
        
        <!-- Subtle Mesh Ambient Background & Check/Grid Pattern matching login screen -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
            <div class="absolute -top-16 -left-16 w-64 h-64 rounded-full bg-gradient-to-br from-brand-600/20 via-orange-600/10 to-transparent blur-3xl"></div>
            <div class="absolute bottom-10 -right-16 w-64 h-64 rounded-full bg-gradient-to-tl from-brand-500/15 via-slate-800/30 to-transparent blur-3xl"></div>
            <!-- Check / Grid Overlay Pattern (32px x 32px) -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff08_1px,transparent_1px),linear-gradient(to_bottom,#ffffff08_1px,transparent_1px)] bg-[size:32px_32px]"></div>
        </div>

        <!-- Brand Header -->
        <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800/80 bg-slate-950/70 backdrop-blur-xs relative z-10">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                
                <div class="min-w-0">
                    <span class="text-base font-black tracking-tight text-white block truncate leading-tight">{{ $globalCompanyName ?? config('app.name') }}</span>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block mt-0.5">HRMS Enterprise</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- User Profile & Role Badge Card -->
        <div class="px-5 py-4 border-b border-slate-800/60 bg-gradient-to-r from-brand-950/30 via-slate-900/60 to-[#0B0F19] relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ Auth::user()->isSuperAdmin() ? 'bg-gradient-to-tr from-brand-500 to-amber-400 text-white shadow-brand-500/30' : 'bg-gradient-to-tr from-brand-600 to-orange-400 text-white shadow-brand-500/20' }} flex items-center justify-center font-bold shadow-md text-sm border border-white/15">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="overflow-hidden">
                    <div class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        @if (Auth::user()->isSuperAdmin())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-500/20 text-brand-300 border border-brand-500/40">
                                ★ SUPERADMIN
                            </span>
                        @elseif (Auth::user()->isAdmin())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                                ★ HR ADMIN
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                                ● EMPLOYEE
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-5 space-y-1 overflow-y-auto relative z-10">
            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                {{ Auth::user()->isAdmin() ? 'Main Menu' : 'Employee Portal' }}
            </div>

            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-lg shadow-brand-500/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            @if (Auth::user()->isAdmin())
                <!-- Organization Masters Accordion / Dropdown Menu (Admin Only) -->
                <div x-data="{ open: {{ (request()->routeIs('employees.*') || request()->routeIs('departments.*') || request()->routeIs('designations.*')) ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button" 
                            @click="open = !open" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition {{ (request()->routeIs('employees.*') || request()->routeIs('departments.*') || request()->routeIs('designations.*')) ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg {{ (request()->routeIs('employees.*') || request()->routeIs('departments.*') || request()->routeIs('designations.*')) ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'bg-slate-800 text-slate-300' }} flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span class="font-semibold">Organization Masters</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180 text-brand-400' : 'text-slate-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Submenu Links -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="pl-4 pr-1 py-1 space-y-1 border-l-2 border-brand-500/30 ml-4">
                        
                        <!-- Employees Submenu -->
                        <a href="{{ route('employees.index') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('employees.*') ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-md shadow-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="flex items-center gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('employees.*') ? 'bg-white' : 'bg-slate-600' }}"></span>
                                <span>Employees Master</span>
                            </div>
                            <span class="text-[10px] {{ request()->routeIs('employees.*') ? 'text-brand-100' : 'text-slate-500' }}">Dir</span>
                        </a>

                        <!-- Departments Submenu -->
                        <a href="{{ route('departments.index') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('departments.*') ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-md shadow-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="flex items-center gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('departments.*') ? 'bg-white' : 'bg-slate-600' }}"></span>
                                <span>Departments</span>
                            </div>
                            <span class="text-[10px] {{ request()->routeIs('departments.*') ? 'text-brand-100' : 'text-slate-500' }}">Master</span>
                        </a>

                        <!-- Designations Submenu -->
                        <a href="{{ route('designations.index') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('designations.*') ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-md shadow-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="flex items-center gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('designations.*') ? 'bg-white' : 'bg-slate-600' }}"></span>
                                <span>Designations</span>
                            </div>
                            <span class="text-[10px] {{ request()->routeIs('designations.*') ? 'text-brand-100' : 'text-slate-500' }}">Master</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Attendance Management Accordion / Dropdown Menu -->
            <div x-data="{ open: {{ request()->routeIs('attendances.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button type="button" 
                        @click="open = !open" 
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('attendances.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg {{ request()->routeIs('attendances.*') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'bg-slate-800 text-slate-300' }} flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <span class="font-semibold">{{ Auth::user()->isAdmin() ? 'Attendance' : 'My Attendance' }}</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180 text-brand-400' : 'text-slate-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Attendance Submenu Links -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-4 pr-1 py-1 space-y-1 border-l-2 border-brand-500/30 ml-4">
                    
                    <!-- Daily Attendance Link -->
                    <a href="{{ route('attendances.index') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('attendances.index') ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-md shadow-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('attendances.index') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>{{ Auth::user()->isAdmin() ? 'Daily Attendance' : 'Daily Punch Sheet' }}</span>
                        </div>
                        <span class="text-[10px] {{ request()->routeIs('attendances.index') ? 'text-brand-100' : 'text-slate-500' }}">Sheet</span>
                    </a>

                    <!-- Monthly Report / Register Link -->
                    <a href="{{ route('attendances.report') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('attendances.report') ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-md shadow-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('attendances.report') ? 'bg-white' : 'bg-slate-600' }}"></span>
                            <span>{{ Auth::user()->isAdmin() ? 'Monthly Register' : 'Monthly Attendance' }}</span>
                        </div>
                        <span class="text-[10px] {{ request()->routeIs('attendances.report') ? 'text-brand-100' : 'text-slate-500' }}">Report</span>
                    </a>
                </div>
            </div>

            @php
                $pendingLeavesCount = \App\Models\LeaveRequest::where('status', 'Pending')->count();
            @endphp
            <a href="{{ route('leaves.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('leaves.*') ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-lg shadow-brand-500/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ Auth::user()->isAdmin() ? 'Leave Management' : 'My Leaves' }}</span>
                @if (Auth::user()->isAdmin() && $pendingLeavesCount > 0)
                    <span class="ml-auto text-xs bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded-full font-bold border border-amber-400/30">{{ $pendingLeavesCount }} Pending</span>
                @endif
            </a>

            <a href="{{ route('payrolls.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('payrolls.*') ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-lg shadow-brand-500/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>{{ Auth::user()->isAdmin() ? 'Payroll & Salary' : 'My Payslips' }}</span>
            </a>

            <a href="{{ route('work-reports.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('work-reports.*') ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-lg shadow-brand-500/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>{{ Auth::user()->isAdmin() ? 'Daily Work & KPIs' : 'My Work Report (DWR)' }}</span>
            </a>

            @if (Auth::user()->isAdmin())
                <div class="pt-4 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Administration</div>

                <a href="{{ route('settings.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('settings.*') ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-lg shadow-brand-500/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} transition">
                    <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>System Settings</span>
                </a>
            @endif

            <a href="{{ route('profile.edit') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('profile.edit') ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-lg shadow-brand-500/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} transition">
                <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>{{ Auth::user()->isAdmin() ? 'Admin Profile' : 'My Profile & Password' }}</span>
            </a>

            @if (Auth::user()->employee)
            <a href="{{ route('profile.employee-edit') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('profile.employee-edit') ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-lg shadow-brand-500/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} transition">
                <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>My Employee Data</span>
            </a>
            @endif
        </nav>

        <!-- Footer Log Out -->
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/40">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-rose-600/90 text-slate-300 hover:text-white text-xs font-semibold tracking-wide transition border border-slate-700/60 hover:border-rose-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Sign Out (Logout)</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:pl-72 flex flex-col flex-1 min-w-0">
        <!-- Top Navbar -->
        <header class="h-16 sm:h-20 bg-white border-b border-slate-200/80 sticky top-0 z-30 flex items-center justify-between px-3 sm:px-8">
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="min-w-0">
                    <h1 class="text-base sm:text-xl font-bold text-slate-900 tracking-tight truncate">@yield('page_title', 'HRMS Dashboard')</h1>
                    <p class="text-[11px] text-slate-500 hidden sm:block">Welcome back, {{ Auth::user()->name }} 👋</p>
                </div>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-3">
                <!-- Search bar -->
                <div class="relative hidden md:block w-64">
                    <input type="text" placeholder="Search employee, ID..." class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-slate-50/50">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Notifications -->
                <button class="p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-brand-500 ring-2 ring-white"></span>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ userMenu: false }">
                    <button @click="userMenu = !userMenu" class="flex items-center gap-3 pl-2 pr-3 py-1.5 rounded-2xl border border-slate-200 hover:border-slate-300 transition">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-amber-500 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <div class="text-left hidden sm:block">
                            <div class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] font-semibold text-brand-600 uppercase tracking-wider">{{ Auth::user()->role }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="userMenu" 
                         @click.away="userMenu = false"
                         x-transition
                         class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">
                            Profile Settings
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50">
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Workspace Body -->
        <main class="flex-1 p-3.5 sm:p-6 lg:p-8 pb-20 lg:pb-8">
            {{ $slot }}
        </main>

        <!-- Clean Authenticated Footer -->
        <footer class="py-4 px-4 sm:px-8 border-t border-slate-200/80 bg-white text-slate-500 text-xs flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} <a href="{{ $globalCompanyWebsite ?: '#' }}" target="_blank" rel="noopener noreferrer" class="text-brand-600 hover:text-brand-700 font-semibold underline decoration-brand-500/30 underline-offset-2">{{ $globalCompanyName ?? config('app.name') }}</a>. All rights reserved.</p>
            <div class="flex items-center gap-3 text-[11px] text-slate-400">
                @if (!empty($globalCompanyWebsite))
                <a href="{{ $globalCompanyWebsite }}" target="_blank" rel="noopener noreferrer" class="hover:text-brand-600 font-medium transition flex items-center gap-1">
                    <span>{{ $globalCompanyWebsite }}</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
                <span>•</span>
                @endif
                <span>HRMS Portal v2.0</span>
            </div>
        </footer>
    </div>
</div>

<!-- Mobile Sticky Bottom Navigation Bar (Quick Access for Mobile Users) -->
<nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200 z-40 px-2 py-1.5 flex items-center justify-around shadow-lg">
    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" 
       class="flex flex-col items-center justify-center py-1 px-2 rounded-xl text-[10px] font-bold transition {{ request()->routeIs('dashboard') ? 'text-brand-600 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-brand-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <span class="mt-0.5">Home</span>
    </a>

    <!-- Attendance -->
    <a href="{{ route('attendances.index') }}" 
       class="flex flex-col items-center justify-center py-1 px-2 rounded-xl text-[10px] font-bold transition {{ request()->routeIs('attendances.*') ? 'text-brand-600 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('attendances.*') ? 'text-brand-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="mt-0.5">Punch</span>
    </a>

    <!-- Leaves -->
    <a href="{{ route('leaves.index') }}" 
       class="flex flex-col items-center justify-center py-1 px-2 rounded-xl text-[10px] font-bold transition {{ request()->routeIs('leaves.*') ? 'text-brand-600 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('leaves.*') ? 'text-brand-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span class="mt-0.5">Leaves</span>
    </a>

    <!-- Work Reports (DWR) -->
    <a href="{{ route('work-reports.index') }}" 
       class="flex flex-col items-center justify-center py-1 px-2 rounded-xl text-[10px] font-bold transition {{ request()->routeIs('work-reports.*') ? 'text-brand-600 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('work-reports.*') ? 'text-brand-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        <span class="mt-0.5">Reports</span>
    </a>

    <!-- Menu / Sidebar Trigger -->
    <button type="button" @click="sidebarOpen = true" 
            class="flex flex-col items-center justify-center py-1 px-2 rounded-xl text-[10px] font-bold text-slate-500 hover:text-slate-800 transition">
        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <span class="mt-0.5">Menu</span>
    </button>
</nav>

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

