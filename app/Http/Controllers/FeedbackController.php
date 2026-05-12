<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\User;
use App\Models\Booking;
use App\Notifications\FeedbackSubmittedNotification;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Landing page with approved public workspace reviews.
     */
    public function welcome()
    {
        $data = $this->buildPublicWorkspaceReviews();
        $data['reviewsHeading'] = 'REVIEWS';
        $data['reviewsSubheading'] = 'Real feedback from guests who booked our partner workspaces.';
        $data['showReviewsLocationLink'] = true;

        return view('welcome', $data);
    }

    /**
     * Show public approved workspace reviews for decision guidance.
     */
    public function publicReviews()
    {
        return view('location', $this->buildPublicWorkspaceReviews());
    }

    /**
     * Approved workspace feedback grouped by hub / booking name for public display.
     *
     * @return array{reviewsByWorkspace: \Illuminate\Support\Collection, workspaceStats: \Illuminate\Support\Collection}
     */
    protected function buildPublicWorkspaceReviews(): array
    {
        $approvedWorkspaceReviews = Review::where('status', 'approved')
            ->where('feedback_type', 'workspace')
            ->with(['user:id,name', 'hubOwner:id,name,company', 'booking:id,hub_name'])
            ->latest()
            ->get();

        $publicReviews = $approvedWorkspaceReviews->map(function (Review $review) {
            // Title shown per card: booking venue name, then company, then hub owner's name — but skip
            // placeholder values like "Hub Owner" (common default account name / bad hub_name).
            $workspaceName = null;
            foreach ([
                $review->booking?->hub_name,
                $review->hubOwner?->company,
                $review->hubOwner?->name,
            ] as $candidate) {
                $t = trim((string) ($candidate ?? ''));
                if ($t === '') {
                    continue;
                }
                if (preg_match('/^hub\s*owner$/i', $t)) {
                    continue;
                }
                $workspaceName = $t;
                break;
            }
            $workspaceName ??= 'Pwesto Workspace';

            $reviewerName = trim((string) ($review->user?->name ?? 'Anonymous'));
            $maskedReviewer = $reviewerName !== ''
                ? strtoupper(substr($reviewerName, 0, 1)) . str_repeat('*', 4)
                : 'Anonymous';

            return [
                'workspace' => $workspaceName,
                'reviewer' => $maskedReviewer,
                'rating' => (int) $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at,
            ];
        });

        $reviewsByWorkspace = $publicReviews
            ->groupBy('workspace')
            ->map(fn ($items) => $items->take(5)->values())
            ->sortKeys();

        $workspaceStats = $publicReviews
            ->groupBy('workspace')
            ->map(function ($items) {
                return [
                    'review_count' => $items->count(),
                    'average_rating' => round($items->avg('rating'), 1),
                ];
            });

        return compact('reviewsByWorkspace', 'workspaceStats');
    }

    /**
     * Show feedback form
     */
    public function create(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $workspace = $request->query('workspace'); // produktiv, nest, mesh-media, pwesto
        $booking = null;
        $hubOwnerId = null;

        if ($bookingId) {
            $booking = Booking::where('id', $bookingId)
                ->where('user_id', Auth::id())
                ->first();
            if ($booking) {
                $hubOwnerId = $booking->hub_owner_id;
            }
        } else {
            // Get hub owner based on workspace selection
            if ($workspace) {
                $hubOwner = $this->getHubOwnerByWorkspace($workspace);
                if ($hubOwner) {
                    $hubOwnerId = $hubOwner->id;
                } else {
                    // Fallback: get first approved hub owner or admin for platform feedback
                    if ($workspace === 'pwesto') {
                        $hubOwner = User::where('role', 'admin')->first();
                    } else {
                        $hubOwner = User::where('role', 'hub_owner')
                            ->where('status', 'approved')
                            ->first();
                    }
                    if ($hubOwner) {
                        $hubOwnerId = $hubOwner->id;
                    }
                }
            }
        }

        // Get workspace list for dropdown
        $workspaces = $this->getAvailableWorkspaces();

        return view('feedback.create', compact('booking', 'workspace', 'hubOwnerId', 'workspaces'));
    }

    /**
     * Store feedback
     */
    public function store(Request $request)
    {
        $request->validate([
            'hub_owner_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'feedback_type' => 'required|in:workspace,platform',
            'booking_id' => 'nullable|exists:bookings,id'
        ], [
            'rating.required' => 'Please provide a rating.',
            'comment.required' => 'Please provide your feedback.',
        ]);

        // Validate word count (500 words maximum)
        $wordCount = str_word_count($request->comment);
        if ($wordCount > 500) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['comment' => 'Feedback must not exceed 500 words. You have ' . $wordCount . ' words.']);
        }

        // Check if user already submitted feedback for this booking (if linked)
        if ($request->booking_id) {
            $existingReview = Review::where('booking_id', $request->booking_id)
                ->where('user_id', Auth::id())
                ->first();
            if ($existingReview) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You have already submitted feedback for this booking.');
            }
        }

        // Auto-flag suspicious content
        $isFlagged = $this->checkForProfanity($request->comment);

        // High priority if flagged OR if rating is 1-2 stars (likely complaint)
        $isLowRating = (int) $request->rating <= 2;
        $isHighPriority = $isFlagged || $isLowRating;

        $review = Review::create([
            'user_id' => Auth::id(),
            'hub_owner_id' => $request->hub_owner_id,
            'booking_id' => $request->booking_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'feedback_type' => $request->feedback_type,
            'status' => 'pending',
            'is_flagged' => $isFlagged,
            'priority' => $isHighPriority ? 1 : 0,
        ]);

        Auth::user()?->notify(new FeedbackSubmittedNotification($review));

        return redirect()->route('profile.feedback')
            ->with('success', 'Thank you for your feedback! It is pending admin approval.');
    }

    /**
     * Show user's feedback history
     */
    public function index()
    {
        $reviews = Review::where('user_id', Auth::id())
            ->with('hubOwner', 'booking')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('feedback.index', compact('reviews'));
    }

    /**
     * Get hub owner by workspace name
     */
    private function getHubOwnerByWorkspace($workspace)
    {
        $workspaceMap = [
            'produktiv' => ['Produktiv', 'produktiv'],
            'nest' => ['Nest', 'nest'],
            'mesh-media' => ['Mesh Media', 'mesh media', 'mesh'],
            'pwesto' => null, // Platform feedback, no specific hub owner
        ];

        // For Pwesto platform feedback, return a default admin user
        if ($workspace === 'pwesto' || !isset($workspaceMap[$workspace])) {
            return User::where('role', 'admin')->first();
        }

        $searchTerms = $workspaceMap[$workspace];
        
        // First, try to find by company name
        $hubOwner = User::where('role', 'hub_owner')
            ->where(function($query) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $query->orWhereRaw('LOWER(company) LIKE ?', ['%' . strtolower($term) . '%']);
                }
            })
            ->where('status', 'approved')
            ->first();

        // If not found by company, try to find by name
        if (!$hubOwner) {
            $hubOwner = User::where('role', 'hub_owner')
                ->where(function($query) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $query->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($term) . '%']);
                    }
                })
                ->where('status', 'approved')
                ->first();
        }

        // If still not found, return the first approved hub owner as fallback
        // This allows feedback to still be submitted
        if (!$hubOwner) {
            $hubOwner = User::where('role', 'hub_owner')
                ->where('status', 'approved')
                ->first();
        }

        return $hubOwner;
    }

    /**
     * Get available workspaces
     */
    private function getAvailableWorkspaces()
    {
        return [
            ['id' => 'produktiv', 'name' => 'Produktiv - Osmeña Workspace'],
            ['id' => 'nest', 'name' => 'Nest - Horizons 101'],
            ['id' => 'mesh-media', 'name' => 'Mesh Media'],
            ['id' => 'pwesto', 'name' => 'Pwesto Platform'],
        ];
    }

    /**
     * Enhanced profanity and spam filter
     */
    private function checkForProfanity($text)
    {
        $textLower = strtolower($text);
        
        // Common profanity and inappropriate words
        $profanityWords = [
            'spam', 'scam', 'fraud', 'fake', 'liar', 'cheat',
            // Add more profanity words here (keeping it basic for now)
        ];
        
        // Check for profanity
        foreach ($profanityWords as $word) {
            if (strpos($textLower, $word) !== false) {
                return true;
            }
        }

        // Check for excessive repetition (spam indicator)
        $words = preg_split('/\s+/', $textLower);
        if (count($words) > 10) {
            $wordCounts = array_count_values($words);
            foreach ($wordCounts as $count) {
                if ($count > 5) {
                    return true; // Same word repeated more than 5 times
                }
            }
        }

        // Check for excessive capitalization (spam indicator)
        $capsCount = preg_match_all('/[A-Z]/', $text);
        if ($capsCount > strlen($text) * 0.5 && strlen($text) > 20) {
            return true; // More than 50% capital letters
        }

        // Check for URL spam (multiple URLs)
        $urlCount = preg_match_all('/(https?:\/\/|www\.)[^\s]+/i', $text);
        if ($urlCount > 2) {
            return true; // More than 2 URLs
        }

        // Check for excessive punctuation (spam indicator)
        $punctCount = preg_match_all('/[!?.]{3,}/', $text);
        if ($punctCount > 2) {
            return true; // Multiple instances of excessive punctuation
        }

        return false;
    }

    /**
     * Get hub owner ID by workspace (AJAX endpoint)
     */
    public function getHubOwner(Request $request)
    {
        $workspace = $request->query('workspace');
        
        if (!$workspace) {
            return response()->json([
                'hub_owner_id' => null,
                'success' => false,
                'message' => 'Workspace parameter is required.'
            ], 400);
        }
        
        $hubOwner = $this->getHubOwnerByWorkspace($workspace);
        
        return response()->json([
            'hub_owner_id' => $hubOwner ? $hubOwner->id : null,
            'success' => $hubOwner !== null,
            'message' => $hubOwner ? 'Hub owner found.' : 'Using default hub owner.'
        ]);
    }
}

