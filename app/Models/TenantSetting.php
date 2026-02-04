<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    protected $connection = 'tenant';
    protected $table = 'tenant_settings';

    protected $fillable = [
        'name',
        'primary_color',
        'logo_path',
    ];
}
