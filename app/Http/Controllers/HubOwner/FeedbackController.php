<?php

namespace App\Http\Controllers\HubOwner;

use App\Http\Controllers\Controller;
use App\Models\HubOwnerReviewDismissal;
use App\Models\Review;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class FeedbackController extends Controller
{
    use AuthorizesRequests;

    private function maskName($name)
    {
        if (! $name || trim($name) === '') {
            return 'Anonymous';
        }

        $words = explode(' ', trim($name));
        $maskedParts = [];

        foreach ($words as $word) {
            $word = trim($word);
            if ($word === '') {
                continue;
            }

            if (strlen($word) <= 2) {
                $maskedParts[] = strtoupper(substr($word, 0, 1)) . '****';
            } else {
                $maskedParts[] = strtoupper(substr($word, 0, 2)) . '****';
            }
        }

        return implode(' ', $maskedParts);
    }

    /**
     * Approved workspace reviews for this hub, excluding rows this hub owner dismissed from their list.
     */
    private function hubOwnerReviewsBaseQuery()
    {
        $q = Review::approvedForHubOwner(Auth::id())
            ->where('feedback_type', 'workspace');

        if (Schema::hasTable('hub_owner_review_dismissals')) {
            $q->whereDoesntHave('hubOwnerDismissals', function ($inner) {
                $inner->where('user_id', Auth::id());
            });
        }

        return $q;
    }

    /**
     * Display all approved feedback for this hub owner's workspace
     */
    public function index(Request $request)
    {
        $query = $this->hubOwnerReviewsBaseQuery()->with('user', 'booking');

        if ($request->filled('rating')) {
            $query->where('rating', $request->input('rating'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%$search%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%$search%");
                    });
            });
        }

        $reviews = $query->paginate(15)->withQueryString();

        $statsBase = fn () => $this->hubOwnerReviewsBaseQuery();

        $stats = [
            'total' => $statsBase()->count(),
            'average_rating' => $statsBase()->avg('rating'),
            'five_star' => $statsBase()->where('rating', 5)->count(),
            'recent_count' => $statsBase()
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];

        foreach ($reviews as $review) {
            if ($review->user) {
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

    public function dismiss(Review $review)
    {
        $this->authorize('dismiss', $review);

        if (! Schema::hasTable('hub_owner_review_dismissals')) {
            return redirect()->back()->with('error', 'Run database migrations to enable hiding feedback from your list.');
        }

        HubOwnerReviewDismissal::firstOrCreate([
            'review_id' => $review->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Feedback removed from your list.');
    }

    public function respond(Request $request, Review $review)
    {
        if (! $review->isOwnedByHubOwner((int) Auth::id()) || $review->status !== 'approved') {
            abort(403, 'Unauthorized review response.');
        }

        if (Schema::hasTable('hub_owner_review_dismissals')
            && HubOwnerReviewDismissal::where('review_id', $review->id)->where('user_id', Auth::id())->exists()) {
            abort(403, 'This feedback is hidden from your list.');
        }

        if (! $this->hasResponseColumns()) {
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
