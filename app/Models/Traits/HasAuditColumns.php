<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasAuditColumns
{
    public static function bootHasAuditColumns()
    {
        static::creating(function ($model) {
            $user = Auth::user();
            if ($user) {
                $model->created_by = $user->id;
                $model->created_by_name = $user->name ?? $user->email ?? null;
            }
            $model->created_at = $model->created_at ?? now();
        });

        static::updating(function ($model) {
            $user = Auth::user();
            if ($user) {
                $model->last_modified_by = $user->id;
                $model->last_modified_by_name = $user->name ?? $user->email ?? null;
            }
            $model->last_modified_at = now();
        });

        static::addGlobalScope('not_deleted', function (Builder $builder) {
            $builder->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            });
        });
    }

    public function delete()
    {
        // Soft delete by marking is_deleted
        $user = Auth::user();
        $this->deleted_by = $user?->id;
        $this->deleted_by_name = $user?->name ?? $user?->email ?? null;
        $this->deleted_at = now();
        $this->is_deleted = 1;
        return $this->save();
    }

    public function restoreAudit()
    {
        $this->deleted_by = null;
        $this->deleted_at = null;
        $this->is_deleted = 0;
        return $this->save();
    }
}
