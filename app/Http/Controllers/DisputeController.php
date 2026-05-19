<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Dispute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
            'evidence' => 'required|string|min:10|max:400000',
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
            'title' => Dispute::summaryTitle($validated['type'], $validated['description']),
            'user_id' => Auth::id(),
            'hub_owner_id' => $booking->hub_owner_id,
            'booking_id' => $booking->id,
            'type' => $validated['type'],
            'description' => $validated['description'],
            'evidence' => $validated['evidence'],
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
            'evidence' => 'required|string|min:10|max:400000',
        ]);

        $hubOwner = Auth::user();

        // Same access rule as hub-owner bookings: hub_owner_id match OR hub_name matches company.
        $booking = Booking::where('id', $validated['booking_id'])
            ->where(function ($q) use ($hubOwner) {
                $q->where('hub_owner_id', $hubOwner->id);
                if ($hubOwner->company) {
                    $q->orWhereRaw('LOWER(hub_name) LIKE ?', ['%' . strtolower($hubOwner->company) . '%']);
                }
            })
            ->firstOrFail();

        $alreadyReported = Dispute::where('booking_id', $booking->id)
            ->where('created_by', $hubOwner->id)
            ->exists();

        if ($alreadyReported) {
            return back()->with('error', 'You have already filed a report for this booking. An admin is reviewing it.');
        }

        Dispute::create([
            'title' => Dispute::summaryTitle($validated['type'], $validated['description']),
            'user_id' => $booking->user_id,
            'hub_owner_id' => $hubOwner->id,
            'booking_id' => $booking->id,
            'type' => $validated['type'],
            'description' => $validated['description'],
            'evidence' => $validated['evidence'],
            'status' => 'open',
            'created_by' => $hubOwner->id,
        ]);

        return back()->with('success', 'Your report has been submitted. An admin will review it shortly.');
    }
}
