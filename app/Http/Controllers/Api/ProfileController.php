<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ApiResponseTrait;


    /**
     * uploadAvatar
     *
     * @param  mixed $request
     * @return JSONResponse
     */
    public function uploadAvatar(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
        ], [
            'avatar.required'   => 'The avatar file is required.',
            'avatar.image'      => 'The uploaded file must be an image.',
            'avatar.mimes'      => 'Only JPEG, PNG, JPG, and GIF file formats are allowed.',
            'avatar.max'        => 'The file size must not exceed 2MB.', // Custom message for max file size
        ]);

        if ($request->hasFile('avatar')) {
            // Get the authenticated user
            $user = Auth::user();

            $fileName = time() . '.' . $request->file('avatar')->getClientOriginalExtension();

            // Define the storage path with user_id
            $filePath = "users/{$user->id}/";

            $absolutePath = $filePath . $fileName;

            // Store the file publicly in the 'storage/app/public/' directory
            $path = $request->file('avatar')->storePubliclyAs($filePath, $fileName, 'public');

            // update path in db
            $request->user()->update(['avatar' => $absolutePath]);

            return $this->successResponse('avatar uploaded', ['url' => asset(Storage::url($path))]);
        }

        return $this->errorResponse('no image uploaded');
    }

    /**
     * getProfile
     *
     * @param  mixed $request
     * @return JSONResponse
     * 
     */
    public function getProfile(Request $request)
    {

        return $this->successResponse('user profile', ['data' => Auth::user()->profile()]);
    }

    /**
     * updateProfile
     *
     * @param  mixed $request
     * @return JSONResponse
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'phone' => 'required',
        ]);

        $user = Auth::user();

        $user->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        return $this->successResponse('Profile updated successfully');
    }

    
    /**
     * updatePassword
     *
     * @param  mixed $request
     * @return JSONResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->successResponse('Password updated successfully');
    }
}
