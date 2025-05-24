<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /**
     * Handle password update.
     */
    public function update(Request $request)
    {
        // Validate user input
        $request->validate([
            'password' => 'required|confirmed|min:8',
        ]);

        // Retrieve the logged-in lawyer/user
        Auth::guard('firm')->user()->update([
            'password' =>Hash::make($request->password),
        ]);

        // Redirect back or wherever you'd like, with a success message
        return redirect()->back()->with('success', 'Password changed successfully!');
    }
}
