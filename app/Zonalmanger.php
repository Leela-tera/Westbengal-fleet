<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zonalmanger extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table="zonal_managers";
    protected $fillable = [
        'Name'
        
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         'created_at'
    ];
	
	
}
