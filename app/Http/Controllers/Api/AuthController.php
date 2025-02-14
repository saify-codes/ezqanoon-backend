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
use App\Http\Requests\Api\SigninRequest;
use App\Http\Requests\Api\SignupRequest;
use App\Mail\Api\Verification;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Handle user signup
     */
    public function signup(SignupRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

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

        $expiryTime = $request->remember ? Carbon::now()->addDays(30) : Carbon::now()->addHours(12);

        $token = $user->createToken('auth_token', expiresAt: $expiryTime)->plainTextToken;

        return $this->successResponse('User logged in', [
            'user'      => $user,
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
     * forgot
     *
     * @param  ForgotRequest $request
     * @return JsonResponse
     */
    public function forgot(ForgotRequest $request): JsonResponse
    {
        // Generate a random token (for example, 16 hex characters)
        $token = bin2hex(random_bytes(32));

        // Look up the user by email.
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->update([
            'verification_token' => $token,
            'verification_token_expiry' => Carbon::now()->addMinute(5),
        ]);

        $url = rtrim(env('NEXT_URL'), '/') . '/verify?token=' . $token;

        Mail::to($request->email)->send(new Verification($url));

        return $this->successResponse("verification link sent to <u>{$request->email}</u>");
    }

    /**
     * verify
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {

        if (empty($request->token)) {
            return $this->errorResponse('token missing');
        }

        $user = User::where('verification_token', $request->token)->first();

        if (!$user) {
            return $this->errorResponse('invalid verification link');
        }

        if (Carbon::now()->isAfter($user->verification_token_expiry)) {
            return $this->errorResponse('verification link expired');
        }

        $user->update([
            'verified_email' => true,
            'verification_token' => null,
            'verification_token_expiry' => null,
        ]);

        return $this->successResponse('email verified');
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
     *
     * @param string $email
     * @return string
     */
    private function maskEmail(string $email): string
    {
        // Split the email into the local and domain parts
        [$local, $domain] = explode('@', $email);

        if (strlen($local) <= 3) {
            $mask = $local[0] . '*' . $local[2];
        } else if (strlen($local) <= 4) {
            $mask = $local[0] . '**' . $local[3];
        } else {
            $mask =  substr($local, 0, 2) . str_repeat('*', strlen($local) - 4) . substr($local, -2);
        }

        // Return the masked email address
        return $mask . '@' . $domain;
    }
}
