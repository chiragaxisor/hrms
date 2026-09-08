<x-hrms-layout>
    @section('page_title', 'Daily Work Report & KPIs')

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
            <div class="font-semibold mb-1">Please fix the following errors:</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top White Action Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-brand-500 text-white flex items-center justify-center shadow-md shadow-brand-500/20 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Daily Work Report & KPIs</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-600 border border-brand-100">
                                Activity Tracking
                            </span>
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">
                            Daily calls, social media outreach, posts, group activity, remarks & performance summary.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                @if (Auth::user()->isSuperAdmin())
                    <button type="button"
                            onclick="openCustomFieldsModal()"
                            class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-300/70 shadow-xs transition-all hover:scale-[1.02]">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>⚙️ Manage Fields</span>
                    </button>
                @endif

                <button type="button"
                        onclick="openLogModal()"
                        class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition-all hover:scale-[1.02]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Log Daily Activity</span>
                </button>
            </div>
        </div>

        <!-- Filter & Tab Switcher Bar -->
        <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            <!-- Left: Tab Switcher -->
            <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-2xl w-full lg:w-auto">
                <a href="{{ route('work-reports.index', ['employee_id' => $selectedEmployee?->id, 'month' => $month, 'tab' => 'sheet']) }}"
                   class="flex-1 lg:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition {{ $activeTab === 'sheet' ? 'bg-white text-brand-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>DAILY WORK REPORT (Sheet View)</span>
                </a>

                <a href="{{ route('work-reports.index', ['employee_id' => $selectedEmployee?->id, 'month' => $month, 'tab' => 'kpi']) }}"
                   class="flex-1 lg:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition {{ $activeTab === 'kpi' ? 'bg-white text-brand-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span>SUMMARY KPI DASHBOARD</span>
                </a>
            </div>

            <!-- Right: Filter Form (Employee & Month) -->
            <form method="GET" action="{{ route('work-reports.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="tab" value="{{ $activeTab }}">

                @if (Auth::user()->isAdmin())
                    <div class="flex items-center gap-2">
                        <label for="employee_id" class="text-xs font-bold text-slate-500 whitespace-nowrap">Employee:</label>
                        <select name="employee_id" 
                                id="employee_id"
                                onchange="this.form.submit()" 
                                class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $selectedEmployee?->id == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->employee_name }} ({{ $emp->employee_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="employee_id" value="{{ $selectedEmployee?->id }}">
                @endif

                <div class="flex items-center gap-2">
                    <label for="month" class="text-xs font-bold text-slate-500 whitespace-nowrap">Month:</label>
                    <input type="month" 
                           name="month" 
                           id="month"
                           value="{{ $month }}" 
                           onchange="this.form.submit()"
                           class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>
            </form>
        </div>
    </div>

    @if ($activeTab === 'sheet')
        <!-- ========================================== -->
        <!-- TAB 1: DAILY WORK REPORT (WHITE SHEET VIEW)-->
        <!-- ========================================== -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
            <!-- Sheet Banner Header (Clean Navy Banner matching Google Sheet) -->
            <div class="bg-[#103a71] px-6 py-4 border-b border-[#0d2e5a] text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                <div>
                    <div class="text-base font-black tracking-wider uppercase flex items-center gap-2">
                        <span>{{ $selectedEmployee?->employee_name ?? 'EMPLOYEE' }} – DAILY WORK REPORT</span>
                    </div>
                    <div class="text-xs text-blue-100 font-normal mt-0.5">
                        Daily calls, social media outreach, posts, group activity and remarks ({{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }})
                    </div>
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <span class="px-2.5 py-1 rounded-lg bg-white/15 text-white font-bold backdrop-blur-xs border border-white/20">
                        {{ $totals['logged_days_count'] }} Days Reported
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/15 text-white font-bold backdrop-blur-xs border border-white/20">
                        {{ $totals['calls'] }} Total Calls
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-amber-400 text-slate-950 font-black shadow-xs">
                        {{ $totals['social_activities'] }} Social Activities
                    </span>
                </div>
            </div>

            <!-- Scrollable Table exactly matching spreadsheet columns -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-700 border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 uppercase font-bold text-[11px] tracking-wider border-b border-slate-200 text-center">
                            <th class="py-3 px-3 border-r border-slate-200 text-left min-w-[100px] sticky left-0 bg-slate-50 z-10">Date</th>
                            
                            <!-- System Default Fields (Conditioned on enabled) -->
                            @if (! empty($systemFields['calls']['enabled']))
                                <th class="py-3 px-2 border-r border-slate-200 bg-blue-50/60 text-blue-900 min-w-[60px]">{{ $systemFields['calls']['label'] }}</th>
                            @endif
                            @if (! empty($systemFields['instagram_dms']['enabled']))
                                <th class="py-3 px-2 border-r border-slate-200 min-w-[80px]">{{ $systemFields['instagram_dms']['label'] }}</th>
                            @endif
                            @if (! empty($systemFields['facebook_dms']['enabled']))
                                <th class="py-3 px-2 border-r border-slate-200 min-w-[80px]">{{ $systemFields['facebook_dms']['label'] }}</th>
                            @endif
                            @if (! empty($systemFields['linkedin_dms']['enabled']))
                                <th class="py-3 px-2 border-r border-slate-200 min-w-[80px]">{{ $systemFields['linkedin_dms']['label'] }}</th>
                            @endif
                            @if (! empty($systemFields['whatsapp_dms']['enabled']))
                                <th class="py-3 px-2 border-r border-slate-200 min-w-[80px]">{{ $systemFields['whatsapp_dms']['label'] }}</th>
                            @endif
                            @if (! empty($systemFields['linkedin_posts']['enabled']))
                                <th class="py-3 px-2 border-r border-slate-200 bg-sky-50/60 text-sky-900 min-w-[80px]">{{ $systemFields['linkedin_posts']['label'] }}</th>
                            @endif
                            @if (! empty($systemFields['instagram_posts']['enabled']))
                                <th class="py-3 px-2 border-r border-slate-200 bg-purple-50/60 text-purple-900 min-w-[80px]">{{ $systemFields['instagram_posts']['label'] }}</th>
                            @endif
                            @if (! empty($systemFields['facebook_group_actions']['enabled']))
                                <th class="py-3 px-2 border-r border-slate-200 min-w-[90px]">{{ $systemFields['facebook_group_actions']['label'] }}</th>
                            @endif
                            @if (! empty($systemFields['facebook_group_dms']['enabled']))
                                <th class="py-3 px-2 border-r border-slate-200 min-w-[90px]">{{ $systemFields['facebook_group_dms']['label'] }}</th>
                            @endif
                            @if (! empty($systemFields['linkedin_connections']['enabled']))
                                <th class="py-3 px-2 border-r border-slate-200 bg-brand-50/60 text-brand-800 min-w-[85px]">{{ $systemFields['linkedin_connections']['label'] }}</th>
                            @endif

                            <!-- Dynamic Custom Fields -->
                            @foreach ($customFields as $customField)
                                @if (! empty($customField['enabled']))
                                    <th class="py-3 px-2 border-r border-slate-200 bg-emerald-50/60 text-emerald-900 min-w-[85px]" title="{{ $customField['label'] }} (Dynamic Field)">
                                        <div class="flex items-center justify-center gap-1">
                                            <span>{{ $customField['label'] }}</span>
                                            <span class="text-[8px] px-1 py-0.2 rounded bg-emerald-200/80 text-emerald-800 font-normal uppercase">{{ $customField['type'] }}</span>
                                        </div>
                                    </th>
                                @endif
                            @endforeach

                            <th class="py-3 px-4 border-r border-slate-200 text-left min-w-[240px]">Remarks</th>
                            <th class="py-3 px-3 border-r border-slate-200 bg-amber-50 text-amber-950 font-black min-w-[100px]">Social / Outreach Activities</th>
                            <th class="py-3 px-2 min-w-[65px] bg-slate-50">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-medium">
                        @foreach ($daysInMonth as $day)
                            @php
                                $isSunday = $day['is_sunday'];
                                $hasData = $day['has_report'];
                                $rowBg = $isSunday 
                                    ? 'bg-rose-50/40 text-slate-400' 
                                    : ($hasData ? 'hover:bg-slate-50/80 transition bg-white' : 'hover:bg-slate-50/60 text-slate-400 bg-white');
                            @endphp
                            <tr class="{{ $rowBg }} text-center group">
                                <!-- Date (Sticky) -->
                                <td class="py-2.5 px-3 border-r border-slate-200 text-left font-bold text-slate-900 whitespace-nowrap sticky left-0 {{ $isSunday ? 'bg-rose-50/80' : 'bg-white' }} group-hover:bg-slate-50 z-10">
                                    <div class="flex items-center gap-1.5">
                                        <span>{{ $day['formatted_date'] }}</span>
                                        @if ($isSunday)
                                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-rose-100 text-rose-700 font-semibold border border-rose-200">Sun</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Calls -->
                                @if (! empty($systemFields['calls']['enabled']))
                                    <td class="py-2.5 px-2 border-r border-slate-200 {{ $day['calls'] > 0 ? 'font-bold text-slate-900 bg-blue-50/30' : 'text-slate-400' }}">
                                        {{ $day['calls'] }}
                                    </td>
                                @endif

                                <!-- Instagram DMs -->
                                @if (! empty($systemFields['instagram_dms']['enabled']))
                                    <td class="py-2.5 px-2 border-r border-slate-200 {{ $day['instagram_dms'] > 0 ? 'font-bold text-pink-600' : 'text-slate-400' }}">
                                        {{ $day['instagram_dms'] }}
                                    </td>
                                @endif

                                <!-- Facebook DMs -->
                                @if (! empty($systemFields['facebook_dms']['enabled']))
                                    <td class="py-2.5 px-2 border-r border-slate-200 {{ $day['facebook_dms'] > 0 ? 'font-bold text-blue-600' : 'text-slate-400' }}">
                                        {{ $day['facebook_dms'] }}
                                    </td>
                                @endif

                                <!-- LinkedIn DMs -->
                                @if (! empty($systemFields['linkedin_dms']['enabled']))
                                    <td class="py-2.5 px-2 border-r border-slate-200 {{ $day['linkedin_dms'] > 0 ? 'font-bold text-cyan-700' : 'text-slate-400' }}">
                                        {{ $day['linkedin_dms'] }}
                                    </td>
                                @endif

                                <!-- WhatsApp DMs -->
                                @if (! empty($systemFields['whatsapp_dms']['enabled']))
                                    <td class="py-2.5 px-2 border-r border-slate-200 {{ $day['whatsapp_dms'] > 0 ? 'font-bold text-emerald-600' : 'text-slate-400' }}">
                                        {{ $day['whatsapp_dms'] }}
                                    </td>
                                @endif

                                <!-- LinkedIn Posts -->
                                @if (! empty($systemFields['linkedin_posts']['enabled']))
                                    <td class="py-2.5 px-2 border-r border-slate-200 bg-sky-50/20 {{ $day['linkedin_posts'] > 0 ? 'font-bold text-sky-800' : 'text-slate-400' }}">
                                        {{ $day['linkedin_posts'] }}
                                    </td>
                                @endif

                                <!-- Instagram Posts -->
                                @if (! empty($systemFields['instagram_posts']['enabled']))
                                    <td class="py-2.5 px-2 border-r border-slate-200 bg-purple-50/20 {{ $day['instagram_posts'] > 0 ? 'font-bold text-purple-800' : 'text-slate-400' }}">
                                        {{ $day['instagram_posts'] }}
                                    </td>
                                @endif

                                <!-- Facebook Group Actions -->
                                @if (! empty($systemFields['facebook_group_actions']['enabled']))
                                    <td class="py-2.5 px-2 border-r border-slate-200 {{ $day['facebook_group_actions'] > 0 ? 'font-bold text-brand-600' : 'text-slate-400' }}">
                                        {{ $day['facebook_group_actions'] }}
                                    </td>
                                @endif

                                <!-- Facebook Group DMs -->
                                @if (! empty($systemFields['facebook_group_dms']['enabled']))
                                    <td class="py-2.5 px-2 border-r border-slate-200 {{ $day['facebook_group_dms'] > 0 ? 'font-bold text-brand-600' : 'text-slate-400' }}">
                                        {{ $day['facebook_group_dms'] }}
                                    </td>
                                @endif

                                <!-- LinkedIn Connections -->
                                @if (! empty($systemFields['linkedin_connections']['enabled']))
                                    <td class="py-2.5 px-2 border-r border-slate-200 bg-brand-50/20 {{ $day['linkedin_connections'] > 0 ? 'font-bold text-brand-700' : 'text-slate-400' }}">
                                        {{ $day['linkedin_connections'] }}
                                    </td>
                                @endif

                                <!-- Dynamic Custom Fields Data -->
                                @foreach ($customFields as $customField)
                                    @if (! empty($customField['enabled']))
                                        @php
                                            $val = $day['custom_fields'][$customField['key']] ?? '';
                                            $isNumeric = $customField['type'] === 'number';
                                        @endphp
                                        <td class="py-2.5 px-2 border-r border-slate-200 bg-emerald-50/10 {{ ($isNumeric && $val > 0) ? 'font-bold text-emerald-800' : 'text-slate-500' }}">
                                            {{ $val !== '' ? $val : '-' }}
                                        </td>
                                    @endif
                                @endforeach

                                <!-- Remarks -->
                                <td class="py-2.5 px-4 border-r border-slate-200 text-left text-xs max-w-[280px] truncate" title="{{ $day['remarks'] }}">
                                    @if ($day['remarks'])
                                        <span class="{{ $day['remarks'] === 'No report' ? 'text-slate-400 italic' : 'text-slate-700' }}">
                                            {{ $day['remarks'] }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 italic">-</span>
                                    @endif
                                </td>

                                <!-- Social / Outreach Activities (Calculated) -->
                                <td class="py-2.5 px-3 border-r border-slate-200 font-black text-xs {{ $day['social_activities'] > 0 ? 'text-amber-900 bg-amber-50' : 'text-slate-400' }}">
                                    {{ $day['social_activities'] }}
                                </td>

                                <!-- Action Button -->
                                <td class="py-2.5 px-2 whitespace-nowrap bg-white group-hover:bg-slate-50">
                                    @if ($hasData)
                                        @if (Auth::user()->isSuperAdmin())
                                            <button type="button" 
                                                    onclick="openEditDayModal(@js($day))"
                                                    class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-500 hover:text-white text-brand-600 text-[10px] font-bold border border-brand-200/80 transition">
                                                Edit
                                            </button>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200/60" title="Report submitted. Only Superadmin can edit.">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>Logged</span>
                                            </span>
                                        @endif
                                    @else
                                        <button type="button" 
                                                onclick="openEditDayModal(@js($day))"
                                                class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-brand-500 hover:text-white text-slate-700 text-[10px] font-bold transition">
                                            + Log
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <!-- Sticky Footer: TOTAL -->
                    <tfoot>
                        <tr class="bg-slate-100 text-slate-900 font-black text-xs border-t-2 border-brand-500 text-center shadow-xs">
                            <td class="py-3 px-3 border-r border-slate-200 text-left tracking-wider uppercase sticky left-0 bg-slate-100 z-10 text-brand-600">
                                TOTAL
                            </td>
                            @if (! empty($systemFields['calls']['enabled']))
                                <td class="py-3 px-2 border-r border-slate-200 bg-blue-100/70 text-blue-900 text-sm">
                                    {{ $totals['calls'] }}
                                </td>
                            @endif
                            @if (! empty($systemFields['instagram_dms']['enabled']))
                                <td class="py-3 px-2 border-r border-slate-200 text-pink-700">
                                    {{ $totals['instagram_dms'] }}
                                </td>
                            @endif
                            @if (! empty($systemFields['facebook_dms']['enabled']))
                                <td class="py-3 px-2 border-r border-slate-200 text-blue-700">
                                    {{ $totals['facebook_dms'] }}
                                </td>
                            @endif
                            @if (! empty($systemFields['linkedin_dms']['enabled']))
                                <td class="py-3 px-2 border-r border-slate-200 text-cyan-800">
                                    {{ $totals['linkedin_dms'] }}
                                </td>
                            @endif
                            @if (! empty($systemFields['whatsapp_dms']['enabled']))
                                <td class="py-3 px-2 border-r border-slate-200 text-emerald-700">
                                    {{ $totals['whatsapp_dms'] }}
                                </td>
                            @endif
                            @if (! empty($systemFields['linkedin_posts']['enabled']))
                                <td class="py-3 px-2 border-r border-slate-200 bg-sky-100/70 text-sky-900">
                                    {{ $totals['linkedin_posts'] }}
                                </td>
                            @endif
                            @if (! empty($systemFields['instagram_posts']['enabled']))
                                <td class="py-3 px-2 border-r border-slate-200 bg-purple-100/70 text-purple-900">
                                    {{ $totals['instagram_posts'] }}
                                </td>
                            @endif
                            @if (! empty($systemFields['facebook_group_actions']['enabled']))
                                <td class="py-3 px-2 border-r border-slate-200 text-brand-700">
                                    {{ $totals['facebook_group_actions'] }}
                                </td>
                            @endif
                            @if (! empty($systemFields['facebook_group_dms']['enabled']))
                                <td class="py-3 px-2 border-r border-slate-200 text-brand-700">
                                    {{ $totals['facebook_group_dms'] }}
                                </td>
                            @endif
                            @if (! empty($systemFields['linkedin_connections']['enabled']))
                                <td class="py-3 px-2 border-r border-slate-200 bg-brand-100/70 text-brand-800">
                                    {{ $totals['linkedin_connections'] }}
                                </td>
                            @endif

                            @foreach ($customFields as $customField)
                                @if (! empty($customField['enabled']))
                                    <td class="py-3 px-2 border-r border-slate-200 bg-emerald-100/70 text-emerald-900">
                                        {{ $customField['type'] === 'number' ? ($totals['custom_fields'][$customField['key']] ?? 0) : '-' }}
                                    </td>
                                @endif
                            @endforeach

                            <td class="py-3 px-4 border-r border-slate-200 text-left text-slate-600 font-semibold">
                                {{ $totals['logged_days_count'] }} days logged in month
                            </td>
                            <td class="py-3 px-3 border-r border-slate-200 bg-amber-100 text-amber-950 text-sm font-black">
                                {{ $totals['social_activities'] }}
                            </td>
                            <td class="py-3 px-2 bg-slate-100">
                                -
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    @else
        <!-- ========================================== -->
        <!-- TAB 2: SUMMARY KPI DASHBOARD (WHITE THEME) -->
        <!-- ========================================== -->
        <div class="space-y-6">
            <!-- Top Metric Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Total Calls & Average -->
                <div class="bg-white border border-slate-200/90 p-5 rounded-3xl shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Phone / Tele Calls</span>
                        <div class="w-9 h-9 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-slate-900 mt-3">{{ number_format($totals['calls']) }}</div>
                    <div class="flex items-center gap-2 mt-2 text-xs text-slate-500">
                        <span class="text-blue-700 font-bold">{{ $kpiData['avg_calls_per_day'] }}</span> avg calls/active day
                    </div>
                </div>

                <!-- Total Social & Outreach Activities -->
                <div class="bg-white border border-slate-200/90 p-5 rounded-3xl shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Outreach Touchpoints</span>
                        <div class="w-9 h-9 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-amber-600 mt-3">{{ number_format($totals['social_activities']) }}</div>
                    <div class="flex items-center gap-2 mt-2 text-xs text-slate-500">
                        <span class="text-amber-700 font-bold">{{ $kpiData['avg_social_per_day'] }}</span> actions / day
                    </div>
                </div>

                <!-- Content Output (Posts) -->
                <div class="bg-white border border-slate-200/90 p-5 rounded-3xl shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Social Posts Created</span>
                        <div class="w-9 h-9 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-purple-700 mt-3">{{ $kpiData['total_posts'] }}</div>
                    <div class="flex items-center gap-2 mt-2 text-xs text-slate-500">
                        <span>{{ $totals['linkedin_posts'] }} LinkedIn + {{ $totals['instagram_posts'] }} IG</span>
                    </div>
                </div>

                <!-- Network & Connections Growth -->
                <div class="bg-white border border-slate-200/90 p-5 rounded-3xl shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">LinkedIn Connections</span>
                        <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-emerald-700 mt-3">{{ $totals['linkedin_connections'] }}</div>
                    <div class="flex items-center gap-2 mt-2 text-xs text-slate-500">
                        <span>Direct outreach invites</span>
                    </div>
                </div>
            </div>

            <!-- KPI Analytics Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Target vs Actuals Progress -->
                <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-brand-500"></span>
                                Target vs Actuals (Calls)
                            </h3>
                            <span class="text-xs font-bold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-md border border-brand-100">
                                {{ $kpiData['call_achievement_rate'] }}%
                            </span>
                        </div>

                        <p class="text-xs text-slate-500 mb-6">
                            Monthly benchmark is 50 calls/working day ({{ $kpiData['monthly_target_calls'] }} target calls across {{ $kpiData['total_working_days'] }} working days).
                        </p>

                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-100 rounded-full h-3 border border-slate-200/60 overflow-hidden mb-3">
                            <div class="bg-gradient-to-r from-brand-500 to-emerald-500 h-full rounded-full transition-all duration-500" 
                                 style="width: {{ min(100, $kpiData['call_achievement_rate']) }}%"></div>
                        </div>

                        <div class="flex justify-between text-xs text-slate-600 font-medium">
                            <span>Current: <strong class="text-slate-900">{{ $totals['calls'] }}</strong></span>
                            <span>Target: <strong class="text-slate-900">{{ $kpiData['monthly_target_calls'] }}</strong></span>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-500">Reporting Consistency</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ $kpiData['consistency_rate'] }}% ({{ $totals['logged_days_count'] }}/{{ $kpiData['total_working_days'] }} days)
                        </span>
                    </div>
                </div>

                <!-- Outreach Channel Distribution -->
                <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-xs lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Outreach Channel Breakdown
                        </h3>
                        <span class="text-xs text-slate-500 font-semibold">{{ $totals['social_activities'] }} total interactions</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                        <!-- Instagram -->
                        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-3.5">
                            <div class="text-xs text-pink-700 font-bold">Instagram</div>
                            <div class="text-xl font-black text-slate-900 mt-1">{{ $totals['instagram_dms'] }} DMs</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $totals['instagram_posts'] }} posts created</div>
                        </div>

                        <!-- Facebook -->
                        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-3.5">
                            <div class="text-xs text-blue-700 font-bold">Facebook</div>
                            <div class="text-xl font-black text-slate-900 mt-1">{{ $totals['facebook_dms'] + $totals['facebook_group_dms'] }} DMs</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $totals['facebook_group_actions'] }} group actions</div>
                        </div>

                        <!-- LinkedIn -->
                        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-3.5">
                            <div class="text-xs text-cyan-800 font-bold">LinkedIn</div>
                            <div class="text-xl font-black text-slate-900 mt-1">{{ $totals['linkedin_dms'] }} DMs</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $totals['linkedin_posts'] }} posts & {{ $totals['linkedin_connections'] }} conn</div>
                        </div>

                        <!-- WhatsApp -->
                        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-3.5">
                            <div class="text-xs text-emerald-700 font-bold">WhatsApp</div>
                            <div class="text-xl font-black text-slate-900 mt-1">{{ $totals['whatsapp_dms'] }} DMs</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Direct chat touches</div>
                        </div>
                    </div>

                    <!-- Highlights & Summary Note -->
                    <div class="p-4 rounded-2xl bg-brand-50/70 border border-brand-100 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-brand-500 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-xs text-slate-700 leading-relaxed">
                            <strong class="text-slate-900 font-bold">Productivity Summary:</strong> This month's primary communication channel has been 
                            <span class="text-blue-800 font-bold">Calls ({{ $totals['calls'] }})</span> followed by 
                            <span class="text-pink-700 font-bold">Instagram DMs ({{ $totals['instagram_dms'] }})</span> and 
                            <span class="text-brand-700 font-bold">Facebook Group Outreach ({{ $totals['facebook_group_actions'] }})</span>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- MODAL: ADD / EDIT DAILY WORK REPORT (WHITE)-->
    <!-- ========================================== -->
    <div id="logActivityModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white border border-slate-200 rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-5 sm:px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="text-base font-bold text-slate-900" id="modalTitle">Log Daily Activity</h3>
                    <p class="text-xs text-slate-500" id="modalSubtitle">Record daily calls, social outreaches and posts</p>
                </div>
                <button type="button" onclick="closeLogModal()" class="text-slate-400 hover:text-slate-700 transition p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form method="POST" action="{{ route('work-reports.store') }}" class="p-4 sm:p-6 space-y-4 overflow-y-auto">
                @csrf
                <input type="hidden" name="employee_id" id="modal_employee_id" value="{{ $selectedEmployee?->id }}">
                <input type="hidden" name="tab" value="{{ $activeTab }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="modal_report_date" class="block text-xs font-bold text-slate-700 mb-1">Report Date *</label>
                        <input type="date" 
                               name="report_date" 
                               id="modal_report_date" 
                               required
                               value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    @if (! empty($systemFields['calls']['enabled']))
                        <div>
                            <label for="modal_calls" class="block text-xs font-bold text-blue-700 mb-1">{{ $systemFields['calls']['label'] }} *</label>
                            <input type="number" 
                                   name="calls" 
                                   id="modal_calls" 
                                   min="0" 
                                   placeholder="0" 
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none font-bold">
                        </div>
                    @else
                        <input type="hidden" name="calls" id="modal_calls" value="0">
                    @endif
                </div>

                <!-- Direct Social DMs Grid -->
                @php
                    $dmsEnabled = ! empty($systemFields['instagram_dms']['enabled']) ||
                                  ! empty($systemFields['facebook_dms']['enabled']) ||
                                  ! empty($systemFields['linkedin_dms']['enabled']) ||
                                  ! empty($systemFields['whatsapp_dms']['enabled']);
                @endphp
                @if ($dmsEnabled)
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Direct Messages (DMs)</span>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-2">
                            @if (! empty($systemFields['instagram_dms']['enabled']))
                                <div>
                                    <label class="block text-[11px] font-semibold text-pink-700 mb-1">{{ $systemFields['instagram_dms']['label'] }}</label>
                                    <input type="number" name="instagram_dms" id="modal_instagram_dms" min="0" placeholder="0" class="w-full bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500">
                                </div>
                            @else
                                <input type="hidden" name="instagram_dms" id="modal_instagram_dms" value="0">
                            @endif

                            @if (! empty($systemFields['facebook_dms']['enabled']))
                                <div>
                                    <label class="block text-[11px] font-semibold text-blue-700 mb-1">{{ $systemFields['facebook_dms']['label'] }}</label>
                                    <input type="number" name="facebook_dms" id="modal_facebook_dms" min="0" placeholder="0" class="w-full bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500">
                                </div>
                            @else
                                <input type="hidden" name="facebook_dms" id="modal_facebook_dms" value="0">
                            @endif

                            @if (! empty($systemFields['linkedin_dms']['enabled']))
                                <div>
                                    <label class="block text-[11px] font-semibold text-cyan-800 mb-1">{{ $systemFields['linkedin_dms']['label'] }}</label>
                                    <input type="number" name="linkedin_dms" id="modal_linkedin_dms" min="0" placeholder="0" class="w-full bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500">
                                </div>
                            @else
                                <input type="hidden" name="linkedin_dms" id="modal_linkedin_dms" value="0">
                            @endif

                            @if (! empty($systemFields['whatsapp_dms']['enabled']))
                                <div>
                                    <label class="block text-[11px] font-semibold text-emerald-700 mb-1">{{ $systemFields['whatsapp_dms']['label'] }}</label>
                                    <input type="number" name="whatsapp_dms" id="modal_whatsapp_dms" min="0" placeholder="0" class="w-full bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500">
                                </div>
                            @else
                                <input type="hidden" name="whatsapp_dms" id="modal_whatsapp_dms" value="0">
                            @endif
                        </div>
                    </div>
                @else
                    <input type="hidden" name="instagram_dms" id="modal_instagram_dms" value="0">
                    <input type="hidden" name="facebook_dms" id="modal_facebook_dms" value="0">
                    <input type="hidden" name="linkedin_dms" id="modal_linkedin_dms" value="0">
                    <input type="hidden" name="whatsapp_dms" id="modal_whatsapp_dms" value="0">
                @endif

                <!-- Posts & Group Outreach -->
                @php
                    $postsEnabled = ! empty($systemFields['linkedin_posts']['enabled']) ||
                                    ! empty($systemFields['instagram_posts']['enabled']) ||
                                    ! empty($systemFields['facebook_group_actions']['enabled']) ||
                                    ! empty($systemFields['facebook_group_dms']['enabled']) ||
                                    ! empty($systemFields['linkedin_connections']['enabled']);
                @endphp
                @if ($postsEnabled)
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Posts & Group Activity</span>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-2">
                            @if (! empty($systemFields['linkedin_posts']['enabled']))
                                <div>
                                    <label class="block text-[11px] font-semibold text-sky-800 mb-1">{{ $systemFields['linkedin_posts']['label'] }}</label>
                                    <input type="number" name="linkedin_posts" id="modal_linkedin_posts" min="0" placeholder="0" class="w-full bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500">
                                </div>
                            @else
                                <input type="hidden" name="linkedin_posts" id="modal_linkedin_posts" value="0">
                            @endif

                            @if (! empty($systemFields['instagram_posts']['enabled']))
                                <div>
                                    <label class="block text-[11px] font-semibold text-purple-800 mb-1">{{ $systemFields['instagram_posts']['label'] }}</label>
                                    <input type="number" name="instagram_posts" id="modal_instagram_posts" min="0" placeholder="0" class="w-full bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500">
                                </div>
                            @else
                                <input type="hidden" name="instagram_posts" id="modal_instagram_posts" value="0">
                            @endif

                            @if (! empty($systemFields['facebook_group_actions']['enabled']))
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">{{ $systemFields['facebook_group_actions']['label'] }}</label>
                                    <input type="number" name="facebook_group_actions" id="modal_facebook_group_actions" min="0" placeholder="0" class="w-full bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500">
                                </div>
                            @else
                                <input type="hidden" name="facebook_group_actions" id="modal_facebook_group_actions" value="0">
                            @endif

                            @if (! empty($systemFields['facebook_group_dms']['enabled']))
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">{{ $systemFields['facebook_group_dms']['label'] }}</label>
                                    <input type="number" name="facebook_group_dms" id="modal_facebook_group_dms" min="0" placeholder="0" class="w-full bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500">
                                </div>
                            @else
                                <input type="hidden" name="facebook_group_dms" id="modal_facebook_group_dms" value="0">
                            @endif

                            @if (! empty($systemFields['linkedin_connections']['enabled']))
                                <div>
                                    <label class="block text-[11px] font-semibold text-brand-700 mb-1">{{ $systemFields['linkedin_connections']['label'] }}</label>
                                    <input type="number" name="linkedin_connections" id="modal_linkedin_connections" min="0" placeholder="0" class="w-full bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500">
                                </div>
                            @else
                                <input type="hidden" name="linkedin_connections" id="modal_linkedin_connections" value="0">
                            @endif
                        </div>
                    </div>
                @else
                    <input type="hidden" name="linkedin_posts" id="modal_linkedin_posts" value="0">
                    <input type="hidden" name="instagram_posts" id="modal_instagram_posts" value="0">
                    <input type="hidden" name="facebook_group_actions" id="modal_facebook_group_actions" value="0">
                    <input type="hidden" name="facebook_group_dms" id="modal_facebook_group_dms" value="0">
                    <input type="hidden" name="linkedin_connections" id="modal_linkedin_connections" value="0">
                @endif

                <!-- Dynamic Custom Fields Section (if any defined) -->
                @php
                    $hasActiveCustom = collect($customFields)->contains(fn ($f) => ! empty($f['enabled']));
                @endphp
                @if (count($customFields) > 0)
                    <div class="pt-2 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Additional Dynamic Fields</span>
                            <span class="text-[10px] text-slate-400">Custom KPI parameters</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach ($customFields as $field)
                                @if (! empty($field['enabled']))
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1 truncate" title="{{ $field['label'] }}">
                                            {{ $field['label'] }}
                                            @if (! empty($field['is_social']))
                                                <span class="text-[9px] text-amber-600 font-bold">(Social)</span>
                                            @endif
                                        </label>
                                        @if ($field['type'] === 'number')
                                            <input type="number" 
                                                   name="custom_fields[{{ $field['key'] }}]" 
                                                   id="modal_custom_{{ $field['key'] }}" 
                                                   min="0" 
                                                   placeholder="0" 
                                                   class="w-full bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500">
                                        @else
                                            <input type="text" 
                                                   name="custom_fields[{{ $field['key'] }}]" 
                                                   id="modal_custom_{{ $field['key'] }}" 
                                                   placeholder="Enter value" 
                                                   class="w-full bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500">
                                        @endif
                                    </div>
                                @else
                                    <input type="hidden" name="custom_fields[{{ $field['key'] }}]" id="modal_custom_{{ $field['key'] }}" value="">
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Remarks -->
                <div>
                    <label for="modal_remarks" class="block text-xs font-bold text-slate-700 mb-1">Remarks & Key Highlights</label>
                    <textarea name="remarks" 
                              id="modal_remarks" 
                              rows="2" 
                              placeholder="e.g. 1 Instagram post; 1 Facebook post; 10 LinkedIn connection requests sent..."
                              class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none"></textarea>
                </div>

                <!-- Footer Buttons -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeLogModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        Save Daily Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (Auth::user()->isSuperAdmin())
        <!-- ========================================== -->
        <!-- MODAL: MANAGE CUSTOM DYNAMIC FIELDS (SUPERADMIN) -->
        <!-- ========================================== -->
        <div id="customFieldsModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div class="bg-white border border-slate-200 rounded-3xl w-full max-w-xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <!-- Modal Header -->
                <div class="px-5 sm:px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between shrink-0">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Manage Custom Dynamic Fields</h3>
                        <p class="text-xs text-slate-500">Add or remove dynamic columns in Daily Work Report & KPI sheet</p>
                    </div>
                    <button type="button" onclick="closeCustomFieldsModal()" class="text-slate-400 hover:text-slate-700 transition p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-4 sm:p-6 space-y-6 overflow-y-auto">
                    <!-- System Default Columns Section -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                <span>Default System Columns ({{ count($systemFields) }})</span>
                            </h4>
                            <span class="text-[11px] text-slate-400">Toggle visibility or customize column titles</span>
                        </div>

                        <div class="divide-y divide-slate-100 border border-slate-200 rounded-2xl overflow-hidden max-h-64 overflow-y-auto">
                            @foreach ($systemFields as $sKey => $sField)
                                <div class="p-3 bg-white hover:bg-slate-50/50 transition">
                                    <!-- View Row -->
                                    <div id="field_view_{{ $sKey }}" class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl {{ $sField['enabled'] ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-slate-100 text-slate-400 border-slate-200' }} flex items-center justify-center text-xs font-black shrink-0 border">
                                                {{ strtoupper(substr($sField['label'], 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-slate-900 flex items-center gap-2">
                                                    <span class="{{ ! $sField['enabled'] ? 'line-through text-slate-400' : '' }}">{{ $sField['label'] }}</span>
                                                    @if ($sField['enabled'])
                                                        <span class="text-[9px] px-1.5 py-0.2 rounded-full font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                            Active
                                                        </span>
                                                    @else
                                                        <span class="text-[9px] px-1.5 py-0.2 rounded-full font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                            Hidden
                                                        </span>
                                                    @endif
                                                    <span class="text-[9px] px-1.5 py-0.2 rounded-full font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                        number
                                                    </span>
                                                    @if (! empty($sField['is_social']))
                                                        <span class="text-[9px] px-1.5 py-0.2 rounded-full font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                            + Social
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-[10px] text-slate-400 font-mono">key: {{ $sKey }}</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1.5">
                                            <!-- Rename/Edit Button -->
                                            <button type="button" 
                                                    onclick="startEditField('{{ $sKey }}')" 
                                                    class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-500 hover:text-white text-brand-600 text-[11px] font-bold border border-brand-200 transition">
                                                Edit Title
                                            </button>

                                            <!-- Toggle Active/Hidden Button -->
                                            <form method="POST" action="{{ route('work-reports.custom-fields.toggle', $sKey) }}">
                                                @csrf
                                                @if ($sField['enabled'])
                                                    <button type="submit" 
                                                            title="Hide this column from sheet and entry form" 
                                                            class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 text-slate-600 text-[11px] font-bold border border-slate-200 transition">
                                                        👁️ Hide
                                                    </button>
                                                @else
                                                    <button type="submit" 
                                                            title="Show this column on sheet and entry form" 
                                                            class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 text-[11px] font-bold border border-emerald-200 transition">
                                                        ✓ Show
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Inline Edit Form (Hidden by default) -->
                                    <form id="field_edit_{{ $sKey }}" 
                                          method="POST" 
                                          action="{{ route('work-reports.custom-fields.update', $sKey) }}" 
                                          class="hidden pt-2 mt-2 border-t border-slate-100 space-y-3 bg-slate-50/80 p-3 rounded-xl border">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Display Title / Label *</label>
                                            <input type="text" 
                                                   name="label" 
                                                   value="{{ $sField['label'] }}" 
                                                   required 
                                                   class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500 font-semibold">
                                            <p class="text-[10px] text-slate-400 mt-1">Changes the title displayed in the spreadsheet header and logging form.</p>
                                        </div>

                                        <div class="flex items-center justify-end gap-2 pt-1">
                                            <button type="button" 
                                                    onclick="cancelEditField('{{ $sKey }}')" 
                                                    class="px-2.5 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition">
                                                Cancel
                                            </button>
                                            <button type="submit" 
                                                    class="px-3 py-1 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-xs transition">
                                                Save Title
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Existing Dynamic Custom Fields List -->
                    <div class="pt-4 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                <span>Custom Dynamic Fields ({{ count($customFields) }})</span>
                            </h4>
                            <span class="text-[11px] text-slate-400">Manage user-added dynamic columns</span>
                        </div>

                        @if (empty($customFields))
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-500">
                                No custom fields added yet. Add a new field below to create a dynamic column in the sheet!
                            </div>
                        @else
                            <div class="divide-y divide-slate-100 border border-slate-200 rounded-2xl overflow-hidden max-h-56 overflow-y-auto">
                                @foreach ($customFields as $field)
                                    <div class="p-3 bg-white hover:bg-slate-50/50 transition">
                                        <!-- View Row -->
                                        <div id="field_view_{{ $field['key'] }}" class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-xl {{ ($field['enabled'] ?? true) ? 'bg-brand-50 text-brand-600 border-brand-100' : 'bg-slate-100 text-slate-400 border-slate-200' }} flex items-center justify-center text-xs font-black shrink-0 border">
                                                    {{ strtoupper(substr($field['label'], 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="text-xs font-bold text-slate-900 flex items-center gap-2">
                                                        <span class="{{ ! ($field['enabled'] ?? true) ? 'line-through text-slate-400' : '' }}">{{ $field['label'] }}</span>
                                                        @if ($field['enabled'] ?? true)
                                                            <span class="text-[9px] px-1.5 py-0.2 rounded-full font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                                Active
                                                            </span>
                                                        @else
                                                            <span class="text-[9px] px-1.5 py-0.2 rounded-full font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                                Hidden
                                                            </span>
                                                        @endif
                                                        <span class="text-[9px] px-1.5 py-0.2 rounded-full font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                            {{ $field['type'] }}
                                                        </span>
                                                        @if (! empty($field['is_social']))
                                                            <span class="text-[9px] px-1.5 py-0.2 rounded-full font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                                + Social
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="text-[10px] text-slate-400 font-mono">key: {{ $field['key'] }}</span>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                <button type="button" 
                                                        onclick="startEditField('{{ $field['key'] }}')" 
                                                        class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-500 hover:text-white text-brand-600 text-[11px] font-bold border border-brand-200 transition">
                                                    Edit
                                                </button>

                                                <!-- Toggle Active/Hidden Button -->
                                                <form method="POST" action="{{ route('work-reports.custom-fields.toggle', $field['key']) }}">
                                                    @csrf
                                                    @if ($field['enabled'] ?? true)
                                                        <button type="submit" 
                                                                title="Hide this column from sheet and entry form" 
                                                                class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 text-slate-600 text-[11px] font-bold border border-slate-200 transition">
                                                            👁️ Hide
                                                        </button>
                                                    @else
                                                        <button type="submit" 
                                                                title="Show this column on sheet and entry form" 
                                                                class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 text-[11px] font-bold border border-emerald-200 transition">
                                                            ✓ Show
                                                        </button>
                                                    @endif
                                                </form>

                                                <form method="POST" action="{{ route('work-reports.custom-fields.destroy', $field['key']) }}" onsubmit="return confirm('Are you sure you want to permanently delete the custom field \'{{ $field['label'] }}\'?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-700 text-[11px] font-bold border border-rose-200 transition">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Inline Edit Form (Hidden by default) -->
                                        <form id="field_edit_{{ $field['key'] }}" 
                                              method="POST" 
                                              action="{{ route('work-reports.custom-fields.update', $field['key']) }}" 
                                              class="hidden pt-2 mt-2 border-t border-slate-100 space-y-3 bg-slate-50/80 p-3 rounded-xl border">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Field Title / Label *</label>
                                                    <input type="text" 
                                                           name="label" 
                                                           value="{{ $field['label'] }}" 
                                                           required 
                                                           class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500 font-semibold">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Data Type *</label>
                                                    <select name="type" required class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 focus:ring-1 focus:ring-brand-500 font-medium">
                                                        <option value="number" {{ $field['type'] === 'number' ? 'selected' : '' }}>Number (Calculated in Totals)</option>
                                                        <option value="text" {{ $field['type'] === 'text' ? 'selected' : '' }}>Text (General Note / Status)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between gap-2 pt-1">
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" 
                                                           name="is_social" 
                                                           value="1" 
                                                           {{ ! empty($field['is_social']) ? 'checked' : '' }}
                                                           class="w-3.5 h-3.5 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                                                    <span class="text-[11px] font-semibold text-slate-700">Count towards Social Outreach total</span>
                                                </label>

                                                <div class="flex items-center gap-2">
                                                    <button type="button" 
                                                            onclick="cancelEditField('{{ $field['key'] }}')" 
                                                            class="px-2.5 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" 
                                                            class="px-3 py-1 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-xs transition">
                                                        Update Field
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Add New Custom Field Form -->
                    <div class="pt-4 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Add New Dynamic Field</h4>
                        <form method="POST" action="{{ route('work-reports.custom-fields.store') }}" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label for="field_label" class="block text-xs font-bold text-slate-700 mb-1">Field Label *</label>
                                    <input type="text" 
                                           name="label" 
                                           id="field_label" 
                                           required 
                                           placeholder="e.g. Threads DMs, Cold Emails" 
                                           class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none font-medium">
                                </div>
                                <div>
                                    <label for="field_type" class="block text-xs font-bold text-slate-700 mb-1">Data Type *</label>
                                    <select name="type" 
                                            id="field_type" 
                                            required 
                                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:outline-none font-medium">
                                        <option value="number" selected>Number (Calculated in Totals)</option>
                                        <option value="text">Text (General Note / Status)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <label class="flex items-center gap-2.5 cursor-pointer">
                                    <input type="checkbox" 
                                           name="is_social" 
                                           value="1" 
                                           class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300">
                                    <span class="text-xs font-semibold text-slate-700">Count towards "Social / Outreach Activities" total benchmark</span>
                                </label>
                                <p class="text-[10px] text-slate-400 mt-1 ml-6.5">If checked, this numeric field will automatically be added to the daily & monthly Social Activities calculation.</p>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2">
                                <button type="button" onclick="closeCustomFieldsModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                    Close
                                </button>
                                <button type="submit" class="px-5 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                                    + Add Field to Sheet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- JavaScript to handle modal population -->
    <script>
        const customFieldDefs = @js($customFields);

        function openCustomFieldsModal() {
            const modal = document.getElementById('customFieldsModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeCustomFieldsModal() {
            const modal = document.getElementById('customFieldsModal');
            if (modal) modal.classList.add('hidden');
        }

        function startEditField(key) {
            const viewRow = document.getElementById('field_view_' + key);
            const editForm = document.getElementById('field_edit_' + key);
            if (viewRow) viewRow.classList.add('hidden');
            if (editForm) editForm.classList.remove('hidden');
        }

        function cancelEditField(key) {
            const viewRow = document.getElementById('field_view_' + key);
            const editForm = document.getElementById('field_edit_' + key);
            if (viewRow) viewRow.classList.remove('hidden');
            if (editForm) editForm.classList.add('hidden');
        }

        function openLogModal() {
            document.getElementById('modalTitle').textContent = 'Log Daily Activity';
            document.getElementById('modalSubtitle').textContent = 'Record daily calls, social outreaches and posts';
            document.getElementById('modal_calls').value = '';
            document.getElementById('modal_instagram_dms').value = '';
            document.getElementById('modal_facebook_dms').value = '';
            document.getElementById('modal_linkedin_dms').value = '';
            document.getElementById('modal_whatsapp_dms').value = '';
            document.getElementById('modal_linkedin_posts').value = '';
            document.getElementById('modal_instagram_posts').value = '';
            document.getElementById('modal_facebook_group_actions').value = '';
            document.getElementById('modal_facebook_group_dms').value = '';
            document.getElementById('modal_linkedin_connections').value = '';
            document.getElementById('modal_remarks').value = '';

            // Reset dynamic custom field inputs
            if (Array.isArray(customFieldDefs)) {
                customFieldDefs.forEach(field => {
                    const el = document.getElementById('modal_custom_' + field.key);
                    if (el) {
                        el.value = '';
                    }
                });
            }

            document.getElementById('logActivityModal').classList.remove('hidden');
        }

        function openEditDayModal(day) {
            document.getElementById('modalTitle').textContent = 'Edit Report for ' + day.formatted_date;
            document.getElementById('modalSubtitle').textContent = 'Update daily metrics for ' + day.formatted_date;
            document.getElementById('modal_report_date').value = day.date_string;
            document.getElementById('modal_calls').value = day.calls || 0;
            document.getElementById('modal_instagram_dms').value = day.instagram_dms || 0;
            document.getElementById('modal_facebook_dms').value = day.facebook_dms || 0;
            document.getElementById('modal_linkedin_dms').value = day.linkedin_dms || 0;
            document.getElementById('modal_whatsapp_dms').value = day.whatsapp_dms || 0;
            document.getElementById('modal_linkedin_posts').value = day.linkedin_posts || 0;
            document.getElementById('modal_instagram_posts').value = day.instagram_posts || 0;
            document.getElementById('modal_facebook_group_actions').value = day.facebook_group_actions || 0;
            document.getElementById('modal_facebook_group_dms').value = day.facebook_group_dms || 0;
            document.getElementById('modal_linkedin_connections').value = day.linkedin_connections || 0;
            document.getElementById('modal_remarks').value = (day.remarks === 'Sunday' || day.remarks === 'No report') ? '' : day.remarks;

            // Populate dynamic custom field inputs
            if (Array.isArray(customFieldDefs)) {
                customFieldDefs.forEach(field => {
                    const el = document.getElementById('modal_custom_' + field.key);
                    if (el) {
                        const customVal = (day.custom_fields && day.custom_fields[field.key] !== undefined)
                            ? day.custom_fields[field.key]
                            : '';
                        el.value = customVal;
                    }
                });
            }

            document.getElementById('logActivityModal').classList.remove('hidden');
        }

        function closeLogModal() {
            document.getElementById('logActivityModal').classList.add('hidden');
        }
    </script>
</x-hrms-layout>
