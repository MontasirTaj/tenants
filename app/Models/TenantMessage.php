<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantMessage extends Model
{
    protected $connection = 'tenant';
    protected $table = 'messages';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
    ];

    public function conversation()
    {
        return $this->belongsTo(TenantConversation::class, 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(TenantUser::class, 'sender_id');
    }
}
