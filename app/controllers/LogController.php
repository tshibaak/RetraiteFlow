<?php

use App\controllers\Controller;
use App\Models\Log;

class LogController extends Controller{
   protected  function getUserIp() 
   {
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        // IP partagée par proxy
        return $_SERVER['HTTP_CLIENT_IP'];
    } 
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
    {
        // IP réelle derrière un proxy/load balancer
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } 
    else 
    {
        // IP directe
        return $_SERVER['REMOTE_ADDR'];
    }
  }
// Enreigistrer un log d application 
  public function store(int $user,string $action, string $detail){
      $log = new Log();
      $logs = [
          'user_id' => $user,
          'action' => $action,
          'detail' => $detail,
          'ip' => $this->getUserIp(),
          'created_at' => date('Y-m-d H:i:s')
      ];
      $log->create($logs);
  }
  
}

?>