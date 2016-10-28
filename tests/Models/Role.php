<?php

namespace Anavel\Crud\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'roles_users');
    }
}
