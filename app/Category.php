<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'icon', 'user_id',
    ];

    public function envelops()
    {
        return $this->hasMany('App\Envelop');
    }

}
