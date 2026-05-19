@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <a href="{{ route('admin.dashboard') }}" class="inline-block mb-4 text-blue-600 hover:underline">&larr; Back to Dashboard</a>
    <h1 class="text-3xl font-bold mb-2">Review Moderation</h1>
    <p class="text-sm text-gray-600 mb-6">Approved workspace reviews appear here and on the public home page. <strong>Trash:</strong> for approved workspace rows, removes them from this admin list and from the public home; hub owners still see them until they remove them from their dashboard. Other types are soft-deleted from moderation.</p>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    @php
        $reviewSortBy = $sortBy ?? '';
        $reviewSortDir = $sortDir ?? 'desc';
        $reviewSortUrl = function (string $column) use ($reviewSortBy, $reviewSortDir) {
            $nextDir = ($reviewSortBy === $column && $reviewSortDir === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(array_merge(request()->except('page'), [
                'sort' => $column,
                'dir' => $nextDir,
            ]));
        };
        $reviewSortIcon = function (string $column) use ($reviewSortBy, $reviewSortDir) {
            if ($reviewSortBy !== $column) {
                return '↕';
            }
            return $reviewSortDir === 'asc' ? '↑' : '↓';
        };
    @endphp
    
    <!-- Dashboard Stats -->
    @if(isset($stats))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow border">
            <p class="text-sm font-medium text-gray-600">Pending Reviews</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_count'] }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow border">
            <p class="text-sm font-medium text-gray-600">High Priority</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['high_priority_count'] }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow border">
            <p class="text-sm font-medium text-gray-600">Avg Rating</p>
            <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['average_rating'] ?? 0, 1) }}</p>
        </div>
    </div>
    @endif
    
    <!-- Filter Form -->
    <form method="GET" action="" class="flex flex-wrap gap-4 mb-6 items-center bg-white p-4 rounded-lg shadow">
        @if($reviewSortBy)
            <input type="hidden" name="sort" value="{{ $reviewSortBy }}">
            <input type="hidden" name="dir" value="{{ $reviewSortDir }}">
        @endif
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search reviews..." class="border rounded px-3 py-2 flex-1 min-w-[200px]" />
        <select name="status" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            <option value="deleted" @if(request('status')=='deleted') selected @endif>Deleted</option>
        </select>
        <select name="priority" class="border rounded px-3 py-2">
            <option value="">All Priorities</option>
            <option value="1" @if(request('priority')=='1') selected @endif>High Priority</option>
            <option value="0" @if(request('priority')=='0') selected @endif>Normal</option>
        </select>
        <select name="feedback_type" class="border rounded px-3 py-2">
            <option value="">All types</option>
            <option value="workspace" @if(request('feedback_type')=='workspace') selected @endif>Workspace</option>
            <option value="platform" @if(request('feedback_type')=='platform') selected @endif>Platform</option>
        </select>
        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition" type="submit">Filter</button>
        <a href="{{ route('admin.reviews.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">Clear</a>
    </form>

    <!-- Reviews Table -->
    <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach(['user' => 'User', 'hub_owner' => 'Hub Owner', 'rating' => 'Rating'] as $column => $label)
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">
                        <a href="{{ $reviewSortUrl($column) }}" class="inline-flex items-center gap-1 hover:text-blue-600 {{ $reviewSortBy === $column ? 'text-blue-600' : '' }}">
                            {{ $label }}
                            <span class="text-xs opacity-70" aria-hidden="true">{{ $reviewSortIcon($column) }}</span>
                        </a>
                    </th>
                    @endforeach
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Comment</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">
                        <a href="{{ $reviewSortUrl('status') }}" class="inline-flex items-center gap-1 hover:text-blue-600 {{ $reviewSortBy === 'status' ? 'text-blue-600' : '' }}">
                            Status
                            <span class="text-xs opacity-70" aria-hidden="true">{{ $reviewSortIcon('status') }}</span>
                        </a>
                    </th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Time ago</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">
                        <a href="{{ $reviewSortUrl('created_at') }}" class="inline-flex items-center gap-1 hover:text-blue-600 {{ $reviewSortBy === 'created_at' ? 'text-blue-600' : '' }}">
                            Date
                            <span class="text-xs opacity-70" aria-hidden="true">{{ $reviewSortIcon('created_at') }}</span>
                        </a>
                    </th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100" id="reviews-tbody">
                @forelse ($reviews as $review)
                <tr class="hover:bg-gray-50 transition {{ $review->is_flagged ? 'bg-red-50' : '' }} {{ $review->isHighPriority() ? 'border-l-4 border-orange-500' : '' }}">
                    <td class="py-2 px-4">
                        <div class="text-sm font-medium text-gray-900">{{ $review->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $review->user->email }}</div>
                    </td>
                    <td class="py-2 px-4">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $review->hubOwner->company ?? $review->hubOwner->name }}
                        </div>
                        <div class="text-sm text-gray-500">{{ $review->hubOwner->name }}</div>
                        @if($review->booking)
                            <div class="text-xs text-gray-400 mt-1">Booking: {{ $review->booking->booking_date->format('M d, Y') }}</div>
                        @endif
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
                        <div class="text-sm text-gray-900 max-w-xs">
                            <div class="truncate" title="{{ $review->comment }}">
                                {{ Str::limit($review->comment, 100) }}
                            </div>
                            @if($review->is_flagged)
                                <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800 mt-1">⚠️ Flagged</span>
                            @endif
                            @if($review->isHighPriority())
                                <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 mt-1">High Priority</span>
                            @endif
                            @if($review->rating <= 2)
                                <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 mt-1">Low Rating</span>
                            @endif
                        </div>
                    </td>
                    <td class="py-2 px-4">
                        @if($review->trashed() || (!empty($review->admin_archived_at)))
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-800">Deleted</span>
                            @if($review->trashed() && $review->deleted_at)
                                <div class="text-xs text-gray-500 mt-1">Removed {{ $review->deleted_at->diffForHumans() }}</div>
                            @elseif(!empty($review->admin_archived_at))
                                <div class="text-xs text-gray-500 mt-1">Removed from admin {{ $review->admin_archived_at->diffForHumans() }}</div>
                            @endif
                        @elseif($review->status == 'pending')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($review->status == 'approved')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                            @if($review->approved_by)
                                <div class="text-xs text-gray-500 mt-1">by {{ $review->approvedBy->name ?? 'Admin' }}</div>
                            @endif
                            @if($review->approved_at)
                                <div class="text-xs text-gray-400 mt-1">{{ $review->approved_at->format('M d, Y') }}</div>
                            @endif
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                            @if($review->rejected_by)
                                <div class="text-xs text-gray-500 mt-1">by {{ $review->rejectedBy->name ?? 'Admin' }}</div>
                            @endif
                            @if($review->rejected_at)
                                <div class="text-xs text-gray-400 mt-1">{{ $review->rejected_at->format('M d, Y') }}</div>
                            @endif
                        @endif
                        @if($review->moderation_notes)
                            <button onclick="showModerationHistory(@json($review->id), @json(json_decode($review->moderation_notes, true)))" class="text-xs text-blue-600 hover:underline mt-1">View History</button>
                        @endif
                        <div class="text-xs text-gray-500 mt-1">Type: {{ ucfirst($review->feedback_type) }}</div>
                    </td>
                    <td class="py-2 px-4 text-sm text-gray-600">
                        {{ $review->created_at->diffForHumans() }}
                    </td>
                    <td class="py-2 px-4 text-sm text-gray-600">
                        {{ $review->created_at->format('M d, Y') }}
                    </td>
                    <td class="py-2 px-4">
                        <div class="flex flex-col gap-1">
                            @if($review->trashed() || !empty($review->admin_archived_at))
                                <span class="text-xs text-gray-500">—</span>
                            @elseif($review->status == 'pending')
                                <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700 transition w-full" type="submit">Approve</button>
                                </form>
                                <form action="{{ route('admin.reviews.reject', $review->id) }}" method="POST" class="inline" id="reject-form-{{ $review->id }}">
                                    @csrf
                                    <button class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700 transition w-full" type="button" onclick="showRejectModal({{ $review->id }})">Reject</button>
                                </form>
                            @else
                                <form action="{{ route('admin.reviews.delete', $review->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center rounded-md p-2 text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                        title="Approved workspace: remove from this list and public home (hub dashboard unchanged). Other types: delete."
                                        aria-label="Remove review from admin list or delete"
                                        onclick="return confirm('ARE YOU SURE YOU WANT TO DELETE?')"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-4 text-center text-gray-500">No reviews found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    </div>
