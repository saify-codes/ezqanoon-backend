<?php

use App\Models\LawyerNotification;

function active_class($path, $active = 'active') {
  return call_user_func_array('Request::is', (array)$path) ? $active : '';
}

function is_active_route($path) {
  return call_user_func_array('Request::is', (array)$path) ? 'true' : 'false';
}

function show_class($path) {
  return call_user_func_array('Request::is', (array)$path) ? 'show' : '';
}

function notify_lawyer($lawyerId, $title, $body = null){

  LawyerNotification::create([
    'lawyer_id' => $lawyerId,
    'title'     => $title,
    'body'      => $body,
  ]);

}