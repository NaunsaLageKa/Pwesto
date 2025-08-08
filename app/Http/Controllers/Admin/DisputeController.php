<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispute;
use App\Models\User;
use App\Models\Booking;

class DisputeController extends Controller
{
    public function index(Request $request)
    {
        $query = Dispute::with('user', 'hubOwner', 'booking');
        
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        
        $disputes = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        return view('admin.disputes.index', compact('disputes'));
    }

    public function show($id)
    {
        $dispute = Dispute::with('user', 'hubOwner', 'booking')->findOrFail($id);
        return view('admin.disputes.show', compact('dispute'));
    }

    public function resolve(Request $request, $id)
    {
        $request->validate([
            'resolution' => 'required|string',
            'action' => 'required|in:warning,suspension,ban,refund,no_action'
        ]);

        $dispute = Dispute::findOrFail($id);
        $dispute->status = 'resolved';
        $dispute->resolution = $request->resolution;
        $dispute->resolved_by = auth()->id();
        $dispute->resolved_at = now();
        $dispute->save();

        // Take action based on resolution
        switch ($request->action) {
            case 'warning':
                // Send warning email
                break;
            case 'suspension':
                $user = $dispute->user;
                $user->status = 'suspended';
                $user->save();
                break;
            case 'ban':
                $user = $dispute->user;
                $user->status = 'banned';
                $user->save();
                break;
            case 'refund':
                // Process refund logic
                break;
        }

        return redirect()->route('admin.disputes.index')->with('success', 'Dispute resolved successfully.');
    }

    public function escalate($id)
    {
        $dispute = Dispute::findOrFail($id);
        $dispute->status = 'escalated';
        $dispute->escalated_at = now();
        $dispute->save();

        return redirect()->back()->with('success', 'Dispute escalated to senior management.');
    }

    public function create(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'hub_owner_id' => 'required|exists:users,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'type' => 'required|in:payment,service,behavior,other',
            'description' => 'required|string',
            'evidence' => 'nullable|string'
        ]);

        Dispute::create([
            'user_id' => $request->user_id,
            'hub_owner_id' => $request->hub_owner_id,
            'booking_id' => $request->booking_id,
            'type' => $request->type,
            'description' => $request->description,
            'evidence' => $request->evidence,
            'status' => 'open',
            'created_by' => auth()->id()
        ]);

        return redirect()->route('admin.disputes.index')->with('success', 'Dispute created successfully.');
    }
}
