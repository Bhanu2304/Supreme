<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\ManageAccess;
use App\PageMaster;
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
    
    public function print_tree($parent,$pageArr,$pageMap)
    {
    
   // echo '<li>';
            //print_r($pageMap[$parent]); exit;
    if(!empty($pageMap[$parent]))
    {
        echo "<ol class='user-tree'>";
        foreach($pageMap[$parent] as $page)
        {
            $this->print_tree($page,$pageArr,$pageMap);   
        }
        echo '</ol>';
    }
    else
    {
        //echo '<li>'.$pageArr[$parent]['page_name'].'</li>';
        echo "<li><div class=\"checkbox-primary\"><label><input class=\".checkbox-info\" type=\"checkbox\" name=\"selectAll[]\" id=\"" . $parent . "\"  value=\"" . $parent . "\"> " . $pageArr[$parent]['page_name'] . "</label></div></li>";
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
        $page_master = PageMaster::whereRaw("Active='1' $page_where_user_type")->orderByRaw("id")->get();
        $pageArr = array();
        
        foreach($page_master as $page)
        {
            $pageMap[$page->parent_id][] = $page->id; 
            $pageArr[$page->id] = json_decode($page,true); 
        }
        
        //print_r($pageArr); exit;
        ?>
        
        <ol class="user-tree">
        <?php  
            foreach($pageMap['0'] as $parent)
            {
                echo "<li id='a".$parent."'><div class='checkbox-primary'><label><input type='checkbox' onchange=".'"show_child('."'".$parent."'".')"'."  name='selectAll[]' id='" . $parent . "'  value='" . $parent . "'> " . $pageArr[$parent]['page_name'];
                //echo "<li>".$pageArr[$parent]['page_name'];
                 $this->print_tree($parent,$pageArr,$pageMap);  
                echo '</label></div></li>'; 
            }
        ?>
        </ol> 

        <?php
    }
    
    public function save_access(Request $request)
    {
        $ride = $request->input('rides');
        $user_id = $request->input('user');
        $LoginId = Session::get("UserId");

        $ch = explode(",", $ride);
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
        
        
        $pageArr = DB::select("SELECT * FROM `pages_master` WHERE active='1' ORDER BY parent_id,priority");
        $userTypeArr = DB::select("SELECT * FROM `user_type_master`");
        
        $userTypeMaster = array();
        foreach($userTypeArr as $type)
        {
            $userTypeMaster[$type->page_column_name] = $type->user_type;
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
            }
            
        }
        
        Session::flash('message', "User Type Access has been updated successfully.");
        Session::flash('alert-class', 'alert-success');
        
        return redirect("manage-page-access");
        
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    
    
    
    
    
}

