<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $settings = json_decode(Option::get('settings', firmId:  Auth::guard('firm')->id()));
        return view('firm.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'calendar' => 'required|array',
            // Add other validation rules here if you have more data
        ]);

        Option::set('settings', json_encode($validated), firmId:  Auth::guard('firm')->id());

        return redirect()
            ->route('firm.settings.index')
            ->with('success', 'Settings successfully saved!');
    }
}
