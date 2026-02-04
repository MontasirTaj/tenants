<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $table = 'tenants';
    protected $primaryKey = 'TenantID';
    public $timestamps = true;
    protected $fillable = [
        'TenantName', 'OwnerName', 'PhoneNumber', 'Subdomain', 'Email', 'Address', 'Logo',
        'Plan', 'JoinDate', 'SubscriptionStartDate', 'SubscriptionEndDate', 'TrialEndDate',
        'IsActive', 'Notes', 'Status', 'CUserID', 'CDate', 'UUserID', 'UDate', 'DUserID', 'DDate',
        'DBName', 'DBHost', 'DBUser', 'DBPassword', 'DBPort'
    ];
}
