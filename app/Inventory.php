<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Inventory extends Authenticatable
{
    
      protected $table = 'tbl_inventory';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'inv_id','Part_Name','Part_No','hsn_code','stock_qty','avg_consmptn','bal_qty','mol','create_date','created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
