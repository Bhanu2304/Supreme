<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\ServiceEngineer;
use App\User;
use Session;
use DB;
use Auth;
use Illuminate\Support\Facades\Hash;

class ServiceEngineerController extends Controller
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
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        /*$whereUser = "";
        if($UserType!='Admin')
        {
            $whereUser = " and id='$UserId'";
        }*/
        
        $qr1 = "select center_id,center_name from tbl_service_centre
WHERE  sc_status='1' ";
        $vendor_arr           =   DB::select($qr1); 
        
        
        $Center_Id = Auth::user()->table_id;
        $whereUser = "1=1  ";
        if($UserType!='Admin')
        {
            $whereUser .= " and se.center_id='$Center_Id'";
        }
        $qr = "SELECT se_id,tsc.center_name,se.center_id,se.email,pass,phone,se_name,se_status,DATE_FORMAT(se.created_at,'%d-%b-%Y') created_at
        FROM  `tbl_service_engineer` se
        INNER JOIN tbl_service_centre tsc ON se.center_id = tsc.center_id  WHERE  $whereUser ORDER BY se_name,center_name";
        
        $data =   DB::select($qr); 
        
        
        $url = $_SERVER['APP_URL'].'/vendor-add-se';
        
        return view('add-se')
        ->with('url', $url)
                ->with('vendor_arr', $vendor_arr)->with('DataArr', $data); 
    }
    
    public function save_se(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('pass');
        $name = $request->input('se_name');
        $mobile = $request->input('phone');
        $vendor_id = $request->input('vendor_id');
        
        $data_emjson = User::where("email",$email)->first();
        $data_em = json_decode($data_emjson,true);
        
        $data_mojson = ServiceEngineer::where("phone",$mobile)->first();
        $data_mo = json_decode($data_mojson,true);
        
        
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Email Allready Associated With Other Account");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(!empty($data_mo))
        {
            Session::flash('error', "Mobile Allready Associated With Other Account");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else
        {
            
            $password_hash           =   Hash::make($password);
            $userArr            =   new User();
            $UserType           = "ServiceEngineer";
            
            $userArr->name=$name;
            $userArr->email=$email;
            $userArr->password=$password_hash;
            $userArr->password2=$password;
            $userArr->UserType=$UserType;
            $userArr->verify_email="Yes";
            $userArr->verified_at=date('Y-m-d H:i:s');
            $UserId = Auth::user()->id;
            $userArr->table_name='tbl_service_centre';
            $userArr->table_id=Auth::user()->table_id;
            $userArr->created_by = $UserId;
            
            if($userArr->save()){
                $wu_id = $userArr->id;
                $seUser = new ServiceEngineer();
                $seUser->se_name = $name;
                $seUser->pass = $password;
                $seUser->email = $email;
                $seUser->phone = $mobile;
                $seUser->se_status = 1;
                $seUser->created_by = $UserId;
                $seUser->LogIn_Id = $wu_id;
                $seUser->center_id = $vendor_id;
                
                $seUser->save(); 
                
                
                Session::flash('message', "Service Engineer Created Successfully.");
                Session::flash('alert-class', 'alert-success');
            }
            else{
                Session::flash('error', "Service Engineer Creation Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
                return back();
            } 
            
            return redirect('vendor-add-se');
        }
        
        
    }
    
    public function se_user()
    {
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $whereUser = "1=1  ";
        if($UserType!='Admin')
        {
            $whereUser .= " and vendor_id='$Center_Id'";
        }
        $qr = "SELECT se_id,tsc.center_name,se.vendor_id,se.email,pass,phone,se_name,se_status,DATE_FORMAT(se.created_at,'%d-%b-%Y') created_at
        FROM  `tbl_service_engineer` se
        INNER JOIN tbl_service_centre tsc ON se.center_id = tsc.center_id  WHERE  $whereUser ORDER BY se_name";
        
        $data =   DB::select($qr); 
       // $data = json_decode($data_json); 
        
        return view('vendor-se-view')->with('DataArr', $data);  
    }
    
    public function edit_se(Request $request)
    {
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        
        $whereUser = "";
        if($UserType!='Admin')
        {
            $whereUser = " and center_id='$Center_Id'";
        }
        
        $qr1 = "select * from tbl_service_centre 
WHERE  sc_status='1' $whereUser";
        $vendor_arr           =   DB::select($qr1); 
        
        $se_id = base64_decode($request->input('se_id')); 
        $data_json = ServiceEngineer::whereRaw("se_id='$se_id' $whereUser")->first();
        $data = json_decode($data_json,true);
        
        $url = $_SERVER['APP_URL'].'/vendor-add-se';
        //print_r($data); exit;
        return view('edit-se-vendor')
                ->with('data',$data)
                ->with('url', $url)
                ->with('vendor_arr', $vendor_arr);
    }
    
    public function update_se(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('pass');
        $name = $request->input('se_name');
        $mobile = $request->input('phone');
        //$vendor_id = $request->input('vendor_id');
        $se_id = $request->input('se_id'); 
        $se_status = $request->input('se_status'); 
        
        $data_emjson = ServiceEngineer::whereRaw("email='$email' and se_id!='$se_id'")->first();
        $data_em = json_decode($data_emjson,true);
        
        $data_mojson = ServiceEngineer::whereRaw("phone='$mobile' and se_id!='$se_id'")->first();
        $data_mo = json_decode($data_mojson,true);
        
        $data_record = ServiceEngineer::whereRaw(" se_id='$se_id'")->first();
        $data = json_decode($data_record,true);
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Email Allready Associated With Other Account");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(!empty($data_mo))
        {
            Session::flash('error', "Mobile Allready Associated With Other Account");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else
        {
            if(!empty($password))
            {
                $password_hash           =   Hash::make($password);
            }
            
            $userArr            =   array();
            
            $userArr['name']=$name;
            $userArr['email']=$email;
            $userArr['UserActive']=$se_status;
            $UserId = Auth::user()->id;
            $userArr['updated_by']=$UserId;
            
            
            if(!empty($password))
            {
                $userArr['password']=$password_hash;
                $userArr['password2']=$password;
            }
            
            
            
            
            if(User::whereRaw("email='{$data['email']}'")->update($userArr)){
                $seUser = array();
                $seUser['se_name'] = $name;
                
                $seUser['pass'] = $password;
                $seUser['email'] = $email;
                $seUser['phone'] = $mobile;
                $seUser['se_status'] = $se_status;
                //$seUser['center_id'] = $vendor_id;
                $seUser['updated_by']=$UserId; 
                
                ServiceEngineer::whereRaw("se_id='$se_id'")->update($seUser);
                
                
                Session::flash('message', "Service Engineer Details Updated Successfully.");
                Session::flash('alert-class', 'alert-success');
            }
            else{
                Session::flash('error', "Service Engineer Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('vendor-se-edit?se_id='. base64_encode($se_id));
        }
    }
    
    
}

