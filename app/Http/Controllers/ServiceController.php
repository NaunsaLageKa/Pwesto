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
        
        // Load the floor plan for the current hub owner
        // In a real application, you might want to get the floor plan based on the user's location or preference
        // For now, we'll get the first active floor plan from any hub owner
        $floorPlan = FloorPlan::where('is_active', true)->first();
        
        // Debug: Log what we found
        \Log::info('ServiceController selectSeat - Service Type: ' . $serviceType);
        \Log::info('ServiceController selectSeat - Floor Plan Found: ' . ($floorPlan ? 'Yes' : 'No'));
        if ($floorPlan) {
            \Log::info('ServiceController selectSeat - Floor Plan Data: ' . json_encode($floorPlan->layout_data));
        }
        
        // If no floor plan exists, we'll create a default one
        if (!$floorPlan) {
            // You could create a default floor plan here or redirect to an error page
            // For now, we'll pass null and let the view handle it
            \Log::info('ServiceController selectSeat - No floor plan found, will use default');
        }
        
        return view('services.select-seat', compact('serviceType', 'floorPlan'));
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
} 