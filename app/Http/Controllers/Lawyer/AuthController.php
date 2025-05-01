<?php

namespace App\Http\Controllers\Lawyer;

use Carbon\Carbon;
use App\Models\Lawyer;
use Illuminate\Http\Request;
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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Handle lawyer signup
     */
    public function signup(SignupRequest $request)
    {       

        // Check if OTP verification is done
        if(Session::get('lawyer_verified_number') !== $request->phone) {
            return $this->errorResponse('Phone number not verified');
        }

        $lawyer = Lawyer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $this->sendEmailVerificationLink($lawyer);

        Session::flash('success',"We have sent a verification mail to <strong class='text-decoration-underline'>$lawyer->email</strong>");

        return $this->successResponse('account created');
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

        if (!$lawyer->email_verified_at) {

            $resendLink = route('lawyer.verification.resend');
            $message    = "Account is not verified <a href='$resendLink?email=$lawyer->email'  class='text-decoration-underline'>Resend verification link</a>";

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
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('lawyer.signin')->with('success','logged out successfully');
    }

    /**
     * Handle "Forgot Password" request
     */
    public function forgot(ForgotRequest $request)
    {
        // Generate token and send a reset link
        
        $lawyer = Lawyer::where('email', $request->email)->first();
        $this->sendPasswordResetLink($lawyer, 'reset');
        return redirect()->route('lawyer.forgot')->with('success', "Pasword reset link sent to <u>{$request->email}</u>");
    }

    /**
     * Handle "Reset Password" request
     */
    public function reset(ResetRequest $request)
    {
        $lawyerID = DB::table('lawyer_password_reset_tokens')->where('token', $request->token)->value('lawyer_id');
        $lawyer   = Lawyer::find($lawyerID);
        
        if (!$lawyer) {
            return back()->with('error','Invalid or expired token');
        }
        
        return redirect()->route('lawyer.signin')->with('success','Password changed successfully');
    }

    /**
     * Resend verification link
     */
    public function sendVerificationLink(ForgotRequest $request)
    {
        $lawyer = Lawyer::where('email',$request->email)->first();
        
        if (!$lawyer) {
            return redirect()->route('lawyer.signin')->with('error','Lawyer not found');
        }

        $this->sendEmailVerificationLink($lawyer);

        return redirect()->route('lawyer.signin')->with("success","Verification link sent to <strong class='text-decoration-underline'>{$request->email}</strong>");
    }

    /**
     * Verify lawyer email
     */
    public function verify(Request $request)
    {
        if (empty($request->token)) {
            return redirect()->route('lawyer.signin');
        }
        
        $lawyerID = DB::table('lawyer_verification_tokens')->where('token', $request->token)->value('lawyer_id');
        $lawyer   = Lawyer::find($lawyerID);

        if (!$lawyer) {
            return redirect()->route('lawyer.signin')->with('error','Invalid or expired token');
        }

        $lawyer->update([
            'email_verified_at' => Carbon::now()->toDateTimeString(),
        ]);

        return redirect()->route('lawyer.signin')->with('success', 'Email verified');
    }

    private function sendEmailVerificationLink(Lawyer $lawyer): void
    {
        $token  = bin2hex(random_bytes(32));
        $url    = URL::to("/verify/$token");

        Mail::to($lawyer->email)->queue(new Verification($url));
        DB::table('lawyer_verification_tokens')->insert([
            'lawyer_id'     => $lawyer->id,
            'token'         => $token,
        ]);

    }

    private function sendPasswordResetLink(Lawyer $lawyer): void
    {
        $token = bin2hex(random_bytes(32));
        $url   = URL::to("/reset/$token");

        Mail::to($lawyer->email)->queue(new Verification($url));
        DB::table('lawyer_password_reset_tokens')->insert([
            'lawyer_id'     => $lawyer->id,
            'token'         => $token,
        ]);
    }
}
