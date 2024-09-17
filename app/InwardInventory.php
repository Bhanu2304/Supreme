<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class InwardInventory extends Authenticatable
{
    
      protected $table = 'inward_inventory';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'inw_id','supplier_name','voucher_no','invoice_date','no_of_case',
        'veh_doc_no','insert_date','created_at','created_by',
        'updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
