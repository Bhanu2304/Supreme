<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AllocationMaster extends Authenticatable
{
    
      protected $table = 'client_allocation';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'Allocation_Id','Campaign_Id','Calling_Type','Allocation_Name','allocation_file','Import_Fields','List_Id',
'ListType','AllocationStatus','created_at','created_by','updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
