<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalCaseRequest;
use App\Http\Requests\UpdateMedicalCaseRequest;
use App\Models\MedicalCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CaseController extends Controller
{
    /**
     * Display a paginated list of medical cases with search and status filtering.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', MedicalCase::class);

        $query = MedicalCase::with('creator');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('surgeon_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->input('status'), MedicalCase::STATUSES, true)) {
            $query->where('status', $request->input('status'));
        }

        $cases = $query->latest()->paginate(10)->withQueryString();
        $statuses = MedicalCase::STATUSES;
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        return view('cases.index', compact('cases', 'statuses', 'search', 'status'));
    }

    /**
     * Show the form for creating a new medical case.
     */
    public function create(): View
    {
        Gate::authorize('create', MedicalCase::class);

        return view('cases.form', [
            'case' => new MedicalCase(),
            'isEdit' => false,
            'statuses' => MedicalCase::STATUSES,
            'priorities' => MedicalCase::PRIORITIES,
        ]);
    }

    /**
     * Store a newly created medical case in storage.
     */
    public function store(StoreMedicalCaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        // Server-side assignment: Creator is always the authenticated user
        $validated['created_by'] = $request->user()->id;

        $case = MedicalCase::create($validated);

        return redirect()
            ->route('cases.index')
            ->with('success', "Case {$case->case_number} has been created successfully.");
    }

    /**
     * Display the specified medical case.
     */
    public function show(MedicalCase $case): View
    {
        Gate::authorize('view', $case);

        $case->load('creator');

        return view('cases.show', compact('case'));
    }

    /**
     * Show the form for editing the specified medical case.
     */
    public function edit(MedicalCase $case): View
    {
        Gate::authorize('update', $case);

        return view('cases.form', [
            'case' => $case,
            'isEdit' => true,
            'statuses' => MedicalCase::STATUSES,
            'priorities' => MedicalCase::PRIORITIES,
        ]);
    }

    /**
     * Update the specified medical case in storage.
     */
    public function update(UpdateMedicalCaseRequest $request, MedicalCase $case): RedirectResponse
    {
        $validated = $request->validated();

        $case->update($validated);

        return redirect()
            ->route('cases.show', $case)
            ->with('success', "Case {$case->case_number} has been updated successfully.");
    }

    /**
     * Remove the specified medical case from storage.
     */
    public function destroy(MedicalCase $case): RedirectResponse
    {
        Gate::authorize('delete', $case);

        $caseNumber = $case->case_number;
        $case->delete();

        return redirect()
            ->route('cases.index')
            ->with('success', "Case {$caseNumber} has been deleted successfully.");
    }
}
