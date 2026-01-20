<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\LanguageClassAssignment;
use App\Models\User;
use App\Models\LanguageClass;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLanguageClassAssignmentRequest;
use App\Http\Resources\LanguageClassAssignmentResource;
use Illuminate\Validation\Rule;

class LanguageClassAssignmentController extends Controller
{
    /**
     * List all assignments (paginated)
     * Admin only
     */
    public function index(Request $request) : \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $this->authorizeAdmin();

        $perPage = (int)$request->query('per_page', '10');

        $assignments = LanguageClassAssignment::with(['student', 'languageClass.professor'])
            ->paginate($perPage);

        return LanguageClassAssignmentResource::collection($assignments);
    }

    /**
     * Show single assignment
     */
    public function show(int $id) : LanguageClassAssignmentResource
    {
        $assignment = LanguageClassAssignment::with(['student', 'languageClass.professor'])
            ->findOrFail($id);

        $this->authorizeAdminOrProfessor($assignment);

        return new LanguageClassAssignmentResource($assignment);
    }

    /**
     * Update assignment status
     * Admin or professor of the class
     */
    public function update(Request $request, int $id) : LanguageClassAssignmentResource
    {
        $assignment = LanguageClassAssignment::with('languageClass')->findOrFail($id);

        $this->authorizeAdminOrProfessor($assignment);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['assigned', 'passed', 'failed'])],
        ]);

        $assignment->update($validated);

        return new LanguageClassAssignmentResource($assignment);
    }

    /**
     * Update an existing assignment (status)
     * Admin only
     */
    public function store(StoreLanguageClassAssignmentRequest $request) : \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();

        $assignment = LanguageClassAssignment::create($validated);

        return (new LanguageClassAssignmentResource($assignment->load(['languageClass', 'student'])))
            ->response()
            ->setStatusCode(201); // HTTP 201 Created
    }

    /**
     * Delete an assignment (optional)
     * Admin only
     */
    public function destroy(int $id) : \Illuminate\Http\JsonResponse
    {
        $assignment = LanguageClassAssignment::findOrFail($id);

        $assignment->delete();

        return response()->json([
            'message' => 'Language class assignment deleted'
        ], 200);
    }

    /**
     * Helper: authorize admin only
     */
    private function authorizeAdmin() : void
    {
        $user = auth()->user();
        if ($user === null || $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Helper: authorize admin or professor of the class
     */
    private function authorizeAdminOrProfessor(LanguageClassAssignment $assignment) : void
    {
        $user = auth()->user();
        if ($user === null) {
            abort(403, 'Unauthorized');
        }

        if ($user->role === 'admin') {
            return;
        }

        if ($user->role === 'professor' && $assignment->languageClass->professor_id === $user->id) {
            return;
        }

        abort(403, 'Forbidden');
    }
}
