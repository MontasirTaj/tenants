<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantConversationParticipant extends Model
{
    protected $connection = 'tenant';
    protected $table = 'conversation_participants';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'joined_at',
        'last_read_at',
    ];

    protected $dates = [
        'joined_at',
        'last_read_at',
    ];

    public function conversation()
    {
        return $this->belongsTo(TenantConversation::class, 'conversation_id');
    }

    public function user()
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }
}
