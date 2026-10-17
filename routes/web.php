<?php

use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Landlord\PropertyController;
use App\Http\Controllers\Landlord\ReportController;
use App\Http\Controllers\Landlord\ReservationController as LandlordReservationController;
use App\Http\Controllers\Landlord\RoomController;
use App\Http\Controllers\Student\DormController;
use App\Http\Controllers\Student\ReservationController as StudentReservationController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:landlord'])
    ->prefix('landlord')
    ->name('landlord.')
    ->group(function () {
        Route::get('properties', [PropertyController::class, 'index'])->name('properties.index');
        Route::get('properties/create', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('properties', [PropertyController::class, 'store'])->name('properties.store');
        Route::get('properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
        Route::delete('properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');

        Route::post('properties/{property}/rooms', [RoomController::class, 'store'])->name('rooms.store');
        Route::delete('rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

        Route::get('reservations', [LandlordReservationController::class, 'index'])->name('reservations.index');
        Route::post('reservations/{reservation}/approve', [LandlordReservationController::class, 'approve'])->name('reservations.approve');
        Route::post('reservations/{reservation}/decline', [LandlordReservationController::class, 'decline'])->name('reservations.decline');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
        Route::get('reports/excel', [ReportController::class, 'excel'])->name('reports.excel');
    });

Route::middleware(['auth', 'verified', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('dorms', [DormController::class, 'index'])->name('dorms.index');
        Route::get('dorms/{property}', [DormController::class, 'show'])->name('dorms.show');

        Route::get('reservations', [StudentReservationController::class, 'index'])->name('reservations.index');
        Route::post('rooms/{room}/reserve', [StudentReservationController::class, 'store'])->name('reservations.store');
        Route::post('reservations/{reservation}/cancel', [StudentReservationController::class, 'cancel'])->name('reservations.cancel');
    });

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('properties', [AdminPropertyController::class, 'index'])->name('properties.index');
        Route::post('properties/{property}/verify', [AdminPropertyController::class, 'toggleVerify'])->name('properties.verify');

        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });

require __DIR__.'/settings.php';