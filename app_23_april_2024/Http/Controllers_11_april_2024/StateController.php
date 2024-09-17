<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\CountryMaster;
use App\StateMaster;
use App\RegionMaster;
use App\DistrictMaster;
use Session;
use DB;
use Auth;

class StateController extends Controller
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
        Session::put("page-title","State");
        $country_json           =   CountryMaster::orderByRaw('country_name ASC')->get(); 
        $country_master = json_decode($country_json);
        
        $region_json           =   RegionMaster::orderByRaw('region_name ASC')->get(); 
        $region_master = json_decode($region_json);
        
        
        $url = $_SERVER['APP_URL'].'/add-state';
        return view('add-state')->with('url', $url)
                ->with('countryArr', $country_master)
                ->with('region_master', $region_master);
    }
    
    public function save_state(Request $request)
    {
        $country_id = $request->input('country');
        $region_id = $request->input('region_id');
        $state_name = addslashes($request->input('state_name')); 
        
        $data_state_exist_json = StateMaster::whereRaw("country_id = '$country_id' and state_name='$state_name'")->first();
        $data_state_exist = json_decode($data_state_exist_json,true);
        
        if(!empty($data_state_exist))
        {
            Session::flash('error', "State Allready Exist."); 
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else 
        {
            $state_master = new StateMaster();
            $state_master->country_id=$country_id;
            $state_master->region_id=$region_id;
            $state_master->state_name=$state_name;
            $UserId = Auth::user()->id;  
            $state_master->created_by=$UserId; 
            if($state_master->save())
            {
                Session::flash('message', "State Name Saved Successfully."); 
                Session::flash('alert-class', 'alert-success');
                return redirect('add-state');
            }
            else
            {
                Session::flash('error', "State Not Saved. Please Try Again Later."); 
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            
        }
        
    }
    
    
    public function state_exist(Request $request)
    {
        $country_id = $request->input('country_id'); 
        $state_name = addslashes($request->input('state_name')); 
       // $state_id = addslashes($request->input('state_id')); 
        
         $data_state_exist_json = StateMaster::whereRaw(" country_id = '$country_id' and state_name='$state_name'")->first();
        $data_state_exist = json_decode($data_state_exist_json,true);
        
        //print_r($data_state_exist); exit;
        
        if(!empty($data_state_exist))
        {
            echo 1;
        }
        else
        {
            echo 2;
        }
        
    }
    
    
    public function state_exist_update()
    {
        $country_id = $request->input('country_id'); 
        $state_name = addslashes($request->input('state_name')); 
        $state_id = addslashes($request->input('state_id')); 
        
        $data_state_exist_json = StateMaster::whereRaw("state_id='$state_id' and country_id = '$country_id' and state_name='$state_name'")->first();
        $data_state_exist = json_decode($data_state_exist_json,true);
        
        if(!empty($data_state_exist))
        {
            echo 1;
        }
        else
        {
            echo 2;
        }
        
    }
    
    public function view_state(Request $request)
    {
        Session::put("page-title","State");
        $country_json           =   CountryMaster::orderByRaw('country_name DESC')->get(); 
        $country_master = json_decode($country_json);
        
        return view('view-state')
                ->with('countryArr', $country_master);
    }
    
    public function get_state(Request $request)
    {
        //Session::put("page-title","State");
        $country_id = $request->input('country_id'); 
        
        if($country_id!='All')
        {
            $state_master = DB::select("SELECT state_id,country_name,state_name,region_name
FROM `state_master` sm
left join region_master rm on sm.region_id = rm.region_id
INNER JOIN country_master cm ON sm.country_id = cm.country_id
WHERE sm.country_id= '$country_id' order by state_name");
            //$state_master = json_decode($data_state_json,true);
        }
        else
        {
            $state_master = DB::select("SELECT state_id,country_name,state_name,region_name
FROM `state_master` sm
left join region_master rm on sm.region_id = rm.region_id
INNER JOIN country_master cm ON sm.country_id = cm.country_id");
            //$state_master = json_decode($data_state_json);
        }
        if(empty($state_master))
        {
            echo 'No Records Found'; exit;
        }
        echo '<table id="table1" class="table table-striped table-bordered" style="width:100%">';
            echo '<tr>';
                echo '<th>Sr. No.</th>';
                echo '<th>Country</th>';
                echo '<th>Region</th>';
                echo '<th>State</th>';
                echo '<th>Action</th>';
            echo '</tr>';
        
        
        $srno=1;
        foreach($state_master as $state)
        {
            echo '<tr>';
                echo '<td>'.$srno++.'</td>';
                echo '<td>'.$state->country_name.'</td>';
                echo '<td>'.$state->region_name.'</td>';
                echo '<td>'.$state->state_name.'</td>';
                $state_id = $state->state_id;
                echo '<td><a href="edit-state?state_id='.$state_id.'">Edit</a></td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }
    
    public function edit_state(Request $request)
    {
        Session::put("page-title","Edit State");
        $state_id = $request->input('state_id'); 
        
        $country_json           =   CountryMaster::orderByRaw('country_name DESC')->get(); 
        $country_master = json_decode($country_json);
        
        $region_json           =   RegionMaster::orderByRaw('region_name ASC')->get(); 
        $region_master = json_decode($region_json);
        
        $state_record_json = StateMaster::whereRaw("state_id='$state_id'")->first();
        $data_state_record = json_decode($state_record_json,true);
        
        //print_r($data_state_record); exit;
        
        $url = $_SERVER['APP_URL'].'/add-state';
         return view('edit-state')
        ->with('url', $url)
                ->with('countryArr', $country_master)
                 ->with('data_state_record',$data_state_record)
                  ->with('region_master', $region_master);
    }
    
    public function update_state(Request $request)
    {
        $country_id = $request->input('country_id'); 
        $state_name = addslashes($request->input('state_name')); 
        $state_id = addslashes($request->input('state_id')); 
        $region_id = $request->input('region_id');
        
        $data_state_exist_json = StateMaster::whereRaw("state_id!='$state_id' and country_id = '$country_id' and state_name='$state_name'")->first();
        $data_state_exist = json_decode($data_state_exist_json,true);
        
        if(!empty($data_state_exist))
        {
            Session::flash('error', "State Allready Exist."); 
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else 
        {
            $state_master = new StateMaster();
            $state_master->country_id=$country_id;
            $state_master->state_name=$state_name;
            $UserId = Auth::user()->id;  
            
            
            if(StateMaster::where('state_id',$state_id)->update(array('country_id'=>$country_id,'state_name'=>$state_name,'region_id'=>$region_id,'created_by'=>$UserId)))
            {
                Session::flash('message', "State Details Updated Successfully."); 
                Session::flash('alert-class', 'alert-success');
                return redirect('edit-state?state_id='.$state_id);
            }
            else
            {
                Session::flash('error', "State Not Saved. Please Try Again Later."); 
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            
        }
    }
    
    public function add_district()
    {
        Session::put("page-title","District");
        $district_json           =   DistrictMaster::orderByRaw('dist_name ASC')->get(); 
        $district_master = json_decode($district_json);
        
        $state_json           =   StateMaster::orderByRaw('state_name ASC')->get(); 
        $state_master = json_decode($state_json);
        
        $url = $_SERVER['APP_URL'].'/add-district';
        return view('add-district')->with('url', $url)
                ->with('stateArr', $state_master)
                ->with('district_arr', $region_json);
    }
    
    public function save_district(Request $request)
    {
        $country_id = $request->input('country'); 
        $dist_name = addslashes($request->input('dist_name'));
        //$div_name = addslashes($request->input('div_name'));
        
        $data_state_exist_json = DistrictMaster::whereRaw("state_id = '$country_id'  and dist_name='$dist_name'")->first();
        $data_state_exist = json_decode($data_state_exist_json,true);
        
        if(!empty($data_state_exist))
        {
            Session::flash('error', "District Allready Exist."); 
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else 
        {
            $state_master = new DistrictMaster();
            $state_master->state_id=$country_id;
           // $state_master->div_name=$div_name;
            $state_master->dist_name=$dist_name;
            $UserId = Auth::user()->id;  
            $state_master->created_by=$UserId; 
            if($state_master->save())
            {
                Session::flash('message', "District Name Saved Successfully."); 
                Session::flash('alert-class', 'alert-success');
                return redirect('add-district');
            }
            else
            {
                Session::flash('error', "District Not Saved. Please Try Again Later."); 
                Session::flash('alert-class', 'alert-danger');
                return back();
            }    
        }
        
    }
    

    public function get_district(Request $request)
    {
        //Session::put("page-title","State");
        $country_id = $request->input('state_id'); 

        if($country_id!='All')
        {
            $state_master = DB::select("SELECT dist_id,state_name,div_name,dist_name
FROM `district_master` dm
INNER JOIN state_master sm ON dm.state_id = sm.state_id
WHERE dm.state_id= '$country_id' order by state_name,dist_name");
            //$state_master = json_decode($data_state_json,true);
        }
        else
        {
            $state_master = DB::select("SELECT dist_id,state_name,div_name,dist_name
FROM `district_master` dm
INNER JOIN state_master sm ON dm.state_id = sm.state_id
 order by state_name,dist_name");
            //$state_master = json_decode($data_state_json);
        }
        if(empty($state_master))
        {
            echo 'No Records Found'; exit;
        }
        echo '<table id="table1" class="table table-striped table-bordered" style="width:100%">';
            echo '<tr>';
                echo '<th>Sr. No.</th>';
                echo '<th>State</th>';
                
                echo '<th>District</th>';
                echo '<th>Action</th>';
                echo '<th>Delete</th>';
            echo '</tr>';


        $srno=1;
        foreach($state_master as $state)
        {
            echo '<tr>';
                echo '<td>'.$srno++.'</td>';
                echo '<td>'.$state->state_name.'</td>';
                
                echo '<td>'.$state->dist_name.'</td>';
                $state_id = $state->dist_id;
                echo '<td><a href="edit-district?dist_id='.$state_id.'">Edit</a></td>';
                echo '<td><a href="delete-district?dist_id='.$state_id.'">Delete</a></td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }
    public function delete_district(Request $request)
    {

        $district_id = $request->input("dist_id");
        if(DistrictMaster::whereRaw("dist_id='$district_id'")->delete())
        {
            Session::flash('message', "District Deleted Successfully.");
            Session::flash('alert-class', 'alert-success');
        }
        else
        {
            Session::flash('message', "District Not Deleted.");
            Session::flash('alert-class', 'alert-danger');
        }
        return redirect('add-district');
    }

    public function edit_district(Request $request)
    {
        Session::put("page-title","Edit State");
        $dist_id = $request->input('dist_id'); 

        $country_json           =   StateMaster::orderByRaw('state_name ASC')->get(); 
        $country_master = json_decode($country_json);
        $state_record_json = DistrictMaster::whereRaw("dist_id='$dist_id'")->first();
        $data_state_record = json_decode($state_record_json,true);

        //print_r($data_state_record); exit;

        $url = $_SERVER['APP_URL'].'/add-district';
         return view('edit-district')
        ->with('url', $url)
                ->with('stateArr', $country_master)
                 ->with('data_state_record',$data_state_record);
    }

    public function update_district(Request $request)
    {
        $state_id = $request->input('state_id'); 
        $dist_name = addslashes($request->input('dist_name'));
       // $div_name = addslashes($request->input('div_name'));
        $dist_id = addslashes($request->input('dist_id')); 

        $data_state_exist_json = DistrictMaster::whereRaw("dist_id!='$dist_id' and state_id = '$state_id'  and dist_name='$dist_name'")->first();
        $data_state_exist = json_decode($data_state_exist_json,true);

        if(!empty($data_state_exist))
        {
            Session::flash('error', "District Allready Exist."); 
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else 
        {
            $state_master = new DistrictMaster();
            $state_master->state_id=$state_id;
            $state_master->dist_name=$dist_name;
            $UserId = Auth::user()->id;  

            //print_r($dist_name); exit;

            if(DistrictMaster::where('dist_id',$dist_id)->update(array('state_id'=>$state_id,'dist_name'=>$dist_name,'updated_by'=>$UserId)))
            {
                Session::flash('message', "District Details Updated Successfully."); 
                Session::flash('alert-class', 'alert-success');
                return redirect('edit-district?dist_id='.$dist_id);
            }
            else
            {
                Session::flash('error', "District Not Saved. Please Try Again Later."); 
                Session::flash('alert-class', 'alert-danger');
                return back();
            }

        }
    }

    public function get_division_by_state_id(Request $request)
    {
        //Session::put("page-title","State");
        $state_id = $request->input('state_id'); 


            $state_master = DB::select("SELECT dist_id,state_name,div_name
FROM `district_master` dm
INNER JOIN state_master sm ON dm.state_id = sm.state_id
WHERE dm.state_id= '$state_id' order by state_name,div_name");
            //$state_master = json_decode($data_state_json,true);

        if(empty($state_master))
        {
            echo '<option value="">No Records Found</option>'; exit;
        }
        echo '<option value="">select</option>';
        foreach($state_master as $state)
        {
            echo '<option value="';
                echo $state->div_name;
                echo '">'.$state->div_name.'</option>';
        }

        exit;
    }
    
    public function get_district_by_div_name(Request $request)
    {
        //Session::put("page-title","State");
        $state_id = $request->input('state_id');
        $div_name = $request->input('div_name');


            $state_master = DB::select("SELECT dist_id,state_name,dist_name
FROM `district_master` dm
INNER JOIN state_master sm ON dm.state_id = sm.state_id
WHERE dm.state_id= '$state_id' and div_name='$div_name' order by state_name,div_name");
            //$state_master = json_decode($data_state_json,true);

        if(empty($state_master))
        {
            echo '<option value="">No Records Found</option>'; exit;
        }
        echo '<option value="">select</option>';
        foreach($state_master as $state)
        {
            echo '<option value="';
                echo $state->dist_id;
                echo '">'.$state->dist_name.'</option>';
        }

        exit;
    }
    
    public function get_district_by_state_id(Request $request)
    {
        //Session::put("page-title","State");
        $state_id = $request->input('state_id');
        $all = $request->input('all');
        $state_str = ""; 
        
        if(is_array($state_id) && $state_id[0] != "All") {

            $state_str = " and dm.state_id IN ('" . implode("','", $state_id) . "')";
        }

        if($state_id!='All' && !is_array($state_id))
        {
            $state_str = " and dm.state_id= '$state_id'";
        }    

        $state_master = DB::select("SELECT dist_id,state_name,dist_name
        FROM `district_master` dm
        INNER JOIN state_master sm ON dm.state_id = sm.state_id
        WHERE 1=1 $state_str  order by dist_name");
        //$state_master = json_decode($data_state_json,true);

        if(empty($state_master))
        {
            echo '<option value="">No Records Found</option>'; exit;
        }
        if($all!='1')
        {
            echo '<option value="">select</option>';
        }
        foreach($state_master as $state)
        {
            echo '<option value="';
                echo $state->dist_id;
                echo '">'.$state->dist_name.'</option>';
        }

        exit;
    }
    
    public function get_district_by_state_id_map(Request $request)
    {
        //Session::put("page-title","State");
        $state_id = $request->input('state_id'); 

        $state_str = ""; 
     
        if($state_id !='All')
        {
            $state_str = " and dm.state_id= '$state_id'";
            
        }
        // if($state_id =='All')
        // {
        //     $checkbox_selected = "checked"; 
        // }

        $dist_master = DB::select("SELECT dist_id,state_name,dist_name
            FROM `district_master` dm
            INNER JOIN state_master sm ON dm.state_id = sm.state_id
            WHERE 1=1 $state_str order by state_name,dist_name");
                        //$state_master = json_decode($data_state_json,true);

        if(empty($dist_master) && $state_id !='All')
        {
            echo 'No Records Found'; exit;
        }
        // echo '<input type="checkbox" name="chkAll" id="chkAll"> All';
        
        foreach($dist_master as $dist)
        {
            $dist_id = $dist->dist_id;
            $dist_name = $dist->dist_name;
            echo '<div class="col-md-3"><div class="position-relative form-group">';
            echo '<input type="checkbox" name="chk[]"  onclick="get_pins();" id="chk'.$dist_id.'" value="'.$dist_id.'">&nbsp;'.$dist_name;
            echo '</div></div>';
        }

        exit;
    }
    
    public function get_state_by_region_id(Request $request)
    {
        //Session::put("page-title","State");
        $region_id = $request->input('region_id');
        $all = $request->input('all');
        
        if($region_id!='All' && !empty($region_id))
        {
            $hrm = "and rm.region_id='$region_id'";
        }

            $state_master = DB::select("SELECT st.* from state_master st inner join region_master rm on st.region_id = rm.region_id where 1=1 $hrm");
            //$state_master = json_decode($data_state_json,true);

        if(empty($state_master))
        {
            echo '<option value="">No States Found</option>'; exit;
        }
      //  if($all==1)
       // {
            echo '<option value="All">All</option>'; 
       // }
        
        $state_arr = array();
        foreach($state_master as $st)
        {
            $state_arr[$st->state_id] = $st->state_name;
        }
        
        asort($state_arr);
        
        foreach($state_arr as $st=>$name)
        {
            echo '<option value="';
                echo $st.'">';
                echo $name.'</option>';
        }
        exit;
        
        

        exit;
    }
    
    public function get_state_id_by_region_id(Request $request)
    {
        //Session::put("page-title","State");
        $region_id = $request->input('region_id');
        $all = $request->input('all');
        
        if($region_id!='All' && !empty($region_id))
        {
            $hrm = "and rm.region_id='$region_id'";
        }

        $state_master = DB::select("SELECT st.* from state_master st inner join region_master rm on st.region_id = rm.region_id where 1=1 $hrm");
            //$state_master = json_decode($data_state_json,true);

        if(empty($state_master))
        {
            echo '<option value="">No States Found</option>'; exit;
        }
        if($all=='1')
        {
            echo '<option value="All">All</option>'; 
        }
        
        $state_arr = array();
        foreach($state_master as $st)
        {
            $state_arr[$st->state_id] = $st->state_name;
        }
        
        asort($state_arr);
        
        foreach($state_arr as $st=>$name)
        {
            echo '<option value="';
                echo $st.'">';
                echo $name.'</option>';
        }
        exit;
        
        

        exit;
    }
    
}

