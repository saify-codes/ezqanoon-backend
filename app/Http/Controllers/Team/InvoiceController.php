<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceMilestone;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class InvoiceController extends Controller
{
    use ApiResponseTrait;

    private $ownerForeignKey;
    private $owner;

    public function __construct() {
        $owners = [
            \App\Models\Firm::class   => 'firm_id',
            \App\Models\Lawyer::class => 'lawyer_id',
            // ... add more role classes here
        ];

        $this->owner           = Auth::guard('team')->user()->owner; 
        $this->ownerForeignKey = $owners[get_class(Auth::guard('team')->user()->owner)] ?? throw new Exception('Owner not found'); 
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // DataTables columns to map for ordering:
            $columns            = ['id', 'name', 'phone', 'type', 'status', 'case_type', 'total', 'balance', 'due_date', 'created_at'];
            $draw               = $request->input('draw');
            $start              = $request->input('start');        // skip
            $length             = $request->input('length');       // rows per page
            $searchValue        = $request->input('search.value');  // global search box
            $orderColumnIndex   = $request->input('order.0.column'); // which column index is being sorted
            $orderDirection     = $request->input('order.0.dir');    // asc or desc

            $query              = Invoice::select('*')->selectRaw('(total - paid) as balance')->with('milestone')->where($this->ownerForeignKey, $this->owner->id);
            $totalRecords       = $query->count();

            if (!empty($searchValue)) {
                $query->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('phone', 'like', "%{$searchValue}%");
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
        return view('team.invoice.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('team.invoice.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                            => 'required|string|max:255',
            'email'                           => 'required|email',
            'country_code'                    => 'required_with:phone',
            'phone'                           => 'required|phone:' . $request->country_code,
            'address'                         => 'required|string',
            'city'                            => 'required|string',
            'country'                         => 'required|string',
            'case_type'                       => 'nullable|string',
            'type'                            => 'required|string',
            'payment_method'                  => 'required|in:CASH,BANK,ONLINE',
            'due_date'                        => 'required_if:type,ONE TIME|date',
            'status'                          => 'required_if:type,ONE TIME|in:PENDING,PAID,OVERDUE',
            'milestone'                       => 'nullable|array',
            'milestone.*.description'         => 'nullable|string|max:255',
            'milestone.*.due_date'            => 'nullable|date',
            'milestone.*.status'              => 'nullable|string',
            'receipt'                         => 'required|array',
            'grand_total'                     => 'required|numeric',
            'paid'                            => 'required|numeric',
        ]);

        $invoice = Invoice::create([
            $this->ownerForeignKey            => $this->owner->id,
            'name'                            => $validated['name'],
            'email'                           => $validated['email'],
            'phone'                           => $validated['phone'],
            'address'                         => $validated['address'],
            'city'                            => $validated['city'],
            'country'                         => $validated['country'],
            'case_type'                       => $validated['case_type'],
            'type'                            => $validated['type'],
            'payment_method'                  => $validated['payment_method'],
            'due_date'                        => $validated['due_date'] ?? null,
            'status'                          => $validated['status']   ?? null,
            'total'                           => $validated['grand_total'],
            'paid'                            => $validated['paid'],
            'receipt'                         => $validated['receipt'] ?? null
        ]);


        if ($request->type === 'MILESTONE') {
            foreach ($validated['milestone'] as $milestone) {
                InvoiceMilestone::create([
                    'invoice_id' => $invoice->id,
                    ...$milestone
                ]);
            }
        }

        return redirect()
            ->route('team.invoice.index')
            ->with('success', 'Invoice created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Retrieve the invloice that belongs to the authenticated lawyer or fail
        $invoice = Invoice::where('id', $id)
            ->where($this->ownerForeignKey, $this->owner->id)
            ->firstOrFail();

        // Return the view with the case data
        return view('team.invoice.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoice = Invoice::where('id', $id)
            ->where($this->ownerForeignKey, $this->owner->id)
            ->firstOrFail();

        return view('team.invoice.edit', compact('invoice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        // validate exactly as in store()
        $validated = $request->validate([
            'name'                            => 'required|string|max:255',
            'email'                           => 'required|email',
            'country_code'                    => 'required_with:phone',
            'phone'                           => 'required|phone:' . $request->country_code,
            'address'                         => 'required|string',
            'city'                            => 'required|string',
            'country'                         => 'required|string',
            'case_type'                       => 'required|string',
            'type'                            => 'required|string',
            'payment_method'                  => 'required|in:CASH,BANK,ONLINE',
            'due_date'                        => 'required_if:type,ONE TIME|date',
            'status'                          => 'required_if:type,ONE TIME|in:PENDING,PAID,OVERDUE',
            'milestone'                       => 'nullable|array',
            'milestone.*.description'         => 'nullable|string|max:255',
            'milestone.*.due_date'            => 'nullable|date',
            'milestone.*.status'              => 'nullable|string',
            'receipt'                         => 'required|array',
            'grand_total'                     => 'required|numeric',
            'paid'                            => 'required|numeric',
        ]);

        // fetch the invoice, ensuring it belongs to this lawyer
        $invoice = Invoice::where('id', $id)
            ->where($this->ownerForeignKey, $this->owner->id)
            ->firstOrFail();

        // update main invoice fields
        $invoice->update([
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'phone'            => $validated['phone'],
            'address'          => $validated['address'],
            'city'             => $validated['city'],
            'country'          => $validated['country'],
            'case_type'        => $validated['case_type'],
            'type'             => $validated['type'],
            'payment_method'   => $validated['payment_method'],
            // only keep due_date/status on ONE TIME invoices
            'due_date'         => $validated['type'] === 'ONE TIME'? $validated['due_date']: null,
            'status'           => $validated['type'] === 'ONE TIME'? $validated['status']: null,
            'total'            => $validated['grand_total'],
            'paid'             => $validated['paid'],
            'receipt'          => $validated['receipt'],
        ]);

        // remove any old milestones
        InvoiceMilestone::where('invoice_id', $invoice->id)->delete();

        // if it's a milestone‐based invoice, recreate them
        if ($validated['type'] === 'MILESTONE' && !empty($validated['milestone'])) {
            foreach ($validated['milestone'] as $ms) {
                InvoiceMilestone::create([
                    'invoice_id'  => $invoice->id,
                    'description' => $ms['description'] ?? null,
                    'due_date'    => $ms['due_date']    ?? null,
                    'status'      => $ms['status']      ?? null,
                ]);
            }
        }

        return redirect()
            ->route('team.invoice.index')
            ->with('success', 'Invoice updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        Invoice::where($this->ownerForeignKey, $this->owner->id)->findOrFail($id)->delete();

        if ($request->ajax()) {
            return $this->successResponse('invoice deleted');
        }

        return redirect()->route('team.invoice.index')->with('success', 'Invoice deleted successfully.');
    }
}
