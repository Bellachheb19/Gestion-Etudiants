<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UIController;

Route::get('/', [UIController::class, 'index'])->name('home');
Route::get('/login', [UIController::class, 'index'])->name('login');

Route::get('/admin/dashboard', [UIController::class, 'adminDashboard'])->name('admin.dashboard');
Route::get('/student/profile', [UIController::class, 'studentProfile'])->name('student.profile');

Route::get('/{any}', [UIController::class, 'index'])->where('any', '.*');
