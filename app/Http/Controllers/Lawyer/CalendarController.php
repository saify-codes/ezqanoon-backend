<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
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

    public function events()
    {
        $cases = Cases::where('lawyer_id', Auth::id())->get();
        $events = [];

        foreach ($cases as $case) {
            if (!empty($case->deadlines) && is_array($case->deadlines)) {
                foreach ($case->deadlines as $deadline) {
                    if (isset($deadline['date']) && isset($deadline['description'])) {
                        $events[] = [
                            'title' => $deadline['description'],
                            'start' => $deadline['date'],
                            'extendedProps' => [
                                'caseId' => $case->id,
                                'caseName' => $case->name
                            ],
                            'backgroundColor' => $this->getColorByUrgency($case->urgency),
                        ];
                    }
                }
            }
        }

        return response()->json($events);
    }

    private function getColorByUrgency($urgency)
    {
        return match ($urgency) {
            'CRITICAL'  => 'var(--bs-danger)', // red
            'HIGH'      => 'var(--bs-primary)',     // orange
            'MEDIUM'    => 'var(--bs-warning)',   // yellow
            default     => 'var(--bs-light)',    // blue
        };
    }
}
