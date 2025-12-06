<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        // Default to showing pending reviews with priority
        if (!$request->filled('status')) {
            $query = Review::pendingPriority()->with('user', 'hubOwner', 'booking');
        } else {
            $query = Review::with('user', 'hubOwner', 'booking');
            
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
        }
        
        // Filter by flagged
        if ($request->filled('flagged')) {
            $query->where('is_flagged', $request->input('flagged') == '1');
        }
        
        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('comment', 'like', "%$search%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%$search%");
                  })
                  ->orWhereHas('hubOwner', function($hubQuery) use ($search) {
                      $hubQuery->where('name', 'like', "%$search%")
                               ->orWhere('company', 'like', "%$search%");
                  });
            });
        }
        
        $reviews = $query->paginate(15)->withQueryString();
        
        // Dashboard statistics
        $stats = $this->getStats();
        
        return view('admin.reviews.index', compact('reviews', 'stats'));
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
        $review->delete();
        
        return $this->successRedirect('Review deleted successfully.');
    }

    /**
     * Bulk actions: approve, reject, or delete multiple reviews
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
            'moderation_notes' => 'nullable|string|max:500'
        ]);

        $action = $request->input('action');
        $reviewIds = $request->input('review_ids');
        $count = 0;

        DB::transaction(function () use ($action, $reviewIds, $request, &$count) {
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
                        
                    case 'delete':
                        $review->delete();
                        break;
                }
                $count++;
            }
        });

        $message = ucfirst($action) . ' ' . $count . ' review(s) successfully.';
        return $this->successRedirect($message);
    }

    /**
     * Get dashboard statistics
     */
    private function getStats()
    {
        return [
            'pending_count' => Review::where('status', 'pending')->count(),
            'flagged_count' => Review::where('is_flagged', true)->where('status', 'pending')->count(),
            'high_priority_count' => Review::where('priority', 1)->where('status', 'pending')->count(),
            'average_rating' => Review::where('status', 'approved')->avg('rating'),
            'total_reviews' => Review::count(),
            'approved_reviews' => Review::where('status', 'approved')->count(),
            'recent_activity' => Review::with('user', 'hubOwner')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Simple moderation log (you can enhance this with a dedicated table)
     */
    private function logModeration($review, $action, $notes)
    {
        // For now, we'll store it in moderation_notes
        // In production, create a separate moderation_logs table
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
