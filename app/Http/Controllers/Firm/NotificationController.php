<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function getNotifications(): JsonResponse
    {
        $notifications = Notification::where('firm_id', Auth::guard('firm')->id())
            // ->orderBy('read', 'asc')    
            ->orderBy('id', 'desc');    
        return response()->json($notifications->paginate(25));
    }

    public function markRead(Notification $notification): JsonResponse
    {
        $notification->where('firm_id', Auth::guard('firm')->id())->update(['read' => 1]);
        return response()->json([
            'status'  => 'success',
            'message' => 'notification read'
        ]);
    }
}
