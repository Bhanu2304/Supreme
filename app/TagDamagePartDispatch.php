<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TagDamagePartDispatch extends Authenticatable
{
    
      protected $table = 'tagging_damage_part_dispatch';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     protected $fillable = [
      'dispatch',
      'dpart_id',
      'center_id',
      'asc_name',
      'asc_code',
      'part_id',
      'po_no',
      'po_date',
      'eway_bill_no',
      'doc_no',
      'veh_doc_no',
      'transportation_charge',
      'dispatch_ref_no',
      'no_of_cases',
      'dispatch_comments',
      'is_short',
      'is_faulty',
      'receive_date',
      'receive_by',
      'created_at',
      'created_by',
      'updated_at',
      'updated_by'
  ];

    

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
