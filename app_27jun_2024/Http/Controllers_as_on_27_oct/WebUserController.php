<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\WebUser;
use App\User;
use Session;
use DB;
use Auth;
use Illuminate\Support\Facades\Hash;

class WebUserController extends Controller
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
    
    public function edit_web_user_post(Request $request)
    {
        $UserId = base64_decode($request->input('UserId')); 
        $data_json = WebUser::where("UserId",$UserId)->first();
        $data = json_decode($data_json,true);
        return view('edit-web-user')->with('data',$data);
    }
    
    


    public function index()
    {
        $data_json           =   WebUser::orderByRaw('UserName DESC')->get(); 
        $data = json_decode($data_json,true);
        
        return view('add-web-user')->with('DataArr', $data); 
    }
    
    public function save_user(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $name = $request->input('name');
        $mobile = $request->input('mobile');
        $UserType = $request->input('UserType');
        
        $data_emjson = WebUser::where("email",$email)->first();
        $data_em = json_decode($data_emjson,true);
        
        $data_mojson = WebUser::where("mobile",$mobile)->first();
        $data_mo = json_decode($data_mojson,true);
        
        $data_user_json = User::where("email",$email)->first();
        $data_user = json_decode($data_user_json,true);
        
        
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
        else if(!empty($data_user))
        {
            Session::flash('error', "Email Allready Associated With Other Account");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else
        {
            
            $password_hash           =   Hash::make($password);
            $userArr            =   new User();
            
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
                $webUser = new WebUser();
                $webUser->UserName = $name;
                $webUser->Password = $password_hash;
                $webUser->UserType = $UserType;
                $webUser->email = $email;
                $webUser->mobile = $mobile;
                $webUser->user_status = 1;
                $webUser->created_by = $UserId;
                $webUser->LogIn_Id = $wu_id;
                
                $webUser->save();
                
                
                Session::flash('message', "User Created Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "User Creation Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('add-user');
        }
        
        
    }
    
    public function view_user()
    {
        $data           =   DB::select("SELECT UserId,UserName,PASSWORD,UserType,email,mobile,user_status,DATE_FORMAT(created_at,'%d-%b-%Y') created_at
FROM  `tbl_web_user` ORDER BY UserName"); 
       // $data = json_decode($data_json); 
        
        return view('view-web-user')->with('DataArr', $data);  
    }
    
    public function edit_user(Request $request)
    {
        $UserId = base64_decode($request->input('UserId')); 
        $data_json = WebUser::where("UserId",$UserId)->first();
        $data = json_decode($data_json,true);
        //print_r($data); exit;
        return view('edit-web-user')->with('data',$data);
    }
    
    public function update_user(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $name = $request->input('name');
        $mobile = $request->input('mobile');
        $UserType = $request->input('UserType');
        $UserId = $request->input('UserId'); 
        $user_status = $request->input('user_status'); 
        
        $data_emjson = WebUser::whereRaw("email='$email' and UserId!='$UserId'")->first();
        $data_em = json_decode($data_emjson,true);
        
        $data_mojson = WebUser::whereRaw("mobile='$mobile' and UserId!='$UserId'")->first();
        $data_mo = json_decode($data_mojson,true);
        
        $data_record = WebUser::whereRaw(" UserId='$UserId'")->first();
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
            $userArr['UserActive']=$user_status;
            $userArr['UserType']=$UserType;
            $UserId = Auth::user()->id;
            $userArr['updated_by']=$UserId;
            
            
            if(!empty($password))
            {
                $userArr['password']=$password_hash;
                $userArr['password2']=$password;
            }
            
            
            
            
            if(User::whereRaw("email='{$data['email']}'")->update($userArr)){
                $webUser = array();
                $webUser['UserName'] = $name;
                
                $webUser['UserType'] = $UserType;
                $webUser['email'] = $email;
                $webUser['mobile'] = $mobile;
                $webUser['user_status'] = $user_status;
                $webUser['updated_by']=$UserId;
                
                WebUser::whereRaw("UserId='$UserId'")->update($webUser);
                
                
                Session::flash('message', "User Details Updated Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "User Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('edit-user?UserId='. base64_encode($UserId));
        }
    }
    
//    public function crypt_pass()
//    {
//        $data_vendor = User::whereRaw("Id>17")->get();
//        
//        foreach($data_vendor as $vendor)
//        {
//            $password=$vendor->password2;
//            $password_hash           =   Hash::make($password);
//            $userArr = array();
//            $userArr['password'] = $password_hash;
//            User::whereRaw("id='{$vendor->id}'")->update($userArr); 
//        }
//    }
    
    
}

