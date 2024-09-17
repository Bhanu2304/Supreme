<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\ManageAccess;
use App\PageMaster;
use App\ServiceCenter;
use App\ClosureCode;
use Session;
use DB;
use Auth;

class ManageController extends Controller
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
        Session::put("page-title","Manage User Rights");
        
        $UserType = Session::get('UserType');
        
        $where_user_type_in = "";
        if($UserType=='Admin')
        {
            $where_user_type_in = " and UserType in ('Admin','ServiceCenter','Supreme Coordinator','Manager','ASM','RSM','NSM')";
        }
        else if($UserType=='ServiceCenter')
        {
            $Center_Id = Auth::user()->table_id;
            $where_user_type_in = " and UserType in ('ServiceEngineer') and table_id='$Center_Id'";
        }
        else if($UserType=='ServiceEngineer')
        {
            $where_user_type_in = " and 1=0";
        }
        else if($UserType=='Supreme Coordinator')
        {
            $where_user_type_in = " and 1=0";
        }
        
        
        $userArr = User::whereRaw("UserActive='1' $where_user_type_in")->orderBy("email")->get();
        $url = $_SERVER['APP_URL'].'/manage-access';
        
            
            return view('manage-access')
                    ->with('userArr', $userArr)
                    ->with('pageArr', $pageArr)
                    ->with('url', $url)
                    ->with('pageMap',$pageMap);
    }
    
    public function get_access(Request $request)
    {
       //$data =   new AgentMaster($request->all()); 
        $user_id = $request->input('user');
        
        
        $pageArr = ManageAccess::whereRaw("UserId='$user_id'")->first();
        
        echo json_encode(array(array('pages_ride_ispark'=>json_decode($pageArr,true)))) ; 
        exit;    
    }
    
    public function print_tree($parent,$pageArr,$pageMap,$parentString)
    {
    
    $parentString .=$parent; 
    if(!empty($pageMap[$parent]))
    {
        ?>
<li><input type="checkbox" name="<?php echo $parentString;?>" id="<?php echo $parent;?>" value="<?php echo $parent;?>">
                     <label id="lbl<?php echo $parent;?>" for="<?php echo $parent;?>" class="custom-unchecked"><?php echo $pageArr[$parent]['page_name'];?></label>
                     <ul>
        <?php $parentString.='-';
        foreach($pageMap[$parent] as $page)
        {
            $this->print_tree($page,$pageArr,$pageMap,$parentString);   
        }?>
                     </ul></li>
   <?php }
    else
    {
        //echo '<li>'.$pageArr[$parent]['page_name'].'</li>';
        
       
        ?>
            <li><input type="checkbox" name="<?php echo $parentString;?>" id="<?php echo $parent;?>" value="<?php echo $parent;?>">
                     <label id="lbl<?php echo $parent;?>" for="<?php echo $parent;?>" class="custom-unchecked"><?php echo $pageArr[$parent]['page_name'];?></label>         
            </li>         
    <?php
        //echo "<li><div class=\"checkbox-primary\"><label><input class=\".checkbox-info\" type=\"checkbox\" name=\"selectAll[]\" id=\"" . $parent . "\"  value=\"" . $parent . "\"> " . $pageArr[$parent]['page_name'] . "</label></div></li>";
       
    }
    
    //echo '</li>';
}
    
    public function get_pages(Request $request) 
    {
        $user_id = $request->input('user');
        $User = User::selectRaw('UserType')->whereRaw("id='$user_id'")->first();
        $UserType = $User->UserType;
        
        
        $page_where_user_type = "";
        if($UserType=='Admin')
        {
            $page_where_user_type = " and ho ='1'";
        }
        else if($UserType=='ServiceCenter')
        {
            $page_where_user_type = " and sc ='1'";            
        }
        else if($UserType=='Manager')
        {
            $page_where_user_type = " and manager ='1'";
        }
        else if($UserType=='ASM')
        {
            $page_where_user_type = " and asm ='1'";
        }
        else if($UserType=='RSM')
        {
            $page_where_user_type = " and rsm ='1'";
        }
        else if($UserType=='NSM')
        {
            $page_where_user_type = " and nsm ='1'";
        }
        
        else if($UserType=='Supreme Coordinator')
        {
            $page_where_user_type = " and cord ='1'";
        }
        else if($UserType=='Store In charge')
        {
            $page_where_user_type = " and store ='1'";
        }
        $page_master = PageMaster::whereRaw("Active='1' $page_where_user_type")->orderByRaw("priority")->get();
        $pageArr = array();
        
        foreach($page_master as $page)
        {
            $pageMap[$page->parent_id][] = $page->id; 
            $pageArr[$page->id] = json_decode($page,true); 
        }
        
        //print_r($pageArr); exit;
        ?>
        
        <ul class="treeview">
        <?php  
            foreach($pageMap['0'] as $parent)
            {  ?>
                
            <?php //   echo "<li id='a".$parent."'><div class='checkbox-primary'><label><input type='checkbox' onchange=".'"show_child('."'".$parent."'".')"'."  name='selectAll[]' id='" . $parent . "'  value='" . $parent . "'> " . $pageArr[$parent]['page_name'];
                //echo "<li>".$pageArr[$parent]['page_name'];
                 $this->print_tree($parent,$pageArr,$pageMap,$parentString);  
            //    echo '</label></div></li>'; ?>
                
          <?php  }
        ?>
        </ul> 

        <?php
    }
    
    public function get_parents($id,$parent_arr)
    {
        //echo "id='$id'";
        $page = PageMaster::whereRaw("id='$id'")->first();
        $parent_arr[] = $page->parent_id;
        echo '<br>';
        print_r($parent_arr);
        if(!empty($page->parent_id))
        {
            return $this->get_parents($page->parent_id,$parent_arr);
        }
        else
        {
            return $parent_arr;
        }
    }
    public function save_access(Request $request)
    {
        $ride = $request->input('rides');
        $user_id = $request->input('user');
        $LoginId = Session::get("UserId");

        $ch = explode(",", $ride);
        
        $chr = array();
        foreach ($ch as $ot) {
            
            $chr[] = $ot;
            //;
            $chr = $this->get_parents($ot,$chr); 
            
        }
       
        //print_r($chr); exit;
        $ch = array_unique($chr);
        $query1 = "SELECT id,parent_id from pages_master WHERE ";
        foreach ($ch as $ot) {
            $query1.="id='$ot' OR ";
        }
        $query1 = substr($query1, 0, -4);

        $dd = DB::select($query1);

        $p = array();
        //$ch = array();
        $child = "";
        foreach ($dd as $row) {
            //$row = json_decode($row,true);
            if ($row->parent_id > 0) {
                //$p.=$row['pages_master_ispark']['parent_id'].",";
                array_push($p, $row->parent_id);
                $child.=$row->id . ",";
                //array_push($ch,$row['pages_master_ispark']['id']);
            } else {
                //$p.=$row['pages_master_ispark']['id'].",";
                array_push($p, $row->id);
            }
        }
		
        $pp = implode(",", array_unique($p));
        
        
        //print_r($child); exit;

        $check = DB::select("select Access_Id from manage_access WHERE UserId='" . $user_id . "'");
        
        //echo "INSERT INTO manage_access set UserId='" . $user_id . "', access='" . $child . "',parent_access='" . $pp . "'"; exit;
        
        //print_r($check); exit;
        
        if (empty($check)) {
            
            $manage_access = new ManageAccess();
            $manage_access->access = $child;
            $manage_access->parent_access = $pp;
            $manage_access->UserId = $user_id;
            $manage_access->created_by = $LoginId;
            if($manage_access->save())
            {
                echo "save"; exit;
            }
            else
            {
                echo "not save"; exit;
            }
            
        } 
        else 
        {
            $manage_access = array();
            $manage_access['access'] = $child;
            $manage_access['parent_access'] = $pp;
            $manage_access['updated_by'] = $LoginId;
            
            ManageAccess::whereRaw("UserId='$user_id'")->update($manage_access);
        }


        /* $email_id = $this->Session->read('email');
          $obj = $this->PageMaster->query("SELECT access,parent_access FROM pages_ride_ispark WHERE user_name='$email_id'");

          $arr = explode(",",$obj[0]['pages_ride_ispark']['access']);

          $query ="SELECT id,page_name,page_url FROM pages_master_ispark WHERE (";
          $q_as = ") AND parent_id='0'";
          foreach($arr as $ot){
          $query.="id='$ot' OR ";
          }
          $query = substr($query,0,-4);

          //$dd = $this->PageMaster->query("SELECT * FROM pages_master_ispark WHERE parent_id='0'");
          //$this->set('dd',$dd);

          $dd = $this->PageMaster->query($query.") AND parent_id='0'");
          //$this->set('dd',$dd);;
          $this->Session->write("dd",$dd); */
        
        echo "save"; exit;
    }
    
    
    public function manage_page_access()
    {
        Session::put("page-title","Manage Page Access");
        
        $UserType = Session::get('UserType');
        
        /*$where_user_type_in = "";
        if($UserType=='Admin')
        {
            $where_user_type_in = " and UserType in ('Admin','ServiceCenter','Coordinator')";
        }
        else if($UserType=='ServiceCenter')
        {
            $Center_Id = Auth::user()->table_id;
            $where_user_type_in = " and UserType in ('ServiceEngineer') and table_id='$Center_Id'";
        }
        else if($UserType=='ServiceEngineer')
        {
            $where_user_type_in = " and 1=0";
        }
        else if($UserType=='Coordinator')
        {
            $where_user_type_in = " and 1=0";
        }*/
        
        
        $pageArr = DB::select("SELECT * FROM `pages_master` 
WHERE active='1' 
 ORDER BY page_name");
        $userTypeArr = DB::select("SELECT * FROM `user_type_master` WHERE parent_name is not null order by user_type");
        
        $userTypeMaster = array();
        foreach($userTypeArr as $type)
        {
            $userTypeMaster[$type->parent_name][$type->page_column_name] = $type->user_type;
        }
        
        
        $url = $_SERVER['APP_URL'].'/manage-access';
        
            
            return view('manage-page-access')
                   ->with('userTypeMaster', $userTypeMaster)
                    ->with('pageArr', $pageArr)
                    ->with('url', $url);
    }
    
    public function save_page_access(Request $request)
    {
        $page_id_arr = $request->input('id_arr');
        
        
        
        foreach($page_id_arr as $page=>$page_Arr)
        {
            //print_r($page_Arr); exit;
            
            DB::update("update pages_master set $page='0'");
            
            foreach($page_Arr as $page_id)
            {
                DB::update("update pages_master set $page='1' where id='$page_id'");
                /*DB::update("update pages_master set $page='1' where parent_id='$page_id'");
                
                $pageArr2 = DB::select("SELECT * FROM `pages_master` WHERE active='1' and id='$page_id'");
                foreach($pageArr2 as $page2)
                {
                    $parent_id2 = $page2->parent_id;
                    DB::update("update pages_master set $page='1' where id='$parent_id2'");
                }*/
            }
        }
        
        Session::flash('message', "User Type Access has been updated successfully.");
        Session::flash('alert-class', 'alert-success');
        
        return redirect("manage-page-access");
        
    }

    public function manage_center_access()
    {
        Session::put("page-title","Manage Center Rights");
        
        $UserType = Session::get('UserType');
        
        $where_user_type_in = "";
        if($UserType=='Admin')
        {
            $where_user_type_in = " and UserType in ('ServiceCenter')";
        }
        else if($UserType=='ServiceCenter')
        {
            $Center_Id = Auth::user()->table_id;
            $where_user_type_in = " and UserType in ('ServiceEngineer') and table_id='$Center_Id'";
        }else{
            
            $where_user_type_in = " and 1=0";
        }
        
        
        $userArr = User::whereRaw("UserActive='1' $where_user_type_in")->orderBy("email")->get();
        $center_data = array();
        foreach($userArr as $user)
        {
            $center_id = $user->table_id;
            $centerArr = ServiceCenter::whereRaw("center_id='$center_id'")->orderBy("center_name")->first();
            $center_name = $centerArr->center_name;
            
            $center['center_name'] = $center_name;
            $center_data[] = array_merge($user->toArray(),$center);
            #print_r($centerArr);die;
        }
        #print_r($center_data);die;
        
        $url = $_SERVER['APP_URL'].'/manage-center-access';
        
            
            return view('manage-center-access')
                    ->with('userArr', $userArr)
                    ->with('center_data',$center_data)
                    ->with('pageArr', $pageArr)
                    ->with('url', $url)
                    ->with('pageMap',$pageMap);
    }


    public function closure_code(Request $request)
    {
        Session::put("page-title","Closure Codes");
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');

        if($request->isMethod('post'))
        #if ($request->has('closure_code'))
        {
            $data = $request->all();
    
            $closure_code = $data['closure_code'];

            $create = ClosureCode::create(['closure_code' => $closure_code,'created_by' => $UserId]);
            if($create)
            {
                Session::flash('message', "Closure Code Created Successfully.");
                Session::flash('alert-class', 'alert-success');
            }else{
                Session::flash('error', "Closure Code Creation Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            }
    
            return redirect()->route('closure-code');
        }
        
        $qr = "SELECT * from closure_codes";
        
        $data =   DB::select($qr); 
        
        $url = $_SERVER['APP_URL'].'/closure-code';
        
        return view('closure-code')
        ->with('url', $url)
                ->with('DataArr', $data); 
    }

    public function edit_closure(Request $request)
    {

        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');

        if($request->isMethod('post')) 
        {
            $closure_code = $request->input('closure_code');
            $se_id = $request->input('se_id'); 
            $status = $request->input('status'); 

            $update = ClosureCode::where('id', $se_id)->update(['closure_code' => $closure_code,'status'=>$status,'updated_by'=>$UserId]);
            if($update)
            {
                Session::flash('message', "Closure Code Updated Successfully.");
                Session::flash('alert-class', 'alert-success');
            }else{

                Session::flash('error', "Closure Code Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            }

            return redirect()->route('closure-code');
        }

        
        $closure_id = base64_decode($request->input('closure_id')); 
        $data_json = ClosureCode::whereRaw("id='$closure_id'")->first();
        $data = json_decode($data_json,true);
        
        $url = $_SERVER['APP_URL'].'/edit-closure';
        //print_r($data); exit;
        return view('edit-closure')->with('data',$data)->with('url', $url);
    
    }

    
    
    
    
    
    
}

