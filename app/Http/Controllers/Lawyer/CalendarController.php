<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CalendarEvent;
use App\Models\CaseFillingDate;
use App\Models\CaseHearingDate;
use App\Models\Cases;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    use ApiResponseTrait;
    /**
     * index
     *
     * @return void
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // DataTables columns to map for ordering:
            $columns            = ['id', 'description', 'deadline', 'created_at'];
            $draw               = $request->input('draw');
            $start              = $request->input('start');        // skip
            $length             = $request->input('length');       // rows per page
            $searchValue        = $request->input('search.value');  // global search box
            $orderColumnIndex   = $request->input('order.0.column'); // which column index is being sorted
            $orderDirection     = $request->input('order.0.dir');    // asc or desc

            $query              = CalendarEvent::where('lawyer_id', getLawyerId());
            $totalRecords       = $query->count();

            if (!empty($searchValue)) {
                $query->where('description', 'like', "%{$searchValue}%");
            }

            $totalRecordsFiltered = $query->count();

            if (isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDirection);
            } else {
                $query->orderBy('id', 'asc');
            }

            $data = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw'            => intval($draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $totalRecordsFiltered,
                'data'            => $data,
            ]);
        }

        return view('lawyer.calendar.index');
    }

    public function create()
    {
        return view('lawyer.calendar.create');
    }

    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'deadline'      => 'required|date',
            'description'   => 'required|string',
        ]);

        CalendarEvent::create([
            'lawyer_id'             => getLawyerId(),
            'deadline'              => $validated['deadline']  ,
            'description'           => $validated['description'],
        ]);

        return redirect()
            ->route('lawyer.calendar.index')
            ->with('success', 'Event created successfully!');
    }

    public function edit($id)
    {
        $calendarEvent = CalendarEvent::where('lawyer_id', getLawyerId())->find($id);
        return view('lawyer.calendar.edit', compact('calendarEvent'));
    }

    public function update(Request $request, $id)
    {
        // Validate incoming data
        $validated = $request->validate([
            'deadline'      => 'required|date',
            'description'   => 'required|string',
        ]);

        $calendarEvent = CalendarEvent::where('lawyer_id', getLawyerId())->findOrFail($id);
        $calendarEvent->update([
            'deadline'              => $validated['deadline'],
            'description'           => $validated['description'],
        ]);

        return redirect()
            ->route('lawyer.calendar.index')
            ->with('success', 'Event updated successfully!');
    }

    public function show(string $id)
    {
        $calendarEvent =  CalendarEvent::where('lawyer_id', getLawyerId())->findOrFail($id);
        return view('lawyer.calendar.show', compact('calendarEvent'));
    }

   
    public function destroy(Request $request, string $id)
    {
        $calendarEvent = CalendarEvent::where('lawyer_id', getLawyerId())->findOrFail($id);
        $calendarEvent->delete();

        if ($request->ajax()) {
            return $this->successResponse('event deleted');
        }

        // Return response or redirect as needed
        return redirect()->route('lawyer.calendar.index')->with('success', 'Event deleted successfully.');
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

        $myEvents = CalendarEvent::where('lawyer_id', getLawyerId())
            ->whereBetween('deadline', [$start, $end])
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
        
        foreach ($myEvents as $item) {
            $events[] = [
                'title'             => $item->description,
                'start'             => $item->deadline,
                'textColor'         => '#FFF',
                'backgroundColor'   => 'var(--bs-success)',
                'extendedProps' => [
                    'type'          => 'MYEVENT',
                    'eventId'       => $item->id,
                    'description'   => $item->description,
                    'date'          => $item->deadline,
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
