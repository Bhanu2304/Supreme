<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\ServiceCenter;
use App\RegionMaster;
use App\StateMaster;
use App\PincodeMaster;
use Illuminate\Support\Facades\Hash;
use App\User;
use DB;
use Auth;
use App\CountryMaster;
use Session;
use App\SCProductMaster;
use App\SCPinMaster;
use App\BrandMaster;
use App\ProductCategoryMaster;
use App\ProductMaster;
use App\ModelMaster;
use App\DistrictMaster;

class ServiceCenterController extends Controller
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
    
    public function add_center()
    {
        Session::put("page-title","Service Center Creation");
        $clientId = Session::get('Client_Id');
        
        
        $region_json           =   RegionMaster::whereRaw("region_status=1")->orderByRaw('region_name ASC')->get(); 
        $region_master = json_decode($region_json,true);
        
        $state_json           =   StateMaster::whereRaw("state_status=1")->orderByRaw('state_name ASC')->get(); 
        $state_master = json_decode($state_json,true);
        
        $data_pin_json =   PincodeMaster::whereRaw("pin_status='1'")
                ->orderByRaw('pincode ASC')->get(); 
        $data_pin = json_decode($data_pin_json);
        
        $data_json =   ServiceCenter::whereRaw("sc_status='1'")
                ->orderByRaw('center_name ASC')->get(); 
        $data_sc = json_decode($data_json);
        
        $data           =   DB::select("SELECT tsc.*,sm.state_name,rm.region_name,dm.dist_name FROM tbl_service_centre tsc
INNER JOIN `state_master` sm ON tsc.state = sm.state_id
left JOIN `district_master` dm ON tsc.dist_id = dm.dist_id
INNER JOIN `region_master` rm ON tsc.region = rm.region_id order by center_name"); 
        
        $url = $_SERVER['APP_URL'].'/add-centre';

        $str_server = str_replace('public', '', $_SERVER['APP_URL']); 
        
        return view('add-center')
                ->with('data_sc',$data_sc)
                ->with('url', $url)
                ->with('region_master',$region_master)
                ->with('state_master',$state_master)
                ->with('str_server',$str_server)
                ->with('data_pin',$data_pin)
                ->with('DataArr', $data); 
    }
    public function save_centre(Request $request)
    {
        
        $center_name = addslashes($request->input("center_name"));
        $person_name = addslashes($request->input("person_name"));
        $contact_no = addslashes($request->input("contact_no"));
        $alt_no1 = addslashes($request->input("alt_no1"));
        $alt_no2 = addslashes($request->input("alt_no2"));
        $alt_no3 = addslashes($request->input("alt_no3"));
        $email_id = addslashes($request->input("email_id"));
        //$asc_code = addslashes($request->input("asc_code"));
        $region = addslashes($request->input("region"));
        $address = addslashes($request->input("address"));
        $dist_id = addslashes($request->input("dist_id"));
        $state = addslashes($request->input("state"));
        $pincode = addslashes($request->input("pincode"));
        $bank_name = addslashes($request->input("bank_name"));
        $bank_add = addslashes($request->input("bank_add"));
        $acc_no = addslashes($request->input("acc_no"));
        $ifsc = addslashes($request->input("ifsc"));
        $pan_no = addslashes($request->input("pan_no"));
        $gst_no = addslashes($request->input("gst_no"));
        $bill_add = addslashes($request->input("bill_add"));
        $ship_add = addslashes($request->input("ship_add"));
        
        $center_remark = addslashes($request->input("center_remark"));
        $password = $request->input('password');
        
        
        
        $state_det = StateMaster::whereRaw("state_id='$state'")->first();
        $state_code = $state_det->state_code;
        $sub_city = substr($city, 0, 2);
        $sub_city = strtoupper($sub_city);
        $no = ServiceCenter::count();
        if(empty($no))
        {
            $no = 0;
        }
        
        
        $str_no = "000";
        $no = $no+1;
        $len = strlen($str_no);
        $newlen = strlen("$no");
        $new_no = substr_replace($str_no, $no, $len-$newlen,$newlen);
        $asc_code = "$state_code"."$sub_city".$new_no;
        
        
        
        $ServiceCenter = new ServiceCenter();
        $ServiceCenter->center_name=$center_name;
        $ServiceCenter->person_name=$person_name;
        $ServiceCenter->contact_no=$contact_no;
        $ServiceCenter->alt_no1=$alt_no1;
        $ServiceCenter->alt_no2=$alt_no2;
        $ServiceCenter->alt_no3=$alt_no3;
        $ServiceCenter->email_id=$email_id;
        $ServiceCenter->asc_code=$asc_code;
        $ServiceCenter->region=$region;
        $ServiceCenter->address=$address;
        $ServiceCenter->dist_id=$dist_id;
        $ServiceCenter->state=$state;
        $ServiceCenter->pincode=$pincode;
        $ServiceCenter->bank_name=$bank_name;
        $ServiceCenter->bank_add=$bank_add;
        $ServiceCenter->acc_no=$acc_no;
        $ServiceCenter->ifsc=$ifsc;
        $ServiceCenter->pan_no=$pan_no;
        $ServiceCenter->gst_no=$gst_no;
        $ServiceCenter->bill_add=$bill_add;
        $ServiceCenter->ship_add=$ship_add;
        $ServiceCenter->center_remark=$center_remark;
        $ServiceCenter->sc_status=1;
        
        
        
        
        
        $data_json  = ServiceCenter::whereRaw("center_name='$center_name' ")->first(); 
        $data = json_decode($data_json,true);
        
        $email_sc_json  = ServiceCenter::whereRaw("email_id='$email_id' ")->first(); 
        $email_sc = json_decode($email_sc_json,true);
        
        $asc_sc_json  = ServiceCenter::whereRaw("asc_code='$asc_code' ")->first(); 
        $asc_sc = json_decode($asc_sc_json,true);
        
        $email_us_json  = User::whereRaw("email='$email_id' ")->first(); 
        $email_us = json_decode($email_us_json,true); 

        // $fileNames = ['upload_image1', 'upload_image2', 'upload_image3', 'upload_image4', 'upload_image5'];
        // foreach ($fileNames as $fileInputName)
        // {

        //     $file = request()->file($fileInputName);
        //     print_r($file);
        // }die;
        
        if(empty($center_name))
        {
           Session::flash('message', "Please Fill Centre Name"); 
           Session::flash('alert-class', 'alert-danger');
           return back();
        }
        else if(empty($person_name) )
        {
           Session::flash('message', "Please Fill Personal Name"); 
           Session::flash('alert-class', 'alert-danger');
           return back();
        }
        else if(empty($contact_no))
        {
           Session::flash('message', "Please Fill Contact No."); 
           Session::flash('alert-class', 'alert-danger');
          return back();
        }
        else if(empty($email_id))
        {
           Session::flash('message', "Please Fill ASC Code."); 
           Session::flash('alert-class', 'alert-danger');
          return back();
        }
        else if(empty($asc_code))
        {
           Session::flash('message', "Please Choose Field Validation"); 
           Session::flash('alert-class', 'alert-danger');
          return back();
        }
        else if(empty($pincode))
        {
           Session::flash('message', "Please Fill Pincode"); 
           Session::flash('alert-class', 'alert-danger');
          return back();
        }
        else if(empty($state))
        {
           Session::flash('message', "Please Fill State."); 
           Session::flash('alert-class', 'alert-danger');
          return back();
        }
        else if(!empty($data))
        {   
            Session::flash('message', "Centre Allready Exist"); 
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(!empty($email_sc))
        {   
            Session::flash('message', "Email Allready Exist."); 
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(!empty($email_us))
        {   
            Session::flash('message', "Email Allready Exist."); 
            Session::flash('alert-class', 'alert-danger');
            return back();
        }

        //print_r($Remarks); exit;
        else if($ServiceCenter->save())
        { 
            $fileNames = ['upload_image1', 'upload_image2', 'upload_image3', 'upload_image4', 'upload_image5'];
            foreach ($fileNames as $fileInputName)
            {
                if(!empty(request()->file($fileInputName)))
                {
                    $file = request()->file($fileInputName);
                    $today_date = date('Y_m_d_h_i_s');
                    $todayDate = now()->format('Y_m_d_h_i_s');
                    $extension = $todayDate . '.' . $file->getClientOriginalExtension();
                    #$filePath = $file->storeAs("{$ServiceCenter->id}", "{$fileInputName}_{$extension}", 'service_center');
                    $filePath = $file->storeAs("service_center/{$ServiceCenter->id}", "{$fileInputName}_{$extension}");
                    $taggingArr[$fileInputName] = "{$fileInputName}_{$extension}";
                    ServiceCenter::where('center_id', $ServiceCenter->id)->update($taggingArr);
                }
            }


            $password_hash           =   Hash::make($password);
            $userArr            =   new User();
            $userArr->name=$person_name;
            $userArr->email=$email_id;
            $userArr->password=$password_hash;
            $userArr->password2=$password;
            $userArr->UserType='ServiceCenter';
            $userArr->table_name='tbl_service_centre';
            $userArr->table_id=$ServiceCenter->id;
            $userArr->verify_email="Yes";
            $userArr->verified_at=date('Y-m-d H:i:s');
            $UserId = Auth::user()->id;
            $userArr->created_by = $UserId;
            $userArr->save();
            
            
          Session::flash('message', "Centre $center_name with $asc_code Saved Successfully.");
          Session::flash('alert-class', 'alert-success');
          activity()
            ->withProperties(json_decode($ServiceCenter,true))
            ->log('Action_Field Save'); //Saving Activity
        }
        else
        {
            Session::flash('message', "Centre $center_name Not Saved. Please Contact To Admin");
            Session::flash('alert-class', 'alert-danger');
            
        }
        return redirect('add-centre');
        //return view('project-view'); 
    }
    
    public function edit_centre(Request $request)
    {
        Session::put("page-title","Service Center Edit");
        $center_id = base64_decode($request->input("center_id"));
        
        $region_json           =   RegionMaster::whereRaw("region_status=1")->orderByRaw('region_name ASC')->get(); 
        $region_master = json_decode($region_json,true);
        
        $state_json           =   StateMaster::whereRaw("state_status=1")->orderByRaw('state_name ASC')->get(); 
        $state_master = json_decode($state_json,true);
        
        
        
        $data_json =   ServiceCenter::whereRaw("center_id='$center_id'")->first(); 
        $data_sc = json_decode($data_json);
        $state_id = $data_sc->state;
        
        $dist_json =   DistrictMaster::whereRaw("state_id='$state_id'")->orderBy('dist_name')->get(); 
        $dist_sc = json_decode($dist_json,true);
        
        
        $data_pin_json =   PincodeMaster::whereRaw("state_id='$state_id'")
                ->orderByRaw('pincode ASC')->get(); 
        $data_pin = json_decode($data_pin_json);
        //$data           =   DB::select("SELECT * from tbl_service_centre order by center_name"); 
        $url = $_SERVER['APP_URL'].'/add-centre';

        $str_server = str_replace('public', '', $_SERVER['APP_URL']); 
        
        return view('edit-center')
                ->with('data_sc',$data_sc)
                ->with('url', $url)
                ->with('region_master',$region_master)
                ->with('state_master',$state_master)
                ->with('str_server', $str_server)
                ->with('data_pin',$data_pin)
                ->with('district_master',$dist_sc);
    }
    
    
    public function update_centre(Request $request)
    {
        $center_name = addslashes($request->input("center_name"));
        $person_name = addslashes($request->input("person_name"));
        $contact_no = addslashes($request->input("contact_no"));
        $alt_no1 = addslashes($request->input("alt_no1"));
        $alt_no2 = addslashes($request->input("alt_no2"));
        $alt_no3 = addslashes($request->input("alt_no3"));
        $email_id = addslashes($request->input("email_id"));
        $asc_code = addslashes($request->input("asc_code"));
        $region = addslashes($request->input("region"));
        $address = addslashes($request->input("address"));
        $dist_id = addslashes($request->input("dist_id"));
        $state = addslashes($request->input("state"));
        $pincode = addslashes($request->input("pincode"));
        $center_remark = addslashes($request->input("center_remark"));
        $center_id = $request->input("center_id");
        $sc_status = $request->input("sc_status");
        $password = $request->input('password');
        $bank_name = addslashes($request->input("bank_name"));
        $bank_add = addslashes($request->input("bank_add"));
        $acc_no = addslashes($request->input("acc_no"));
        $ifsc = addslashes($request->input("ifsc"));
        $pan_no = addslashes($request->input("pan_no"));
        $gst_no = addslashes($request->input("gst_no"));
        $bill_add = addslashes($request->input("bill_add"));
        $ship_add = addslashes($request->input("ship_add"));
        
        
        
        $ServiceCenter = array();
        $ServiceCenter['center_name']=$center_name;
        $ServiceCenter['person_name']=$person_name;
        $ServiceCenter['contact_no']=$contact_no;
        $ServiceCenter['alt_no1']=$alt_no1;
        $ServiceCenter['alt_no2']=$alt_no2;
        $ServiceCenter['alt_no3']=$alt_no3;
        $ServiceCenter['email_id']=$email_id;
        $ServiceCenter['region']=$region;
        $ServiceCenter['address']=$address;
        $ServiceCenter['dist_id']=$dist_id;
        $ServiceCenter['state']=$state;
        $ServiceCenter['pincode']=$pincode;
        $ServiceCenter['bank_name']=$bank_name;
        $ServiceCenter['bank_add']=$bank_add;
        $ServiceCenter['acc_no']=$acc_no;
        $ServiceCenter['ifsc']=$ifsc;
        $ServiceCenter['pan_no']=$pan_no;
        $ServiceCenter['gst_no']=$gst_no;
        $ServiceCenter['bill_add']=$bill_add;
        $ServiceCenter['ship_add']=$ship_add;
        $ServiceCenter['center_remark']=$center_remark;
        $ServiceCenter['sc_status']=$sc_status;
        
        $data_json  = ServiceCenter::whereRaw("center_id!='$center_id' and center_name='$center_name' ")->first(); 
        $data = json_decode($data_json,true);
        
        $data_json  = ServiceCenter::whereRaw("center_id='$center_id' ")->first(); 
        $data_old = json_decode($data_json,true);
        $old_email = $data_json->email_id;
        
        $email_sc_json  = ServiceCenter::whereRaw("center_id!='$center_id' and email_id='$email_id' ")->first(); 
        $email_sc = json_decode($email_sc_json,true);
        
        $email_us_json  = User::whereRaw("email!='$old_email' and email='$email_id'")->first(); 
        $email_us = json_decode($email_us_json,true);
        
        if(empty($center_name))
        {
           Session::flash('message', "Please Fill Centre Name"); 
           Session::flash('alert-class', 'alert-danger');
        }
        else if(empty($person_name) )
        {
           Session::flash('message', "Please Fill Persona Name"); 
           Session::flash('alert-class', 'alert-danger');
        }
        else if(empty($contact_no))
        {
           Session::flash('message', "Please Fill Contact No."); 
           Session::flash('alert-class', 'alert-danger');
          
        }
        else if(empty($email_id))
        {
           Session::flash('message', "Please Fill ASC Code."); 
           Session::flash('alert-class', 'alert-danger');
          
        }
        else if(empty($asc_code))
        {
           Session::flash('message', "Please Choose Field Validation"); 
           Session::flash('alert-class', 'alert-danger');
          
        }
        else if(empty($pincode))
        {
           Session::flash('message', "Please Fill Pincode"); 
           Session::flash('alert-class', 'alert-danger');
          
        }
        else if(!isset($state))
        {
           Session::flash('message', "Please Fill State."); 
           Session::flash('alert-class', 'alert-danger');
          
        }
        else if(!empty($data))
        {   
            Session::flash('message', "Center Name Allready Exist"); 
            Session::flash('alert-class', 'alert-danger');
            
        }
        else if(!empty($email_sc))
        {   
            Session::flash('message', "Email Allready Exist."); 
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(!empty($email_us))
        {   
            Session::flash('message', "Email Allready Exist."); 
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        //print_r($Remarks); exit;
        else if(ServiceCenter::whereRaw("center_id='$center_id'")->update($ServiceCenter))
        {
            $fileNames = ['upload_image1', 'upload_image2', 'upload_image3', 'upload_image4', 'upload_image5'];
            foreach($fileNames as $fileInputName)
            {
                if(!empty(request()->file($fileInputName)))
                {
                    $file = request()->file($fileInputName);
                    $today_date = date('Y_m_d_h_i_s');
                    $todayDate = now()->format('Y_m_d_h_i_s');
                    $extension = $todayDate . '.' . $file->getClientOriginalExtension();
                    #$filePath = $file->storeAs("{$ServiceCenter->id}", "{$fileInputName}_{$extension}", 'service_center');
                    $filePath = $file->storeAs("service_center/{$center_id}", "{$fileInputName}_{$extension}");
                    $taggingArr[$fileInputName] = "{$fileInputName}_{$extension}";
                    ServiceCenter::where('center_id', $center_id)->update($taggingArr);
                }
            }

            if(!empty($password))
            {
                $password_hash           =   Hash::make($password);
            }
            $userArr            =   array();
            
            $userArr['name']=$person_name;
            $userArr['email']=$email_id;
            $userArr['UserActive']=$sc_status;
            $userArr['UserType']='ServiceCenter';
            $UserId = Auth::user()->id;
            $userArr['updated_by']=$UserId;
            
            
            User::whereRaw("email='$email_id'")->update($userArr);
            SCProductMaster::whereRaw("center_id='$center_id'")->update(array('UserActive'=>$sc_status));
            SCPinMaster::whereRaw("center_id='$center_id'")->update(array('UserActive'=>$sc_status));
            //print_r($userArr);
            //exit;
            
          Session::flash('message', "Center $center_name Updated Successfully.");
          Session::flash('alert-class', 'alert-success');
          activity()
            ->withProperties(json_decode($data_old,true))
            ->log('ServiceCenter Save'); //Saving Activity
        }
        else
        {
            Session::flash('message', "Center $center_name Not Saved. Please Contact To Admin");
            Session::flash('alert-class', 'alert-danger');
            
        }
        return redirect('add-centre');
        //return view('project-view'); 
    }
    
    public function map_pincode(Request $request)
    {
        Session::put("page-title","Map Pincode");
        $center_arr  = ServiceCenter::whereRaw("sc_status='1' ")->orderBy("center_name")->get();
        $country_json           =   CountryMaster::orderByRaw('country_name ASC')->get(); 
        $country_master = json_decode($country_json);
        $data_pin = array();
        $url = $_SERVER['APP_URL'].'/map-pincode';
        return view('sc-pincode')
                ->with('center_arr',$center_arr)
                ->with('url', $url)
                ->with('countryArr',$country_master)
                
                ->with('data_pin',$data_pin);
    }
    
    public function save_map_pincode(Request $request)
    {
        $country_id = $request->input('country'); 
        $state_id = addslashes($request->input('state_id')); 
        $center_id = addslashes($request->input('center_id')); 
        #$pincode_arrr = addslashes($request->input('pincode'));
        $pincode_arrr = $request->input('pincode');
        $dist_arr = $request->input('chk');
        //$dist_id = $request->input('dist_id'); 
        //$vendorArr = $request->input('vendor'); 
        //$dist_str = implode(",",$dist_arr);
        #print_r($request->all()); exit;
        #print_r($pincode_arrr);die;
        $record_Insert = '0';
        $pincode_mapped = '0';
        $mappedPincodes = [];
        foreach($pincode_arrr as $pincode)
        {   
            
            $data_pincode_exist_json = SCPinMaster::whereRaw("center_id='$center_id' and pincode='$pincode'")->first();
            $data_pincode_exist = json_decode($data_pincode_exist_json,true);
            // echo "center_id='$center_id' and pincode='$pincode'";
            // print_r($data_pincode_exist);die;
            if(!empty($data_pincode_exist))
            {
                Session::flash('error', "Pincode Already Exist."); 
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            #else if($state_id=='All')
            #{
                #$dist_master = DB::select("SELECT dist_id FROM `district_master` order by dist_name");
                #foreach($dist_master as $dist)
                #{
                    #$dist_id = $dist->dist_id;
                    // $all_pincode = PincodeMaster::whereRaw("pin_status='1'")->get();
                    // foreach($all_pincode as $PIN)
                    // {
                    //     #echo $PIN->state_id;die;
                    //     $pincode_master = new SCPinMaster();
                    //     $pincode_master->country_id=$country_id;
                    //     $pincode_master->state_id=$PIN->state_id;
                    //     $pincode_master->center_id=$center_id;
                    //     $pincode_master->pincode=$PIN->pincode;
                    //     $pincode_master->dist_id=$PIN->dist_id;
                    //     $UserId = Auth::user()->id;
                    //     $pincode_master->created_by = $UserId;
                    //     $pincode_master->save();

                    //     $record_Insert = '1';
                    // }
                #}
            #}
            else if($pincode=='All')
            {
                foreach($dist_arr as $dist_id)
                {
                    $all_pincode =PincodeMaster::whereRaw("dist_id='$dist_id'")->get();
                    #print_r($all_pincode);die;
                    foreach($all_pincode as $PIN)
                    {
                        #echo $PIN->state_id;die;
                        $pincode_master = new SCPinMaster();
                        $pincode_master->country_id=$country_id;
                        $pincode_master->state_id=$PIN->state_id;
                        $pincode_master->center_id=$center_id;
                        $pincode_master->pincode=$PIN->pincode;
                        $pincode_master->dist_id=$dist_id;
                        $UserId = Auth::user()->id;
                        $pincode_master->created_by = $UserId;
                        $pincode_master->save();

                        $record_Insert = '1';
                    }
                }

                if($record_Insert=='1')
                {
                    Session::flash('message', "Pincode $pincode Mapped Successfully."); 
                    Session::flash('alert-class', 'alert-success');
                    return redirect('map-pincode');
                }
                else
                {
                    Session::flash('message', "Pincode Not Mapped."); 
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('map-pincode');
                }
            }
            else 
            {
                $first_pincode =PincodeMaster::whereRaw("pincode='$pincode'")->first();
                $dist_id = $first_pincode->dist_id;
                
                $pincode_master = new SCPinMaster();
                $pincode_master->country_id=$country_id;
                $pincode_master->state_id=$state_id;
                $pincode_master->center_id=$center_id;
                $pincode_master->pincode=$pincode;
                $pincode_master->dist_id=$dist_id;
                $UserId = Auth::user()->id;
                $pincode_master->created_by = $UserId;
                
                
                if($pincode_master->save())
                {
                    $mappedPincodes[] = $pincode;
                    $pincode_mapped = '1';
                    // Session::flash('message', "Pincode $pincode Mapped Successfully."); 
                    // Session::flash('alert-class', 'alert-success');
                    // return redirect('map-pincode');
                }
                else
                {
                    // Session::flash('error', "Pincode Not Mapped. Please Try Again Later."); 
                    // Session::flash('alert-class', 'alert-danger');
                    // return back();
                }
                
            }

        }
        if($pincode_mapped = '1')
        {
            $message = "Pincodes mapped successfully: " . implode(', ', $mappedPincodes);
            Session::flash('message', $message); 
            Session::flash('alert-class', 'alert-success');
            return redirect('map-pincode');
        }else{

            Session::flash('error', "Pincode Not Mapped. Please Try Again Later."); 
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        

        
    }
    
    public function get_map_pincode(Request $request)
    {
        $country_id = $request->input('country_id');
        $state_id = $request->input('state_id');
        $center_id = $request->input('center_id');
        $pincode = $request->input('pincode');
        
        $qry = "";
        
        
        if(!empty($state_id))
        {
            
            $qry .= " and pins.state_id= '$state_id'";
        }
        if(!empty($center_id))
        {
            
            $qry .= " and pins.center_id= '$center_id'";
        }
        if(!empty($country_id))
        {
            
            $qry .= " and pins.country_id= '$country_id'";
        }
        if(!empty($pincode))
        {
            
            $qry .= " and pins.pincode= '$pincode'";
        }
        $pin_master = DB::select("SELECT pins.sc_pin_id,pins.country_id,country_name,state_name,center_name,pins.pincode,dm.dist_name
FROM  `tbl_service_centre_pins` pins
left JOIN `district_master` dm ON pins.dist_id=dm.dist_id
INNER JOIN `state_master` sm ON pins.state_id=sm.state_id
INNER JOIN country_master cm ON pins.country_id = cm.country_id
inner JOIN tbl_service_centre sc ON pins.center_id = sc.center_id
WHERE 1=1 $qry order by state_name");
        
        
        if(empty($pin_master))
        {
            echo 'No Records Found'; exit;
        }
        echo '<table id="table1" class="table table-striped table-bordered" style="width:100%">';
            echo '<tr>';
                echo '<th>Sr. No.</th>';
                echo '<th>Center Name</th>';
                echo '<th>Country</th>';
                echo '<th>State</th>';
                echo '<th>District</th>';
                echo '<th>Pincode</th>';
                
                echo '<th>Action</th>';
            echo '</tr>';
        
        
        $srno=1;
        foreach($pin_master as $pin)
        {
            echo '<tr>';
                echo '<td>'.$srno++.'</td>';
                echo '<td>'.$pin->center_name.'</td>';
                echo '<td>'.$pin->country_name.'</td>';
                echo '<td>'.$pin->state_name.'</td>';
                echo '<td>'.$pin->dist_name.'</td>';
                echo '<td>'.$pin->pincode.'</td>';
                //echo '<td>'.$pin->vendor.'</td>';
                $pin_id = $pin->sc_pin_id;
                echo '<td><a href="#" onclick="remove_pincode('."'$pin_id'".')">Remove</a></td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }
   
    public function remove_pincode(Request $request)
    {

        $sc_pin_id = $request->input("sc_pin_id");
        if(SCPinMaster::whereRaw("sc_pin_id='$sc_pin_id'")->delete())
        {
            echo "1";
        }
        else
        {
            echo '0';
        }
        exit;
    }
    
    public function map_product(Request $request)
    {
        Session::put("page-title","Map Product Details");
        #$center_arr  = ServiceCenter::whereRaw("sc_status='1' ")->get();
        #$center_arr = ServiceCenter::whereRaw("sc_status='1'")->orderBy('center_name')->get();

        $qr2 = "SELECT tsc.center_id,center_name,sm.state_name,city,pincode FROM tbl_service_centre tsc 
            INNER JOIN users us ON tsc.email_id = us.email 
            INNER JOIN state_master sm ON tsc.state =  sm.state_id
            WHERE sc_status='1' ORDER BY center_name";
        $center_arr           =   DB::select($qr2); 

        $brand_json           =   BrandMaster::orderByRaw('brand_name ASC')->get(); 
        $brand_master = json_decode($brand_json,true);
        $data_pin = array();
        $url = $_SERVER['APP_URL'].'/map-product-detail';
        return view('sc-product_detail')
                ->with('center_arr',$center_arr)
                ->with('url', $url)
                ->with('brand_master',$brand_master)
                
                ;
    }
    
    public function check_map_product_exist()
    {
        
    }
    
    public function save_map_product(Request $request)
    {
        $brand_id   = addslashes($request->input('brand_id'));
        $product_category_id   = addslashes($request->input('product_category_id'));
        $product_id   = addslashes($request->input('product_id'));
        $model_id   = addslashes($request->input('model_id'));
        $center_id = addslashes($request->input('center_id')); 
         
        $str = "";
        
        if($brand_id!='All')
        {
            $str .=" and brand_id='$brand_id' ";
        }
        if($product_category_id!='All')
        {
            $str .=" and product_category_id='$product_category_id' ";
        }
        if($product_id!='All')
        {
            $str .=" and product_id='$product_id'";
        }
        if($model_id!='All')
        {
            $str .=" and model_id='$model_id'";
        }
        //echo $str; exit;
        $model_arr = ModelMaster::whereRaw("1=1 $str")->get();
        $map_arr = array();
        foreach($model_arr as $model)
        {
            $brand_id = $model->brand_id;
            $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
            $brand = $brand_det->brand_name;
            $product_category_id   = $model->product_category_id;
            $cat_det = ProductCategoryMaster::whereRaw("product_category_id='$product_category_id'")->first();
            $Product_Detail = $cat_det->category_name;
            $product_id   = $model->product_id;
            $prod_det = ProductMaster::whereRaw("product_id='$product_id'")->first();
            $Product = $prod_det->product_name;
            $model_id   = $model->model_id;
            $model_det = ModelMaster::whereRaw("model_id='$model_id'")->first();
            $Model = $model_det->model_name;
            
            
            $data_pincode_exist_json = SCProductMaster::whereRaw("center_id='$center_id' and brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id'")->first();
            $data_pincode_exist = json_decode($data_pincode_exist_json,true);
            if(!empty($data_pincode_exist))
            {
                
            }
            else
            {
                $prod_master = array();
                $prod_master['brand_id']=$brand_id;
                $prod_master['product_category_id']=$product_category_id;
                $prod_master['product_id']=$product_id;
                $prod_master['model_id']=$model_id;
                $prod_master['Brand']=$brand;
                $prod_master['Product_Detail']=$Product_Detail;
                $prod_master['Product']=$Product;
                $prod_master['Model']=$Model;
                
                $prod_master['center_id']=$center_id;
                $UserId = Auth::user()->id;
                $prod_master['created_by'] = $UserId;
                $prod_master['created_at'] = date('Y-m-d H:i:s');
                $map_arr[] = $prod_master;
            }
            
        }
        
        if(!empty($data_pincode_exist))
        {
            Session::flash('error', "Product Allready Exist."); 
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else 
        {
            if(SCProductMaster::insert($map_arr))
            {
                
                Session::flash('message', "Product Detail Mapped Successfully."); 
                Session::flash('alert-class', 'alert-success');
                return redirect('map-product-detail');
            }
            else
            {
                Session::flash('error', "Product Not Mapped. Please Try Again Later."); 
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            
        }
        
    }
    
    
    public function get_map_product(Request $request)
    {
        $brand_search   = addslashes($request->input('brand_id'));
        $product_category_search   = addslashes($request->input('product_category_id'));
        $product_search   = addslashes($request->input('product_id'));
        $model_search   = addslashes($request->input('model_id'));
       
         $center_id = addslashes($request->input('center_id'));
        
        
         $whereRaw = "";
       
         if(!empty($center_id))
        {
            
            $whereRaw .= " and ta.center_id= '$center_id'";
        }
       
        
        if(!empty($brand_search) && $brand_search != "All")
        {
            $whereRaw = " and ta.brand_id='$brand_search'";
        }
        if(!empty($product_category_search) && $product_category_search != "All")
        {
            $whereRaw .= " and ta.product_category_id='$product_category_search'";
        }
        if(!empty($product_search) && $product_search != "All")
        {
            $whereRaw .= " and ta.product_id='$product_search'";
        }
        if(!empty($model_search) && $model_search != "All")
        {
            $whereRaw .= " and ta.model_id='$model_search'";
        }
        
       $qr = "SELECT ta.*,brand_name,category_name,product_name,model_name,cen.center_name FROM tbl_service_centre_product ta
inner join tbl_service_centre cen on ta.center_id = cen.center_id
INNER JOIN brand_master bm ON ta.brand_id = bm.brand_id AND brand_status='1' 
INNER JOIN product_category_master cm ON ta.brand_id=cm.brand_id AND ta.product_category_id = cm.product_category_id and category_status='1'
INNER JOIN product_master pm ON ta.brand_id=pm.brand_id AND ta.product_category_id = pm.product_category_id and ta.product_id = pm.product_id AND product_status='1' 
INNER JOIN model_master mm ON ta.brand_id=mm.brand_id AND ta.product_category_id = mm.product_category_id and ta.product_id = mm.product_id and ta.model_id = mm.model_id AND mm.model_status='1'
WHERE 1=1 $whereRaw
ORDER BY brand_name,category_name,product_name,model_name";
        $pin_master           =   DB::select($qr);
        
        if(empty($pin_master))
        {
            echo 'No Records Found'; exit;
        }
        echo '<table id="table1" class="table table-striped table-bordered" style="width:100%">';
            echo '<tr>';
                echo '<th>Sr. No.</th>';
                echo '<th>Center Name</th>';
                echo '<th>Brand</th>';
                echo '<th>Product Detail</th>';
                echo '<th>Product</th>';
                echo '<th>Model</th>';
                
                echo '<th>Action</th>';
            echo '</tr>';
        
        
        $srno=1;
        foreach($pin_master as $pin)
        {
            echo '<tr>';
                echo '<td>'.$srno++.'</td>';
                echo '<td>'.$pin->center_name.'</td>';
                echo '<td>'.$pin->brand_name.'</td>';
                echo '<td>'.$pin->category_name.'</td>';
                echo '<td>'.$pin->product_name.'</td>';
                echo '<td>'.$pin->model_name.'</td>';
                //echo '<td>'.$pin->vendor.'</td>';
                $pin_id = $pin->sc_product_id;
                echo '<td><a href="#" onclick="remove_pincode('."'$pin_id'".')">Remove</a></td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }
    public function get_asc_code(Request $request)
    {

        $sc_id = $request->input("asc_id");
        $ser = ServiceCenter::whereRaw("center_id='$sc_id'")->first();
        echo $ser->asc_code;exit;
        
        exit;
    }
    
}

