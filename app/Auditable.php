<?php

namespace App;

use App\Models\AuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            self::changeLog($model, 'created', null, $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $changes = $model->getChanges();

            if (empty($changes)) {
                return;
            }

            $old = array_intersect_key($model->getOriginal(), $changes);
            self::changeLog($model, 'updated', $old, $changes);
        });

        static::deleted(function (Model $model) {
            self::changeLog($model, 'deleted', $model->getOriginal(), null);
        });
    }

    protected static function changeLog(
        Model $model,
        string $event,
        mixed $old,
        mixed $new,
    ): void {
        $hidden = $model->getHidden();
        $hidden = [
            ...$hidden,
            'created_at',
            'updated_at',
            'id',
        ];

        if ($old) {
            $old = array_diff_key(
                $old,
                array_flip($hidden)
            );
        }

        if ($new) {
            $new = array_diff_key(
                $new,
                array_flip($hidden)
            );
        }

        $newData = [
            'old' => $old,
            'new' => $new,
        ];

        AuditTrail::create([
            'request_id' => request()->request_id,
            'event' => $event,
            'ip_address' => Request::ip(),
            'auditable_id' => $model->getKey(),
            'auditable_type' => $model::class,
            'data' => json_encode($newData),
            'user_id' => Auth::id() ?? null,
        ]);

    }
}
