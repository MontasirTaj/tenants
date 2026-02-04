<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\AdminPermission;

class AdminRole extends Model
{
    protected $table = 'admin_roles';

    protected $fillable = [
        'name',
        'description',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'admin_role_user', 'role_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_permission_role', 'role_id', 'permission_id');
    }
}
