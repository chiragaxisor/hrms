<x-hrms-layout>
    @section('page_title', 'Daily Attendance Management')

    <div x-data="attendanceManager()">


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
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top Clock-In / Clock-Out Punch Banner (If linked to an employee profile) -->
    @if ($myEmployee)
        <div class="mb-6 p-5 rounded-3xl bg-gradient-to-r from-[#0B0F19] via-slate-900 to-[#0B0F19] text-white shadow-xl shadow-slate-900/10 border border-slate-800/40 relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-brand-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-md flex items-center justify-center text-brand-300 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-brand-300 uppercase tracking-wider">My Shift Punch</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-slate-200">{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</span>
                        </div>
                        <h3 class="text-base font-bold text-white mt-0.5">{{ $myEmployee->employee_name }} ({{ $myEmployee->employee_code }})</h3>
                        <p class="text-xs text-slate-300 mt-0.5 flex flex-wrap items-center gap-2">
                            @if ($myTodayAttendance && $myTodayAttendance->clock_in)
                                <span>Punched IN at <span class="font-mono font-bold text-emerald-400">{{ \Carbon\Carbon::parse($myTodayAttendance->clock_in)->format('h:i A') }}</span></span>
                                @if ($myTodayAttendance->clock_out)
                                    <span>• Punched OUT at <span class="font-mono font-bold text-amber-400">{{ \Carbon\Carbon::parse($myTodayAttendance->clock_out)->format('h:i A') }}</span></span>
                                    <span>• <span class="font-mono font-bold text-brand-300">Total Work: {{ $myTodayAttendance->total_hours }} hrs</span></span>
                                @elseif ($myTodayAttendance->break_in && !$myTodayAttendance->break_out)
                                    <span>• <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-500/30 text-amber-300 font-bold animate-pulse border border-amber-400/30">● On Break since {{ \Carbon\Carbon::parse($myTodayAttendance->break_in)->format('h:i A') }}</span></span>
                                @else
                                    <span>• <span class="text-emerald-300 font-semibold animate-pulse">● Currently Working</span></span>
                                @endif
                                @if ($myTodayAttendance->break_minutes > 0)
                                    <span class="text-slate-400 font-normal">(Break: {{ $myTodayAttendance->break_minutes }} mins)</span>
                                @endif
                            @else
                                <span>You have not punched in for today yet.</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                    @if (!$myTodayAttendance || !$myTodayAttendance->clock_in)
                        <button type="button" 
                                @click="openPunchModal('clock_in', 'Clock-In (Punch In)', 'Office', 'bg-emerald-500 hover:bg-emerald-600')"
                                class="w-full sm:w-auto justify-center inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-500/20 transition hover:scale-[1.02]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                            <span>📸 Face Scan Clock-In</span>
                        </button>
                    @elseif (!$myTodayAttendance->clock_out)
                        <!-- Break In / Break Out Action -->
                        @if ($myTodayAttendance->break_in && !$myTodayAttendance->break_out)
                            <!-- Currently on Break -> Show Break-Out (Resume Work) -->
                            <button type="button" 
                                    @click="openPunchModal('break_out', 'Break-Out (Resume Work)', 'Office', 'bg-sky-500 hover:bg-sky-600')"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 active:bg-sky-700 text-white text-xs font-bold shadow-lg shadow-sky-500/20 transition hover:scale-[1.02]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>📸 Face Scan Break-Out</span>
                            </button>
                        @else
                            <!-- Working -> Can Start Break -->
                            <button type="button" 
                                    @click="openPunchModal('break_in', 'Break-In (Tea/Lunch)', 'Office', 'bg-amber-500 hover:bg-amber-600')"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-500/30 hover:bg-brand-500/50 text-brand-200 hover:text-white border border-brand-400/40 text-xs font-bold transition hover:scale-[1.02]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>📸 Face Scan Break-In</span>
                            </button>
                        @endif

                        <!-- Final Clock Out -->
                        <button type="button" 
                                @click="openPunchModal('clock_out', 'Clock-Out (Punch Out)', 'Office', 'bg-rose-500 hover:bg-rose-600')"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white text-xs font-bold shadow-lg shadow-amber-500/20 transition hover:scale-[1.02]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                            <span>📸 Face Scan Clock-Out</span>
                        </button>
                    @else
                        <div class="px-4 py-2 rounded-xl bg-white/10 text-xs font-semibold text-slate-200 flex items-center gap-2 border border-white/10">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span>Shift Completed Today</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Metric Counter Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mb-6">
        <!-- Total Staff -->
        <a href="{{ route('attendances.index', ['date' => $date]) }}" class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs hover:border-brand-300 transition">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Active</span>
            <span class="text-xl font-black text-slate-800 mt-1 block">{{ $stats['total_staff'] }}</span>
            <span class="text-[10px] text-slate-500 mt-0.5 block">Staff Members</span>
        </a>

        <!-- Present -->
        <a href="{{ route('attendances.index', ['date' => $date, 'status' => 'Present', 'department' => $departmentFilter]) }}" 
           class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs hover:border-emerald-300 transition {{ $statusFilter === 'Present' ? 'ring-2 ring-emerald-500 bg-emerald-50/20' : '' }}">
            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block">Present</span>
            <span class="text-xl font-black text-emerald-600 mt-1 block">{{ $stats['present'] }}</span>
            <span class="text-[10px] text-emerald-600/80 mt-0.5 block">On Duty</span>
        </a>

        <!-- Absent -->
        <a href="{{ route('attendances.index', ['date' => $date, 'status' => 'Absent', 'department' => $departmentFilter]) }}" 
           class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs hover:border-rose-300 transition {{ $statusFilter === 'Absent' ? 'ring-2 ring-rose-500 bg-rose-50/20' : '' }}">
            <span class="text-[11px] font-bold text-rose-600 uppercase tracking-wider block">Absent</span>
            <span class="text-xl font-black text-rose-600 mt-1 block">{{ $stats['absent'] }}</span>
            <span class="text-[10px] text-rose-600/80 mt-0.5 block">Unreported</span>
        </a>

        <!-- Half Day -->
        <a href="{{ route('attendances.index', ['date' => $date, 'status' => 'Half Day', 'department' => $departmentFilter]) }}" 
           class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs hover:border-amber-300 transition {{ $statusFilter === 'Half Day' ? 'ring-2 ring-amber-500 bg-amber-50/20' : '' }}">
            <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider block">Half Day</span>
            <span class="text-xl font-black text-amber-600 mt-1 block">{{ $stats['half_day'] }}</span>
            <span class="text-[10px] text-amber-600/80 mt-0.5 block">4-5 Hours</span>
        </a>

        <!-- On Leave -->
        <a href="{{ route('attendances.index', ['date' => $date, 'status' => 'Leave', 'department' => $departmentFilter]) }}" 
           class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs hover:border-purple-300 transition {{ $statusFilter === 'Leave' ? 'ring-2 ring-purple-500 bg-purple-50/20' : '' }}">
            <span class="text-[11px] font-bold text-purple-600 uppercase tracking-wider block">On Leave</span>
            <span class="text-xl font-black text-purple-600 mt-1 block">{{ $stats['leave'] }}</span>
            <span class="text-[10px] text-purple-600/80 mt-0.5 block">Approved Leave</span>
        </a>

        <!-- Unmarked -->
        <a href="{{ route('attendances.index', ['date' => $date, 'status' => 'Unmarked', 'department' => $departmentFilter]) }}" 
           class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs hover:border-slate-400 transition {{ $statusFilter === 'Unmarked' ? 'ring-2 ring-slate-500 bg-slate-100/40' : '' }}">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Unmarked</span>
            <span class="text-xl font-black text-slate-700 mt-1 block">{{ $stats['unmarked'] }}</span>
            <span class="text-[10px] text-slate-400 mt-0.5 block">Pending Action</span>
        </a>
    </div>

    <!-- Main Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Header & Date Picker Action Toolbar -->
        <div class="p-6 sm:p-7 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-5 bg-gradient-to-b from-slate-50/60 to-white">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-brand-500 text-white flex items-center justify-center shadow-md shadow-brand-500/20 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Daily Attendance Register</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Track daily shift attendance, punch timings, working hours, and physical presence.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Monthly Report Navigation -->
                <a href="{{ route('attendances.report', ['year' => \Carbon\Carbon::parse($date)->year, 'month' => \Carbon\Carbon::parse($date)->month]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Monthly Register</span>
                </a>

                @if (Auth::user()->isAdmin())
                    <!-- Quick Batch Mark Modal Button -->
                    <button type="button" @click="batchModalOpen = true" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-600 text-xs font-bold border border-brand-200/80 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Quick Batch Mark</span>
                    </button>

                    <!-- One-click Mark All Unmarked as Present -->
                    @if ($stats['unmarked'] > 0)
                        <form method="POST" action="{{ route('attendances.batch') }}" onsubmit="return confirm('Are you sure you want to mark all {{ $stats['unmarked'] }} unmarked employees as Present today?')">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="mode" value="all_unmarked">
                            <input type="hidden" name="status" value="Present">
                            <input type="hidden" name="clock_in" value="09:30">
                            <input type="hidden" name="clock_out" value="18:30">
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span>Mark All Unmarked ({{ $stats['unmarked'] }})</span>
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>

        <!-- Filter & Date Selector Bar -->
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/40">
            <form method="GET" action="{{ route('attendances.index') }}" class="flex flex-col lg:flex-row items-center gap-3">
                <!-- Date Selector with Next / Prev buttons -->
                <div class="flex items-center gap-1.5 w-full lg:w-auto">
                    @php
                        $prevDate = \Carbon\Carbon::parse($date)->subDay()->toDateString();
                        $nextDate = \Carbon\Carbon::parse($date)->addDay()->toDateString();
                        $todayDate = \Carbon\Carbon::today()->toDateString();
                    @endphp
                    <a href="{{ route('attendances.index', array_merge(request()->query(), ['date' => $prevDate])) }}" 
                       class="p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 transition" title="Previous Day">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </a>

                    <div class="relative flex-1 sm:w-48">
                        <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" 
                               class="w-full px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    </div>

                    <a href="{{ route('attendances.index', array_merge(request()->query(), ['date' => $nextDate])) }}" 
                       class="p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 transition" title="Next Day">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>

                    @if ($date !== $todayDate)
                        <a href="{{ route('attendances.index', array_merge(request()->query(), ['date' => $todayDate])) }}" 
                           class="px-3 py-2 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-600 text-xs font-semibold transition">
                            Today
                        </a>
                    @endif
                </div>

                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Search employee by name, code, designation..." 
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white placeholder-slate-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Department Filter -->
                <div class="w-full lg:w-48">
                    <select name="department" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->name }}" {{ $departmentFilter === $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="w-full lg:w-40">
                    <select name="status" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        <option value="">All Statuses</option>
                        <option value="Present" {{ $statusFilter === 'Present' ? 'selected' : '' }}>Present</option>
                        <option value="Absent" {{ $statusFilter === 'Absent' ? 'selected' : '' }}>Absent</option>
                        <option value="Half Day" {{ $statusFilter === 'Half Day' ? 'selected' : '' }}>Half Day</option>
                        <option value="Leave" {{ $statusFilter === 'Leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="Holiday" {{ $statusFilter === 'Holiday' ? 'selected' : '' }}>Holiday</option>
                        <option value="Unmarked" {{ $statusFilter === 'Unmarked' ? 'selected' : '' }}>Unmarked</option>
                    </select>
                </div>

                @if ($search || $departmentFilter || $statusFilter)
                    <a href="{{ route('attendances.index', ['date' => $date]) }}" 
                       class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-200 text-xs transition" title="Clear Filters">
                        ✕ Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Attendance Sheet Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-6">Employee</th>
                        <th class="py-3.5 px-4">Department & Role</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Face / Geo</th>
                        <th class="py-3.5 px-4 text-center">Clock In</th>
                        <th class="py-3.5 px-4 text-center">Break</th>
                        <th class="py-3.5 px-4 text-center">Clock Out</th>
                        <th class="py-3.5 px-4 text-center">Total Hours</th>
                        <th class="py-3.5 px-4">Late / Shift</th>
                        <th class="py-3.5 px-4">Remarks</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($employees as $emp)
                        @php
                            $att = $attendances->get($emp->id);
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition group">
                            <!-- Employee Info -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                                        {{ strtoupper(substr($emp->employee_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('employees.show', $emp) }}" class="font-bold text-slate-900 hover:text-brand-600 transition block">
                                            {{ $emp->employee_name }}
                                        </a>
                                        <div class="flex items-center gap-1.5 text-[11px] text-slate-400 mt-0.5">
                                            <span class="font-mono">{{ $emp->employee_code }}</span>
                                            <span>•</span>
                                            <span>{{ $emp->company_email ?: $emp->personal_email ?: 'No Email' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Department & Designation -->
                            <td class="py-4 px-4">
                                <div class="font-semibold text-slate-800">{{ $emp->department ?: 'General' }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $emp->designation ?: 'Staff' }}</div>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-4 text-center">
                                @if ($att)
                                    @php $badge = $att->status_badge; @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $badge['bg'] }} {{ $badge['text'] }} border {{ $badge['border'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                        <span>{{ $att->status }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Unmarked</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Face Scan / Geo Location Cell -->
                            <td class="py-4 px-4 text-center">
                                @if ($att && ($att->clock_in_photo || $att->latitude))
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if ($att->clock_in_photo)
                                            <button type="button" 
                                                    @click="openPhotoPreview('{{ asset('storage/' . $att->clock_in_photo) }}', '{{ $emp->employee_name }} - Clock In Selfie', '{{ $att->clock_in }}', '{{ $att->distance_meters ? round($att->distance_meters) . 'm from Office' : 'Location Not Recorded' }}')"
                                                    class="w-7 h-7 rounded-lg overflow-hidden border border-brand-300 hover:ring-2 hover:ring-brand-400 transition shrink-0 group/img relative"
                                                    title="Click to view verified selfie photo">
                                                <img src="{{ asset('storage/' . $att->clock_in_photo) }}" alt="Selfie" class="w-full h-full object-cover">
                                            </button>
                                        @endif
                                        @if ($att->latitude)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold {{ ($att->distance_meters ?? 0) <= 250 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}"
                                                  title="GPS: {{ $att->latitude }}, {{ $att->longitude }} ({{ round($att->distance_meters ?? 0) }}m away)">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                {{ round($att->distance_meters ?? 0) }}m
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-300 text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- Clock In -->
                            <td class="py-4 px-4 text-center font-mono text-xs">
                                @if ($att && $att->clock_in)
                                    <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($att->clock_in)->format('h:i A') }}</span>
                                @else
                                    <span class="text-slate-300">--:--</span>
                                @endif
                            </td>

                            <!-- Break In / Out -->
                            <td class="py-4 px-4 text-center font-mono text-xs">
                                @if ($att && ($att->break_in || $att->break_minutes > 0))
                                    <div>
                                        <span class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 font-semibold text-[11px]">
                                            @if ($att->break_in && !$att->break_out)
                                                On Break ({{ \Carbon\Carbon::parse($att->break_in)->format('h:i A') }})
                                            @elseif ($att->break_minutes > 0)
                                                {{ $att->break_minutes }}m Break
                                            @else
                                                {{ \Carbon\Carbon::parse($att->break_in)->format('h:i A') }}
                                            @endif
                                        </span>
                                    </div>
                                @else
                                    <span class="text-slate-300">--:--</span>
                                @endif
                            </td>

                            <!-- Clock Out -->
                            <td class="py-4 px-4 text-center font-mono text-xs">
                                @if ($att && $att->clock_out)
                                    <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($att->clock_out)->format('h:i A') }}</span>
                                @else
                                    <span class="text-slate-300">--:--</span>
                                @endif
                            </td>

                            <!-- Total Hours -->
                            <td class="py-4 px-4 text-center">
                                @if ($att && $att->total_hours > 0)
                                    <span class="font-mono font-bold text-brand-600 bg-brand-50 px-2.5 py-1 rounded-lg border border-brand-100">{{ $att->total_hours }} hrs</span>
                                @else
                                    <span class="text-slate-300 font-mono">-</span>
                                @endif
                            </td>

                            <!-- Late / Shift Details -->
                            <td class="py-4 px-4">
                                @if ($att && $att->late_minutes > 0)
                                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100">
                                        Late by {{ $att->late_minutes }}m
                                    </span>
                                @elseif ($att)
                                    <span class="text-[11px] text-slate-500 font-medium">{{ $att->shift }} ({{ $att->location_type }})</span>
                                @else
                                    <span class="text-[11px] text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Remarks -->
                            <td class="py-4 px-4 text-[11px] text-slate-500 max-w-[160px] truncate">
                                {{ $att->remarks ?? '-' }}
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 text-right">
                                @if (Auth::user()->isAdmin())
                                    <button type="button" 
                                            @click="openEdit({{ $emp->toJson() }}, {{ $att ? $att->toJson() : 'null' }})" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-brand-50 text-slate-700 hover:text-brand-600 text-xs font-semibold transition border border-transparent hover:border-brand-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        <span>Edit</span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Logged</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400 text-xs">
                                No employees found matching the specified filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Attendance Edit / Mark Modal -->
        <div x-show="editModalOpen" x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="editModalOpen = false" 
                 class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5 animate-scale-in">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900" x-text="'Attendance for ' + (selectedEmployee.employee_name || '')"></h3>
                        <p class="text-xs text-slate-500 mt-0.5">Date: {{ \Carbon\Carbon::parse($date)->format('d M Y, l') }}</p>
                    </div>
                    <button type="button" @click="editModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">✕</button>
                </div>

                <form method="POST" action="{{ route('attendances.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="employee_id" :value="selectedEmployee.id">
                    <input type="hidden" name="date" value="{{ $date }}">

                    <!-- Status Selector -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Attendance Status *</label>
                        <select name="status" x-model="selectedAttendance.status" required 
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="Half Day">Half Day</option>
                            <option value="Leave">On Leave</option>
                            <option value="Holiday">Holiday</option>
                            <option value="Week Off">Week Off</option>
                        </select>
                    </div>

                    <!-- Timings row -->
                    <div class="grid grid-cols-2 gap-4" x-show="selectedAttendance.status === 'Present' || selectedAttendance.status === 'Half Day'">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Clock In Time</label>
                            <input type="time" name="clock_in" x-model="selectedAttendance.clock_in" 
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Clock Out Time</label>
                            <input type="time" name="clock_out" x-model="selectedAttendance.clock_out" 
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        </div>
                    </div>

                    <!-- Break Timings row -->
                    <div class="p-3.5 rounded-2xl bg-amber-50/50 border border-amber-200/60 space-y-3" x-show="selectedAttendance.status === 'Present' || selectedAttendance.status === 'Half Day'">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Break Timings (Lunch / Tea)
                            </span>
                            <span class="text-[10px] text-amber-700 font-medium">Deducted from work hours</span>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Break In</label>
                                <input type="time" name="break_in" x-model="selectedAttendance.break_in" 
                                       class="w-full px-2.5 py-1.5 text-xs rounded-xl border border-slate-300 bg-white focus:border-brand-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Break Out</label>
                                <input type="time" name="break_out" x-model="selectedAttendance.break_out" 
                                       class="w-full px-2.5 py-1.5 text-xs rounded-xl border border-slate-300 bg-white focus:border-brand-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Break (Mins)</label>
                                <input type="number" name="break_minutes" x-model="selectedAttendance.break_minutes" placeholder="0" min="0" 
                                       class="w-full px-2.5 py-1.5 text-xs rounded-xl border border-slate-300 bg-white focus:border-brand-500">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Shift</label>
                            <select name="shift" x-model="selectedAttendance.shift" 
                                    class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                                <option value="General">General (09:30 - 18:30)</option>
                                <option value="Morning">Morning</option>
                                <option value="Evening">Evening</option>
                                <option value="Night">Night</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Location Type</label>
                            <select name="location_type" x-model="selectedAttendance.location_type" 
                                    class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                                <option value="Office">Office</option>
                                <option value="Work From Home">Work From Home</option>
                                <option value="Field">Field / Client Site</option>
                            </select>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Remarks / Reason</label>
                        <input type="text" name="remarks" x-model="selectedAttendance.remarks" placeholder="e.g. Permission given, medical leave, late approval..." 
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="editModalOpen = false" 
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                            Save Attendance Record
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Batch Mark Modal -->
        <div x-show="batchModalOpen" x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="batchModalOpen = false" 
                 class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5 animate-scale-in">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Batch Mark Attendance</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Apply bulk status for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                    </div>
                    <button type="button" @click="batchModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">✕</button>
                </div>

                <form method="POST" action="{{ route('attendances.batch') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Apply To Employees</label>
                        <select name="mode" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                            <option value="all_unmarked">All Currently Unmarked Employees ({{ $stats['unmarked'] }})</option>
                            <option value="all">All Active Staff (Overwrite all)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status to Apply *</label>
                        <select name="status" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                            <option value="Present">Present</option>
                            <option value="Holiday">Company Holiday</option>
                            <option value="Week Off">Week Off / Sunday</option>
                            <option value="Absent">Absent</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">In Time</label>
                            <input type="time" name="clock_in" value="09:30" 
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Out Time</label>
                            <input type="time" name="clock_out" value="18:30" 
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Batch Note</label>
                        <input type="text" name="remarks" value="Bulk marked by Admin" 
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="batchModalOpen = false" 
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                            Apply Batch Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ===================================================== -->
        <!-- LIVE CAMERA FACE SCAN & GEOFENCE PUNCH MODAL         -->
        <!-- ===================================================== -->
        <div x-show="punchModalOpen" x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-4">
            <div @click.away="closePunchModal()" 
                 class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5 animate-scale-in">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold shrink-0">
                            <span class="text-lg">📸</span>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900" x-text="punchActionTitle">Face Scan & Location Check</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Live Selfie Verification • GPS Geofence Check</p>
                        </div>
                    </div>
                    <button type="button" @click="closePunchModal()" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">✕</button>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('attendances.punch') }}" @submit.prevent="submitPunch($event)" class="space-y-4">
                    @csrf
                    <input type="hidden" name="employee_id" :value="punchEmployeeId">
                    <input type="hidden" name="action_type" :value="punchAction">
                    <input type="hidden" name="location_type" :value="punchLocationType">
                    <input type="hidden" name="latitude" :value="empLat">
                    <input type="hidden" name="longitude" :value="empLng">
                    <input type="hidden" name="location_address" :value="empAddress">
                    <input type="hidden" name="face_photo" :value="capturedPhoto">

                    <!-- Live Date, Time & Verification Status Cards -->
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Date & Time Card -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Punch Time & Date</div>
                            <div class="text-base font-mono font-black text-slate-900 mt-0.5" x-text="liveTimeString">--:--:-- --</div>
                            <div class="text-[11px] text-slate-500 font-medium" x-text="liveDateString">-- --- ----</div>
                        </div>

                        <!-- Location & Geofence Status Card -->
                        <div class="p-3.5 rounded-2xl border transition"
                             :class="{
                                 'bg-emerald-50 border-emerald-200 text-emerald-800': isLocationVerified,
                                 'bg-rose-50 border-rose-200 text-rose-800': isLocationChecked && !isLocationVerified,
                                 'bg-amber-50 border-amber-200 text-amber-800': isLocationLoading
                             }">
                            <div class="text-[10px] font-bold uppercase tracking-wider flex items-center justify-between">
                                <span>GPS Geofence</span>
                                <template x-if="isLocationVerified">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                </template>
                            </div>
                            <div class="text-xs font-bold mt-1" x-text="locationStatusText">Checking location...</div>
                            <div class="text-[10px] opacity-80 mt-0.5 truncate" x-text="locationDetailText">Radius: {{ $attendanceSettings['geofence_radius_meters'] }}m</div>
                        </div>
                    </div>

                    <!-- CAMERA VIEWFINDER & LIVE FACE SCANNER -->
                    <div class="relative w-full aspect-4/3 bg-slate-950 rounded-2xl overflow-hidden border-2 border-slate-800 shadow-inner flex items-center justify-center">
                        <!-- Video Feed (When not captured) -->
                        <video x-ref="videoElement" autoplay playsinline muted 
                               x-show="!capturedPhoto && cameraStreamActive"
                               class="w-full h-full object-cover transform -scale-x-100"></video>

                        <!-- Canvas for Snapshot capture (hidden) -->
                        <canvas x-ref="canvasElement" class="hidden"></canvas>

                        <!-- Captured Snapshot Preview -->
                        <img :src="capturedPhoto" x-show="capturedPhoto" class="w-full h-full object-cover transform -scale-x-100" alt="Captured Face">

                        <!-- Camera Loading or Permission Denied Placeholder -->
                        <div x-show="!cameraStreamActive && !capturedPhoto" class="p-6 text-center space-y-3">
                            <div class="w-12 h-12 rounded-full bg-white/10 text-white flex items-center justify-center mx-auto animate-pulse">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                            </div>
                            <div class="text-xs text-slate-300 font-medium" x-text="cameraErrorMessage || 'Initializing camera viewfinder...'"></div>
                            <button type="button" @click="startCamera()" 
                                    class="px-3 py-1.5 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-[11px] font-bold">
                                Allow Camera Access
                            </button>
                        </div>

                        <!-- Face Guidance Oval Overlay -->
                        <div x-show="cameraStreamActive && !capturedPhoto" 
                             class="absolute inset-0 pointer-events-none flex items-center justify-center">
                            <div class="w-48 h-56 rounded-[50%] border-2 border-dashed border-white/60 shadow-[0_0_0_9999px_rgba(0,0,0,0.35)] flex flex-col items-center justify-between py-4">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-white bg-slate-900/60 px-2.5 py-0.5 rounded-full backdrop-blur-xs">Align Face</span>
                                <span class="text-[9px] text-white/90 bg-slate-900/60 px-2 py-0.5 rounded-full backdrop-blur-xs">Look directly into camera</span>
                            </div>
                        </div>

                        <!-- Face Captured Success Watermark -->
                        <div x-show="capturedPhoto" class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-emerald-500 text-white text-[10px] font-bold shadow-md flex items-center gap-1">
                            <span>✓ Face Captured</span>
                        </div>
                    </div>

                    <!-- Camera Control Actions -->
                    <div class="flex items-center justify-center gap-3">
                        <template x-if="!capturedPhoto && cameraStreamActive">
                            <button type="button" @click="capturePhoto()" 
                                    class="px-5 py-2.5 rounded-2xl bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white text-xs font-bold shadow-lg shadow-brand-500/20 flex items-center gap-2 transition hover:scale-105">
                                <span class="w-3 h-3 rounded-full bg-white animate-ping"></span>
                                <span>📸 Snap Face Photo</span>
                            </button>
                        </template>
                        <template x-if="capturedPhoto">
                            <button type="button" @click="retakePhoto()" 
                                    class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center gap-1.5 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                <span>Retake Photo</span>
                            </button>
                        </template>

                        <!-- Refresh Location Button -->
                        <button type="button" @click="fetchLocation()" 
                                class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium flex items-center gap-1.5 transition"
                                title="Refresh GPS Coordinates">
                            <svg class="w-3.5 h-3.5 text-slate-500" :class="{ 'animate-spin': isLocationLoading }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span>Refresh GPS</span>
                        </button>
                    </div>

                    <!-- Optional Remarks -->
                    <div>
                        <input type="text" name="remarks" x-model="punchRemarks" placeholder="Optional remark (e.g. On-time, client visit)..." 
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    </div>

                    <!-- Error Alert inside modal -->
                    <div x-show="punchErrorMessage" x-cloak 
                         class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                        <span class="text-rose-500 font-bold">✕</span>
                        <span x-text="punchErrorMessage"></span>
                    </div>

                    <!-- Modal Actions Footer -->
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="closePunchModal()" 
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                :disabled="isSubmitting || (settingsRequireCamera && !capturedPhoto) || (settingsRequireGeofence && !isLocationVerified)"
                                :class="{ 'opacity-50 cursor-not-allowed': isSubmitting || (settingsRequireCamera && !capturedPhoto) || (settingsRequireGeofence && !isLocationVerified) }"
                                class="px-6 py-2.5 rounded-xl text-white text-xs font-black shadow-lg transition"
                                :class="punchThemeClass">
                            <span x-show="!isSubmitting">✓ Confirm & Verify Punch</span>
                            <span x-show="isSubmitting" class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" class="opacity-75"></path></svg>
                                <span>Verifying & Saving...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ===================================================== -->
        <!-- PHOTO PREVIEW MODAL (FOR ADMIN & AUDIT)                -->
        <!-- ===================================================== -->
        <div x-show="previewModalOpen" x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
            <div @click.away="previewModalOpen = false" 
                 class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl border border-slate-100 space-y-4 animate-scale-in">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900" x-text="previewTitle">Selfie Verification</h4>
                        <div class="text-[11px] text-slate-500 font-mono mt-0.5" x-text="previewSubtitle"></div>
                    </div>
                    <button type="button" @click="previewModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">✕</button>
                </div>

                <div class="rounded-2xl overflow-hidden bg-slate-900 aspect-square border border-slate-200 shadow-inner flex items-center justify-center">
                    <img :src="previewImageUrl" alt="Punch Selfie" class="w-full h-full object-cover">
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs font-semibold text-slate-700">
                    <span class="text-slate-500">Location Range:</span>
                    <span class="font-bold text-brand-600" x-text="previewDistance"></span>
                </div>
            </div>
        </div>

    </div> <!-- Close x-data attendanceManager() wrapper -->

    <!-- ===================================================== -->
    <!-- ALPINE CONTROLLER LOGIC (CAMERA, GPS, TIME VERIFICATION) -->
    <!-- ===================================================== -->
    <script>
        function attendanceManager() {
            return {
                batchModalOpen: false,
                editModalOpen: false,
                selectedEmployee: {},
                selectedAttendance: {
                    status: 'Present',
                    clock_in: '09:30',
                    clock_out: '18:30',
                    break_in: '',
                    break_out: '',
                    break_minutes: 0,
                    shift: 'General',
                    location_type: 'Office',
                    remarks: ''
                },

                // Punch & Face Scan State
                punchModalOpen: false,
                punchAction: 'clock_in',
                punchActionTitle: 'Clock-In (Punch In)',
                punchEmployeeId: {{ $myEmployee ? $myEmployee->id : 0 }},
                punchLocationType: 'Office',
                punchThemeClass: 'bg-emerald-500 hover:bg-emerald-600',
                punchRemarks: '',
                punchErrorMessage: '',
                isSubmitting: false,

                // Camera State
                cameraStreamActive: false,
                cameraStream: null,
                cameraErrorMessage: '',
                capturedPhoto: null,

                // GPS & Geofence State
                officeLat: {{ $attendanceSettings['office_latitude'] }},
                officeLng: {{ $attendanceSettings['office_longitude'] }},
                geofenceRadius: {{ $attendanceSettings['geofence_radius_meters'] }},
                settingsRequireGeofence: {{ $attendanceSettings['require_geofence'] ? 'true' : 'false' }},
                settingsRequireCamera: {{ $attendanceSettings['require_camera_punch'] ? 'true' : 'false' }},

                empLat: null,
                empLng: null,
                empAddress: '',
                empDistanceMeters: null,
                isLocationLoading: false,
                isLocationChecked: false,
                isLocationVerified: false,
                locationStatusText: 'Waiting for GPS...',
                locationDetailText: 'Allowed radius: {{ $attendanceSettings['geofence_radius_meters'] }}m',

                // Live Date/Time Clock
                liveTimeString: '',
                liveDateString: '',
                clockInterval: null,

                // Photo Preview Modal
                previewModalOpen: false,
                previewImageUrl: '',
                previewTitle: '',
                previewSubtitle: '',
                previewDistance: '',

                init() {
                    this.updateClock();
                    this.clockInterval = setInterval(() => {
                        this.updateClock();
                    }, 1000);
                },

                updateClock() {
                    const now = new Date();
                    this.liveTimeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
                    this.liveDateString = now.toLocaleDateString([], { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' });
                },

                formatTime(t) {
                    if (!t) return '';
                    return t.length >= 5 ? t.substring(0, 5) : t;
                },

                openEdit(employee, attendance) {
                    this.selectedEmployee = employee;
                    if (attendance) {
                        this.selectedAttendance = {
                            status: attendance.status || 'Present',
                            clock_in: this.formatTime(attendance.clock_in),
                            clock_out: this.formatTime(attendance.clock_out),
                            break_in: this.formatTime(attendance.break_in),
                            break_out: this.formatTime(attendance.break_out),
                            break_minutes: attendance.break_minutes || 0,
                            shift: attendance.shift || 'General',
                            location_type: attendance.location_type || 'Office',
                            remarks: attendance.remarks || ''
                        };
                    } else {
                        this.selectedAttendance = {
                            status: 'Present',
                            clock_in: '09:30',
                            clock_out: '18:30',
                            break_in: '',
                            break_out: '',
                            break_minutes: 0,
                            shift: 'General',
                            location_type: 'Office',
                            remarks: ''
                        };
                    }
                    this.editModalOpen = true;
                },

                openPhotoPreview(url, title, time, distance) {
                    this.previewImageUrl = url;
                    this.previewTitle = title;
                    this.previewSubtitle = time ? `Punched at: ${time}` : '';
                    this.previewDistance = distance;
                    this.previewModalOpen = true;
                },

                openPunchModal(action, title, locationType, themeClass) {
                    this.punchAction = action;
                    this.punchActionTitle = title;
                    this.punchLocationType = locationType || 'Office';
                    this.punchThemeClass = themeClass || 'bg-brand-500 hover:bg-brand-600';
                    this.punchErrorMessage = '';
                    this.capturedPhoto = null;
                    this.punchModalOpen = true;

                    // Automatically start camera & location verification
                    this.$nextTick(() => {
                        this.startCamera();
                        this.fetchLocation();
                    });
                },

                closePunchModal() {
                    this.stopCamera();
                    this.punchModalOpen = false;
                    this.capturedPhoto = null;
                    this.punchErrorMessage = '';
                },

                // 1. Camera Handling
                async startCamera() {
                    this.cameraErrorMessage = '';
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        this.cameraErrorMessage = 'Camera API is not supported in this browser.';
                        return;
                    }

                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: 'user',
                                width: { ideal: 640 },
                                height: { ideal: 480 }
                            },
                            audio: false
                        });
                        this.cameraStream = stream;
                        this.cameraStreamActive = true;
                        if (this.$refs.videoElement) {
                            this.$refs.videoElement.srcObject = stream;
                            this.$refs.videoElement.play();
                        }
                    } catch (err) {
                        console.error('Camera access error:', err);
                        this.cameraErrorMessage = 'Unable to access live camera. Please grant camera permissions in your browser.';
                        this.cameraStreamActive = false;
                    }
                },

                stopCamera() {
                    if (this.cameraStream) {
                        this.cameraStream.getTracks().forEach(track => track.stop());
                        this.cameraStream = null;
                    }
                    this.cameraStreamActive = false;
                },

                capturePhoto() {
                    const video = this.$refs.videoElement;
                    const canvas = this.$refs.canvasElement;
                    if (!video || !canvas) return;

                    const width = video.videoWidth || 640;
                    const height = video.videoHeight || 480;

                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');

                    // Mirror horizontally to match the mirrored selfie viewfinder
                    ctx.translate(width, 0);
                    ctx.scale(-1, 1);
                    ctx.drawImage(video, 0, 0, width, height);

                    // Optional timestamp watermark on the canvas
                    ctx.setTransform(1, 0, 0, 1, 0, 0); // reset transform
                    ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
                    ctx.fillRect(10, height - 35, width - 20, 25);
                    ctx.fillStyle = '#ffffff';
                    ctx.font = '12px monospace';
                    ctx.fillText(`${this.liveDateString} ${this.liveTimeString} | GPS: ${this.empLat ? this.empLat.toFixed(4) : '--'}, ${this.empLng ? this.empLng.toFixed(4) : '--'}`, 20, height - 18);

                    this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.85);
                    this.punchErrorMessage = '';
                },

                retakePhoto() {
                    this.capturedPhoto = null;
                    this.punchErrorMessage = '';
                    if (!this.cameraStreamActive) {
                        this.startCamera();
                    }
                },

                // 2. Geolocation & Geofence Verification
                fetchLocation() {
                    this.isLocationLoading = true;
                    this.locationStatusText = 'Fetching GPS coordinates...';

                    if (!navigator.geolocation) {
                        this.isLocationLoading = false;
                        this.isLocationChecked = true;
                        this.isLocationVerified = !this.settingsRequireGeofence;
                        this.locationStatusText = 'GPS Not Supported';
                        this.locationDetailText = 'Browser does not support geolocation';
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.empLat = position.coords.latitude;
                            this.empLng = position.coords.longitude;
                            this.isLocationLoading = false;
                            this.isLocationChecked = true;

                            // Calculate Haversine distance
                            const dist = this.calculateDistance(this.empLat, this.empLng, this.officeLat, this.officeLng);
                            this.empDistanceMeters = Math.round(dist);

                            if (!this.settingsRequireGeofence || this.empDistanceMeters <= this.geofenceRadius) {
                                this.isLocationVerified = true;
                                this.locationStatusText = `✓ Verified (${this.empDistanceMeters}m)`;
                                this.locationDetailText = `Inside office zone (Max: ${this.geofenceRadius}m)`;
                                this.punchErrorMessage = '';
                            } else {
                                this.isLocationVerified = false;
                                this.locationStatusText = `✕ Outside Boundary (${this.empDistanceMeters}m)`;
                                this.locationDetailText = `Allowed radius is ${this.geofenceRadius}m. Please move closer.`;
                                this.punchErrorMessage = `You are ${this.empDistanceMeters}m away from the office. Allowed radius: ${this.geofenceRadius}m.`;
                            }
                        },
                        (error) => {
                            this.isLocationLoading = false;
                            this.isLocationChecked = true;
                            this.isLocationVerified = !this.settingsRequireGeofence;
                            this.locationStatusText = 'GPS Permission Denied';
                            this.locationDetailText = 'Please enable device location/GPS';
                            if (this.settingsRequireGeofence) {
                                this.punchErrorMessage = 'GPS Location permission is required for attendance punch.';
                            }
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                },

                calculateDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371000; // meters
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                              Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    return R * c;
                },

                // 3. Form Submit Validation
                submitPunch(e) {
                    if (this.settingsRequireCamera && !this.capturedPhoto) {
                        this.punchErrorMessage = 'Face photo capture is mandatory. Click "Snap Face Photo" to capture your face.';
                        return;
                    }

                    if (this.settingsRequireGeofence && !this.isLocationVerified) {
                        this.punchErrorMessage = `Location outside office perimeter (${this.empDistanceMeters ?? 0}m). Geofence verification required.`;
                        return;
                    }

                    this.isSubmitting = true;
                    this.stopCamera();
                    e.target.submit();
                }
            };
        }
    </script>
</x-hrms-layout>
