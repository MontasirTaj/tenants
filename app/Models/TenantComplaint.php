<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantComplaint extends Model
{
    protected $table = 'tenant_complaints';

    // تخزين البلاغات في قاعدة البيانات الرئيسية (mysql)
    protected $connection = 'mysql';

    protected $fillable = [
        'tenant_id',
        'tenant_subdomain',
        'reporter_id',
        'reporter_name',
        'reporter_email',
        'subject',
        'message',
        'attachment_path',
        'status',
        'admin_reply',
        'admin_user_id',
        'admin_replied_at',
        'tenant_seen_at',
    ];

    protected $casts = [
        'admin_replied_at' => 'datetime',
        'tenant_seen_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'TenantID');
    }
}
