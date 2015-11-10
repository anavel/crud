<?php


namespace Crudoado\Tests\Models;


use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(UserTranslations::class, 'user_id');
    }
}