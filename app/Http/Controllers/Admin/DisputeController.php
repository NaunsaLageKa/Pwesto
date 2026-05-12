<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispute;
use App\Models\User;
use App\Models\Booking;
use App\Notifications\DisputeResolvedNotification;
use App\Notifications\DisputeEscalatedNotification;

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

        $dispute = Dispute::with(['user', 'hubOwner'])->findOrFail($id);
        $dispute->status = 'resolved';
        $dispute->resolution = $request->resolution;
        $dispute->resolved_by = auth()->id();
        $dispute->resolved_at = now();
        $dispute->save();

        // Take action based on resolution
        switch ($request->action) {
            case 'suspension':
                $user = $dispute->user;
                if ($user) {
                    $user->status = 'suspended';
                    $user->save();
                }
                break;
            case 'ban':
                $user = $dispute->user;
                if ($user) {
                    $user->status = 'banned';
                    $user->save();
                }
                break;
            // notification below is what informs the affected user.
        }

        $this->notifyDisputeResolved($dispute, $request->action, $request->resolution);

        return redirect()->route('admin.disputes.index')->with('success', 'Dispute resolved successfully.');
    }

    public function escalate($id)
    {
        $dispute = Dispute::with(['user', 'hubOwner'])->findOrFail($id);
        $dispute->status = 'escalated';
        $dispute->escalated_at = now();
        $dispute->save();

        $this->notifyDisputeEscalated($dispute);

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


    private function notifyDisputeResolved(Dispute $dispute, string $action, ?string $resolution): void
    {
        $customer = $dispute->user;
        $hubOwner = $dispute->hubOwner;

        if ($customer) {
            $role = $dispute->created_by === $customer->id ? 'reporter' : 'reported';
            $customer->notify(new DisputeResolvedNotification($dispute, $role, $action, $resolution));
        }

        if ($hubOwner) {
            $role = $dispute->created_by === $hubOwner->id ? 'reporter' : 'reported';
            $hubOwner->notify(new DisputeResolvedNotification($dispute, $role, $action, $resolution));
        }
    }

    /**
     * Notify both parties that the dispute has been escalated.
     */
    private function notifyDisputeEscalated(Dispute $dispute): void
    {
        $customer = $dispute->user;
        $hubOwner = $dispute->hubOwner;

        if ($customer) {
            $role = $dispute->created_by === $customer->id ? 'reporter' : 'reported';
            $customer->notify(new DisputeEscalatedNotification($dispute, $role));
        }

        if ($hubOwner) {
            $role = $dispute->created_by === $hubOwner->id ? 'reporter' : 'reported';
            $hubOwner->notify(new DisputeEscalatedNotification($dispute, $role));
        }
    }
}
