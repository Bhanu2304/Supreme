<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\CoordinatorMaster;
use App\User;
use Session;
use DB;
use Auth;
use Illuminate\Support\Facades\Hash;

class CoordinatorController extends Controller
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
        $qr = "SELECT coord_id,email,pass,phone,coord_name,coord_status,DATE_FORMAT(se.created_at,'%d-%b-%Y') created_at
        FROM  `tbl_coordinator` se";
        
        $url = $_SERVER['APP_URL'].'/add-coord';
        $data =   DB::select($qr); 
        
        return view('add-coord')->with('DataArr', $data)->with('url', $url); 
    }
    
    public function save_coord(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('pass');
        $name = $request->input('coord_name');
        $mobile = $request->input('phone');
       
        
        $data_emjson = User::where("email",$email)->first();
        $data_em = json_decode($data_emjson,true);
        
        $data_mojson = CoordinatorMaster::where("phone",$mobile)->first();
        $data_mo = json_decode($data_mojson,true);
        
        
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Email Allready Associated With Other Account.");
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
            $UserType           = "Coordinator";
            
            $userArr->name=$name;
            $userArr->email=$email;
            $userArr->password=$password_hash;
            $userArr->password2=$password;
            $userArr->UserType=$UserType;
            $userArr->verify_email="Yes";
            $userArr->verified_at=date('Y-m-d H:i:s');
            $UserId = Auth::user()->id;
            $userArr->table_name='ho';
            $userArr->created_by = $UserId;
            
            if($userArr->save()){
                $wu_id = $userArr->id;
                $seUser = new CoordinatorMaster();
                $seUser->coord_name = $name;
                $seUser->pass = $password;
                $seUser->email = $email;
                $seUser->phone = $mobile;
                $seUser->coord_status = 1;
                $seUser->created_by = $UserId;
                $seUser->LogIn_Id = $wu_id;
                
                
                $seUser->save(); 
                
                
                Session::flash('message', "Coordinator Created Successfully.");
                Session::flash('alert-class', 'alert-success');
            }
            else{
                Session::flash('error', "Coordinator Creation Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
                return back();
            } 
            
            return redirect('add-coord');
        }
        
        
    }
    
    public function coord_user()
    {
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        //$Center_Id = Auth::user()->table_id;
        $whereUser = "1=1  ";
        
        $qr = "SELECT coord_id,email,pass,phone,coord_name,coord_status,DATE_FORMAT(se.created_at,'%d-%b-%Y') created_at
        FROM  `tbl_coordinator` se";
        
        $data =   DB::select($qr); 
       // $data = json_decode($data_json); 
        
        return view('coord-view')->with('DataArr', $data);  
    }
    
    public function edit_coord(Request $request)
    {
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        
        
        
        
        $coord_id = base64_decode($request->input('coord_id')); 
        $data_json = CoordinatorMaster::whereRaw("coord_id='$coord_id'")->first();
        $data = json_decode($data_json,true);
        $url = $_SERVER['APP_URL'].'/add-coord';
        //print_r($data); exit;
        return view('edit-coord')
        ->with('url', $url)
                ->with('data',$data);
    }
    
    public function update_coord(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('pass');
        $name = $request->input('coord_name');
        $mobile = $request->input('phone');
        //$vendor_id = $request->input('vendor_id');
        $coord_id = $request->input('coord_id'); 
        $coord_status = $request->input('coord_status'); 
        
        $data_emjson = CoordinatorMaster::whereRaw("email='$email' and coord_id!='$coord_id'")->first();
        $data_em = json_decode($data_emjson,true);
        
        $data_mojson = CoordinatorMaster::whereRaw("phone='$mobile' and coord_id!='$coord_id'")->first();
        $data_mo = json_decode($data_mojson,true);
        
        $data_record = CoordinatorMaster::whereRaw(" coord_id='$coord_id'")->first();
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
            $userArr['UserActive']=$coord_status;
            $UserId = Auth::user()->id;
            $userArr['updated_by']=$UserId;
            
            
            if(!empty($password))
            {
                $userArr['password']=$password_hash;
                $userArr['password2']=$password;
            }
            
            
            
            
            if(User::whereRaw("email='{$data['email']}'")->update($userArr)){
                $seUser = array();
                $seUser['coord_name'] = $name;
                
                $seUser['pass'] = $password;
                $seUser['email'] = $email;
                $seUser['phone'] = $mobile;
                $seUser['coord_status'] = $coord_status;
                //$seUser['center_id'] = $vendor_id;
                $seUser['updated_by']=$UserId; 
                
                CoordinatorMaster::whereRaw("coord_id='$coord_id'")->update($seUser);
                
                
                Session::flash('message', "Coordinator Details Updated Successfully.");
                Session::flash('alert-class', 'alert-success');
            }
            else{
                Session::flash('error', "Coordinator Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('coord-edit?coord_id='. base64_encode($coord_id));
        }
    }
    
    
}

