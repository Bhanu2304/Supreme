<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class JobSheet extends Authenticatable
{
    
      protected $table = 'tbl_jobsheet';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'js_id','center_id','job_id','job_no','brand_id','brand_name','product_category_id','category_name',
        'product_id','product_name','model_id','model_name','serial_no','warranty_type','spare_id','part_name','part_no',
        'qty','part_charge_type','part_amt','labour_amt','po_no','job_type','claim_type','job_apply',
        'created_at','created_by','updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
