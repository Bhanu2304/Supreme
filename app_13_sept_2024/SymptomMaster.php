<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SymptomMaster extends Authenticatable
{
    
      protected $table = 'tbl_symptom';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'symptom_id','field_name','field_code','field_type','remark','sypt_status','create_date','created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
