<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SCRequestInventory extends Authenticatable
{
    
      protected $table = 'sc_request_inventory';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'req_id','req_date','req_no','center_id','brand_id','product_category_id','product_id','model_id','remarks',
        'part_required','qty','total','qty_approve','qty_pending','part_approve','part_reject','part_status_pending',
        'approve_by','approve_date','approve_status','delivery_date','created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
