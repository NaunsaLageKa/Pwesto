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
        // Show bookings for this hub owner by ID OR by matching company name
        $currentUser = Auth::user();
        $query = Booking::where(function($q) use ($currentUser) {
            $q->where('hub_owner_id', $currentUser->id);
            // Also include bookings where hub_name matches this hub owner's company (case-insensitive)
            if ($currentUser->company) {
                $q->orWhereRaw('LOWER(hub_name) LIKE ?', ['%' . strtolower($currentUser->company) . '%']);
            }
        })->with('user');

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
        // Ensure this booking belongs to the current hub owner (by ID or company name)
        $currentUser = Auth::user();
        $hasAccess = $booking->hub_owner_id === $currentUser->id;
        
        // Also check if hub_name matches the current hub owner's company
        if (!$hasAccess && $currentUser->company) {
            $hasAccess = stripos($booking->hub_name, $currentUser->company) !== false;
        }
        
        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this booking.');
        }
        
        return view('hub-owner.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        // Ensure this booking belongs to the current hub owner (by ID or company name)
        $currentUser = Auth::user();
        $hasAccess = $booking->hub_owner_id === $currentUser->id;
        
        // Also check if hub_name matches the current hub owner's company
        if (!$hasAccess && $currentUser->company) {
            $hasAccess = stripos($booking->hub_name, $currentUser->company) !== false;
        }
        
        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this booking.');
        }
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $booking->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Booking status updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        // Ensure this booking belongs to the current hub owner (by ID or company name)
        $currentUser = Auth::user();
        $hasAccess = $booking->hub_owner_id === $currentUser->id;
        
        // Also check if hub_name matches the current hub owner's company
        if (!$hasAccess && $currentUser->company) {
            $hasAccess = stripos($booking->hub_name, $currentUser->company) !== false;
        }
        
        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this booking.');
        }
        
        $booking->delete();

        return redirect()->back()->with('success', 'Booking cancelled successfully.');
    }
}
