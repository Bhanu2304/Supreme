<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class InwardNoSC extends Authenticatable
{
    
      protected $table = 'inward_no_sc';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'inw_id','center_id','fin_year','po_type','sr_no','created_at','created_by','updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
