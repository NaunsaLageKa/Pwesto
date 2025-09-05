<?php

namespace App\Http\Controllers\HubOwner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingApprovalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all pending and confirmed bookings for this hub owner
        $pendingBookings = Booking::where('hub_owner_id', $user->id)
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->get();
            
        $confirmedBookings = Booking::where('hub_owner_id', $user->id)
            ->where('status', 'confirmed')
            ->with('user')
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->get();
            
        return view('hub-owner.booking-approvals', compact('pendingBookings', 'confirmedBookings'));
    }

    public function approve(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('hub_owner_id', Auth::id())
            ->firstOrFail();
            
        if ($booking->status === 'pending') {
            $booking->update(['status' => 'confirmed']);
            return response()->json(['success' => true, 'message' => 'Booking approved successfully']);
        }
        
        return response()->json(['success' => false, 'message' => 'Cannot approve this booking']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        $booking = Booking::where('id', $id)
            ->where('hub_owner_id', Auth::id())
            ->firstOrFail();
            
        if ($booking->status === 'pending') {
            $booking->update([
                'status' => 'rejected',
                'notes' => 'Rejected: ' . $request->rejection_reason
            ]);
            return response()->json(['success' => true, 'message' => 'Booking rejected successfully']);
        }
        
        return response()->json(['success' => false, 'message' => 'Cannot reject this booking']);
    }

    public function complete(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('hub_owner_id', Auth::id())
            ->firstOrFail();
            
        if ($booking->status === 'confirmed') {
            $booking->update(['status' => 'completed']);
            return response()->json(['success' => true, 'message' => 'Booking marked as completed']);
        }
        
        return response()->json(['success' => false, 'message' => 'Cannot complete this booking']);
    }
}
