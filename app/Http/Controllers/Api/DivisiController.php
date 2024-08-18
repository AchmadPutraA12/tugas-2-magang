<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DivisiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'data' => $validator->errors()
            ], 422);
        }

        $perPage = 5;
        $query = Divisi::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->get('name') . '%');
        }

        $divisions = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Divisions retrieved successfully.',
            'data' => [
                'divisions' => $divisions->items(),
            ],
            'pagination' => [
                'current_page' => $divisions->currentPage(),
                'last_page' => $divisions->lastPage(),
                'per_page' => $divisions->perPage(),
                'total' => $divisions->total(),
                'next_page_url' => $divisions->nextPageUrl(),
                'prev_page_url' => $divisions->previousPageUrl(),
            ],
        ], 200);
    }

    public function getAll(): JsonResponse
    {
        $divisions = Divisi::all();

        return response()->json([
            'status' => 'success',
            'message' => 'All divisions retrieved successfully.',
            'data' => $divisions
        ], 200);
    }
}
