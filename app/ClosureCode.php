<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ClosureCode extends Authenticatable
{
    
      protected $table = 'closure_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'closure_code','amount','description','created_at','created_by','updated_at','updated_by'
    ];
    

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
