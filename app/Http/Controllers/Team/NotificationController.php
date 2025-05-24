<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function getNotifications(): JsonResponse
    {
        $notifications = Notification::where('team_id', Auth::guard('team')->id())
            // ->orderBy('read', 'asc')    
            ->orderBy('id', 'desc');    
        return response()->json($notifications->paginate(25));
    }

    public function markRead(Notification $notification): JsonResponse
    {
        $notification->where('team_id', Auth::guard('team')->id())->update(['read' => 1]);
        return response()->json([
            'status'  => 'success',
            'message' => 'notification read'
        ]);
    }
}
