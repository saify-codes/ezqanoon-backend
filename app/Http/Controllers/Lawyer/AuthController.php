<?php

namespace App\Http\Controllers\Lawyer;

use Carbon\Carbon;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lawyer\ForgotRequest;
use App\Http\Requests\Lawyer\ResetRequest;
use App\Http\Requests\Lawyer\SigninRequest;
use App\Http\Requests\Lawyer\SignupRequest;
use App\Mail\Lawyer\Verification;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Handle lawyer signup
     */
    public function signup(SignupRequest $request)
    {
        $lawyer = Lawyer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $this->generateAndSendToken($lawyer, 'verify');

        return redirect()->route('lawyer.signin')->with('success','We have sent a verification mail to ' . $lawyer->email);
    }

    /**
     * Handle lawyer signin with Remember Me and Token Expiry
     */
    public function signin(SigninRequest $request)
    {
        $lawyer = Lawyer::where('email', $request->email)->first();

        if (!$lawyer) {
            return redirect()->route('lawyer.signin')->with('error','Email not found');
        }

        if (!Hash::check($request->password, $lawyer->password)) {
            return redirect()->route('lawyer.signin')->with('error','Invalid email or password');
        }

        if (!$lawyer->verified_email) {

            $resendLink = route('lawyer.verification.resend');
            $message    = "Account is not verified <a href='$resendLink?email=$lawyer->email'  class='underline'>Resend verification link</a>";

            return redirect()->route('lawyer.signin')->with('error', $message);
        }

        Auth::login($lawyer, $request->remember);

        return redirect()->route('lawyer.dashboard');
    }

    /**
     * Handle lawyer signout
     */
    public function signout()
    {
        Auth::logout();
        return redirect()->route('lawyer.signin')->with('success','Lawyer logged out successfully');
    }

    /**
     * Handle "Forgot Password" request
     */
    public function forgot(ForgotRequest $request): JsonResponse
    {
        $lawyer = $this->getLawyerByEmail($request->email);
        if (!$lawyer) {
            return $this->errorResponse('Lawyer not found', 404);
        }

        // Generate token and send a reset link
        $this->generateAndSendToken($lawyer, 'reset');

        return $this->successResponse("Verification link sent to <u>{$request->email}</u>");
    }

    /**
     * Handle "Reset Password" request
     */
    public function reset(ResetRequest $request): JsonResponse
    {
        $lawyer = $this->getLawyerByToken($request->token);
        if (!$lawyer) {
            return $this->errorResponse('Invalid or expired token');
        }

        $lawyer->update([
            'verification_token'         => null,
            'verification_token_expiry'  => null,
            'password'                   => Hash::make($request->password),
        ]);

        return $this->successResponse("Password reset successfully");
    }

    /**
     * Resend verification link
     */
    public function sendVerificationLink(ForgotRequest $request)
    {
        $lawyer = $this->getLawyerByEmail($request->email);
        
        if (!$lawyer) {
            return redirect()->route('lawyer.signin')->with('error','Lawyer not found');
        }

        // Generate token and send a verification link
        $this->generateAndSendToken($lawyer, 'verify');

        return redirect()->route('lawyer.signin')->with("success","Verification link sent to <u>{$request->email}</u>");
    }

    /**
     * Verify lawyer email
     */
    public function verify(Request $request)
    {
        if (empty($request->token)) {
            return $this->errorResponse('Token missing');
        }

        $lawyer = $this->getLawyerByToken($request->token);

        if (!$lawyer) {
            return $this->errorResponse('Invalid or expired token');
        }

        $lawyer->update([
            'verified_email'            => true,
            'verification_token'        => null,
            'verification_token_expiry' => null,
        ]);

        return $this->successResponse('Email verified');
    }

    /**
     * Get the lawyer's profile (if you need a route for it)
     */
    public function profile(): JsonResponse
    {
        return $this->successResponse('Lawyer profile', Auth::user());
    }

    /**
     * Retrieve lawyer by email (or return null if not found).
     */
    private function getLawyerByEmail(string $email): ?Lawyer
    {
        return Lawyer::where('email', $email)->first();
    }

    /**
     * Retrieve lawyer by valid (non-expired) verification token.
     * Return null if the token is invalid or expired.
     */
    private function getLawyerByToken(string $token): ?Lawyer
    {
        $lawyer = Lawyer::where('verification_token', $token)->first();
        if (!$lawyer) {
            return null;
        }

        // Check if token is expired
        if (Carbon::now()->isAfter($lawyer->verification_token_expiry)) {
            return null;
        }

        return $lawyer;
    }

    /**
     * Generate a new token, update the lawyer, and send verification/reset mail.
     *
     * @param  Lawyer   $lawyer
     * @param  string   $endpoint  The endpoint ('reset' or 'verify') used to build the final URL
     */
    private function generateAndSendToken(Lawyer $lawyer, string $endpoint): void
    {
        $token = bin2hex(random_bytes(32));

        $lawyer->update([
            'verification_token'        => $token,
            'verification_token_expiry' => Carbon::now()->addHours(12),
        ]);

        $url = URL::to($endpoint) . '?token=' . $token;

        Mail::to($lawyer->email)->queue(new Verification($url));
    }
}
