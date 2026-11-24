<x-hrms-layout>
    @section('page_title', 'My Employee Data')

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
                <a href="{{ route('profile.edit') }}" class="hover:text-brand-600 transition">My Profile</a>
                <span>/</span>
                <span class="text-slate-600 font-semibold">My Employee Data</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">My Employee Data</h2>
            <p class="text-xs text-slate-500 mt-1">Update your personal, bank, address, nominee, family and document details.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('profile.edit') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Back to Profile</span>
            </a>
        </div>
    </div>

    <!-- Employee Info Card (Read-Only) -->
    <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-brand-50 to-slate-50 border border-brand-100 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-500 to-orange-400 text-white flex items-center justify-center font-black text-lg shadow-md shrink-0">
            {{ strtoupper(substr($employee->employee_name, 0, 2)) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-sm font-bold text-slate-900">{{ $employee->employee_name }}</div>
            <div class="flex flex-wrap items-center gap-2 mt-1">
                <span class="text-xs font-mono font-bold text-brand-700 bg-brand-100 px-2 py-0.5 rounded-lg">{{ $employee->employee_code }}</span>
                <span class="text-xs text-slate-500">{{ $employee->department }} &middot; {{ $employee->designation }}</span>
                <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $employee->status === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $employee->status }}</span>
            </div>
        </div>
        <div class="text-xs text-slate-400 shrink-0">
            Employee since {{ optional($employee->joining_date)->format('d M Y') ?? 'N/A' }}
        </div>
    </div>

    @if (session('success'))
        <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
            <div class="font-bold mb-1">Please fix the following errors:</div>
            <ul class="list-disc list-inside space-y-0.5 ml-2">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Multi-Section Self-Service Form -->
    <form method="POST" action="{{ route('profile.employee-update') }}" x-ref="empSelfForm"
          x-data="{
              activeTab: '{{ request('tab', 'personal') }}',
              sameAddress: false,
              copyAddress() {
                  if (this.sameAddress) {
                      $refs.perm_address.value = $refs.comm_address.value;
                      $refs.perm_city.value    = $refs.comm_city.value;
                      $refs.perm_pin.value     = $refs.comm_pin.value;
                      const commStateVal = document.querySelector('input[name="address_details[communication][state]"]')?.value || '';
                      window.dispatchEvent(new CustomEvent('sync-perm-state', { detail: commStateVal }));
                  }
              },
              submitWithNextTab(nextTab) {
                  $refs.nextTabInput.value = nextTab;
                  $refs.empSelfForm.submit();
              }
          }">
        @csrf
        @method('PATCH')
        <input type="hidden" name="next_tab" x-ref="nextTabInput" value="">

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-3xl p-2 border border-slate-100 shadow-sm mb-6 overflow-x-auto scrollbar-none">
            <div class="flex items-center gap-1.5 min-w-max">
                @php
                    $tabs = [
                        ['key'=>'job',       'label'=>'Job Details',     'badge'=>'(View)'],
                        ['key'=>'personal',  'label'=>'Personal',        'badge'=>null],
                        ['key'=>'statutory', 'label'=>'Statutory / PF',  'badge'=>null],
                        ['key'=>'bank',      'label'=>'Bank Account',    'badge'=>null],
                        ['key'=>'address',   'label'=>'Address',         'badge'=>null],
                        ['key'=>'emergency', 'label'=>'Emergency / Ref', 'badge'=>null],
                        ['key'=>'nominee',   'label'=>'Nominee',         'badge'=>null],
                        ['key'=>'family',    'label'=>'Family Members',  'badge'=>null],
                        ['key'=>'docs',      'label'=>'Documents',       'badge'=>null],
                        ['key'=>'assets',    'label'=>'Assets / Devices','badge'=>null],
                    ];
                @endphp
                @foreach ($tabs as $i => $tab)
                    <button type="button" @click="activeTab = '{{ $tab['key'] }}'"
                            :class="activeTab === '{{ $tab['key'] }}' ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-100'"
                            class="flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-semibold transition">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                              :class="activeTab === '{{ $tab['key'] }}' ? 'bg-white/20' : 'bg-slate-200 text-slate-700'">{{ $i + 1 }}</span>
                        <span>{{ $tab['label'] }}</span>
                        @if ($tab['badge'])
                            <span class="text-[10px] opacity-70">{{ $tab['badge'] }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <!-- TAB 1: JOB DETAILS (Read-Only) -->
        <div x-show="activeTab === 'job'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">1. Job &amp; Employment Details</h3>
                    <p class="text-xs text-slate-500">These fields are managed by HR/Admin and cannot be changed here.</p>
                </div>
                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">&#128274; Read Only</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ([
                    'Employee Full Name' => $employee->employee_name,
                    'Employee Code'      => $employee->employee_code,
                    'Department'         => $employee->department,
                    'Designation'        => $employee->designation,
                    'Reporting Person'   => $employee->reporting_person_name ?? '—',
                    'Work Location'      => $employee->work_location ?? '—',
                    'Branch'             => $employee->branch ?? '—',
                    'Office Number'      => $employee->office_number ?? '—',
                    'Company Email'      => $employee->company_email ?? '—',
                    'Joining Date'       => optional($employee->joining_date)->format('d M Y') ?? '—',
                    'Employment Type'    => $employee->employment_type,
                    'Status'             => $employee->status,
                ] as $label => $value)
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ $label }}</label>
                        <div class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'personal'"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                    <span>Next: Personal Details</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        <!-- TAB 2: PERSONAL DETAILS -->
        <div x-show="activeTab === 'personal'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">2. Personal Details</h3>
                    <p class="text-xs text-slate-500">Identity, contacts &amp; government identifiers</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 2 of 10</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($employee->date_of_birth)->format('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Gender</label>
                    <select name="gender" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        <option value="">Select Gender</option>
                        @foreach (['Male', 'Female', 'Other'] as $g)
                            <option value="{{ $g }}" {{ old('gender', $employee->gender) === $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Marital Status</label>
                    <select name="marital_status" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        @foreach (['Single', 'Married', 'Other'] as $ms)
                            <option value="{{ $ms }}" {{ old('marital_status', $employee->marital_status) === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Anniversary Date</label>
                    <input type="date" name="anniversary_date" value="{{ old('anniversary_date', optional($employee->anniversary_date)->format('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Personal Mobile Number</label>
                    <input type="text" name="personal_mobile" value="{{ old('personal_mobile', $employee->personal_mobile) }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Personal Email ID</label>
                    <input type="email" name="personal_email" value="{{ old('personal_email', $employee->personal_email) }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Aadhaar-Linked Mobile</label>
                    <input type="text" name="aadhaar_linked_mobile" value="{{ old('aadhaar_linked_mobile', $employee->aadhaar_linked_mobile) }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
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
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">PAN Number</label>
                    <input type="text" name="pan_number" value="{{ old('pan_number', $employee->pan_number) }}"
                           class="w-full px-3.5 py-2.5 text-xs font-mono uppercase rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Aadhaar Number</label>
                    <input type="text" name="aadhaar_number" value="{{ old('aadhaar_number', $employee->aadhaar_number) }}"
                           class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'job'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'statutory'" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Skip &rarr;</button>
                    <button type="button" @click="submitWithNextTab('statutory')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Save &amp; Next: Statutory</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 3: STATUTORY / PF -->
        @php $stat = $employee->statutory_details ?? []; @endphp
        <div x-show="activeTab === 'statutory'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">3. Statutory / Government Details</h3>
                    <p class="text-xs text-slate-500">PF, UAN, ESIC and statutory records</p>
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
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Docs Received</label>
                    <select name="statutory_details[docs_received]" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        @foreach (['Yes', 'No', 'N/A'] as $dr)
                            <option value="{{ $dr }}" {{ old('statutory_details.docs_received', $stat['docs_received'] ?? '') === $dr ? 'selected' : '' }}>{{ $dr }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Previous PF / UAN Details</label>
                    <input type="text" name="statutory_details[prev_pf_uan]" value="{{ old('statutory_details.prev_pf_uan', $stat['prev_pf_uan'] ?? '') }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Previous ESIC Details</label>
                    <input type="text" name="statutory_details[prev_esic]" value="{{ old('statutory_details.prev_esic', $stat['prev_esic'] ?? '') }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'personal'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'bank'" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Skip &rarr;</button>
                    <button type="button" @click="submitWithNextTab('bank')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Save &amp; Next: Bank</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 4: BANK DETAILS -->
        @php $bank = $employee->bank_details ?? []; @endphp
        <div x-show="activeTab === 'bank'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">4. Bank / Salary Account Details</h3>
                    <p class="text-xs text-slate-500">Bank credentials for salary transfer</p>
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
                @foreach ([
                    'account_holder'  => ['Account Holder Name', 'text',  false],
                    'account_number'  => ['Account Number',      'text',  true],
                    'ifsc_code'       => ['IFSC Code',           'text',  true],
                    'branch_name'     => ['Branch Name',         'text',  false],
                ] as $key => [$label, $type, $mono])
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ $label }}</label>
                        <input type="{{ $type }}" name="bank_details[{{ $key }}]" value="{{ old('bank_details.'.$key, $bank[$key] ?? '') }}"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 {{ $mono ? 'font-mono' : '' }}">
                    </div>
                @endforeach
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Account Type</label>
                    <select name="bank_details[account_type]" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        @foreach (['Savings', 'Current', 'Salary', 'Other'] as $at)
                            <option value="{{ $at }}" {{ old('bank_details.account_type', $bank['account_type'] ?? '') === $at ? 'selected' : '' }}>{{ $at }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'statutory'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'address'" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Skip &rarr;</button>
                    <button type="button" @click="submitWithNextTab('address')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Save &amp; Next: Address</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 5: ADDRESS DETAILS -->
        @php $addr = $employee->address_details ?? []; @endphp
        <div x-show="activeTab === 'address'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">5. Address Details</h3>
                    <p class="text-xs text-slate-500">Communication &amp; Permanent residential addresses</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 5 of 10</span>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-slate-50/70 p-6 rounded-2xl border border-slate-200/70 space-y-4">
                    <h4 class="text-sm font-bold text-slate-800">Communication / Mailing Address</h4>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Full Address</label>
                        <textarea x-ref="comm_address" name="address_details[communication][address]" rows="3"
                                  class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">{{ old('address_details.communication.address', $addr['communication']['address'] ?? '') }}</textarea>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">City</label>
                            <input x-ref="comm_city" type="text" name="address_details[communication][city]" value="{{ old('address_details.communication.city', $addr['communication']['city'] ?? '') }}" list="indian_cities_list_self" placeholder="City"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
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
                            <input x-ref="comm_pin" type="text" name="address_details[communication][pincode]" value="{{ old('address_details.communication.pincode', $addr['communication']['pincode'] ?? '') }}"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50/70 p-6 rounded-2xl border border-slate-200/70 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold text-slate-800">Permanent Address</h4>
                        <label class="inline-flex items-center gap-2 text-xs font-medium text-brand-600 cursor-pointer">
                            <input type="checkbox" x-model="sameAddress" @change="copyAddress" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 h-4 w-4">
                            <span>Same as Communication</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Full Address</label>
                        <textarea x-ref="perm_address" name="address_details[permanent][address]" rows="3"
                                  class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">{{ old('address_details.permanent.address', $addr['permanent']['address'] ?? '') }}</textarea>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">City</label>
                            <input x-ref="perm_city" type="text" name="address_details[permanent][city]" value="{{ old('address_details.permanent.city', $addr['permanent']['city'] ?? '') }}" list="indian_cities_list_self" placeholder="City"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
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
                            <input x-ref="perm_pin" type="text" name="address_details[permanent][pincode]" value="{{ old('address_details.permanent.pincode', $addr['permanent']['pincode'] ?? '') }}"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cities Datalist Autocomplete -->
            <datalist id="indian_cities_list_self">
                @foreach ($cities as $cityName)
                    <option value="{{ $cityName }}"></option>
                @endforeach
            </datalist>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'bank'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'emergency'" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Skip &rarr;</button>
                    <button type="button" @click="submitWithNextTab('emergency')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Save &amp; Next: Emergency</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 6: EMERGENCY / REFERENCE -->
        @php $emg = $employee->emergency_contact ?? []; $ref = $employee->reference_contact ?? []; @endphp
        <div x-show="activeTab === 'emergency'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">6. Emergency / Reference Contact</h3>
                    <p class="text-xs text-slate-500">Emergency contact and professional reference</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 6 of 10</span>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-slate-50/70 p-6 rounded-2xl border border-slate-200/70 space-y-4">
                    <h4 class="text-sm font-bold text-slate-800">Emergency Contact</h4>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Contact Name</label>
                        <input type="text" name="emergency_contact[name]" value="{{ old('emergency_contact.name', $emg['name'] ?? '') }}"
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Mobile Number</label>
                            <input type="text" name="emergency_contact[mobile]" value="{{ old('emergency_contact.mobile', $emg['mobile'] ?? '') }}"
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Relation</label>
                            <select name="emergency_contact[relation]" 
                                    class="w-full px-3 py-2 text-xs font-medium rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
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
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                    </div>
                </div>
                <div class="bg-slate-50/70 p-6 rounded-2xl border border-slate-200/70 space-y-4">
                    <h4 class="text-sm font-bold text-slate-800">Professional / Personal Reference</h4>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Reference Name</label>
                        <input type="text" name="reference_contact[name]" value="{{ old('reference_contact.name', $ref['name'] ?? '') }}"
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Mobile Number</label>
                            <input type="text" name="reference_contact[mobile]" value="{{ old('reference_contact.mobile', $ref['mobile'] ?? '') }}"
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Relation / Association</label>
                            <select name="reference_contact[relation]" 
                                    class="w-full px-3 py-2 text-xs font-medium rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                                <option value="">Select Association</option>
                                @foreach ($referenceRelations as $rel)
                                    <option value="{{ $rel }}" {{ old('reference_contact.relation', $ref['relation'] ?? '') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Remarks</label>
                        <input type="text" name="reference_contact[remarks]" value="{{ old('reference_contact.remarks', $ref['remarks'] ?? '') }}"
                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'address'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'nominee'" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Skip &rarr;</button>
                    <button type="button" @click="submitWithNextTab('nominee')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Save &amp; Next: Nominee</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 7: NOMINEE DETAILS -->
        @php $nom = $employee->nominee_details ?? []; @endphp
        <div x-show="activeTab === 'nominee'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">7. Nominee Details</h3>
                    <p class="text-xs text-slate-500">Statutory nominee information and nominee bank account</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 7 of 10</span>
            </div>
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
            </div>
            <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/70">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-4">Nominee Bank Account</h4>
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
                    @foreach ([
                        'account_holder' => ['Account Holder', false],
                        'account_number' => ['Account Number', true],
                        'ifsc_code'      => ['IFSC Code', true],
                        'branch_name'    => ['Branch Name', false],
                    ] as $key => [$label, $mono])
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">{{ $label }}</label>
                            <input type="text" name="nominee_details[{{ $key }}]" value="{{ old('nominee_details.'.$key, $nom[$key] ?? '') }}"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white {{ $mono ? 'font-mono' : '' }}">
                        </div>
                    @endforeach
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Account Type</label>
                        <select name="nominee_details[account_type]" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                            @foreach (['Savings', 'Current', 'Other'] as $at)
                                <option value="{{ $at }}" {{ old('nominee_details.account_type', $nom['account_type'] ?? '') === $at ? 'selected' : '' }}>{{ $at }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'emergency'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'family'" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Skip &rarr;</button>
                    <button type="button" @click="submitWithNextTab('family')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Save &amp; Next: Family</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 8: FAMILY MEMBERS -->
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
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Relation</label>
                            <select name="family_members[{{ $i }}][relation]" 
                                    class="w-full px-3 py-2 text-xs font-medium rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                                <option value="">Select Relation</option>
                                @foreach ($familyRelations as $rel)
                                    <option value="{{ $rel }}" {{ old("family_members.{$i}.relation", $f['relation'] ?? '') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Date of Birth</label>
                            <input type="date" name="family_members[{{ $i }}][dob]" value="{{ old("family_members.{$i}.dob", $f['dob'] ?? '') }}"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Aadhaar Mobile</label>
                            <input type="text" name="family_members[{{ $i }}][aadhaar_mobile]" value="{{ old("family_members.{$i}.aadhaar_mobile", $f['aadhaar_mobile'] ?? '') }}"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Aadhaar Number</label>
                            <input type="text" name="family_members[{{ $i }}][aadhaar]" value="{{ old("family_members.{$i}.aadhaar", $f['aadhaar'] ?? '') }}"
                                   class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">PAN Number</label>
                            <input type="text" name="family_members[{{ $i }}][pan]" value="{{ old("family_members.{$i}.pan", $f['pan'] ?? '') }}"
                                   class="w-full px-3 py-2 text-xs font-mono uppercase rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Occupation</label>
                            <select name="family_members[{{ $i }}][occupation]" 
                                    class="w-full px-3 py-2 text-xs font-medium rounded-xl border border-slate-300 focus:border-brand-500 bg-white">
                                <option value="">Select Occupation</option>
                                @foreach ($occupations as $occ)
                                    <option value="{{ $occ }}" {{ old("family_members.{$i}.occupation", $f['occupation'] ?? '') === $occ ? 'selected' : '' }}>{{ $occ }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endfor
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'nominee'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'docs'" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Skip &rarr;</button>
                    <button type="button" @click="submitWithNextTab('docs')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Save &amp; Next: Documents</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 9: DOCUMENT CHECKLIST -->
        @php $docs = $employee->document_checklist ?? []; @endphp
        <div x-show="activeTab === 'docs'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">9. Document Submission Checklist</h3>
                    <p class="text-xs text-slate-500">Track mandatory verification documents</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 9 of 10</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                    $checklists = [
                        'aadhaar_card'   => 'Aadhaar Card (Employee + Nominee + Family Member)',
                        'pan_card'       => 'PAN Card (Employee + Nominee + Family Member)',
                        'bank_proof'     => 'Bank Proof / Cancelled Cheque',
                        'passport_photo' => 'Passport Size Photograph',
                        'address_proof'  => 'Address Proof (Electricity / Rent / Voter ID)',
                        'pf_uan'         => 'PF / UAN Details',
                        'esic_details'   => 'ESIC Details',
                        'family_photo'   => 'Family Photo For ESIC',
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
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Other Documents / Notes</label>
                <input type="text" name="document_checklist[other]" value="{{ old('document_checklist.other', $docs['other'] ?? '') }}"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'family'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <div class="flex items-center gap-2.5">
                    <button type="button" @click="activeTab = 'assets'" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Skip &rarr;</button>
                    <button type="button" @click="submitWithNextTab('assets')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Save &amp; Next: Assets</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 10: ASSETS / DEVICES -->
        @php $assets = $employee->asset_details ?? []; @endphp
        <div x-show="activeTab === 'assets'" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">10. Asset / Device Details</h3>
                    <p class="text-xs text-slate-500">Company-issued devices and equipment assigned to you</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Step 10 of 10</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Laptop / PC Model</label>
                    <x-searchable-select 
                        name="asset_details[laptop_model]" 
                        :value="old('asset_details.laptop_model', $assets['laptop_model'] ?? '')" 
                        :options="$laptopModels" 
                        placeholder="Search or enter laptop / PC model..." 
                        :allowCustom="true" 
                    />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Laptop / PC Serial Number</label>
                    <input type="text" name="asset_details[laptop_serial]" value="{{ old('asset_details.laptop_serial', $assets['laptop_serial'] ?? '') }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mobile Phone Model</label>
                    <input type="text" name="asset_details[mobile_model]" value="{{ old('asset_details.mobile_model', $assets['mobile_model'] ?? '') }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mobile Serial Number (IMEI)</label>
                    <input type="text" name="asset_details[mobile_imei]" value="{{ old('asset_details.mobile_imei', $assets['mobile_imei'] ?? '') }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Company SIM Option / Number</label>
                    <x-searchable-select 
                        name="asset_details[sim_number]" 
                        :value="old('asset_details.sim_number', $assets['sim_number'] ?? '')" 
                        :options="$simOptions" 
                        placeholder="Select or enter SIM option..." 
                        :allowCustom="true" 
                    />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Other Assets / Accessories</label>
                    <input type="text" name="asset_details[other_assets]" value="{{ old('asset_details.other_assets', $assets['other_assets'] ?? '') }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'docs'" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">Back</button>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Save All Employee Data</span>
                </button>
            </div>
        </div>

    </form>
</x-hrms-layout>
