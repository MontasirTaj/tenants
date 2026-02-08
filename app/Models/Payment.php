<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $connection = 'mysql';

    protected $fillable = [
        'tenant_id', 'user_id', 'plan', 'currency', 'amount_total', 'status', 'type',
        'ip_address', 'user_agent',
        'stripe_session_id', 'stripe_payment_intent_id', 'stripe_customer_id', 'stripe_charge_id', 'receipt_url',
        'customer_details', 'metadata',
    ];

    protected $casts = [
        'customer_details' => 'array',
        'metadata' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'TenantID');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
