<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        // 1) If it's an AJAX request from DataTables:
        if ($request->ajax()) {

            // DataTables columns to map for ordering:
            $columns            = ['id', 'name', 'email', 'phone', 'counttry', 'created_at', 'meeting_link_lawyer'];
            $draw               = $request->input('draw');
            $start              = $request->input('start');        // skip
            $length             = $request->input('length');       // rows per page
            $searchValue        = $request->input('search.value');  // global search box
            $orderColumnIndex   = $request->input('order.0.column'); // which column index is being sorted
            $orderDirection     = $request->input('order.0.dir');    // asc or desc

            $query          = Appointment::with(['user:id,name,email,phone,avatar', 'attachments'])->where('lawyer_id', Auth::guard('lawyer')->id());
            $totalRecords   = $query->count();

            if (!empty($searchValue)) {
                $query->when($searchValue, fn($query, $searchValue) =>
                    $query->where(fn($q) =>
                        $q->where('id', $searchValue)
                        ->orWhere('country', $searchValue)
                        ->orWhereHas('user', fn($q) =>
                            $q->where('name', 'like', "%{$searchValue}%")
                                ->orWhere('email', 'like', "%{$searchValue}%")
                                ->orWhere('phone', 'like', "%{$searchValue}%")
                        )
                    )
                );
            }

            $totalRecordsFiltered = $query->count();

            if (isset($columns[$orderColumnIndex]))  $query->orderBy($columns[$orderColumnIndex], $orderDirection);
            else $query->orderBy('id', 'asc');

            $data = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw'            => intval($draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $totalRecordsFiltered,
                'data'            => $data,
            ]);
        }

        // 9) If not an AJAX request, simply load the Blade view
        return view('lawyer.appointments.index');
    }

    public function edit(string $id)
    {
        $appointment = Appointment::where('id', $id)->where('lawyer_id', Auth::guard('lawyer')->id())->firstOrFail();
        return view('lawyer.appointments.edit', compact('appointment'));
    }

    public function update(Request $request, string $id)
    {
        // Validate incoming data
        $validated = $request->validate([
            'summary'               => 'required|string',
            'attachments'           => 'array|max:10',
        ]);

        $appointment = Appointment::where('id', $id)->where('lawyer_id', Auth::guard('lawyer')->id())->firstOrFail();

        // Update client details
        $appointment->update([
            'summary' => $validated['summary'],
        ]);

        // Process attachments if any exist in the request.
        if ($request->has('attachments')) {
            // Delete all old attachments first
            $appointment->summaryAttachments()->delete();
            // Decode each attachment JSON string
            $attachments = array_map(fn($attachment) => json_decode($attachment), $request->attachments);
            // Upload the new attachments
            foreach ($attachments as $attachment) {
                // Check if the file exists in the public disk
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    $mimeType = Storage::disk('public')->mimeType($attachment->file_path);
                    // Move the file into a directory specific to the client
                    if (Storage::disk('public')->move($attachment->file_path, "appointments/summary/$appointment->id/{$attachment->file}")) {
                        // Save the attachment record
                        $appointment->summaryAttachments()->create([
                            'file'          => $attachment->file,
                            'original_name' => $attachment->original_name,
                            'mime_type'     => $mimeType,
                        ]);
                    }
                }
            }
        }

        return redirect()
            ->route('lawyer.appointment.index')
            ->with('success', 'Appointment updated successfully!');
    }

    public function show(string $id)
    {
        // Retrieve the client that belongs to the authenticated lawyer or fail
        $appointment = Appointment::where('id', $id)
            ->where('lawyer_id', Auth::guard('lawyer')->id())
            ->firstOrFail();

        // Return the view with the case data
        return view('lawyer.appointments.show', compact('appointment'));
    }
}
