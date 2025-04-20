<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Attachment;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    use ApiResponseTrait;

        
    public function getAppointments(){
        $user = Auth::user();
        $appointments = Appointment::with(['attachments', 'lawyer'])->where('user_id', $user->id)->get();
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

        // Create the appointment record
        $appointment = Appointment::create([
            'user_id'      => $user->id,
            'lawyer_id'    => $request->lawyer_id,
            'country'      => $request->country,
            'details'      => $request->details,
            'meeting_date' => $request->meeting_date,
        ]);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $attachmentPath = $file->store("users/{$user->id}/appointments", 'public');
                Attachment::create([
                    'appointment_id' => $appointment->id,
                    'file_path'      => $attachmentPath,
                    'original_name'  => $file->getClientOriginalName(),
                    'mime_type'      => $file->getClientMimeType(),
                ]);
            }
        }

        notifyLawyer($request->lawyer_id, 'New Appointment', "you have an appointemnt from $user->name");

        return $this->successResponse('Appointment created successfully', ['data' => $appointment], 201);
    }
}

