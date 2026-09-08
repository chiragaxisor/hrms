<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    /**
     * Display a listing of designations.
     */
    public function index(Request $request): View
    {
        $query = Designation::with('department')->withCount('employees');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $designations = $query->latest()->paginate(10)->withQueryString();
        $departments = Department::where('status', 'Active')->orderBy('name')->get();

        // Auto code suggestion
        $latestId = Designation::max('id') ?? 0;
        $suggestedCode = 'DES-'.str_pad($latestId + 1, 3, '0', STR_PAD_LEFT);

        return view('designations.index', compact('designations', 'departments', 'suggestedCode'));
    }

    /**
     * Store a newly created designation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:designations,code',
            'title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:Active,Inactive',
        ]);

        Designation::create($validated);

        return redirect()->route('designations.index')->with('success', 'Designation added successfully.');
    }

    /**
     * Update the specified designation.
     */
    public function update(Request $request, Designation $designation): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:designations,code,'.$designation->id,
            'title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:Active,Inactive',
        ]);

        $designation->update($validated);

        return redirect()->route('designations.index')->with('success', 'Designation updated successfully.');
    }

    /**
     * Remove the specified designation.
     */
    public function destroy(Designation $designation): RedirectResponse
    {
        if ($designation->employees()->count() > 0) {
            return redirect()->route('designations.index')->with('error', 'Cannot delete designation with active employees assigned.');
        }

        $designation->delete();

        return redirect()->route('designations.index')->with('success', 'Designation deleted successfully.');
    }
}
