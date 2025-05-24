<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CalendarEvent;
use App\Models\CaseFillingDate;
use App\Models\CaseHearingDate;
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

            $query              = CalendarEvent::where('firm_id',  Auth::guard('firm')->id());
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

        return view('firm.calendar.index');
    }

    public function create()
    {
        return view('firm.calendar.create');
    }

    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'deadline'      => 'required|date',
            'description'   => 'required|string',
        ]);

        CalendarEvent::create([
            'firm_id'               =>  Auth::guard('firm')->id(),
            'deadline'              => $validated['deadline']  ,
            'description'           => $validated['description'],
        ]);

        return redirect()
            ->route('firm.calendar.index')
            ->with('success', 'Event created successfully!');
    }

    public function edit($id)
    {
        $calendarEvent = CalendarEvent::where('firm_id',  Auth::guard('firm')->id())->find($id);
        return view('firm.calendar.edit', compact('calendarEvent'));
    }

    public function update(Request $request, $id)
    {
        // Validate incoming data
        $validated = $request->validate([
            'deadline'      => 'required|date',
            'description'   => 'required|string',
        ]);

        $calendarEvent = CalendarEvent::where('firm_id',  Auth::guard('firm')->id())->findOrFail($id);
        $calendarEvent->update([
            'deadline'              => $validated['deadline'],
            'description'           => $validated['description'],
        ]);

        return redirect()
            ->route('firm.calendar.index')
            ->with('success', 'Event updated successfully!');
    }

    public function show(string $id)
    {
        $calendarEvent =  CalendarEvent::where('firm_id',  Auth::guard('firm')->id())->findOrFail($id);
        return view('firm.calendar.show', compact('calendarEvent'));
    }

   
    public function destroy(Request $request, string $id)
    {
        $calendarEvent = CalendarEvent::where('firm_id',  Auth::guard('firm')->id())->findOrFail($id);
        $calendarEvent->delete();

        if ($request->ajax()) {
            return $this->successResponse('event deleted');
        }

        // Return response or redirect as needed
        return redirect()->route('firm.calendar.index')->with('success', 'Event deleted successfully.');
    }

    public function events(Request $request)
    {

        $start      = $request->start;
        $end        = $request->end;
        $settings   = Auth::guard('firm')->user()->settings();

        $appointments = Appointment::with('user')
            ->where('firm_id',  Auth::guard('firm')->id())
            ->whereBetween('meeting_date', [$start, $end])
            ->get();

        $hearingDates = CaseHearingDate::with('caseRelation')
            ->where('firm_id',  Auth::guard('firm')->id())
            ->whereBetween('date', [$start, $end])
            ->get();

        $myEvents = CalendarEvent::where('firm_id',  Auth::guard('firm')->id())
            ->whereBetween('deadline', [$start, $end])
            ->get();

        $filingDates  = CaseFillingDate::with('caseRelation')
            ->where('firm_id',  Auth::guard('firm')->id())
            ->whereBetween('date', [$start, $end])
            ->get();

        $events = [];

        foreach ($appointments as $item) {
            $events[] = [
                'title'             => 'Appointment',
                'start'             => Carbon::parse($item->meeting_date)->format('Y-m-d'),
                'textColor'         => '#FFF',
                'backgroundColor'   => $settings?->calendar->appointment_event_color ?? "#95a5a6",
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
                'title'             => $item->caseRelation->name,
                'start'             => $item->date,
                'textColor'         => '#FFF',
                'backgroundColor'   => $settings?->calendar->hearing_event_color ?? "#95a5a6",
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
                'backgroundColor'   => $settings?->calendar->custom_event_color ?? "#95a5a6",
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

}
