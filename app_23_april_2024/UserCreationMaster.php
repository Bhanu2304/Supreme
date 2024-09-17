<?php
namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserCreationMaster extends Authenticatable{
    protected $table    = 'tbl_register';
    protected $fillable = ['reg_id','customer_name','company_name','email','mobile','country',
        'industry','otp','otp_send_date','password','password_at','created_at'];     
}
