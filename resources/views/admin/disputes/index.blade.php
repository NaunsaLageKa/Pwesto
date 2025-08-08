@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <a href="{{ route('admin.dashboard') }}" class="inline-block mb-4 text-blue-600 hover:underline">&larr; Back to Dashboard</a>
    <h1 class="text-3xl font-bold mb-6">Dispute Resolution</h1>
    
    <!-- Filter Form -->
    <form method="GET" action="" class="flex flex-wrap gap-4 mb-6 items-center bg-white p-4 rounded-lg shadow">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            <option value="open" @if(request('status')=='open') selected @endif>Open</option>
            <option value="resolved" @if(request('status')=='resolved') selected @endif>Resolved</option>
            <option value="escalated" @if(request('status')=='escalated') selected @endif>Escalated</option>
        </select>
        <select name="type" class="border rounded px-3 py-2">
            <option value="">All Types</option>
            <option value="payment" @if(request('type')=='payment') selected @endif>Payment</option>
            <option value="service" @if(request('type')=='service') selected @endif>Service</option>
            <option value="behavior" @if(request('type')=='behavior') selected @endif>Behavior</option>
            <option value="other" @if(request('type')=='other') selected @endif>Other</option>
        </select>
        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition" type="submit">Filter</button>
        <a href="{{ route('admin.disputes.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">Clear</a>
    </form>

    <!-- Disputes Table -->
    <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">User</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Hub Owner</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Type</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Description</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Status</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Date</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse ($disputes as $dispute)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-2 px-4">
                        <div class="text-sm font-medium text-gray-900">{{ $dispute->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $dispute->user->email }}</div>
                    </td>
                    <td class="py-2 px-4">
                        <div class="text-sm font-medium text-gray-900">{{ $dispute->hubOwner->name }}</div>
                        <div class="text-sm text-gray-500">{{ $dispute->hubOwner->email }}</div>
                    </td>
                    <td class="py-2 px-4">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($dispute->type == 'payment') bg-blue-100 text-blue-800
                            @elseif($dispute->type == 'service') bg-green-100 text-green-800
                            @elseif($dispute->type == 'behavior') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($dispute->type) }}
                        </span>
                    </td>
                    <td class="py-2 px-4">
                        <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $dispute->description }}">
                            {{ $dispute->description }}
                        </div>
                    </td>
                    <td class="py-2 px-4">
                        @if($dispute->status == 'open')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Open</span>
                        @elseif($dispute->status == 'resolved')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Resolved</span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Escalated</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-sm text-gray-500">
                        {{ $dispute->created_at->diffForHumans() }}
                    </td>
                    <td class="py-2 px-4">
                        <a href="{{ route('admin.disputes.show', $dispute->id) }}" class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700 transition">View</a>
                        @if($dispute->status == 'open')
                            <form action="{{ route('admin.disputes.escalate', $dispute->id) }}" method="POST" class="inline ml-2">
                                @csrf
                                <button class="bg-orange-600 text-white px-2 py-1 rounded text-xs hover:bg-orange-700 transition" type="submit">Escalated</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-4 text-center text-gray-500">No disputes found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6">
            {{ $disputes->links() }}
        </div>
    </div>
</div>
@endsection
