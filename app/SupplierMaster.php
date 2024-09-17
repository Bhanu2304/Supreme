<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SupplierMaster extends Authenticatable
{
    
      protected $table = 'supplier_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'supplier_code','supplier_name','address','state','state_code','phone_no','email','gst_code','city','created_at','created_by','updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
