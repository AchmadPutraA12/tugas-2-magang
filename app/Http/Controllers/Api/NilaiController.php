<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NilaiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 5);
        $nilai = Nilai::paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully.',
            'data' => $nilai->items(),
            'pagination' => [
                'current_page' => $nilai->currentPage(),
                'last_page' => $nilai->lastPage(),
                'per_page' => $nilai->perPage(),
                'total' => $nilai->total(),
            ]
        ], 200);
    }

    public function nilaiRt(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 5);
        $nilaiRT = Nilai::where('materi_uji_id', 7)
            ->where('nama_pelajaran', '!=', 'pelajaran_khusus')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Data nilai RT retrieved successfully.',
            'data' => $nilaiRT->items(),
            'pagination' => [
                'current_page' => $nilaiRT->currentPage(),
                'last_page' => $nilaiRT->lastPage(),
                'per_page' => $nilaiRT->perPage(),
                'total' => $nilaiRT->total(),
            ]
        ]);
    }

    public function nilaiSt(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 5);
        try {
            $nilaiSTQuery = Nilai::where('materi_uji_id', 4);

            $nilaiSTPaginated = $nilaiSTQuery->paginate($perPage);
            $transformedData = $nilaiSTPaginated->getCollection()->map(function ($nilai) {
                $faktor = match ($nilai->pelajaran_id) {
                    44 => 41.67,
                    45 => 29.67,
                    46 => 100,
                    47 => 23.81,
                    default => 1
                };
                return [
                    'nama' => $nilai->nama,
                    'nisn' => $nilai->nisn,
                    'nilaiST' => $nilai->skor * $faktor
                ];
            });

            $nilaiSTPaginated->setCollection($transformedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Data nilai ST retrieved successfully.',
                'data' => $nilaiSTPaginated->items(),
                'pagination' => [
                    'current_page' => $nilaiSTPaginated->currentPage(),
                    'last_page' => $nilaiSTPaginated->lastPage(),
                    'per_page' => $nilaiSTPaginated->perPage(),
                    'total' => $nilaiSTPaginated->total(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching nilai ST data:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve nilai ST data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
