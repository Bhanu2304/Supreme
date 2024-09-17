<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class InventoryAdd extends Authenticatable
{
    
      protected $table = 'tbl_inventory_add';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'inv_id','Part_Name','Part_No','hsn_code','stock_qty','landing_cost','customer_price','discount','raw_no','inv_status','create_date','created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
