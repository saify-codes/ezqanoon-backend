<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\CaseFillingDate;
use App\Models\CaseHearingDate;
use App\Models\Cases;
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
        // 1.  Pull the two date tables with the related case in one go
        $hearingDates = CaseHearingDate::with('caseRelation')
            ->where('lawyer_id', getLawyerId())
            ->whereBetween('date', [$start, $end])
            ->get();

        $filingDates  = CaseFillingDate::with('caseRelation')
            ->where('lawyer_id', getLawyerId())
            ->whereBetween('date', [$start, $end])
            ->get();

        // 2.  Build one events array for FullCalendar
        $events = [];

        /* ‑‑‑ filings ‑‑‑ */

        /* ‑‑‑ hearings ‑‑‑ */
        foreach ($hearingDates as $item) {
            $events[] = [
                'title'             => $item->description ?: 'Hearing',
                'start'             => $item->date,
                'backgroundColor'   => $this->getColorByUrgency($item->caseRelation->urgency),
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

    private function getColorByUrgency($urgency)
    {
        return match ($urgency) {
            'URGENT'    => 'var(--bs-danger)', // red
            'HIGH'      => 'var(--bs-primary)',// orange
            'MEDIUM'    => 'var(--bs-warning)',// yellow
            'LOW'       => 'var(--bs-info)',   // yellow
            default     => 'var(--bs-light)',  // blue
        };
    }
}
