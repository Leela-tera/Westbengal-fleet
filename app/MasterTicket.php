<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterTicket extends Model
{

     protected $table = 'master_tickets';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'district',
        'mandal',
		'lat',
		'log',
		'downdate',
		'downtime',
		'up_date',
		'up_time',
		'downreason',
		'downreasonindetailed',
		'subsategory',
		'ticketid',
                'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at'
    ];

    public function userrequest()
    {
        return $this->hasMany('App\UserRequests');
    }

   /**
     * Master ticket Linked
     */
    public function jointicket()
    {
        return $this->hasOne('App\UserRequests','booking_id');
    }


}
