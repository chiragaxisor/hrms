<x-hrms-layout>
    @section('page_title', $isEmployee ? 'Employee Self-Service Portal' : 'Superadmin Dashboard')

    @if ($isEmployee)
        <!-- ========================================================= -->
        <!-- EMPLOYEE SELF-SERVICE DASHBOARD                           -->
        <!-- ========================================================= -->

        <!-- Welcome Banner with Today's Live Clock-In Action -->
        <div class="mb-8 p-6 sm:p-8 bg-[#0B0F19] rounded-3xl text-white shadow-xl border border-slate-800/80 relative overflow-hidden">
            <!-- Subtle Mesh Ambient Background & Check/Grid Pattern matching login screen -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
                <div class="absolute -top-12 -left-12 w-64 h-64 rounded-full bg-gradient-to-br from-brand-600/25 via-orange-600/10 to-transparent blur-3xl"></div>
                <div class="absolute -bottom-12 -right-12 w-64 h-64 rounded-full bg-gradient-to-tl from-slate-800/40 via-brand-500/15 to-transparent blur-3xl"></div>
                <!-- Check / Grid Overlay Pattern (32px x 32px) -->
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff08_1px,transparent_1px),linear-gradient(to_bottom,#ffffff08_1px,transparent_1px)] bg-[size:32px_32px]"></div>
            </div>
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-500/20 text-brand-300 border border-brand-400/30 uppercase tracking-wider">
                            Employee Self Service
                        </span>
                        <span class="text-xs text-slate-300 font-medium">
                            {{ \Carbon\Carbon::now()->format('l, d M Y') }}
                        </span>
                    </div>
                    <h2 class="text-2xl font-black text-white mt-2">
                        Welcome, {{ Auth::user()->name }}! 👋
                    </h2>
                    <p class="text-xs text-slate-300 mt-1 max-w-xl">
                        {{ $employee ? $employee->designation . ' • ' . $employee->department : 'Staff Member' }} 
                        (Code: <span class="font-mono font-bold text-brand-300">{{ $employee->employee_code ?? 'EMP' }}</span>)
                    </p>

                    <!-- Today's Punch Summary text -->
                    <div class="mt-4 flex flex-wrap items-center gap-3 text-xs">
                        @if ($todayAttendance && $todayAttendance->clock_in)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                Clocked In at {{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('h:i A') }}
                            </span>
                            @if ($todayAttendance->clock_out)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-500/20 text-amber-300 font-bold border border-amber-500/30">
                                    Clocked Out at {{ \Carbon\Carbon::parse($todayAttendance->clock_out)->format('h:i A') }} ({{ $todayAttendance->total_hours }} hrs)
                                </span>
                            @elseif ($todayAttendance->break_in && !$todayAttendance->break_out)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-500/20 text-amber-300 font-bold border border-amber-500/30 animate-pulse">
                                    On Break since {{ \Carbon\Carbon::parse($todayAttendance->break_in)->format('h:i A') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30">
                                    Currently Working
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-rose-500/20 text-rose-300 font-bold border border-rose-500/30">
                                <span class="w-2 h-2 rounded-full bg-rose-400 animate-ping"></span>
                                Not Clocked In Yet Today
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Quick Action Buttons on Banner -->
                @if ($employee)
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('attendances.index') }}" 
                           class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white text-xs font-bold shadow-lg shadow-brand-500/30 transition hover:scale-[1.02]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Punch Attendance / Breaks</span>
                        </a>

                        <a href="{{ route('leaves.index') }}" 
                           class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/15 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Request Leave</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Employee Summary Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <!-- Present This Month -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Present This Month</span>
                        <span class="text-3xl font-black text-slate-800 mt-2 block">{{ $employeeStats['present_days'] }} <span class="text-sm font-normal text-slate-400">Days</span></span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-emerald-600 font-semibold gap-1">
                    <span>{{ $employeeStats['total_hours'] }} logged work hours</span>
                </div>
            </div>

            <!-- Leaves & Offs -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Leaves Taken</span>
                        <span class="text-3xl font-black text-amber-500 mt-2 block">{{ $employeeStats['leave_days'] }} <span class="text-sm font-normal text-slate-400">Days</span></span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-slate-500 gap-1">
                    <span>{{ $employeeStats['pending_leaves'] }} pending leave application(s)</span>
                </div>
            </div>

            <!-- Latest Payslip / Salary Status -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Latest Net Salary</span>
                        <span class="text-2xl sm:text-3xl font-black text-brand-600 mt-2 block font-mono">{{ $employeeStats['latest_net_salary'] }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-slate-500 gap-1">
                    @if ($employeeStats['latest_payroll'])
                        <a href="{{ route('payrolls.payslip', $employeeStats['latest_payroll']->id) }}" target="_blank" class="text-brand-600 font-bold hover:underline">
                            View Payslip ({{ \Carbon\Carbon::createFromDate($employeeStats['latest_payroll']->year, $employeeStats['latest_payroll']->month, 1)->format('M Y') }}) →
                        </a>
                    @else
                        <span>No payslip processed yet</span>
                    @endif
                </div>
            </div>

            <!-- Daily Work Activity -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Daily Work Report</span>
                        <span class="text-3xl font-black text-purple-600 mt-2 block">{{ $recentWorkReports->count() }} <span class="text-sm font-normal text-slate-400">Entries</span></span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-slate-500 gap-1">
                    <a href="{{ route('work-reports.index') }}" class="text-purple-600 font-bold hover:underline">
                        Open Work Report & KPIs →
                    </a>
                </div>
            </div>
        </div>

        <!-- Employee Quick Sections Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Recent Work Reports Logged (2 cols) -->
            <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50 to-white">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">My Recent Work Activity (DWR)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Calls, social outreach, and tasks logged in your work report</p>
                    </div>
                    <a href="{{ route('work-reports.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                        View All Entries →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/60 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                <th class="py-3.5 px-6">Date</th>
                                <th class="py-3.5 px-4 text-center">Calls</th>
                                <th class="py-3.5 px-4 text-center">Social Outreach</th>
                                <th class="py-3.5 px-4 text-center">Posts / Groups</th>
                                <th class="py-3.5 px-6">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-normal">
                            @forelse ($recentWorkReports as $report)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-6 font-bold text-slate-800 font-mono">
                                        {{ $report->report_date->format('d M Y') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-brand-600 font-mono">
                                        {{ $report->calls }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono">
                                        {{ $report->social_outreach_activities }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono">
                                        {{ $report->linkedin_posts + $report->instagram_posts + $report->facebook_group_actions }}
                                    </td>
                                    <td class="py-3.5 px-6 text-slate-600 truncate max-w-[200px]" title="{{ $report->remarks }}">
                                        {{ $report->remarks ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-xs text-slate-400">
                                        No recent work activity logged yet. Click below to add your first report.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/40 text-right">
                    <a href="{{ route('work-reports.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-xs shadow-xs transition">
                        <span>+ Log Today's Work Report</span>
                    </a>
                </div>
            </div>

            <!-- Right: Quick Links & Personal Profile (1 col) -->
            <div class="space-y-6">
                <!-- My Profile Card -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>My Employee Information</span>
                    </h3>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Employee Code:</span>
                            <span class="font-bold font-mono text-slate-800">{{ $employee->employee_code ?? 'EMP-NA' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Department:</span>
                            <span class="font-semibold text-slate-800">{{ $employee->department ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Designation:</span>
                            <span class="font-semibold text-slate-800">{{ $employee->designation ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-500">Joining Date:</span>
                            <span class="font-semibold text-slate-800">{{ $employee && $employee->joining_date ? $employee->joining_date->format('d M Y') : '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5">
                            <span class="text-slate-500">Role:</span>
                            <span class="font-bold text-brand-600 uppercase">Employee</span>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100">
                        <a href="{{ route('profile.edit') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                            <span>Change Password & Profile</span>
                        </a>
                    </div>
                </div>

                <!-- Self Service Support Card -->
                <div class="bg-gradient-to-br from-brand-50 to-purple-50 rounded-3xl p-6 border border-brand-100">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-brand-800 mb-1">HR Employee Desk</h4>
                    <p class="text-xs text-slate-600 mb-4">Need to apply for an official leave or check salary breakups? Everything is synchronized in your portal.</p>
                    <div class="space-y-2">
                        <a href="{{ route('leaves.index') }}" class="block px-3.5 py-2 rounded-xl bg-white text-xs font-bold text-slate-800 shadow-xs hover:border-brand-400 border border-slate-200/80 transition">
                            🏖️ Apply for Leave (PL / CL / SL)
                        </a>
                        <a href="{{ route('payrolls.index') }}" class="block px-3.5 py-2 rounded-xl bg-white text-xs font-bold text-slate-800 shadow-xs hover:border-brand-400 border border-slate-200/80 transition">
                            💰 View My Salary Slips
                        </a>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- ========================================================= -->
        <!-- SUPERADMIN & HR ADMIN DASHBOARD (DYNAMIC & REAL-TIME)    -->
        <!-- ========================================================= -->

        <!-- Top Greeting & Live Pulse Banner -->
        <div class="mb-8 p-6 sm:p-8 bg-[#0B0F19] rounded-3xl text-white shadow-xl border border-slate-800/80 relative overflow-hidden">
            <!-- Subtle Mesh Ambient Background & Check/Grid Pattern matching login screen -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
                <div class="absolute -top-16 -left-16 w-80 h-80 rounded-full bg-gradient-to-br from-brand-600/25 via-orange-600/15 to-transparent blur-3xl"></div>
                <div class="absolute -bottom-16 -right-16 w-80 h-80 rounded-full bg-gradient-to-tl from-slate-800/40 via-brand-500/15 to-transparent blur-3xl"></div>
                <!-- Check / Grid Overlay Pattern (32px x 32px) -->
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff08_1px,transparent_1px),linear-gradient(to_bottom,#ffffff08_1px,transparent_1px)] bg-[size:32px_32px]"></div>
            </div>
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-500/20 text-brand-300 border border-brand-400/30 uppercase tracking-wider">
                            Superadmin Command Center
                        </span>
                        <span class="text-xs text-slate-300 font-medium">
                            {{ \Carbon\Carbon::now()->format('l, d M Y') }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold border border-emerald-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Live HRMS
                        </span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-white mt-2">
                        Welcome Back, {{ Auth::user()->name }}! 🚀
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                        Here is your company's live attendance rate ({{ $stats['attendance_rate'] }}%), staff operations, pending leave approvals, monthly payroll summary and daily work KPIs.
                    </p>

                    <!-- Live Today Snapshot Pills -->
                    <div class="mt-4 flex flex-wrap items-center gap-2.5 text-xs">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/10 text-slate-200 font-semibold border border-white/10">
                            <span>👥 Total Active: <strong class="text-white">{{ $stats['active_staff'] }}</strong></span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Present Today: {{ $stats['present_today'] }}
                        </span>
                        @if ($stats['currently_working'] > 0)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-sky-500/20 text-sky-300 font-bold border border-sky-500/30">
                                ⚡ Working Now: {{ $stats['currently_working'] }}
                            </span>
                        @endif
                        @if ($stats['currently_on_break'] > 0)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-500/20 text-amber-300 font-bold border border-amber-500/30">
                                ☕ On Break: {{ $stats['currently_on_break'] }}
                            </span>
                        @endif
                        @if ($stats['pending_leaves_count'] > 0)
                            <a href="{{ route('leaves.index', ['status' => 'Pending']) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-rose-500/30 text-rose-200 font-bold border border-rose-400/40 hover:bg-rose-500/50 transition">
                                <span class="w-2 h-2 rounded-full bg-rose-400 animate-ping"></span>
                                {{ $stats['pending_leaves_count'] }} Leaves Pending Approval
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Quick Action Buttons on Banner -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('employees.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white text-xs font-bold shadow-lg shadow-brand-500/30 transition hover:scale-[1.02]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Add Employee</span>
                    </a>

                    <a href="{{ route('work-reports.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/15 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>KPI Reports</span>
                    </a>

                    <a href="{{ route('payrolls.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Payroll</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 6-Metric Dynamic Statistics Strip -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mb-8">
            <!-- 1. Total Workforce -->
            <a href="{{ route('employees.index') }}" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-brand-400 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Staff Force</span>
                    <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                </div>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 mt-2 block">{{ $stats['total_staff'] }}</span>
                <span class="text-[11px] text-emerald-600 font-semibold mt-1 block">{{ $stats['active_staff'] }} Active</span>
            </a>

            <!-- 2. Present Today -->
            <a href="{{ route('attendances.index') }}" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-emerald-400 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Present Today</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <span class="text-2xl sm:text-3xl font-black text-emerald-600 mt-2 block">{{ $stats['present_today'] }}</span>
                <span class="text-[11px] text-slate-500 font-medium mt-1 block">{{ $stats['attendance_rate'] }}% Turnout</span>
            </a>

            <!-- 3. On Leave -->
            <a href="{{ route('leaves.index') }}" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-amber-400 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-amber-700 uppercase tracking-wider block">On Leave</span>
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                </div>
                <span class="text-2xl sm:text-3xl font-black text-amber-500 mt-2 block">{{ $stats['on_leave'] }}</span>
                <span class="text-[11px] text-slate-500 font-medium mt-1 block">{{ $stats['absent_today'] }} Absent</span>
            </a>

            <!-- 4. Pending Leave Approvals -->
            <a href="{{ route('leaves.index', ['status' => 'Pending']) }}" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-rose-400 hover:shadow-md transition relative">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-rose-700 uppercase tracking-wider block">Pending Leaves</span>
                    @if ($stats['pending_leaves_count'] > 0)
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                    @endif
                </div>
                <span class="text-2xl sm:text-3xl font-black text-rose-600 mt-2 block">{{ $stats['pending_leaves_count'] }}</span>
                <span class="text-[11px] text-slate-500 font-medium mt-1 block">Awaiting Action</span>
            </a>

            <!-- 5. Monthly Payroll Cost -->
            <a href="{{ route('payrolls.index') }}" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-brand-400 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Month Payroll</span>
                    <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                </div>
                <span class="text-lg sm:text-xl font-black text-brand-600 mt-2 block font-mono truncate">{{ $stats['payroll_cost'] }}</span>
                <span class="text-[11px] text-slate-500 font-medium mt-1 block">{{ $stats['payroll_generated_count'] }} slips</span>
            </a>

            <!-- 6. Daily Activity Calls -->
            <a href="{{ route('work-reports.index') }}" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:border-purple-400 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] sm:text-[11px] font-bold text-purple-700 uppercase tracking-wider block">Today Calls</span>
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                </div>
                <span class="text-2xl sm:text-3xl font-black text-purple-600 mt-2 block">{{ $stats['today_calls'] }}</span>
                <span class="text-[11px] text-slate-500 font-medium mt-1 block">{{ $stats['today_social'] }} Social touches</span>
            </a>
        </div>

        <!-- Main Multi-Widget Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 mb-8">
            
            <!-- Left Side: Personnel Directory & Recent Clock-In Activity (2 cols) -->
            <div class="lg:col-span-2 space-y-6 sm:space-y-8">

                <!-- Widget 1: Live Today's Clock-Ins & Shift Status -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-gradient-to-b from-slate-50/70 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900">Today's Live Punch & Attendance</h3>
                                <p class="text-[11px] text-slate-500">Real-time shift clock-ins and current working status</p>
                            </div>
                        </div>
                        <a href="{{ route('attendances.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                            View All ({{ $stats['present_today'] }}) →
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    <th class="py-3 px-5 sm:px-6">Employee</th>
                                    <th class="py-3 px-4">Clock In</th>
                                    <th class="py-3 px-4">Clock Out</th>
                                    <th class="py-3 px-4">Work Hours</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($recentPunches as $punch)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-5 sm:px-6">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 font-bold text-xs flex items-center justify-center shrink-0">
                                                    {{ substr($punch->employee?->employee_name ?? 'NA', 0, 2) }}
                                                </div>
                                                <div>
                                                    <span class="font-bold text-slate-800 block">{{ $punch->employee?->employee_name ?? 'N/A' }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">{{ $punch->employee?->department ?? 'General' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 font-mono font-bold text-emerald-600">
                                            {{ $punch->clock_in ? \Carbon\Carbon::parse($punch->clock_in)->format('h:i A') : '-' }}
                                        </td>
                                        <td class="py-3 px-4 font-mono font-bold text-amber-600">
                                            {{ $punch->clock_out ? \Carbon\Carbon::parse($punch->clock_out)->format('h:i A') : '-' }}
                                        </td>
                                        <td class="py-3 px-4 font-mono text-slate-700 font-semibold">
                                            {{ $punch->total_hours ? $punch->total_hours . ' hrs' : '-' }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($punch->clock_out)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">Completed</span>
                                            @elseif ($punch->break_in && !$punch->break_out)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 animate-pulse">On Break</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Working</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-xs text-slate-400">
                                            No attendance records clocked in yet for today.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Widget 2: Company Personnel Directory (Dynamic Active Staff) -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-gradient-to-b from-slate-50/70 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900">Recent Employee Additions</h3>
                                <p class="text-[11px] text-slate-500">Recently onboarded workforce personnel</p>
                            </div>
                        </div>
                        <a href="{{ route('employees.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                            Directory ({{ $stats['total_staff'] }}) →
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    <th class="py-3 px-5 sm:px-6">Name & Code</th>
                                    <th class="py-3 px-4">Department</th>
                                    <th class="py-3 px-4">Designation</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Profile</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($recentEmployees as $emp)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3.5 px-5 sm:px-6 flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-500 to-brand-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                                {{ substr($emp->employee_name, 0, 2) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('employees.show', $emp) }}" class="font-bold text-slate-800 hover:text-brand-600 block">
                                                    {{ $emp->employee_name }}
                                                </a>
                                                <span class="text-[10px] text-slate-400 font-mono">{{ $emp->employee_code }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 font-semibold text-slate-700">
                                            {{ $emp->department ?: '-' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600">
                                            {{ $emp->designation ?: '-' }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $emp->status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                                ● {{ $emp->status }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
                                            <a href="{{ route('employees.show', $emp) }}" class="text-brand-600 hover:text-brand-700 font-semibold text-xs">
                                                View →
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-xs text-slate-400">
                                            No employee records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Widget 3: Today's Daily Work Reports Activity Stream -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-gradient-to-b from-slate-50/70 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900">Today's Daily Work & KPI Logs</h3>
                                <p class="text-[11px] text-slate-500">Live submission of calls and outreach activities for {{ \Carbon\Carbon::now()->format('d M') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('work-reports.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                            KPI Register →
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    <th class="py-3 px-5 sm:px-6">Employee</th>
                                    <th class="py-3 px-4 text-center">Calls</th>
                                    <th class="py-3 px-4 text-center">Social / Outreach</th>
                                    <th class="py-3 px-4">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($todayWorkReports as $report)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-5 sm:px-6 font-bold text-slate-800">
                                            {{ $report->employee?->employee_name ?? 'N/A' }}
                                        </td>
                                        <td class="py-3 px-4 text-center font-bold text-blue-700 font-mono">
                                            {{ $report->calls }}
                                        </td>
                                        <td class="py-3 px-4 text-center font-bold text-pink-700 font-mono">
                                            {{ $report->social_outreach_activities }}
                                        </td>
                                        <td class="py-3 px-4 text-slate-600 truncate max-w-[200px]" title="{{ $report->remarks }}">
                                            {{ $report->remarks ?: '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-xs text-slate-400">
                                            No daily work reports logged yet today.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right Side: Action Center, Pending Leaves & Department Breakdown (1 col) -->
            <div class="space-y-6 sm:space-y-8">

                <!-- 1. Pending Leave Approvals Card -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs">
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $stats['pending_leaves_count'] > 0 ? 'bg-rose-500 animate-ping' : 'bg-emerald-500' }}"></span>
                            <h3 class="text-sm font-bold text-slate-900">Pending Leave Requests</h3>
                        </div>
                        <a href="{{ route('leaves.index', ['status' => 'Pending']) }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                            Manage ({{ $stats['pending_leaves_count'] }})
                        </a>
                    </div>

                    <div class="space-y-3 text-xs">
                        @forelse ($pendingLeaves as $leave)
                            <div class="p-3 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-800">{{ $leave->employee?->employee_name ?? 'Employee' }}</span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800">{{ $leave->leave_type }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 flex items-center justify-between">
                                    <span>{{ $leave->start_date ? $leave->start_date->format('d M') : '' }} - {{ $leave->end_date ? $leave->end_date->format('d M Y') : '' }}</span>
                                    <span class="font-bold text-brand-600">{{ $leave->total_days }} day(s)</span>
                                </div>
                                <div class="text-[11px] text-slate-600 italic truncate" title="{{ $leave->reason }}">
                                    "{{ $leave->reason }}"
                                </div>
                                <div class="pt-1 flex items-center gap-2">
                                    <form method="POST" action="{{ route('leaves.approve', $leave) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] transition">
                                            ✓ Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('leaves.reject', $leave) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full py-1.5 rounded-xl bg-slate-200 hover:bg-rose-100 hover:text-rose-700 text-slate-700 font-bold text-[10px] transition">
                                            ✕ Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-slate-400">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2 font-bold">
                                    ✓
                                </div>
                                <p class="font-semibold text-slate-700 text-xs">All Clear!</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">No pending leave requests awaiting approval.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- 2. Department Allocation Breakdown Card -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs">
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Department Workforce</h3>
                        <a href="{{ route('departments.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                            Manage ({{ $stats['departments'] }})
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($departmentStats as $dept)
                            @php
                                $pct = $stats['total_staff'] > 0 ? round(($dept->count / $stats['total_staff']) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                    <span class="text-slate-700">{{ $dept->department }}</span>
                                    <span class="text-brand-600 font-mono">{{ $dept->count }} staff ({{ $pct }}%)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-brand-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-4 text-center">No departments assigned yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- 3. System & Quick Controls Card -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>System Administration</span>
                    </h3>

                    <div class="space-y-2.5 text-xs">
                        <a href="{{ route('settings.index') }}" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-brand-50/60 transition group border border-slate-100">
                            <span class="font-semibold text-slate-700 group-hover:text-brand-600">Company & Shift Settings</span>
                            <span class="text-slate-400 group-hover:text-brand-600 font-bold">→</span>
                        </a>
                        <a href="{{ route('departments.index') }}" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-brand-50/60 transition group border border-slate-100">
                            <span class="font-semibold text-slate-700 group-hover:text-brand-600">Departments & Designations</span>
                            <span class="text-slate-400 group-hover:text-brand-600 font-bold">→</span>
                        </a>
                        <a href="{{ route('payrolls.index') }}" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-brand-50/60 transition group border border-slate-100">
                            <span class="font-semibold text-slate-700 group-hover:text-brand-600">Monthly Payroll Generator</span>
                            <span class="text-slate-400 group-hover:text-brand-600 font-bold">→</span>
                        </a>
                        <a href="{{ route('work-reports.index') }}" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-brand-50/60 transition group border border-slate-100">
                            <span class="font-semibold text-slate-700 group-hover:text-brand-600">Dynamic KPI Parameters</span>
                            <span class="text-slate-400 group-hover:text-brand-600 font-bold">→</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    @endif

</x-hrms-layout>
