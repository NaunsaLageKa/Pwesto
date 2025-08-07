<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Check if the current user is an admin.
     */
    protected function isAdmin()
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get hub owners with optional status, search, and sort.
     */
    protected function getHubOwners($status = null, $search = null, $sort = 'created_at', $direction = 'desc', $perPage = 15)
    {
        $query = \App\Models\User::where('role', 'hub_owner');
        if ($status) {
            $query->where('status', $status);
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }
        return $query->orderBy($sort, $direction)->paginate($perPage)->withQueryString();
    }
}