</div>

<!-- Moderation History Modal -->
<div id="history-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[80vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Moderation History</h3>
                <button onclick="hideHistoryModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="history-content" class="space-y-3">
                <!-- History items will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-96 mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Reject Review</h3>
            <form id="reject-modal-form" method="POST">
                @csrf
                <textarea name="moderation_notes" placeholder="Reason for rejection (optional)" class="w-full border rounded px-3 py-2 mb-4" rows="3"></textarea>
                <div class="flex gap-2">
                    <button type="button" onclick="hideRejectModal()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                    <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Reject Modal
function showRejectModal(reviewId) {
    const form = document.getElementById('reject-modal-form');
    const originalForm = document.getElementById('reject-form-' + reviewId);
    form.action = originalForm.action;
    document.getElementById('reject-modal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
    document.getElementById('reject-modal-form').reset();
}

// Moderation History
function showModerationHistory(reviewId, notes) {
    const historyContent = document.getElementById('history-content');
    
    if (notes && Array.isArray(notes) && notes.length > 0) {
        historyContent.innerHTML = notes.map(note => {
            const actionClass = note.action === 'approved' ? 'bg-green-100 text-green-800'
                : note.action === 'rejected' ? 'bg-red-100 text-red-800'
                : (note.action === 'published_public' || note.action === 'removed_from_public_home') ? 'bg-slate-100 text-slate-800'
                : 'bg-gray-100 text-gray-700';
            return `
                <div class="border-l-4 border-blue-500 pl-4 py-2 bg-gray-50 rounded mb-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-semibold text-gray-900">${note.action.charAt(0).toUpperCase() + note.action.slice(1)}</div>
                            <div class="text-sm text-gray-600">by ${note.admin_name || 'Admin'}</div>
                            <div class="text-xs text-gray-500">${note.timestamp || 'N/A'}</div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded ${actionClass}">
                            ${note.action}
                        </span>
                    </div>
                    ${note.notes ? `<div class="mt-2 text-sm text-gray-700">${note.notes}</div>` : ''}
                </div>
            `;
        }).join('');
    } else {
        historyContent.innerHTML = '<p class="text-gray-500">No moderation history available.</p>';
    }
    
    document.getElementById('history-modal').classList.remove('hidden');
}

function hideHistoryModal() {
    document.getElementById('history-modal').classList.add('hidden');
}
</script>
@endsection