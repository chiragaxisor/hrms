<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payroll->employee->employee_name }} ({{ \Carbon\Carbon::createFromDate($payroll->year, $payroll->month, 1)->format('M Y') }})</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-size: 11pt !important; }
            .print-card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-10 px-4 flex flex-col items-center">

    <!-- Action Toolbar (No Print) -->
    <div class="no-print max-w-3xl w-full mb-6 flex items-center justify-between">
        <a href="{{ route('payrolls.index', ['month' => $payroll->month, 'year' => $payroll->year]) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-slate-700 text-xs font-bold border border-slate-200 shadow-xs hover:bg-slate-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            <span>Back to Payroll Register</span>
        </a>

        <button onclick="window.print()" 
                class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            <span>Print / Save as PDF</span>
        </button>
    </div>

    <!-- Formal Printable Payslip Card -->
    <div class="print-card bg-white max-w-3xl w-full rounded-3xl p-8 sm:p-10 shadow-xl border border-slate-200/80 space-y-7">
        
        <!-- Company Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-slate-900 pb-6">
                <div class="flex items-center gap-3.5">
                <img src="{{ asset('images/logo.png') }}" alt="{{ $globalCompanyName ?? config('app.name') }}" class="w-12 h-12 rounded-2xl shadow-md object-contain bg-black p-0.5 border border-slate-200 shrink-0">
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">{{ $globalCompanyName ?? config('app.name') }}</h1>
                    @if (!empty($globalCompanyAddress))
                        <p class="text-xs text-slate-500 mt-0.5">{{ $globalCompanyAddress }}</p>
                    @endif
                    @if (!empty($globalCompanyEmail))
                        <p class="text-[11px] text-slate-400">{{ $globalCompanyEmail }}{{ !empty($globalCompanyPhone) ? ' | ' . $globalCompanyPhone : '' }}</p>
                    @endif
                </div>
            </div>


            <div class="text-left sm:text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-brand-50 text-brand-600 border border-brand-100">
                    Salary Slip
                </span>
                <div class="text-sm font-bold text-slate-800 mt-1">
                    {{ \Carbon\Carbon::createFromDate($payroll->year, $payroll->month, 1)->format('F Y') }}
                </div>
                <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $payroll->payroll_code }}</div>
            </div>
        </div>

        <!-- Employee Summary Grid -->
        @php
            $emp = $payroll->employee;
            $bank = $emp->bank_details ?? [];
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Employee Name</span>
                <span class="font-bold text-slate-900 mt-0.5 block">{{ $emp->employee_name }}</span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Employee ID</span>
                <span class="font-bold font-mono text-slate-900 mt-0.5 block">{{ $emp->employee_code }}</span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Department</span>
                <span class="font-semibold text-slate-800 mt-0.5 block">{{ $emp->department }}</span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Designation</span>
                <span class="font-semibold text-slate-800 mt-0.5 block">{{ $emp->designation }}</span>
            </div>

            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Bank Name</span>
                <span class="font-semibold text-slate-800 mt-0.5 block">{{ $bank['bank_name'] ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Account No</span>
                <span class="font-mono text-slate-800 mt-0.5 block">{{ !empty($bank['account_number']) ? '••••' . substr($bank['account_number'], -4) : 'N/A' }}</span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">IFSC Code</span>
                <span class="font-mono text-slate-800 mt-0.5 block">{{ $bank['ifsc_code'] ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PAN / Aadhaar</span>
                <span class="font-mono text-slate-800 mt-0.5 block">{{ $emp->pan_number ?? '-' }}</span>
            </div>
        </div>

        <!-- Attendance Summary Row -->
        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 text-center p-3.5 rounded-xl border border-slate-200 text-xs">
            <div>
                <span class="text-[10px] text-slate-400 block font-bold uppercase">Month Days</span>
                <span class="font-bold font-mono text-slate-800">{{ $payroll->total_month_days }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 block font-bold uppercase">Working Days</span>
                <span class="font-bold font-mono text-slate-800">{{ $payroll->working_days }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 block font-bold uppercase">Present Days</span>
                <span class="font-bold font-mono text-emerald-600">{{ $payroll->present_days }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 block font-bold uppercase">Paid Leaves</span>
                <span class="font-bold font-mono text-brand-600">{{ $payroll->paid_leaves }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 block font-bold uppercase">Holidays</span>
                <span class="font-bold font-mono text-slate-600">{{ $payroll->holidays_weekoffs }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 block font-bold uppercase">Payable Days</span>
                <span class="font-black font-mono text-slate-900 bg-slate-100 px-2 py-0.5 rounded">{{ $payroll->payable_days }}</span>
            </div>
        </div>

        <!-- Earnings vs Deductions Breakdown -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Earnings Table -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <div class="bg-emerald-50/70 px-4 py-2.5 border-b border-slate-200 flex justify-between items-center text-xs font-bold text-emerald-900">
                    <span>EARNINGS</span>
                    <span>AMOUNT (₹)</span>
                </div>
                <div class="p-4 space-y-2.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Basic Salary</span>
                        <span class="font-mono font-medium text-slate-800">₹{{ number_format($payroll->earned_basic, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">House Rent Allowance (HRA)</span>
                        <span class="font-mono font-medium text-slate-800">₹{{ number_format($payroll->earned_hra, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Special & Other Allowances</span>
                        <span class="font-mono font-medium text-slate-800">₹{{ number_format($payroll->earned_allowances, 2) }}</span>
                    </div>
                    @if ($payroll->overtime_bonus > 0)
                        <div class="flex justify-between">
                            <span class="text-slate-600">Overtime / Bonus</span>
                            <span class="font-mono font-medium text-slate-800">₹{{ number_format($payroll->overtime_bonus, 2) }}</span>
                        </div>
                    @endif
                </div>
                <div class="bg-slate-50 px-4 py-2.5 border-t border-slate-200 flex justify-between items-center text-xs font-bold text-slate-900">
                    <span>Total Earnings (A)</span>
                    <span class="font-mono font-black text-emerald-700">₹{{ number_format($payroll->total_earnings, 2) }}</span>
                </div>
            </div>

            <!-- Deductions Table -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <div class="bg-rose-50/70 px-4 py-2.5 border-b border-slate-200 flex justify-between items-center text-xs font-bold text-rose-900">
                    <span>DEDUCTIONS</span>
                    <span>AMOUNT (₹)</span>
                </div>
                <div class="p-4 space-y-2.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Provident Fund (PF)</span>
                        <span class="font-mono font-medium text-slate-800">₹{{ number_format($payroll->pf_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Professional Tax (PT)</span>
                        <span class="font-mono font-medium text-slate-800">₹{{ number_format($payroll->prof_tax, 2) }}</span>
                    </div>
                    @if ($payroll->esic_amount > 0)
                        <div class="flex justify-between">
                            <span class="text-slate-600">ESIC</span>
                            <span class="font-mono font-medium text-slate-800">₹{{ number_format($payroll->esic_amount, 2) }}</span>
                        </div>
                    @endif
                    @if ($payroll->tds_amount > 0)
                        <div class="flex justify-between">
                            <span class="text-slate-600">Income Tax (TDS)</span>
                            <span class="font-mono font-medium text-slate-800">₹{{ number_format($payroll->tds_amount, 2) }}</span>
                        </div>
                    @endif
                    @if ($payroll->unpaid_leave_deduction > 0)
                        <div class="flex justify-between text-rose-600">
                            <span>Unpaid Leave / LWP</span>
                            <span class="font-mono font-medium">₹{{ number_format($payroll->unpaid_leave_deduction, 2) }}</span>
                        </div>
                    @endif
                </div>
                <div class="bg-slate-50 px-4 py-2.5 border-t border-slate-200 flex justify-between items-center text-xs font-bold text-slate-900">
                    <span>Total Deductions (B)</span>
                    <span class="font-mono font-black text-rose-700">₹{{ number_format($payroll->total_deductions, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Net Take-Home Pay Highlight Card -->
        <div class="p-5 rounded-2xl bg-[#0B0F19] text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-md">
            <div>
                <span class="text-xs text-brand-300 font-bold uppercase tracking-wider block">Net Take Home Pay (A - B)</span>
                <div class="text-2xl font-black font-mono text-emerald-400 mt-1">
                    ₹{{ number_format($payroll->net_salary, 2) }}
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $payroll->payment_status === 'Paid' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-amber-500/20 text-amber-300 border border-amber-400/30' }}">
                    Status: {{ $payroll->payment_status }}
                </span>
                @if ($payroll->paid_at)
                    <p class="text-[11px] text-slate-300 mt-1">Disbursed: {{ $payroll->paid_at->format('d M Y') }}</p>
                @endif
                @if ($payroll->transaction_reference)
                    <p class="text-[10px] font-mono text-slate-400">Ref: {{ $payroll->transaction_reference }}</p>
                @endif
            </div>
        </div>

        <!-- Footer Signatures -->
        <div class="pt-8 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
            <div>
                <p class="text-[11px]">This is a computer-generated payslip and does not require a physical signature.</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Generated on {{ now()->format('d M Y, h:i A') }} by HRMS System</p>
            </div>
            <div class="text-right">
                <div class="w-32 border-b border-slate-400 mb-1"></div>
                <span class="font-bold text-slate-700">Authorized Signatory</span>
            </div>
        </div>

    </div>
</body>
</html>
