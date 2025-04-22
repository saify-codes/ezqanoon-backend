<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AeroDropController extends Controller
{

    use ApiResponseTrait;
    /**
     * Handle the file upload from AeroDrop.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Upload failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Check if the file exists in the request.
        if ($request->hasFile('file')) {

            $file = $request->file('file');

            // Store the file in public storage
            $path = $file->store('aerodrop', 'public');

            return $this->successResponse('Upload successful', [
                'file'          => basename($path),
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getMimeType(),
                'file_path'     => $path,
            ]);
        }

        return $this->errorResponse('No file uploaded');
    }
}
