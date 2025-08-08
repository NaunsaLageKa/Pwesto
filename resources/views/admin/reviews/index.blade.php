@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <a href="{{ route('admin.dashboard') }}" class="inline-block mb-4 text-blue-600 hover:underline">&larr; Back to Dashboard</a>
    <h1 class="text-3xl font-bold mb-6">Review Moderation</h1>
    
    <!-- Filter Form -->
    <form method="GET" action="" class="flex flex-wrap gap-4 mb-6 items-center bg-white p-4 rounded-lg shadow">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search reviews..." class="border rounded px-3 py-2 flex-1 min-w-[200px]" />
        <select name="status" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            <option value="pending" @if(request('status')=='pending') selected @endif>Pending</option>
            <option value="approved" @if(request('status')=='approved') selected @endif>Approved</option>
            <option value="rejected" @if(request('status')=='rejected') selected @endif>Rejected</option>
        </select>
        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition" type="submit">Filter</button>
        <a href="{{ route('admin.reviews.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">Clear</a>
    </form>

    <!-- Reviews Table -->
    <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">User</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Hub Owner</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Rating</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Comment</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Status</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Date</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse ($reviews as $review)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-2 px-4">
                        <div class="text-sm font-medium text-gray-900">{{ $review->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $review->user->email }}</div>
                    </td>
                    <td class="py-2 px-4">
                        <div class="text-sm font-medium text-gray-900">{{ $review->hubOwner->name }}</div>
                        <div class="text-sm text-gray-500">{{ $review->hubOwner->email }}</div>
                    </td>
                    <td class="py-2 px-4">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="ml-1 text-sm text-gray-600">({{ $review->rating }}/5)</span>
                        </div>
                    </td>
                    <td class="py-2 px-4">
                        <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $review->comment }}">
                            {{ $review->comment }}
                        </div>
                    </td>
                    <td class="py-2 px-4">
                        @if($review->status == 'pending')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($review->status == 'approved')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-sm text-gray-500">
                        {{ $review->created_at->diffForHumans() }}
                    </td>
                    <td class="py-2 px-4">
                        @if($review->status == 'pending')
                            <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="inline">
                                @csrf
                                <button class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700 transition" type="submit">Approve</button>
                            </form>
                            <form action="{{ route('admin.reviews.reject', $review->id) }}" method="POST" class="inline ml-2">
                                @csrf
                                <button class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700 transition" type="submit">Reject</button>
                            </form>
                        @else
                            <form action="{{ route('admin.reviews.delete', $review->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="bg-gray-600 text-white px-2 py-1 rounded text-xs hover:bg-gray-700 transition" type="submit" onclick="return confirm('Are you sure you want to delete this review?')">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-4 text-center text-gray-500">No reviews found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    </div>
</div>
@endsection
