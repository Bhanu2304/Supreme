<?php
namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class WebUser extends Authenticatable{
    protected $table    = 'tbl_web_user';
    protected $fillable = ['UserId','UserName','Password','UserType','email','mobile','LogIn_Id','created_at'];      
}
