<x-hrms-layout>
    @section('page_title', 'Edit Employee - ' . $employee->employee_name)

    @php
        $formData = $formData ?? \App\Support\EmployeeFormData::all($employee->id);
        $bloodGroups = $bloodGroups ?? $formData['bloodGroups'];
        $workLocations = $workLocations ?? $formData['workLocations'];
        $branches = $branches ?? $formData['branches'];
        $banks = $banks ?? $formData['banks'];
        $states = $states ?? $formData['states'];
        $cities = $cities ?? $formData['cities'];
        $emergencyRelations = $emergencyRelations ?? $formData['emergencyRelations'];
        $referenceRelations = $referenceRelations ?? $formData['referenceRelations'];
        $nomineeRelations = $nomineeRelations ?? $formData['nomineeRelations'];
        $familyRelations = $familyRelations ?? $formData['familyRelations'];
        $occupations = $occupations ?? $formData['occupations'];
        $laptopModels = $laptopModels ?? $formData['laptopModels'];
        $simOptions = $simOptions ?? $formData['simOptions'];
        $reportingPersons = $reportingPersons ?? $formData['reportingPersons'];
        $verifiers = $verifiers ?? $formData['verifiers'];
    @endphp

    <!-- Breadcrumb & Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('employees.index') }}" class="hover:text-brand-600 transition">Employees</a>
                <span>/</span>
                <a href="{{ route('employees.show', $employee) }}" class="hover:text-brand-600 transition">{{ $employee->employee_code }}</a>
                <span>/</span>
                <span class="text-slate-600 font-semibold">Edit Master Data</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Employee Master Data</h2>
            <p class="text-xs text-slate-500 mt-1">Update employee profile, statutory records, banking, and compliance details.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('employees.show', $employee) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span>View Profile</span>
            </a>

            <a href="{{ route('employees.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition">
                <span>Back to List</span>
            </a>
        </div>
    </div>

    <!-- Error Validation summary -->
    @if (isset($errors) && $errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
            <div class="font-bold mb-1 flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Please fix the following form errors:
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-2">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Master Multi-Section Form using Alpine.js for Tabs -->
    <form method="POST" action="{{ route('employees.update', $employee) }}" x-ref="employeeEditForm" 
          x-data="{ 
              activeTab: '{{ request('tab', 'job') }}',
              sameAddress: false,
              selectedDepartment: '{{ old('department', $employee->department) }}',
              selectedDesignation: '{{ old('designation', $employee->designation) }}',
              allDepartments: {{ $departments->toJson() }},
              allDesignations: {{ $designations->toJson() }},
              filteredDesignations: [],
              init() {
                  this.updateFilteredDesignations(false);
              },
              onDepartmentChange() {
                  this.selectedDesignation = '';
                  this.updateFilteredDesignations(true);
              },
              updateFilteredDesignations(resetSelected = false) {
                  if (!this.selectedDepartment) {
                      this.filteredDesignations = this.allDesignations;
                      return;
                  }
                  const dept = this.allDepartments.find(d => d.name === this.selectedDepartment);
                  if (dept) {
                      const matched = this.allDesignations.filter(d => d.department_id === dept.id);
                      this.filteredDesignations = matched.length > 0 ? matched : this.allDesignations;
                  } else {
                      this.filteredDesignations = this.allDesignations;
                  }
              },
              copyAddress() {
                  if (this.sameAddress) {
                      $refs.perm_address.value = $refs.comm_address.value;
                      $refs.perm_city.value = $refs.comm_city.value;
                      $refs.perm_pincode.value = $refs.comm_pincode.value;
                      const commStateVal = document.querySelector('input[name="address_details[communication][state]"]')?.value || '';
                      window.dispatchEvent(new CustomEvent('sync-perm-state', { detail: commStateVal }));
                  }
              },
              submitWithNextTab(nextTab) {
                  $refs.nextTabInput.value = nextTab;
                  $refs.employeeEditForm.submit();
              }
          }">
        @csrf
        @method('PUT')
        <input type="hidden" name="next_tab" x-ref="nextTabInput" value="">

        <!-- Section Tabs Navigation -->
        <div class="bg-white rounded-3xl p-2 border border-slate-100 shadow-sm mb-6 overflow-x-auto scrollbar-none">
            <div class="flex items-center gap-1.5 min-w-max">
                <button type="button" @click="activeTab = 'job'" 
                        :class="activeTab === 'job' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-100'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-semibold transition">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                          :class="activeTab === 'job' ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">1</span>
                    <span>Job Details</span>
                </button>

                <button type="button" @click="activeTab = 'personal'" 
                        :class="activeTab === 'personal' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-100'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-semibold transition">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                          :class="activeTab === 'personal' ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">2</span>
                    <span>Personal</span>
                </button>

                <button type="button" @click="activeTab = 'statutory'" 
                        :class="activeTab === 'statutory' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-100'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-semibold transition">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                          :class="activeTab === 'statutory' ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">3</span>
                    <span>Statutory / PF</span>
                </button>

                <button type="button" @click="activeTab = 'bank'" 
                        :class="activeTab === 'bank' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-100'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-semibold transition">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                          :class="activeTab === 'bank' ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">4</span>
                    <span>Bank Account</span>
                </button>

                <button type="button" @click="activeTab = 'address'" 
                        :class="activeTab === 'address' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-100'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-semibold transition">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                          :class="activeTab === 'address' ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">5</span>
                    <span>Address</span>
                </button>

                <button type="button" @click="activeTab = 'emergency'" 
                        :class="activeTab === 'emergency' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-100'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-semibold transition">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                          :class="activeTab === 'emergency' ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">6</span>
                    <span>Emergency / Ref</span>
                </button>

                <button type="button" @click="activeTab = 'nominee'" 
                        :class="activeTab === 'nominee' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-100'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-semibold transition">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                          :class="activeTab === 'nominee' ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">7</span>
                    <span>Nominee</span>
                </button>

                <button type="button" @click="activeTab = 'family'" 
                        :class="activeTab === 'family' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-100'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-semibold transition">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                          :class="activeTab === 'family' ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">8</span>
                    <span>Family Members</span>
                </button>

                <button type="button" @click="activeTab = 'docs'" 
                        :class="activeTab === 'docs' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-100'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-semibold transition">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                          :class="activeTab === 'docs' ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">9</span>
                    <span>Documents Checklist</span>
                </button>

                <button type="button" @click="activeTab = 'assets'" 
                        :class="activeTab === 'assets' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-100'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-semibold transition">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                          :class="activeTab === 'assets' ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">10</span>
                    <span>Assets / Devices</span>
                </button>
            </div>
        </div>

        <!-- Tab 1: EMPLOYEE & JOB DETAILS -->
        <div x-show="activeTab === 'job'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">1. Employee & Job Details</h3>
                    <p class="text-xs text-slate-500">Essential organization, designation and employment metadata</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 1 of 10</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Employee Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Employee Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="employee_name" value="{{ old('employee_name', $employee->employee_name) }}" required 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <!-- Employee Code -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Employee Code / Ref. Number <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" required 
                           class="w-full px-3.5 py-2.5 text-xs font-mono font-bold rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-slate-50">
                </div>

                <!-- Department Select -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Department <span class="text-rose-500">*</span>
                    </label>
                    <select name="department" 
                            x-model="selectedDepartment"
                            @change="onDepartmentChange()"
                            required 
                            class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        <option value="">-- Select Department --</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->name }}" :selected="selectedDepartment === '{{ $dept->name }}'">
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Designation Select -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Designation <span class="text-rose-500">*</span>
                    </label>
                    <select name="designation" 
                            x-model="selectedDesignation"
                            required 
                            class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        <option value="">-- Select Designation --</option>
                        <template x-for="desig in filteredDesignations" :key="desig.id">
                            <option :value="desig.title" x-text="desig.title" :selected="desig.title === selectedDesignation"></option>
                        </template>
                    </select>
                </div>

                <!-- Reporting Person Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Reporting Person Name
                    </label>
                    <x-searchable-select 
                        name="reporting_person_name" 
                        :value="old('reporting_person_name', $employee->reporting_person_name)" 
                        :options="$reportingPersons" 
                        placeholder="Search employee or type reporting manager..." 
                        :allowCustom="true" 
                    />
                </div>

                <!-- Work Location -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Work Location
                    </label>
                    <x-searchable-select 
                        name="work_location" 
                        :value="old('work_location', $employee->work_location)" 
                        :options="$workLocations" 
                        placeholder="Select or enter work location..." 
                        :allowCustom="true" 
                    />
                </div>

                <!-- Branch -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Branch
                    </label>
                    <x-searchable-select 
                        name="branch" 
                        :value="old('branch', $employee->branch)" 
                        :options="$branches" 
                        placeholder="Select or enter branch office..." 
                        :allowCustom="true" 
                    />
                </div>

                <!-- Office Number -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Office Number / Desk Ext.
                    </label>
                    <input type="text" name="office_number" value="{{ old('office_number', $employee->office_number) }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <!-- Company Email ID -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Company Email ID (Login Username)
                    </label>
                    <input type="email" name="company_email" value="{{ old('company_email', $employee->company_email) }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    <span class="text-[11px] text-brand-600 font-medium mt-1 block">
                        ★ Employee uses this email to log in with password: <strong>password</strong>
                    </span>
                </div>

                <!-- Joining Date -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Joining Date
                    </label>
                    <input type="date" name="joining_date" value="{{ old('joining_date', optional($employee->joining_date)->format('Y-m-d')) }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <!-- Employment Type -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Employment Type <span class="text-rose-500">*</span>
                    </label>
                    <select name="employment_type" required 
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        <option value="Full Time" {{ old('employment_type', $employee->employment_type) === 'Full Time' ? 'selected' : '' }}>Full Time</option>
                        <option value="Part Time" {{ old('employment_type', $employee->employment_type) === 'Part Time' ? 'selected' : '' }}>Part Time</option>
                        <option value="Contract" {{ old('employment_type', $employee->employment_type) === 'Contract' ? 'selected' : '' }}>Contract</option>
                        <option value="Intern" {{ old('employment_type', $employee->employment_type) === 'Intern' ? 'selected' : '' }}>Intern</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Employee Status <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" required 
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        <option value="Active" {{ old('status', $employee->status) === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="On Leave" {{ old('status', $employee->status) === 'On Leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="Resigned" {{ old('status', $employee->status) === 'Resigned' ? 'selected' : '' }}>Resigned</option>
                        <option value="Terminated" {{ old('status', $employee->status) === 'Terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <div class="text-xs text-slate-400">Step 1 of 10</div>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'personal'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        <span>Skip to Personal Details</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                    <button type="button" @click="submitWithNextTab('personal')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        <span>Save & Next: Personal Details</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab 2: PERSONAL DETAILS -->
        <div x-show="activeTab === 'personal'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">2. Personal Details</h3>
                    <p class="text-xs text-slate-500">Identity, contact numbers, government identifiers & background</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 2 of 10</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Date of Birth -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($employee->date_of_birth)->format('Y-m-d')) }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Gender</label>
                    <select name="gender" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender', $employee->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $employee->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $employee->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Marital Status -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Marital Status</label>
                    <select name="marital_status" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        <option value="Single" {{ old('marital_status', $employee->marital_status) === 'Single' ? 'selected' : '' }}>Single</option>
                        <option value="Married" {{ old('marital_status', $employee->marital_status) === 'Married' ? 'selected' : '' }}>Married</option>
                        <option value="Other" {{ old('marital_status', $employee->marital_status) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Anniversary Date -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Anniversary Date</label>
                    <input type="date" name="anniversary_date" value="{{ old('anniversary_date', optional($employee->anniversary_date)->format('Y-m-d')) }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <!-- Personal Mobile Number -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Personal Mobile Number</label>
                    <input type="text" name="personal_mobile" value="{{ old('personal_mobile', $employee->personal_mobile) }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <!-- Personal Email ID -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Personal Email ID</label>
                    <input type="email" name="personal_email" value="{{ old('personal_email', $employee->personal_email) }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <!-- Aadhaar-Linked Mobile Number -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Aadhaar-Linked Mobile Number</label>
                    <input type="text" name="aadhaar_linked_mobile" value="{{ old('aadhaar_linked_mobile', $employee->aadhaar_linked_mobile) }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <!-- Blood Group -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Blood Group</label>
                    <select name="blood_group" 
                            class="w-full px-3.5 py-2.5 text-xs font-medium rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        <option value="">-- Select Blood Group --</option>
                        @foreach ($bloodGroups as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group', $employee->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- PAN Number -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">PAN Number</label>
                    <input type="text" name="pan_number" value="{{ old('pan_number', $employee->pan_number) }}" 
                           class="w-full px-3.5 py-2.5 text-xs font-mono uppercase rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <!-- Aadhaar Number -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Aadhaar Number</label>
                    <input type="text" name="aadhaar_number" value="{{ old('aadhaar_number', $employee->aadhaar_number) }}" 
                           class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'job'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'statutory'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        <span>Skip to Statutory</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                    <button type="button" @click="submitWithNextTab('statutory')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        <span>Save & Next: Statutory Details</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab 3: STATUTORY / GOVERNMENT DETAILS -->
        @php $stat = $employee->statutory_details ?? []; @endphp
        <div x-show="activeTab === 'statutory'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">3. Statutory / Government Details</h3>
                    <p class="text-xs text-slate-500">PF, UAN, ESIC and statutory verification records</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 3 of 10</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">UAN Number</label>
                    <input type="text" name="statutory_details[uan_number]" value="{{ old('statutory_details.uan_number', $stat['uan_number'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">ESIC Number</label>
                    <input type="text" name="statutory_details[esic_number]" value="{{ old('statutory_details.esic_number', $stat['esic_number'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">PF / ESIC Documents Received</label>
                    <select name="statutory_details[docs_received]" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        <option value="Yes" {{ old('statutory_details.docs_received', $stat['docs_received'] ?? '') === 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ old('statutory_details.docs_received', $stat['docs_received'] ?? '') === 'No' ? 'selected' : '' }}>No</option>
                        <option value="N/A" {{ old('statutory_details.docs_received', $stat['docs_received'] ?? '') === 'N/A' ? 'selected' : '' }}>N/A</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Previous PF Account / UAN Details</label>
                    <input type="text" name="statutory_details[prev_pf_uan]" value="{{ old('statutory_details.prev_pf_uan', $stat['prev_pf_uan'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Previous ESIC Details</label>
                    <input type="text" name="statutory_details[prev_esic]" value="{{ old('statutory_details.prev_esic', $stat['prev_esic'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Statutory Details Verified By</label>
                    <x-searchable-select 
                        name="statutory_details[verified_by]" 
                        :value="old('statutory_details.verified_by', $stat['verified_by'] ?? Auth::user()->name)" 
                        :options="$verifiers" 
                        placeholder="Select verifier or type name..." 
                        :allowCustom="true" 
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Verification Date</label>
                    <input type="date" name="statutory_details[verification_date]" value="{{ old('statutory_details.verification_date', $stat['verification_date'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'personal'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'bank'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        <span>Skip to Bank</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                    <button type="button" @click="submitWithNextTab('bank')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        <span>Save & Next: Bank Details</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab 4: BANK / SALARY ACCOUNT DETAILS -->
        @php $bank = $employee->bank_details ?? []; @endphp
        <div x-show="activeTab === 'bank'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">4. Bank / Salary Account Details</h3>
                    <p class="text-xs text-slate-500">Bank credentials for direct salary transfer and payroll processing</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 4 of 10</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bank Name</label>
                    <x-searchable-select 
                        name="bank_details[bank_name]" 
                        :value="old('bank_details.bank_name', $bank['bank_name'] ?? '')" 
                        :options="$banks" 
                        placeholder="Search bank name (e.g. HDFC, SBI)..." 
                        :allowCustom="true" 
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Account Holder Name</label>
                    <input type="text" name="bank_details[account_holder]" value="{{ old('bank_details.account_holder', $bank['account_holder'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Account Number</label>
                    <input type="text" name="bank_details[account_number]" value="{{ old('bank_details.account_number', $bank['account_number'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">IFSC Code</label>
                    <input type="text" name="bank_details[ifsc_code]" value="{{ old('bank_details.ifsc_code', $bank['ifsc_code'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs font-mono uppercase rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Branch Name</label>
                    <input type="text" name="bank_details[branch_name]" value="{{ old('bank_details.branch_name', $bank['branch_name'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Account Type</label>
                    <select name="bank_details[account_type]" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        <option value="Savings" {{ old('bank_details.account_type', $bank['account_type'] ?? '') === 'Savings' ? 'selected' : '' }}>Savings</option>
                        <option value="Current" {{ old('bank_details.account_type', $bank['account_type'] ?? '') === 'Current' ? 'selected' : '' }}>Current</option>
                        <option value="Salary" {{ old('bank_details.account_type', $bank['account_type'] ?? '') === 'Salary' ? 'selected' : '' }}>Salary</option>
                        <option value="Other" {{ old('bank_details.account_type', $bank['account_type'] ?? '') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Cancelled Cheque / Bank Proof Received</label>
                    <select name="bank_details[cheque_received]" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        <option value="Yes" {{ old('bank_details.cheque_received', $bank['cheque_received'] ?? '') === 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ old('bank_details.cheque_received', $bank['cheque_received'] ?? '') === 'No' ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bank Details Verified By</label>
                    <x-searchable-select 
                        name="bank_details[verified_by]" 
                        :value="old('bank_details.verified_by', $bank['verified_by'] ?? Auth::user()->name)" 
                        :options="$verifiers" 
                        placeholder="Select verifier or type name..." 
                        :allowCustom="true" 
                    />
                </div>
            </div>

            <!-- Salary Structure Section (CTC, Gross, Basic, HRA) -->
            @php $salary = $employee->salaryStructure; @endphp
            <div class="p-5 rounded-2xl bg-brand-50/50 border border-brand-100 space-y-4"
                 x-data="{
                     monthlyGross: '{{ old('salary_structure.monthly_gross', $salary->monthly_gross ?? 35000) }}',
                     basicSalary: '{{ old('salary_structure.basic_salary', $salary->basic_salary ?? 17500) }}',
                     hra: '{{ old('salary_structure.hra', $salary->hra ?? 8750) }}',
                     specialAllowance: '{{ old('salary_structure.special_allowance', $salary->special_allowance ?? 8750) }}',
                     pf: '{{ old('salary_structure.pf_deduction', $salary->pf_deduction ?? 1800) }}',
                     pt: '{{ old('salary_structure.professional_tax', $salary->professional_tax ?? 200) }}',
                     tds: '{{ old('salary_structure.tds', $salary->tds ?? 0) }}',
                     recalc() {
                         let g = Number(this.monthlyGross || 0);
                         this.basicSalary = Math.round(g * 0.5);
                         this.hra = Math.round(g * 0.25);
                         this.specialAllowance = Math.round(g * 0.25);
                     }
                 }">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-brand-800 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            Employee Salary Package & Compensation (Monthly)
                        </h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Define base monthly salary used for automated payroll calculation</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-brand-600 bg-white px-3 py-1 rounded-xl border border-brand-200" x-text="'Annual CTC: ₹' + Number(monthlyGross * 12).toLocaleString('en-IN')"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Monthly Gross Salary (₹) *</label>
                        <input type="number" name="salary_structure[monthly_gross]" x-model="monthlyGross" @input="recalc()" required step="0.01" 
                               class="w-full px-3.5 py-2.5 text-xs font-bold font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Basic Pay (50%)</label>
                        <input type="number" name="salary_structure[basic_salary]" x-model="basicSalary" step="0.01" 
                               class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">HRA (25%)</label>
                        <input type="number" name="salary_structure[hra]" x-model="hra" step="0.01" 
                               class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Special Allowance</label>
                        <input type="number" name="salary_structure[special_allowance]" x-model="specialAllowance" step="0.01" 
                               class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-brand-100/80">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">PF Deduction (₹)</label>
                        <input type="number" name="salary_structure[pf_deduction]" x-model="pf" step="0.01" 
                               class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Professional Tax (PT) (₹)</label>
                        <input type="number" name="salary_structure[professional_tax]" x-model="pt" step="0.01" 
                               class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Income Tax (TDS) (₹)</label>
                        <input type="number" name="salary_structure[tds]" x-model="tds" step="0.01" 
                               class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'statutory'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'address'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        <span>Skip to Address</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                    <button type="button" @click="submitWithNextTab('address')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        <span>Save & Next: Address Details</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab 5: ADDRESS DETAILS -->
        @php $addr = $employee->address_details ?? []; @endphp
        <div x-show="activeTab === 'address'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">5. Address Details</h3>
                    <p class="text-xs text-slate-500">Communication & Permanent residential addresses</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 5 of 10</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Communication Address -->
                <div class="bg-slate-50/70 p-6 rounded-2xl border border-slate-200/70 space-y-4">
                    <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                        Communication / Mailing Address
                    </h4>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Full Address</label>
                        <textarea x-ref="comm_address" name="address_details[communication][address]" rows="3" 
                                  class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">{{ old('address_details.communication.address', $addr['communication']['address'] ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">City</label>
                            <input x-ref="comm_city" type="text" name="address_details[communication][city]" value="{{ old('address_details.communication.city', $addr['communication']['city'] ?? '') }}" list="indian_cities_list_edit" placeholder="City" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">State</label>
                            <x-searchable-select 
                                name="address_details[communication][state]" 
                                :value="old('address_details.communication.state', $addr['communication']['state'] ?? '')" 
                                :options="$states" 
                                placeholder="Search state / UT..." 
                                :allowCustom="true" 
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">PIN Code</label>
                            <input x-ref="comm_pincode" type="text" name="address_details[communication][pincode]" value="{{ old('address_details.communication.pincode', $addr['communication']['pincode'] ?? '') }}" placeholder="PIN" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        </div>
                    </div>
                </div>

                <!-- Permanent Address -->
                <div class="bg-slate-50/70 p-6 rounded-2xl border border-slate-200/70 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                            Permanent Address
                        </h4>
                        <label class="inline-flex items-center gap-2 text-xs font-medium text-brand-600 cursor-pointer">
                            <input type="checkbox" x-model="sameAddress" @change="copyAddress" 
                                   class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 h-4 w-4">
                            <span>Same as Communication</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Full Address</label>
                        <textarea x-ref="perm_address" name="address_details[permanent][address]" rows="3" 
                                  class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">{{ old('address_details.permanent.address', $addr['permanent']['address'] ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">City</label>
                            <input x-ref="perm_city" type="text" name="address_details[permanent][city]" value="{{ old('address_details.permanent.city', $addr['permanent']['city'] ?? '') }}" list="indian_cities_list_edit" placeholder="City" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">State</label>
                            <x-searchable-select 
                                name="address_details[permanent][state]" 
                                :value="old('address_details.permanent.state', $addr['permanent']['state'] ?? '')" 
                                :options="$states" 
                                placeholder="Search state / UT..." 
                                :allowCustom="true" 
                                eventSync="sync-perm-state"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">PIN Code</label>
                            <input x-ref="perm_pincode" type="text" name="address_details[permanent][pincode]" value="{{ old('address_details.permanent.pincode', $addr['permanent']['pincode'] ?? '') }}" placeholder="PIN" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cities Datalist Autocomplete -->
            <datalist id="indian_cities_list_edit">
                @foreach ($cities as $cityName)
                    <option value="{{ $cityName }}"></option>
                @endforeach
            </datalist>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'bank'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'emergency'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        <span>Skip to Emergency</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                    <button type="button" @click="submitWithNextTab('emergency')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        <span>Save & Next: Emergency & Ref</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab 6: EMERGENCY / REFERENCE CONTACT -->
        @php 
            $emg = $employee->emergency_contact ?? [];
            $ref = $employee->reference_contact ?? [];
        @endphp
        <div x-show="activeTab === 'emergency'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">6. Emergency / Reference Contact</h3>
                    <p class="text-xs text-slate-500">Emergency reach-out contact and professional reference checks</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 6 of 10</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Emergency Contact -->
                <div class="bg-slate-50/70 p-6 rounded-2xl border border-slate-200/70 space-y-4">
                    <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Emergency Contact
                    </h4>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Contact Name</label>
                        <input type="text" name="emergency_contact[name]" value="{{ old('emergency_contact.name', $emg['name'] ?? '') }}" 
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Mobile Number</label>
                            <input type="text" name="emergency_contact[mobile]" value="{{ old('emergency_contact.mobile', $emg['mobile'] ?? '') }}" 
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Relation</label>
                            <select name="emergency_contact[relation]" 
                                    class="w-full px-3 py-2 text-xs font-medium rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                                <option value="">Select Relation</option>
                                @foreach ($emergencyRelations as $rel)
                                    <option value="{{ $rel }}" {{ old('emergency_contact.relation', $emg['relation'] ?? '') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Address</label>
                        <input type="text" name="emergency_contact[address]" value="{{ old('emergency_contact.address', $emg['address'] ?? '') }}" 
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    </div>
                </div>

                <!-- Professional Reference -->
                <div class="bg-slate-50/70 p-6 rounded-2xl border border-slate-200/70 space-y-4">
                    <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                        Professional / Personal Reference
                    </h4>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Reference Name</label>
                        <input type="text" name="reference_contact[name]" value="{{ old('reference_contact.name', $ref['name'] ?? '') }}" 
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Mobile Number</label>
                            <input type="text" name="reference_contact[mobile]" value="{{ old('reference_contact.mobile', $ref['mobile'] ?? '') }}" 
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Relation / Association</label>
                            <select name="reference_contact[relation]" 
                                    class="w-full px-3 py-2 text-xs font-medium rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                                <option value="">Select Association</option>
                                @foreach ($referenceRelations as $rel)
                                    <option value="{{ $rel }}" {{ old('reference_contact.relation', $ref['relation'] ?? '') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Reference Checked By</label>
                            <x-searchable-select 
                                name="reference_contact[checked_by]" 
                                :value="old('reference_contact.checked_by', $ref['checked_by'] ?? Auth::user()->name)" 
                                :options="$verifiers" 
                                placeholder="Select verifier..." 
                                :allowCustom="true" 
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Reference Check Date</label>
                            <input type="date" name="reference_contact[check_date]" value="{{ old('reference_contact.check_date', $ref['check_date'] ?? '') }}" 
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Remarks</label>
                        <input type="text" name="reference_contact[remarks]" value="{{ old('reference_contact.remarks', $ref['remarks'] ?? '') }}" 
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'address'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'nominee'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        <span>Skip to Nominee</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                    <button type="button" @click="submitWithNextTab('nominee')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        <span>Save & Next: Nominee Details</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab 7: NOMINEE DETAILS -->
        @php $nom = $employee->nominee_details ?? []; @endphp
        <div x-show="activeTab === 'nominee'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">7. Nominee Details</h3>
                    <p class="text-xs text-slate-500">Statutory nominee information and nominee bank account</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 7 of 10</span>
            </div>

            <!-- Nominee Personal Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nominee Name</label>
                    <input type="text" name="nominee_details[name]" value="{{ old('nominee_details.name', $nom['name'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nominee Relation</label>
                    <select name="nominee_details[relation]" 
                            class="w-full px-3.5 py-2.5 text-xs font-medium rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        <option value="">Select Nominee Relation</option>
                        @foreach ($nomineeRelations as $rel)
                            <option value="{{ $rel }}" {{ old('nominee_details.relation', $nom['relation'] ?? '') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nominee Date of Birth</label>
                    <input type="date" name="nominee_details[dob]" value="{{ old('nominee_details.dob', $nom['dob'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nominee Aadhaar-Linked Mobile</label>
                    <input type="text" name="nominee_details[aadhaar_mobile]" value="{{ old('nominee_details.aadhaar_mobile', $nom['aadhaar_mobile'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nominee Aadhaar Number</label>
                    <input type="text" name="nominee_details[aadhaar]" value="{{ old('nominee_details.aadhaar', $nom['aadhaar'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nominee PAN Number</label>
                    <input type="text" name="nominee_details[pan]" value="{{ old('nominee_details.pan', $nom['pan'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs font-mono uppercase rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nominee Occupation</label>
                    <select name="nominee_details[occupation]" 
                            class="w-full px-3.5 py-2.5 text-xs font-medium rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        <option value="">Select Nominee Occupation</option>
                        @foreach ($occupations as $occ)
                            <option value="{{ $occ }}" {{ old('nominee_details.occupation', $nom['occupation'] ?? '') === $occ ? 'selected' : '' }}>{{ $occ }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nominee Details Cross-Checked By</label>
                    <x-searchable-select 
                        name="nominee_details[verified_by]" 
                        :value="old('nominee_details.verified_by', $nom['verified_by'] ?? Auth::user()->name)" 
                        :options="$verifiers" 
                        placeholder="Select verifier..." 
                        :allowCustom="true" 
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Date of Verification</label>
                    <input type="date" name="nominee_details[verification_date]" value="{{ old('nominee_details.verification_date', $nom['verification_date'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
            </div>

            <!-- Nominee Bank Details -->
            <div class="mt-4 p-5 rounded-2xl bg-slate-50/80 border border-slate-200/70">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    Nominee Bank Account Details
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Bank Name</label>
                        <x-searchable-select 
                            name="nominee_details[bank_name]" 
                            :value="old('nominee_details.bank_name', $nom['bank_name'] ?? '')" 
                            :options="$banks" 
                            placeholder="Search bank name..." 
                            :allowCustom="true" 
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Account Holder Name</label>
                        <input type="text" name="nominee_details[account_holder]" value="{{ old('nominee_details.account_holder', $nom['account_holder'] ?? '') }}" 
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Account Number</label>
                        <input type="text" name="nominee_details[account_number]" value="{{ old('nominee_details.account_number', $nom['account_number'] ?? '') }}" 
                               class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">IFSC Code</label>
                        <input type="text" name="nominee_details[ifsc_code]" value="{{ old('nominee_details.ifsc_code', $nom['ifsc_code'] ?? '') }}" 
                               class="w-full px-3 py-2 text-xs font-mono uppercase rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Branch Name</label>
                        <input type="text" name="nominee_details[branch_name]" value="{{ old('nominee_details.branch_name', $nom['branch_name'] ?? '') }}" 
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Account Type</label>
                        <select name="nominee_details[account_type]" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                            <option value="Savings" {{ old('nominee_details.account_type', $nom['account_type'] ?? '') === 'Savings' ? 'selected' : '' }}>Savings</option>
                            <option value="Current" {{ old('nominee_details.account_type', $nom['account_type'] ?? '') === 'Current' ? 'selected' : '' }}>Current</option>
                            <option value="Other" {{ old('nominee_details.account_type', $nom['account_type'] ?? '') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'emergency'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'family'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        <span>Skip to Family</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                    <button type="button" @click="submitWithNextTab('family')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        <span>Save & Next: Family Members</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab 8: FAMILY MEMBER DETAILS -->
        @php $fams = $employee->family_members ?? []; @endphp
        <div x-show="activeTab === 'family'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">8. Family Member Details</h3>
                    <p class="text-xs text-slate-500">Family details for ESIC medical benefit and group insurance</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 8 of 10</span>
            </div>

            @for ($i = 1; $i <= 3; $i++)
                @php $f = $fams[$i] ?? []; @endphp
                <div class="p-6 rounded-2xl bg-slate-50/70 border border-slate-200/70 space-y-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-brand-600 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center font-bold text-xs">{{ $i }}</span>
                        Family Member {{ $i }}
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Name</label>
                            <input type="text" name="family_members[{{ $i }}][name]" value="{{ old("family_members.{$i}.name", $f['name'] ?? '') }}" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Relation</label>
                            <select name="family_members[{{ $i }}][relation]" 
                                    class="w-full px-3 py-2 text-xs font-medium rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                                <option value="">Select Relation</option>
                                @foreach ($familyRelations as $rel)
                                    <option value="{{ $rel }}" {{ old("family_members.{$i}.relation", $f['relation'] ?? '') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Date of Birth</label>
                            <input type="date" name="family_members[{{ $i }}][dob]" value="{{ old("family_members.{$i}.dob", $f['dob'] ?? '') }}" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Aadhaar-Linked Mobile</label>
                            <input type="text" name="family_members[{{ $i }}][aadhaar_mobile]" value="{{ old("family_members.{$i}.aadhaar_mobile", $f['aadhaar_mobile'] ?? '') }}" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Aadhaar Number</label>
                            <input type="text" name="family_members[{{ $i }}][aadhaar]" value="{{ old("family_members.{$i}.aadhaar", $f['aadhaar'] ?? '') }}" 
                                   class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">PAN Number</label>
                            <input type="text" name="family_members[{{ $i }}][pan]" value="{{ old("family_members.{$i}.pan", $f['pan'] ?? '') }}" 
                                   class="w-full px-3 py-2 text-xs font-mono uppercase rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Occupation</label>
                            <select name="family_members[{{ $i }}][occupation]" 
                                    class="w-full px-3 py-2 text-xs font-medium rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                                <option value="">Select Occupation</option>
                                @foreach ($occupations as $occ)
                                    <option value="{{ $occ }}" {{ old("family_members.{$i}.occupation", $f['occupation'] ?? '') === $occ ? 'selected' : '' }}>{{ $occ }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Details Cross-Checked By</label>
                            <x-searchable-select 
                                name="family_members[{{ $i }}][checked_by]" 
                                :value="old("family_members.{$i}.checked_by", $f['checked_by'] ?? Auth::user()->name)" 
                                :options="$verifiers" 
                                placeholder="Select staff..." 
                                :allowCustom="true" 
                            />
                        </div>
                    </div>
                </div>
            @endfor

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'nominee'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'docs'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        <span>Skip to Documents</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                    <button type="button" @click="submitWithNextTab('docs')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        <span>Save & Next: Documents Checklist</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab 9: DOCUMENT SUBMISSION CHECKLIST -->
        @php $docs = $employee->document_checklist ?? []; @endphp
        <div x-show="activeTab === 'docs'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">9. Document Submission Checklist</h3>
                    <p class="text-xs text-slate-500">Track mandatory physical/digital verification documents</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 9 of 10</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                    $checklists = [
                        'aadhaar_card' => 'Aadhaar Card (Employee + Nominee + Family Member)',
                        'pan_card' => 'PAN Card (Employee + Nominee + Family Member)',
                        'bank_proof' => 'Bank Proof / Cancelled Cheque (Employee + Nominee + Family Member)',
                        'passport_photo' => 'Passport Size Photograph (Employee + Nominee + Family Member)',
                        'address_proof' => 'Address Proof (Electricity / Rent Agreement / Voter ID)',
                        'pf_uan' => 'PF / UAN Details',
                        'esic_details' => 'ESIC Details',
                        'family_photo' => 'Family Photo For ESIC',
                    ];
                @endphp

                @foreach ($checklists as $key => $label)
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl border border-slate-200/80 hover:border-brand-300 hover:bg-brand-50/20 transition cursor-pointer">
                        <input type="checkbox" name="document_checklist[{{ $key }}]" value="Received" 
                               {{ old("document_checklist.{$key}", isset($docs[$key])) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 h-5 w-5 mt-0.5">
                        <span class="text-xs font-medium text-slate-700 leading-relaxed">{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            <div class="pt-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Other Documents / Notes</label>
                <input type="text" name="document_checklist[other]" value="{{ old('document_checklist.other', $docs['other'] ?? '') }}" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'family'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'assets'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        <span>Skip to Assets</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                    <button type="button" @click="submitWithNextTab('assets')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        <span>Save & Next: Asset Details</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab 10: ASSET / DEVICE DETAILS & SUBMIT -->
        @php $ast = $employee->asset_details ?? []; @endphp
        <div x-show="activeTab === 'assets'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">10. Asset / Device Details</h3>
                    <p class="text-xs text-slate-500">Hardware and inventory allocation records</p>
                </div>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Final Step</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Laptop / PC Model</label>
                    <x-searchable-select 
                        name="asset_details[laptop_pc]" 
                        :value="old('asset_details.laptop_pc', $ast['laptop_pc'] ?? '')" 
                        :options="$laptopModels" 
                        placeholder="Search or enter laptop / PC model..." 
                        :allowCustom="true" 
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Asset ID</label>
                    <input type="text" name="asset_details[asset_id]" value="{{ old('asset_details.asset_id', $ast['asset_id'] ?? '') }}" placeholder="e.g. AST-IT-0089" 
                           class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Serial Number</label>
                    <input type="text" name="asset_details[serial_number]" value="{{ old('asset_details.serial_number', $ast['serial_number'] ?? '') }}" placeholder="e.g. SN-8921839210" 
                           class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mobile / SIM Allocated</label>
                    <x-searchable-select 
                        name="asset_details[mobile_sim]" 
                        :value="old('asset_details.mobile_sim', $ast['mobile_sim'] ?? '')" 
                        :options="$simOptions" 
                        placeholder="Select or enter SIM / mobile device..." 
                        :allowCustom="true" 
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Office Phone Number</label>
                    <input type="text" name="asset_details[office_phone]" value="{{ old('asset_details.office_phone', $ast['office_phone'] ?? '') }}" placeholder="Phone / CUG Number" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Other Assets</label>
                    <input type="text" name="asset_details[other_asset]" value="{{ old('asset_details.other_asset', $ast['other_asset'] ?? '') }}" placeholder="Headphones, Monitor, ID Card" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Asset Handover Date</label>
                    <input type="date" name="asset_details[handover_date]" value="{{ old('asset_details.handover_date', $ast['handover_date'] ?? '') }}" 
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Asset Handover By</label>
                    <x-searchable-select 
                        name="asset_details[handover_by]" 
                        :value="old('asset_details.handover_by', $ast['handover_by'] ?? Auth::user()->name)" 
                        :options="$verifiers" 
                        placeholder="Select staff or IT admin..." 
                        :allowCustom="true" 
                    />
                </div>
            </div>

            <!-- Final Submission Confirmation Box -->
            <div class="p-5 rounded-2xl bg-brand-50 border border-brand-100 flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
                <div>
                    <div class="text-xs font-bold text-brand-800">Update Employee Master Data</div>
                    <div class="text-xs text-brand-600 mt-0.5">Changes across all 10 sections will be saved to the database.</div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" @click="activeTab = 'docs'" class="px-5 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold transition border border-slate-200">Back</button>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white text-xs font-bold tracking-wide shadow-lg shadow-amber-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Update Master Record</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-hrms-layout>
