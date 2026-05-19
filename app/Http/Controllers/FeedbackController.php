<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Review;
use App\Models\User;
use App\Models\Booking;
use App\Notifications\FeedbackSubmittedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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
     * Location page (map + workspace info). Public review cards are shown on the home page only.
     */
    public function publicReviews()
    {
        return view('location');
    }

    /**
     * Workspace reviews that are approved and published for public Location / welcome pages.
     * New workspace feedback is published automatically on submit (published_to_public_at set in store).
     *
     * @return array{reviewsByWorkspace: \Illuminate\Support\Collection, workspaceStats: \Illuminate\Support\Collection}
     */
    protected function buildPublicWorkspaceReviews(): array
    {
        if (! Schema::hasColumn('reviews', 'published_to_public_at')) {
            return [
                'reviewsByWorkspace' => collect(),
                'workspaceStats' => collect(),
            ];
        }

        $approvedQuery = Review::query()
            ->where('reviews.status', 'approved')
            ->where('reviews.feedback_type', 'workspace')
            ->whereNotNull('reviews.published_to_public_at');

        if (Schema::hasColumn('reviews', 'deleted_at')) {
            $approvedQuery->whereNull('reviews.deleted_at');
        }

        $approvedWorkspaceReviews = $approvedQuery
            ->with(['user:id,name', 'hubOwner:id,name,company', 'booking:id,hub_name'])
            ->latest('reviews.created_at')
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
                ->where('status', 'completed')
                ->first();
            if ($booking) {
                $hubOwnerId = $booking->hub_owner_id;
            }
        } elseif ($workspace) {
            $hubOwner = $this->resolveHubOwnerForWorkspace($workspace);
            if ($hubOwner) {
                $hubOwnerId = $hubOwner->id;
            }
        }

        // Get workspace list for dropdown
        $workspaces = $this->getAvailableWorkspaces();

        $bookableWorkspaceIds = [];
        foreach (['produktiv', 'nest', 'mesh-media'] as $wid) {
            $ho = $this->resolveHubOwnerForWorkspace($wid);
            if ($ho && $this->userHasBookingForHub(Auth::user(), $ho)) {
                $bookableWorkspaceIds[] = $wid;
            }
        }

        return view('feedback.create', compact('booking', 'workspace', 'hubOwnerId', 'workspaces', 'bookableWorkspaceIds'));
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
            'booking_id' => 'nullable|exists:bookings,id',
            'workspace' => 'nullable|string|in:produktiv,nest,mesh-media,pwesto',
        ], [
            'rating.required' => 'Please provide a rating.',
            'comment.required' => 'Please provide your feedback.',
        ]);

        if ($request->booking_id) {
            $linkedBooking = Booking::where('id', $request->booking_id)
                ->where('user_id', Auth::id())
                ->first();
            if (! $linkedBooking || $linkedBooking->status !== 'completed') {
                throw ValidationException::withMessages([
                    'booking_id' => 'Feedback can only be linked to a completed booking.',
                ]);
            }
        }

        $workspace = $request->input('workspace');

        if ($request->feedback_type === 'workspace') {
            if ($workspace === 'pwesto') {
                throw ValidationException::withMessages([
                    'feedback_type' => 'For Pwesto app feedback, choose “Platform Feedback”. To review a coworking space, select Produktiv, Nest, or Mesh Media.',
                ]);
            }

            $resolvedHub = $workspace ? $this->resolveHubOwnerForWorkspace($workspace) : null;
            if (! $resolvedHub || (int) $resolvedHub->id !== (int) $request->hub_owner_id) {
                throw ValidationException::withMessages([
                    'workspace' => 'The selected workspace does not match the hub. Refresh the page and try again.',
                ]);
            }

            if (! $this->userHasBookingForHub(Auth::user(), $resolvedHub)) {
                throw ValidationException::withMessages([
                    'workspace' => 'You can submit workspace feedback only after a visit at that coworking space has been marked complete.',
                ]);
            }
        } elseif ($request->feedback_type === 'platform') {
            $resolvedPlatform = $this->resolveHubOwnerForWorkspace('pwesto');
            if (! $resolvedPlatform || (int) $resolvedPlatform->id !== (int) $request->hub_owner_id) {
                throw ValidationException::withMessages([
                    'workspace' => 'Platform feedback must use “Pwesto Platform” as the workspace.',
                ]);
            }
            if (($workspace ?? '') !== 'pwesto') {
                throw ValidationException::withMessages([
                    'workspace' => 'Select “Pwesto Platform” for platform feedback.',
                ]);
            }
        }

        // Validate word count (500 words maximum)
        $wordCount = str_word_count($request->comment);
        if ($wordCount > 500) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['comment' => 'Feedback must not exceed 500 words. You have ' . $wordCount . ' words.']);
        }

        // Auto-flag suspicious content
        $isFlagged = $this->checkForProfanity($request->comment);

        // High priority if flagged OR if rating is 1-2 stars (likely complaint)
        $isLowRating = (int) $request->rating <= 2;
        $isHighPriority = $isFlagged || $isLowRating;

        $isWorkspace = $request->feedback_type === 'workspace';

        // Workspace feedback: approved for the hub owner dashboard immediately; also published to
        // public Location / welcome when the schema supports it (published_to_public_at).
        $reviewAttributes = [
            'user_id' => Auth::id(),
            'hub_owner_id' => $request->hub_owner_id,
            'booking_id' => $request->booking_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'feedback_type' => $request->feedback_type,
            'status' => $isWorkspace ? 'approved' : 'pending',
            'approved_at' => $isWorkspace ? now() : null,
            'approved_by' => null,
            'is_flagged' => $isFlagged,
            'priority' => $isHighPriority ? 1 : 0,
        ];

        if ($isWorkspace && Schema::hasColumn('reviews', 'published_to_public_at')) {
            $reviewAttributes['published_to_public_at'] = now();
            if (Schema::hasColumn('reviews', 'published_to_public_by')) {
                $reviewAttributes['published_to_public_by'] = null;
            }
        }

        $review = Review::create($reviewAttributes);

        Auth::user()?->notify(new FeedbackSubmittedNotification($review));

        $successMessage = $isWorkspace
            ? 'Thank you for your feedback! The venue can see it right away, and it is shown on our public reviews as well.'
            : 'Thank you for your feedback! Platform feedback is pending admin approval.';

        return redirect()->route('profile.feedback')
            ->with('success', $successMessage);
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
     * Resolve the hub owner or admin for a workspace key.
     * Uses ordered company candidates (most specific first) and prefers recently updated rows,
     * so feedback is stored against the same hub owner the app uses for Nest / Produktiv / Mesh bookings.
     */
    private function resolveHubOwnerForWorkspace(?string $workspace): ?User
    {
        if (! $workspace) {
            return null;
        }

        if ($workspace === 'pwesto') {
            return User::where('role', 'admin')->first();
        }

        /** @var list<string> $companyCandidates longest / canonical names first */
        $companyCandidates = match ($workspace) {
            'produktiv' => ['Produktiv'],
            'nest' => ['Nest Workspaces', 'Nest'],
            'mesh-media' => ['Mesh Media', 'Mesh'],
            default => [],
        };

        if ($companyCandidates === []) {
            return null;
        }

        foreach ($companyCandidates as $needle) {
            $n = strtolower(trim($needle));
            if ($n === '') {
                continue;
            }
            $hub = User::where('role', 'hub_owner')
                ->where('status', 'approved')
                ->whereRaw('LOWER(TRIM(company)) = ?', [$n])
                ->orderByDesc('updated_at')
                ->first();
            if ($hub) {
                return $hub;
            }
        }

        foreach ($companyCandidates as $needle) {
            $n = strtolower(trim($needle));
            if ($n === '') {
                continue;
            }
            $hub = User::where('role', 'hub_owner')
                ->where('status', 'approved')
                ->whereRaw('LOWER(company) LIKE ?', ['%' . $n . '%'])
                ->orderByDesc('updated_at')
                ->first();
            if ($hub) {
                return $hub;
            }
        }

        foreach ($companyCandidates as $needle) {
            $n = strtolower(trim($needle));
            if ($n === '') {
                continue;
            }
            $hub = User::where('role', 'hub_owner')
                ->where('status', 'approved')
                ->whereRaw('LOWER(name) LIKE ?', ['%' . $n . '%'])
                ->orderByDesc('updated_at')
                ->first();
            if ($hub) {
                return $hub;
            }
        }

        return null;
    }

    /**
     * Whether the user has at least one completed booking at this hub (by hub_owner_id or hub_name vs company).
     */
    private function userHasBookingForHub(User $user, User $hubOwner): bool
    {
        if ($hubOwner->role === 'admin') {
            return true;
        }

        return Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->where(function ($q) use ($hubOwner) {
                $q->where('hub_owner_id', $hubOwner->id);
                if ($hubOwner->company) {
                    $q->orWhereRaw('LOWER(hub_name) LIKE ?', ['%' . strtolower($hubOwner->company) . '%']);
                }
            })
            ->exists();
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
        
        $hubOwner = $this->resolveHubOwnerForWorkspace($workspace);

        return response()->json([
            'hub_owner_id' => $hubOwner?->id,
            'success' => $hubOwner !== null,
            'message' => $hubOwner ? 'Hub owner found.' : 'No hub owner is configured for this workspace.',
        ]);
    }
}

