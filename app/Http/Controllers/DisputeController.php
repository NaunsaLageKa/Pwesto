<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Dispute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Public dispute reporting for customers and hub owners.
 *
 * - Customers can report a hub owner about one of their bookings.
 * - Hub owners can report a customer about one of their bookings.
 *
 * Admin-side dispute management lives in App\Http\Controllers\Admin\DisputeController.
 */
class DisputeController extends Controller
{
    /**
     * Customer reports an issue with the hub owner on a specific booking.
     */
    public function reportHubOwner(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'type' => 'required|in:payment,service,behavior,other',
            'description' => 'required|string|min:10|max:2000',
            'evidence' => 'nullable|string|max:2000',
        ]);

        $booking = Booking::where('id', $validated['booking_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$booking->hub_owner_id) {
            return back()->with('error', 'This booking is not linked to a hub owner. Please contact support.');
        }

        $alreadyReported = Dispute::where('booking_id', $booking->id)
            ->where('created_by', Auth::id())
            ->exists();

        if ($alreadyReported) {
            return back()->with('error', 'You have already filed a report for this booking. An admin is reviewing it.');
        }

        Dispute::create([
            'user_id' => Auth::id(),
            'hub_owner_id' => $booking->hub_owner_id,
            'booking_id' => $booking->id,
            'type' => $validated['type'],
            'description' => $validated['description'],
            'evidence' => $validated['evidence'] ?? null,
            'status' => 'open',
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Your report has been submitted. An admin will review it shortly.');
    }

    /**
     * Hub owner reports an issue with the customer on a specific booking.
     */
    public function reportUser(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'type' => 'required|in:payment,service,behavior,other',
            'description' => 'required|string|min:10|max:2000',
            'evidence' => 'nullable|string|max:2000',
        ]);

        $booking = Booking::where('id', $validated['booking_id'])
            ->where('hub_owner_id', Auth::id())
            ->firstOrFail();

        $alreadyReported = Dispute::where('booking_id', $booking->id)
            ->where('created_by', Auth::id())
            ->exists();

        if ($alreadyReported) {
            return back()->with('error', 'You have already filed a report for this booking. An admin is reviewing it.');
        }

        Dispute::create([
            'user_id' => $booking->user_id,
            'hub_owner_id' => Auth::id(),
            'booking_id' => $booking->id,
            'type' => $validated['type'],
            'description' => $validated['description'],
            'evidence' => $validated['evidence'] ?? null,
            'status' => 'open',
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Your report has been submitted. An admin will review it shortly.');
    }
}
