<?php
namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PageMaster extends Authenticatable{
    protected $table    = 'pages_master';
    protected $fillable = ['id', 'page_name','page_icon','page_url','parent_id','created_at','updated_at'];    
}
