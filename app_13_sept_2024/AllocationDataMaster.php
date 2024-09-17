<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AllocationDataMaster extends Authenticatable
{
    
      protected $table = 'client_allocation_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'Allocation_Data_Id','Allocation_Client_Id','Allocation_Campaign_Id','Allocation_List_Id','Allocation_Id','vendor_lead_code','MSISDN','Field1',
'Field2',
'Field3',
'Field4',
'Field5',
'Field6',
'Field7',
'Field8',
'Field9',
'Field10',
'Field11',
'Field12',
'Field13',
'Field14',
'Field15',
'Field16',
'Field17',
'Field18',
'Field19',
'Field20',
'Field21',
'Field22',
'Field23',
'Field24',
'Field25',
'Field26',
'Field27',
'Field28',
'Field29',
'Field30',
'Field31',
'Field32',
'Field33',
'Field34',
'Field35',
'Field36',
'Field37',
'Field38',
'Field39',
'Field40',
'Field41',
'Field42',
'Field43',
'Field44',
'Field45',
'Field46',
'Field47',
'Field48',
'Field49',
'Field50',
'Field51',
'Field52',
'Field53',
'Field54',
'Field55',
'Field56',
'Field57',
'Field58',
'Field59',
'Field60',
'created_at','created_by','updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
