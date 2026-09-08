<x-hrms-layout>
    @section('page_title', 'My Profile & Account Settings')

    <!-- Flash Notifications -->
    @if (session('status') === 'profile-updated')
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="font-semibold text-xs">Profile information has been successfully updated.</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold p-1 text-sm">✕</button>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="font-semibold text-xs">Your password has been changed successfully.</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold p-1 text-sm">✕</button>
        </div>
    @endif

    <!-- Profile Header Hero Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-8 mb-6 sm:mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-brand-50/50 rounded-full blur-3xl -z-10 translate-x-1/3 -translate-y-1/3"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 sm:gap-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-gradient-to-tr from-brand-500 to-brand-600 text-white flex items-center justify-center text-xl sm:text-2xl font-black shadow-lg shadow-brand-500/25 shrink-0">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold uppercase tracking-wider {{ $user->isAdmin() ? 'bg-brand-50 text-brand-600 border border-brand-200/80' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/80' }}">
                            {{ $user->role === 'superadmin' ? 'Super Administrator' : ($user->role === 'admin' ? 'HR Administrator' : 'Staff Employee') }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 mt-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>{{ $user->email }}</span>
                    </p>
                    @if ($employee)
                        <div class="mt-2.5 flex flex-wrap items-center gap-3 text-xs text-slate-600 font-semibold">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200/60">
                                <span class="text-slate-400">Code:</span> {{ $employee->employee_code }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200/60">
                                <span class="text-slate-400">Department:</span> {{ $employee->department ?? 'General' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200/60">
                                <span class="text-slate-400">Designation:</span> {{ $employee->designation ?? 'Team Member' }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-3.5 py-2 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-xs font-bold flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Account Active</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Main Settings Form Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Profile Information & Password -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- 1. Profile Information Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-8">
                <div class="flex items-center justify-between pb-6 mb-6 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Personal Information</h3>
                            <p class="text-xs text-slate-500">Update your primary account details and official email address.</p>
                        </div>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <input id="name" 
                                   name="name" 
                                   type="text" 
                                   value="{{ old('name', $user->name) }}" 
                                   required 
                                   autocomplete="name" 
                                   class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition bg-slate-50/50 hover:bg-white" />
                            @error('name')
                                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <input id="email" 
                                   name="email" 
                                   type="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required 
                                   autocomplete="username" 
                                   class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition bg-slate-50/50 hover:bg-white" />
                            @error('email')
                                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end">
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-brand-500 hover:bg-brand-600 active:bg-brand-700 text-white text-xs font-bold shadow-lg shadow-brand-500/20 transition hover:scale-[1.01]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- 2. Update Password Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-8">
                <div class="flex items-center justify-between pb-6 mb-6 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Change Password</h3>
                            <p class="text-xs text-slate-500">Ensure your account is protected with a strong, secure passphrase.</p>
                        </div>
                    </div>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('put')

                    <!-- Current Password -->
                    <div>
                        <label for="update_password_current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            Current Password <span class="text-rose-500">*</span>
                        </label>
                        <input id="update_password_current_password" 
                               name="current_password" 
                               type="password" 
                               autocomplete="current-password" 
                               placeholder="••••••••"
                               class="w-full sm:w-2/3 px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition bg-slate-50/50 hover:bg-white" />
                        @if ($errors->updatePassword->has('current_password'))
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $errors->updatePassword->first('current_password') }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-1">
                        <!-- New Password -->
                        <div>
                            <label for="update_password_password" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                New Password <span class="text-rose-500">*</span>
                            </label>
                            <input id="update_password_password" 
                                   name="password" 
                                   type="password" 
                                   autocomplete="new-password" 
                                   placeholder="••••••••"
                                   class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition bg-slate-50/50 hover:bg-white" />
                        @if ($errors->updatePassword->has('password'))
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $errors->updatePassword->first('password') }}</p>
                        @endif
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="update_password_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                Confirm New Password <span class="text-rose-500">*</span>
                            </label>
                            <input id="update_password_password_confirmation" 
                                   name="password_confirmation" 
                                   type="password" 
                                   autocomplete="new-password" 
                                   placeholder="••••••••"
                                   class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition bg-slate-50/50 hover:bg-white" />
                        @if ($errors->updatePassword->has('password_confirmation'))
                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                        @endif
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end">
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white text-xs font-bold shadow-lg shadow-slate-900/10 transition hover:scale-[1.01]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- 3. Account Deletion (Danger Zone) -->
            <div class="bg-white rounded-3xl border border-rose-200/80 shadow-xs p-4 sm:p-8">
                <div class="flex items-center justify-between pb-6 mb-4 border-b border-rose-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-rose-950">Danger Zone: Delete Account</h3>
                            <p class="text-xs text-rose-600/80">Once deleted, all related user resources and access will be permanently revoked.</p>
                        </div>
                    </div>
                </div>

                <div x-data="{ confirmingUserDeletion: false }">
                    <p class="text-xs text-slate-500 leading-relaxed mb-5">
                        Please exercise caution. Deleting this account will immediately log you out and terminate login credentials.
                    </p>

                    <button type="button" 
                            @click="confirmingUserDeletion = true" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Delete Account</span>
                    </button>

                    <!-- Delete Confirmation Modal -->
                    <div x-show="confirmingUserDeletion" 
                         x-cloak
                         class="fixed inset-0 z-50 overflow-y-auto"
                         aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="confirmingUserDeletion = false"></div>

                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                                <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8">
                                    @csrf
                                    @method('delete')

                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-slate-900">Are you sure?</h3>
                                            <p class="text-xs text-slate-500">This action cannot be undone.</p>
                                        </div>
                                    </div>

                                    <p class="text-xs text-slate-600 mb-5 leading-relaxed">
                                        Please enter your current password to confirm permanent deletion of your account.
                                    </p>

                                    <div>
                                        <label for="delete_password" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                            Confirm Password
                                        </label>
                                        <input id="delete_password" 
                                               name="password" 
                                               type="password" 
                                               required 
                                               placeholder="Your current password" 
                                               class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500" />
                                        @if ($errors->userDeletion->has('password'))
                                            <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $errors->userDeletion->first('password') }}</p>
                                        @endif
                                    </div>

                                    <div class="mt-6 flex items-center justify-end gap-3">
                                        <button type="button" 
                                                @click="confirmingUserDeletion = false" 
                                                class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                            Cancel
                                        </button>
                                        <button type="submit" 
                                                class="px-5 py-2.5 rounded-2xl bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs font-bold shadow-lg shadow-rose-600/20 transition">
                                            Confirm Deletion
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Account Snapshot & Employee Profile Summary -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Account Snapshot Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Account Snapshot</h4>

                <div class="space-y-4 text-xs">
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">System Role</span>
                        <span class="font-bold text-slate-800 capitalize">{{ $user->role }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Account Created</span>
                        <span class="font-semibold text-slate-700">{{ $user->created_at ? $user->created_at->format('d M, Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Last Profile Update</span>
                        <span class="font-semibold text-slate-700">{{ $user->updated_at ? $user->updated_at->diffForHumans() : 'Never' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-slate-500">Two-Factor Status</span>
                        <span class="inline-flex items-center gap-1 font-bold text-slate-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                            <span>Standard Auth</span>
                        </span>
                    </div>
                </div>
            </div>

            @if ($employee)
                <!-- Linked Employee Profile Card -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                        </div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">HR Record Details</h4>
                    </div>

                    <div class="space-y-3.5 text-xs">
                        <div>
                            <span class="text-[11px] text-slate-400 block font-semibold">Employee ID</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $employee->employee_code }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div>
                                <span class="text-[11px] text-slate-400 block font-semibold">Department</span>
                                <span class="font-bold text-slate-700">{{ $employee->department ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-[11px] text-slate-400 block font-semibold">Designation</span>
                                <span class="font-bold text-slate-700">{{ $employee->designation ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div>
                                <span class="text-[11px] text-slate-400 block font-semibold">Employment</span>
                                <span class="font-bold text-slate-700">{{ $employee->employment_type ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-[11px] text-slate-400 block font-semibold">Joining Date</span>
                                <span class="font-bold text-slate-700">{{ $employee->joining_date ? $employee->joining_date->format('d M, Y') : '-' }}</span>
                            </div>
                        </div>
                        @if ($employee->phone)
                            <div class="pt-1">
                                <span class="text-[11px] text-slate-400 block font-semibold">Phone</span>
                                <span class="font-bold text-slate-700">{{ $employee->phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Quick Navigation Card -->
            <div class="bg-gradient-to-br from-brand-900 to-slate-900 rounded-3xl p-6 text-white shadow-xl shadow-slate-900/20">
                <h4 class="text-sm font-bold text-white mb-2">Need Help or Leave Request?</h4>
                <p class="text-xs text-brand-200/80 leading-relaxed mb-4">
                    You can quickly apply for leaves or log your daily activities directly through your portal.
                </p>
                <div class="space-y-2">
                    <a href="{{ route('leaves.index') }}" 
                       class="w-full flex items-center justify-between px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/10 transition">
                        <span>Apply for Leave</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                    <a href="{{ route('attendances.index') }}" 
                       class="w-full flex items-center justify-between px-4 py-2.5 rounded-2xl bg-brand-500 hover:bg-brand-500 text-white text-xs font-bold transition shadow-md shadow-brand-500/30">
                        <span>Clock Attendance</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

        </div>

    </div>
</x-hrms-layout>
