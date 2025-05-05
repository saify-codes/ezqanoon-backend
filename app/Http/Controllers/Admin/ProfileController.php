<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    public function profile()
    {
        return view('admin.profile')->with('admin', Auth::guard('admin')->user());
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'phone'             => 'nullable|phone', 
            'email'             => 'required|string|max:255',
        ]);
        Auth::guard('admin')->user()->update($validated);
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * uploadAvatar
     *
     * @param  mixed $request
     * @return JSONResponse
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Allow only images up to 2MB
        ], [
            'avatar.required'   => 'The avatar file is required.',
            'avatar.image'      => 'The uploaded file must be an image.',
            'avatar.mimes'      => 'Only JPEG, PNG, JPG, and webp file formats are allowed.',
            'avatar.max'        => 'The file size must not exceed 2MB.', // Custom message for max file size
        ]);

        if ($request->hasFile('avatar')) {
            // Store the file publicly in the 'storage/app/public/' directory
            $avatarPath = $request->file('avatar')->store('admins/' . Auth::guard('admin')->user()->id . '/avatars', 'public');
            // update path in db
            Auth::guard('admin')->user()->update(['avatar' => basename($avatarPath)]);

            return $this->successResponse('avatar uploaded', ['url' => asset("storage/$avatarPath")]);
        }

        return $this->errorResponse('no image uploaded');
    }
    
    /**
     * deleteAvatar
     *
     * @param  mixed $request
     * @return JSONResponse
     */
    public function deleteAvatar(Request $request)
    {
       Auth::guard('admin')->user()->update(['avatar' => null]);
        return $this->successResponse('avatar deleted');
    }
}
