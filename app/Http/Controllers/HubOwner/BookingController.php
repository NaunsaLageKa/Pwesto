<?php

namespace App\Http\Controllers\HubOwner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::where('hub_owner_id', Auth::id())->with('user');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%$search%")
                              ->orWhere('email', 'like', "%$search%");
                })->orWhere('hub_name', 'like', "%$search%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Date range filter
        if ($request->filled('date_range')) {
            $dateRange = $request->input('date_range');
            switch ($dateRange) {
                case 'today':
                    $query->whereDate('booking_date', today());
                    break;
                case 'week':
                    $query->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('booking_date', now()->month)
                          ->whereYear('booking_date', now()->year);
                    break;
            }
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('hub-owner.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated hub owner
        if ($booking->hub_owner_id !== Auth::id()) {
            abort(403);
        }

        return view('hub-owner.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        // Ensure the booking belongs to the authenticated hub owner
        if ($booking->hub_owner_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $booking->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Booking status updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated hub owner
        if ($booking->hub_owner_id !== Auth::id()) {
            abort(403);
        }

        $booking->delete();

        return redirect()->back()->with('success', 'Booking cancelled successfully.');
    }
}
