<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    public function profile()
    {
        return view('lawyer.profile')->with('lawyer', Auth::user());
    }

    public function update(Request $request)
    {
        switch(Auth::user()->role) {
            case 'ADMIN':
                $validated = $request->validate([
                    'name'              => 'required|string|max:255',
                    'licence_number'    => 'required|string|max:255',
                    'phone'             => 'nullable|phone:PK', 
                    'city'              => 'required|string|max:255',
                    'country'           => 'string|max:255',
                    'location'          => 'required|string|max:255',
                    'specialization'    => 'required|string|max:255',
                    'qualification'     => 'required|string|max:255',
                    'experience'        => 'required|integer|min:0',
                    'price'             => 'required|integer|min:0',
                    'availability_from' => 'required',
                    'availability_to'   => 'required',
                    'description'       => 'required|string',
                ]);
                Auth::user()->update([...$validated, 'is_profile_completed' => true]);
                break;

            // case 'USER':
            //     $validated = $request->validate([
            //         'name'  => 'required|string|max:255',
            //         'phone' => 'nullable|phone:PK',
            //     ]);
            //     Auth::user()->update($validated);
            //     break;
        }

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
            $avatarPath = $request->file('avatar')->store('users/' . Auth::user()->id . '/avatars', 'public');
            // update path in db
            $request->user()->update(['avatar' => basename($avatarPath)]);

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
        $request->user()->update(['avatar' => null]);
        return $this->successResponse('avatar deleted');
    }
}
