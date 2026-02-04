<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPermission extends Model
{
    protected $table = 'admin_permissions';

    protected $fillable = [
        'name',
        'description',
    ];

    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_permission_role');
    }
}
