<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class StatusMaster extends Authenticatable
{
    
      protected $table = 'client_status';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'Status_Id','Status_Name','Status_Client_Id','Status_Project_Id','Status_Scenario1','Status_Scenario2','Status_Scenario3','Status_Scenario4','Status_Scenario5','Status_Scenario6','Status_Auto_Closure','Status_Scenario1_Name','Status_Scenario2_Name','Status_Scenario3_Name','Status_Scenario4_Name','Status_Scenario5_Name','Status_Scenario6_Name','created_at','created_by','updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
