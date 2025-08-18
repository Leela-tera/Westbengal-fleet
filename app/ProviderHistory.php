<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderHistory extends Model
{
	/**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $table = 'provider_history';
    
    protected $fillable = [
        'provider_id','latitude', 'longitude'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at'
    ];

}
