<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\ContactUs;


class ContactUsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    
    public function index()
    {
        
        $data_json           =   ContactUs::orderByRaw('id DESC')->get(); 
        $data = json_decode($data_json,true);
        
        return view('contact')->with('DataArr', $data);
    }
    
    
    
    
}

