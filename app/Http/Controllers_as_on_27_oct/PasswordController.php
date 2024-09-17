<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\AgentMaster;

class PasswordController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function __construct()
    {
        //$this->middleware('auth');
    }
    
    public function index()
    {
     
    }
    
    public function getReset($token)
    {
        print_r($token); exit;
        //return view('/auth/reset')->with('msg',$msg);
    }
}