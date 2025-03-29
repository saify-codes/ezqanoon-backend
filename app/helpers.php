<?php

use App\Models\LawyerNotification;
use Illuminate\Support\Facades\Auth;

function activeClass($path, $active = 'active') {
  return call_user_func_array('Request::is', (array)$path) ? $active : '';
}

function isActiveRoute($path) {
  return call_user_func_array('Request::is', (array)$path) ? 'true' : 'false';
}

function showClass($path) {
  return call_user_func_array('Request::is', (array)$path) ? 'show' : '';
}

function notifyLawyer($lawyerId, $title, $body = null){

  LawyerNotification::create([
    'lawyer_id' => $lawyerId,
    'title'     => $title,
    'body'      => $body,
  ]);

}

function getLawyerId(){
  
    if (!Auth::check()) {
        throw new \Exception('No active login session found');
    }
    
    return Auth::user()->role === 'USER' ? Auth::user()->lawyer_id : Auth::user()->id;
}
