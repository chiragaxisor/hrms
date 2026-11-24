<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Department;
use App\Models\Designation;
use App\Support\EmployeeFormData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $employee = $user->employee;

        return view('profile.edit', [
            'user' => $user,
            'employee' => $employee,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show the 10-step employee master data self-service edit form.
     * Employees can only edit their own linked employee record.
     */
    public function employeeEdit(Request $request): View|RedirectResponse
    {
        $employee = $request->user()->employee;

        if (! $employee) {
            return Redirect::route('profile.edit')
                ->with('error', 'No employee record is linked to your account. Please contact HR.');
        }

        $departments = Department::where('status', 'Active')->orderBy('name')->get();
        $designations = Designation::where('status', 'Active')->orderBy('title')->get();
        $formData = EmployeeFormData::all($employee->id);

        return view('profile.employee-edit', array_merge(
            compact('employee', 'departments', 'designations'),
            $formData
        ));
    }

    /**
     * Update the logged-in employee's own master data.
     * Restricted fields (employee_code, status, employment_type, department, designation) cannot be changed.
     */
    public function employeeUpdate(Request $request): RedirectResponse
    {
        $employee = $request->user()->employee;

        if (! $employee) {
            return Redirect::route('profile.edit')
                ->with('error', 'No employee record is linked to your account. Please contact HR.');
        }

        $validated = $request->validate([
            // Personal Details (employee can edit)
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'marital_status' => 'nullable|string|in:Single,Married,Other',
            'anniversary_date' => 'nullable|date',
            'personal_mobile' => 'nullable|string|max:20',
            'personal_email' => 'nullable|email|max:255',
            'aadhaar_linked_mobile' => 'nullable|string|max:20',
            'blood_group' => 'nullable|string|max:10',
            'pan_number' => 'nullable|string|max:20',
            'aadhaar_number' => 'nullable|string|max:20',

            // Statutory Details
            'statutory_details' => 'nullable|array',

            // Bank Details
            'bank_details' => 'nullable|array',

            // Address Details
            'address_details' => 'nullable|array',

            // Emergency & Reference Contact
            'emergency_contact' => 'nullable|array',
            'reference_contact' => 'nullable|array',

            // Nominee Details
            'nominee_details' => 'nullable|array',

            // Family Members Details
            'family_members' => 'nullable|array',

            // Document Checklist
            'document_checklist' => 'nullable|array',

            // Asset / Device Details
            'asset_details' => 'nullable|array',
        ]);

        $employee->update($validated);

        if ($request->filled('next_tab')) {
            return Redirect::route('profile.employee-edit', ['tab' => $request->input('next_tab')])
                ->with('success', 'Progress saved! Continued to the next step.');
        }

        return Redirect::route('profile.employee-edit')
            ->with('success', 'Your employee data has been updated successfully!');
    }
}
