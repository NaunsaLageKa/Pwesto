@extends('layouts.app')

@section('content')
<?php $isEdit = request()->query('edit') == 1; $hasImage = Auth::user()->profile_image; ?>
<div style="background:#222; min-height:100vh; padding:0; margin:0; display:flex; flex-direction:column; align-items:center;">
    <div style="width:100vw; background:#fff; display:flex; align-items:center; justify-content:space-between; padding:0 2.5rem; height:64px; box-shadow:0 2px 8px #0001; position:sticky; top:0; z-index:10;">
        <div style="display:flex; align-items:center; gap:2rem;">
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" style="display:inline-flex; align-items:center; gap:0.5rem; background:#19c2b8; color:#fff; padding:0.5rem 1rem; border-radius:6px; text-decoration:none; font-weight:600; font-size:0.9rem; transition:background-color 0.2s;" onmouseover="this.style.background='#17a8a0'" onmouseout="this.style.background='#19c2b8'">
                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Admin Dashboard
            </a>
            @endif
            <div style="font-size:1.5rem; font-weight:900; color:#19c2b8; letter-spacing:2px;">PWESTO!</div>
        </div>
        <div style="display:flex; align-items:center; gap:2rem;">
            <a href="{{ route('dashboard') }}" style="font-weight:700; color:#111; text-decoration:none;">Home</a>
            <a href="#" style="color:#222; text-decoration:none;">Booking History</a>
            <a href="#" style="color:#222; text-decoration:none;">Services</a>
            <a href="{{ route('about') }}" style="color:#222; text-decoration:none;">About</a>
            <a href="{{ route('location') }}" style="color:#222; text-decoration:none;">Location</a>
            <a href="{{ route('profile.edit') }}">
                <img src="{{ $hasImage ? asset('storage/' . Auth::user()->profile_image) : asset('images/avatar.svg') }}" alt="User" style="width:44px; height:44px; border-radius:50%; object-fit:cover; border:2px solid #eee; {{ !$hasImage ? 'background:#f3f4f6; padding:8px;' : '' }}">
            </a>
        </div>
    </div>
    <div style="max-width:540px; width:100%; margin:40px auto 0 auto; background:#fff; border-radius:24px; padding:2.5rem 2rem; color:#222; box-shadow:0 4px 32px #0002;">
        <h2 style="font-size:2.2rem; font-weight:900; margin-bottom:0.5rem; text-align:left;">Profile</h2>
        <p style="margin-bottom:2rem; color:#666; text-align:left;">Manage your account settings and preferences</p>
        <div style="display:flex; flex-direction:column; align-items:center; margin-bottom:2rem; position:relative;">
            @if ($errors->any())
                <div style="color: red; margin-bottom: 1rem;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div style="position:relative; display:inline-block;">
                <img src="{{ $hasImage ? asset('storage/' . Auth::user()->profile_image) : asset('images/avatar.svg') }}" alt="Profile" style="width:110px; height:110px; border-radius:50%; object-fit:cover; border:4px solid #19c2b8; background:#f3f6f9; {{ !$hasImage ? 'padding:2rem;' : '' }}">
                @if($hasImage)
                <span style="position:absolute; bottom:6px; right:6px; background:#19c2b8; color:#fff; border-radius:50%; width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:2px solid #fff; font-size:1.2rem;">
                    &#10003;
                </span>
                @endif
            </div>
            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" style="text-align:center; margin-top:1rem;">
                @csrf
                @method('patch')
                <input type="hidden" name="name" value="{{ old('name', Auth::user()->name) }}">
                <input type="hidden" name="email" value="{{ old('email', Auth::user()->email) }}">
                <input type="hidden" name="phone" value="{{ old('phone', Auth::user()->phone) }}">
                @if($isEdit)
                    <input type="file" name="profile_image" accept="image/*" style="margin-bottom:1rem; color:#222;">
                    <br>
                    <button type="submit" style="background:#19c2b8; color:#fff; border:none; border-radius:8px; padding:0.5rem 1.5rem; font-size:1rem; font-weight:600; cursor:pointer;">Upload New Image</button>
                @endif
            </form>
        </div>
        <!-- Tabs -->
        <div style="display:flex; gap:2rem; border-bottom:2px solid #e0e0e0; margin-bottom:2.5rem;">
            <a href="{{ route('profile.edit', ['tab' => 'details']) }}" style="font-weight:{{ $activeTab === 'details' ? '700' : '500' }}; color:{{ $activeTab === 'details' ? '#222' : '#888' }}; border-bottom:{{ $activeTab === 'details' ? '3px solid #19c2b8' : '3px solid transparent' }}; padding-bottom:0.5rem; text-decoration:none;">Personal Details</a>
            <a href="{{ route('profile.edit', ['tab' => 'feedback']) }}" style="font-weight:{{ $activeTab === 'feedback' ? '700' : '500' }}; color:{{ $activeTab === 'feedback' ? '#222' : '#888' }}; border-bottom:{{ $activeTab === 'feedback' ? '3px solid #19c2b8' : '3px solid transparent' }}; padding-bottom:0.5rem; text-decoration:none;">Feedback</a>
        </div>
        @if($activeTab === 'details')
        <div style="font-size:1.3rem; font-weight:700; margin-bottom:1.5rem;">Personal Details</div>
        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')
            <div style="margin-bottom:1.5rem;">
                <label for="name" style="font-weight:500;">Full Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', Auth::user()->name) }}" required autofocus autocomplete="name" style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;color:#222;font-size:1.1rem;" {{ $isEdit ? '' : 'readonly' }}>
            </div>
            <div style="margin-bottom:1.5rem;">
                <label for="email" style="font-weight:500;">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', Auth::user()->email) }}" required autocomplete="username" style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;color:#222;font-size:1.1rem;" {{ $isEdit ? '' : 'readonly' }}>
            </div>
            <div style="margin-bottom:2rem;">
                <label for="phone" style="font-weight:500;">Phone Number</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', Auth::user()->phone ?? '') }}" autocomplete="tel" style="width:100%;padding:1rem 1.2rem;border:1px solid #e0e0e0;border-radius:10px;margin-top:0.25rem;background:#f3f6f9;color:#222;font-size:1.1rem;" {{ $isEdit ? '' : 'readonly' }}>
            </div>
            @if($isEdit)
            <div style="display:flex; gap:1rem;">
                <button type="submit" style="background:#1976d2;color:#fff;padding:1rem 0;border:none;border-radius:10px;font-size:1.1rem;font-weight:600;cursor:pointer;width:100%;">Update Profile</button>
                <a href="{{ route('profile.edit') }}" style="background:#aaa; color:#fff; border:none; border-radius:10px; padding:1rem 2rem; font-size:1.1rem; font-weight:600; cursor:pointer; text-decoration:none; display:inline-block; text-align:center;">Cancel</a>
            </div>
            @else
            <div style="text-align:right; margin-bottom:1rem;">
                <a href="{{ route('profile.edit', ['edit' => 1]) }}" style="background:#19c2b8; color:#fff; border:none; border-radius:8px; padding:0.5rem 1.5rem; font-size:1rem; font-weight:600; cursor:pointer; text-decoration:none;">Edit</a>
            </div>
            @endif
        </form>
        @elseif($activeTab === 'feedback')
        <div style="font-size:1.3rem; font-weight:700; margin-bottom:1.5rem;">My Feedback</div>
        
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('feedback.create') }}" style="background:#19c2b8; color:#fff; padding:0.75rem 1.5rem; border-radius:8px; text-decoration:none; font-weight:600; display:inline-block;">
                + Submit New Feedback
            </a>
        </div>

        @if(session('success'))
            <div style="background:#d4edda; color:#155724; padding:1rem; border-radius:8px; margin-bottom:1.5rem;">
                {{ session('success') }}
            </div>
        @endif

        @if($reviews && $reviews->count() > 0)
            @foreach($reviews as $review)
                <div style="border:1px solid #e0e0e0; border-radius:12px; padding:1.5rem; margin-bottom:1rem;">
                    <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:1rem;">
                        <div>
                            <h3 style="font-size:1.1rem; font-weight:600; margin-bottom:0.25rem;">
                                {{ $review->hubOwner ? ($review->hubOwner->company ?? $review->hubOwner->name) : 'Pwesto Platform' }}
                            </h3>
                            <p style="color:#666; font-size:0.85rem;">
                                {{ $review->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>

                    <div style="display:flex; gap:0.25rem; margin-bottom:0.75rem;">
                        @for($i = 1; $i <= 5; $i++)
                            <svg style="width:16px; height:16px;" fill="{{ $i <= $review->rating ? '#ffc107' : 'none' }}" stroke="{{ $i <= $review->rating ? '#ffc107' : '#ddd' }}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        @endfor
                    </div>

                    <p style="color:#333; line-height:1.6; font-size:0.95rem;">{{ Str::limit($review->comment, 200) }}</p>
                    
                    @if(strlen($review->comment) > 200)
                        <a href="{{ route('feedback.index') }}" style="color:#19c2b8; text-decoration:none; font-size:0.9rem; margin-top:0.5rem; display:inline-block;">Read more →</a>
                    @endif
                </div>
            @endforeach

            @if($reviews->hasPages())
                <div style="margin-top:2rem; display:flex; justify-content:center; align-items:center;">
                    {{ $reviews->appends(['tab' => 'feedback'])->links() }}
                </div>
            @endif
        @else
            <div style="text-align:center; padding:2rem; color:#666;">
                <p style="font-size:1.1rem; margin-bottom:1rem;">No feedback submitted yet</p>
                <a href="{{ route('feedback.create') }}" style="background:#19c2b8; color:#fff; padding:0.75rem 1.5rem; border-radius:8px; text-decoration:none; font-weight:600; display:inline-block;">
                    Submit Your First Feedback
                </a>
            </div>
        @endif
        @endif
    </div>
</div>
@endsection
