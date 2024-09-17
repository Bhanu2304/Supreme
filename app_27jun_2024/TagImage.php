<?php

namespace App;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TagImage extends Authenticatable
{
    
      protected $table = 'tagging_master_image';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'ImagId','TagId','RTagId','image_type','img_url','created_at','created_by','updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
