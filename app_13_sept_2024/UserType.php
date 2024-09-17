<?php

namespace App;


use Illuminate\Foundation\Auth\User as Authenticatable;

class UserType extends Authenticatable
{
    
      protected $table = 'user_type_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'user_type_id','user_type','page_column_name','user_show','created_at','created_by','updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
