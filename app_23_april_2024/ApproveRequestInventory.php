<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ApproveRequestInventory extends Authenticatable
{
    
      protected $table = 'approve_request_inventory';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'approve_id','approve_ref_no','req_id','req_ref_no','center_id',
        'remarks','part_required','qty','total','total_tax','net_total',
        'delivery_date','delivery_status',
        'created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
