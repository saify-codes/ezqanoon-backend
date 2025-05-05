<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SigninRequest;
use App\Models\Admin;
use App\Models\AdminOption;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    use ApiResponseTrait;


    /**
     * Handle lawyer signin with Remember Me and Token Expiry
     */
    public function signin(SigninRequest $request)
    {
        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return redirect()->route('admin.signin')->with('error','Email not found');
        }

        if (!Hash::check($request->password, $admin->password)) {
            return redirect()->route('admin.signin')->with('error','Invalid email or password');
        }

        if(AdminOption::get('2fa_enabled')){

            Cache::put('admin_2fa_code', $this->generateOTP());
            return redirect()->route('admin.2fa');

        }

        Auth::guard('admin')->login($admin, $request->remember);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Handle lawyer signout
     */
    public function signout()
    {
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.signin')->with('success','logged out successfully');
    }

    private function generateOTP()
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
