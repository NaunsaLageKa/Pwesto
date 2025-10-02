<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UserController;



Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/location', function () {
    return view('location');
})->name('location');


Route::get('/services', [App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
Route::get('/services/booking', [App\Http\Controllers\ServiceController::class, 'booking'])->name('services.booking');
Route::get('/services/nest-booking', [App\Http\Controllers\ServiceController::class, 'nestBooking'])->name('services.nest-booking');
Route::get('/services/select-seat', [App\Http\Controllers\ServiceController::class, 'selectSeat'])->name('services.select-seat');
Route::post('/services/create-booking', [App\Http\Controllers\ServiceController::class, 'createBooking'])->name('services.create-booking');
Route::post('/services/check-booking-status', [App\Http\Controllers\ServiceController::class, 'checkBookingStatus'])->name('services.check-booking-status');
Route::get('/booking-history', [App\Http\Controllers\BookingHistoryController::class, 'index'])
    ->middleware(['auth'])
    ->name('booking-history');

Route::post('/booking-history/{id}/cancel', [App\Http\Controllers\BookingHistoryController::class, 'cancel'])
    ->middleware(['auth'])
    ->name('booking-history.cancel');


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
    
    // Floor Plan Routes
    Route::get('/floor-plan', [App\Http\Controllers\HubOwner\FloorPlanController::class, 'index'])->name('floor-plan');
    Route::post('/floor-plan/save', [App\Http\Controllers\HubOwner\FloorPlanController::class, 'save'])->name('floor-plan.save');
    Route::get('/floor-plan/load', [App\Http\Controllers\HubOwner\FloorPlanController::class, 'load'])->name('floor-plan.load');
});

Route::middleware(['auth'])->group(function () {
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
        // Show only bookings for this specific hub owner
        $totalBookings = \App\Models\Booking::where('hub_owner_id', auth()->id())->count();
        $activeUsers = \App\Models\Booking::where('hub_owner_id', auth()->id())
            ->where('status', 'confirmed')
            ->distinct('user_id')
            ->count();
        $recentBookings = \App\Models\Booking::where('hub_owner_id', auth()->id())
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        
        // Get current user's company name for dynamic title
        $hubOwner = auth()->user();
        
        return view('hub-owner.dashboard', compact('totalBookings', 'activeUsers', 'recentBookings', 'hubOwner'));
    })->name('hub-owner.dashboard');

    // Hub Owner Booking Management
    Route::get('/hub-owner/bookings', [App\Http\Controllers\HubOwner\BookingController::class, 'index'])->name('hub-owner.bookings.index');
    Route::get('/hub-owner/bookings/{booking}', [App\Http\Controllers\HubOwner\BookingController::class, 'show'])->name('hub-owner.bookings.show');
    Route::post('/hub-owner/bookings/{booking}/status', [App\Http\Controllers\HubOwner\BookingController::class, 'updateStatus'])->name('hub-owner.bookings.update-status');
    Route::delete('/hub-owner/bookings/{booking}', [App\Http\Controllers\HubOwner\BookingController::class, 'destroy'])->name('hub-owner.bookings.destroy');
    
    
    // User Management Routes
    Route::get('/hub-owner/users', [App\Http\Controllers\HubOwnerUserController::class, 'index'])->name('hub-owner.users.index');
    Route::get('/hub-owner/users/{user}', [App\Http\Controllers\HubOwnerUserController::class, 'show'])->name('hub-owner.users.show');
    Route::post('/hub-owner/users/{user}/status', [App\Http\Controllers\HubOwnerUserController::class, 'updateStatus'])->name('hub-owner.users.update-status');
    Route::get('/hub-owner/users-analytics', [App\Http\Controllers\HubOwnerUserController::class, 'analytics'])->name('hub-owner.users.analytics');
    
});

require __DIR__.'/auth.php';
