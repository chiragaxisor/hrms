<x-hrms-layout>
    @section('page_title', 'Leave Management & Approvals')

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

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <!-- Pending Requests -->
        <a href="{{ route('leaves.index', ['status' => 'Pending', 'department' => $departmentFilter]) }}" 
           class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-amber-400 hover:shadow-md transition relative overflow-hidden {{ $statusFilter === 'Pending' ? 'ring-2 ring-amber-500 bg-amber-50/20' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Pending Action</span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
            </div>
            <span class="text-2xl font-black text-slate-900 mt-2 block">{{ $stats['pending'] }}</span>
            <span class="text-[11px] text-slate-500 mt-0.5 block font-medium">Awaiting Approval</span>
        </a>

        <!-- Approved Leaves -->
        <a href="{{ route('leaves.index', ['status' => 'Approved', 'department' => $departmentFilter]) }}" 
           class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-emerald-400 hover:shadow-md transition {{ $statusFilter === 'Approved' ? 'ring-2 ring-emerald-500 bg-emerald-50/20' : '' }}">
            <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Approved</span>
            <span class="text-2xl font-black text-slate-900 mt-2 block">{{ $stats['approved'] }}</span>
            <span class="text-[11px] text-slate-500 mt-0.5 block font-medium">Synced with Attendance</span>
        </a>

        <!-- Rejected Leaves -->
        <a href="{{ route('leaves.index', ['status' => 'Rejected', 'department' => $departmentFilter]) }}" 
           class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-rose-400 hover:shadow-md transition {{ $statusFilter === 'Rejected' ? 'ring-2 ring-rose-500 bg-rose-50/20' : '' }}">
            <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block">Rejected</span>
            <span class="text-2xl font-black text-slate-900 mt-2 block">{{ $stats['rejected'] }}</span>
            <span class="text-[11px] text-slate-500 mt-0.5 block font-medium">Denied Requests</span>
        </a>

        <!-- Staff on Leave Today -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-brand-600 uppercase tracking-wider block">On Leave Today</span>
            <span class="text-2xl font-black text-slate-900 mt-2 block">{{ $stats['on_leave_today'] }}</span>
            <span class="text-[11px] text-slate-500 mt-0.5 block font-medium">Active Attendance Leaves</span>
        </div>
    </div>

    <!-- Main Container with Alpine.js Modals -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden"
         x-data="{
             applyModalOpen: false,
             approveModalOpen: false,
             rejectModalOpen: false,
             selectedLeave: {},
             openApprove(leave) {
                 this.selectedLeave = leave;
                 this.approveModalOpen = true;
             },
             openReject(leave) {
                 this.selectedLeave = leave;
                 this.rejectModalOpen = true;
             }
         }">

        <!-- Toolbar Header -->
        <div class="p-6 sm:p-7 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-5 bg-gradient-to-b from-slate-50/60 to-white">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-brand-500 text-white flex items-center justify-center shadow-md shadow-brand-500/20 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Leave Management & Approvals</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Review, approve, or reject leave applications. Approved leaves automatically reflect as Leave in Attendance.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="button" @click="applyModalOpen = true" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Apply / Request Leave</span>
                </button>
            </div>
        </div>

        <!-- Filters Strip -->
        <div class="p-4 sm:px-7 bg-slate-50/70 border-b border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Status Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
                <a href="{{ route('leaves.index', ['department' => $departmentFilter, 'search' => $search]) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ empty($statusFilter) ? 'bg-white text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    All Requests
                </a>
                <a href="{{ route('leaves.index', ['status' => 'Pending', 'department' => $departmentFilter, 'search' => $search]) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $statusFilter === 'Pending' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    <span>Pending</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $statusFilter === 'Pending' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-700 font-bold' }}">{{ $stats['pending'] }}</span>
                </a>
                <a href="{{ route('leaves.index', ['status' => 'Approved', 'department' => $departmentFilter, 'search' => $search]) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $statusFilter === 'Approved' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Approved
                </a>
                <a href="{{ route('leaves.index', ['status' => 'Rejected', 'department' => $departmentFilter, 'search' => $search]) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $statusFilter === 'Rejected' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
                    Rejected
                </a>
            </div>

            <!-- Search & Department Form -->
            <form method="GET" action="{{ route('leaves.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                @if ($statusFilter)
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                @endif

                <select name="department" onchange="this.form.submit()" 
                        class="text-xs rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-700 focus:border-brand-500">
                    <option value="">All Departments</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->name }}" {{ $departmentFilter === $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>

                <div class="relative flex-1 md:w-56">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search employee, leave type..." 
                           class="w-full text-xs rounded-xl border border-slate-200 bg-white pl-9 pr-3 py-2 text-slate-700 focus:border-brand-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>
        </div>

        <!-- Leaves Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Employee</th>
                        <th class="py-3.5 px-4">Leave Type</th>
                        <th class="py-3.5 px-4">Dates & Duration</th>
                        <th class="py-3.5 px-4">Reason / Notes</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Action By / Remarks</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-normal">
                    @forelse ($leaveRequests as $req)
                        @php
                            $badge = $req->status_badge;
                        @endphp
                        <tr class="hover:bg-brand-50/20 transition group">
                            <!-- Employee Column -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs border border-slate-200 shrink-0">
                                        {{ substr($req->employee->employee_name ?? 'NA', 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-brand-600 transition">
                                            {{ $req->employee->employee_name ?? 'N/A' }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                            <span>{{ $req->employee->employee_code ?? '-' }}</span>
                                            <span>•</span>
                                            <span>{{ $req->employee->department ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Leave Type -->
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-bold text-xs bg-slate-100 text-slate-800 border border-slate-200">
                                    @if ($req->is_half_day)
                                        <span class="text-[10px] text-brand-600 uppercase font-black">Half</span>
                                    @endif
                                    {{ $req->leave_type }}
                                </span>
                            </td>

                            <!-- Dates & Duration -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($req->start_date)->format('d M Y') }}
                                    @if ($req->start_date !== $req->end_date)
                                        <span class="text-slate-400 font-normal">to</span> {{ \Carbon\Carbon::parse($req->end_date)->format('d M Y') }}
                                    @endif
                                </div>
                                <div class="mt-0.5">
                                    <span class="inline-flex items-center text-[10px] font-bold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-md border border-brand-100">
                                        {{ $req->total_days }} {{ $req->total_days == 1 ? 'day' : 'days' }}
                                        @if ($req->is_half_day)
                                            ({{ $req->half_day_type === 'first_half' ? '1st Half' : '2nd Half' }})
                                        @endif
                                    </span>
                                </div>
                            </td>

                            <!-- Reason -->
                            <td class="py-4 px-4 max-w-[220px]">
                                <p class="text-slate-700 font-medium line-clamp-2" title="{{ $req->reason }}">{{ $req->reason }}</p>
                                <span class="text-[10px] text-slate-400 mt-0.5 block">Applied: {{ $req->created_at->format('d M Y, h:i A') }}</span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $badge['bg'] }} {{ $badge['text'] }} border {{ $badge['border'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    {{ $req->status }}
                                </span>
                            </td>

                            <!-- Action By & Remarks -->
                            <td class="py-4 px-4 text-[11px]">
                                @if ($req->action_by && $req->actionBy)
                                    <div class="font-semibold text-slate-800">{{ $req->actionBy->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $req->action_at ? $req->action_at->format('d M, h:i A') : '' }}</div>
                                @endif
                                @if ($req->admin_remarks)
                                    <div class="text-slate-600 italic mt-0.5 text-[11px] max-w-[160px] truncate" title="{{ $req->admin_remarks }}">"{{ $req->admin_remarks }}"</div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Actions (Approve / Reject) -->
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                @if (Auth::user()->isAdmin() && $req->status === 'Pending')
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" @click="openApprove({{ $req->toJson() }})" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            <span>Approve</span>
                                        </button>
                                        <button type="button" @click="openReject({{ $req->toJson() }})" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition border border-rose-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            <span>Reject</span>
                                        </button>
                                    </div>
                                @elseif ($req->status === 'Pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span>Under Review</span>
                                    </span>
                                @elseif (Auth::user()->isAdmin())
                                    <div class="inline-flex items-center gap-2">
                                        <form method="POST" action="{{ route('leaves.destroy', $req->id) }}" onsubmit="return confirm('Delete this leave request record?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Delete Leave Request">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-6 text-center text-slate-500">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">No Leave Requests Found</h4>
                                    <p class="text-xs text-slate-400">There are no leave requests matching your current filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($leaveRequests->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $leaveRequests->links() }}
            </div>
        @endif

        <!-- Modal 1: Apply / Request Leave -->
        <div x-show="applyModalOpen" x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="applyModalOpen = false" 
                 class="bg-white rounded-3xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-5 sm:p-7 shadow-2xl border border-slate-100 space-y-4 sm:space-y-5 animate-scale-in">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Apply for Leave</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Submit leave request for approval</p>
                    </div>
                    <button type="button" @click="applyModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">✕</button>
                </div>

                <form method="POST" action="{{ route('leaves.store') }}" class="space-y-4" x-data="{ isHalfDay: false }">
                    @csrf
                    
                    @if (Auth::user()->isAdmin())
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Select Employee *</label>
                            <select name="employee_id" required 
                                    class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                                <option value="">-- Choose Employee --</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ ($myEmployee && $myEmployee->id === $emp->id) ? 'selected' : '' }}>
                                        {{ $emp->employee_name }} ({{ $emp->employee_code }}) - {{ $emp->department }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="employee_id" value="{{ $myEmployee?->id }}">
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold flex items-center justify-between">
                            <span>Applying As:</span>
                            <span class="text-brand-600 font-bold">{{ $myEmployee?->employee_name }} ({{ $myEmployee?->employee_code }})</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Leave Type *</label>
                            <select name="leave_type" required 
                                    class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500">
                                <option value="Paid Leave">Paid Leave (PL)</option>
                                <option value="Casual Leave">Casual Leave (CL)</option>
                                <option value="Sick Leave">Sick Leave (SL)</option>
                                <option value="Unpaid Leave">Unpaid Leave (LWP)</option>
                                <option value="Maternity Leave">Maternity Leave</option>
                                <option value="Paternity Leave">Paternity Leave</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Leave Nature</label>
                            <label class="flex items-center gap-2 mt-2 cursor-pointer">
                                <input type="checkbox" name="is_half_day" value="1" x-model="isHalfDay" 
                                       class="rounded text-brand-600 focus:ring-brand-500 w-4 h-4 border-slate-300">
                                <span class="text-xs font-semibold text-slate-700">Half Day Leave</span>
                            </label>
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                <span x-show="!isHalfDay">Start Date *</span>
                                <span x-show="isHalfDay">Leave Date *</span>
                            </label>
                            <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" 
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500">
                        </div>
                        <div x-show="!isHalfDay">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">End Date *</label>
                            <input type="date" name="end_date" value="{{ date('Y-m-d') }}" 
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500">
                        </div>
                        <div x-show="isHalfDay">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Half Day Session</label>
                            <select name="half_day_type" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500">
                                <option value="first_half">First Half (Morning)</option>
                                <option value="second_half">Second Half (Afternoon)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Reason for Leave *</label>
                        <textarea name="reason" rows="3" required placeholder="Explain purpose (e.g. medical checkup, personal urgent work, family function)..." 
                                  class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="applyModalOpen = false" 
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 2: Approve Leave Modal -->
        <div x-show="approveModalOpen" x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="approveModalOpen = false" 
                 class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5 animate-scale-in">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Approve Leave Request</h3>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="selectedLeave.employee ? selectedLeave.employee.employee_name : ''"></p>
                        </div>
                    </div>
                    <button type="button" @click="approveModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">✕</button>
                </div>

                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-xs text-emerald-900 space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Automatic Attendance Effect:
                    </div>
                    <p class="text-emerald-800">Upon approval, attendance will automatically be logged as <strong x-text="selectedLeave.is_half_day ? 'Half Day' : 'Leave'"></strong> for the requested dates in the Daily Attendance Register.</p>
                </div>

                <form :action="'{{ url('/leaves') }}/' + selectedLeave.id + '/approve'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Approval Remarks (Optional)</label>
                        <input type="text" name="admin_remarks" placeholder="e.g. Approved as per company policy" value="Approved by Admin" 
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-emerald-600">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="approveModalOpen = false" 
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                            Confirm & Approve Leave
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 3: Reject Leave Modal -->
        <div x-show="rejectModalOpen" x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="rejectModalOpen = false" 
                 class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 space-y-5 animate-scale-in">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Reject Leave Request</h3>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="selectedLeave.employee ? selectedLeave.employee.employee_name : ''"></p>
                        </div>
                    </div>
                    <button type="button" @click="rejectModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">✕</button>
                </div>

                <form :action="'{{ url('/leaves') }}/' + selectedLeave.id + '/reject'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Reason for Rejection *</label>
                        <textarea name="admin_remarks" rows="3" required placeholder="State reason for rejecting (e.g. Critical project deadline, staffing shortage, leave balance exhausted)..." 
                                  class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-rose-600"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="rejectModalOpen = false" 
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-600/20 transition">
                            Reject Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-hrms-layout>
