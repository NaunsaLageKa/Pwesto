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

    public function selectSeat(Request $request)
    {
        // Get the service type from the request (e.g., 'hot-desk', 'napping-room')
        $serviceType = $request->query('service', 'hot-desk');
        
        // Load the floor plan for any hub owner
        // Prioritize the floor plan with the most items (your detailed floor plan)
        $floorPlan = FloorPlan::where('is_active', true)
            ->get()
            ->sortByDesc(function ($plan) {
                return count($plan->layout_data ?? []);
            })
            ->first();

        // If no active floor plan with items, get the most recent one regardless of item count
        if (!$floorPlan) {
            $floorPlan = FloorPlan::orderBy('created_at', 'desc')->first();
        }
        
        // If still no floor plan, try to get any floor plan regardless of status
        if (!$floorPlan) {
            $floorPlan = FloorPlan::first();
        }
        
        // Get booking statuses for all seats to determine colors
        // Note: This will be updated dynamically via JavaScript based on selected date/time
        $bookingStatuses = [];
        // Don't set initial statuses - let JavaScript handle it based on selected date/time
        
        // Debug: Log what we found
        \Log::info('ServiceController selectSeat - Service Type: ' . $serviceType);
        \Log::info('ServiceController selectSeat - Floor Plan Found: ' . ($floorPlan ? 'Yes' : 'No'));
        if ($floorPlan) {
            \Log::info('ServiceController selectSeat - Floor Plan ID: ' . $floorPlan->id);
            \Log::info('ServiceController selectSeat - Floor Plan Name: ' . $floorPlan->name);
            \Log::info('ServiceController selectSeat - Floor Plan Active: ' . ($floorPlan->is_active ? 'Yes' : 'No'));
            \Log::info('ServiceController selectSeat - Floor Plan Items Count: ' . count($floorPlan->layout_data ?? []));
            \Log::info('ServiceController selectSeat - Floor Plan Data: ' . json_encode($floorPlan->layout_data));
        } else {
            \Log::info('ServiceController selectSeat - No floor plan found in database');
            // Let's also check total count of floor plans
            $totalPlans = FloorPlan::count();
            \Log::info('ServiceController selectSeat - Total floor plans in database: ' . $totalPlans);
            
            // Let's also check what floor plans exist
            $allPlans = FloorPlan::all();
            foreach($allPlans as $plan) {
                \Log::info('ServiceController selectSeat - Found plan: ' . $plan->name . ' (ID: ' . $plan->id . ') with ' . count($plan->layout_data ?? []) . ' items');
            }
        }
        \Log::info('ServiceController selectSeat - Booking Statuses: ' . json_encode($bookingStatuses));
        
        // If no floor plan exists, we'll create a default one
        if (!$floorPlan) {
            // You could create a default floor plan here or redirect to an error page
            // For now, we'll pass null and let the view handle it
            \Log::info('ServiceController selectSeat - No floor plan found, will use default');
        }
        
        return view('services.select-seat', compact('serviceType', 'floorPlan', 'bookingStatuses'));
    }

    public function createBooking(Request $request)
    {
        $request->validate([
            'service_type' => 'required|string',
            'seat_id' => 'required|string',
            'seat_label' => 'required|string',
            'booking_date' => 'required|date',
            'booking_time' => 'required|string',
        ]);

        try {
            // Check if the same seat is already booked for the same date and time
            $existingBooking = Booking::where('seat_id', $request->seat_id)
                ->where('booking_date', $request->booking_date)
                ->where('booking_time', $request->booking_time)
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();

            if ($existingBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'This seat is already booked for the selected date and time. Please choose a different seat or time.',
                ], 400);
            }

            // Get the floor plan to determine hub owner and hub name
            $floorPlan = FloorPlan::where('is_active', true)->first();
            
            if (!$floorPlan) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active floor plan found. Please contact the hub owner.',
                ], 400);
            }
            
            // Get hub owner information
            $hubOwner = User::find($floorPlan->hub_owner_id);
            
            if (!$hubOwner || $hubOwner->role !== 'hub_owner') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid hub owner configuration.',
                ], 400);
            }

            // Create the booking with all required fields
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'hub_owner_id' => $hubOwner->id,
                'hub_name' => $hubOwner->company_name ?: 'PWESTO Workspace',
                'service_type' => $request->service_type,
                'seat_id' => $request->seat_id,
                'seat_label' => $request->seat_label,
                'booking_date' => $request->booking_date,
                'start_time' => $request->booking_time, // Map booking_time to start_time
                'end_time' => $request->booking_time, // Use same time for end_time for now
                'booking_time' => $request->booking_time,
                'status' => 'pending',
                'amount' => 0.00, // Set default amount, can be updated later
                'notes' => 'Booking created via floor plan selection',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully! Your request is pending approval.',
                'booking_id' => $booking->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating booking: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function checkBookingStatus(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
        ]);

        $bookingDate = $request->date;
        $bookingTime = $request->time;

        // Get all chairs from the floor plan
        $floorPlan = FloorPlan::where('is_active', true)->first();
        $bookingStatuses = [];

        if ($floorPlan && $floorPlan->layout_data) {
            foreach ($floorPlan->layout_data as $item) {
                if ($item['shape'] === 'chair') {
                    // Check if this chair has a booking for the specific date and time
                    $activeBooking = Booking::where('seat_id', $item['id'])
                        ->where('booking_date', $bookingDate)
                        ->where('booking_time', $bookingTime)
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->first();
                    
                    if ($activeBooking) {
                        $bookingStatuses[$item['id']] = $activeBooking->status;
                    } else {
                        $bookingStatuses[$item['id']] = 'available';
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'bookingStatuses' => $bookingStatuses
        ]);
    }
} 