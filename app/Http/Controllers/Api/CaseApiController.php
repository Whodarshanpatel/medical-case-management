<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalCaseRequest;
use App\Http\Requests\UpdateMedicalCaseRequest;
use App\Models\MedicalCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CaseApiController extends Controller
{
    /**
     * GET /api/cases
     * Retrieve paginated medical cases with optional search and status filter.
     */
    public function index(Request $request): JsonResponse
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

        $perPage = (int) $request->input('per_page', 10);
        $cases = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Cases retrieved successfully.',
            'data' => [
                'items' => $cases->items(),
                'pagination' => [
                    'current_page' => $cases->currentPage(),
                    'per_page' => $cases->perPage(),
                    'total' => $cases->total(),
                    'last_page' => $cases->lastPage(),
                ],
            ],
        ], 200);
    }

    /**
     * POST /api/cases
     * Create a new medical case (Admin only).
     */
    public function store(StoreMedicalCaseRequest $request): JsonResponse
    {
        // FormRequest handles authorize() -> checks Gate::can('create', MedicalCase::class)
        $validated = $request->validated();
        // Server-side assignment: Creator is always the authenticated user
        $validated['created_by'] = $request->user()->id;

        $case = MedicalCase::create($validated);
        $case->load('creator');

        return response()->json([
            'success' => true,
            'message' => 'Case created successfully.',
            'data' => $case,
        ], 201);
    }

    /**
     * GET /api/cases/{id}
     * Retrieve a single medical case by ID (Admin and Viewer).
     */
    public function show(Request $request, $id): JsonResponse
    {
        $case = MedicalCase::with('creator')->find($id);

        if (!$case) {
            return response()->json([
                'success' => false,
                'message' => "Medical case with ID {$id} not found.",
                'data' => null,
            ], 404);
        }

        Gate::authorize('view', $case);

        return response()->json([
            'success' => true,
            'message' => 'Case retrieved successfully.',
            'data' => $case,
        ], 200);
    }

    /**
     * PUT /api/cases/{id}
     * Update an existing medical case (Admin only).
     */
    public function update(UpdateMedicalCaseRequest $request, $id): JsonResponse
    {
        $case = MedicalCase::find($id);

        if (!$case) {
            return response()->json([
                'success' => false,
                'message' => "Medical case with ID {$id} not found.",
                'data' => null,
            ], 404);
        }

        Gate::authorize('update', $case);

        $case->update($request->validated());
        $case->load('creator');

        return response()->json([
            'success' => true,
            'message' => 'Case updated successfully.',
            'data' => $case,
        ], 200);
    }

    /**
     * DELETE /api/cases/{id}
     * Delete an existing medical case (Admin only).
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $case = MedicalCase::find($id);

        if (!$case) {
            return response()->json([
                'success' => false,
                'message' => "Medical case with ID {$id} not found.",
                'data' => null,
            ], 404);
        }

        Gate::authorize('delete', $case);

        $caseNumber = $case->case_number;
        $case->delete();

        return response()->json([
            'success' => true,
            'message' => "Case {$caseNumber} deleted successfully.",
            'data' => null,
        ], 200);
    }
}
