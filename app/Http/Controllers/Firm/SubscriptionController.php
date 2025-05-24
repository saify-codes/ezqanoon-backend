<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = Subscription::where('status', 'active')->get();
        return view('firm.subscription', compact('subscriptions'));
    }

    public function select(Subscription $subscription){

        // simulation of payment

        Auth::guard('firm')->user()->update([
            'subscription_id'           => $subscription->id,
            'subscription_expires_at'   => now()->addDays($subscription->duration),
        ]);

        SubscriptionHistory::create([
            'firm_id'           => Auth::guard('firm')->id(),
            'subscription_id'   => $subscription->id,
            'amount'            => $subscription->price,
            'start_date'        => now(),
            'end_date'          => now()->addDays($subscription->duration),
        ]);

        session()->flash('subscription_success', 'Subscription successfull');

        return redirect()->route('firm.dashboard');

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
