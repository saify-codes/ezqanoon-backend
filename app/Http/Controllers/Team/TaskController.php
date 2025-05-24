<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use ApiResponseTrait;

    private $ownerForeignKey;
    private $owner;

    public function __construct()
    {
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
            $columns            = ['id', 'name', 'start_date', 'end_date', 'status', 'created_at'];
            $draw               = $request->input('draw');
            $start              = $request->input('start');        // skip
            $length             = $request->input('length');       // rows per page
            $searchValue        = $request->input('search.value');  // global search box
            $orderColumnIndex   = $request->input('order.0.column'); // which column index is being sorted
            $orderDirection     = $request->input('order.0.dir');    // asc or desc

            $query              = Task::where($this->ownerForeignKey, $this->owner->id);
            $totalRecords       = $query->count();

            if (!empty($searchValue)) {
                $query->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('status', 'like', "%{$searchValue}%");
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
        return view('team.task.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::where('id', $id)
            ->where($this->ownerForeignKey, $this->owner->id)
            ->firstOrFail();

        return view('team.task.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $team = Auth::guard('team')->user()->team;
        $task = Task::where('id', $id)
            ->where($this->ownerForeignKey, $this->owner->id)
            ->firstOrFail();
        return view('team.task.edit', compact('team', 'task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:PENDING,IN PROGRESS,COMPLETED'
        ]);

        $task = Task::where($this->ownerForeignKey, $this->owner->id)->findOrFail($id);
        $task->update(['status' => $validated['status']]);

        return redirect()
            ->route('team.task.index')
            ->with('success', 'Task updated successfully!');
    }

}
