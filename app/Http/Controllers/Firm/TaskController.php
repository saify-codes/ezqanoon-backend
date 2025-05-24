<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Traits\ApiResponseTrait;
use App\Utils\Icon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // DataTables columns to map for ordering:
            $columns            = ['id', 'name', 'start_date', 'end_date', 'status', 'assign_to', 'created_at'];
            $draw               = $request->input('draw');
            $start              = $request->input('start');        // skip
            $length             = $request->input('length');       // rows per page
            $searchValue        = $request->input('search.value');  // global search box
            $orderColumnIndex   = $request->input('order.0.column'); // which column index is being sorted
            $orderDirection     = $request->input('order.0.dir');    // asc or desc

            $query              = Task::with(['member:id,name,email,phone'])->where('firm_id',  Auth::guard('firm')->id());
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
        return view('firm.task.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $team =  Auth::guard('firm')->user()->team;
        return view('firm.task.create', compact('team'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                            => 'required|string|max:255',
            'start_date'                      => 'required|date',
            'end_date'                        => 'required|date',
            'status'                          => 'required|in:PENDING,IN PROGRESS,COMPLETED',
            'assign_to'                       => 'nullable|exists:teams,id',
            
        ]);
        
        Task::create([
            'name'                            => $validated['name'],
            'start_date'                      => $validated['start_date'],
            'end_date'                        => $validated['end_date'],
            'status'                          => $validated['status'],
            'firm_id'                         => Auth::guard('firm')->id(),
            'assign_to'                       => $validated['assign_to'],
        ]);

        if ($validated['assign_to']) {
            notifyTeamMember($validated['assign_to'], 'New task', 'You have been assigned a task', Icon::clipboard());
        }

        return redirect()
            ->route('firm.task.index')
            ->with('success', 'Task created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::where('id', $id)
            ->where('firm_id',  Auth::guard('firm')->id())
            ->firstOrFail();

        return view('firm.task.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $team =  Auth::guard('firm')->user()->team;
        $task = Task::where('id', $id)
            ->where('firm_id',  Auth::guard('firm')->id())
            ->firstOrFail();
        return view('firm.task.edit', compact('team', 'task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name'                            => 'required|string|max:255',
            'start_date'                      => 'required|date',
            'end_date'                        => 'required|date',
            'status'                          => 'required|in:PENDING,IN PROGRESS,COMPLETED',
            'assign_to'                       => 'nullable|exists:teams,id', 
        ]);

        $task = Task::where('firm_id',  Auth::guard('firm')->id())->findOrFail($id);
        
        if ($validated['assign_to']) {

            // check if previously assigned to someone
            if($task->assign_to){
                notifyTeamMember($task->assign_to, 'Task changed', "$task->name has been assigned to someone else", Icon::alert());            
                notifyTeamMember($validated['assign_to'], 'New task', 'You have been assigned a task', Icon::clipboard());
            }else{
                notifyTeamMember($validated['assign_to'], 'New task', 'You have been assigned a task', Icon::clipboard());
            }
        }

        $task->update([
            'name'                            => $validated['name'],
            'start_date'                      => $validated['start_date'],
            'end_date'                        => $validated['end_date'],
            'status'                          => $validated['status'],
            'assign_to'                       => $validated['assign_to'],
        ]);



        return redirect()
            ->route('firm.task.index')
            ->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $task = Task::where('firm_id',  Auth::guard('firm')->id())->findOrFail($id);

        if ($task->member?->exists()) {
            notifyTeamMember($task->member->id, 'Task deleted', "$task->name deleted", Icon::x());            
        }

        $task->delete();

        if ($request->ajax()) {
            return $this->successResponse('task deleted');
        }

        return redirect()->route('firm.task.index')->with('success', 'task deleted successfully.');
   
    }
}
