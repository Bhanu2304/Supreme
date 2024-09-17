<?php

namespace App;


use Illuminate\Foundation\Auth\User as Authenticatable;

class OutwardInventoryPart extends Authenticatable
{
    
      protected $table = 'outward_inventory';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'out_id','out_no','po_date','po_no','po_type','job_id','job_no','center_id','asc_name','asc_code','brand_id','brand_name',
        'model_id','model_name','product_category_id','product_id','model_id','spare_id','part_no','part_name','item_color','hsn_code',
        'gst','item_qty','bin_no','purchase_amt','asc_amount','customer_amount','remarks','created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
