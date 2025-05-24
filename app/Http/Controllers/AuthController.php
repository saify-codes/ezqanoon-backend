<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotRequest;
use App\Http\Requests\ResetRequest;
use App\Http\Requests\SigninRequest;
use App\Http\Requests\SignupRequest;
use App\Mail\AccountActivationEmail;
use App\Mail\AccountVerificationEmail;
use App\Mail\Lawyer\Verification;
use App\Mail\PasswordResetEmail;
use App\Models\EmailVerificationToken;
use App\Models\Firm;
use App\Models\PasswordResetToken;
use App\Models\Team;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    use ApiResponseTrait;

  
    public function signup(SignupRequest $request)
    {

        // Check if OTP verification is done
        // if(Cache::get('lawyer_verified_number_' . $request->phone) !== true) {
        //     return $this->errorResponse('Phone number not verified');
        // }

        switch ($request->signup_type) {
            case 'FIRM':
                $user = Firm::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'phone'    => $request->phone,
                    'password' => Hash::make($request->password),
                ]);
                break;

            case 'LAWYER':
                $user = Lawyer::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'phone'    => $request->phone,
                    'password' => Hash::make($request->password),
                ]);
                break;
        }

        Session::flash('success', "We have sent a verification mail to <strong class='text-decoration-underline'>$user->email</strong>");
        $this->sendEmailActivationLink($user);
        return $this->successResponse('account created');
    }
   
    public function signin(SigninRequest $request)
    {
        switch ($request->signin_type) {
            case 'FIRM':
                $user = Firm::where('email', $request->email)->first();
                break;

            case 'LAWYER':
                $user = Lawyer::where('email', $request->email)->first();
                break;
            
            case 'TEAM':
                $user = Team::where('email', $request->email)->first();
                break;
        }


        if (!Hash::check($request->password, $user->password)) {
            return redirect()->route('signin')->with('error', 'Invalid email or password');
        }

        if (!($user instanceof Team ) && !$user->email_verified_at) {

            $resendLink = route('verification.resend');
            $message    = "Account is not verified <a href='$resendLink?email=$user->email&type=$request->signin_type'  class='text-decoration-underline'>Resend verification link</a>";

            return redirect()->route('signin')->with('error', $message);
        }

        // Get the guard name from the signin type (e.g., 'FIRM' or 'LAWYER')
        $guardName = strtolower($request->signin_type);

        // Log the user in using the appropriate guard and "remember me" option
        Auth::guard($guardName)->login($user, $request->remember);

        // Build the dashboard route name dynamically (e.g., 'firm.dashboard' or 'lawyer.dashboard')
        $dashboardRoute = $guardName . '.dashboard';

        // Redirect the user to their dashboard
        return redirect()->route($dashboardRoute);

    }

    public function signout()
    {
        $guard = Auth::user() instanceof Firm ? 'firm' : 'lawyer';

        Auth::guard($guard)->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('signin')->with('success', 'logged out successfully');
    }

    public function forgot(ForgotRequest $request)
    {
        switch ($request->type) {
            case 'FIRM':
                $user = Firm::where('email', $request->email)->first();
                break;

            case 'LAWYER':
                $user = Lawyer::where('email', $request->email)->first();
                break;
        }

        $this->sendPasswordResetLink($user);
        return redirect()->route('forgot')->with('success', "Pasword reset link sent to <u>{$request->email}</u>");
    }

    public function reset(ResetRequest $request)
    {
        switch ($request->type) {
            case 'FIRM':
                $userId = PasswordResetToken::where('token', $request->token)->value('firm_id');
                $user   = Firm::find($userId);

                break;
                
            case 'LAWYER':
                $userId = PasswordResetToken::where('token', $request->token)->value('lawyer_id');
                $user   = Lawyer::find($userId);
                break;

        }

        if (!$user) {
            return redirect()->route('signin')->with('error', 'Reset link expired');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('signin')->with('success', 'Password changed successfully');
    }

    public function sendActivationLink(ForgotRequest $request)
    {
        switch ($request->type) {
            case 'FIRM':
                $user = Firm::where('email', $request->email)->first();
                break;

            case 'LAWYER':
                $user = Lawyer::where('email', $request->email)->first();
                break;
        }

        if (!$user) {
            return redirect()->route('signin')->with('error', 'Lawyer not found');
        }

        $this->sendEmailActivationLink($user);

        return redirect()->route('signin')->with("success", "Verification link sent to <strong class='text-decoration-underline'>{$request->email}</strong>");
    }

    public function verify(Request $request)
    {
        if (empty($request->token)) {
            return redirect()->route('signin');
        }

        switch ($request->type) {
            case 'FIRM':
                $userId = EmailVerificationToken::where('token', $request->token)->value('firm_id');
                $user   = Firm::find($userId);

                break;
                
            case 'LAWYER':
                $userId = EmailVerificationToken::where('token', $request->token)->value('lawyer_id');
                $user   = Lawyer::find($userId);
                break;

            default:
                return redirect()->route('signin');
        }


        if (!$user) {
            return redirect()->route('signin')->with('error', 'Invalid or expired token');
        }

        $user->update([
            'email_verified_at' => Carbon::now()->toDateTimeString(),
        ]);

        return redirect()->route('signin')->with('success', 'Email verified');
    }

    private function sendEmailActivationLink(Firm|Lawyer $user): void
    {
        $token = bin2hex(random_bytes(32));
        $type  = $user instanceof Firm ? 'FIRM' : 'LAWYER';
        $url   = URL::to("/verify/$token") . "?type=$type";
        $data = [
            'token'      => $token,
            "{$type}_id" => $user->id,
        ];

        EmailVerificationToken::insert($data);
        Mail::to($user->email)->queue(new AccountActivationEmail($url));
        // Mail::to($user->email)->send(new AccountActivationEmail($url));
    }

    private function sendPasswordResetLink(Firm|Lawyer $user): void
    {
        $token = bin2hex(random_bytes(32));
        $type  = $user instanceof Firm ? 'FIRM' : 'LAWYER';
        $url   = URL::to("/reset/$type/$token");
        $data = [
            'token'      => $token,
            "{$type}_id" => $user->id,
        ];

        PasswordResetToken::insert($data);
        // Mail::to($user->email)->queue(new PasswordResetEmail($url));
        Mail::to($user->email)->send(new PasswordResetEmail($url));
    }
}
