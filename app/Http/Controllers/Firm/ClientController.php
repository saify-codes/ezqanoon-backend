<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {

            // DataTables columns to map for ordering:
            $columns            = ['id', 'first_name', 'last_name', 'gender', 'phone', 'origin', 'type', 'created_at'];
            $draw               = $request->input('draw');
            $start              = $request->input('start');        // skip
            $length             = $request->input('length');       // rows per page
            $searchValue        = $request->input('search.value');  // global search box
            $orderColumnIndex   = $request->input('order.0.column'); // which column index is being sorted
            $orderDirection     = $request->input('order.0.dir');    // asc or desc

            $query              = Client::where('firm_id', Auth::guard('firm')->id());
            $totalRecords       = $query->count();

            if (!empty($searchValue)) {
                $query->where('first_name', 'like', "%{$searchValue}%")
                    ->orWhere('last_name', 'like', "%{$searchValue}%")
                    ->orWhere('phone', 'like', "%{$searchValue}%")
                    ->orWhere('origin', 'like', "%{$searchValue}%");
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

        return view('firm.client.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('firm.client.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'first_name'            => 'required|string|max:255',
            'last_name'             => 'required|string|max:255',
            'dob'                   => 'nullable|date',
            'gender'                => 'required|in:MALE,FEMALE,OTHER',
            'type'                  => 'required',
            'country_code'          => 'required_with:phone',
            'phone'                 => 'nullable|phone:' . $request->country_code,
            'email'                 => 'nullable|email|max:255',
            'company_name'          => 'nullable|string|max:255',
            'company_website'       => 'nullable|url|max:255',
            'communication_method'  => 'nullable|string|max:255',
            'contact_time'          => 'nullable',
            'language'              => 'nullable|string|max:50',
            'origin'                => 'required|in:LOCAL,FOREIGN',
            'city'                  => 'nullable|string|max:255',
            'state'                 => 'nullable|string|max:255',
            'country'               => 'nullable|string|max:255',
            'address'               => 'nullable|string',
            'billing_address'       => 'nullable|string',
            'payment_methods'       => 'nullable|array',
            'tin'                   => 'nullable|string|max:50',
            'notes'                 => 'nullable|array',
            'notes.*'               => 'nullable|string',
            'attachments'           => 'array|max:10',
        ]);

        $client = Client::create([
            'firm_id'               => Auth::guard('firm')->id(),
            'first_name'            => $validated['first_name'],
            'last_name'             => $validated['last_name'],
            'gender'                => $validated['gender'],
            'type'                  => $validated['type'],
            'email'                 => $validated['email'],
            'origin'                => $validated['origin'],
            'dob'                   => $validated['dob']                    ?? null,
            'phone'                 => $validated['phone']                  ?? null,
            'company_name'          => $validated['company_name']           ?? null,
            'company_website'       => $validated['company_website']        ?? null,
            'city'                  => $validated['city']                   ?? null,
            'state'                 => $validated['state']                  ?? null,
            'country'               => $validated['country']                ?? null,
            'address'               => $validated['address']                ?? null,
            'communication_method'  => $validated['communication_method']   ?? null,
            'contact_time'          => $validated['contact_time']           ?? null,
            'language'              => $validated['language']               ?? null,
            'billing_address'       => $validated['billing_address']        ?? null,
            'payment_methods'       => $validated['payment_methods']        ?? null,
            'tin'                   => $validated['tin']                    ?? null,
            'notes'                 => $validated['notes']                  ?? null,
        ], [
            'phone.required'    => 'Phone number is required',
            'phone.string'      => 'Phone number must be a valid string',
            'phone.max'         => 'Phone number cannot exceed 20 characters',
            'phone.validation'  => 'Phone number is invalid',
        ]);

        // Process attachments if any exist in the request.
        if ($request->has('attachments')) {
            // Decode each attachment JSON string
            $attachments = array_map(fn($attachment) => json_decode($attachment), $request->attachments);

            foreach ($attachments as $attachment) {
                // Check if the file exists in the public disk
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    $mimeType = Storage::disk('public')->mimeType($attachment->file_path);
                    // Move the file into a directory specific to the client
                    if (Storage::disk('public')->move($attachment->file_path, "clients/$client->id/{$attachment->file}")) {
                        // Save the attachment record (assuming a one-to-many relationship between Client and AppointmentAttachment)
                        $client->attachments()->create([
                            'file'          => $attachment->file,
                            'original_name' => $attachment->original_name,
                            'mime_type'     => $mimeType,
                        ]);
                    }
                }
            }
        }

        return redirect()
            ->route('firm.client.index')
            ->with('success', 'Client created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $client = Client::where('id', $id)->where('firm_id', Auth::guard('firm')->id())->firstOrFail();
        return view('firm.client.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate incoming data
        $validated = $request->validate([
            'first_name'            => 'required|string|max:255',
            'last_name'             => 'required|string|max:255',
            'dob'                   => 'nullable|date',
            'gender'                => 'required|in:MALE,FEMALE,OTHER',
            'type'                  => 'required',
            'country_code'          => 'required_with:phone',
            'phone'                 => 'nullable|phone:' . $request->country_code,
            'email'                 => 'nullable|email|max:255',
            'company_name'          => 'nullable|string|max:255',
            'company_website'       => 'nullable|url|max:255',
            'communication_method'  => 'nullable|string|max:255',
            'contact_time'          => 'nullable',
            'language'              => 'nullable|string|max:50',
            'origin'                => 'required|in:LOCAL,FOREIGN',
            'city'                  => 'nullable|string|max:255',
            'state'                 => 'nullable|string|max:255',
            'country'               => 'nullable|string|max:255',
            'address'               => 'nullable|string',
            'billing_address'       => 'nullable|string',
            'payment_methods'       => 'nullable|array',
            'tin'                   => 'nullable|string|max:50',
            'notes'                 => 'nullable|array',
            'notes.*'               => 'nullable|string',
            'attachments'           => 'array|max:10',
        ],[
            'phone.required'    => 'Phone number is required',
            'phone.string'      => 'Phone number must be a valid string',
            'phone.max'         => 'Phone number cannot exceed 20 characters',
            'phone.validation'  => 'Phone number is invalid',
        ]);

        // Find the client that belongs to the authenticated lawyer
        $client = Client::where('id', $id)->where('firm_id', Auth::guard('firm')->id())->firstOrFail();

        // Update client details
        $client->update([
            'first_name'            => $validated['first_name'],
            'last_name'             => $validated['last_name'],
            'gender'                => $validated['gender'],
            'type'                  => $validated['type'],
            'email'                 => $validated['email'],                 
            'origin'                 => $validated['origin'],                 
            'phone'                 => $validated['phone']                  ?? null,
            'dob'                   => $validated['dob']                    ?? null,
            'company_name'          => $validated['company_name']           ?? null,
            'company_website'       => $validated['company_website']        ?? null,
            'address'               => $validated['address']                ?? null,
            'city'                  => $validated['city']                   ?? null,
            'state'                 => $validated['state']                  ?? null,
            'country'               => $validated['country']                ?? null,
            'communication_method'  => $validated['communication_method']   ?? null,
            'contact_time'          => $validated['contact_time']           ?? null,
            'language'              => $validated['language']               ?? null,
            'billing_address'       => $validated['billing_address']        ?? null,
            'payment_methods'       => $validated['payment_methods']        ?? null,
            'tin'                   => $validated['tin']                    ?? null,
            'notes'                 => $validated['notes']                  ?? null,
        ]);

        // Process attachments if any exist in the request.
        if ($request->has('attachments')) {
            // Delete all old attachments first
            $client->attachments()->delete();
            // Decode each attachment JSON string
            $attachments = array_map(fn($attachment) => json_decode($attachment), $request->attachments);
            // Upload the new attachments
            foreach ($attachments as $attachment) {
                // Check if the file exists in the public disk
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    $mimeType = Storage::disk('public')->mimeType($attachment->file_path);
                    // Move the file into a directory specific to the client
                    if (Storage::disk('public')->move($attachment->file_path, "clients/$client->id/{$attachment->file}")) {
                        // Save the attachment record
                        $client->attachments()->create([
                            'file'          => $attachment->file,
                            'original_name' => $attachment->original_name,
                            'mime_type'     => $mimeType,
                        ]);
                    }
                }
            }
        }

        // Redirect to the client index with success message
        return redirect()
            ->route('firm.client.index')
            ->with('success', 'Client updated successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Retrieve the client that belongs to the authenticated lawyer or fail
        $client = Client::where('id', $id)
            ->where('firm_id', Auth::guard('firm')->id())
            ->firstOrFail();

        // Return the view with the case data
        return view('firm.client.show', compact('client'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $case = Client::where('firm_id', Auth::guard('firm')->id())->findOrFail($id);
        $case->delete();

        if ($request->ajax()) {
            return $this->successResponse('case deleted');
        }

        // Return response or redirect as needed
        return redirect()->route('firm.client.index')->with('success', 'Client deleted successfully.');
    }
}
