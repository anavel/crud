<?php


namespace Crudoado\Tests\Models;


use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'user_group_id');
    }
}