<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OTPController extends Controller
{
    use ApiResponseTrait;

    public function sendOTP(Request $request){
        /**
         * Validates phone number, generates and caches a new OTP
         * 
         * This method performs the following:
         * 1. Validates that a phone number was provided in the request
         * 2. Clears any existing OTP verification from session
         * 3. Generates a new OTP code
         * 4. Stores OTP in cache for 5 minutes with session ID as key
         * 5. send the generated OTP
         *
         * @param \Illuminate\Http\Request $request Request containing phone number
         * @throws \Illuminate\Validation\ValidationException If phone validation fails
         * @uses generateOTP() To create new OTP code
         */
        $request->validate([
            'phone' => 'required',
        ]);

        session()->forget('otp_verified');
        $otp = $this->generateOTP();

        Cache::put('otp_' . Session::getId(), $otp, now()->addMinutes(5));

        Log::info('OTP generated: ' . $otp);

        return response()->json(['message' => 'OTP sent successfully!']);
    }

    public function verifyOTP(Request $request){
        
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $otp = Cache::get('otp_' . Session::getId());
        
        if ($otp && $request->otp == $otp) {
            session()->put('otp_verified', true);
            return response()->json(['message' => 'OTP verified successfully!']);
        }

        return $this->errorResponse('Invalid OTP', 400);
    }

    private function generateOTP(){
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
