<?php

namespace App\Http\Controllers\HubOwner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Mask name for privacy (e.g., Angel Cortez -> AN*** CO****)
     */
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

        return view('hub-owner.feedback.index', compact('reviews', 'stats'));
    }
}
