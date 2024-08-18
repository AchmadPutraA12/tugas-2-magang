<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
            'division_id' => 'sometimes|uuid|exists:divisis,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'data' => $validator->errors()
            ], 422);
        }

        $perPage = 5;

        $query = Employee::with('divisi');

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->get('name') . '%');
        }

        if ($request->has('division_id')) {
            $query->where('divisi_id', $request->get('division_id'));
        }

        $employees = $query->paginate($perPage);

        $employeesData = $employees->items();
        $transformedEmployees = array_map(function ($employee) {
            return [
                'id' => $employee->id,
                'image' => $employee->image,
                'name' => $employee->name,
                'phone' => $employee->phone,
                'division' => [
                    'id' => $employee->divisi->id,
                    'name' => $employee->divisi->name,
                ],
                'position' => $employee->position,
            ];
        }, $employeesData);

        return response()->json([
            'status' => 'success',
            'message' => 'Employees retrieved successfully.',
            'data' => [
                'employees' => $transformedEmployees,
            ],
            'pagination' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
                'next_page_url' => $employees->nextPageUrl(),
                'prev_page_url' => $employees->previousPageUrl(),
            ],
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'divisi_id' => 'required|uuid|exists:divisis,id',
            'position' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'data' => $validator->errors()
            ], 422);
        }

        try {
            $imagePath = $request->hasFile('image') ? $request->file('image')->store('employees', 'public') : null;

            $employee = new Employee([
                'id' => Str::uuid(),
                'name' => $request->input('name'),
                'image' => $imagePath ? Storage::url($imagePath) : null,
                'phone' => $request->input('phone'),
                'divisi_id' => $request->input('divisi_id'),
                'position' => $request->input('position'),
            ]);

            $employee->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Employee created successfully.',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Employee creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create employee.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $uuid)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'divisi_id' => 'required|uuid|exists:divisis,id',
                'position' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'data' => $validator->errors()
                ], 422);
            }

            $employee = Employee::find($uuid);
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found',
                ], 404);
            }

            $employee->name = $request->input('name');
            $employee->phone = $request->input('phone');
            $employee->position = $request->input('position');
            $employee->divisi_id = $request->input('divisi_id');

            if ($request->hasFile('image')) {
                if ($employee->image) {
                    $oldImagePath = str_replace('/storage/', '', $employee->image);
                    Storage::disk('public')->delete($oldImagePath);
                }
                $path = $request->file('image')->store('employees', 'public');
                $employee->image = Storage::url($path);
            }

            $employee->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Employee updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Update employee failed: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($uuid): JsonResponse
    {
        try {
            $employee = Employee::find($uuid);

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found',
                ], 404);
            }

            if ($employee->image) {
                $oldImagePath = str_replace('/storage/', '', $employee->image);
                Storage::disk('public')->delete($oldImagePath);
            }

            $employee->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Employee deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Delete employee failed: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
