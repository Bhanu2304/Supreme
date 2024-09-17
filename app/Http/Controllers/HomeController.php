<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Mail;
use Session;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        Session::put("page-title","Dashboard");
        Session::put("page-logo","glyphicon-dashboard");
        
        $UserType = Session::get('UserType');
        if($UserType=='Admin' )
        {
            return redirect('brand-dashboard');
        }else{
            return redirect('brand-dashboard');
            #return redirect('dashboard');
        }
        
        //return view('home'); 
    }
    
    
    public function support(){
        Session::put("page-title","Support");
        Session::put("page-logo","glyphicon-dashboard");
        
        $url = $_SERVER['APP_URL'].'/support';
        
        return view('support')->with('url', $url); 
    }
    
}
