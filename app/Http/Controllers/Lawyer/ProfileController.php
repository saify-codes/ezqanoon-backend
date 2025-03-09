<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile(){
        return view('lawyer.profile')->with('lawyer', Auth::user());
    }

    public function update(Request $request){
        
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'location'         => 'required|string|max:255',
            'specialization'   => 'required|string|max:255',
            'qualification'    => 'required|string|max:255',
            'experience'       => 'required|integer|min:0',
            'price'            => 'required|integer|min:0',
            'availability_from'=> 'required',
            'availability_to'  => 'required',
            'description'      => 'required|string'
        ]);

        Auth::user()->update([...$validated, 'is_profile_completed' => true]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    
}
