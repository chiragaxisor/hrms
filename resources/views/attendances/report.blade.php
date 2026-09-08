<x-hrms-layout>
    @section('page_title', 'Monthly Attendance Register')

    <!-- Header & Action Toolbar -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-7 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-brand-500 text-white flex items-center justify-center shadow-md shadow-brand-500/20 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Monthly Attendance Register & Matrix</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Comprehensive view of full-month day-by-day attendance records and employee performance.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('attendances.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Back to Daily Sheet</span>
                </a>
            </div>
        </div>

        <!-- Month & Year Filter Toolbar -->
        <div class="mt-6 pt-5 border-t border-slate-100">
            <form method="GET" action="{{ route('attendances.report') }}" class="flex flex-wrap items-center gap-3">
                <!-- Month Selection -->
                <div class="w-44">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Month</label>
                    <select name="month" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Year Selection -->
                <div class="w-32">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Year</label>
                    <select name="year" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        @for ($y = \Carbon\Carbon::now()->year + 1; $y >= \Carbon\Carbon::now()->year - 3; $y--)
                            <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Department Filter -->
                <div class="w-48">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Department</label>
                    <select name="department" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->name }}" {{ $departmentFilter === $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="self-end ml-auto flex items-center gap-4 text-xs">
                    <!-- Legend Indicators -->
                    <div class="flex items-center gap-3 bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200/80 text-[11px] font-semibold">
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold">P</span> Present</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded bg-rose-500 text-white flex items-center justify-center text-[10px] font-bold">A</span> Absent</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded bg-amber-500 text-white flex items-center justify-center text-[10px] font-bold">HD</span> Half Day</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded bg-purple-500 text-white flex items-center justify-center text-[10px] font-bold">L</span> Leave</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded bg-sky-500 text-white flex items-center justify-center text-[10px] font-bold">H</span> Holiday</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded bg-slate-300 text-slate-700 flex items-center justify-center text-[10px] font-bold">WO</span> Off</div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Monthly Grid Matrix Table Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4 sticky left-0 z-20 bg-slate-50 min-w-[200px] border-r border-slate-200">
                            Employee
                        </th>
                        @for ($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $cellDate = \Carbon\Carbon::createFromDate($year, $month, $d);
                                $isSunday = $cellDate->isSunday();
                                $isToday = $cellDate->isToday();
                            @endphp
                            <th class="py-2.5 px-1.5 text-center min-w-[34px] border-r border-slate-100 {{ $isSunday ? 'bg-rose-50/50 text-rose-500' : '' }} {{ $isToday ? 'bg-brand-50 font-black text-brand-600' : '' }}">
                                <div class="text-[9px] font-normal uppercase">{{ substr($cellDate->format('D'), 0, 2) }}</div>
                                <div class="text-xs">{{ $d }}</div>
                            </th>
                        @endfor
                        <th class="py-3 px-2 text-center bg-emerald-50 text-emerald-800 font-bold min-w-[40px]" title="Total Present">P</th>
                        <th class="py-3 px-2 text-center bg-rose-50 text-rose-800 font-bold min-w-[40px]" title="Total Absent">A</th>
                        <th class="py-3 px-2 text-center bg-amber-50 text-amber-800 font-bold min-w-[40px]" title="Half Days">HD</th>
                        <th class="py-3 px-2 text-center bg-purple-50 text-purple-800 font-bold min-w-[40px]" title="Leaves">L</th>
                        <th class="py-3 px-3 text-center bg-brand-50 text-brand-700 font-bold min-w-[60px]" title="Total Hours">Hrs</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($employees as $emp)
                        @php
                            $empAtts = $attendances->get($emp->id, collect())->keyBy(function ($item) {
                                return \Carbon\Carbon::parse($item->date)->day;
                            });

                            $presentCount = 0;
                            $absentCount = 0;
                            $halfDayCount = 0;
                            $leaveCount = 0;
                            $totalHours = 0;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition group">
                            <!-- Employee Info sticky column -->
                            <td class="py-3 px-4 sticky left-0 z-10 bg-white group-hover:bg-slate-50/60 border-r border-slate-200">
                                <div class="font-bold text-slate-900 truncate">{{ $emp->employee_name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono flex items-center gap-1 mt-0.5">
                                    <span>{{ $emp->employee_code }}</span>
                                    <span>•</span>
                                    <span class="truncate max-w-[100px]">{{ $emp->department ?: 'Staff' }}</span>
                                </div>
                            </td>

                            <!-- 1 to 31 Days Matrix Cells -->
                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $cellDate = \Carbon\Carbon::createFromDate($year, $month, $d);
                                    $isSunday = $cellDate->isSunday();
                                    $isToday = $cellDate->isToday();
                                    $rec = $empAtts->get($d);

                                    $statusShort = '';
                                    $cellClass = 'text-slate-300';

                                    if ($rec) {
                                        $totalHours += $rec->total_hours;
                                        switch ($rec->status) {
                                            case 'Present':
                                                $presentCount++;
                                                $statusShort = 'P';
                                                $cellClass = 'bg-emerald-500 text-white font-bold';
                                                break;
                                            case 'Absent':
                                                $absentCount++;
                                                $statusShort = 'A';
                                                $cellClass = 'bg-rose-500 text-white font-bold';
                                                break;
                                            case 'Half Day':
                                                $halfDayCount++;
                                                $statusShort = 'HD';
                                                $cellClass = 'bg-amber-500 text-white font-bold';
                                                break;
                                            case 'Leave':
                                                $leaveCount++;
                                                $statusShort = 'L';
                                                $cellClass = 'bg-purple-500 text-white font-bold';
                                                break;
                                            case 'Holiday':
                                                $statusShort = 'H';
                                                $cellClass = 'bg-sky-500 text-white font-bold';
                                                break;
                                            case 'Week Off':
                                                $statusShort = 'WO';
                                                $cellClass = 'bg-slate-300 text-slate-700 font-medium';
                                                break;
                                        }
                                    } elseif ($isSunday) {
                                        $statusShort = 'WO';
                                        $cellClass = 'text-slate-300 bg-rose-50/20';
                                    }
                                @endphp
                                <td class="py-2.5 px-1 text-center border-r border-slate-100 {{ $isToday ? 'bg-brand-50/40' : '' }}">
                                    @if ($statusShort)
                                        <span class="w-6 h-6 mx-auto rounded flex items-center justify-center text-[10px] {{ $cellClass }}" 
                                              title="{{ $rec ? ($rec->status . ($rec->clock_in ? ' (' . $rec->clock_in . ' - ' . $rec->clock_out . ')' : '')) : 'Week Off' }}">
                                            {{ $statusShort }}
                                        </span>
                                    @else
                                        <span class="text-slate-200 text-xs">·</span>
                                    @endif
                                </td>
                            @endfor

                            <!-- Summary Totals -->
                            <td class="py-3 px-2 text-center font-bold text-emerald-700 bg-emerald-50/40 border-l border-slate-100">
                                {{ $presentCount }}
                            </td>
                            <td class="py-3 px-2 text-center font-bold text-rose-700 bg-rose-50/40">
                                {{ $absentCount }}
                            </td>
                            <td class="py-3 px-2 text-center font-bold text-amber-700 bg-amber-50/40">
                                {{ $halfDayCount }}
                            </td>
                            <td class="py-3 px-2 text-center font-bold text-purple-700 bg-purple-50/40">
                                {{ $leaveCount }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-bold text-brand-600 bg-brand-50/40">
                                {{ $totalHours }}h
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 6 }}" class="py-12 text-center text-slate-400 text-xs">
                                No employee records found for this register.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-hrms-layout>
