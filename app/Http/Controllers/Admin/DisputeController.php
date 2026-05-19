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
        $query = Dispute::with('user', 'hubOwner', 'booking', 'createdBy');
        
        if ($request->filled('status')) {
            $query->where('disputes.status', $request->input('status'));
        }
        
        if ($request->filled('type')) {
            $query->where('disputes.type', $request->input('type'));
        }

        $allowedSorts = ['user', 'hub_owner', 'type', 'status', 'created_at'];
        $sortBy = $request->input('sort', 'created_at');
        $sortDir = strtolower((string) $request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (! in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'created_at';
            $sortDir = 'desc';
        }

        $this->applyDisputeSort($query, $sortBy, $sortDir);

        $disputes = $query->paginate(15)->withQueryString();

        return view('admin.disputes.index', compact('disputes', 'sortBy', 'sortDir'));
    }

    private function applyDisputeSort($query, string $sortBy, string $sortDir): void
    {
        match ($sortBy) {
            'user' => $query->leftJoin('users as dispute_users', 'disputes.user_id', '=', 'dispute_users.id')
                ->orderBy('dispute_users.name', $sortDir)
                ->select('disputes.*'),
            'hub_owner' => $query->leftJoin('users as dispute_hub_owners', 'disputes.hub_owner_id', '=', 'dispute_hub_owners.id')
                ->orderBy('dispute_hub_owners.name', $sortDir)
                ->select('disputes.*'),
            'type' => $query->orderBy('disputes.type', $sortDir),
            'status' => $query->orderBy('disputes.status', $sortDir),
            default => $query->orderBy('disputes.created_at', $sortDir),
        };
    }

    public function show($id)
    {
        $dispute = Dispute::with('user', 'hubOwner', 'booking', 'createdBy')->findOrFail($id);
        return view('admin.disputes.show', compact('dispute'));
    }

    public function resolve(Request $request, $id)
    {
        $request->validate([
            'resolution' => 'required|string',
            'action' => 'required|in:warning,suspension,ban,refund,no_action'
        ]);

        $dispute = Dispute::with(['user', 'hubOwner'])->findOrFail($id);

        if (! in_array($dispute->status, ['open', 'escalated'], true)) {
            return redirect()->back()->with('error', 'Only open or flagged disputes can be resolved.');
        }

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

        if ($dispute->status !== 'open') {
            return redirect()->back()->with('error', 'Only open disputes can be flagged.');
        }

        $dispute->status = 'escalated';
        $dispute->escalated_at = now();
        $dispute->save();

        $this->notifyDisputeEscalated($dispute);

        return redirect()->back()->with('success', 'Dispute flagged for senior review.');
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
            'title' => Dispute::summaryTitle($request->type, $request->description),
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
     * Notify both parties that the dispute has been flagged.
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
