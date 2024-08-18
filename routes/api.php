<?php

use App\Http\Controllers\Api\DivisiController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\NilaiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [LoginController::class, 'user'])->name('user');
    Route::get('/devisi', [DivisiController::class, 'index'])->name('divisi.index');
    Route::get('/divisions/all', [DivisiController::class, 'getAll'])->name('divisions.all');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/employe', [EmployeeController::class, 'index'])->name('employe.index');
    Route::post('/employe', [EmployeeController::class, 'store'])->name('employe.store');
    Route::post('/employe/{uuid}', [EmployeeController::class, 'update'])->name('employe.update');
    Route::delete('/employe/{uuid}', [EmployeeController::class, 'destroy'])->name('employe.destroy');

    Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::get('/nilairt', [NilaiController::class, 'nilaiRt'])->name('nilai.rt');
    Route::get('/nilaist', [NilaiController::class, 'nilaiSt'])->name('nilai.st');
});
