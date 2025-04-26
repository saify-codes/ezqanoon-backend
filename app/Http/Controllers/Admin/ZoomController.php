<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminOption;
use App\Services\ZoomService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZoomController extends Controller
{
    public function __construct(private ZoomService $zoom) {
    }

    public function authenticate(){
        return redirect()->to($this->zoom->getOAuthUrl());
    }

    public function handleOAuthCallback(Request $request){

        if ($request->code) {

            try {
                $credentials = $this->zoom->exchangeAuthorizationCodeForTokens($request->code);
                AdminOption::set('zoom_access_token', $credentials['access_token']);
                AdminOption::set('zoom_refresh_token', $credentials['refresh_token']);
                return "SUCCESS";

            } catch (Exception $e) {
                dd($e->getMessage());
            }
            
        }

        return "CODE MISSING";
    }
    
    public function createMeeting(Request $request){


        try {

        dd($this->zoom->createMeeting('AAA', '2025-04-26 08:00'));
          
    
        } catch (Exception $e) {
            Log::channel('zoom')->error('Zoom create meeting failed', [
                'lawyer'  => getLawyerId(),
                'user'      
            ]);
        }

    }
}
