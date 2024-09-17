<?php

namespace App;


use Illuminate\Foundation\Auth\User as Authenticatable;

class InwardInventoryPartSc extends Authenticatable
{
    
      protected $table = 'inward_inventory_particulars_sc';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'part_inw_id','inw_id','inw_ser_no','supplier_name','voucher_no','invoice_date','brand_id','product_category_id',
        'product_id','model_id','spare_id','part_no','part_name','item_color','hsn_code','gst','item_qty','bin_no','purchase_amt',
        'asc_amount','customer_amount','remarks','created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
