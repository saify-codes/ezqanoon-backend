<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentAttachment;
use App\Models\Firm;
use App\Models\Lawyer;
use App\Services\ZoomService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AppointmentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private ZoomService $zoom) {}

    /**
     * Return all appointments that belong to the logged-in user.
     */
    public function getAppointments()
    {
        $appointments = Appointment::with([
                'attachments',
                'lawyer:id,name',
                'firm:id,name',
            ])
            ->where('user_id', Auth::id())
            ->latest('meeting_date')
            ->get();

        return $this->successResponse('appointments', ['data' => $appointments]);
    }

    public function createAppointmentFirm(Request $request)
    {

        // ──────────────────────────
        // 1. Validation rules
        // ──────────────────────────
        $request->validate([
            'details'      => 'required|string',
            'date_time'    => 'required|date_format:Y-m-d H:i',
            'country'      => 'required|string',
            'firm_id'      => 'required|exists:firms,id',
            'attachment.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5000',
        ]);

        $user = Auth::user();

        // ──────────────────────────
        // 2. Create Zoom meeting + DB records in a transaction
        // ──────────────────────────
        DB::beginTransaction();

        try {
            // create the Zoom meeting (subject doesn’t matter much – use either entity name if it exists)
            $firm        = Firm::find($request->firm_id);
            $zoomMeeting = $this->zoom->createMeeting("Consultation with $firm->name", $request->date_time);
            $appointment = Appointment::create([
                'user_id'           => $user->id,
                'firm_id'           => $request->firm_id,
                'country'           => $request->country,
                'details'           => $request->details,
                'meeting_date'      => $request->date_time,
                'meeting_link'      => $zoomMeeting['start_url'], // host link
                'meeting_link_user' => $zoomMeeting['join_url'], // attendee link
            ]);

            // store any attachments
            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
                    $path = $file->store("appointments/{$appointment->id}", 'public');

                    AppointmentAttachment::create([
                        'appointment_id' => $appointment->id,
                        'file'           => basename($path),
                        'original_name'  => $file->getClientOriginalName(),
                        'mime_type'      => $file->getClientMimeType(),
                    ]);
                }
            }

            // ──────────────────────────
            // 3. Notify firm
            // ──────────────────────────
            notifyFirm($request->firm_id, 'New Appointment', "You have a new appointment from {$user->name}");
            
            DB::commit();
            
            return $this->successResponse('Appointment created successfully',['data' => $appointment],201);

        } catch (Exception $e) {
            DB::rollBack();

            Log::channel('zoom')->error('Zoom createMeeting failed', [
                'firm_id'   => $request->firm_id,
                'user_id'   => $user->id,
                'error'     => $e->getMessage(),
            ]);

            return $this->errorResponse('Appointment not created', 500, ['error' => $e->getMessage()]);
        }
    }
    
    public function createAppointmentLawyer(Request $request)
    {

        // ──────────────────────────
        // 1. Validation rules
        // ──────────────────────────
        $request->validate([
            'details'      => 'required|string',
            'date_time'    => 'required|date_format:Y-m-d H:i',
            'country'      => 'required|string',
            'lawyer_id'    => 'required|exists:lawyers,id',
            'attachment.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5000',
        ]);

        $user = Auth::user();

        // ──────────────────────────
        // 2. Create Zoom meeting + DB records in a transaction
        // ──────────────────────────
        DB::beginTransaction();

        try {
            // create the Zoom meeting (subject doesn’t matter much – use either entity name if it exists)
            $lawyer      = Lawyer::find($request->lawyer_id);
            $zoomMeeting = $this->zoom->createMeeting("Consultation with $lawyer->name", $request->date_time);
            $appointment = Appointment::create([
                'user_id'           => $user->id,
                'lawyer_id'         => $request->lawyer_id,
                'country'           => $request->country,
                'details'           => $request->details,
                'meeting_date'      => $request->date_time,
                'meeting_link'      => $zoomMeeting['start_url'], // host link
                'meeting_link_user' => $zoomMeeting['join_url'], // attendee link
            ]);

            // store any attachments
            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
                    $path = $file->store("appointments/{$appointment->id}", 'public');

                    AppointmentAttachment::create([
                        'appointment_id' => $appointment->id,
                        'file'           => basename($path),
                        'original_name'  => $file->getClientOriginalName(),
                        'mime_type'      => $file->getClientMimeType(),
                    ]);
                }
            }

            // ──────────────────────────
            // 3. Notify lawyer
            // ──────────────────────────
            notifyLawyer($request->lawyer_id, 'New Appointment', "You have a new appointment from {$user->name}");
            
            DB::commit();
            
            return $this->successResponse('Appointment created successfully',['data' => $appointment],201);

        } catch (Exception $e) {
            DB::rollBack();

            Log::channel('zoom')->error('Zoom createMeeting failed', [
                'lawyer_id' => $request->lawyer_id,
                'user_id'   => $user->id,
                'error'     => $e->getMessage(),
            ]);

            return $this->errorResponse('Appointment not created', 500, ['error' => $e->getMessage()]);
        }
    }
}
