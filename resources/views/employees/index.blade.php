<x-hrms-layout>
    @section('page_title', 'Employee Master Directory')

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

    <!-- Main Card Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Header & Action Toolbar -->
        <div class="p-4 sm:p-7 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-b from-slate-50/60 to-white">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-brand-500 text-white flex items-center justify-center shadow-md shadow-brand-500/20 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Employees Master Directory</h2>
                        <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Manage onboarding, personnel records, compliance, and department allocations.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('employees.create') }}" 
                   class="w-full sm:w-auto justify-center inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white text-xs font-bold tracking-wide shadow-md shadow-brand-500/20 transition-all hover:scale-[1.02]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>New Employee Joining</span>
                </a>
            </div>
        </div>

        <!-- Inline Compact Search & Filter Bar -->
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/40">
            <form method="GET" action="{{ route('employees.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <!-- Search Input with icon -->
                <div class="relative flex-1 w-full">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name, code, designation, email..." 
                           class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white placeholder-slate-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Department Select Filter -->
                <div class="w-full sm:w-60">
                    <select name="department" onchange="this.form.submit()" 
                            class="w-full py-2.5 px-3.5 text-xs rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white text-slate-700">
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-semibold transition shrink-0">
                        Filter
                    </button>
                    @if (request()->hasAny(['search', 'department']))
                        <a href="{{ route('employees.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold transition shrink-0">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Master Directory Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200/80">
                        <th class="py-3.5 px-6">Employee</th>
                        <th class="py-3.5 px-6">Department & Designation</th>
                        <th class="py-3.5 px-6">Contact & Email</th>
                        <th class="py-3.5 px-6">Joining Details</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($employees as $emp)
                        <tr class="hover:bg-brand-50/30 transition-colors group">
                            <!-- Employee Name & Code -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-500 to-brand-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                        {{ substr($emp->employee_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('employees.show', $emp) }}" class="font-bold text-slate-900 hover:text-brand-600 transition-colors block text-sm">
                                            {{ $emp->employee_name }}
                                        </a>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-brand-50 text-brand-600 mt-0.5 border border-brand-100">
                                            {{ $emp->employee_code }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Department & Role -->
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-800">{{ $emp->designation }}</div>
                                <div class="text-slate-400 text-[11px] mt-0.5">{{ $emp->department }}</div>
                            </td>

                            <!-- Contact & Email (Portal Login) -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-mono text-slate-800 font-bold text-xs">{{ $emp->company_email ?: $emp->personal_email ?: 'N/A' }}</span>
                                    @if ($emp->user || $emp->company_email)
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Login Enabled: Email + default pass 'password'">
                                            ✓ Login Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-medium bg-slate-100 text-slate-400" title="No company email configured">
                                            No Login
                                        </span>
                                    @endif
                                </div>
                                <div class="text-slate-400 text-[11px] mt-0.5 flex items-center gap-2">
                                    <span>{{ $emp->personal_mobile ?: 'N/A' }}</span>
                                    @if ($emp->user || $emp->company_email)
                                        <span class="text-[10px] text-slate-400 font-mono">(Pass: password)</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Joining Date & Type -->
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-700">
                                    {{ $emp->joining_date ? $emp->joining_date->format('d M, Y') : 'N/A' }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">
                                        {{ $emp->employment_type }}
                                    </span>
                                    @if ($emp->salaryStructure)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold font-mono bg-emerald-50 text-emerald-700 border border-emerald-100" title="Monthly Gross Salary">
                                            ₹{{ number_format($emp->salaryStructure->monthly_gross) }}/mo
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-6 text-center">
                                @if ($emp->status === 'Active')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Active
                                    </span>
                                @elseif ($emp->status === 'On Leave')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        On Leave
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        {{ $emp->status }}
                                    </span>
                                @endif
                            </td>

                            <!-- Quick Action Buttons -->
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- View Details Button -->
                                    <a href="{{ route('employees.show', $emp) }}" 
                                       title="View Full Profile"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-brand-500 hover:text-white text-slate-700 font-semibold transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span class="text-[11px]">View</span>
                                    </a>

                                    <!-- Edit Button -->
                                    <a href="{{ route('employees.edit', $emp) }}" 
                                       title="Edit Master Data"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-700 border border-amber-200 hover:border-amber-500 font-semibold transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span class="text-[11px]">Edit</span>
                                    </a>

                                    <!-- Delete Button -->
                                    <form method="POST" action="{{ route('employees.destroy', $emp) }}" onsubmit="return confirm('Are you sure you want to delete this employee?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Delete Record"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-400">
                                <div class="w-16 h-16 rounded-3xl bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-700">No employees found</p>
                                <p class="text-xs text-slate-400 mt-1">Try changing your search terms or add a new employee.</p>
                                <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-semibold transition">
                                    + Add New Employee
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</x-hrms-layout>
