<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\WarrantyMaster;
use DB;
use Auth;
use Session;



class WarrantyController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function add_warranty()
    {
        $data           =   DB::select("SELECT * from tbl_warranty   "); 
        $url = $_SERVER['APP_URL'].'/add-warranty';
        return view('add-warranty')->with('DataArr', $data)->with('url', $url);  
    }
    
    public function save_warranty(Request $request)
    {
        $warranty_name = addslashes($request->input('warranty_name'));
        
        
        
        $data_emjson = WarrantyMaster::whereRaw("warranty_name='$warranty_name'")->first(); 
        $data_em = json_decode($data_emjson,true);
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Warranty Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $accArr            =   new WarrantyMaster();
            
            $accArr->warranty_name=$warranty_name;
            
            $UserId = Auth::user()->id;    
            $accArr->created_by=$UserId; 
            $accArr->created_at=date("Y-m-d H:i:s"); 
            
            
            if($accArr->save()){
                Session::flash('message', "Warranty Added Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Warranty Not Added. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('add-warranty');
        }
        
        
    }
    
    
    
    public function edit_warranty(Request $request)
    {
        $warranty_id = base64_decode($request->input('warranty_id')); 
        $data_json = WarrantyMaster::where("warranty_id",$warranty_id)->first();
        $data = json_decode($data_json,true);
        //print_r($data); exit;
        //$product_json           =   ProductMaster::whereRaw("product_status='1'")->orderByRaw('product_name DESC')->get(); 
        //$product_arr = json_decode($product_json);
        
        //print_r($data); exit;
        $url = $_SERVER['APP_URL'].'/add-warranty';
        return view('edit-warranty')->with('data',$data)->with('url', $url);
    }
    
    public function update_warranty(Request $request)
    {
        
        $warranty_id = $request->input('warranty_id'); 
        $warranty_status = $request->input('warranty_status'); 
        $warranty_name = addslashes($request->input('warranty_name'));
        
        
        
        
        $data_emjson = WarrantyMaster::whereRaw("warranty_id!='$warranty_id' and warranty_name='$warranty_name'")->first();
        $data_em = json_decode($data_emjson,true);
        
        
        
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Warranty Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $accArr            =   array();
            
            $accArr['warranty_name']=$warranty_name;
            
            $accArr['warranty_status']=$warranty_status;
            $UserId = Auth::user()->id;    
            $accArr->updated_by=$UserId; 
            $accArr->updated_at=date("Y-m-d H:i:s"); 
            
            //print_r($modelArr); exit;
            
            if(WarrantyMaster::whereRaw("warranty_id='$warranty_id'")->update($accArr)){
                
                
                
                Session::flash('message', "Warranty Details Updated Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Warranty Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('add-warranty?warranty_id='. base64_encode($warranty_id));
        }
    }
    
    
    
    
    
    
    
    
    
    
}

