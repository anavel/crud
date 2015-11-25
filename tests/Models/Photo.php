<?php


namespace Crudoado\Tests\Models;


use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}