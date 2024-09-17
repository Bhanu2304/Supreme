<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class InventoryItemList extends Authenticatable
{
    
      protected $table = 'tbl_inventory_item_list';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
         #'inv_id','Part_Name','Part_No','hsn_code','stock_qty','avg_consmptn','bal_qty','mol','create_date','created_at','updated_at'
         'part_inw_id','inw_po_no','part_id','srno','is_out','out_po_id','out_po_no','asc_id','asc_part_id','created_at','created_by','updated_at','updated_by','out_date','out_by','cancel_by','cancel_at'
     ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
