<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Envelop extends Model 
{
    protected $fillable = [
        'category_id', 'name', 'icon', 'amount', 'type', 'created_at'
    ];

   

    
}
