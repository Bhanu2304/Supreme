<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\ServiceStore;
use App\User;
use Session;
use DB;
use Auth;
use Illuminate\Support\Facades\Hash;

class StoreController extends Controller
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
        Session::put("page-title","Add Store");
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        /*$whereUser = "";
        if($UserType!='Admin')
        {
            $whereUser = " and id='$UserId'";
        }*/
        
        
        
        
        $Center_Id = Auth::user()->table_id;
        $whereUser = "1=1  ";
        $whereUser2 = "";
        if($UserType!='Admin')
        {
            $whereUser .= " and se.center_id='$Center_Id'";
            $whereUser2 .= " and sc.center_id='$Center_Id'";
        }
        
        // $qr1 = "select center_id,center_name from tbl_service_centre sc
        // WHERE  sc_status='1' $whereUser2";
        // $vendor_arr           =   DB::select($qr1); 

        $qr2 = "SELECT sc.center_id,center_name,sm.state_name,city,pincode FROM tbl_service_centre sc 
            INNER JOIN users us ON sc.email_id = us.email 
            INNER JOIN state_master sm ON sc.state =  sm.state_id
            WHERE sc_status='1' $whereUser2 ORDER BY center_name";
        $vendor_arr           =   DB::select($qr2);
        
        
        $qr = "SELECT se_id,tsc.center_name,se.center_id,se.email,pass,phone,se_name,se_status,DATE_FORMAT(se.created_at,'%d-%b-%Y') created_at
        FROM  `tbl_store` se
        INNER JOIN tbl_service_centre tsc ON se.center_id = tsc.center_id  WHERE  $whereUser ORDER BY se_name,center_name";
        
        $data =   DB::select($qr); 
        
        
        $url = $_SERVER['APP_URL'].'/add-store';
        
        return view('add-store')
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
        
        $data_mojson = ServiceStore::where("phone",$mobile)->first();
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
            $UserType           = "Store";
            
            $userArr->name=$name;
            $userArr->email=$email;
            $userArr->password=$password_hash;
            $userArr->password2=$password;
            $userArr->UserType=$UserType;
            $userArr->verify_email="Yes";
            $userArr->verified_at=date('Y-m-d H:i:s');
            $UserId = Auth::user()->id;
            $userArr->table_name='tbl_service_centre';
            $userArr->table_id= $vendor_id;
            $userArr->created_by = $UserId;
            
            if($userArr->save()){
                $wu_id = $userArr->id;
                $seUser = new ServiceStore();
                $seUser->se_name = $name;
                $seUser->pass = $password;
                $seUser->email = $email;
                $seUser->phone = $mobile;
                $seUser->se_status = 1;
                $seUser->created_by = $UserId;
                $seUser->LogIn_Id = $wu_id;
                $seUser->center_id = $vendor_id;
                
                $seUser->save(); 
                
                
                Session::flash('message', "Store Created Successfully.");
                Session::flash('alert-class', 'alert-success');
            }
            else{
                Session::flash('error', "Store Creation Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
                return back();
            } 
            
            return redirect('add-store');
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
        $data_json = ServiceStore::whereRaw("se_id='$se_id' $whereUser")->first();
        $data = json_decode($data_json,true);
        
        $url = $_SERVER['APP_URL'].'/add-store';
        //print_r($data); exit;
        return view('edit-store')
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
        
        $data_emjson = ServiceStore::whereRaw("email='$email' and se_id!='$se_id'")->first();
        $data_em = json_decode($data_emjson,true);
        
        $data_mojson = ServiceStore::whereRaw("phone='$mobile' and se_id!='$se_id'")->first();
        $data_mo = json_decode($data_mojson,true);
        
        $data_record = ServiceStore::whereRaw(" se_id='$se_id'")->first();
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
                
                ServiceStore::whereRaw("se_id='$se_id'")->update($seUser);
                
                
                Session::flash('message', "Store Details Updated Successfully.");
                Session::flash('alert-class', 'alert-success');
            }
            else{
                Session::flash('error', "Store Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('store-edit?se_id='. base64_encode($se_id));
        }
    }
    
    
}

