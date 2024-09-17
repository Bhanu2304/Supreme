<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\UserCreationMaster;
use App\User;
use App\DepartmentMaster;
use App\DesignationMaster;
use App\PageMaster;
use App\ProjectMaster;
use Auth;
use Mail;
use Session;
use Illuminate\Support\Facades\Hash;

class UserCreationController extends Controller{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        Session::put("page-title","User Creation");
        
        $client_id          =   Auth::User()->id;
        
        $project_json       =   ProjectMaster::whereRaw("Client_Id='$client_id' AND Active_Status='1'")->get(); 
        $project_data       =   json_decode($project_json);
        
        $page_json          =   PageMaster::orderByRaw('page_name ASC')->get();
        $page_data          =   json_decode($page_json);
        
        $designation_json   =   DesignationMaster::orderByRaw('name ASC')->get();
        $designation_data   =   json_decode($designation_json);
        
        $department_json    =   DepartmentMaster::orderByRaw('name ASC')->get();
        $department_data    =   json_decode($department_json);
        
        return view('user-creation')
                ->with('ProjectArr',$project_data)
                ->with('PageArr',$page_data)
                ->with('DesignationArr',$designation_data)
                ->with('DepartmentArr',$department_data);  
    }
    
    public function saveUser(Request $request){
        $client_id          =   Auth::User()->id;
        $created_by         =   Auth::User()->email;
        $project_id         =   implode(",", $request->input("project_id"));
        $email              =   $request->input("email");
        $name               =   $request->input("name");
        $mobile_no          =   $request->input("mobile_no");
        $user_rights        =   implode(",", $request->input("user_rights"));
        $designation        =   $request->input("designation");
        $department         =   $request->input("department");
        $status             =   $request->input("status");
        $token              =   str_random(40);
        $imie               =   '';
        $nazwisko           =   '';
        $password           =   '';
        
        $user = new UserCreationMaster();
        $user->client_id=$client_id;
        $user->project_id=$project_id;
        $user->email=$email;
        $user->name=$name;
        $user->mobile_no=$mobile_no;
        $user->user_rights=$user_rights;
        $user->designation=$designation;
        $user->department=$department;
        $user->status=$status;
        $user->created_by=$created_by;
        $user->token=$token;
         
        if($user->save()){
            Mail::send(['name'=>'123'],array(),  function($message) use ($name, $imie, $nazwisko, $email, $password,$token) {
                $message->to($email, $name)->subject('Dialdesk Email Verification');
                $message->setBody('<h2>Hi '.$name.'</h2>  <br/><br/>Please click on the below link to verify your email account <br/><a href="'.url(config('app.url')).'/user-create-password/'.$token.'">Verify Email</a>','text/html');
                $message->from('ispark@teammas.in','Dialdesk');
            });
            
            Session::flash('message', "This user create successfully and link send on user email id to create password.");
        }
        else{
            Session::flash('message', "This user not created please try again.");
        }
        return redirect('user-creation');
    }
    
    public function existUser(Request $request){
        $client_id  =   Auth::User()->id;
        $field      =   $request->input("field");
        $value      =   $request->input("value");
        
        if($field=="email"){
            $data_json  =   User::whereRaw("$field='$value'")->first(); 
            $datacheck  =   json_decode($data_json,true);
            
            if(!empty($datacheck)){
                print_r(json_decode($data_json,true));die;
            }
            else{
                $data_json  =   UserCreationMaster::whereRaw("client_id='$client_id' AND $field='$value'")->first(); 
                print_r(json_decode($data_json,true));die;
            }    
        }
        else{
            $data_json  =   UserCreationMaster::whereRaw("client_id='$client_id' AND $field='$value'")->first(); 
            print_r(json_decode($data_json,true));die;
        }
    }
    
    public function viewUser(){
        $client_id          =   Auth::User()->id;

        $user_json =   UserCreationMaster::selectRaw("*,user_creation_master.id UserId,user_creation_master.name UserName,department_master.name DeptName,designation_master.name DesiName")
                ->join('department_master','department','=','department_master.id')
                ->join('designation_master','designation','=','designation_master.id')
                ->whereRaw('client_id',$client_id)
                ->get(); 
        
        $user_data       =   json_decode($user_json);
        
        $project_json       =   ProjectMaster::whereRaw("Client_Id='$client_id' AND Active_Status='1'")->get(); 
        $project_data       =   json_decode($project_json);
        
        $page_json          =   PageMaster::orderByRaw('page_name ASC')->get();
        $page_data          =   json_decode($page_json);
        
        $designation_json   =   DesignationMaster::orderByRaw('name ASC')->get();
        $designation_data   =   json_decode($designation_json);
        
        $department_json    =   DepartmentMaster::orderByRaw('name ASC')->get();
        $department_data    =   json_decode($department_json);
        
        $user_right_array   =   array();
        $project_array      =   array();
        
        foreach($page_data as $row){
            $user_right_array[$row->id]=$row->page_name;
        }
        
        foreach($project_data as $row){
            $project_array[$row->Project_Id]=$row->Project_Name;
        }
        
        return view('user-view')
                ->with('project_array',$project_array)
                ->with('user_right_array',$user_right_array)
                ->with('UserArr',$user_data)  
                ->with('ProjectArr',$project_data)
                ->with('PageArr',$page_data)
                ->with('DesignationArr',$designation_data)
                ->with('DepartmentArr',$department_data);       
    }
    
    public function editUser(Request $request){
        $client_id          =   Auth::User()->id;
        $user_id            =   $request->input("id");
        $project_id         =   implode(",", $request->input("project_id"));
        $user_rights        =   implode(",", $request->input("user_rights"));
        $designation        =   $request->input("designation");
        $department         =   $request->input("department");
        $status             =   $request->input("status$user_id");
       
        $user = new UserCreationMaster();

        if($user->where('id',$user_id)->where('client_id',$client_id)->update(['project_id'=>$project_id,'user_rights'=>$user_rights,'designation'=>$designation,'department'=>$department,'status'=>$status])){
            $msg = "This user details update successfully.";
        }
        else{
            $msg = "This user details not update please try again.";
        }
        echo $msg;die;
    }
    
    public function exportUser(){
        
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=user-export.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
            
        $client_id          =   Auth::User()->id;
        
        $project_json       =   ProjectMaster::whereRaw("Client_Id='$client_id' AND Active_Status='1'")->get(); 
        $project_data       =   json_decode($project_json);
        
        $page_json          =   PageMaster::orderByRaw('page_name ASC')->get();
        $page_data          =   json_decode($page_json);
        
        $user_right_array   =   array();
        $project_array      =   array();
        
        foreach($page_data as $row){
            $user_right_array[$row->id]=$row->page_name;
        }
        
        foreach($project_data as $row){
            $project_array[$row->Project_Id]=$row->Project_Name;
        }
        
        $user_json =   UserCreationMaster::selectRaw("*,user_creation_master.id UserId,user_creation_master.name UserName,department_master.name DeptName,designation_master.name DesiName")
                ->join('department_master','department','=','department_master.id')
                ->join('designation_master','designation','=','designation_master.id')
                ->whereRaw('client_id',$client_id)
                ->get(); 
        
        $user_data       =   json_decode($user_json);
        ?>
        <table border='1'>
        <tr>
            <th>name</th>
            <th>email</th>
            <th>mobile no</th>
            <th>project name</th>
            <th>user rights</th>
            <th>designation</th>
            <th>department</th>
            <th>status</th>
            <th>created_date</th>
        </tr>
        <?php
        foreach($user_data as $row){
            if($row->status =="1"){$status="Active";} 
            if($row->status =="0"){$status="De-Active";}
            
            $project_id    = explode(",", $row->project_id);
            $user_rights    = explode(",", $row->user_rights);
            ?>
            <tr>
                <td><?php echo $row->UserName?></td>
                <td><?php echo $row->email?></td>
                <td><?php echo $row->mobile_no?></td>
                <td>
                    <?php 
                        foreach($project_id as $val){
                            echo $project_array[$val]."<br/>";
                        }
                    ?>
                </td>
                <td>
                    <?php 
                        foreach($user_rights as $val){
                            echo $user_right_array[$val]."<br/>";
                        }
                    ?>
                </td>
                <td><?php echo $row->DesiName?></td>
                <td><?php echo $row->DeptName?></td>
                <td><?php echo $status?></td>
                <td><?php echo $row->created_at?></td>
            </tr>
            <?php }?>
        </table>
        <?php
       die; 
    }
    
    public function viewUserPassword($token){
        $client_id          =   Auth::User()->id;
        $user_json          =   UserCreationMaster::where('token',$token)->first();
        $user_data          =   json_decode($user_json);
                
        $project_json       =   ProjectMaster::whereRaw("Client_Id='$client_id' AND Active_Status='1'")->get(); 
        $project_data       =   json_decode($project_json);
        
        $page_json          =   PageMaster::orderByRaw('page_name ASC')->get();
        $page_data          =   json_decode($page_json);
        
        $designation_json   =   DesignationMaster::orderByRaw('name ASC')->get();
        $designation_data   =   json_decode($designation_json);
        
        $department_json    =   DepartmentMaster::orderByRaw('name ASC')->get();
        $department_data    =   json_decode($department_json);
        
        $user_right_array   =   array();
        $project_array      =   array();
        
        foreach($page_data as $row){
            $user_right_array[$row->id]=$row->page_name;
        }
        
        foreach($project_data as $row){
            $project_array[$row->Project_Id]=$row->Project_Name;
        }
        
        return view('user-create-password')
                ->with('project_array',$project_array)
                ->with('user_right_array',$user_right_array)
                ->with('UserArr',$user_data)  
                ->with('ProjectArr',$project_data)
                ->with('PageArr',$page_data)
                ->with('DesignationArr',$designation_data)
                ->with('DepartmentArr',$department_data);       
    }
    
    public function saveUserPassword(Request $request){
        $client_id          =   Auth::User()->id;
        $user_id            =   $request->input("id");
        $password2          =   $request->input("password");
        $password           =   Hash::make($password2);
        
        $secret_question    =   $request->input("secret_question");
        $secret_answer      =   $request->input("secret_answer");
        $password_status    =   1;

        $user = new UserCreationMaster();
        
        if($user->where('id',$user_id)->update(['password'=>$password,'password2'=>$password2,'secret_question'=>$secret_question,'secret_answer'=>$secret_answer,'password_status'=>$password_status])){
            
            $data_json          =   UserCreationMaster::whereRaw("id='$user_id'")->first(); 
            $data               =   json_decode($data_json,true);
            
            $userArr            =   new User();
            $userArr->client_user_id=$data['id'];
            $userArr->name=$data['name'];
            $userArr->email=$data['email'];
            $userArr->password=$data['password'];
            $userArr->password2=$data['password2'];
            $userArr->verify_email="Yes";
            $userArr->verified_at=date('Y-m-d H:i:s');
            $userArr->UserType="ClientUser";
            if($userArr->save()){
                $msg = ""; 
            }
            else{
                $msg = ""; 
            }
            
        }
        else{
            $msg = "Password does not create please try again.";
        }
        echo $msg;die;
    }
    
}
