<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class InvoicePart extends Authenticatable
{
    
      protected $table = 'tbl_invoice_parts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
      
    protected $fillable = [
'inv_id',
'tag_id',
'product_desc',
'sac_code',
'qty',
'rate',
'total',
'discount',
'cgst_per',
'cgst_amt',
'sgst_per',
'sgst_amt',
'igst_per',
'igst_amt',
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
