<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get upcoming and pending bookings with pagination
        $upcomingBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('booking_date', '>=', now()->toDateString())
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->paginate(6, ['*'], 'upcoming_page');
            
        // Get completed and cancelled bookings with pagination
        $pastBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled', 'rejected'])
            ->orderBy('booking_date', 'desc')
            ->paginate(6, ['*'], 'past_page');
            
        return view('booking-history', compact('upcomingBookings', 'pastBookings'));
    }

    public function cancel($id)
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

}
