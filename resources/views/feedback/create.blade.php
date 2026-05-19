@extends('layouts.app')

@section('content')
<div style="background:#222; min-height:100vh; padding:2rem 0;">
    <div style="max-width:800px; width:100%; margin:0 auto; background:#fff; border-radius:24px; padding:2.5rem 2rem; box-shadow:0 4px 32px rgba(0,0,0,0.2);">
        <h2 style="font-size:2.2rem; font-weight:900; margin-bottom:0.5rem;">Submit Feedback</h2>
        <p style="margin-bottom:2rem; color:#666;">Share your experience with us</p>

        @if(session('success'))
            <div style="background:#d4edda; color:#155724; padding:1rem; border-radius:8px; margin-bottom:1.5rem; border:1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background:#f8d7da; color:#721c24; padding:1rem; border-radius:8px; margin-bottom:1.5rem; border:1px solid #f5c6cb;">
                <ul style="margin:0; padding-left:1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('feedback.store') }}" id="feedback-form">
            @csrf

            <!-- Workspace Selection -->
            <div style="margin-bottom:1.5rem;">
                <label for="workspace" style="font-weight:600; display:block; margin-bottom:0.5rem;">Select Workspace/Platform</label>
                <select id="workspace" name="workspace" required style="width:100%; padding:1rem 1.2rem; border:1px solid #e0e0e0; border-radius:10px; font-size:1rem; background:#f3f6f9;">
                    <option value="">-- Select a workspace --</option>
                    @foreach($workspaces as $ws)
                        <option value="{{ $ws['id'] }}" {{ ($workspace === $ws['id'] || old('workspace') === $ws['id']) ? 'selected' : '' }}>
                            {{ $ws['name'] }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="hub_owner_id" id="hub_owner_id" value="{{ old('hub_owner_id', $hubOwnerId) }}" required>
                <p style="font-size:0.9rem;color:#555;margin-top:0.5rem;line-height:1.4;">
                    <strong>Workspace feedback</strong> is available only after that stay has been <strong>marked complete</strong> by the hub (completed booking).
                    <strong>Platform feedback</strong> is for the Pwesto app itself and does not require a completed visit.
                </p>
                <p style="font-size:0.9rem;color:#444;margin-top:0.75rem;padding:0.75rem 1rem;background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;line-height:1.5;">
                    <strong>Coworking space feedback:</strong> your hub and Pwesto admins see it as soon as you submit (after a completed visit). It can appear on our public home page. <strong>Pwesto admins</strong> can remove it from the public home only; your hub still sees it until you hide it from your hub list.
                </p>
            </div>

            <!-- Feedback Type -->
            <div style="margin-bottom:1.5rem;">
                <label style="font-weight:600; display:block; margin-bottom:0.5rem;">Feedback Type</label>
                <div style="display:flex; gap:1rem;">
                    <label style="display:flex; align-items:center; cursor:pointer;">
                        <input type="radio" name="feedback_type" value="workspace" {{ old('feedback_type', 'workspace') === 'workspace' ? 'checked' : '' }} required style="margin-right:0.5rem;">
                        <span>Workspace Feedback</span>
                    </label>
                    <label style="display:flex; align-items:center; cursor:pointer;">
                        <input type="radio" name="feedback_type" value="platform" {{ old('feedback_type') === 'platform' ? 'checked' : '' }} required style="margin-right:0.5rem;">
                        <span>Platform Feedback (Pwesto)</span>
                    </label>
                </div>
            </div>

            <!-- Booking Reference (if available) -->
            @if($booking)
                <div style="margin-bottom:1.5rem; padding:1rem; background:#e3f2fd; border-radius:8px;">
                    <p style="margin:0; color:#1976d2; font-weight:500;">
                        📅 Feedback for booking on {{ $booking->booking_date->format('M d, Y') }} at {{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}
                    </p>
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                </div>
            @endif

            <!-- Star Rating -->
            <div style="margin-bottom:1.5rem;">
                <label style="font-weight:600; display:block; margin-bottom:0.5rem;">Rating</label>
                <div style="display:flex; gap:0.5rem; align-items:center;" id="rating-container">
                    @for($i = 1; $i <= 5; $i++)
                        <svg id="star-{{ $i }}" class="star-icon" data-rating="{{ $i }}" style="width:40px; height:40px; cursor:pointer; transition:all 0.2s;" fill="none" stroke="#ddd" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    @endfor
                    <span id="rating-text" style="margin-left:1rem; font-weight:500; color:#666;">Select a rating</span>
                </div>
                <input type="hidden" name="rating" id="rating-input" value="{{ old('rating', '') }}" required>
            </div>

            <!-- Feedback Comment -->
            <div style="margin-bottom:1.5rem;">
                <label for="comment" style="font-weight:600; display:block; margin-bottom:0.5rem;">
                    Feedback (Max 500 words)
                </label>
                <textarea id="comment" name="comment" rows="8" required style="width:100%; padding:1rem 1.2rem; border:1px solid #e0e0e0; border-radius:10px; font-size:1rem; resize:vertical; font-family:inherit;" placeholder="Share your experience...">{{ old('comment') }}</textarea>
                <div style="display:flex; justify-content:space-between; margin-top:0.5rem;">
                    <span id="word-count" style="color:#666; font-size:0.9rem;">0 / 500 words</span>
                    <span id="char-count" style="color:#666; font-size:0.9rem;">0 characters</span>
                </div>
            </div>

            <!-- Submit Button -->
            <div style="display:flex; gap:1rem;">
                <button type="submit" style="background:#19c2b8; color:#fff; padding:1rem 2rem; border:none; border-radius:10px; font-size:1.1rem; font-weight:600; cursor:pointer; flex:1; transition:background 0.2s;" onmouseover="this.style.background='#17a8a0'" onmouseout="this.style.background='#19c2b8'">
                    Submit Feedback
                </button>
                <a href="{{ route('profile.feedback') }}" style="background:#aaa; color:#fff; padding:1rem 2rem; border-radius:10px; font-size:1.1rem; font-weight:600; text-decoration:none; text-align:center; display:inline-block;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Star Rating Functionality
let selectedRating = 0;
const stars = document.querySelectorAll('.star-icon');
const ratingInput = document.getElementById('rating-input');
const ratingText = document.getElementById('rating-text');

stars.forEach(star => {
    star.addEventListener('click', function() {
        selectedRating = parseInt(this.dataset.rating);
        ratingInput.value = selectedRating;
        updateStars(selectedRating);
        ratingText.textContent = selectedRating === 1 ? '1 star - Poor' : 
                                  selectedRating === 2 ? '2 stars - Fair' :
                                  selectedRating === 3 ? '3 stars - Good' :
                                  selectedRating === 4 ? '4 stars - Very Good' :
                                  '5 stars - Excellent';
    });

    star.addEventListener('mouseenter', function() {
        const hoverRating = parseInt(this.dataset.rating);
        updateStars(hoverRating, true);
    });
});

document.getElementById('rating-container').addEventListener('mouseleave', function() {
    updateStars(selectedRating, false);
});

function updateStars(rating, isHover = false) {
    stars.forEach((star, index) => {
        const starNum = index + 1;
        if (starNum <= rating) {
            star.style.fill = '#ffc107';
            star.style.stroke = '#ffc107';
        } else {
            star.style.fill = 'none';
            star.style.stroke = '#ddd';
        }
    });
}

// Word and Character Counter (500 WORDS limit)
const commentTextarea = document.getElementById('comment');
const wordCountSpan = document.getElementById('word-count');
const charCountSpan = document.getElementById('char-count');

function countWords(text) {
    if (text.trim() === '') return 0;
    // Split by whitespace and filter out empty strings
    return text.trim().split(/\s+/).filter(word => word.length > 0).length;
}

commentTextarea.addEventListener('input', function() {
    const text = this.value;
    const charCount = text.length;
    const wordCount = countWords(text);
    
    charCountSpan.textContent = `${charCount} characters`;
    wordCountSpan.textContent = `${wordCount} / 500 words`;
    
    // Warning at 90% (450 words)
    if (wordCount > 450) {
        wordCountSpan.style.color = '#f44336';
        wordCountSpan.style.fontWeight = 'bold';
    } else if (wordCount > 400) {
        wordCountSpan.style.color = '#ff9800';
        wordCountSpan.style.fontWeight = '600';
    } else {
        wordCountSpan.style.color = '#666';
        wordCountSpan.style.fontWeight = 'normal';
    }
    
    // Disable submit if over 500 words
    const submitBtn = document.querySelector('button[type="submit"]');
    if (wordCount > 500) {
        commentTextarea.style.borderColor = '#f44336';
        wordCountSpan.textContent = `${wordCount} / 500 words (EXCEEDED LIMIT)`;
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
        submitBtn.style.cursor = 'not-allowed';
    } else {
        commentTextarea.style.borderColor = '#e0e0e0';
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
        submitBtn.style.cursor = 'pointer';
    }
});

const bookableWorkspaces = @json($bookableWorkspaceIds ?? []);

function getFeedbackType() {
    return document.querySelector('input[name="feedback_type"]:checked')?.value || 'workspace';
}

function applyWorkspaceRules() {
    const type = getFeedbackType();
    const sel = document.getElementById('workspace');
    const hubOwnerInput = document.getElementById('hub_owner_id');
    Array.from(sel.options).forEach(opt => {
        if (!opt.value) return;
        if (type === 'platform') {
            opt.disabled = opt.value !== 'pwesto';
        } else {
            opt.disabled = opt.value === 'pwesto' || (['produktiv','nest','mesh-media'].includes(opt.value) && !bookableWorkspaces.includes(opt.value));
        }
    });
    const cur = sel.value;
    if (cur && ((type === 'platform' && cur !== 'pwesto') || (type === 'workspace' && (cur === 'pwesto' || (['produktiv','nest','mesh-media'].includes(cur) && !bookableWorkspaces.includes(cur)))))) {
        sel.value = '';
        hubOwnerInput.value = '';
    }
}

document.querySelectorAll('input[name="feedback_type"]').forEach(r => {
    r.addEventListener('change', () => {
        applyWorkspaceRules();
        document.getElementById('workspace').dispatchEvent(new Event('change'));
    });
});

// Workspace selection handler to fetch hub owner
document.getElementById('workspace').addEventListener('change', function() {
    const workspace = this.value;
    const hubOwnerInput = document.getElementById('hub_owner_id');

    if (workspace) {
        fetch(`/feedback/get-hub-owner?workspace=${encodeURIComponent(workspace)}`)
            .then(response => response.json())
            .then(data => {
                if (data.hub_owner_id) {
                    hubOwnerInput.value = data.hub_owner_id;
                } else {
                    hubOwnerInput.value = '';
                }
            })
            .catch(() => {
                hubOwnerInput.value = '';
            });
    } else {
        hubOwnerInput.value = '';
    }
});

applyWorkspaceRules();
const wsElInit = document.getElementById('workspace');
if (wsElInit.value) {
    wsElInit.dispatchEvent(new Event('change'));
}

// Initialize counts
commentTextarea.dispatchEvent(new Event('input'));
</script>
@endsection
