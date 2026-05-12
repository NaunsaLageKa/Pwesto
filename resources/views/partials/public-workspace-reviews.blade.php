@php
    $heading = $reviewsHeading ?? 'Reviews';
    $sub = $reviewsSubheading ?? 'Community feedback to guide your coworking space decision.';
@endphp
<div class="public-reviews-wrap">
    <h2 class="public-reviews-title">{{ $heading }}</h2>
    <p class="public-reviews-sub">{{ $sub }}</p>

    <div class="public-reviews-grid">
        @forelse(($reviewsByWorkspace ?? collect()) as $workspaceName => $workspaceReviews)
            @php
                $stats = ($workspaceStats ?? collect())->get($workspaceName, ['review_count' => 0, 'average_rating' => 0]);
            @endphp
            <div class="workspace-review-card">
                <div class="workspace-review-title">{{ $workspaceName }}</div>
                <div class="workspace-review-meta">
                    {{ number_format($stats['average_rating'], 1) }}/5
                    • {{ $stats['review_count'] }} {{ $stats['review_count'] === 1 ? 'review' : 'reviews' }}
                </div>

                @foreach($workspaceReviews as $review)
                    <div class="review-item">
                        <div class="review-rating">Rating: {{ $review['rating'] }}/5</div>
                        <div class="review-comment">{{ \Illuminate\Support\Str::limit($review['comment'], 120) }}</div>
                        <div class="review-footer">
                            By {{ $review['reviewer'] }} • {{ $review['created_at']->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="public-reviews-empty">
                No approved public reviews yet.
            </div>
        @endforelse
    </div>

    @if(!empty($showReviewsLocationLink))
        <p class="public-reviews-more">
            <a href="{{ route('location') }}">View map &amp; more reviews</a>
        </p>
    @endif
</div>
