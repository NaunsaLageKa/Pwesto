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

Route::get('/about', function () {
    return view('about');
})->name('about');

// Temporary debug route to check floor plans
Route::get('/debug-floor-plans', function () {
    try {
        $plans = \App\Models\FloorPlan::all();
        $result = [
            'total_plans' => $plans->count(),
            'plans' => $plans->map(function($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'active' => $plan->is_active,
                    'items_count' => count($plan->layout_data ?? []),
                    'created_at' => $plan->created_at,
                    'hub_owner_id' => $plan->hub_owner_id,
                    'sample_data' => array_slice($plan->layout_data ?? [], 0, 3)
                ];
            })
        ];
        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Database connection failed',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Debug route to check bookings
Route::get('/debug-bookings', function () {
    try {
        $bookings = \App\Models\Booking::with('user')->get();
        $result = [
            'total_bookings' => $bookings->count(),
            'bookings' => $bookings->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'hub_owner_id' => $booking->hub_owner_id,
                    'user_name' => $booking->user ? $booking->user->name : 'Unknown',
                    'seat_label' => $booking->seat_label,
                    'status' => $booking->status,
                    'booking_date' => $booking->booking_date,
                    'created_at' => $booking->created_at
                ];
            })
        ];
        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Database connection failed',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Debug route to check current user and floor plans
Route::get('/debug-user-info', function () {
    try {
        $currentUser = auth()->user();
        $floorPlans = \App\Models\FloorPlan::all();
        $users = \App\Models\User::all();
        
        $result = [
            'current_user' => [
                'id' => $currentUser ? $currentUser->id : 'Not logged in',
                'name' => $currentUser ? $currentUser->name : 'Not logged in',
                'role' => $currentUser ? $currentUser->role : 'Not logged in',
                'email' => $currentUser ? $currentUser->email : 'Not logged in'
            ],
            'floor_plans' => $floorPlans->map(function($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'hub_owner_id' => $plan->hub_owner_id,
                    'is_active' => $plan->is_active,
                    'items_count' => count($plan->layout_data ?? [])
                ];
            }),
            'users' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ];
            })
        ];
        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Database connection failed',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Fix route to update floor plan and bookings
Route::get('/fix-hub-owner', function () {
    try {
        // Get the detailed floor plan (ID 2 with 115 items)
        $detailedFloorPlan = \App\Models\FloorPlan::find(2);
        
        if (!$detailedFloorPlan) {
            return response()->json(['error' => 'Detailed floor plan not found'], 404);
        }
        
        // Get all users to find the correct hub owner
        $users = \App\Models\User::where('role', 'hub_owner')->get();
        
        if ($users->isEmpty()) {
            return response()->json(['error' => 'No hub owner users found'], 404);
        }
        
        // Use the first hub owner (or you can specify which one)
        $hubOwner = $users->first();
        
        // Update the detailed floor plan to be the active one
        $detailedFloorPlan->update([
            'is_active' => true,
            'hub_owner_id' => $hubOwner->id
        ]);
        
        // Deactivate the other floor plan
        \App\Models\FloorPlan::where('id', '!=', 2)->update(['is_active' => false]);
        
        // Update all bookings to use the correct hub owner
        \App\Models\Booking::query()->update(['hub_owner_id' => $hubOwner->id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Fixed hub owner assignment',
            'hub_owner' => [
                'id' => $hubOwner->id,
                'name' => $hubOwner->name,
                'email' => $hubOwner->email
            ],
            'updated_bookings' => \App\Models\Booking::count(),
            'active_floor_plan' => $detailedFloorPlan->name
        ], 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Fix failed',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Fix route specifically for harvey (user ID 6)
Route::get('/fix-for-harvey', function () {
    try {
        // Get harvey's user ID (6)
        $harvey = \App\Models\User::find(6);
        
        if (!$harvey || $harvey->role !== 'hub_owner') {
            return response()->json(['error' => 'Harvey not found or not a hub owner'], 404);
        }
        
        // Get the detailed floor plan (ID 2 with 115 items)
        $detailedFloorPlan = \App\Models\FloorPlan::find(2);
        
        if (!$detailedFloorPlan) {
            return response()->json(['error' => 'Detailed floor plan not found'], 404);
        }
        
        // Update the detailed floor plan to use harvey as the hub owner
        $detailedFloorPlan->update([
            'is_active' => true,
            'hub_owner_id' => $harvey->id
        ]);
        
        // Deactivate the other floor plan
        \App\Models\FloorPlan::where('id', '!=', 2)->update(['is_active' => false]);
        
        // Update all bookings to use harvey as the hub owner
        \App\Models\Booking::query()->update(['hub_owner_id' => $harvey->id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Fixed for Harvey - All bookings now assigned to Harvey',
            'hub_owner' => [
                'id' => $harvey->id,
                'name' => $harvey->name,
                'email' => $harvey->email
            ],
            'updated_bookings' => \App\Models\Booking::count(),
            'active_floor_plan' => $detailedFloorPlan->name
        ], 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Fix failed',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Setup multi-hub-owner system for Produktiv workspace
Route::get('/setup-produktiv-workspace', function () {
    try {
        // Get all hub owners
        $hubOwners = \App\Models\User::where('role', 'hub_owner')->get();
        
        if ($hubOwners->isEmpty()) {
            return response()->json(['error' => 'No hub owners found'], 404);
        }
        
        // Get the detailed floor plan (ID 2 with 115 items)
        $detailedFloorPlan = \App\Models\FloorPlan::find(2);
        
        if (!$detailedFloorPlan) {
            return response()->json(['error' => 'Detailed floor plan not found'], 404);
        }
        
        // Update the floor plan to use the first hub owner as primary
        $primaryHubOwner = $hubOwners->first();
        $detailedFloorPlan->update([
            'is_active' => true,
            'hub_owner_id' => $primaryHubOwner->id
        ]);
        
        // Deactivate the other floor plan
        \App\Models\FloorPlan::where('id', '!=', 2)->update(['is_active' => false]);
        
        // Update all bookings to use the primary hub owner
        \App\Models\Booking::query()->update(['hub_owner_id' => $primaryHubOwner->id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Produktiv workspace setup complete - All hub owners can now see the same bookings',
            'primary_hub_owner' => [
                'id' => $primaryHubOwner->id,
                'name' => $primaryHubOwner->name,
                'email' => $primaryHubOwner->email
            ],
            'all_hub_owners' => $hubOwners->map(function($owner) {
                return [
                    'id' => $owner->id,
                    'name' => $owner->name,
                    'email' => $owner->email
                ];
            }),
            'updated_bookings' => \App\Models\Booking::count(),
            'active_floor_plan' => $detailedFloorPlan->name
        ], 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Setup failed',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Database connection verification
Route::get('/verify-database', function () {
    try {
        // Test database connection
        $dbConnection = \DB::connection()->getPdo();
        
        // Get counts from all main tables
        $userCount = \App\Models\User::count();
        $bookingCount = \App\Models\Booking::count();
        $floorPlanCount = \App\Models\FloorPlan::count();
        
        // Get sample data to verify relationships
        $sampleBooking = \App\Models\Booking::with('user')->first();
        $sampleFloorPlan = \App\Models\FloorPlan::where('is_active', true)->first();
        
        $result = [
            'database_connection' => 'Connected ✅',
            'pdo_connection' => $dbConnection ? 'Active ✅' : 'Failed ❌',
            'table_counts' => [
                'users' => $userCount,
                'bookings' => $bookingCount,
                'floor_plans' => $floorPlanCount
            ],
            'relationships' => [
                'booking_user_relationship' => $sampleBooking ? 'Working ✅' : 'No bookings ❌',
                'floor_plan_active' => $sampleFloorPlan ? 'Working ✅' : 'No active floor plan ❌'
            ],
            'sample_data' => [
                'booking' => $sampleBooking ? [
                    'id' => $sampleBooking->id,
                    'user_name' => $sampleBooking->user ? $sampleBooking->user->name : 'No user',
                    'seat_label' => $sampleBooking->seat_label,
                    'status' => $sampleBooking->status
                ] : null,
                'floor_plan' => $sampleFloorPlan ? [
                    'id' => $sampleFloorPlan->id,
                    'name' => $sampleFloorPlan->name,
                    'items_count' => count($sampleFloorPlan->layout_data ?? []),
                    'hub_owner_id' => $sampleFloorPlan->hub_owner_id
                ] : null
            ]
        ];
        
        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'database_connection' => 'Failed ❌',
            'error' => $e->getMessage(),
            'details' => 'Check your .env file and database configuration'
        ], 500);
    }
});

Route::get('/dashboard-copy', function () {
    return view('dashboard_copy');
})->name('dashboard.copy');

Route::get('/services', [App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
Route::get('/services/booking', [App\Http\Controllers\ServiceController::class, 'booking'])->name('services.booking');
Route::get('/services/nest-booking', [App\Http\Controllers\ServiceController::class, 'nestBooking'])->name('services.nest-booking');
Route::get('/services/select-seat', [App\Http\Controllers\ServiceController::class, 'selectSeat'])->name('services.select-seat');
Route::post('/services/create-booking', [App\Http\Controllers\ServiceController::class, 'createBooking'])->name('services.create-booking');
Route::post('/services/check-booking-status', [App\Http\Controllers\ServiceController::class, 'checkBookingStatus'])->name('services.check-booking-status');
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
        // Get the active floor plan to determine which workspace this hub owner belongs to
        $activeFloorPlan = \App\Models\FloorPlan::where('is_active', true)->first();
        
        if (!$activeFloorPlan) {
            // If no active floor plan, show only current user's bookings
            $totalBookings = \App\Models\Booking::where('hub_owner_id', auth()->id())->count();
            $activeUsers = \App\Models\Booking::where('hub_owner_id', auth()->id())
                ->where('status', 'confirmed')
                ->distinct('user_id')
                ->count();
            $recentBookings = \App\Models\Booking::where('hub_owner_id', auth()->id())
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        } else {
            // Show all bookings for this workspace (all hub owners can see the same bookings)
            $totalBookings = \App\Models\Booking::count();
            $activeUsers = \App\Models\Booking::where('status', 'confirmed')
                ->distinct('user_id')
                ->count();
            $recentBookings = \App\Models\Booking::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        }
        
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
