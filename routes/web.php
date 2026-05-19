<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UserController;



Route::get('/', [App\Http\Controllers\FeedbackController::class, 'welcome'])->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/location', [App\Http\Controllers\FeedbackController::class, 'publicReviews'])
    ->name('location');


Route::get('/services', [App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
Route::get('/services/booking', [App\Http\Controllers\ServiceController::class, 'booking'])->name('services.booking');
Route::get('/services/nest-booking', [App\Http\Controllers\ServiceController::class, 'nestBooking'])->name('services.nest-booking');
Route::get('/services/mesh-booking', [App\Http\Controllers\ServiceController::class, 'meshBooking'])->name('services.mesh-booking');
Route::get('/services/select-seat', [App\Http\Controllers\ServiceController::class, 'selectSeat'])->name('services.select-seat');
Route::post('/services/create-booking', [App\Http\Controllers\ServiceController::class, 'createBooking'])->name('services.create-booking');
Route::post('/services/create-booking-payment', [App\Http\Controllers\ServiceController::class, 'createBookingPayment'])->name('services.create-booking-payment');
Route::post('/services/check-booking-status', [App\Http\Controllers\ServiceController::class, 'checkBookingStatus'])->name('services.check-booking-status');
Route::get('/booking-history', [App\Http\Controllers\BookingHistoryController::class, 'index'])
    ->middleware(['auth'])
    ->name('booking-history');

Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'read'])
        ->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'readAll'])
        ->name('notifications.read-all');
});

Route::post('/booking-history/{id}/cancel', [App\Http\Controllers\BookingHistoryController::class, 'cancel'])
    ->middleware(['auth'])
    ->name('booking-history.cancel');
Route::get('/booking-history/{booking}/invoice', [App\Http\Controllers\BookingHistoryController::class, 'invoice'])
    ->middleware(['auth'])
    ->name('booking-history.invoice');
Route::get('/booking-history/{booking}/invoice/pdf', [App\Http\Controllers\BookingHistoryController::class, 'invoicePdf'])
    ->middleware(['auth'])
    ->name('booking-history.invoice.pdf');

// Dispute Reporting (customers + hub owners)
Route::post('/disputes/report-hub-owner', [App\Http\Controllers\DisputeController::class, 'reportHubOwner'])
    ->middleware(['auth'])
    ->name('disputes.report-hub-owner');

Route::post('/disputes/report-user', [App\Http\Controllers\DisputeController::class, 'reportUser'])
    ->middleware(['auth', 'hub.owner'])
    ->name('disputes.report-user');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Feedback Routes
    Route::get('/feedback', [App\Http\Controllers\FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/feedback/create', [App\Http\Controllers\FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/feedback/get-hub-owner', [App\Http\Controllers\FeedbackController::class, 'getHubOwner'])->name('feedback.get-hub-owner');
    
    // Profile Feedback Tab Route
    Route::get('/profile/feedback', function() {
        return redirect()->route('profile.edit', ['tab' => 'feedback']);
    })->name('profile.feedback');
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
    
    // Hub Owner Feedback Routes
    Route::get('/feedback', [App\Http\Controllers\HubOwner\FeedbackController::class, 'index'])->name('feedback.index');
    Route::post('/feedback/{review}/respond', [App\Http\Controllers\HubOwner\FeedbackController::class, 'respond'])->name('feedback.respond');
    Route::post('/feedback/{review}/dismiss', [App\Http\Controllers\HubOwner\FeedbackController::class, 'dismiss'])->name('feedback.dismiss');
});

Route::middleware(['auth'])->group(function () {
    // Admin routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [UserController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
        Route::get('/admin/users/{user}/company-id', [UserController::class, 'companyIdDocument'])->name('admin.users.company-id');
        Route::post('/admin/users/{id}/role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
        Route::post('/admin/users/{id}/ban', [UserController::class, 'toggleBan'])->name('admin.users.toggleBan');
        Route::post('/admin/users/{id}/approve', [UserController::class, 'approve'])->name('admin.users.approve');
        Route::post('/admin/users/{id}/reject', [UserController::class, 'reject'])->name('admin.users.reject');
        
        // Review Management
        Route::get('/admin/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('admin.reviews.index');
        Route::post('/admin/reviews/{id}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('admin.reviews.approve');
        Route::post('/admin/reviews/{id}/reject', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('admin.reviews.reject');
        Route::post('/admin/reviews/{id}/publish-public', [App\Http\Controllers\Admin\ReviewController::class, 'publishPublic'])->name('admin.reviews.publish-public');
        Route::delete('/admin/reviews/{id}', [App\Http\Controllers\Admin\ReviewController::class, 'delete'])->name('admin.reviews.delete');
        Route::post('/admin/reviews/bulk-action', [App\Http\Controllers\Admin\ReviewController::class, 'bulkAction'])->name('admin.reviews.bulk-action');
        
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
    Route::get('/hub-owner/dashboard', [App\Http\Controllers\HubOwner\DashboardController::class, 'index'])
        ->name('hub-owner.dashboard');

    // Hub Owner Booking Management
    Route::get('/hub-owner/bookings', [App\Http\Controllers\HubOwner\BookingController::class, 'index'])->name('hub-owner.bookings.index');
    Route::get('/hub-owner/bookings/{booking}', [App\Http\Controllers\HubOwner\BookingController::class, 'show'])->name('hub-owner.bookings.show');
    Route::post('/hub-owner/bookings/{booking}/status', [App\Http\Controllers\HubOwner\BookingController::class, 'updateStatus'])->name('hub-owner.bookings.update-status');
    Route::delete('/hub-owner/bookings/{booking}', [App\Http\Controllers\HubOwner\BookingController::class, 'destroy'])->name('hub-owner.bookings.destroy');
    
    
    // User Management Routes
    Route::get('/hub-owner/users', [App\Http\Controllers\HubOwnerUserController::class, 'index'])->name('hub-owner.users.index');
    Route::get('/hub-owner/users/{user}', [App\Http\Controllers\HubOwnerUserController::class, 'show'])->name('hub-owner.users.show');
    Route::post('/hub-owner/users/{user}/status', [App\Http\Controllers\HubOwnerUserController::class, 'updateStatus'])->name('hub-owner.users.update-status');
    Route::post('/hub-owner/users/{user}/ban', [App\Http\Controllers\HubOwnerUserController::class, 'toggleBan'])->name('hub-owner.users.toggle-ban');
    Route::get('/hub-owner/users-analytics', [App\Http\Controllers\HubOwnerUserController::class, 'analytics'])->name('hub-owner.users.analytics');
    
});

require __DIR__.'/auth.php';
