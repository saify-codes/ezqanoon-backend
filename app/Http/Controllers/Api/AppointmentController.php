<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentAttachment;
use App\Services\ZoomService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private ZoomService $zoom) {
    }

        
    public function getAppointments(){
        $appointments = Appointment::with(['attachments', 'lawyer:id,name'])
                        ->select('*')
                        ->where('user_id', Auth::id())
                        ->get();
        return $this->successResponse('appointments', ['data' => $appointments]);
    }
    
    /**
     * makeAppointment
     *
     * @param  mixed $request
     * @return void
     */
    public function makeAppointment(Request $request)
    {
        $request->validate([
            'details'      => 'required|string',
            'meeting_date' => 'required|date',
            'lawyer_id'    => 'required|exists:lawyers,id',
            'country'      => 'required|string',
            'attachment.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5000' // Allow optional file upload
        ]);

        $user = Auth::user();

        
        try {
            // create meeting
            $data = $this->zoom->createMeeting("consultant with advocate", $request->meeting_date);
            
            // Create the appointment record
            $appointment = Appointment::create([
                'user_id'               => $user->id,
                'lawyer_id'             => $request->lawyer_id,
                'country'               => $request->country,
                'details'               => $request->details,
                'meeting_date'          => $request->meeting_date,
                'meeting_link_lawyer'   => $data['start_url'],
                'meeting_link_user'     => $data['join_url'],
            ]);
    
            // Handle file upload
            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
                    $path       = $file->store("appointments/{$appointment->id}", 'public');
                    $fileName   = basename($path);
                    AppointmentAttachment::create([
                        'appointment_id' => $appointment->id,
                        'file'           => $fileName,
                        'original_name'  => $file->getClientOriginalName(),
                        'mime_type'      => $file->getClientMimeType(),
                    ]);
                }
            }

            notifyLawyer($request->lawyer_id, 'New Appointment', "you have an appointemnt from $user->name");

            return $this->successResponse('Appointment created successfully', ['data' => $appointment], 201);

            
        } catch (Exception $e) {

            Log::channel('zoom')->error('Zoom create meeting failed', [
                'lawyer_id' => $request->lawyer_id,
                'user_id'   => $user->id,
                'error'     => $e->getMessage(),
            ]);
        }

        return $this->errorResponse('Appointment not created', 500);
    }
}

