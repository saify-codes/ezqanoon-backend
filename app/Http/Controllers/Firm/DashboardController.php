<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\Firm;
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
        $totalClients               = Auth::guard('firm')->user()->clients->count();
        $totalAppointments          = Auth::guard('firm')->user()->appointments->count();
        $totalCasesToday            = Auth::guard('firm')->user()->cases->where('date_created', Carbon::now())->count();
        $totalHighPriorityCases     = Auth::guard('firm')->user()->cases->where('urgency', 'HIGH')->count();
        $totalDecidedCases          = Auth::guard('firm')->user()->cases->where('status', 'CLOSED')->count();
        return view('firm.dashboard', compact('totalClients', 'totalAppointments', 'totalCasesToday', 'totalHighPriorityCases', 'totalDecidedCases'));
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
