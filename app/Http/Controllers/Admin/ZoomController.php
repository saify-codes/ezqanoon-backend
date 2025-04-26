<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminOption;
use App\Services\ZoomService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ZoomController extends Controller
{
    public function __construct(private ZoomService $zoom) {}

    public function authenticate()
    {
        return redirect()->to($this->zoom->getOAuthUrl());
    }

    public function handleOAuthCallback(Request $request)
    {
        if (!$request->code) {
            Session::flash('zoom_oauth_error', 'Missing authorization code');
            return redirect()->route('integration.zoom.error');
        }

        try {
            $credentials = $this->zoom->exchangeAuthorizationCodeForTokens($request->code);
            AdminOption::set('zoom_access_token', $credentials['access_token']);
            AdminOption::set('zoom_refresh_token', $credentials['refresh_token']);
            Session::flash('zoom_oauth_success', true);
            return redirect()->route('integration.zoom.success');
        } catch (Exception $e) {
            Log::channel('error')->error('Zoom OAuth callback failed: ' . $e->getMessage());
            Session::flash('zoom_oauth_error', $e->getMessage());
            return redirect()->route('integration.zoom.error');
        }

    }

    public function createMeeting(Request $request)
    {


        try {

            dd($this->zoom->createMeeting('AAA', '2025-04-26 08:00'));
        } catch (Exception $e) {
            Log::channel('zoom')->error('Zoom create meeting failed', [
                'lawyer'  => getLawyerId(),
                'user'
            ]);
        }
    }

    public function success()
    {
        if (Session::has('zoom_oauth_error')) {
            return view('integrations.zoom.success');
        }
    
        return redirect('/');
    }

    public function error()
    {
        if (Session::has('zoom_oauth_error')) {
            return view('integrations.zoom.error')->with('error', Session::get('zoom_oauth_error'));
        }

        return redirect('/');
    }
}
