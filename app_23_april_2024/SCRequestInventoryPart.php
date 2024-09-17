<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SCRequestInventoryPart extends Authenticatable
{
    
      protected $table = 'sc_request_inventory_particulars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'req_part_id','center_id','req_id','spare_id','part_name','part_no','hsn_code','rate','qty','total','qty_approve','qty_pending','remarks','created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
