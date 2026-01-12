<?php

use App\Models\ActivityLog;

function activity_log($userId, $action, $description = null)
{
    ActivityLog::create([
        'user_id' => $userId,
        'action' => $action,
        'description' => $description
    ]);
}
