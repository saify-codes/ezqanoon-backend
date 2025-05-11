<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Lawyer;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // DataTables columns to map for ordering:
            $columns            = ['id', 'name', 'email', 'phone', 'permissions', 'email_verified_at', 'created_at'];
            $draw               = $request->input('draw');
            $start              = $request->input('start');        // skip
            $length             = $request->input('length');       // rows per page
            $searchValue        = $request->input('search.value');  // global search box
            $orderColumnIndex   = $request->input('order.0.column'); // which column index is being sorted
            $orderDirection     = $request->input('order.0.dir');    // asc or desc

            // Get the base query instead of the collection
            $query              = Auth::user()->team()->getQuery();
            $totalRecords       = $query->count();

            if (!empty($searchValue)) {
                $query->where('name', 'like', "%{$searchValue}%")
                      ->orWhere('email', 'like', "%{$searchValue}%");
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
        return view('lawyer.team.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lawyer.team.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:lawyers',
            'country_code'  => 'required_with:phone',
            'phone'         => 'required|phone:' . $request->country_code,
            'password'      => 'required|string|min:8|confirmed',
            'permissions'   => 'nullable|array',
        ]);

        Lawyer::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'password'      => Hash::make($request->password),
            'permissions'   => $request->permissions,
            'role'          => 'USER',
            'lawyer_id'     => getLawyerId(),
        ],[
            'validation.phone' => 'invalid phone number'
        ]);

        return redirect()->route('lawyer.team.index')->with('success', 'User created successfully');    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Lawyer::where('lawyer_id', getLawyerId())->findOrFail($id);

        return view('lawyer.team.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'country_code'  => 'required_with:phone',
            'phone'         => 'required|phone:' . $request->country_code,
            'permissions'   => 'nullable|array',
        ]);

        $user           = Lawyer::where('lawyer_id', getLawyerId())->findOrFail($id);
        $oldPermissions = $user->permissions;

        $user->update([
            'name'          => $request->name,
            'phone'         => $request->phone,
            'permissions'   => $request->permissions,
        ]);

        // Notify if permissions changed
        if ($oldPermissions != $request->permissions) {
            notifyUser($user->id, 'Permissions Updated', 'Admin has updated your permissions');
        }

        return redirect()->route('lawyer.team.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
       
        Lawyer::where('lawyer_id', getLawyerId())->findOrFail($id)->delete();

        if ($request->ajax()) {
            return $this->successResponse('user deleted');
        }

        return redirect()->route('lawyer.team.index')->with('success', 'User deleted successfully.');
    }

    public function changePassword(Request $request, string $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        Lawyer::where('lawyer_id', getLawyerId())->findOrFail($id)->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('lawyer.team.index')->with('success', 'Password changed successfully');
    }
}
