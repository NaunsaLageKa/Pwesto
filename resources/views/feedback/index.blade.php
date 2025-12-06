@extends('layouts.app')

@section('content')
<div style="background:#222; min-height:100vh; padding:2rem 0;">
    <div style="max-width:1000px; width:100%; margin:0 auto; background:#fff; border-radius:24px; padding:2.5rem 2rem; box-shadow:0 4px 32px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <div>
                <h2 style="font-size:2.2rem; font-weight:900; margin-bottom:0.5rem;">My Feedback</h2>
                <p style="color:#666;">View and manage your submitted feedback</p>
            </div>
            <a href="{{ route('feedback.create') }}" style="background:#19c2b8; color:#fff; padding:0.75rem 1.5rem; border-radius:8px; text-decoration:none; font-weight:600;">
                + Submit New Feedback
            </a>
        </div>

        @if(session('success'))
            <div style="background:#d4edda; color:#155724; padding:1rem; border-radius:8px; margin-bottom:1.5rem;">
                {{ session('success') }}
            </div>
        @endif

        @forelse($reviews as $review)
            <div style="border:1px solid #e0e0e0; border-radius:12px; padding:1.5rem; margin-bottom:1.5rem;">
                <div style="margin-bottom:1rem;">
                    <div>
                        <h3 style="font-size:1.2rem; font-weight:600; margin-bottom:0.5rem;">
                            {{ $review->hubOwner ? $review->hubOwner->company ?? $review->hubOwner->name : 'Pwesto Platform' }}
                        </h3>
                        <p style="color:#666; font-size:0.9rem;">
                            {{ $review->created_at->format('M d, Y g:i A') }}
                            @if($review->booking)
                                | Booking: {{ $review->booking->booking_date->format('M d, Y') }}
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Rating -->
                <div style="display:flex; gap:0.25rem; margin-bottom:1rem;">
                    @for($i = 1; $i <= 5; $i++)
                        <svg style="width:20px; height:20px;" fill="{{ $i <= $review->rating ? '#ffc107' : 'none' }}" stroke="{{ $i <= $review->rating ? '#ffc107' : '#ddd' }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    @endfor
                    <span style="margin-left:0.5rem; color:#666;">{{ $review->rating }}/5</span>
                </div>

                <!-- Comment -->
                <p style="color:#333; line-height:1.6; margin-bottom:0.5rem;">{{ $review->comment }}</p>

                <div style="margin-top:1rem; font-size:0.85rem; color:#666;">
                    <span>Type: {{ ucfirst($review->feedback_type) }}</span>
                </div>
            </div>
        @empty
            <div style="text-align:center; padding:3rem; color:#666;">
                <p style="font-size:1.2rem; margin-bottom:1rem;">No feedback submitted yet</p>
                <a href="{{ route('feedback.create') }}" style="background:#19c2b8; color:#fff; padding:0.75rem 1.5rem; border-radius:8px; text-decoration:none; font-weight:600; display:inline-block;">
                    Submit Your First Feedback
                </a>
            </div>
        @endforelse

        @if($reviews->hasPages())
            <div style="margin-top:2rem;">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
