<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        if (!Schema::hasTable('reviews')) {
            $emptyReviews = new LengthAwarePaginator([], 0, 15, 1, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);

            return view('admin.reviews.index', [
                'reviews' => $emptyReviews,
                'stats' => $this->emptyStats(),
                'sortBy' => '',
                'sortDir' => 'desc',
            ])->with('error', 'Reviews table is missing. Run database migrations to enable review moderation.');
        }

        $allowedSorts = ['user', 'hub_owner', 'rating', 'status', 'created_at', 'feedback_type'];
        $sortBy = (string) $request->input('sort', '');
        $sortDir = strtolower((string) $request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $hasSort = $sortBy !== '' && in_array($sortBy, $allowedSorts, true);

        $reviewWith = ['user', 'hubOwner', 'booking'];
        if (Schema::hasColumn('reviews', 'published_to_public_by')) {
            $reviewWith[] = 'publishedToPublicBy';
        }

        $statusFilter = $request->input('status');

        if ($statusFilter === 'deleted') {
            $query = $this->deletedReviewsQuery($reviewWith);
            if (! $hasSort) {
                $query->orderByDesc('reviews.created_at');
            }
        } else {
            // Active moderation list: pending + approved workspace (not admin-trashed)
            $query = Review::query()->with($reviewWith)->where(function ($q) {
                $q->where('reviews.status', 'pending');
                $q->orWhere(function ($sub) {
                    $sub->where('reviews.status', 'approved')
                        ->where('reviews.feedback_type', 'workspace');
                    $this->excludeAdminArchived($sub);
                });
            });
            if (! $hasSort) {
                $query->orderByRaw("CASE WHEN reviews.status = 'pending' THEN 0 ELSE 1 END");
                $query->orderByRaw('GREATEST(COALESCE(reviews.priority, 0), CASE WHEN reviews.rating <= 2 THEN 1 ELSE 0 END) DESC');
                $query->orderByDesc('reviews.is_flagged');
                $query->orderByDesc('reviews.created_at');
            }
        }

        if ($request->filled('feedback_type')) {
            $query->where('reviews.feedback_type', $request->input('feedback_type'));
        }
        
        // Filter by priority (treat rating <= 2 as high priority regardless of stored value)
        if ($request->filled('priority')) {
            if ($request->input('priority') == '1') {
                $query->where(function ($q) {
                    $q->where('reviews.priority', 1)->orWhere('reviews.rating', '<=', 2);
                });
            } else {
                $query->where('reviews.priority', 0)->where('reviews.rating', '>', 2);
            }
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('reviews.comment', 'like', "%$search%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%$search%");
                  })
                  ->orWhereHas('hubOwner', function($hubQuery) use ($search) {
                      $hubQuery->where('name', 'like', "%$search%")
                               ->orWhere('company', 'like', "%$search%");
                  });
            });
        }

        if ($hasSort) {
            $this->applyReviewSort($query, $sortBy, $sortDir);
        }

        $reviews = $query->paginate(15)->withQueryString();

        // Dashboard statistics
        $stats = $this->getStats();

        return view('admin.reviews.index', compact('reviews', 'stats', 'sortBy', 'sortDir'));
    }

    private function applyReviewSort($query, string $sortBy, string $sortDir): void
    {
        match ($sortBy) {
            'user' => $query->leftJoin('users as review_users', 'reviews.user_id', '=', 'review_users.id')
                ->orderBy('review_users.name', $sortDir)
                ->select('reviews.*'),
            'hub_owner' => $query->leftJoin('users as review_hub_owners', 'reviews.hub_owner_id', '=', 'review_hub_owners.id')
                ->orderByRaw('COALESCE(review_hub_owners.company, review_hub_owners.name) ' . ($sortDir === 'asc' ? 'asc' : 'desc'))
                ->select('reviews.*'),
            'rating' => $query->orderBy('reviews.rating', $sortDir),
            'status' => $query->orderBy('reviews.status', $sortDir),
            'feedback_type' => $query->orderBy('reviews.feedback_type', $sortDir),
            default => $query->orderBy('reviews.created_at', $sortDir),
        };
    }

    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $review->status = 'approved';
        $review->approved_at = now();
        $review->approved_by = Auth::id();
        $review->is_flagged = false; // Clear flag on approval
        $review->save();
        
        // Log moderation action
        $this->logModeration($review, 'approved', null);
        
        return $this->successRedirect('Review approved successfully.');
    }

    public function reject($id, Request $request)
    {
        $review = Review::findOrFail($id);
        $review->status = 'rejected';
        $review->rejected_at = now();
        $review->rejected_by = Auth::id();
        
        if ($request->filled('moderation_notes')) {
            $review->moderation_notes = $request->input('moderation_notes');
        }
        
        $review->save();
        
        // Log moderation action
        $this->logModeration($review, 'rejected', $request->input('moderation_notes'));
        
        return $this->successRedirect('Review rejected successfully.');
    }

    public function delete($id)
    {
        $review = Review::findOrFail($id);
        $this->authorize('delete', $review);

        if ($review->feedback_type === 'workspace' && $review->status === 'approved') {
            if (Schema::hasColumn('reviews', 'published_to_public_at')) {
                $review->published_to_public_at = null;
                $review->published_to_public_by = null;
            }
            if (Schema::hasColumn('reviews', 'admin_archived_at')) {
                $review->admin_archived_at = now();
            }
            $review->save();
            $this->logModeration($review, 'removed_from_public_home', null);

            $msg = Schema::hasColumn('reviews', 'admin_archived_at')
                ? 'Review removed from this admin list and from the public home. Hub owners still see it in their dashboard.'
                : 'Review removed from the public home page. Hub owners still see it in their dashboard.';

            return $this->successRedirect($msg);
        }

        $review->delete();

        return $this->successRedirect('Review deleted.');
    }

    /**
     * Mark an approved workspace review for display on public marketing pages (e.g. home).
     */
    public function publishPublic($id)
    {
        if (! Schema::hasColumn('reviews', 'published_to_public_at')) {
            return $this->errorRedirect('Run database migrations first: php artisan migrate');
        }

        $review = Review::findOrFail($id);

        if ($review->feedback_type !== 'workspace' || $review->status !== 'approved') {
            return $this->errorRedirect('Only approved workspace feedback can be published publicly.');
        }

        $review->published_to_public_at = now();
        $review->published_to_public_by = Auth::id();
        $review->save();

        $this->logModeration($review, 'published_public', null);

        return $this->successRedirect('Review published to the public home page.');
    }

    /**
     * Bulk actions: approve, reject, or delete multiple reviews
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete,publish_public',
            'review_ids' => 'required|array',
            'review_ids.*' => [
                'required',
                Rule::exists('reviews', 'id')->whereNull('deleted_at'),
            ],
            'moderation_notes' => 'nullable|string|max:500'
        ]);

        $action = $request->input('action');
        $reviewIds = $request->input('review_ids');
        $count = 0;
        $removedFromPublic = 0;
        $softDeleted = 0;

        DB::transaction(function () use ($action, $reviewIds, $request, &$count, &$removedFromPublic, &$softDeleted) {
            foreach ($reviewIds as $reviewId) {
                $review = Review::findOrFail($reviewId);
                
                switch ($action) {
                    case 'approve':
                        $review->status = 'approved';
                        $review->approved_at = now();
                        $review->approved_by = Auth::id();
                        $review->is_flagged = false;
                        $review->save();
                        $this->logModeration($review, 'approved', null);
                        break;
                        
                    case 'reject':
                        $review->status = 'rejected';
                        $review->rejected_at = now();
                        $review->rejected_by = Auth::id();
                        if ($request->filled('moderation_notes')) {
                            $review->moderation_notes = $request->input('moderation_notes');
                        }
                        $review->save();
                        $this->logModeration($review, 'rejected', $request->input('moderation_notes'));
                        break;

                    case 'publish_public':
                        if (Schema::hasColumn('reviews', 'published_to_public_at')
                            && $review->feedback_type === 'workspace'
                            && $review->status === 'approved') {
                            $review->published_to_public_at = now();
                            $review->published_to_public_by = Auth::id();
                            $review->save();
                            $this->logModeration($review, 'published_public', null);
                        }
                        break;

                    case 'delete':
                        $this->authorize('delete', $review);
                        if ($review->feedback_type === 'workspace' && $review->status === 'approved') {
                            if (Schema::hasColumn('reviews', 'published_to_public_at')) {
                                $review->published_to_public_at = null;
                                $review->published_to_public_by = null;
                            }
                            if (Schema::hasColumn('reviews', 'admin_archived_at')) {
                                $review->admin_archived_at = now();
                            }
                            $review->save();
                            $this->logModeration($review, 'removed_from_public_home', null);
                            $removedFromPublic++;
                        } else {
                            $review->delete();
                            $softDeleted++;
                        }
                        break;
                }
                $count++;
            }
        });

        if ($action === 'delete') {
            $parts = [];
            if ($removedFromPublic > 0) {
                $parts[] = $removedFromPublic . ' removed from this admin list and public home (hub dashboards unchanged)';
            }
            if ($softDeleted > 0) {
                $parts[] = $softDeleted . ' deleted';
            }
            $message = $parts !== [] ? implode('; ', $parts) . '.' : 'No changes applied.';

            return $this->successRedirect($message);
        }

        $message = ucfirst($action) . ' ' . $count . ' review(s) successfully.';
        return $this->successRedirect($message);
    }

    /**
     * Get dashboard statistics
     */
    private function getStats()
    {
        if (!Schema::hasTable('reviews')) {
            return $this->emptyStats();
        }

        return [
            'pending_count' => $this->adminVisibleReviewQuery()->where('status', 'pending')->count(),
            'high_priority_count' => $this->adminVisibleReviewQuery()->where('status', 'pending')
                ->where(function ($q) {
                    $q->where('priority', 1)->orWhere('rating', '<=', 2);
                })
                ->count(),
            'average_rating' => $this->adminVisibleReviewQuery()->where('status', 'approved')->avg('rating'),
            'total_reviews' => $this->adminVisibleReviewQuery()->count(),
            'approved_reviews' => $this->adminVisibleReviewQuery()->where('status', 'approved')->count(),
            'recent_activity' => $this->adminVisibleReviewQuery()->with('user', 'hubOwner')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    private function emptyStats()
    {
        return [
            'pending_count' => 0,
            'high_priority_count' => 0,
            'average_rating' => 0,
            'total_reviews' => 0,
            'approved_reviews' => 0,
            'recent_activity' => collect(),
        ];
    }

   
    /**
     * Base query for counts and lists shown in admin (excludes workspace rows removed via trash).
     */
    private function adminVisibleReviewQuery()
    {
        $query = Review::query();
        $this->excludeAdminArchived($query);

        return $query;
    }

    /**
     * Reviews removed via trash: soft-deleted rows and admin-archived workspace rows.
     */
    private function deletedReviewsQuery(array $with)
    {
        return Review::withTrashed()
            ->with($with)
            ->where(function ($q) {
                $q->whereNotNull('reviews.deleted_at');
                if (Schema::hasColumn('reviews', 'admin_archived_at')) {
                    $q->orWhereNotNull('reviews.admin_archived_at');
                }
            });
    }

    /**
     * Approved workspace rows "deleted" from admin stay in the DB for hub owners but are hidden here.
     */
    private function excludeAdminArchived($query): void
    {
        if (Schema::hasColumn('reviews', 'admin_archived_at')) {
            $query->whereNull('reviews.admin_archived_at');
        }
    }

    private function logModeration($review, $action, $notes)
    {
       
        $log = [
            'action' => $action,
            'admin_id' => Auth::id(),
            'admin_name' => Auth::user()->name,
            'timestamp' => now()->toDateTimeString(),
            'notes' => $notes
        ];
        
        // Append to existing notes
        $existingNotes = $review->moderation_notes ? json_decode($review->moderation_notes, true) : [];
        if (!is_array($existingNotes)) {
            $existingNotes = [];
        }
        $existingNotes[] = $log;
        
        // Update moderation notes (store as JSON)
        $review->moderation_notes = json_encode($existingNotes);
        $review->save();
    }
}
