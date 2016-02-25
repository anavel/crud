<?php


namespace Anavel\Crud\Tests\Models;


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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id');
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'imageable');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_users');
    }
}