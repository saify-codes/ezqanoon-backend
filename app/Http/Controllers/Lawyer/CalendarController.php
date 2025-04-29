<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CaseFillingDate;
use App\Models\CaseHearingDate;
use App\Models\Cases;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('lawyer.calendar.index');
    }

    public function events(Request $request)
    {
        $start = $request->start;
        $end   = $request->end;

        $appointments = Appointment::with('user')
            ->where('lawyer_id', getLawyerId())
            ->whereBetween('meeting_date', [$start, $end])
            ->get();

        $hearingDates = CaseHearingDate::with('caseRelation')
            ->where('lawyer_id', getLawyerId())
            ->whereBetween('date', [$start, $end])
            ->get();

        $filingDates  = CaseFillingDate::with('caseRelation')
            ->where('lawyer_id', getLawyerId())
            ->whereBetween('date', [$start, $end])
            ->get();

        $events = [];

        foreach ($appointments as $item) {
            $events[] = [
                'title'             => 'Appointment',
                'start'             => Carbon::parse($item->meeting_date)->format('Y-m-d'),
                'textColor'         => '#FFF',
                'backgroundColor'   => 'var(--bs-warning)',
                'extendedProps' => [
                    'type'          => 'APPOINTMENT',
                    'appointmentId' => $item->id,
                    'name'          => $item->user->name,
                    'details'       => $item->details,
                    'meetingDate'   => $item->meeting_date,
                ],
            ];
        }
        
        foreach ($hearingDates as $item) {
            $events[] = [
                'title'             => $item->description ?: 'Hearing',
                'start'             => $item->date,
                'textColor'         => '#FFF',
                'backgroundColor'   => 'var(--bs-primary)',
                'extendedProps' => [
                    'type'          => 'HEARING',
                    'caseId'        => $item->case_id,
                    'hearingId'     => $item->id,
                    'caseName'      => $item->caseRelation->name,
                    'description'   => $item->description,
                    'date'          => $item->date,
                ],
            ];
        }


        return response()->json($events);
    }

    // private function getColorByUrgency($urgency)
    // {
    //     return match ($urgency) {
    //         'URGENT'    => 'var(--bs-danger)', // red
    //         'HIGH'      => 'var(--bs-primary)',// orange
    //         'MEDIUM'    => 'var(--bs-warning)',// yellow
    //         'LOW'       => 'var(--bs-info)',   // yellow
    //         default     => 'var(--bs-light)',  // blue
    //     };
    // }
}
