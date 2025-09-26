<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FloorPlan;
use App\Models\User;
use App\Models\Booking;

class ServiceController extends Controller
{
    public function index()
    {
        return view('services.index');
    }
    
    public function booking()
    {
        return view('services.booking');
    }

    public function nestBooking()
    {
        return view('services.nest-booking');
    }

    public function selectSeat(Request $request)
    {
        $serviceType = $request->query('service', 'hot-desk');
        
        // Map service types to specific hub owners
        $serviceToHubOwner = [
            'hot-desk' => 'Produktiv', // Hot desk service maps to Produktiv hub owner
            'private-office' => 'Nest Workspaces', // Private office service maps to Nest Workspaces hub owner
            'meeting-room' => 'Mesh Media', // Meeting room service maps to Mesh Media hub owner
        ];
        
        $targetCompany = $serviceToHubOwner[$serviceType] ?? 'Produktiv';
        
        // Get the active floor plan for the specific hub owner based on service type
        $floorPlan = FloorPlan::whereHas('hubOwner', function($query) use ($targetCompany) {
            $query->where('company', $targetCompany);
        })
        ->where('is_active', true)
        ->whereNotNull('layout_data')
        ->first();
        
        // Load booking statuses for the floor plan items
        $bookingStatuses = [];
        if ($floorPlan && $floorPlan->layout_data) {
            $selectedDate = $request->query('date', date('Y-m-d'));
            $selectedTime = $request->query('time', '09:00');
            
            // Get all active bookings for this floor plan on the selected date (exclude cancelled)
            $bookings = \App\Models\Booking::where('hub_owner_id', $floorPlan->hub_owner_id)
                ->where('booking_date', $selectedDate)
                ->where('booking_time', $selectedTime)
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->get();
            
            // Create status mapping for each item
            foreach ($floorPlan->layout_data as $item) {
                if (isset($item['id'])) {
                    // Get the most recent booking for this seat
                    $booking = $bookings->where('seat_id', $item['id'])->sortByDesc('created_at')->first();
                    if ($booking) {
                        $bookingStatuses[$item['id']] = $booking->status;
                    } else {
                        $bookingStatuses[$item['id']] = 'available';
                    }
                }
            }
        }
        
        // Debug: Log the floor plan loading
        \Log::info('Floor plan loading for service', [
            'service_type' => $serviceType,
            'target_company' => $targetCompany,
            'floor_plan_id' => $floorPlan ? $floorPlan->id : 'null',
            'items_count' => $floorPlan ? count($floorPlan->layout_data) : 0,
            'booking_statuses_count' => count($bookingStatuses)
        ]);
        
        return view('services.select-seat', compact('serviceType', 'floorPlan', 'bookingStatuses'));
    }

    public function createBooking(Request $request)
    {
        $request->validate([
            'service_type' => 'required|string',
            'seat_id' => 'required|string',
            'seat_label' => 'required|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|string',
        ]);

        // Additional validation to prevent past date bookings
        $bookingDate = \Carbon\Carbon::parse($request->booking_date);
        $today = \Carbon\Carbon::today();
        
        if ($bookingDate->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot book for past dates. Please select today or a future date.',
            ], 400);
        }
        
        // Also check if the date is before today (not just past)
        if ($bookingDate->lt($today)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot book for dates before today. Please select today or a future date.',
            ], 400);
        }

        $existingBooking = Booking::where('seat_id', $request->seat_id)
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'This seat is already booked for the selected date and time.',
            ], 400);
        }

        // Map service types to specific hub owners
        $serviceToHubOwner = [
            'hot-desk' => 'Produktiv', // Hot desk service maps to Produktiv hub owner
            'private-office' => 'Nest Workspaces', // Private office service maps to Nest Workspaces hub owner
            'meeting-room' => 'Mesh Media', // Meeting room service maps to Mesh Media hub owner
        ];
        
        $targetCompany = $serviceToHubOwner[$request->service_type] ?? 'Produktiv';
        
        // Get the active floor plan for the specific hub owner based on service type
        $floorPlan = FloorPlan::whereHas('hubOwner', function($query) use ($targetCompany) {
            $query->where('company', $targetCompany);
        })
        ->where('is_active', true)
        ->whereNotNull('layout_data')
        ->first();
        
        if (!$floorPlan) {
            return response()->json([
                'success' => false,
                'message' => 'No floor plan found for this service.',
            ], 400);
        }
        
        $hubOwner = $floorPlan->hubOwner;
        if (!$hubOwner || $hubOwner->role !== 'hub_owner') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid hub owner configuration.',
            ], 400);
        }

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'hub_owner_id' => $hubOwner->id,
            'hub_name' => $hubOwner->company ?: 'PWESTO Workspace',
            'service_type' => $request->service_type,
            'seat_id' => $request->seat_id,
            'seat_label' => $request->seat_label,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'start_time' => $request->booking_time,  // Use booking_time as start_time
            'end_time' => $request->booking_time,     // Use booking_time as end_time
            'status' => 'pending',
            'amount' => 150,
            'notes' => 'Booking created via floor plan selection',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully!',
            'booking_id' => $booking->id,
        ]);
    }

    public function checkBookingStatus(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
            'service_type' => 'required|string',
        ]);

        // Map service types to specific hub owners
        $serviceToHubOwner = [
            'hot-desk' => 'Produktiv',
            'private-office' => 'Nest Workspaces',
            'meeting-room' => 'Mesh Media',
        ];
        
        $targetCompany = $serviceToHubOwner[$request->service_type] ?? 'Produktiv';
        
        // Get the active floor plan for the specific hub owner
        $floorPlan = FloorPlan::whereHas('hubOwner', function($query) use ($targetCompany) {
            $query->where('company', $targetCompany);
        })
        ->where('is_active', true)
        ->whereNotNull('layout_data')
        ->first();
        
        $bookingStatuses = [];

        if ($floorPlan && $floorPlan->layout_data) {
            // Get all active bookings for this floor plan on the selected date and time (exclude cancelled)
            $bookings = Booking::where('hub_owner_id', $floorPlan->hub_owner_id)
                ->where('booking_date', $request->date)
                ->where('booking_time', $request->time)
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->get();
            
            foreach ($floorPlan->layout_data as $item) {
                if (isset($item['id'])) {
                    // Get the most recent booking for this seat
                    $booking = $bookings->where('seat_id', $item['id'])->sortByDesc('created_at')->first();
                    $bookingStatuses[$item['id']] = $booking ? $booking->status : 'available';
                }
            }
        }

        return response()->json([
            'success' => true,
            'bookingStatuses' => $bookingStatuses
        ]);
    }
} 