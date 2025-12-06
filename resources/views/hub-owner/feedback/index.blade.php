@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 min-h-screen">
            <div class="p-6">
                <div class="text-sm font-bold mb-1" style="color: #19c2b8;">PWESTO</div>
                <div class="text-xl font-bold text-yellow-400 mb-8">{{ strtoupper(auth()->user()->company ?? 'HUB') }} HUB</div>
                <nav class="space-y-1">
                    <a href="{{ route('hub-owner.dashboard') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Dashboard
                    </a>
                    <a href="{{ route('hub-owner.bookings.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Bookings
                    </a>
                    <a href="{{ route('hub-owner.users.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Users
                    </a>
                    <a href="{{ route('hub-owner.feedback.index') }}" class="block px-4 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg">
                        Reviews
                    </a>
                    <a href="{{ route('hub-owner.floor-plan') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Floor Plan
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-50">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Customer Reviews</h1>
                <p class="text-gray-600">View approved feedback from your customers</p>
            </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow border">
                <p class="text-sm font-medium text-gray-600">Total Feedback</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow border">
                <p class="text-sm font-medium text-gray-600">Avg Rating</p>
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['average_rating'] ?? 0, 1) }}</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow border">
                <p class="text-sm font-medium text-gray-600">5-Star Reviews</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['five_star'] }}</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow border">
                <p class="text-sm font-medium text-gray-600">This Week</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['recent_count'] }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <form method="GET" action="{{ route('hub-owner.feedback.index') }}" class="flex flex-wrap gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search feedback..." 
                       class="border rounded px-3 py-2 flex-1 min-w-[200px]">
                <select name="rating" class="border rounded px-3 py-2">
                    <option value="">All Ratings</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                        </option>
                    @endfor
                </select>
                <select name="feedback_type" class="border rounded px-3 py-2">
                    <option value="">All Types</option>
                    <option value="workspace" {{ request('feedback_type') == 'workspace' ? 'selected' : '' }}>Workspace</option>
                    <option value="platform" {{ request('feedback_type') == 'platform' ? 'selected' : '' }}>Platform</option>
                </select>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Filter</button>
                <a href="{{ route('hub-owner.feedback.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Clear</a>
            </form>
        </div>

        <!-- Feedback List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @forelse($reviews as $review)
                <div class="border-b border-gray-200 p-6 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold text-lg">
                                        @php
                                            $displayName = $review->user->display_info ?? ($review->user->name ?? 'A');
                                            $initial = strtoupper(substr($displayName, 0, 1));
                                        @endphp
                                        {{ $initial }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">
                                    {{ $review->user->display_info ?? ($review->user->name ?? 'Anonymous') }}
                                </h3>
                                <p class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y g:i A') }}</p>
                                @if($review->booking)
                                    <p class="text-xs text-gray-400 mt-1">
                                        Booking: {{ $review->booking->booking_date->format('M d, Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-sm text-gray-600">{{ $review->rating }}/5</span>
                        </div>
                    </div>
                    <p class="text-gray-700 leading-relaxed mb-3">{{ $review->comment }}</p>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span class="px-2 py-1 bg-gray-100 rounded">
                            {{ ucfirst($review->feedback_type) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-gray-500 text-lg">No approved feedback yet.</p>
                    <p class="text-gray-400 text-sm mt-2">Feedback will appear here once approved by admin.</p>
                </div>
            @endforelse

            @if($reviews->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
        </main>
    </div>
</div>
@endsection
