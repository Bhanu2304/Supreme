<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\FieldUser;
use App\FeTrace;

class FieldUserController extends Controller
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
        
        $data_json           =   FieldUser::orderByRaw('id DESC')->get(); 
        $data = json_decode($data_json,true);
        
        return view('field-user')->with('DataArr', $data);
    }
    
    public function fe_trace()
    {
        $data_json           =   FeTrace::orderByRaw('id DESC')->get(); 
        $data = json_decode($data_json,true);
        
        return view('fe-trace')->with('DataArr', $data);
    }
    
    
}

