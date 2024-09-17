<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SCInwardInventory extends Authenticatable
{
    
      protected $table = 'inward_inventory_sc';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'inwsc_id','inwsc_date','scvouchscer_no','invoice_date',
        'scno_of_case','veh_doc_no','part_added','qty','insert_date','created_at','created_by',
        'updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
