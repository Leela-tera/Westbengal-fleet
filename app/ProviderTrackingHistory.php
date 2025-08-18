<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderTrackingHistory extends Model
{
    /**
     * The services that belong to the user.
     */
    public function provider()
    {
        return $this->belongsTo('App\Provider');
    }
}
