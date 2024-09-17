<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable implements CanResetPassword
{
    use Notifiable;
      protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'id', 'name','UserName','email','password','password2','remember_token','verified_at','created_at','updated_at','UserType'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function verifyUser()
    {
        
        return $this->hasOne('App\VerifyUser');
    }
    
    
}
