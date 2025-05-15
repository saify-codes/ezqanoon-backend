<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\CaseFillingDate;
use App\Models\CaseHearingDate;
use App\Models\Cases;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CasesController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // DataTables columns to map for ordering:
            $columns            = ['id', 'name', 'type', 'urgency', 'status', 'payment_status', 'hearings', 'created_at'];
            $draw               = $request->input('draw');
            $start              = $request->input('start');        // skip
            $length             = $request->input('length');       // rows per page
            $searchValue        = $request->input('search.value');  // global search box
            $orderColumnIndex   = $request->input('order.0.column'); // which column index is being sorted
            $orderDirection     = $request->input('order.0.dir');    // asc or desc

            $query              = Cases::with('hearings')->where('lawyer_id', getLawyerId());
            $totalRecords       = $query->count();

            if (!empty($searchValue)) {
                $query->where('name', 'like', "%{$searchValue}%")
                      ->orWhere('court_case_number', 'like', "%{$searchValue}%")
                      ->orWhere('type', 'like', "%{$searchValue}%");
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

        return view('lawyer.cases.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(!Auth::user()->hasPermission('cases:create')){
            abort(403, 'Unauthorized');
        }

        return view('lawyer.cases.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(!Auth::user()->hasPermission('cases:create')){
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name'                            => 'required|string|max:255',
            'type'                            => 'required|string|max:255',
            'urgency'                         => 'nullable|in:HIGH,MEDIUM,LOW,URGENT',
            'court_name'                      => 'required|string|max:255',
            'court_case_number'               => 'required|string|max:255',
            'judge_name'                      => 'nullable|string|max:255',
            'under_acts'                      => 'nullable|string|max:255',
            'under_sections'                  => 'nullable|string|max:255',
            'fir_number'                      => 'nullable|string|max:255',
            'fir_year'                        => 'nullable|string|max:255',
            'police_station'                  => 'nullable|string|max:255',
            'your_party_details'              => 'nullable|string',
            'opposite_party_details'          => 'nullable|string',
            'opposite_party_advocate_details' => 'nullable|string',
            'case_information'                => 'nullable|string',
            'payment_status'                  => 'nullable|in:PENDING,PAID,OVERDUE',
            'fillings'                        => 'nullable|array',
            'fillings.*.description'          => 'nullable|string|max:255',
            'fillings.*.date'                 => 'nullable|date',
            'hearings'                        => 'nullable|array',
            'hearings.*.description'          => 'nullable|string|max:255',
            'hearings.*.date'                 => 'nullable|date',
            'attachments'                     => 'array|max:10',
        ]);

        $case = Cases::create([
            'lawyer_id'                      => Auth::id(),  // or auth()->id()
            'name'                           => $validated['name'],
            'type'                           => $validated['type'],
            'court_name'                     => $validated['court_name'],
            'court_case_number'              => $validated['court_case_number'],
            'urgency'                        => $validated['urgency']                           ?? null,
            'judge_name'                     => $validated['judge_name']                        ?? null,
            'under_acts'                     => $validated['under_acts']                        ?? null,
            'under_sections'                 => $validated['under_sections']                    ?? null,
            'fir_number'                     => $validated['fir_number']                        ?? null,
            'fir_year'                       => $validated['fir_year']                          ?? null,
            'police_station'                 => $validated['police_station']                    ?? null,
            'your_party_details'             => $validated['your_party_details']                ?? null,
            'opposite_party_details'         => $validated['opposite_party_details']            ?? null,
            'opposite_party_advocate_details'=> $validated['opposite_party_advocate_details']   ?? null,
            'case_information'               => $validated['case_information']                  ?? null,
            'payment_status'                 => $validated['payment_status']                    ?? 'PENDING',
        ]);

        if ($request->has('attachments')) {
            // Decode the attachment JSON data
            $attachments = array_map(fn($attachment) => json_decode($attachment), $request->attachments);

            // Process each decoded attachment
            foreach ($attachments as $attachment) {
                // Check if the file exists in the public directory
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    $mimeType = Storage::disk('public')->mimeType($attachment->file_path);

                    if (Storage::disk('public')->move($attachment->file_path, "cases/$case->id/$attachment->file")) {
                        // Save the attachment details to the database
                        $case->attachments()->create([
                            'file'          => $attachment->file,
                            'original_name' => $attachment->original_name,
                            'mime_type'     => $mimeType,
                        ]);
                    }
                }
            }
        }

        if ($request->has('fillings')) {
            
            $fillingsData = array_map(fn($fillingData) => [...$fillingData, 'case_id' => $case->id, 'lawyer_id' => getLawyerId()],$request->fillings);           
            CaseFillingDate::insert($fillingsData);
        }
        
        if ($request->has('hearings')) {
            
            $hearingsData = array_map(fn($hearingData) => [...$hearingData, 'case_id' => $case->id,  'lawyer_id' => getLawyerId()],$request->hearings);           
            CaseHearingDate::insert($hearingsData);
        }


        return redirect()
            ->route('lawyer.cases.index')
            ->with('success', 'Case created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if(!Auth::user()->hasPermission('cases:view')){
            abort(403, 'Unauthorized');
        }
        // Retrieve the case that belongs to the authenticated lawyer or fail
        $case = Cases::where('id', $id)
            ->where('lawyer_id', getLawyerId())
            ->firstOrFail();

        return view('lawyer.cases.show', compact('case'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if(!Auth::user()->hasPermission('cases:edit')){
            abort(403, 'Unauthorized');
        }

        $case = Cases::where('id', $id)
            ->where('lawyer_id', getLawyerId())
            ->firstOrFail();
        return view('lawyer.cases.edit', compact('case'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if(!Auth::user()->hasPermission('cases:edit')){
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name'                              => 'required|string|max:255',
            'type'                              => 'required|string|max:255',
            'urgency'                           => 'nullable|in:HIGH,MEDIUM,LOW,URGENT',
            'court_name'                        => 'required|string|max:255',
            'court_case_number'                 => 'required|string|max:255',
            'judge_name'                        => 'nullable|string|max:255',
            'under_acts'                        => 'nullable|string|max:255',
            'under_sections'                    => 'nullable|string|max:255',
            'fir_number'                        => 'nullable|string|max:255',
            'fir_year'                          => 'nullable|string|max:255',
            'police_station'                    => 'nullable|string|max:255',
            'your_party_details'                => 'nullable|string',
            'opposite_party_details'            => 'nullable|string',
            'opposite_party_advocate_details'   => 'nullable|string',
            'case_information'                  => 'nullable|string',
            'payment_status'                    => 'nullable|in:PENDING,PAID,OVERDUE',
            'status'                            => 'nullable|in:OPEN,IN PROGRESS,CLOSED',
            'fillings'                          => 'nullable|array',
            'fillings.*.description'            => 'nullable|string|max:255',
            'fillings.*.date'                   => 'nullable|date',
            'hearings'                          => 'nullable|array',
            'hearings.*.description'            => 'nullable|string|max:255',
            'hearings.*.date'                   => 'nullable|date',
            'attachments'                       => 'array|max:10',
        ]);

        $case = Cases::where('lawyer_id', getLawyerId())->findOrFail($id);

        $case->update([
            'name'                            => $validated['name'],
            'type'                            => $validated['type'],
            'court_name'                      => $validated['court_name'],
            'court_case_number'               => $validated['court_case_number'],
            'urgency'                         => $validated['urgency']                         ?? null,
            'judge_name'                      => $validated['judge_name']                      ?? null,
            'under_acts'                      => $validated['under_acts']                      ?? null,
            'under_sections'                  => $validated['under_sections']                  ?? null,
            'fir_number'                      => $validated['fir_number']                      ?? null,
            'fir_year'                        => $validated['fir_year']                        ?? null,
            'police_station'                  => $validated['police_station']                  ?? null,
            'your_party_details'              => $validated['your_party_details']              ?? null,
            'opposite_party_details'          => $validated['opposite_party_details']          ?? null,
            'opposite_party_advocate_details' => $validated['opposite_party_advocate_details'] ?? null,
            'case_information'                => $validated['case_information']                ?? null,
            'payment_status'                  => $validated['payment_status']                  ?? 'PENDING',
            'status'                          => $validated['status']                          ?? 'OPEN',
        ]);

        // Handle file attachments
        if ($request->has('attachments')) {
            // Remove old attachments
            $case->attachments()->where('case_id', $case->id)->delete();

            // Decode the attachment JSON data
            $attachments = array_map(fn($attachment) => json_decode($attachment), $request->attachments);

            // Process each decoded attachment
            foreach ($attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    $mimeType = Storage::disk('public')->mimeType($attachment->file_path);

                    if (Storage::disk('public')->move($attachment->file_path, "cases/$case->id/$attachment->file")) {
                        $case->attachments()->create([
                            'file'          => $attachment->file,
                            'original_name' => $attachment->original_name,
                            'mime_type'     => $mimeType,
                        ]);
                    }
                }
            }
        }

        CaseFillingDate::where('case_id', $case->id)->delete();
        
        if ($request->has('fillings')) {
            $fillingsData = array_map(fn($fillingData) => [...$fillingData, 'case_id' => $case->id, 'lawyer_id' => getLawyerId()],$request->fillings);           
            CaseFillingDate::insert($fillingsData);
        }
        
        CaseHearingDate::where('case_id', $case->id)->delete();
        
        if ($request->has('hearings')) {
            $hearingsData = array_map(fn($hearingData) => [...$hearingData, 'case_id' => $case->id, 'lawyer_id' => getLawyerId()],$request->hearings);           
            CaseHearingDate::insert($hearingsData);
        }

        return redirect()
            ->route('lawyer.cases.index')
            ->with('success', 'Case updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        if(!Auth::user()->hasPermission('cases:delete')){
            abort(403, 'Unauthorized');
        }

        Cases::where('lawyer_id', getLawyerId())->findOrFail($id)->delete();

        if ($request->ajax()) {
            return $this->successResponse('case deleted');
        }

        return redirect()->route('lawyer.cases.index')->with('success', 'Case deleted successfully.');
    }

    public function changeHearingDate(Request $request, $caseId, $hearingId)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        // Retrieve the case and hearing
        $case    = Cases::where('lawyer_id', getLawyerId())->findOrFail($caseId);
        $hearing = $case->hearings->findOrFail($hearingId);

        // Update the hearing date
        $hearing->update(['date' => $request->date]);

        // Return success response
        return $this->successResponse('Date changed successfully');
    }

}
