<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
        $totalClients               = Auth::guard('team')->user()->owner->clients->count();
        $totalAppointments          = Auth::guard('team')->user()->owner->appointments->count();
        $totalCasesToday            = Auth::guard('team')->user()->owner->cases->where('date_created', Carbon::now())->count();
        $totalHighPriorityCases     = Auth::guard('team')->user()->owner->cases->where('urgency', 'HIGH')->count();
        $totalDecidedCases          = Auth::guard('team')->user()->owner->cases->where('status', 'CLOSED')->count();
        return view('team.dashboard', compact('totalClients', 'totalAppointments', 'totalCasesToday', 'totalHighPriorityCases', 'totalDecidedCases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
