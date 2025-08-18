<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterCoordinate extends Model
{
	/**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $table = 'master_coordinates';
    
    protected $fillable = [
        'provider_id', 'ticket_id','request_id','latitude', 'longitude'
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
