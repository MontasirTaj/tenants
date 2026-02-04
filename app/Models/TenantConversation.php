<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantConversation extends Model
{
    protected $connection = 'tenant';
    protected $table = 'conversations';

    protected $fillable = [
        'type',
        'title',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(TenantUser::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(TenantConversationParticipant::class, 'conversation_id');
    }

    public function messages()
    {
        return $this->hasMany(TenantMessage::class, 'conversation_id');
    }
}
