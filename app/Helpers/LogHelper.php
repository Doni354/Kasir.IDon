<?php

namespace App\Helpers;

use App\Models\Logs;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    public static function logAction($action, $model, $recordId, $message, $oldData = [], $newData = [])
    {
        Logs::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'table_name' => $model,
            'record_id'  => $recordId,
            'msg'        => $message,
            'old_data'   => json_encode($oldData, JSON_PRETTY_PRINT),
            'new_data'   => json_encode($newData, JSON_PRETTY_PRINT),
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
        ]);
    }
}
