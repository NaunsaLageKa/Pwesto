<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get upcoming and pending bookings
        $upcomingBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('booking_date', '>=', now()->toDateString())
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->get();
            
        // Get completed and cancelled bookings
        $pastBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled', 'rejected'])
            ->orderBy('booking_date', 'desc')
            ->limit(6)
            ->get();
            
        return view('booking-history', compact('upcomingBookings', 'pastBookings'));
    }

    public function cancel(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        if ($booking->status === 'pending') {
            $booking->update(['status' => 'cancelled']);
            return response()->json(['success' => true, 'message' => 'Booking cancelled successfully']);
        }
        
        return response()->json(['success' => false, 'message' => 'Cannot cancel this booking']);
    }

    public function rebook(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        // Redirect to services with pre-filled data
        return redirect()->route('services.booking')
            ->with('rebook_data', [
                'service_type' => $booking->service_type,
                'seat_label' => $booking->seat_label
            ]);
    }
}
