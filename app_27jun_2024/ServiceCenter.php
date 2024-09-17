<?php

namespace App;


use Illuminate\Foundation\Auth\User as Authenticatable;

class ServiceCenter extends Authenticatable
{
    
      protected $table = 'tbl_service_centre';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'center_id','center_name','person_name','contact_no','email_id','asc_code','region','address','city','state','pincode','center_remark','created_at','created_by','updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
