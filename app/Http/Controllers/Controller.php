<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FloorPlan;

abstract class Controller
{
    /**
     * Get hub owners for a specific company (case-insensitive)
     */
    protected function getHubOwnersForCompany($company)
    {
        return User::whereRaw('LOWER(company) LIKE ?', ['%' . strtolower($company) . '%'])
            ->where('role', 'hub_owner')
            ->pluck('id');
    }

    /**
     * Get active floor plan for a company
     */
    protected function getActiveFloorPlanForCompany($company)
    {
        return FloorPlan::whereHas('hubOwner', function($query) use ($company) {
            $query->whereRaw('LOWER(company) LIKE ?', ['%' . strtolower($company) . '%']);
        })
        ->where('is_active', true)
        ->whereNotNull('layout_data')
        ->orderBy('updated_at', 'desc')
        ->first();
    }

    /**
     * Get hub owner statistics
     */
    protected function getHubOwnerStats()
    {
        return [
            'total' => User::where('role', 'hub_owner')->count(),
            'pending' => User::where('role', 'hub_owner')->where('status', 'pending')->count(),
            'approved' => User::where('role', 'hub_owner')->where('status', 'approved')->count(),
        ];
    }

    /**
     * Standard success redirect
     */
    protected function successRedirect($message)
    {
        return redirect()->back()->with('success', $message);
    }

    /**
     * Standard error redirect
     */
    protected function errorRedirect($message)
    {
        return redirect()->back()->with('error', $message);
    }
}
