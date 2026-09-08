<x-hrms-layout>
    @section('page_title', $employee->employee_name . ' - Master Profile')

    <!-- Top Action / Breadcrumb Bar -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="{{ route('employees.index') }}" class="hover:text-brand-600 transition">Employees</a>
            <span>/</span>
            <span class="text-slate-600 font-semibold">{{ $employee->employee_code }}</span>
            <span>/</span>
            <span class="text-slate-900 font-bold">Master Profile</span>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('employees.edit', $employee) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold tracking-wide shadow-sm shadow-amber-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Employee</span>
            </a>

            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Print</span>
            </button>

            <a href="{{ route('employees.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition">
                <span>Back</span>
            </a>
        </div>
    </div>

    <!-- Flash Notifications -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
        </div>
    @endif

    <!-- Profile Hero Card (Box Design) -->
    <div class="bg-white rounded-3xl p-4 sm:p-8 border border-slate-200/80 shadow-sm mb-6 sm:mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-brand-500 to-brand-600 text-white flex items-center justify-center font-bold text-xl sm:text-2xl shadow-lg shadow-brand-500/15 ring-4 ring-brand-50 shrink-0">
                    {{ substr($employee->employee_name, 0, 2) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $employee->employee_name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-brand-50 text-brand-600 border border-brand-200/60 font-mono">
                            {{ $employee->employee_code }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold {{ $employee->status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            ● {{ $employee->status }}
                        </span>
                    </div>
                    <div class="text-xs sm:text-sm font-semibold text-slate-700 mt-1 flex flex-wrap items-center gap-2">
                        <span>{{ $employee->designation }}</span>
                        <span class="text-slate-300">&bull;</span>
                        <span class="text-brand-600">{{ $employee->department }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Metrics Pills -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Joining Date</span>
                    <span class="text-xs font-bold text-slate-800 mt-0.5 block">{{ $employee->joining_date ? $employee->joining_date->format('d M, Y') : 'N/A' }}</span>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Employment</span>
                    <span class="text-xs font-bold text-slate-800 mt-0.5 block">{{ $employee->employment_type }}</span>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center col-span-2 sm:col-span-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Work Location</span>
                    <span class="text-xs font-bold text-slate-800 mt-0.5 block truncate">{{ $employee->work_location ?: 'Head Office' }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Contact & Portal Login Credentials Strip -->
        <div class="pt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="flex items-center gap-2.5 text-slate-600">
                <div class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
                <span class="font-mono text-slate-800 truncate">{{ $employee->company_email ?: $employee->personal_email ?: 'N/A' }}</span>
            </div>
            <div class="flex items-center gap-2.5 text-slate-600">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                </div>
                <span class="font-mono text-slate-800">{{ $employee->personal_mobile ?: 'N/A' }}</span>
            </div>
            <div class="flex items-center gap-2.5 text-slate-600">
                <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <span class="text-slate-800">{{ $employee->branch ?: 'Main Branch' }} &bull; Desk: {{ $employee->office_number ?: 'N/A' }}</span>
            </div>
        </div>

        <!-- Employee Portal Login Access Card -->
        <div class="mt-4 p-3.5 rounded-2xl bg-gradient-to-r from-brand-50 via-slate-50 to-brand-50 border border-brand-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-brand-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-slate-900 flex items-center gap-2">
                        <span>Employee Portal Login Credentials</span>
                        @if ($employee->user || $employee->company_email)
                            <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Login Active</span>
                        @else
                            <span class="px-2 py-0.2 rounded-full text-[10px] font-medium bg-amber-100 text-amber-800">No Email Provided</span>
                        @endif
                    </div>
                    <div class="text-slate-600 text-[11px] mt-0.5">
                        This employee can log in to HRMS to mark attendance, apply for leaves & log daily work.
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-white px-3 py-1.5 rounded-xl border border-slate-200/80 font-mono text-[11px] text-slate-700">
                <div>
                    <span class="text-slate-400">Email:</span>
                    <strong class="text-brand-600">{{ $employee->company_email ?: 'N/A' }}</strong>
                </div>
                <div class="h-3 w-px bg-slate-200"></div>
                <div>
                    <span class="text-slate-400">Default Pass:</span>
                    <strong class="text-slate-800">password</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Structured Side-by-Side (2 Columns) Master Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- 1. EMPLOYEE & JOB DETAILS BOX -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 font-bold text-xs flex items-center justify-center border border-brand-100">1</span>
                        <h3 class="text-sm font-bold text-slate-900">Job & Organizational Details</h3>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-400">Section 1</span>
                </div>

                <div class="grid grid-cols-2 gap-3.5 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Employee Name</span>
                        <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $employee->employee_name }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Employee Code</span>
                        <span class="font-bold font-mono text-brand-600 text-sm mt-0.5 block">{{ $employee->employee_code }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Department</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $employee->department }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Designation</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $employee->designation }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Reporting Person</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $employee->reporting_person_name ?: 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Branch / Location</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $employee->branch ?: 'N/A' }} ({{ $employee->work_location ?: 'N/A' }})</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Office Desk Number</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $employee->office_number ?: 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Company Email</span>
                        <span class="font-semibold font-mono text-brand-600 mt-0.5 block truncate">{{ $employee->company_email ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. PERSONAL DETAILS BOX -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 font-bold text-xs flex items-center justify-center border border-brand-100">2</span>
                        <h3 class="text-sm font-bold text-slate-900">Personal Information & KYC</h3>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-400">Section 2</span>
                </div>

                <div class="grid grid-cols-2 gap-3.5 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Date of Birth</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $employee->date_of_birth ? $employee->date_of_birth->format('d M, Y') : 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Gender / Marital Status</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $employee->gender ?: 'N/A' }} &bull; {{ $employee->marital_status ?: 'Single' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Anniversary Date</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $employee->anniversary_date ? $employee->anniversary_date->format('d M, Y') : 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Blood Group</span>
                        <span class="font-bold text-rose-600 mt-0.5 block">{{ $employee->blood_group ?: 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Personal Mobile</span>
                        <span class="font-semibold font-mono text-slate-800 mt-0.5 block">{{ $employee->personal_mobile ?: 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Aadhaar-Linked Mobile</span>
                        <span class="font-semibold font-mono text-slate-800 mt-0.5 block">{{ $employee->aadhaar_linked_mobile ?: 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">PAN Card Number</span>
                        <span class="font-bold font-mono text-slate-800 mt-0.5 block uppercase">{{ $employee->pan_number ?: 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Aadhaar Card Number</span>
                        <span class="font-bold font-mono text-slate-800 mt-0.5 block">{{ $employee->aadhaar_number ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. STATUTORY / GOVERNMENT DETAILS BOX -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 font-bold text-xs flex items-center justify-center border border-brand-100">3</span>
                        <h3 class="text-sm font-bold text-slate-900">Statutory & Government Details</h3>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-400">Section 3</span>
                </div>

                @php $stat = $employee->statutory_details ?? []; @endphp
                <div class="grid grid-cols-2 gap-3.5 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">UAN Number</span>
                        <span class="font-bold font-mono text-slate-800 mt-0.5 block">{{ $stat['uan_number'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">ESIC Number</span>
                        <span class="font-bold font-mono text-slate-800 mt-0.5 block">{{ $stat['esic_number'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Previous PF Account / UAN</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block truncate">{{ $stat['prev_pf_uan'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Previous ESIC Details</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block truncate">{{ $stat['prev_esic'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">PF / ESIC Docs Received</span>
                        <span class="font-bold {{ ($stat['docs_received'] ?? '') === 'Yes' ? 'text-emerald-600' : 'text-slate-600' }} mt-0.5 block">{{ $stat['docs_received'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Statutory Verified By</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $stat['verified_by'] ?? 'N/A' }} @if(!empty($stat['verification_date'])) ({{ $stat['verification_date'] }}) @endif</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. BANK / SALARY ACCOUNT BOX -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 font-bold text-xs flex items-center justify-center border border-brand-100">4</span>
                        <h3 class="text-sm font-bold text-slate-900">Bank & Salary Account</h3>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-400">Section 4</span>
                </div>

                @php $bank = $employee->bank_details ?? []; @endphp
                <div class="grid grid-cols-2 gap-3.5 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Bank Name</span>
                        <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $bank['bank_name'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Account Holder</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $bank['account_holder'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Account Number</span>
                        <span class="font-bold font-mono text-brand-600 text-sm mt-0.5 block">{{ $bank['account_number'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">IFSC Code</span>
                        <span class="font-bold font-mono text-slate-800 mt-0.5 block uppercase">{{ $bank['ifsc_code'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Branch & Type</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $bank['branch_name'] ?? 'N/A' }} &bull; {{ $bank['account_type'] ?? 'Savings' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Cheque / Bank Proof</span>
                        <span class="font-semibold {{ ($bank['cheque_received'] ?? '') === 'Yes' ? 'text-emerald-600' : 'text-slate-600' }} mt-0.5 block">{{ $bank['cheque_received'] ?? 'N/A' }} (By: {{ $bank['verified_by'] ?? 'N/A' }})</span>
                    </div>
                </div>

                <!-- Salary Package Card in Show View -->
                @php $salary = $employee->salaryStructure; @endphp
                <div class="mt-4 p-4 rounded-2xl bg-brand-50/60 border border-brand-100 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-950 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            Salary Package & CTC
                        </span>
                        @if ($salary)
                            <span class="text-xs font-mono font-bold text-brand-600 bg-white px-2.5 py-0.5 rounded-lg border border-brand-200">
                                CTC: ₹{{ number_format($salary->ctc_annual, 2) }}
                            </span>
                        @endif
                    </div>

                    @if ($salary)
                        <div class="grid grid-cols-3 gap-2 text-xs pt-1">
                            <div class="p-2.5 rounded-xl bg-white border border-brand-100">
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">Monthly Gross</span>
                                <span class="font-bold font-mono text-brand-600 text-sm mt-0.5 block">₹{{ number_format($salary->monthly_gross, 2) }}</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-white border border-brand-100">
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">Basic Pay (50%)</span>
                                <span class="font-bold font-mono text-slate-800 text-xs mt-0.5 block">₹{{ number_format($salary->basic_salary, 2) }}</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-white border border-brand-100">
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">HRA + Allowances</span>
                                <span class="font-bold font-mono text-slate-800 text-xs mt-0.5 block">₹{{ number_format($salary->hra + $salary->special_allowance, 2) }}</span>
                            </div>
                        </div>
                    @else
                        <div class="p-2.5 rounded-xl bg-white/80 border border-brand-100 text-xs text-slate-500 flex items-center justify-between">
                            <span>Salary package not configured yet (Default ₹35,000/mo will be used).</span>
                            <a href="{{ route('payrolls.index') }}" class="text-xs font-bold text-brand-600 hover:underline">Set Salary &rarr;</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 5. ADDRESS DETAILS BOX -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 font-bold text-xs flex items-center justify-center border border-brand-100">5</span>
                        <h3 class="text-sm font-bold text-slate-900">Residential Addresses</h3>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-400">Section 5</span>
                </div>

                @php $addr = $employee->address_details ?? []; @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-brand-600 block mb-1.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                                Communication / Mailing Address
                            </span>
                            <p class="text-slate-700 leading-relaxed font-medium">{{ $addr['communication']['address'] ?? 'No address recorded' }}</p>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-200/60 font-semibold text-slate-600">
                            {{ $addr['communication']['city'] ?? '' }}, {{ $addr['communication']['state'] ?? '' }} - {{ $addr['communication']['pincode'] ?? '' }}
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                Permanent Address
                            </span>
                            <p class="text-slate-700 leading-relaxed font-medium">{{ $addr['permanent']['address'] ?? 'No address recorded' }}</p>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-200/60 font-semibold text-slate-600">
                            {{ $addr['permanent']['city'] ?? '' }}, {{ $addr['permanent']['state'] ?? '' }} - {{ $addr['permanent']['pincode'] ?? '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. EMERGENCY & REFERENCE CONTACT BOX -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 font-bold text-xs flex items-center justify-center border border-brand-100">6</span>
                        <h3 class="text-sm font-bold text-slate-900">Emergency & Reference Contact</h3>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-400">Section 6</span>
                </div>

                @php 
                    $emg = $employee->emergency_contact ?? [];
                    $ref = $employee->reference_contact ?? [];
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                    <div class="p-4 rounded-2xl bg-rose-50/50 border border-rose-100/80">
                        <span class="text-xs font-bold text-rose-800 block mb-1.5">🚨 Emergency Contact</span>
                        <div class="font-bold text-slate-800 text-sm">{{ $emg['name'] ?? 'N/A' }}</div>
                        <div class="text-slate-500 font-medium">Relation: {{ $emg['relation'] ?? 'N/A' }}</div>
                        <div class="text-rose-700 font-mono font-bold mt-1.5">{{ $emg['mobile'] ?? 'N/A' }}</div>
                        <div class="text-slate-400 text-[11px] mt-1">{{ $emg['address'] ?? '' }}</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-xs font-bold text-slate-700 block mb-1.5">👤 Professional Reference</span>
                        <div class="font-bold text-slate-800 text-sm">{{ $ref['name'] ?? 'N/A' }}</div>
                        <div class="text-slate-500 font-medium">Association: {{ $ref['relation'] ?? 'N/A' }}</div>
                        <div class="text-slate-700 font-mono font-bold mt-1.5">{{ $ref['mobile'] ?? 'N/A' }}</div>
                        <div class="text-slate-400 text-[11px] mt-1">Checked by {{ $ref['checked_by'] ?? 'HR' }} @if(!empty($ref['remarks'])) &bull; {{ $ref['remarks'] }} @endif</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. NOMINEE DETAILS BOX -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 font-bold text-xs flex items-center justify-center border border-brand-100">7</span>
                        <h3 class="text-sm font-bold text-slate-900">Nominee Information & Bank</h3>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-400">Section 7</span>
                </div>

                @php $nom = $employee->nominee_details ?? []; @endphp
                <div class="grid grid-cols-2 gap-3.5 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Nominee Name</span>
                        <span class="font-bold text-slate-800 mt-0.5 block">{{ $nom['name'] ?? 'N/A' }} ({{ $nom['relation'] ?? 'Relation' }})</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Nominee DOB</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $nom['dob'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Aadhaar / Mobile</span>
                        <span class="font-semibold font-mono text-slate-800 mt-0.5 block">{{ $nom['aadhaar'] ?? 'N/A' }} &bull; {{ $nom['aadhaar_mobile'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">PAN & Occupation</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $nom['pan'] ?? 'N/A' }} ({{ $nom['occupation'] ?? 'N/A' }})</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 col-span-2">
                        <span class="text-slate-400 font-medium block text-[11px]">Nominee Bank Account Details</span>
                        <span class="font-bold text-slate-800 mt-0.5 block">{{ $nom['bank_name'] ?? 'Bank' }} &bull; Acc: {{ $nom['account_number'] ?? 'N/A' }} &bull; IFSC: {{ $nom['ifsc_code'] ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 8. FAMILY MEMBER DETAILS BOX -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 font-bold text-xs flex items-center justify-center border border-brand-100">8</span>
                        <h3 class="text-sm font-bold text-slate-900">Family Members (ESIC / Insurance)</h3>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-400">Section 8</span>
                </div>

                @php $fams = $employee->family_members ?? []; @endphp
                <div class="space-y-2.5 text-xs">
                    @forelse ($fams as $idx => $f)
                        @if (!empty($f['name']))
                            <div class="p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100 flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-slate-800">{{ $f['name'] }} <span class="text-brand-600 text-xs font-semibold">({{ $f['relation'] ?? 'Relation' }})</span></div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        DOB: {{ $f['dob'] ?? 'N/A' }} &bull; Aadhaar: {{ $f['aadhaar'] ?? 'N/A' }}
                                    </div>
                                </div>
                                <span class="text-[11px] px-2.5 py-1 rounded-lg bg-brand-50 text-brand-600 font-bold">{{ $f['occupation'] ?? 'Dependent' }}</span>
                            </div>
                        @endif
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs italic bg-slate-50 rounded-2xl">
                            No family members added.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 9. DOCUMENT SUBMISSION CHECKLIST BOX -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 font-bold text-xs flex items-center justify-center border border-brand-100">9</span>
                        <h3 class="text-sm font-bold text-slate-900">Document Submission Checklist</h3>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-400">Section 9</span>
                </div>

                @php 
                    $docs = $employee->document_checklist ?? [];
                    $docLabels = [
                        'aadhaar_card' => 'Aadhaar Card (All Members)',
                        'pan_card' => 'PAN Card (All Members)',
                        'bank_proof' => 'Bank Proof / Cheque',
                        'passport_photo' => 'Passport Size Photographs',
                        'address_proof' => 'Address Proof (Utility/Rent)',
                        'pf_uan' => 'PF / UAN Details',
                        'esic_details' => 'ESIC Details',
                        'family_photo' => 'Family Photo for ESIC',
                    ];
                @endphp
                <div class="grid grid-cols-2 gap-2.5 text-xs">
                    @foreach ($docLabels as $key => $title)
                        <div class="flex items-center gap-2 p-2.5 rounded-xl border {{ isset($docs[$key]) ? 'border-emerald-200 bg-emerald-50/60 text-emerald-800 font-semibold' : 'border-slate-100 bg-slate-50 text-slate-400' }}">
                            <span class="text-xs">{{ isset($docs[$key]) ? '✓' : '✗' }}</span>
                            <span class="truncate">{{ $title }}</span>
                        </div>
                    @endforeach
                </div>
                @if (!empty($docs['other']))
                    <div class="mt-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600">
                        <span class="font-bold text-slate-700">Other Documents:</span> {{ $docs['other'] }}
                    </div>
                @endif
            </div>
        </div>

        <!-- 10. ASSET / DEVICE DETAILS BOX -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 font-bold text-xs flex items-center justify-center border border-brand-100">10</span>
                        <h3 class="text-sm font-bold text-slate-900">Assets & Device Allocation</h3>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-400">Section 10</span>
                </div>

                @php $ast = $employee->asset_details ?? []; @endphp
                <div class="grid grid-cols-2 gap-3.5 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Laptop / PC</span>
                        <span class="font-bold text-slate-800 mt-0.5 block">{{ $ast['laptop_pc'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Asset ID / Serial</span>
                        <span class="font-bold font-mono text-brand-600 mt-0.5 block">{{ $ast['asset_id'] ?? 'N/A' }} ({{ $ast['serial_number'] ?? 'N/A' }})</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Mobile / SIM</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $ast['mobile_sim'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Office Desk Phone</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $ast['office_phone'] ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Other Assets</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $ast['other_asset'] ?? 'None' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100">
                        <span class="text-slate-400 font-medium block text-[11px]">Handover Info</span>
                        <span class="font-semibold text-slate-800 mt-0.5 block">{{ $ast['handover_date'] ?? 'N/A' }} by {{ $ast['handover_by'] ?? 'IT Dept' }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-hrms-layout>
