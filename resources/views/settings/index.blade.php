<x-hrms-layout>
    @section('page_title', 'System Settings & Configurations')

    <!-- Flash Notifications -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-xs animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="font-semibold text-xs">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold p-1 text-sm">✕</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
            <div class="font-semibold mb-1">Please fix the following validation errors:</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-brand-500 text-white flex items-center justify-center shadow-md shadow-brand-500/20 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-slate-900 tracking-tight">System Settings & Configurations</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-600 border border-brand-100">
                                Core Engine
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Manage company profiles, shift timings, payroll tax rules, and system-wide automation parameters.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>System Online & Active</span>
                </span>
            </div>
        </div>

        <!-- Settings Navigation Tabs -->
        <div class="mt-6 pt-5 border-t border-slate-100 flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
            <a href="{{ route('settings.index', ['tab' => 'company']) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 {{ $activeTab === 'company' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>Company Profile & Branding</span>
            </a>

            <a href="{{ route('settings.index', ['tab' => 'attendance']) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 {{ $activeTab === 'attendance' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Attendance & Shift Policies</span>
            </a>

            <a href="{{ route('settings.index', ['tab' => 'payroll']) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 {{ $activeTab === 'payroll' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Payroll, CTC & Tax Rules</span>
            </a>

            <a href="{{ route('settings.index', ['tab' => 'notifications']) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 {{ $activeTab === 'notifications' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span>KPI Benchmarks & Alerts</span>
            </a>

            <a href="{{ route('settings.index', ['tab' => 'security']) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 {{ $activeTab === 'security' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Security & Change Password</span>
            </a>
        </div>
    </div>

    <!-- Tab Content Forms -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-8">
        @if ($activeTab === 'company')
            <!-- TAB 1: COMPANY PROFILE -->
            <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="tab" value="company">

                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h3 class="text-base font-bold text-slate-900">Organization & Legal Entity Info</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Used on official payslips, joining letters, and HR correspondence.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="company_name" class="block text-xs font-bold text-slate-700 mb-1">Company Registered Name *</label>
                        <input type="text" name="company_name" id="company_name" required value="{{ old('company_name', $settings['company_name']) }}"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="company_email" class="block text-xs font-bold text-slate-700 mb-1">Official Support / HR Email *</label>
                        <input type="email" name="company_email" id="company_email" required value="{{ old('company_email', $settings['company_email']) }}"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="company_phone" class="block text-xs font-bold text-slate-700 mb-1">Office Telephone / Mobile</label>
                        <input type="text" name="company_phone" id="company_phone" value="{{ old('company_phone', $settings['company_phone']) }}"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="company_website" class="block text-xs font-bold text-slate-700 mb-1">Company Website URL</label>
                        <input type="url" name="company_website" id="company_website" value="{{ old('company_website', $settings['company_website']) }}"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="company_tax_id" class="block text-xs font-bold text-slate-700 mb-1">GSTIN / Tax Registration ID</label>
                        <input type="text" name="company_tax_id" id="company_tax_id" value="{{ old('company_tax_id', $settings['company_tax_id']) }}"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none uppercase font-mono">
                    </div>

                    <div>
                        <label for="company_cin" class="block text-xs font-bold text-slate-700 mb-1">Corporate Identification Number (CIN)</label>
                        <input type="text" name="company_cin" id="company_cin" value="{{ old('company_cin', $settings['company_cin']) }}"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none uppercase font-mono">
                    </div>
                </div>

                <div>
                    <label for="company_address" class="block text-xs font-bold text-slate-700 mb-1">Headquarters / Registered Address</label>
                    <textarea name="company_address" id="company_address" rows="3"
                              class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none">{{ old('company_address', $settings['company_address']) }}</textarea>
                </div>

                <div class="border-t border-slate-100 pt-6 mt-6">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Localization & System Formatting</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="timezone" class="block text-xs font-bold text-slate-700 mb-1">System Timezone *</label>
                            <select name="timezone" id="timezone" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500">
                                <option value="Asia/Kolkata" {{ $settings['timezone'] === 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST +5:30)</option>
                                <option value="UTC" {{ $settings['timezone'] === 'UTC' ? 'selected' : '' }}>UTC (Coordinated Universal Time)</option>
                                <option value="America/New_York" {{ $settings['timezone'] === 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                                <option value="Europe/London" {{ $settings['timezone'] === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                            </select>
                        </div>

                        <div>
                            <label for="currency_symbol" class="block text-xs font-bold text-slate-700 mb-1">Default Currency Symbol *</label>
                            <input type="text" name="currency_symbol" id="currency_symbol" required value="{{ old('currency_symbol', $settings['currency_symbol']) }}"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div>
                            <label for="date_format" class="block text-xs font-bold text-slate-700 mb-1">Date Display Format *</label>
                            <select name="date_format" id="date_format" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500">
                                <option value="d-M-Y" {{ $settings['date_format'] === 'd-M-Y' ? 'selected' : '' }}>DD-Mon-YYYY (08-Sep-2026)</option>
                                <option value="Y-m-d" {{ $settings['date_format'] === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2026-09-08)</option>
                                <option value="d/m/Y" {{ $settings['date_format'] === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (08/09/2026)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        Save Company Profile
                    </button>
                </div>
            </form>

        @elseif ($activeTab === 'attendance')
            <!-- TAB 2: ATTENDANCE & SHIFTS -->
            <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="tab" value="attendance">

                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h3 class="text-base font-bold text-slate-900">Shift Timings & Work Hours Thresholds</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Defines standard working hours, late mark grace periods, and break policies.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="office_start_time" class="block text-xs font-bold text-slate-700 mb-1">Shift Start Time (Clock-In) *</label>
                        <input type="text" name="office_start_time" id="office_start_time" required value="{{ old('office_start_time', $settings['office_start_time']) }}" placeholder="09:30"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 font-mono">
                        <span class="text-[10px] text-slate-400 mt-1 block">Format: 24-hour format HH:MM (e.g. 09:30)</span>
                    </div>

                    <div>
                        <label for="office_end_time" class="block text-xs font-bold text-slate-700 mb-1">Shift End Time (Clock-Out) *</label>
                        <input type="text" name="office_end_time" id="office_end_time" required value="{{ old('office_end_time', $settings['office_end_time']) }}" placeholder="18:30"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 font-mono">
                        <span class="text-[10px] text-slate-400 mt-1 block">Format: 24-hour format HH:MM (e.g. 18:30)</span>
                    </div>

                    <div>
                        <label for="grace_period_minutes" class="block text-xs font-bold text-slate-700 mb-1">Late Grace Period (Minutes) *</label>
                        <input type="number" name="grace_period_minutes" id="grace_period_minutes" required min="0" max="120" value="{{ old('grace_period_minutes', $settings['grace_period_minutes']) }}"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500">
                        <span class="text-[10px] text-slate-400 mt-1 block">Clocking in within grace period won't count as late arrival.</span>
                    </div>

                    <div>
                        <label for="standard_break_minutes" class="block text-xs font-bold text-slate-700 mb-1">Standard Break Allowance (Minutes) *</label>
                        <input type="number" name="standard_break_minutes" id="standard_break_minutes" required min="0" max="180" value="{{ old('standard_break_minutes', $settings['standard_break_minutes']) }}"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500">
                        <span class="text-[10px] text-slate-400 mt-1 block">Daily lunch & tea break quota (e.g. 60 mins).</span>
                    </div>

                    <div>
                        <label for="full_day_hours" class="block text-xs font-bold text-slate-700 mb-1">Minimum Hours for Full Day *</label>
                        <input type="number" step="0.5" name="full_day_hours" id="full_day_hours" required min="1" max="16" value="{{ old('full_day_hours', $settings['full_day_hours']) }}"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500">
                        <span class="text-[10px] text-slate-400 mt-1 block">Working at or above this threshold counts as 1.0 payable day.</span>
                    </div>

                    <div>
                        <label for="half_day_hours" class="block text-xs font-bold text-slate-700 mb-1">Minimum Hours for Half Day *</label>
                        <input type="number" step="0.5" name="half_day_hours" id="half_day_hours" required min="1" max="12" value="{{ old('half_day_hours', $settings['half_day_hours']) }}"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500">
                        <span class="text-[10px] text-slate-400 mt-1 block">Working between half-day and full-day threshold counts as 0.5 payable day.</span>
                    </div>
                </div>

                <!-- Office Geolocation & Facial Verification Controls -->
                <div class="border-t border-slate-100 pt-6 mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span>📍 Office GPS Geofence & Camera Facial Verification</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Control location-radius verification and live selfie capture during employee clock-in, clock-out, and breaks.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="office_latitude" class="block text-xs font-bold text-slate-700">Office Latitude *</label>
                                <button type="button" onclick="detectCurrentLocation()" class="text-[10px] text-brand-600 hover:text-brand-700 font-bold underline cursor-pointer">
                                    📍 Set to My Current GPS
                                </button>
                            </div>
                            <input type="text" name="office_latitude" id="office_latitude" value="{{ old('office_latitude', $settings['office_latitude'] ?? '23.0225') }}"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 font-mono">
                            <span class="text-[10px] text-slate-400 mt-1 block">GPS coordinates for office center point</span>
                        </div>

                        <div>
                            <label for="office_longitude" class="block text-xs font-bold text-slate-700 mb-1">Office Longitude *</label>
                            <input type="text" name="office_longitude" id="office_longitude" value="{{ old('office_longitude', $settings['office_longitude'] ?? '72.5714') }}"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 font-mono">
                            <span class="text-[10px] text-slate-400 mt-1 block">GPS coordinates for office center point</span>
                        </div>

                        <div>
                            <label for="geofence_radius_meters" class="block text-xs font-bold text-slate-700 mb-1">Geofence Radius (Meters) *</label>
                            <input type="number" name="geofence_radius_meters" id="geofence_radius_meters" required min="10" max="500000" value="{{ old('geofence_radius_meters', $settings['geofence_radius_meters'] ?? '250') }}"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 font-mono">
                            <span class="text-[10px] text-slate-400 mt-1 block">Maximum allowed distance (e.g. 250m or 500m)</span>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="require_geofence" value="1" {{ ($settings['require_geofence'] ?? '1') ? 'checked' : '' }}
                                   class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                            <div>
                                <span class="text-xs font-bold text-slate-800">Enforce Office GPS Verification</span>
                                <span class="block text-[11px] text-slate-500">Employee device must be within the geofence radius to successfully check-in/check-out.</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="require_camera_punch" value="1" {{ ($settings['require_camera_punch'] ?? '1') ? 'checked' : '' }}
                                   class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                            <div>
                                <span class="text-xs font-bold text-slate-800">Enforce Live Camera Face Scan Capture</span>
                                <span class="block text-[11px] text-slate-500">Opens real-time camera viewfinder to capture a photo preview and verification timestamp.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="auto_deduct_breaks" value="1" {{ $settings['auto_deduct_breaks'] ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                        <div>
                            <span class="text-xs font-bold text-slate-800">Auto-Deduct Break Timings from Total Hours</span>
                            <span class="block text-[11px] text-slate-500">Automatically subtract recorded lunch/tea breaks from the shift duration.</span>
                        </div>
                    </label>
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        Save Attendance Policies
                    </button>
                </div>
            </form>

        @elseif ($activeTab === 'payroll')
            <!-- TAB 3: PAYROLL & TAXATION -->
            <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="tab" value="payroll">

                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h3 class="text-base font-bold text-slate-900">Salary Structure Breakdown & Statutory Deductions</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Control default gross allocation percentages and statutory compliance rates.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="basic_salary_percentage" class="block text-xs font-bold text-slate-700 mb-1">Basic Salary (% of Gross) *</label>
                        <div class="relative">
                            <input type="number" step="1" name="basic_salary_percentage" id="basic_salary_percentage" required value="{{ old('basic_salary_percentage', $settings['basic_salary_percentage']) }}"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 pr-8">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold">%</span>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block">Default: 50%</span>
                    </div>

                    <div>
                        <label for="hra_salary_percentage" class="block text-xs font-bold text-slate-700 mb-1">House Rent Allowance - HRA (% of Gross) *</label>
                        <div class="relative">
                            <input type="number" step="1" name="hra_salary_percentage" id="hra_salary_percentage" required value="{{ old('hra_salary_percentage', $settings['hra_salary_percentage']) }}"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 pr-8">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold">%</span>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block">Default: 25%</span>
                    </div>

                    <div>
                        <label for="allowance_salary_percentage" class="block text-xs font-bold text-slate-700 mb-1">Special / Other Allowances (% of Gross) *</label>
                        <div class="relative">
                            <input type="number" step="1" name="allowance_salary_percentage" id="allowance_salary_percentage" required value="{{ old('allowance_salary_percentage', $settings['allowance_salary_percentage']) }}"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 pr-8">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold">%</span>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block">Default: 25%</span>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6 mt-6">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Statutory Deductions & Payroll Cycle</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label for="default_pt_amount" class="block text-xs font-bold text-slate-700 mb-1">Default Professional Tax (PT) *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">₹</span>
                                <input type="number" name="default_pt_amount" id="default_pt_amount" required value="{{ old('default_pt_amount', $settings['default_pt_amount']) }}"
                                       class="w-full bg-white border border-slate-200 rounded-xl pl-7 pr-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500">
                            </div>
                            <span class="text-[10px] text-slate-400 mt-1 block">Monthly standard state deduction</span>
                        </div>

                        <div>
                            <label for="pf_employee_percent" class="block text-xs font-bold text-slate-700 mb-1">Employee PF Contribution *</label>
                            <div class="relative">
                                <input type="number" step="0.5" name="pf_employee_percent" id="pf_employee_percent" required value="{{ old('pf_employee_percent', $settings['pf_employee_percent']) }}"
                                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 pr-8">
                                <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold">%</span>
                            </div>
                            <span class="text-[10px] text-slate-400 mt-1 block">Standard 12% of Basic</span>
                        </div>

                        <div>
                            <label for="pf_employer_percent" class="block text-xs font-bold text-slate-700 mb-1">Employer PF Contribution *</label>
                            <div class="relative">
                                <input type="number" step="0.5" name="pf_employer_percent" id="pf_employer_percent" required value="{{ old('pf_employer_percent', $settings['pf_employer_percent']) }}"
                                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 pr-8">
                                <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold">%</span>
                            </div>
                            <span class="text-[10px] text-slate-400 mt-1 block">Standard 12% of Basic</span>
                        </div>

                        <div>
                            <label for="salary_disbursement_day" class="block text-xs font-bold text-slate-700 mb-1">Disbursement Day *</label>
                            <input type="number" name="salary_disbursement_day" id="salary_disbursement_day" required min="1" max="31" value="{{ old('salary_disbursement_day', $settings['salary_disbursement_day']) }}"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500">
                            <span class="text-[10px] text-slate-400 mt-1 block">Day of the month (e.g. 7th)</span>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        Save Payroll Rules
                    </button>
                </div>
            </form>

        @elseif ($activeTab === 'notifications')
            <!-- TAB 4: KPI & NOTIFICATIONS -->
            <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="tab" value="notifications">

                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h3 class="text-base font-bold text-slate-900">Work Targets & System Alerts</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Configure performance benchmarks and workflow alert toggles.</p>
                </div>

                <div class="max-w-md mb-8">
                    <label for="daily_work_report_target_calls" class="block text-xs font-bold text-slate-700 mb-1">Daily Work Report - Target Calls / Day *</label>
                    <input type="number" name="daily_work_report_target_calls" id="daily_work_report_target_calls" required min="1" max="500" value="{{ old('daily_work_report_target_calls', $settings['daily_work_report_target_calls']) }}"
                           class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 font-bold">
                    <span class="text-[10px] text-slate-400 mt-1 block">Used to calculate target achievement % on the KPI Summary Dashboard.</span>
                </div>

                <div class="space-y-4 border-t border-slate-100 pt-6">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Automated Notification Preferences</h4>

                    <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 cursor-pointer hover:bg-slate-100/60 transition">
                        <input type="checkbox" name="notify_leave_applied" value="1" {{ $settings['notify_leave_applied'] ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                        <div>
                            <span class="text-xs font-bold text-slate-900">Notify Admin when an Employee submits a Leave Request</span>
                            <span class="block text-[11px] text-slate-500">Triggers pending review alert on dashboard and sidebar.</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 cursor-pointer hover:bg-slate-100/60 transition">
                        <input type="checkbox" name="notify_attendance_absent" value="1" {{ $settings['notify_attendance_absent'] ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                        <div>
                            <span class="text-xs font-bold text-slate-900">Flag Consecutive Unmarked Absences</span>
                            <span class="block text-[11px] text-slate-500">Highlights employees missing clock-ins on working days.</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 cursor-pointer hover:bg-slate-100/60 transition">
                        <input type="checkbox" name="notify_payroll_generated" value="1" {{ $settings['notify_payroll_generated'] ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                        <div>
                            <span class="text-xs font-bold text-slate-900">Notify Finance upon Monthly Payroll Generation</span>
                            <span class="block text-[11px] text-slate-500">Alerts management when monthly payslips are ready for disbursement.</span>
                        </div>
                    </label>
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        Save Preferences
                    </button>
                </div>
            </form>

        @elseif ($activeTab === 'security')
            <!-- TAB 5: CHANGE PASSWORD & SECURITY -->
            <div class="max-w-2xl">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h3 class="text-base font-bold text-slate-900">Change Account Password</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Ensure your administrator account is using a secure and robust password.</p>
                </div>

                @if ($errors->updatePassword->any())
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                        <div class="font-semibold mb-1">Password change failed:</div>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->updatePassword->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('status') === 'password-updated')
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Password has been updated successfully!</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-bold text-slate-700 mb-1">Current Password *</label>
                        <input type="password" 
                               name="current_password" 
                               id="current_password" 
                               required 
                               autocomplete="current-password"
                               placeholder="Enter your current password"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1">New Password *</label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               autocomplete="new-password"
                               placeholder="Minimum 8 characters"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <span class="text-[10px] text-slate-400 mt-1 block">Must be at least 8 characters long with letters and numbers.</span>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Confirm New Password *</label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               required 
                               autocomplete="new-password"
                               placeholder="Re-enter new password"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-400">Logged in as: <strong class="text-slate-700">{{ Auth::user()->email }}</strong></span>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <script>
        function detectCurrentLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }
            navigator.geolocation.getCurrentPosition(function(pos) {
                document.getElementById('office_latitude').value = pos.coords.latitude.toFixed(6);
                document.getElementById('office_longitude').value = pos.coords.longitude.toFixed(6);
                alert('Successfully detected current coordinates: ' + pos.coords.latitude.toFixed(4) + ', ' + pos.coords.longitude.toFixed(4) + '. Click "Save Changes" to apply.');
            }, function(err) {
                alert('Error getting GPS location: ' + err.message + '. Please ensure location permission is allowed.');
            }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
        }
    </script>
</x-hrms-layout>
