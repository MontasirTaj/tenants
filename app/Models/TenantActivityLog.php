<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantActivityLog extends Model
{
    protected $connection = 'tenant';

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'guard',
        'event',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'ip_address',
        'user_agent',
        'extra',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }
}
