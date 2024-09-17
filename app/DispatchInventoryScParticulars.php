<?php

namespace App;


use Illuminate\Foundation\Auth\User as Authenticatable;

class DispatchInventoryScParticulars extends Authenticatable
{
    
      protected $table = 'outward_inventory_dispatch_particulars_sc';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'dispatch_part_id','invoice_id','invoice_no','created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
