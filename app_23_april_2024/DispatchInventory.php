<?php

namespace App;


use Illuminate\Foundation\Auth\User as Authenticatable;

class DispatchInventory extends Authenticatable
{
    
      protected $table = 'outward_inventory_dispatch';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'dispatch_id','invoice_id','invoice_no','po_id','po_no','po_date','eway_bill_no','doc_no','veh_doc_no','dispatch_ref_no','no_of_cases',
	'dispatch_comments','created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
