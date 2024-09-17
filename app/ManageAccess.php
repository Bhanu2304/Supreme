<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ManageAccess extends Authenticatable
{
    
      protected $table = 'manage_access';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'Access_Id','UserId','UserType','access','parent_access','created_at','created_by','updated_at','updated_by'  
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
