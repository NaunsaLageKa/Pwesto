@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <a href="{{ route('admin.disputes.index') }}" class="inline-block mb-4 text-blue-600 hover:underline">&larr; Back to Disputes</a>
    <h1 class="text-3xl font-bold mb-6">Dispute Details</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Dispute Information -->
        <div class="bg-white p-6 rounded-lg shadow border">
            <h2 class="text-xl font-semibold mb-4">Dispute Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full 
                        @if($dispute->type == 'payment') bg-blue-100 text-blue-800
                        @elseif($dispute->type == 'service') bg-green-100 text-green-800
                        @elseif($dispute->type == 'behavior') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($dispute->type) }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    @if($dispute->status == 'open')
                        <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Open</span>
                    @elseif($dispute->status == 'resolved')
                        <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Resolved</span>
                    @else
                        <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Escalated</span>
                    @endif
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $dispute->description }}</p>
                </div>
                
                @if($dispute->evidence)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Evidence</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $dispute->evidence }}</p>
                </div>
                @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $dispute->created_at->format('F j, Y g:i A') }}</p>
                </div>
                
                @if($dispute->resolved_at)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Resolved</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $dispute->resolved_at->format('F j, Y g:i A') }}</p>
                </div>
                @endif
                
                @if($dispute->escalated_at)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Escalated</label>
                    <p class="text-sm text-gray-900 mt-1">{{ $dispute->escalated_at->format('F j, Y g:i A') }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- User Information -->
        <div class="bg-white p-6 rounded-lg shadow border">
            <h2 class="text-xl font-semibold mb-4">User Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">User</label>
                    <div class="mt-1">
                        <p class="text-sm font-medium text-gray-900">{{ $dispute->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $dispute->user->email }}</p>
                        <p class="text-sm text-gray-500">Role: {{ ucfirst($dispute->user->role) }}</p>
                        <p class="text-sm text-gray-500">Status: {{ ucfirst($dispute->user->status) }}</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hub Owner</label>
                    <div class="mt-1">
                        <p class="text-sm font-medium text-gray-900">{{ $dispute->hubOwner->name }}</p>
                        <p class="text-sm text-gray-500">{{ $dispute->hubOwner->email }}</p>
                        <p class="text-sm text-gray-500">Role: {{ ucfirst($dispute->hubOwner->role) }}</p>
                        <p class="text-sm text-gray-500">Status: {{ ucfirst($dispute->hubOwner->status) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Resolution Form -->
    @if($dispute->status == 'open')
    <div class="bg-white p-6 rounded-lg shadow border mt-8">
        <h2 class="text-xl font-semibold mb-4">Resolve Dispute</h2>
        
        <form action="{{ route('admin.disputes.resolve', $dispute->id) }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="resolution" class="block text-sm font-medium text-gray-700">Resolution</label>
                    <textarea name="resolution" id="resolution" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your resolution details..." required></textarea>
                </div>
                
                <div>
                    <label for="action" class="block text-sm font-medium text-gray-700">Action</label>
                    <select name="action" id="action" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select an action</option>
                        <option value="warning">Send Warning</option>
                        <option value="suspension">Suspend User</option>
                        <option value="ban">Ban User</option>
                        <option value="refund">Process Refund</option>
                        <option value="no_action">No Action</option>
                    </select>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                        Resolve Dispute
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endif
    
    @if($dispute->status == 'resolved' && $dispute->resolution)
    <div class="bg-white p-6 rounded-lg shadow border mt-8">
        <h2 class="text-xl font-semibold mb-4">Resolution Details</h2>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Resolution</label>
                <p class="text-sm text-gray-900 mt-1">{{ $dispute->resolution }}</p>
            </div>
            
            @if($dispute->resolvedBy)
            <div>
                <label class="block text-sm font-medium text-gray-700">Resolved By</label>
                <p class="text-sm text-gray-900 mt-1">{{ $dispute->resolvedBy->name }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
