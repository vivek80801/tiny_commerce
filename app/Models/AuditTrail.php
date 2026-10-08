<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $request_id
 * @property string $event
 * @property string $ip_address
 * @property string|null $auditable_type
 * @property int|null $auditable_id
 * @property string $data
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|\Eloquent|null $auditable
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditTrail whereUserId($value)
 *
 * @mixin \Eloquent
 */
class AuditTrail extends Model
{
    protected $fillable = [
        'request_id',
        'event',
        'ip_address',
        'auditable_id',
        'auditable_type',
        'data',
        'user_id',
    ];

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
