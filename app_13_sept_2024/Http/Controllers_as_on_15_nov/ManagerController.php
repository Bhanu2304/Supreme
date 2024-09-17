<?php

namespace App\Http\Controllers;
use DB;
use Auth;
use Session;
use App\User;
use App\UserType;
use App\RegAreaMaster;
use App\RegionMaster;
use App\StateMaster;
use App\DistrictMaster;
use App\ManagerMaster;
use App\CountryMaster;
use App\ServiceCenter;
use Illuminate\Http\Request;
use App\RegionalManagerMaster;

use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
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
        Session::put("page-title","Add Manager");
        $qr = "SELECT man_id,email,pass,phone,man_name,man_status,DATE_FORMAT(se.created_at,'%d-%b-%Y') created_at,center_name
        FROM  `tbl_manager` se left join tbl_service_centre ce on se.center_id = ce.center_id";
        
        $center_json           =   ServiceCenter::orderByRaw('center_name ASC')->get(); 
        $center_master = json_decode($center_json);
        
        $url = $_SERVER['APP_URL'].'/add-man';
        $data =   DB::select($qr); 
        
        return view('add-man')->with('DataArr', $data)->with('url', $url)->with('center_master',$center_master); 
    }
    
    public function save_man(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('pass');
        $name = $request->input('man_name');
        $mobile = $request->input('phone');
        $center_id = $request->input('center_id');
        
        $data_emjson = User::where("email",$email)->first();
        $data_em = json_decode($data_emjson,true);
        
        $data_mojson = ManagerMaster::where("phone",$mobile)->first();
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
            $UserType           = "Manager";
            
            $userArr->name=$name;
            $userArr->email=$email;
            $userArr->password=$password_hash;
            $userArr->password2=$password;
            $userArr->UserType=$UserType;
            $userArr->verify_email="Yes";
            $userArr->verified_at=date('Y-m-d H:i:s');
            $UserId = Auth::user()->id;
            $userArr->table_name='center';
            $userArr->created_by = $UserId;
            
            if($userArr->save()){
                $wu_id = $userArr->id;
                $seUser = new ManagerMaster();
                $seUser->man_name = $name;
                $seUser->pass = $password;
                $seUser->email = $email;
                $seUser->phone = $mobile;
                $seUser->man_status = 1;
                $seUser->center_id = $center_id;
                $seUser->created_by = $UserId;
                $seUser->LogIn_Id = $wu_id;
                
                
                $seUser->save(); 
                
                
                Session::flash('message', "Manager Created Successfully.");
                Session::flash('alert-class', 'alert-success');
            }
            else{
                Session::flash('error', "Manager Creation Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
                return back();
            } 
            
            return redirect('add-man');
        }
        
        
    }
    
    public function man_user()
    {
        Session::put("page-title","View Manager");
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        //$Center_Id = Auth::user()->table_id;
        $whereUser = "1=1  ";
        
        $qr = "SELECT man_id,email,pass,phone,man_name,man_status,DATE_FORMAT(se.created_at,'%d-%b-%Y') created_at
        FROM  `tbl_manager` se";
        
        $data =   DB::select($qr); 
       // $data = json_decode($data_json); 
        
        return view('man-view')->with('DataArr', $data);  
    }
    
    public function edit_man(Request $request)
    {
        Session::put("page-title","Edit Manager");
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        
        $center_json           =   ServiceCenter::orderByRaw('center_name ASC')->get(); 
        $center_master = json_decode($center_json);
        
        
        $man_id = base64_decode($request->input('man_id')); 
        $data_json = ManagerMaster::whereRaw("man_id='$man_id'")->first();
        $data = json_decode($data_json,true);
        $url = $_SERVER['APP_URL'].'/add-man';
        //print_r($data); exit;
        return view('edit-man')
        ->with('url', $url)
        ->with('center_master',$center_master)        
                ->with('data',$data);
    }
    
    public function update_man(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('pass');
        $name = $request->input('man_name');
        $mobile = $request->input('phone');
        //$vendor_id = $request->input('vendor_id');
        $man_id = $request->input('man_id'); 
        $man_status = $request->input('man_status'); 
        
        $data_emjson = ManagerMaster::whereRaw("email='$email' and man_id!='$man_id'")->first();
        $data_em = json_decode($data_emjson,true);
        
        $data_mojson = ManagerMaster::whereRaw("phone='$mobile' and man_id!='$man_id'")->first();
        $data_mo = json_decode($data_mojson,true);
        
        $data_record = ManagerMaster::whereRaw(" man_id='$man_id'")->first();
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
            $userArr['UserActive']=$man_status;
            $UserId = Auth::user()->id;
            $userArr['updated_by']=$UserId;
            
            
            if(!empty($password))
            {
                $userArr['password']=$password_hash;
                $userArr['password2']=$password;
            }
            
            
            
            
            if(User::whereRaw("email='{$data['email']}'")->update($userArr)){
                $seUser = array();
                $seUser['man_name'] = $name;
                
                $seUser['pass'] = $password;
                $seUser['email'] = $email;
                $seUser['phone'] = $mobile;
                $seUser['man_status'] = $man_status;
                //$seUser['center_id'] = $vendor_id;
                $seUser['updated_by']=$UserId; 
                
                ManagerMaster::whereRaw("man_id='$man_id'")->update($seUser);
                
                
                Session::flash('message', "Manager Details Updated Successfully.");
                Session::flash('alert-class', 'alert-success');
            }
            else{
                Session::flash('error', "Manager Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('man-edit?man_id='. base64_encode($man_id));
        }
    }
    
    public function add_region_manager()
    {
        Session::put("page-title","Add/View Regional Manager");
        $qr = "SELECT reg_man_id,email,pass,phone,user_type,man_name,man_status,DATE_FORMAT(se.created_at,'%d-%b-%Y') created_at
        FROM  `tbl_region_manager` se order by user_type,man_name asc";
        
        //$region_json           =   RegionMaster::orderByRaw('region_name ASC')->get(); 
        //$region_master = json_decode($region_json);
        
        $url = $_SERVER['APP_URL'].'/add-reg-man';
        $data =   DB::select($qr); 
        $dataNew = array();
        foreach($data as $record)
        {
            $reg_man_id = $record->reg_man_id;
            $user_type = $record->user_type;
            $region_arr = array();
            $st_arr = array();
            $region_str = "";
            if($user_type=='NSMs')
            {
                $select_region = "SELECT DISTINCT(region_name) region
    from `region_master` ";
                $region_master =   DB::select($select_region); 
                foreach($region_master as $reg)
                {
                   $region_arr[] = $reg->region;
                }
                $select_state = "SELECT st.state_name
    from `state_master` st ";
                $state_master =   DB::select($select_state); 
                
                foreach($state_master as $st)
                {
                    $st_arr[] = $st->state_name;
                } 
            }
            else
            {
                $region_str = "where reg_man_id='$reg_man_id'";
            
            
            $select_region = "SELECT DISTINCT(region_name) region
FROM  `tbl_region_area_map` ar_map
inner JOIN `region_master` regm ON ar_map.region_id=regm.region_id $region_str";
            $region_master =   DB::select($select_region); 
            
            
           foreach($region_master as $reg)
            {
               $region_arr[] = $reg->region;
            } 
            
            if($user_type=='RSM' || $user_type=='NSM')
            {
                $select_state = "SELECT st.state_name,st.region_id region_id
    FROM  `tbl_region_area_map` ar_map
    INNER JOIN `state_master` st ON ar_map.region_id=st.region_id $region_str";
                $state_master =   DB::select($select_state); 
                foreach($state_master as $st)
                {
                   $st_arr[] = $st->state_name;
                }
            }
            else
            {
                $select_state = "SELECT st.state_name,st.region_id region_id
    FROM  `tbl_region_area_map` ar_map
    INNER JOIN district_master dist ON ar_map.dist_id = dist.dist_id
    INNER JOIN `state_master` st ON dist.state_id=st.state_id $region_str";
                $state_master =   DB::select($select_state); 
                
                foreach($state_master as $st)
                {
                    //$st_det = StateMaster::whereRaw("state_id='{$st->state_id}'")->first();
                   //$st_arr[] = $st_det->state_name;
                    $st_arr[] = $st->state_name;

                   $reg_det = RegionMaster::whereRaw("region_id='{$st->region_id}'")->first();
                   $region_arr[] = $reg_det->region_name;

                } 
            }
            }
            $region_arr = array_unique($region_arr);
            $st_arr = array_unique($st_arr);
            sort($region_arr);
            sort($st_arr);
            $record->region = implode(",",$region_arr);
            $record->state = implode(",",$st_arr);
            $dataNew[] = $record;
        }
        
        $ut_master = UserType::whereRaw("user_show='1'")->orderBy("user_type")->get();
        
        return view('add-reg-man')
                ->with('DataArr', $dataNew)
                ->with('ut_master', $ut_master)
                ->with('url', $url); 
    }
    
    public function save_region_man(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('pass');
        $name = $request->input('man_name');
        $mobile = $request->input('phone');
        $user_type = $request->input('user_type'); 
        $region_id = $request->input('region_id');
        
        $data_emjson = User::where("email",$email)->first();
        $data_em = json_decode($data_emjson,true);
        
        $data_mojson = RegionalManagerMaster::where("phone",$mobile)->first();
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
            $UserType           = $user_type;
            
            $userArr->name=$name;
            $userArr->email=$email;
            $userArr->password=$password_hash;
            $userArr->password2=$password;
            $userArr->UserType=$user_type;
            $userArr->verify_email="Yes";
            $userArr->verified_at=date('Y-m-d H:i:s');
            $UserId = Auth::user()->id;
            $userArr->table_name='ho';
            $userArr->created_by = $UserId;
            
            if($userArr->save()){
                $wu_id = $userArr->id;
                $seUser = new RegionalManagerMaster();
                $seUser->man_name = $name;
                $seUser->pass = $password;
                $seUser->email = $email;
                $seUser->phone = $mobile;
                $seUser->region_id = $region_id;
                $seUser->user_type=$user_type;
                $seUser->man_status = 1;
                $seUser->created_by = $UserId;
                $seUser->LogIn_Id = $wu_id;
                
                
                $seUser->save(); 
                
                
                Session::flash('message', "$user_type Created Successfully.");
                Session::flash('alert-class', 'alert-success');
            }
            else{
                Session::flash('error', "$user_type Creation Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
                return back();
            } 
            
            return redirect('add-reg-man');
        }
        
        
    }
    
    public function edit_region_man(Request $request)
    {
        Session::put("page-title","Edit Regional Manager");
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        
        $reg_man_id = base64_decode($request->input('reg_man_id')); 
        $data_json = RegionalManagerMaster::whereRaw("reg_man_id='$reg_man_id'")->first();
        $data = json_decode($data_json,true);
        $url = $_SERVER['APP_URL'].'/add-reg-man';
        //print_r($data); exit;
        $ut_master = UserType::whereRaw("user_show='1'")->orderBy("user_type")->get();
        return view('edit-reg-man')
        ->with('url', $url)   
        ->with('ut_master', $ut_master)        
        ->with('data',$data);
    }
    
    public function update_region_man(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('pass');
        $name = $request->input('man_name');
        $mobile = $request->input('phone');
        //$vendor_id = $request->input('vendor_id');
        $reg_man_id = $request->input('reg_man_id'); 
        $man_status = $request->input('man_status'); 
        
        $data_emjson = RegionalManagerMaster::whereRaw("email='$email' and reg_man_id!='$reg_man_id'")->first();
        $data_em = json_decode($data_emjson,true);
        
        $data_mojson = RegionalManagerMaster::whereRaw("phone='$mobile' and reg_man_id!='$reg_man_id'")->first();
        $data_mo = json_decode($data_mojson,true);
        
        $data_record = RegionalManagerMaster::whereRaw(" reg_man_id='$reg_man_id'")->first();
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
            $userArr['UserActive']=$man_status;
            $UserId = Auth::user()->id;
            $userArr['updated_by']=$UserId;
            
            
            if(!empty($password))
            {
                $userArr['password']=$password_hash;
                $userArr['password2']=$password;
            }
            
            
            
            
            if(User::whereRaw("email='{$data['email']}'")->update($userArr)){
                $seUser = array();
                $seUser['man_name'] = $name;
                
                $seUser['pass'] = $password;
                $seUser['email'] = $email;
                $seUser['phone'] = $mobile;
                $seUser['man_status'] = $man_status;
                //$seUser['center_id'] = $vendor_id;
                $seUser['updated_by']=$UserId; 
                
                RegionalManagerMaster::whereRaw("reg_man_id='$reg_man_id'")->update($seUser);
                Session::flash('message', "Regional Manager Details Updated Successfully.");
                Session::flash('alert-class', 'alert-success');
            }
            else{
                Session::flash('error', "Regional Manager Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('edit-reg-man?reg_man_id='. base64_encode($reg_man_id));
        }
    }
    
    public function map_region(Request $request)
    {
        Session::put("page-title","Map Area/Region");
        $regional_man  = RegionalManagerMaster::whereRaw(" man_status='1' ")->orderByRaw('man_name ASC')->get();
        $country_json           =   CountryMaster::orderByRaw('country_name ASC')->get();
        $region_json           =   RegionMaster::orderByRaw('region_name ASC')->get();
        
        $country_master = json_decode($country_json);
        $region_master = json_decode($region_json);
        
        $data_pin = array();
        $url = $_SERVER['APP_URL'].'/map-region';
        return view('rm-area')
                ->with('regional_man',$regional_man)
                ->with('url', $url)
                ->with('countryArr',$country_master)
                ->with('region_master',$region_master)
                ->with('data_pin',$data_pin);
    }
    
    public function save_map_area(Request $request)
    {
        $country_id = $request->input('country'); 
        $state_id = addslashes($request->input('state_id')); 
        $reg_man_id_type = addslashes($request->input('reg_man_id'));   
        $dist_id = $request->input('dist_id'); 
        $region_id = $request->input('region_id'); 
        $reg_man_id_type_arr = explode('_',$reg_man_id_type);
        $reg_man_id = $reg_man_id_type_arr[0];
        $user_type = $reg_man_id_type_arr[1];
        Session::flash('form_id', '1');
        
        if($user_type=='RSM' || $user_type=='NSM')
        {
            if($region_id=='All')
            {
                $region_mas = RegionMaster::whereRaw("region_status='1'")->get();
                $area_master = array();
                $UserId = Auth::user()->id;
                $RegionalManagerMaster = RegionalManagerMaster::whereRaw("reg_man_id='$reg_man_id'")->first();
                    $index = 0;
                foreach($region_mas as $regmas)
                {
                    $region_id = $regmas->region_id;
                    $data_area_exist_json = RegAreaMaster::whereRaw("reg_man_id='$reg_man_id' and region_id='$region_id'")->first();
                    $data_area_exist = json_decode($data_area_exist_json,true);
                    if(!empty($data_area_exist))
                    {
                        continue;
                    }
                    $area_master[$index]['region_id']=$region_id;
                    $area_master[$index]['reg_man_id']=$reg_man_id;
                    $area_master[$index]['created_by'] = $UserId;
                    $area_master[$index++]['user_type'] = $RegionalManagerMaster->user_type;    
                }
                
                if(!empty($area_master) &&  RegAreaMaster::insert($area_master))
                {
                    Session::flash('message', "Area Mapped Successfully."); 
                    Session::flash('alert-class', 'alert-success');
                    return redirect('map-region');
                }
                else
                {

                    Session::flash('error', "Area Already Mapped."); 
                    Session::flash('alert-class', 'alert-danger');
                    return back();
                }
            }
            else
            {
               $data_area_exist_json = RegAreaMaster::whereRaw("reg_man_id='$reg_man_id' and region_id='$region_id'")->first();
                $data_area_exist = json_decode($data_area_exist_json,true);
                if(!empty($data_area_exist))
                {
                    Session::flash('error', "Region Allready Exist."); 
                    Session::flash('alert-class', 'alert-danger');

                    return back();
                }
            
            
            $area_master = new RegAreaMaster();
            $area_master->region_id=$region_id;
            $area_master->reg_man_id=$reg_man_id;
            $UserId = Auth::user()->id;
            $area_master->created_by = $UserId;

            //get user type
            $RegionalManagerMaster = RegionalManagerMaster::whereRaw("reg_man_id='$reg_man_id'")->first();
            $area_master->user_type = $RegionalManagerMaster->user_type;

            if($area_master->save())
            {
                Session::flash('message', "Area Mapped Successfully."); 
                Session::flash('alert-class', 'alert-success');
                return redirect('map-region');
            }
            else
            {
                
                Session::flash('error', "Area Not Mapped. Please Try Again Later."); 
                Session::flash('alert-class', 'alert-danger');
                return back();
            } 
            }
            
            
            
            
        }
        else if($user_type=='ASM' ||$user_type=='Coordinator' ||$user_type=='Store' ||$user_type=='BSM' ||$user_type=='Account' )
        {
            $data_area_exist_json = RegAreaMaster::whereRaw("reg_man_id='$reg_man_id' and dist_id='$dist_id'")->first();
            $data_area_exist = json_decode($data_area_exist_json,true);
        
            if(!empty($data_area_exist))
            {
                Session::flash('error', "Area Allready Exist."); 
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if($dist_id=='All')
            {
                $all_area =DistrictMaster::whereRaw("state_id='$state_id'")->get();
                $RegionalManagerMaster = RegionalManagerMaster::whereRaw("reg_man_id='$reg_man_id'")->first();
                $state_det = StateMaster::whereRaw("state_id='$state_id'")->first();

                //print_r(json_decode($RegionalManagerMaster)); exit;

                foreach($all_area as $area)
                {
                    $area_master = new RegAreaMaster();
                    $area_master->dist_id=$area->dist_id;
                    $area_master->reg_man_id=$reg_man_id;
                    $UserId = Auth::user()->id;
                    $area_master->created_by = $UserId;

                    //get user type
                    $area_master->user_type = $RegionalManagerMaster->user_type; //exit;

                    //get region from state

                    $area_master->region_id = $state_det->region_id;
                    $area_master->save();


                }
                Session::flash('message', "Area Mapped Successfully."); 
                    Session::flash('alert-class', 'alert-success');
                    return redirect('map-region');
            }
            else 
            {
                $area_master = new RegAreaMaster();
                    $area_master->dist_id=$dist_id;
                    $area_master->reg_man_id=$reg_man_id;
                    $UserId = Auth::user()->id;
                    $area_master->created_by = $UserId;

                    //get user type
                    $RegionalManagerMaster = RegionalManagerMaster::whereRaw("reg_man_id='$reg_man_id'")->first();
                    $area_master->user_type = $RegionalManagerMaster->user_type; 

                    //get region from state
                    $state_det = StateMaster::selectRaw("state_id='$state_id'")->first();
                    $area_master->region_id = $state_det->region_id;
                    //$area_master->save();

                if($area_master->save())
                {
                    Session::flash('message', "Area Mapped Successfully."); 
                    Session::flash('alert-class', 'alert-success');
                    return redirect('map-region');
                }
                else
                {
                    Session::flash('error', "Area Not Mapped. Please Try Again Later."); 
                    Session::flash('alert-class', 'alert-danger');
                    return back();
                }

            }
        }
        //print_r($dist_id); exit;
        
        
        
    }
    
    public function get_map_area(Request $request)
    {
        $country_id = $request->input('country_id');
        $state_id = $request->input('state_id');
        $region_id = $request->input('region_id');
        $reg_man_id_type = $request->input('reg_man_id');
        $reg_man_id_type_arr = explode('_',$reg_man_id_type);
        $reg_man_id = $reg_man_id_type_arr[0];
        $user_type = $reg_man_id_type_arr[1];
        
        $qry = "";
        $qry1 = "";
        
        if(!empty($state_id))
        {
            
            $qry .= " and sm.state_id= '$state_id'";
        }
        if(!empty($reg_man_id))
        {
            
            $qry .= " and rm.reg_man_id= '$reg_man_id'";
            $qry1 .= " and rm.reg_man_id= '$reg_man_id'";
        }
        if(!empty($country_id))
        {
            
            $qry .= " and cm.country_id= '$country_id'";
        }
        if(!empty($region_id))
        {
            
            $qry .= " and regm.region_id= '$region_id'";
            $qry1 .= " and regm.region_id= '$region_id'";
        }
        
        if($user_type=='ASM' || $user_type=='Coordinator' || $user_type=='Store' || $user_type=='BSM' || $user_type=='Account' )
        {
        $select = "SELECT ar_map.reg_map_id,country_name,state_name,man_name,dm.dist_name,region_name
FROM  `tbl_region_area_map` ar_map
INNER JOIN `tbl_region_manager` rm ON ar_map.reg_man_id = rm.reg_man_id
LEFT JOIN `region_master` regm ON ar_map.region_id=regm.region_id
LEFT JOIN `district_master` dm ON ar_map.dist_id=dm.dist_id
LEFT JOIN `state_master` sm ON dm.state_id=sm.state_id
LEFT JOIN country_master cm ON sm.country_id = cm.country_id

WHERE 1=1 $qry order by state_name"; 
        }
        else
        {
            $select = "SELECT ar_map.reg_map_id,man_name,region_name
FROM  `tbl_region_area_map` ar_map
INNER JOIN `tbl_region_manager` rm ON ar_map.reg_man_id = rm.reg_man_id
INNER JOIN `region_master` regm ON ar_map.region_id=regm.region_id
WHERE 1=1 $qry1 order by region_name";
        }
        $area_master = DB::select($select);
        
        
        if(empty($area_master))
        {
            echo 'No Records Found'; exit;
        }
            echo '<table id="table1" class="table table-striped table-bordered" style="width:100%">';
            
            echo '<tr>';
                echo '<th>Sr. No.</th>';
                
                echo '<th> Name</th>';
                echo '<th>Region</th>';
                if($user_type=='ASM' || $user_type=='Coordinator' || $user_type=='Store' || $user_type=='BSM' || $user_type=='Account')
                {
                    echo '<th>State</th>';
                    echo '<th>District</th>';
                }
                echo '<th>Action</th>';
            echo '</tr>';
        
        
        $srno=1;
        foreach($area_master as $pin)
        {
            echo '<tr>';
                echo '<td>'.$srno++.'</td>';
                echo '<td>'.$pin->man_name.'</td>';
                echo '<td>'.$pin->region_name.'</td>';
                if($user_type=='ASM' || $user_type=='Coordinator' || $user_type=='Store' || $user_type=='BSM' || $user_type=='Account')
                {
                echo '<td>'.$pin->state_name.'</td>';
                echo '<td>'.$pin->dist_name.'</td>';
                }
                //echo '<td>'.$pin->vendor.'</td>';
                $pin_id = $pin->reg_map_id;
                echo '<td><a href="#" onclick="remove_area('."'$pin_id'".')">Remove</a></td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }
    
    public function remove_map_area(Request $request)
    {

        $reg_map_id = $request->input("reg_map_id");
        if(RegAreaMaster::whereRaw("reg_map_id='$reg_map_id'")->delete())
        {
            echo "1";
        }
        else
        {
            echo '0';
        }
        exit;
    }
    
}

