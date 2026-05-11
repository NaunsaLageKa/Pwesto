<?php

namespace App\Http\Controllers\HubOwner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class FeedbackController extends Controller
{
    
    private function maskName($name)
    {
        if (!$name || trim($name) === '') {
            return 'Anonymous';
        }
        
        $words = explode(' ', trim($name));
        $maskedParts = [];
        
        foreach ($words as $word) {
            $word = trim($word);
            if ($word === '') continue;
            
            // For each word, show first 2 characters in uppercase + ****
            if (strlen($word) <= 2) {
                $maskedParts[] = strtoupper(substr($word, 0, 1)) . '****';
            } else {
                $maskedParts[] = strtoupper(substr($word, 0, 2)) . '****';
            }
        }
        
        return implode(' ', $maskedParts);
    }
    /**
     * Display all approved feedback for this hub owner's workspace
     */
    public function index(Request $request)
    {
        $query = Review::approvedForHubOwner(Auth::id())
            ->with('user', 'booking');

        // Filter by feedback type
        if ($request->filled('feedback_type')) {
            $query->where('feedback_type', $request->input('feedback_type'));
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->input('rating'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('comment', 'like', "%$search%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%$search%");
                  });
            });
        }

        $reviews = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Review::approvedForHubOwner(Auth::id())->count(),
            'average_rating' => Review::approvedForHubOwner(Auth::id())->avg('rating'),
            'five_star' => Review::approvedForHubOwner(Auth::id())->where('rating', 5)->count(),
            'recent_count' => Review::approvedForHubOwner(Auth::id())
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];
        // Mask user information for privacy (mask names only)
        foreach ($reviews as $review) {
            if ($review->user) {
                // Mask name if available, otherwise show Anonymous
                if ($review->user->name && trim($review->user->name) !== '') {
                    $review->user->display_info = $this->maskName($review->user->name);
                } else {
                    $review->user->display_info = 'Anonymous';
                }
            }
        }

        return view('hub-owner.feedback.index', [
            'reviews' => $reviews,
            'stats' => $stats,
            'canRespond' => $this->hasResponseColumns(),
        ]);
    }

    public function respond(Request $request, Review $review)
    {
        if ((int) $review->hub_owner_id !== (int) Auth::id() || $review->status !== 'approved') {
            abort(403, 'Unauthorized review response.');
        }

        if (!$this->hasResponseColumns()) {
            return redirect()->back()->with('error', 'Response feature is not ready yet.');
        }

        $validated = $request->validate([
            'hub_owner_response' => 'required|string|max:500',
        ]);

        $review->update([
            'hub_owner_response' => trim($validated['hub_owner_response']),
            'hub_owner_responded_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Response posted successfully.');
    }

    private function hasResponseColumns(): bool
    {
        return Schema::hasColumn('reviews', 'hub_owner_response')
            && Schema::hasColumn('reviews', 'hub_owner_responded_at');
    }
}
