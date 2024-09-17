<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class RequestInventory extends Authenticatable
{
    
      protected $table = 'request_inventory';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'req_id','req_part_id','part_required','part_approve','part_reject',
        'part_status_pending','approve_by','approve_date','approve_status',
        'delivery_date','delivery_status',
        'created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
