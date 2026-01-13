<?php

namespace App\Http\Controllers;

use App\Models\LanguageClass;
use App\Models\User;
use App\Http\Resources\LanguageClassResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\StoreLanguageClassRequest;
use App\Http\Requests\UpdateLanguageClassRequest;

class LanguageClassController extends Controller
{
    /**
     * List all classes (paginated)
     * Admin only
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $perPage = $request->query('per_page', 10);

        $classes = LanguageClass::with('professor')
            ->withCount('students')
            ->paginate($perPage);

        return LanguageClassResource::collection($classes);
    }

    /**
     * Show single class with students and professor
     */
    public function show($id)
    {
        $class = LanguageClass::with(['professor', 'students'])
            ->findOrFail($id);

        return new LanguageClassResource($class);
    }

    /**
     * Create a new language class
     * Admin only
     */
    public function store(StoreLanguageClassRequest $request)
    {
        $validated = $request->validated();

        $languageClass = LanguageClass::create($validated);

        if (!empty($validated['student_ids'])) {
            $languageClass->students()->attach(
                array_fill_keys($validated['student_ids'], ['status' => 'assigned'])
            );
        }

        return new LanguageClassResource($languageClass->load(['professor', 'students']));
    }

    /**
     * Update a class
     * Admin only
     */
    public function update(UpdateLanguageClassRequest $request, $id)
    {
        $languageClass = LanguageClass::findOrFail($id);
        $validated = $request->validated();

        $languageClass->update($validated);

        if (isset($validated['student_ids'])) {
            $syncData = array_fill_keys($validated['student_ids'], ['status' => 'assigned']);
            $languageClass->students()->sync($syncData);
        }

        return new LanguageClassResource($languageClass->load(['professor', 'students']));
    }

    /**
     * Delete a class
     * Admin only
     */
    public function destroy($id)
    {
        $this->authorizeAdmin();

        $languageClass = LanguageClass::findOrFail($id);
        $languageClass->delete();

        return response()->json(['message' => 'Language class deleted'], 200);
    }

    /**
     * Confirm class completion
     * Professor only
     */
    public function confirmCompletion($id)
    {
        $languageClass = LanguageClass::with('professor')->findOrFail($id);

        $this->authorizeProfessor($languageClass);

        $languageClass->update(['status' => 'completed']);

        return new LanguageClassResource($languageClass->load('professor', 'students'));
    }

    /**
     * Helper: authorize only admin
     */
    private function authorizeAdmin()
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Helper: authorize professor of this class
     */
    private function authorizeProfessor(LanguageClass $class)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'professor' || $class->professor_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
    }
}
