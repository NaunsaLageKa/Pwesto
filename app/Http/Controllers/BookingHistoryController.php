<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Notifications\BookingStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $recentPaidBookingId = $request->query('payment') === 'success'
            ? (int) $request->query('booking')
            : null;

        $paymentSuccessBooking = null;

        if ($recentPaidBookingId) {
            $paidBooking = Booking::where('id', $recentPaidBookingId)
                ->where('user_id', $user->id)
                ->first();

            if ($paidBooking) {
                $paymentSuccessBooking = $paidBooking;
                $alreadyNotified = $user->notifications()
                    ->where('type', BookingStatusNotification::class)
                    ->latest()
                    ->limit(50)
                    ->get()
                    ->contains(function ($notification) use ($recentPaidBookingId) {
                        $data = $notification->data;
                        return is_array($data)
                            && ($data['status'] ?? null) === 'paid'
                            && (int) ($data['booking_id'] ?? 0) === $recentPaidBookingId;
                    });

                if (!$alreadyNotified) {
                    $user->notify(new BookingStatusNotification($paidBooking, 'paid'));
                }
            }
        }
        
        // Get upcoming and pending bookings with pagination
        $upcomingBookings = Booking::where('user_id', $user->id)
            ->where(function ($query) use ($recentPaidBookingId) {
                $query->where(function ($inner) {
                    $inner->whereIn('status', ['confirmed', 'pending'])
                        ->where('booking_date', '>=', now()->toDateString());
                });

                // Ensure the just-paid booking is visible immediately after return from PayMongo.
                if ($recentPaidBookingId) {
                    $query->orWhere('id', $recentPaidBookingId);
                }
            })
            ->orderByRaw($recentPaidBookingId ? "CASE WHEN id = {$recentPaidBookingId} THEN 0 ELSE 1 END" : '1')
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->paginate(6, ['*'], 'upcoming_page');
            
        // Get completed and cancelled bookings with pagination
        $pastBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled', 'rejected'])
            ->orderBy('booking_date', 'desc')
            ->paginate(6, ['*'], 'past_page');
            
        return view('booking-history', compact('upcomingBookings', 'pastBookings', 'paymentSuccessBooking'));
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
