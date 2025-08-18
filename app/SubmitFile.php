<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubmitFile extends Model
{
	/**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $table = 'submitfiles';
    
    protected $fillable = [
        'provider_id', 'ticket_id','request_id','category', 'subcategory', 'description', 'before_image', 'after_image','materials'
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
