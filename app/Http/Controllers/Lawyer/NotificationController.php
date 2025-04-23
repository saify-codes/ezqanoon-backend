<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\LawyerNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function getNotifications(): JsonResponse
    {
        $notifications = LawyerNotification::where('lawyer_id', Auth::user()->id)
            // ->orderBy('read', 'asc')    
            ->orderBy('id', 'desc');    
        return response()->json($notifications->paginate(25));
    }

    public function markRead(LawyerNotification $notification): JsonResponse
    {
        $notification->update(['read' => 1]);
        return response()->json([
            'status'  => 'success',
            'message' => 'notification read'
        ]);
    }
}
