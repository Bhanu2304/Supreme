<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Activity;
use Session;
use DB;
use Auth;
use App\PincodeMaster;

//For User Role & Permission
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    public function authenticated(Request $request, $user)
    {

        // $qr = "flush hosts;";
        // $mpm = DB::select($qr);
        if (!$user->verify_email || $user->verify_email=='No') {
            auth()->logout();
            activity()
            ->withProperties(['login' => $user->name])
            ->log('Logged In'); //Saving Activity
            
            return back()->with('warning', 'You need to confirm your account. We have sent you an activation code, please check your email.');
        }
        if($user->UserActive==='0')
        {
            auth()->logout();
            activity()
            ->withProperties(['login' => $user->name])
            ->log('Logged In'); //Saving Activity
            
            return back()->with('warning', 'Your Account is Deactivated.');
        }
        
        //$lastLoggedActivity = Activity::all()->last();
        //Session::put('Last Access', $lastLoggedActivity->updated_at->format('d M H:i'));
        //Session::put('Last Activity', $lastLoggedActivity->description);
        $UserId = Auth::user()->id;
        Session::put('UserId', Auth::user()->id);
        Session::put('UserType', Auth::user()->UserType);
        Session::put('DisplayName', Auth::user()->name);
        
        $UserType = Auth::user()->UserType; 
        $page_access = array();
        if($UserType==='ServiceEngineer')
        {
            $page_access[0]->access = "28,6,167,231";
            $page_access[0]->parent_access = "1,130,6";
        }
        else if($UserType==='Account')
        {
            $page_access[0]->access = "201,202,256,257";
            $page_access[0]->parent_access = "200";
        }
        else
        {
            $page_access = DB::select("SELECT access,parent_access FROM `manage_access` WHERE UserId='$UserId' limit 1");
        }
        //User Rights 
        
        $page_arr =  "";
        //print_r($page_access); exit;
        foreach($page_access as $pa)
        {
            if(!empty($pa->access) && !empty($pa->parent_access))
            {
                $access = explode(',',$pa->access);
                $parent_access = explode(',',$pa->parent_access);
                $merge_acc = array_unique(array_merge($access,$parent_access));
                $page_arr = implode("','",$merge_acc);
            }
        }
        
        //print_r($page_arr); exit;
        //all pages
        $qr = "SELECT * FROM `pages_master` WHERE id in ('$page_arr') and active='1'";
        //echo $qr; exit;
        $mpm = DB::select($qr);
        
        $main_menu = array();
        $menu_relation = array();
        $page = array();
        
        foreach($mpm as $menu)
        {
            if($menu->parent_id=='0')
            {
                $main_menu[$menu->priority] = $menu->id; //parent main menu
            }
            else
            {
                $menu_relation[$menu->parent_id][$menu->priority] = $menu->id; //sub menu
            }
            $page[$menu->id] = $menu; //page name details
        }
        
        //print_r($page[$menu->id]); exit;
        $html = "";
        ksort($main_menu); //ordered keys
        foreach($main_menu as $parent_menu)
        {
            $html .= '<li class="app-sidebar__heading">'.$page[$parent_menu]->page_name.'</li>';
            $sub_menu = $menu_relation[$parent_menu];
            ksort($sub_menu); 
            
            foreach($sub_menu as $parent_id)
            {
                $html =  $this->make_submenu($parent_id,$html,$menu_relation,$page,0);
            }  
        }
        
        Session::put('menu',$html);
        if(Auth::user()->UserType=='Admin')
        {
            Session::put('PermissionableField', $PermissionAdmin);
        }
        else if(Auth::user()->UserType=='ServiceCenter')
        {
            Session::put('Center_Id', Auth::user()->table_id);
        }
        else if(Auth::user()->UserType=='ServiceEngineer')
        {
            Session::put('Center_Id', Auth::user()->table_id);
        }
        /*else if(Auth::user()->UserType=='Vendor')
        {
            Session::put('PermissionableField', $PermissionServicePartner);
            $data_pincode_json = PincodeMaster::whereRaw("vendor_id = '$UserId'")->get();
            $data_pincode = json_decode($data_pincode_json);
            
            $pin_arr = array();
            foreach($data_pincode as $pin)
            {
                $pin_arr[] = $pin->pincode;
            }
            
            $pincode = implode("','",$pin_arr);
            
            
            
            Session::put('pincode', $pincode); 
        }
        else if(Auth::user()->UserType=='ServiceEngineer')
        {
            Session::put('PermissionableField', $PermissionServiceEngineer);
        }*/
        
        return redirect()->intended($this->redirectPath());
    }
    
    public function make_submenu($parent_id,$html,$menu_relation,$page,$count)
    {
        
//        if($count==4)
//        {
//            return $html; exit;
//        }
        
        if(!is_array($menu_relation[$parent_id]))
        {
            $html .='<li><a href="'.$page[$parent_id]->page_url.'">
                                        <i class="';
            $html .= $page[$parent_id]->page_icon;
            $html .= '"></i>';
            $html .=$page[$parent_id]->page_name;
            
            $html .='</a>
                                </li>';
        }
        else
        {
            $html.='<li>
                                    <a href="#">
                                        <i class="';
            $html.=$page[$parent_id]->page_icon;
            $html.='"></i>';
            
            $html.=$page[$parent_id]->page_name;
            
            $html.='<i class="metismenu-state-icon fa fa-angle-right"></i>
                                    </a>
                                    <ul>';
            $submenu = $menu_relation[$parent_id]; //get all childs
            ksort($submenu);
            //print_r($submenu); exit;
            
            foreach($submenu as $parent_id)
            {
                $html =  $this->make_submenu($parent_id,$html,$menu_relation,$page,$count++);
            }
            
            $html.='</ul>
                                </li>';    
        }
        
        return $html;
        
    }
    
    
    
}
