<x-hrms-layout>
    @section('page_title', 'Payroll & Salary Management')

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

    <!-- Month & Year Selector Header Toolbar -->
    <div class="mb-6 p-4 sm:p-6 bg-gradient-to-r from-[#0B0F19] via-slate-900 to-[#0B0F19] rounded-3xl text-white shadow-xl shadow-slate-900/15 border border-slate-800/40 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5 relative overflow-hidden">
        <div class="flex items-center gap-3.5 sm:gap-4 relative z-10">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 flex items-center justify-center text-brand-300 shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <span class="text-[10px] sm:text-xs font-semibold text-brand-300 uppercase tracking-wider">Payroll Cycle</span>
                <h2 class="text-base sm:text-xl font-bold text-white mt-0.5">
                    {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }} Payroll
                </h2>
                <p class="text-[11px] sm:text-xs text-slate-300 mt-0.5">Automated pro-rata earnings, attendance deductions & payout disbursement</p>
            </div>
        </div>

        <!-- Month Navigation and Quick Actions -->
        <div class="flex flex-wrap items-center gap-2.5 sm:gap-3 relative z-10 w-full lg:w-auto justify-between lg:justify-end">
            @php
                $prevDate = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
                $nextDate = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
            @endphp
            <a href="{{ route('payrolls.index', ['month' => $prevDate->month, 'year' => $prevDate->year]) }}" 
               class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white transition border border-white/15" title="Previous Month">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>

            <!-- Month Dropdown Form -->
            <form method="GET" action="{{ route('payrolls.index') }}" class="flex items-center gap-2">
                <select name="month" onchange="this.form.submit()" 
                        class="text-xs rounded-xl bg-slate-800 text-white border border-slate-700 py-2 px-3 focus:ring-1 focus:ring-brand-500">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m === $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(2000, $m, 1)->format('F') }}
                        </option>
                    @endfor
                </select>
                <select name="year" onchange="this.form.submit()" 
                        class="text-xs rounded-xl bg-slate-800 text-white border border-slate-700 py-2 px-3 focus:ring-1 focus:ring-brand-500">
                    @for ($y = 2023; $y <= 2028; $y++)
                        <option value="{{ $y }}" {{ $y === $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>

            <a href="{{ route('payrolls.index', ['month' => $nextDate->month, 'year' => $nextDate->year]) }}" 
               class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white transition border border-white/15" title="Next Month">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>

            @if (Auth::user()->isAdmin())
                <!-- Generate Payroll Form (Admin Only) -->
                <form method="POST" action="{{ route('payrolls.generate') }}" class="inline">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Generate / Recalculate Payroll</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <!-- Total Net Payout -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-brand-600 uppercase tracking-wider block">Total Net Payout</span>
            <span class="text-2xl font-black text-slate-900 mt-2 block">₹{{ number_format($stats['total_net_payout'], 2) }}</span>
            <span class="text-[11px] text-slate-500 mt-0.5 block font-medium">{{ $stats['total_staff_processed'] }} Employees in cycle</span>
        </div>

        <!-- Paid Salaries -->
        <a href="{{ route('payrolls.index', ['month' => $month, 'year' => $year, 'payment_status' => 'Paid', 'department' => $department]) }}" 
           class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-emerald-400 hover:shadow-md transition {{ $status === 'Paid' ? 'ring-2 ring-emerald-500 bg-emerald-50/20' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Disbursed (Paid)</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            </div>
            <span class="text-2xl font-black text-slate-900 mt-2 block">₹{{ number_format($stats['paid_amount'], 2) }}</span>
            <span class="text-[11px] text-slate-500 mt-0.5 block font-medium">{{ $stats['paid_count'] }} Salar(ies) Paid</span>
        </a>

        <!-- Pending Payouts -->
        <a href="{{ route('payrolls.index', ['month' => $month, 'year' => $year, 'payment_status' => 'Pending', 'department' => $department]) }}" 
           class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-amber-400 hover:shadow-md transition {{ $status === 'Pending' ? 'ring-2 ring-amber-500 bg-amber-50/20' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Pending Payout</span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
            </div>
            <span class="text-2xl font-black text-slate-900 mt-2 block">₹{{ number_format($stats['pending_amount'], 2) }}</span>
            <span class="text-[11px] text-slate-500 mt-0.5 block font-medium">{{ $stats['pending_count'] }} Pending Release</span>
        </a>

        <!-- Total Staff Processed -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Staff Processed</span>
            <span class="text-2xl font-black text-slate-900 mt-2 block">{{ $stats['total_staff_processed'] }} / {{ $employees->count() }}</span>
            <span class="text-[11px] text-slate-500 mt-0.5 block font-medium">Active Staff Count</span>
        </div>
    </div>

    <!-- Main Container with Alpine.js Modals -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden"
         x-data="{
             payoutModalOpen: false,
             salaryStructModalOpen: false,
             selectedPayroll: {},
             selectedEmployee: {},
             openPayout(payroll) {
                 this.selectedPayroll = payroll;
                 this.payoutModalOpen = true;
             },
             openSalaryStruct(emp) {
                 this.selectedEmployee = emp;
                 this.salaryStructModalOpen = true;
             }
         }">

        <!-- Toolbar Strip -->
        <div class="p-4 sm:px-7 bg-slate-50/70 border-b border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Payment Status Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
                <a href="{{ route('payrolls.index', ['month' => $month, 'year' => $year, 'department' => $department, 'search' => $search]) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ empty($status) ? 'bg-white text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    All Records ({{ $stats['total_staff_processed'] }})
                </a>
                <a href="{{ route('payrolls.index', ['month' => $month, 'year' => $year, 'payment_status' => 'Pending', 'department' => $department, 'search' => $search]) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $status === 'Pending' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    <span>Pending</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === 'Pending' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-700 font-bold' }}">{{ $stats['pending_count'] }}</span>
                </a>
                <a href="{{ route('payrolls.index', ['month' => $month, 'year' => $year, 'payment_status' => 'Paid', 'department' => $department, 'search' => $search]) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $status === 'Paid' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Paid ({{ $stats['paid_count'] }})
                </a>
            </div>

            <!-- Department & Search & Salary Setup Button -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <button type="button" @click="salaryStructModalOpen = true" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-brand-50 text-slate-700 hover:text-brand-600 text-xs font-bold border border-slate-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
                    <span>Configure Salary Structures</span>
                </button>

                <form method="GET" action="{{ route('payrolls.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    @if ($status)
                        <input type="hidden" name="payment_status" value="{{ $status }}">
                    @endif

                    <select name="department" onchange="this.form.submit()" 
                            class="text-xs rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-700 focus:border-brand-500">
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->name }}" {{ $department === $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>

                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search employee..." 
                               class="text-xs rounded-xl border border-slate-200 bg-white pl-8 pr-3 py-2 text-slate-700 focus:border-brand-500 w-44">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payroll Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Employee</th>
                        <th class="py-3.5 px-4 text-center">Attendance Factors</th>
                        <th class="py-3.5 px-4 text-right">Gross Salary</th>
                        <th class="py-3.5 px-4 text-right">Earned Pay</th>
                        <th class="py-3.5 px-4 text-right">Deductions</th>
                        <th class="py-3.5 px-4 text-right">Net Payable</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-normal">
                    @forelse ($payrolls as $pay)
                        @php
                            $badge = $pay->payment_status_badge;
                            $bank = $pay->employee->bank_details ?? [];
                        @endphp
                        <tr class="hover:bg-brand-50/20 transition group">
                            <!-- Employee Details -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs border border-slate-200 shrink-0">
                                        {{ substr($pay->employee->employee_name ?? 'NA', 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-brand-600 transition">
                                            {{ $pay->employee->employee_name }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                            <span>{{ $pay->employee->employee_code }}</span>
                                            <span>•</span>
                                            <span>{{ $pay->employee->department }}</span>
                                        </div>
                                        @if (!empty($bank['bank_name']) || !empty($bank['account_number']))
                                            <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                                {{ $bank['bank_name'] ?? 'Bank' }}: {{ !empty($bank['account_number']) ? '••••' . substr($bank['account_number'], -4) : '' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Attendance Factors -->
                            <td class="py-4 px-4 text-center">
                                <div class="font-bold text-slate-800 font-mono">
                                    {{ $pay->payable_days }} / {{ $pay->total_month_days }} Days
                                </div>
                                <div class="flex items-center justify-center gap-2 mt-1 text-[10px] text-slate-500 font-medium">
                                    <span class="text-emerald-600" title="Present Days">P: {{ $pay->present_days }}</span>
                                    <span>•</span>
                                    <span class="text-brand-600" title="Paid Leaves">L: {{ $pay->paid_leaves }}</span>
                                    @if ($pay->absent_days > 0)
                                        <span>•</span>
                                        <span class="text-rose-600 font-bold" title="Absent / LWP">A: {{ $pay->absent_days }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Gross Salary -->
                            <td class="py-4 px-4 text-right font-mono font-medium text-slate-600">
                                ₹{{ number_format($pay->gross_salary, 2) }}
                            </td>

                            <!-- Earned Total -->
                            <td class="py-4 px-4 text-right font-mono font-bold text-slate-800">
                                ₹{{ number_format($pay->total_earnings, 2) }}
                            </td>

                            <!-- Deductions -->
                            <td class="py-4 px-4 text-right font-mono text-rose-600">
                                -₹{{ number_format($pay->total_deductions, 2) }}
                                <div class="text-[10px] text-slate-400 font-normal">
                                    PT: ₹{{ $pay->prof_tax }} | PF: ₹{{ $pay->pf_amount }}
                                </div>
                            </td>

                            <!-- Net Payable -->
                            <td class="py-4 px-4 text-right">
                                <span class="text-sm font-black font-mono text-brand-600 bg-brand-50 px-2.5 py-1 rounded-xl border border-brand-100 inline-block">
                                    ₹{{ number_format($pay->net_salary, 2) }}
                                </span>
                            </td>

                            <!-- Payment Status Badge -->
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $badge['bg'] }} {{ $badge['text'] }} border {{ $badge['border'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    {{ $pay->payment_status }}
                                </span>
                                @if ($pay->payment_status === 'Paid' && $pay->paid_at)
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $pay->paid_at->format('d M Y') }}</div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <!-- Payslip Modal Button -->
                                    <a href="{{ route('payrolls.payslip', $pay->id) }}" target="_blank" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                        <span>Payslip</span>
                                    </a>

                                    @if (Auth::user()->isAdmin())
                                        <!-- Mark as Paid / Record Payout -->
                                        @if ($pay->payment_status !== 'Paid')
                                            <button type="button" @click="openPayout({{ $pay->toJson() }})" 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                <span>Payout</span>
                                            </button>
                                        @else
                                            <button type="button" @click="openPayout({{ $pay->toJson() }})" 
                                                    class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition" title="Edit Payout Details">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </button>
                                        @endif

                                        <!-- Delete Payroll Record -->
                                        <form method="POST" action="{{ route('payrolls.destroy', $pay->id) }}" onsubmit="return confirm('Remove this payroll entry?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 px-6 text-center text-slate-500">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">No Payroll Records for this Period</h4>
                                    <p class="text-xs text-slate-400">Click <strong>"Generate / Recalculate Payroll"</strong> above to compute monthly salaries for {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payrolls->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $payrolls->links() }}
            </div>
        @endif

        <!-- Modal 1: Record Payout / Mark as Paid -->
        <div x-show="payoutModalOpen" x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="payoutModalOpen = false" 
                 class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5 animate-scale-in">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Record Salary Payout</h3>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="selectedPayroll.employee ? selectedPayroll.employee.employee_name : ''"></p>
                        </div>
                    </div>
                    <button type="button" @click="payoutModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">✕</button>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Net Payable Amount</span>
                        <span class="text-xl font-black text-brand-600 font-mono mt-0.5 block" x-text="'₹' + Number(selectedPayroll.net_salary || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})"></span>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800" x-text="selectedPayroll.payment_status"></span>
                </div>

                <form :action="'{{ url('/payrolls') }}/' + selectedPayroll.id + '/payout'" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Payment Status *</label>
                        <select name="payment_status" x-model="selectedPayroll.payment_status" required 
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500">
                            <option value="Paid">Mark as Paid (Disbursed)</option>
                            <option value="Processing">In Processing (Bank queued)</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Payment Mode</label>
                            <select name="payment_mode" x-model="selectedPayroll.payment_mode" 
                                    class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500">
                                <option value="Bank Transfer">Bank Transfer (NEFT/RTGS)</option>
                                <option value="UPI">UPI</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Payment Date</label>
                            <input type="date" name="paid_at" value="{{ date('Y-m-d') }}" 
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Transaction ID / UTR / Cheque No</label>
                        <input type="text" name="transaction_reference" x-model="selectedPayroll.transaction_reference" placeholder="e.g. UTR2026090812345" 
                               class="w-full px-3.5 py-2 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Payment Note</label>
                        <input type="text" name="remarks" x-model="selectedPayroll.remarks" placeholder="e.g. Disbursed via HDFC Corporate Banking" 
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="payoutModalOpen = false" 
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                            Save Payout Record
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 2: Configure Salary Structure -->
        <div x-show="salaryStructModalOpen" x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="salaryStructModalOpen = false" 
                 class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5 animate-scale-in"
                 x-data="{
                     activeEmpId: '',
                     employeesList: {{ $employees->toJson() }},
                     monthlyGross: 35000,
                     basicSalary: 17500,
                     hra: 8750,
                     specialAllowance: 8750,
                     pf: 1800,
                     pt: 200,
                     tds: 0,
                     loadEmployeeSalary(empId) {
                         const emp = this.employeesList.find(e => e.id == empId);
                         if (emp && emp.salary_structure) {
                             const s = emp.salary_structure;
                             this.monthlyGross = Number(s.monthly_gross || 0);
                             this.basicSalary = Number(s.basic_salary || 0);
                             this.hra = Number(s.hra || 0);
                             this.specialAllowance = Number(s.special_allowance || 0);
                             this.pf = Number(s.pf_deduction || 0);
                             this.pt = Number(s.professional_tax || 200);
                             this.tds = Number(s.tds || 0);
                         } else {
                             this.monthlyGross = 35000;
                             this.recalculate();
                         }
                     },
                     recalculate() {
                         let g = Number(this.monthlyGross || 0);
                         this.basicSalary = Math.round(g * 0.5);
                         this.hra = Math.round(g * 0.25);
                         this.specialAllowance = Math.round(g * 0.25);
                     }
                 }">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Configure Employee Salary Structure</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Set base CTC, monthly gross, components, and statutory deductions</p>
                    </div>
                    <button type="button" @click="salaryStructModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">✕</button>
                </div>

                <form method="POST" action="{{ route('payrolls.salary-structure') }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Select Employee *</label>
                        <select name="employee_id" x-model="activeEmpId" @change="loadEmployeeSalary(activeEmpId)" required 
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500">
                            <option value="">-- Choose Employee --</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->employee_name }} ({{ $emp->employee_code }}) - {{ $emp->department }} {{ $emp->salaryStructure ? '• Current Gross: ₹' . number_format($emp->salaryStructure->monthly_gross) : '• (Salary Not Set)' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-4 rounded-2xl bg-brand-50/60 border border-brand-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-brand-800 uppercase">Monthly Gross Pay (₹)</span>
                            <span class="text-[10px] text-brand-600 font-medium">Auto-distributes into Basic, HRA & Special Allowance</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="number" name="monthly_gross" x-model="monthlyGross" @input="recalculate()" required step="0.01" 
                                   class="w-full px-3.5 py-2.5 text-sm font-bold font-mono rounded-xl border border-brand-200 bg-white focus:border-brand-500">
                            <span class="text-xs font-mono font-bold text-slate-600 whitespace-nowrap" x-text="'Annual CTC: ₹' + (monthlyGross * 12).toLocaleString('en-IN')"></span>
                        </div>
                    </div>

                    <!-- Earnings Breakdown -->
                    <div>
                        <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Monthly Earnings Components</span>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Basic Pay (50%)</label>
                                <input type="number" name="basic_salary" x-model="basicSalary" step="0.01" required 
                                       class="w-full px-3 py-1.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">HRA (25%)</label>
                                <input type="number" name="hra" x-model="hra" step="0.01" 
                                       class="w-full px-3 py-1.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Special Allowance</label>
                                <input type="number" name="special_allowance" x-model="specialAllowance" step="0.01" 
                                       class="w-full px-3 py-1.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500">
                            </div>
                        </div>
                    </div>

                    <!-- Deductions Breakdown -->
                    <div>
                        <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Monthly Deductions</span>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Provident Fund (PF)</label>
                                <input type="number" name="pf_deduction" x-model="pf" step="0.01" 
                                       class="w-full px-3 py-1.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Professional Tax (PT)</label>
                                <input type="number" name="professional_tax" x-model="pt" step="0.01" 
                                       class="w-full px-3 py-1.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Income Tax (TDS)</label>
                                <input type="number" name="tds" x-model="tds" step="0.01" 
                                       class="w-full px-3 py-1.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="salaryStructModalOpen = false" 
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                            Save Salary Package
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-hrms-layout>
