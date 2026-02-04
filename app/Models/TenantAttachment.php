<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantAttachment extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'attachments';

    protected $fillable = [
        'type',
        'original_name',
        'extension',
        'mime_type',
        'path',
        'disk',
        'size_bytes',
        'page_count',
        'uploaded_by',
        'processing_response',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'page_count' => 'integer',
        'processing_response' => 'array',
        'processed_at' => 'datetime',
    ];

    public function uploader()
    {
        return $this->belongsTo(TenantUser::class, 'uploaded_by');
    }

    public function processor()
    {
        return $this->belongsTo(TenantUser::class, 'processed_by');
    }
}
