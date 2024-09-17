<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\CountryMaster;
use App\StateMaster;
use App\PincodeMaster;
use App\User;
use App\PinVendorList;
use App\DistrictMaster;
use Session;
use DB;
use Auth;

class PinCodeController extends Controller
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
        Session::put("page-title","Pincode");
        $country_json           =   CountryMaster::orderByRaw('country_name ASC')->get(); 
        $country_master = json_decode($country_json);
        
        
        
        $url = $_SERVER['APP_URL'].'/add-pincode';
        return view('add-pincode')
        ->with('url', $url)
                ->with('countryArr', $country_master)
                ;
    }
    
    public function save_pincode(Request $request)
    {
        $country_id = $request->input('country'); 
        $state_id = addslashes($request->input('state_id'));
        $dist_id = addslashes($request->input('dist_id'));
        $place = addslashes($request->input('place')); 
        $pincode = addslashes($request->input('pincode')); 
        //$vendor_id = $request->input('vendor_id'); 
        //$vendorArr = $request->input('vendor'); 
        
        //print_r($vendor); exit;
        
        $data_pincode_exist_json = PincodeMaster::whereRaw("country_id = '$country_id' and state_id='$state_id' and dist_id='$dist_id' and place='$place' and pincode='$pincode'")->first();
        $data_pincode_exist = json_decode($data_pincode_exist_json,true);
        
        if(!empty($data_pincode_exist))
        {
            Session::flash('error', "Pincode Allready Exist to $place"); 
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else 
        {
            $pincode_master = new PincodeMaster();
            $pincode_master->country_id=$country_id;
            $pincode_master->state_id=$state_id;
            $pincode_master->dist_id=$dist_id;
            $pincode_master->place=$place;
            $pincode_master->pincode=$pincode;
            //$pincode_master->vendor_id=$vendor_id;
            $UserId = Auth::user()->id;
            $pincode_master->created_by = $UserId;
            
            if($pincode_master->save())
            {
                $pin_id = $pincode_master->id; 
//                foreach($vendorArr as $vendor_id)
//                {
//                    $vendor_list = array();
//                    $vendor_list['Pin_Id'] = $pin_id;
//                    $vendor_list['Vendor_Id'] = $vendor_id;
//                    $vendor_list['created_by'] = $UserId;
//                    $vendor_list['created_at'] = date('Y-m-d H:i:s');
//                    $vendor_list['updated_by'] = $UserId;
//                    $vendor_list['updated_at'] = date('Y-m-d H:i:s');
//                    $inserArr[] = $vendor_list;
//                }
//                
//                PinVendorList::insert($inserArr);
                
                Session::flash('message', "Pincode Saved Successfully."); 
                Session::flash('alert-class', 'alert-success');
                return redirect('add-pincode');
            }
            else
            {
                Session::flash('error', "Pincode Not Saved. Please Try Again Later."); 
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            
        }
        
    }
    
    
    public function pincode_exist(Request $request)
    {
        $country_id = $request->input('country_id'); 
        $state_id = addslashes($request->input('state_id')); 
        $place = $request->input('place'); 
        $pincode = addslashes($request->input('pincode')); 
        
       // $state_id = addslashes($request->input('state_id')); 
        
         $data_pincode_exist_json = PincodeMaster::whereRaw(" country_id = '$country_id' and state_id='$state_id' and place='$place' and pincode='$pincode'")->first();
        $data_pincode_exist = json_decode($data_pincode_exist_json,true);
        
        //print_r($data_state_exist); exit;
        
        if(!empty($data_pincode_exist))
        {
            echo 1;
        }
        else
        {
            echo 2;
        }
        
    }
    
    
    public function pincode_exist_update()
    {
        $country_id = $request->input('country_id'); 
        $state_id = addslashes($request->input('state_id'));
        $place = addslashes($request->input('place'));
        $pincode = addslashes($request->input('pincode'));
        $pin_id = addslashes($request->input('pin_id')); 
        
        $data_pin_exist_json = StateMaster::whereRaw("pin_id='$pin_id' and country_id = '$country_id' and state_id='$state_id' and place='$place' and pincode='$pincode'")->first();
        $data_state_exist = json_decode($data_pin_exist_json,true);
        
        if(!empty($data_state_exist))
        {
            echo 1;
        }
        else
        {
            echo 2;
        }
        
    }
    
    public function view_pincode(Request $request)
    {
        $country_json           =   CountryMaster::orderByRaw('country_name DESC')->get(); 
        $country_master = json_decode($country_json);
        
        return view('view-pincode')
                ->with('countryArr', $country_master);
    }
    
    public function get_states(Request $request)
    {
        $country_id = $request->input('country_id'); 
        
        
            $state_master = DB::select("SELECT state_id,country_name,state_name
FROM `state_master` sm
INNER JOIN country_master cm ON sm.country_id = cm.country_id
WHERE sm.country_id= '$country_id'");
            //$state_master = json_decode($data_state_json,true);
        
        
        if(empty($state_master))
        {
            echo '<option value="">No Records Found</option>'; exit;
        }
        
        
        echo '<option value="">Select</option>';
        echo '<option value="All">All</option>';
       
        foreach($state_master as $state)
        {
            echo '<option ';
                echo 'value="'.$state->state_id.'">';
                
                echo $state->state_name;
                
                
            echo '</option>';
        } 
        
        exit;
    }
    
    public function get_pincode(Request $request)
    {
        //$country_id = $request->input('country_id');
        $state_id = $request->input('state_id');
        
        $qry = "";
        
        /*if($country_id!='All')
        {
            
            $qry .= " and pc.country_id= '$country_id'";
        }*/
        
        if($state_id!='All')
        {
            
            $qry .= " and pc.state_id= '$state_id'";
        }
        
        $pin_master = DB::select("SELECT pc.pin_id,pc.country_id,country_name,region_name,state_name,div_name,dist_name,place,pincode
FROM  `pincode_master` pc
INNER JOIN `state_master` sm ON pc.state_id=sm.state_id
INNER JOIN `region_master` rm ON sm.region_id=rm.region_id
LEFT JOIN `district_master` dm ON pc.dist_id=dm.dist_id
INNER JOIN country_master cm ON sm.country_id = cm.country_id
WHERE 1=1 $qry order by state_name,place");
        
        
        if(empty($pin_master))
        {
            echo 'No Records Found'; exit;
        }
        echo '<table id="table1" class="table table-striped table-bordered" style="width:100%">';
            echo '<tr>';
                echo '<th>Sr. No.</th>';
                echo '<th>Region</th>';
                echo '<th>State</th>';
                
                echo '<th>District</th>';
                echo '<th>Place</th>';
                echo '<th>Pincode</th>';
                
                echo '<th>Action</th>';
            echo '</tr>';
        
        
        $srno=1;
        foreach($pin_master as $pin)
        {
            echo '<tr>';
                echo '<td>'.$srno++.'</td>';
                echo '<td>'.$pin->region_name.'</td>';
                echo '<td>'.$pin->state_name.'</td>';
                
                echo '<td>'.$pin->dist_name.'</td>';
                echo '<td>'.$pin->place.'</td>';
                echo '<td>'.$pin->pincode.'</td>';
                //echo '<td>'.$pin->vendor.'</td>';
                $pin_id = $pin->pin_id;
                echo '<td><a href="edit-pincode?pin_id='.$pin_id.'&country_id='.$pin->country_id.'">Edit</a></td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }
    
    
    public function edit_pincode(Request $request)
    {
        Session::put("page-title","Edit Pincode");
        $pin_id = $request->input('pin_id'); 
        $country_id = $request->input('country_id'); 
        
        $country_json           =   CountryMaster::orderByRaw('country_name ASC')->get(); 
        $country_master = json_decode($country_json);
        
        $state_json           =   StateMaster::whereRaw("country_id='$country_id'")->orderByRaw('state_name ASC')->get(); 
        $state_master = json_decode($state_json);
        
        
        
        
        $pin_record_json = PincodeMaster::whereRaw("pin_id='$pin_id'")->first();
        $data_pin_record = json_decode($pin_record_json,true);
        $state_id = $data_pin_record['state_id'];
        
        $dist_json           =   DistrictMaster::whereRaw("state_id='$state_id'")->orderByRaw('dist_name ASC')->get(); 
        $dist_master = json_decode($dist_json);
        
        $vendor_json           =   User::whereRaw("UserType='Vendor' and UserActive='1'")->orderByRaw('name ASC')->get(); 
        $Vendor_master = json_decode($vendor_json);
        
        $pinvendorlist_json           =   PinVendorList::whereRaw("Pin_Id='$pin_id'")->get(); 
        $PinVendorList_master = json_decode($pinvendorlist_json);
        
        $vendorList = array();
        foreach($PinVendorList_master as $vlist)
        {
            $vendorList[] =  $vlist->Vendor_Id;
        }
        
        $url = $_SERVER['APP_URL'].'/add-pincode';
         return view('edit-pincode')
        ->with('url', $url)
                ->with('countryArr', $country_master)
                 ->with('state_master', $state_master)
                 ->with('dist_master', $dist_master)
                 ->with('vendorArr', $Vendor_master)
                 ->with('vendorList', $vendorList)
                 ->with('data_pin_record',$data_pin_record);
    }
    
    public function update_pincode(Request $request)
    {
        $country_id = $request->input('country_id'); 
        $state_id = addslashes($request->input('state_id'));
        $dist_id = addslashes($request->input('dist_id'));
        $pin_id = addslashes($request->input('pin_id')); 
        $place = addslashes($request->input('place')); 
        $pincode = addslashes($request->input('pincode')); 
        //$vendor_id = $request->input('vendor_id'); 
        //$vendorArr = $request->input('vendor'); 
        
        $data_pin_exist_json = PincodeMaster::whereRaw("pin_id!='$pin_id' and country_id = '$country_id' and state_id='$state_id' and dist_id='$dist_id' and place='$place' and pincode='$pincode'")->first();
        $data_pin_exist = json_decode($data_pin_exist_json,true);
        
        if(!empty($data_pin_exist))
        {
            Session::flash('error', "Pin Allready Exist."); 
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else 
        {
            
            $UserId = Auth::user()->id;
            
            
            
            
            if(PincodeMaster::where('pin_id',$pin_id)->update(array('country_id'=>$country_id,'state_id'=>$state_id,'dist_id'=>$dist_id,
                'place'=>$place,'pincode'=>$pincode,'updated_by'=>$UserId)))
            {
//                PinVendorList::where('pin_id',$pin_id)->delete();
//                foreach($vendorArr as $vendor_id)
//                {
//                        $vendor_list = array();
//                        $vendor_list['Pin_Id'] = $pin_id;
//                        $vendor_list['Vendor_Id'] = $vendor_id;
//                        $vendor_list['created_by'] = $UserId;
//                        $vendor_list['created_at'] = date('Y-m-d H:i:s');
//                        $vendor_list['updated_by'] = $UserId;
//                        $vendor_list['updated_at'] = date('Y-m-d H:i:s');
//                        $inserArr[] = $vendor_list;
//                        
//                        
//                }
//                
//                PinVendorList::insert($inserArr);
                
                Session::flash('message', "Pincode Details Updated Successfully."); 
                Session::flash('alert-class', 'alert-success');
                return redirect("edit-pincode?pin_id=$pin_id&country_id=$country_id");
            }
            else
            {
                Session::flash('error', "Pincode Not Saved. Please Try Again Later."); 
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            
        }
    }
    
    
    public function get_pincode_by_state(Request $request)
    {
        //$country_id = $request->input('country_id');
        $state_id = $request->input('state_id');
        
        $qry = "";
        
        
        
        if($state_id!='All')
        {
            
            $qry .= " and state_id= '$state_id'";
        }
        
        $pin_master = DB::select("SELECT Pin_Id,pincode from pincode_master where state_id='$state_id'");
        
        
        if(empty($pin_master))
        {
            echo '<option value="">No Pincode Found</option>'; exit;
        }
        
        echo '<option value="">Pincode</option>'; 
        
        
        $pin_arr = array();
        foreach($pin_master as $pin)
        {
            $pin_arr[$pin->pincode] = $pin->pincode;
        }
        
        sort($pin_arr);
        
        foreach($pin_arr as $pin)
        {
            echo '<option value="';
                echo $pin.'">';
                echo $pin.'</option>';
        }
        exit;
    }
    
    public function get_pincode_by_state_name(Request $request)
    {
        //$country_id = $request->input('country_id');
        $state_name = $request->input('state_name');
        
        $qry = "";
        
        $state = StateMaster::whereRaw("state_name = '$state_name'")->first();
        $state_id = $state->state_id;
        
        if($state_id!='All')
        {
            
            $qry .= " and state_id= '$state_id'";
        }
        
        $pin_master = DB::select("SELECT DISTINCT pincode from pincode_master where state_id='$state_id'");
        
        
        if(empty($pin_master))
        {
            echo '<option value="">No Pincode Found</option>'; exit;
        }
        
        echo '<option value="">Pincode</option>'; 
        
        $pin_arr = array();
        foreach($pin_master as $pin)
        {
            $pin_arr[$pin->pincode] = $pin->pincode;
        }
        
        sort($pin_arr);
        
        foreach($pin_arr as $pin)
        {
            echo '<option value="';
                echo $pin.'">';
                echo $pin.'</option>';
        }
        exit;
    }
    
   public function get_pincode_by_state_id(Request $request)
    {
        //$country_id = $request->input('country_id');
        $state_name = $request->input('state_id');
        
        $qry = "";
        
        $state = StateMaster::whereRaw("state_name = '$state_name'")->first();
        $state_id = $state->state_id;
        
        if($state_id!='All')
        {
            
            $qry .= " and state_id= '$state_id'";
        }
        
        $pin_master = DB::select("SELECT Pin_Id,pincode from pincode_master where state_id='$state_id'");
        
        
        if(empty($pin_master))
        {
            echo '<option value="">No Pincode Found</option>'; exit;
        }
        
        echo '<option value="">Pincode</option>'; 
        
        
        $pin_arr = array();
        foreach($pin_master as $pin)
        {
            $pin_arr[$pin->pincode] = $pin->pincode;
        }
        
        sort($pin_arr);
        
        foreach($pin_arr as $pin)
        {
            echo '<option value="';
                echo $pin.'">';
                echo $pin.'</option>';
        }
        exit;
    } 
    
   public function get_pincode_by_dist_id(Request $request)
    {
        //$country_id = $request->input('country_id');
        $dist_id = $request->input('dist_id');
        $all = $request->input('all');
        
        $qry = "";
        
        $pin_master = DB::select("SELECT pincode from pincode_master where dist_id='$dist_id'");
        
        
        if(empty($pin_master))
        {
            echo '<option value="">No Pincode Found</option>'; exit;
        }
        
        if($all=='1')
        {
            echo '<option value="All">All</option>'; 
        }
        $pin_arr = array();
        foreach($pin_master as $pin)
        {
            $pin_arr[$pin->pincode] = $pin->pincode;
        }
        foreach($pin_arr as $pin)
        {
            echo '<option value="';
                echo $pin.'">';
                echo $pin.'</option>';
        }
        exit;
    }  
    
    
    public function get_pincode_by_mdist_id(Request $request)
    {
        //$country_id = $request->input('country_id');
        $dist_ids = $request->input('dist_ids');
        $dist_str = $dist_ids.'0';
        
        
        $qry = "";
        //echo "SELECT Pin_Id,pincode from pincode_master where dist_id in ($dist_str)"; exit;
        $pin_master = DB::select("SELECT Pin_Id,place,pincode from pincode_master where dist_id in ($dist_str)");
        
        
        if(empty($pin_master))
        {
            echo '<option value="">No Pincode Found</option>'; exit;
        }
        
        echo '<option value="All">All</option>'; 
        
        
        foreach($pin_master as $pin)
        {
            echo '<option value="';
                echo $pin->pincode.'">';
                echo $pin->place.' - '.$pin->pincode.'</option>';
        }
        exit;
    }  
    
    public function get_area_by_pincode(Request $request)
    {
        //$country_id = $request->input('country_id');
        $pincode = $request->input('pincode');
        
        
        
        $qry = "";
        //echo "SELECT Pin_Id,pincode from pincode_master where dist_id in ($dist_str)"; exit;
        $pin_master = DB::select("SELECT pin_id,place from pincode_master where pincode = '$pincode'");
        
        
        if(empty($pin_master))
        {
            echo '<option value="">No Area Found</option>'; exit;
        }
        $area_list = array();
        foreach($pin_master as $pin)
        {
            $area_list[$pin->pin_id] = $pin->place;
        }
        
        
        
        foreach($area_list as $areaId=>$area)
        {
            echo '<option value="';
                echo $areaId.'">';
                echo $area.'</option>';
        }
        exit;
    }  
    
}

