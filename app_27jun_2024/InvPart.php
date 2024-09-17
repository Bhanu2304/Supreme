<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class InvPart extends Authenticatable
{
    
      protected $table = 'tbl_inventory_part';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'spare_id','allocation_type','allocation_id','tag_id','part_name','part_no',
        'hsn_code','part_status','pending_status','approval_date','approve_by',
        'created_at','created_by','updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
