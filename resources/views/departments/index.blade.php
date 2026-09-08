<x-hrms-layout>
    @section('page_title', 'Department Master')

    <!-- Flash Notifications -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs shadow-xs animate-fade-in">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold p-1">✕</button>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-xs shadow-xs animate-fade-in">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold p-1">✕</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
            <div class="font-bold mb-1">Please fix the errors below:</div>
            <ul class="list-disc list-inside space-y-0.5 ml-2">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{ 
            isModalOpen: false, 
            isEdit: false, 
            modalTitle: 'Add New Department',
            formAction: '{{ route('departments.store') }}',
            formMethod: 'POST',
            formData: {
                id: '',
                code: '{{ $suggestedCode }}',
                name: '',
                description: '',
                status: 'Active'
            },
            openCreate() {
                this.isEdit = false;
                this.modalTitle = 'Add New Department';
                this.formAction = '{{ route('departments.store') }}';
                this.formMethod = 'POST';
                this.formData = {
                    id: '',
                    code: '{{ $suggestedCode }}',
                    name: '',
                    description: '',
                    status: 'Active'
                };
                this.isModalOpen = true;
            },
            openEdit(dept) {
                this.isEdit = true;
                this.modalTitle = 'Edit Department - ' + dept.name;
                this.formAction = '/departments/' + dept.id;
                this.formMethod = 'POST';
                this.formData = {
                    id: dept.id,
                    code: dept.code,
                    name: dept.name,
                    description: dept.description || '',
                    status: dept.status
                };
                this.isModalOpen = true;
            }
         }">

        <!-- Main Master Card Container -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            
            <!-- Header Toolbar -->
            <div class="p-6 sm:p-7 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-5 bg-gradient-to-b from-slate-50/60 to-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-brand-500 text-white flex items-center justify-center shadow-md shadow-brand-500/20 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Department Master</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Manage organization departments, designations allocation, and workforce groups.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('designations.index') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>Designation Master</span>
                    </a>

                    <button @click="openCreate()" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition-all hover:scale-[1.02]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Add Department</span>
                    </button>
                </div>
            </div>

            <!-- Inline Filter Bar -->
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/40">
                <form method="GET" action="{{ route('departments.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search by code, department name, or description..." 
                               class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white placeholder-slate-400">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <div class="w-full sm:w-48">
                        <select name="status" onchange="this.form.submit()" 
                                class="w-full py-2.5 px-3.5 text-xs rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white text-slate-700">
                            <option value="">All Statuses</option>
                            <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-semibold transition shrink-0">
                            Filter
                        </button>
                        @if (request()->hasAny(['search', 'status']))
                            <a href="{{ route('departments.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold transition shrink-0">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Departments Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200/80">
                            <th class="py-3.5 px-6">Dept Code</th>
                            <th class="py-3.5 px-6">Department Name</th>
                            <th class="py-3.5 px-6">Description</th>
                            <th class="py-3.5 px-6 text-center">Designations</th>
                            <th class="py-3.5 px-6 text-center">Employees</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse ($departments as $dept)
                            <tr class="hover:bg-brand-50/30 transition-colors">
                                <td class="py-4 px-6 font-mono font-bold text-brand-600">
                                    <span class="px-2 py-0.5 rounded bg-brand-50 border border-brand-100">
                                        {{ $dept->code }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-bold text-slate-900 text-sm block">{{ $dept->name }}</span>
                                </td>
                                <td class="py-4 px-6 text-slate-500 max-w-xs truncate">
                                    {{ $dept->description ?: '—' }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <a href="{{ route('designations.index', ['department_id' => $dept->id]) }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 hover:bg-brand-100 text-slate-700 hover:text-brand-600 transition">
                                        {{ $dept->designations_count }} Roles
                                    </a>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <a href="{{ route('employees.index', ['department' => $dept->name]) }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-600 border border-brand-100 hover:bg-brand-100 transition">
                                        {{ $dept->employees_count }} Staff
                                    </a>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if ($dept->status === 'Active')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Edit Modal Trigger -->
                                        <button @click="openEdit({{ $dept->toJson() }})" 
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-700 border border-amber-200 hover:border-amber-500 font-semibold transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span class="text-[11px]">Edit</span>
                                        </button>

                                        <!-- Delete Form -->
                                        <form method="POST" action="{{ route('departments.destroy', $dept) }}" 
                                              onsubmit="return confirm('Are you sure you want to delete department: {{ $dept->name }}?');" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Delete Department"
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
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-bold text-slate-700">No departments found</p>
                                    <p class="text-xs text-slate-400 mt-1">Create a new department to organize your organization roles and staff.</p>
                                    <button @click="openCreate()" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-semibold transition">
                                        + Add First Department
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($departments->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $departments->links() }}
                </div>
            @endif
        </div>

        <!-- Add / Edit Department Modal Dialog -->
        <div x-show="isModalOpen" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="min-h-screen px-4 flex items-center justify-center">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="isModalOpen = false"></div>

                <!-- Modal Window -->
                <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-100 z-10">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <h3 class="text-base font-bold text-slate-900" x-text="modalTitle"></h3>
                        <button @click="isModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold p-1">✕</button>
                    </div>

                    <form :action="formAction" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <template x-if="isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Department Code <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="code" x-model="formData.code" required 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-semibold uppercase focus:ring-1 focus:ring-brand-500 focus:border-brand-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Department Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" x-model="formData.name" required placeholder="e.g. Information Technology"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-1 focus:ring-brand-500 focus:border-brand-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Description
                            </label>
                            <textarea name="description" x-model="formData.description" rows="3" placeholder="Brief notes or role responsibilities..."
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-1 focus:ring-brand-500 focus:border-brand-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Status <span class="text-rose-500">*</span>
                            </label>
                            <select name="status" x-model="formData.status" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:ring-1 focus:ring-brand-500 focus:border-brand-500">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                            <button type="button" @click="isModalOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                                Save Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-hrms-layout>
