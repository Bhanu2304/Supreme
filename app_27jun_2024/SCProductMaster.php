<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SCProductMaster extends Authenticatable
{
    
      protected $table = 'tbl_service_centre_product';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'sc_product_id','center_id','product_category_id','product_id','model_id','Brand','Product_Detail','Product','Model','created_at','created_by','updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
