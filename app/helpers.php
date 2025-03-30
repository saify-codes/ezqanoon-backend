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

function notifyUser($userId, $title, $body = null){

  LawyerNotification::create([
    'lawyer_id' => $userId,
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

function getPermissionsList(){
    return [

      'Cases' => [  
        'cases:view'    => 'View Cases',
        'cases:create'  => 'Create Cases',
        'cases:edit'    => 'Edit Cases',
        'cases:delete'  => 'Delete Cases',
      ],

      'Clients' => [
        'clients:view'    => 'View Clients',
        'clients:create'  => 'Create Clients',
        'clients:edit'    => 'Edit Clients',
        'clients:delete'  => 'Delete Clients',
      ],

      'Appointments' => [
        'appointments:view'    => 'View Appointments',
        'appointments:create'  => 'Create Appointments',
        'appointments:edit'    => 'Edit Appointments',
        'appointments:delete'  => 'Delete Appointments',
      ],

      'Menu' => [
        'manage:client'       => 'Manage Client',
        'manage:case'         => 'Manage Case',
        'manage:appointment'  => 'Manage Appointment',
        'manage:billing'      => 'Manage Billing',
        'manage:report'       => 'Manage Report',
      ],
      
    ];
}