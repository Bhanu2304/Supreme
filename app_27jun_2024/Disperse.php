<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Disperse extends Authenticatable
{
    
      protected $table = 'tbl_disperse';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'tag_id','center_id','disperse_amount','disperse_date','created_by','created_at','updated_at','transaction_id'
    ];
    

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
