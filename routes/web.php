<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('auth.login');
})->name('home');

Route::prefix('auth')->name('auth.')->group(function (): void {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegistration'])->name('storeRegistration');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::match(['get', 'post'], '/books/add', [AdminController::class, 'addBook'])->name('addBook');
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('manageUsers');
    Route::get('/fines', [AdminController::class, 'monitorFines'])->name('monitorFines');
    Route::get('/requests', [AdminController::class, 'manageRequests'])->name('manageRequests');
    Route::post('/requests/approve', [AdminController::class, 'approveRequest'])->name('approveRequest');
    Route::post('/requests/reject', [AdminController::class, 'rejectRequest'])->name('rejectRequest');
    Route::get('/returns', [AdminController::class, 'manageReturns'])->name('manageReturns');
    Route::post('/returns/approve', [AdminController::class, 'approveReturn'])->name('approveReturn');
});

Route::prefix('member')->name('member.')->group(function (): void {
    Route::get('/dashboard', [MemberController::class, 'dashboard'])->name('dashboard');
    Route::get('/books', [MemberController::class, 'books'])->name('books');
    Route::post('/books/request', [MemberController::class, 'requestBook'])->name('requestBook');
    Route::get('/current', [MemberController::class, 'current'])->name('current');
    Route::get('/returns', [MemberController::class, 'returns'])->name('returns');
    Route::post('/returns/request', [MemberController::class, 'requestReturn'])->name('requestReturn');
    Route::get('/history', [MemberController::class, 'history'])->name('history');
    Route::get('/requests', [MemberController::class, 'requests'])->name('requests');
});
