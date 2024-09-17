<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ManualChallan extends Authenticatable
{
    
      protected $table = 'manual_challan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'challan_no',
      'brand_id',
      'party_type',
      'center_id',
      'to',
      'description',
      'part_name',
      'part_number',
      'ticket_number',
      'job_number',
      'serial_no',
      'type_of_part',
      'issue_qty',
      'rate',
      'gst',
      'total',
      'grand_total',
      'eway_bill',
      'remarks',
      'created_by',
      'updated_at',
  ];
    

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
