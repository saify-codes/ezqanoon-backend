<?php

use App\Models\Admin;
use App\Models\Notification;
use App\Utils\Icon;

function activeClass($path, $active = 'active') {
  return call_user_func_array('Request::is', (array)$path) ? $active : '';
}

function isActiveRoute($path) {
  return call_user_func_array('Request::is', (array)$path) ? 'true' : 'false';
}

function showClass($path) {
  return call_user_func_array('Request::is', (array)$path) ? 'show' : '';
}

function notifyAllAdmin($title, $body = null, $icon = null){
  $adminIds = Admin::pluck('id');
  
  $notifications  = [];
  $now            = now();
  
  foreach ($adminIds as $adminId) {
    $notifications[] = [
        'admin_id'   => $adminId,
        'title'      => $title,
        'body'       => $body,
        'created_at' => $now,
        'updated_at' => $now,
    ];
  }
  
  Notification::insert($notifications);  // Bulk insert
}

function notifyAdmin($adminId, $title, $body = null, $icon = null){
  Notification::create([
    'admin_id'  => $adminId,
    'icon'      => $icon ?? Icon::email(),
    'title'     => $title,
    'body'      => $body,
  ]);
}

function notifyFirm($firmId, $title, $body = null, $icon = null){
  Notification::create([
    'firm_id'   => $firmId,
    'icon'      => $icon ?? Icon::email(),
    'title'     => $title,
    'body'      => $body,
  ]);
}

function notifyLawyer($lawyerId, $title, $body = null, $icon = null){
  Notification::create([
    'lawyer_id' => $lawyerId,
    'icon'      => $icon ?? Icon::email(),
    'title'     => $title,
    'body'      => $body,
  ]);
}

function notifyTeamMember($teamId, $title, $body = null, $icon = null,){
  Notification::create([
    'team_id'   => $teamId,
    'icon'      => $icon ?? Icon::email(),
    'title'     => $title,
    'body'      => $body,
  ]);
}

function getPermissionsList(){
    return [

      'Dashboard' => [
        'dashboard:stats'    => 'Can see stats',
      ],

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

function getSpecializationList(){
  return [
    'Family Law',
    'Civil Law',
    'Criminal Law',
    'Property',
    'Corporate & Business law',
    'Employment and Labour Laws',
    'Intellectual Property',
    'Taxation & Financial law',
    'Custom Overseas Matter',
    'Immigration & Nationality matters',
    'Constitution & Administration laws',
    'Technology, Media and Cyber laws',
    'Shariah & Islamic Law Services',
    'Alternative Dispute Resolution',
    'Banking Laws',
    'Aviation laws',
  ];
}