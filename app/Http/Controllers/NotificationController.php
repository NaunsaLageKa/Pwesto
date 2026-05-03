<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function read(string $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        if ($notification->unread()) {
            $notification->markAsRead();
        }
        $data = $notification->data;
        $url = is_array($data) && ! empty($data['url']) ? $data['url'] : route('booking-history');

        return redirect($url);
    }

    public function readAll(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back();
    }
}
