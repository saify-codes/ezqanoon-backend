<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Services\TwilioService;
use App\Traits\ApiResponseTrait;
use App\Utils\Phone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OTPController extends Controller
{
    use ApiResponseTrait;

    public function __construct(public TwilioService $twilioService) {
    }

    public function sendOTP(Request $request)
    {
        $request->validate([
            'country_code'  => 'required_with:phone',
            'phone'         => 'required|phone:' . $request->country_code,
        ]);

        $otp     = $this->generateOTP();
        $message = "Your OTP is: $otp. It is valid for 5 minutes.";

        $this->twilioService->sendSMS($request->phone, $message);

        Cache::put("otp_" . Session::id(), ['code' => $otp, 'phone' => $request->phone], now()->addMinutes(5));
        Log::info('Lawyer OTP generated: ' . $otp);

        return $this->successResponse('OTP sent successfully!');
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp'   => 'required|numeric',
        ]);

        $otp = Cache::get("otp_" . Session::id());

        if (!$otp) {
            return $this->errorResponse('OTP expired', 400);
        }

        if ($otp['code'] == $request->otp) {
            Session::flash('lawyer_verified_number', $otp['phone']);
            return $this->successResponse('OTP verified successfully!');
        }

        return $this->errorResponse('Invalid OTP', 400);
    }

    private function generateOTP()
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
