<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Cases;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $columns            = ['id', 'name', 'type', 'urgency', 'status', 'payment_status', 'created_at'];
            $draw               = $request->input('draw');
            $start              = $request->input('start');        // skip
            $length             = $request->input('length');       // rows per page
            $searchValue        = $request->input('search.value');  // global search box
            $orderColumnIndex   = $request->input('order.0.column'); // which column index is being sorted
            $orderDirection     = $request->input('order.0.dir');    // asc or desc

            $query              = Cases::where('lawyer_id', Auth::user()->id);
            $totalRecords       = $query->count();

            if (!empty($searchValue)) {
                $query->where('name', 'like', "%{$searchValue}%")->orWhere('court_case_number', 'like', "%{$searchValue}%");
            }

            $totalRecordsFiltered = $query->count();

            if (isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDirection);
            } else {
                $query->orderBy('id', 'asc');
            }

            $query->skip($start)->take($length);

            $appointments = $query->get();

            return response()->json([
                'draw'            => intval($draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $totalRecordsFiltered,
                'data'            => $appointments,
            ]);
        }

        return view('lawyer.cases.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lawyer.cases.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name'                            => ['required', 'string', 'max:255'],
            'type'                            => ['required', 'string', 'max:255'],
            'urgency'                         => ['nullable', 'in:HIGH,MEDIUM,CRITICAL'],
            'court_name'                      => ['required', 'string', 'max:255'],
            'court_case_number'               => ['required', 'string', 'max:255'],
            'judge_name'                      => ['nullable', 'string', 'max:255'],
            'under_acts'                      => ['nullable', 'string', 'max:255'],
            'under_sections'                  => ['nullable', 'string', 'max:255'],
            'fir_number'                      => ['nullable', 'string', 'max:255'],
            'fir_year'                        => ['nullable', 'string', 'max:255'],
            'police_station'                  => ['nullable', 'string', 'max:255'],
            'your_party_details'              => ['nullable', 'string'],
            'opposite_party_details'          => ['nullable', 'string'],
            'opposite_party_advocate_details' => ['nullable', 'string'],
            'case_information'                => ['nullable', 'string'],
            'payment_status'                  => ['nullable', 'in:PENDING,PAID,OVERDUE'],
            'deadlines'                       => ['nullable', 'array'],
            'deadlines.*.description'         => ['nullable', 'string', 'max:255'],
            'deadlines.*.date'                => ['nullable', 'date'],

            // Attachments: up to 10, each validated by a custom callback
            'attachments'   => ['array', 'max:10'],
            'attachments.*' => [
                'file', // ensures it's an actual file
                function ($_, $file, $fail) {
                    // Allowed MIME types
                    $allowedImages = ['image/png', 'image/jpeg', 'image/webp'];
                    $allowedDocs   = [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ];

                    $mimeType = $file->getMimeType();
                    $fileSize = $file->getSize(); // bytes

                    // Images => max 2 MB
                    if (in_array($mimeType, $allowedImages)) {
                        if ($fileSize > 2 * 1024 * 1024) {
                            return $fail("The image {$file->getClientOriginalName()} exceeds the 2 MB limit.");
                        }
                    }
                    // Docs => max 10 MB
                    elseif (in_array($mimeType, $allowedDocs)) {
                        if ($fileSize > 10 * 1024 * 1024) {
                            return $fail("The file {$file->getClientOriginalName()} exceeds the 10 MB limit for documents/PDFs.");
                        }
                    }
                    // Otherwise => not an allowed file type
                    else {
                        return $fail("The file {$file->getClientOriginalName()} is not an allowed format (png,jpg,webp,pdf,doc,docx).");
                    }
                }
            ],
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
            'opposite_party_advocate_details' => $validated['opposite_party_advocate_details']   ?? null,
            'case_information'               => $validated['case_information']                  ?? null,
            'deadlines'                      => $validated['deadlines']                         ?? null,
            'payment_status'                 => $validated['payment_status']                    ?? 'PENDING',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storedPath = $file->store("cases/{$case->id}", 'public');
                $case->attachments()->create([
                    'file_path'     => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getMimeType(),
                ]);
            }
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
        // Retrieve the case that belongs to the authenticated lawyer or fail
        $case = Cases::where('id', $id)
            ->where('lawyer_id', Auth::id())
            ->firstOrFail();

        // Return the view with the case data
        return view('lawyer.cases.show', compact('case'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $case = Cases::where('id', $id)->where('lawyer_id', Auth::user()->id)->firstOrFail();
        return view('lawyer.cases.edit')->with('case', $case);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $case = Cases::where('lawyer_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name'                              => ['required', 'string', 'max:255'],
            'type'                              => ['required', 'string', 'max:255'],
            'urgency'                           => ['nullable', 'in:HIGH,MEDIUM,CRITICAL'],
            'court_name'                        => ['required', 'string', 'max:255'],
            'court_case_number'                 => ['required', 'string', 'max:255'],
            'judge_name'                        => ['nullable', 'string', 'max:255'],
            'under_acts'                        => ['nullable', 'string', 'max:255'],
            'under_sections'                    => ['nullable', 'string', 'max:255'],
            'fir_number'                        => ['nullable', 'string', 'max:255'],
            'fir_year'                          => ['nullable', 'string', 'max:255'],
            'police_station'                    => ['nullable', 'string', 'max:255'],
            'your_party_details'                => ['nullable', 'string'],
            'opposite_party_details'            => ['nullable', 'string'],
            'opposite_party_advocate_details'   => ['nullable', 'string'],
            'case_information'                  => ['nullable', 'string'],
            'payment_status'                    => ['nullable', 'in:PENDING,PAID,OVERDUE'],
            'status'                            => ['nullable', 'in:OPEN,IN PROGRESS,CLOSED'],
            'deadlines'                         => ['nullable', 'array'],
            'deadlines.*.description'           => ['nullable', 'string', 'max:255'],
            'deadlines.*.date'                  => ['nullable', 'date'],

            'attachments'   => ['array', 'max:10'],
            'attachments.*' => [
                'file',
                function ($_, $file, $fail) {
                    $allowedImages = ['image/png', 'image/jpeg', 'image/webp'];
                    $allowedDocs   = [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ];

                    $mimeType = $file->getMimeType();
                    $fileSize = $file->getSize();

                    if (in_array($mimeType, $allowedImages) && $fileSize > 2 * 1024 * 1024) {
                        return $fail("The image {$file->getClientOriginalName()} exceeds the 2 MB limit.");
                    } elseif (in_array($mimeType, $allowedDocs) && $fileSize > 10 * 1024 * 1024) {
                        return $fail("The file {$file->getClientOriginalName()} exceeds the 10 MB limit.");
                    } elseif (!in_array($mimeType, array_merge($allowedImages, $allowedDocs))) {
                        return $fail("The file {$file->getClientOriginalName()} is not an allowed format.");
                    }
                }
            ],
        ]);

        // Update the case fields
        $case->update([
            'name'                            => $validated['name'],
            'type'                            => $validated['type'],
            'court_name'                      => $validated['court_name'],
            'court_case_number'               => $validated['court_case_number'],
            'urgency'                         => $validated['urgency']                          ?? null,
            'judge_name'                      => $validated['judge_name']                       ?? null,
            'under_acts'                      => $validated['under_acts']                       ?? null,
            'under_sections'                  => $validated['under_sections']                   ?? null,
            'fir_number'                      => $validated['fir_number']                       ?? null,
            'fir_year'                        => $validated['fir_year']                         ?? null,
            'police_station'                  => $validated['police_station']                   ?? null,
            'your_party_details'              => $validated['your_party_details']               ?? null,
            'opposite_party_details'          => $validated['opposite_party_details']           ?? null,
            'opposite_party_advocate_details' => $validated['opposite_party_advocate_details']  ?? null,
            'case_information'                => $validated['case_information']                 ?? null,
            'deadlines'                       => $validated['deadlines']                        ?? null,
            'payment_status'                  => $validated['payment_status']                   ?? 'PENDING',
            'status'                          => $validated['status']                           ?? 'OPEN',
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {

            // remove old attachments
            $case->attachments()->where('case_id', $case->id)->delete();

            foreach ($request->file('attachments') as $file) {
                $storedPath = $file->store("cases/{$case->id}", 'public');
                $case->attachments()->create([
                    'file_path'     => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getMimeType(),
                ]);
            }
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
        $case = Cases::where('lawyer_id', Auth::id())->findOrFail($id);
        $case->delete();

        if ($request->ajax()) {
            return $this->successResponse('case deleted');
        }

        // Return response or redirect as needed
        return redirect()->route('lawyer.cases.index')->with('success', 'Case deleted successfully.');
    }

    /**
     * Handle the file upload from AeroDrop.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        // Validate the file. Note: max is in kilobytes (10 MB = 10240 KB)
        $validatedData = $request->validate([
            'file' => 'required|file|max:10240|mimes:jpeg,jpg,png,webp,pdf',
        ]);

        // Check if the file exists in the request.
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Create a unique file name.
            $filename = time() . '_' . $file->getClientOriginalName();
            // Store the file in the "uploads" folder on the "public" disk.
            $path = $file->storeAs('uploads', $filename, 'public');

            return $this->successResponse('Upload successful', [
                'file' => $filename,
                'path' => $path,
            ]);
        }

        return $this->errorResponse('No file uploaded');
    }
}
