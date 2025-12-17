<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, $model = null, array $changes = null)
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'model_type' => $model ? (is_object($model) ? get_class($model) : null) : null,
            'model_id' => $model && is_object($model) && isset($model->id) ? $model->id : null,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
