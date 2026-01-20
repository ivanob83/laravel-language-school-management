<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{

    public function index(Request $request) : \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        // Default 10 per page, can be overridden via ?per_page=
        $perPage = (int) $request->query('per_page', 10);

        $professors = User::where('role', 'professor')
            ->withCount('taughtClasses') // optional: count classes
            ->paginate($perPage);

        // Return paginated collection
        return UserResource::collection($professors);
    }

    public function show(int $id) : UserResource
    {
        $professor = User::with(['taughtClasses.students'])
            ->where('role', 'professor')
            ->findOrFail($id);

        return new UserResource($professor);
    }
}
