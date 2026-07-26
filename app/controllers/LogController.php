<?php
namespace App\Controllers;

use App\Models\Log;
use App\Models\User;

class LogController extends Controller
{
    protected Log $log;

    public function __construct()
    {
        $this->log = new Log();
    }

    public function store(?int $userId, string $action, string $detail): void
    {
        if (!$userId) {
            return;
        }

        try {
            $this->log->create([
                'user_id' => $userId,
                'action' => mb_substr($action, 0, 60),
                'detail' => $detail,
                'ip' => $this->clientIp(),
            ]);
        } catch (\Throwable $e) {
            // Les logs ne doivent jamais bloquer l'action principale.
        }
    }
}
