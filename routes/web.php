<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\HubOwnerController;
use App\Http\Controllers\Admin\UserController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard-copy', function () {
    return view('dashboard_copy');
})->name('dashboard.copy');

Route::get('/services', [App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
Route::get('/services/booking', [App\Http\Controllers\ServiceController::class, 'booking'])->name('services.booking');
Route::get('/services/select-seat', [App\Http\Controllers\ServiceController::class, 'selectSeat'])->name('services.select-seat');
Route::post('/services/create-booking', [App\Http\Controllers\ServiceController::class, 'createBooking'])->name('services.create-booking');
Route::get('/booking-history', [App\Http\Controllers\BookingHistoryController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('booking-history');

Route::post('/booking-history/{id}/cancel', [App\Http\Controllers\BookingHistoryController::class, 'cancel'])
    ->middleware(['auth', 'verified'])
    ->name('booking-history.cancel');

Route::post('/booking-history/{id}/rebook', [App\Http\Controllers\BookingHistoryController::class, 'rebook'])
    ->middleware(['auth', 'verified'])
    ->name('booking-history.rebook');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Hub Owner Routes
Route::middleware(['auth', 'hub.owner'])->prefix('hub-owner')->name('hub-owner.')->group(function () {
    Route::get('/booking-approvals', [App\Http\Controllers\HubOwner\BookingApprovalController::class, 'index'])->name('booking-approvals');
    Route::post('/booking-approvals/{id}/approve', [App\Http\Controllers\HubOwner\BookingApprovalController::class, 'approve'])->name('booking-approvals.approve');
    Route::post('/booking-approvals/{id}/reject', [App\Http\Controllers\HubOwner\BookingApprovalController::class, 'reject'])->name('booking-approvals.reject');
    Route::post('/booking-approvals/{id}/complete', [App\Http\Controllers\HubOwner\BookingApprovalController::class, 'complete'])->name('booking-approvals.complete');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [UserController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
        Route::post('/admin/users/{id}/role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
        Route::post('/admin/users/{id}/ban', [UserController::class, 'toggleBan'])->name('admin.users.toggleBan');
        Route::post('/admin/users/{id}/approve', [UserController::class, 'approve'])->name('admin.users.approve');
        Route::post('/admin/users/{id}/reject', [UserController::class, 'reject'])->name('admin.users.reject');
        
        // Review Management
        Route::get('/admin/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('admin.reviews.index');
        Route::post('/admin/reviews/{id}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('admin.reviews.approve');
        Route::post('/admin/reviews/{id}/reject', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('admin.reviews.reject');
        Route::delete('/admin/reviews/{id}', [App\Http\Controllers\Admin\ReviewController::class, 'delete'])->name('admin.reviews.delete');
        
        // Reports and Analytics
        Route::get('/admin/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/admin/reports/users/export', [App\Http\Controllers\Admin\ReportController::class, 'exportUsers'])->name('admin.reports.export-users');
        Route::get('/admin/reports/bookings/export', [App\Http\Controllers\Admin\ReportController::class, 'exportBookings'])->name('admin.reports.export-bookings');
        
        // Dispute Resolution
        Route::get('/admin/disputes', [App\Http\Controllers\Admin\DisputeController::class, 'index'])->name('admin.disputes.index');
        Route::get('/admin/disputes/{id}', [App\Http\Controllers\Admin\DisputeController::class, 'show'])->name('admin.disputes.show');
        Route::post('/admin/disputes/{id}/resolve', [App\Http\Controllers\Admin\DisputeController::class, 'resolve'])->name('admin.disputes.resolve');
        Route::post('/admin/disputes/{id}/escalate', [App\Http\Controllers\Admin\DisputeController::class, 'escalate'])->name('admin.disputes.escalate');
        Route::post('/admin/disputes', [App\Http\Controllers\Admin\DisputeController::class, 'create'])->name('admin.disputes.create');
    });
    // Hub Owner Dashboard
    Route::get('/hub-owner/dashboard', function () {
        $totalBookings = \App\Models\Booking::where('hub_owner_id', auth()->id())->count();
        $activeUsers = \App\Models\Booking::where('hub_owner_id', auth()->id())
            ->where('status', 'confirmed')
            ->distinct('user_id')
            ->count();
        $recentBookings = \App\Models\Booking::where('hub_owner_id', auth()->id())
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        
        return view('hub-owner.dashboard', compact('totalBookings', 'activeUsers', 'recentBookings'));
    })->name('hub-owner.dashboard');

    // Hub Owner Booking Management
    Route::get('/hub-owner/bookings', [App\Http\Controllers\HubOwner\BookingController::class, 'index'])->name('hub-owner.bookings.index');
    Route::get('/hub-owner/bookings/{booking}', [App\Http\Controllers\HubOwner\BookingController::class, 'show'])->name('hub-owner.bookings.show');
    Route::post('/hub-owner/bookings/{booking}/status', [App\Http\Controllers\HubOwner\BookingController::class, 'updateStatus'])->name('hub-owner.bookings.update-status');
    Route::delete('/hub-owner/bookings/{booking}', [App\Http\Controllers\HubOwner\BookingController::class, 'destroy'])->name('hub-owner.bookings.destroy');
    
    // Floor Plan Routes
    Route::get('/hub-owner/floor-plan', [App\Http\Controllers\HubOwner\FloorPlanController::class, 'index'])->name('hub-owner.floor-plan');
    Route::post('/hub-owner/floor-plan/save', [App\Http\Controllers\HubOwner\FloorPlanController::class, 'save'])->name('hub-owner.floor-plan.save');
    Route::get('/hub-owner/floor-plan/load', [App\Http\Controllers\HubOwner\FloorPlanController::class, 'load'])->name('hub-owner.floor-plan.load');
    Route::get('/hub-owner/floor-plan/export', [App\Http\Controllers\HubOwner\FloorPlanController::class, 'export'])->name('hub-owner.floor-plan.export');
    
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users/{id}/role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::post('/admin/users/{id}/ban', [UserController::class, 'toggleBan'])->name('admin.users.toggleBan');
    Route::post('/admin/users/{id}/approve', [UserController::class, 'approve'])->name('admin.users.approve');
    Route::post('/admin/users/{id}/reject', [UserController::class, 'reject'])->name('admin.users.reject');
});

require __DIR__.'/auth.php';
