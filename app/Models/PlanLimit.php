<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanLimit extends Model
{
    protected $table = 'plan_limits';

    protected $fillable = [
        'plan',
        'max_users',
    ];
}
