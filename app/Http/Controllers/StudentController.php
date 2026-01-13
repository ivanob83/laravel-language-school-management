<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    public function index(Request $request)
    {
        // Default 10 per page, can be overridden via ?per_page=
        $perPage = $request->query('per_page', 10);

        $students = User::where('role', 'student')
            ->withCount('enrolledClasses') // optional: count classes
            ->paginate($perPage);

        // Return paginated collection
        return UserResource::collection($students);
    }

    public function show($id)
    {
        $student = User::with(['enrolledClasses.professor'])
            ->where('role', 'student')
            ->findOrFail($id);

        return new UserResource($student);
    }
}
