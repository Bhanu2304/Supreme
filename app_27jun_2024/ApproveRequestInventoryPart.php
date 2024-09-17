<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ApproveRequestInventoryPart extends Authenticatable
{
    
      protected $table = 'approve_request_inventory_particulars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'req_approve_id','approve_ref_no','center_id','req_id','req_ref_no',
        'brand_id','product_id','model_id','spare_id','part_name','part_no','hsn_code',
        'landing_cost','customer_price','discount','part_tax','qty','total','total_tax','net_total','remarks',
        'created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
