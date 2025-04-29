<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ForgotRequest;
use App\Http\Requests\Api\ResetRequest;
use App\Http\Requests\Api\SigninRequest;
use App\Http\Requests\Api\SignupRequest;
use App\Mail\Api\Verification;
use App\Utils\Phone;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Handle user signup
     */
    public function signup(SignupRequest $request): JsonResponse
    {
        // Check if otp verified
        // if(Cache::get('user_verified_number_' . $request->phone) !== true){
        //     return $this->errorResponse('Phone number not verified', 401);
        // }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => Phone::convertToE164Format($request->phone, $request->country_code),
            'password' => Hash::make($request->password),
        ]);

        $this->generateAndSendToken($user, 'verify', env('NEXT_URL'));

        // remove the verification cache
        Cache::forget('user_verified_number_' . $request->phone);

        return $this->successResponse('User created', compact('user'), 201);
    }

    /**
     * Handle user signin with Remember Me and Token Expiry
     */
    public function signin(SigninRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse('Email not found', 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid email or password', 400);
        }

        if (!$user->verified_email) {
            return $this->errorResponse('Account is not verified', 401);
        }

        $expiryTime = $request->remember
            ? Carbon::now()->addDays(30)
            : Carbon::now()->addHours(12);

        $token = $user->createToken('auth_token', expiresAt: $expiryTime)->plainTextToken;

        return $this->successResponse('User logged in', [
            'user'      => $user->profile(),
            'token'     => $token,
            'expiresAt' => $expiryTime,
        ]);
    }

    /**
     * Handle user signout
     */
    public function signout(): JsonResponse
    {
        Auth::user()->tokens()->delete();
        return $this->successResponse('User logged out successfully');
    }

    /**
     * Handle "Forgot Password" request
     */
    public function forgot(ForgotRequest $request): JsonResponse
    {
        $user = $this->getUserByEmail($request->email);
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        // Generate token and send a reset link
        $this->generateAndSendToken($user, 'reset', env('NEXT_URL'));

        return $this->successResponse("Verification link sent to <u>{$request->email}</u>");
    }

    /**
     * Handle "Reset Password" request
     */
    public function reset(ResetRequest $request): JsonResponse
    {
        $user = $this->getUserByToken($request->token);
        if (!$user) {
            return $this->errorResponse('Invalid or expired token');
        }

        $user->update([
            'verification_token'        => null,
            'verification_token_expiry' => null,
            'password'                  => Hash::make($request->password),
        ]);

        return $this->successResponse("Password reset successfully");
    }

    /**
     * Resend verification link
     */
    public function sendVerificationLink(ForgotRequest $request): JsonResponse
    {
        $user = $this->getUserByEmail($request->email);
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        // Generate token and send a verification link
        $this->generateAndSendToken($user, 'verify', env('NEXT_URL'));

        return $this->successResponse("Verification link sent to <u>{$request->email}</u>");
    }

    /**
     * Verify user email
     */
    public function verify(Request $request): JsonResponse
    {
        if (empty($request->token)) {
            return $this->errorResponse('Token missing');
        }

        $user = $this->getUserByToken($request->token);

        if (!$user) {
            return $this->errorResponse('Invalid or expired token');
        }

        $user->update([
            'verified_email'            => true,
            'verification_token'        => null,
            'verification_token_expiry' => null,
        ]);

        return $this->successResponse('Email verified');
    }

    /**
     * Get user profile
     */
    public function profile(): JsonResponse
    {
        return $this->successResponse('User profile', Auth::user());
    }

    /**
     * Mask the given email address.
     */
    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email);

        if (strlen($local) <= 3) {
            $mask = $local[0] . '*' . $local[2];
        } elseif (strlen($local) <= 4) {
            $mask = $local[0] . '**' . $local[3];
        } else {
            $mask = substr($local, 0, 2)
                . str_repeat('*', strlen($local) - 4)
                . substr($local, -2);
        }

        return $mask . '@' . $domain;
    }

    /**
     * Retrieve user by email (or return null if not found).
     */
    private function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Retrieve user by valid (non-expired) verification token.
     * Return null if the token is invalid or expired.
     */
    private function getUserByToken(string $token): ?User
    {
        $user = User::where('verification_token', $token)->first();
        if (!$user) {
            return null;
        }

        // Check if token is expired
        if (Carbon::now()->isAfter($user->verification_token_expiry)) {
            return null;
        }

        return $user;
    }

    /**
     * Generate a new token, update the user, and send verification/reset mail.
     *
     * @param  User   $user
     * @param  string $endpoint  The endpoint ('reset' or 'verify') used to build the final URL
     * @param  string $baseUrl   The base URL from environment (NEXT_URL)
     */
    private function generateAndSendToken(User $user, string $endpoint, string $baseUrl): void
    {
        $token = bin2hex(random_bytes(32));

        $user->update([
            'verification_token'        => $token,
            'verification_token_expiry' => Carbon::now()->addHours(12),
        ]);

        $url = rtrim($baseUrl, '/') . '/' . $endpoint . '?token=' . $token;

        Mail::to($user->email)->queue(new Verification($url));
    }
}
