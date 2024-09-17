<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TaggingMaster extends Authenticatable
{
    
      protected $table = 'tagging_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'TagId','tag_type','Customer_Group','ticket_no','job_no','Customer_Category','Pincode','Customer_Name','Customer_Address','Customer_Address_Landmark','Contact_No','Alternate_Contact_No','State','City','email','Residence_No','Gst_No','Registration_Name','Ext1','Ext2','Ext3','Product','Brand','Serial_No','Model','Bill_Purchase_Date','Warrenty_End_Date','Date_of_Installation','call_type','amc_no','amc_expiry_date','warranty_status','dealer_name','invoice_no','service_type','entity_name','accessories_required','Ext4','Ext5','Ext6','tag_voc','remark','device_image','warranty_card_copy','product_serial_no','product_photo1','product_photo2','PO_Status','PO_Type','Job_Status','created_at','created_by','updated_at','updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
}
