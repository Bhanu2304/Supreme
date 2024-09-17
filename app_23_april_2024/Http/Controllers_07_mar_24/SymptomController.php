<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SymptomMaster;
use DB;
use Auth;
use Session;



class SymptomController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function add_symptom()
    {
        //print_r($_SERVER['APP_URL']); exit;
        
        $data = DB::select("SELECT * from tbl_symptom"); 
        $url = $_SERVER['APP_URL'].'/add-symptom';
        
        
        
        return view('add-sypt1')
                ->with('DataArr', $data)
                ->with('url', $url)
                ;
    }
    
    public function save_symptom(Request $request)
    {
        $field_name = addslashes($request->input('field_name'));
        $field_code = addslashes($request->input('field_code'));
        $field_type = addslashes($request->input('field_type'));
        $remark = addslashes($request->input('remark'));
        
        
        $data_emjson = SymptomMaster::whereRaw("field_name='$field_name'")->first(); 
        $data_em = json_decode($data_emjson,true);
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Symptom Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $accArr            =   new SymptomMaster();
            
            $accArr->field_name=$field_name;
            $accArr->field_code=$field_code;
            $accArr->field_type=$field_type;
            $accArr->remark=$remark;
            $UserId = Auth::user()->id;    
            $accArr->created_by=$UserId; 
            $accArr->created_at=date("Y-m-d H:i:s"); 
            
            
            if($accArr->save()){
                Session::flash('message', "Symptom Added Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Symptom Not Added. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('add-symptom');
        }
        
        
    }
    
    
    
    public function edit_symptom(Request $request)
    {
        $symptom_id = base64_decode($request->input('symptom_id')); 
        $data_json = SymptomMaster::where("symptom_id",$symptom_id)->first();
        $data = json_decode($data_json,true);
        //print_r($data); exit;
        //$product_json           =   ProductMaster::whereRaw("product_status='1'")->orderByRaw('product_name DESC')->get(); 
        //$product_arr = json_decode($product_json);
        
        //print_r($data); exit;
        $url = $_SERVER['APP_URL'].'/add-symptom';
        return view('edit-sypt1')->with('data',$data)->with('url', $url);
    }
    
    public function update_symptom(Request $request)
    {
        
        $symptom_id = $request->input('symptom_id'); 
        $sypt_status = $request->input('sypt_status'); 
        $field_name = addslashes($request->input('field_name'));
        $field_code = addslashes($request->input('field_code'));
        $field_type = addslashes($request->input('field_type'));
        $remark = addslashes($request->input('remark'));
        
        
        
        $data_emjson = SymptomMaster::whereRaw("symptom_id!='$symptom_id' and field_name='$field_name'")->first();
        $data_em = json_decode($data_emjson,true);
        
        
        
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Symptom Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $accArr            =   array();
            
            $accArr['field_name']=$field_name;
            $accArr['field_code']=$field_code;
            $accArr['field_type']=$field_type;
            $accArr['remark']=$remark;
            $accArr['sypt_status']=$sypt_status;
            $UserId = Auth::user()->id;    
            $accArr->updated_by=$UserId; 
            $accArr->updated_at=date("Y-m-d H:i:s"); 
            
            //print_r($modelArr); exit;
            
            if(SymptomMaster::whereRaw("symptom_id='$symptom_id'")->update($accArr)){
                
                
                
                Session::flash('message', "Symptom Details Updated Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Symptom Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('edit-symptom?symptom_id='. base64_encode($symptom_id));
        }
    }
    
    
    
    
    
    
    
    
    
    
}

