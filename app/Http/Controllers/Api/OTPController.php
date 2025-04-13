<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TwilioService;
use App\Traits\ApiResponseTrait;
use App\Utils\Phone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OTPController extends Controller
{
    use ApiResponseTrait;

    public function __construct(public TwilioService  $twilioService) {
    
    }

    public function sendOTP(Request $request)
    {
        
        $request->validate([
            'phone' => [
                'required',
                function ($_, $value, $fail) use ($request) {
                    if(!Phone::isValid($value, $request->country_code)){
                        $fail('The phone number format is invalid.');
                    }
                }
            ],
            'country_code' => 'required'
        ]);

        $otp = $this->generateOTP();

        Cache::put('user_otp_' . $request->phone, $otp, now()->addMinutes(5));

        Log::info('User OTP generated: ' . $otp);

        $message = "Your OTP is: $otp. It is valid for 5 minutes.";
        $this->twilioService->sendSMS(Phone::convertToE164Format($request->phone, $request->country_code), $message);

        return $this->successResponse('OTP sent successfully!');
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp'   => 'required|numeric',
            'phone' => 'required'
        ]);

        $otp = Cache::get('user_otp_' . $request->phone);

        if (!$otp) {
            return $this->errorResponse('OTP expired or invalid', 400);
        }

        // Verify both OTP and phone number match
        if ($otp == $request->otp) {
            Cache::put('user_verified_number_' . $request->phone, true);
            return response()->json(['message' => 'OTP verified successfully!']);
        }

        return $this->errorResponse('Invalid OTP or phone number', 400);
    }

    private function generateOTP()
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
