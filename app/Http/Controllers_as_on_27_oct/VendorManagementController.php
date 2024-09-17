<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SparePart;
use App\StateMaster;
use App\PincodeMaster;
use Auth;
use Session;
use DB;
use App\TaggingMaster;
use App\RegionalManagerMaster;
use App\InvPart;
use App\TagPart;
use App\BrandMaster;
use App\ProductCategoryMaster;
use App\ProductMaster;
use App\ModelMaster;
use App\ServiceCenter;

use App\ServiceEngineer;
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Facades\Excel;

class VendorManagementController extends Controller
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
    
    public function search_pen_case(Request $request)
    {
        Session::put("page-title","Invoice View");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        
        $whereUser = "";
        $se_str = "se_id is null";
        
        if($UserType=='ASM' || $UserType=='RSM' )
        {
            $reg_det = RegionalManagerMaster::whereRaw("LogIn_Id='$UserId'")->first();
            $reg_man_id = $reg_det->reg_man_id;
            $whereUser = " and rmap.reg_man_id='$reg_man_id'";
        }
        else if($UserType=='NSM')
        {
            
        }
        else if($UserType=='ServiceEngineer')
        {
            $se =ServiceEngineer::whereRaw("LogIn_Id='$UserId'")->first();
            $se_id = $se->se_id;
            $whereUser = "and tm.se_id='$se_id' and tm.center_id='$Center_Id'";
            $whereSe = " and se_id='$UserId'";
        }
        else if($UserType!='Admin')
        {
            $whereUser = "and tm.center_id='$Center_Id'";
            $whereTag1 = "and tm.center_id='$Center_Id'";
        }
        
        
        if($UserType=='ASM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.dist_id = tm.dist_id and rmap.reg_man_id='$reg_man_id'";
            $on_table_pin = " ON rmap.dist_id = pm.dist_id ";
            
            $sel_center_ar = "SELECT center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc ON rmap.dist_id = sc.dist_id
WHERE rmap.reg_man_id = '$reg_man_id'";
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType=='RSM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.region_id = tm.region_id ";
            $on_table_pin2 = " AND rmap.region_id = st.region_id ";
            
            $sel_center_ar = "SELECT sc.center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc 
INNER JOIN state_master st ON sc.state_id = st.state_id AND rmap.region_id = st.region_id
WHERE rmap.reg_man_id = '$reg_man_id'"; 
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType!='Admin' && $UserType!='NSM')
        {
            $from_table2 = "`tbl_service_centre_pins` sc INNER JOIN  ";
            $on_table_pin = " ON pm.pincode = sc.pincode AND pm.state_id = sc.state_id and sc.center_id='$Center_Id' ";
            $center_qr = " and center_id ='$Center_Id'"; 
        }
        
        $qr1 = "SELECT pm.state_id,state_name,pm.pincode FROM $from_table2 `pincode_master` pm $on_table_pin
INNER JOIN state_master st ON pm.state_id = st.state_id $on_table_pin2
WHERE 1=1 "; 
        $vendor_pin_json           =   DB::select($qr1); 
        
        $state_master = $pin_master = array();
        foreach($vendor_pin_json as $vpin)
        {
            $state_master[$vpin->state_id] = $vpin->state_name;
            $pin_master[$vpin->pincode] = $vpin->pincode;
        }
        ksort($pin_master);
        ksort($state_master);
        //get method request
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        
        $whereTag = "";
        $VendorPincode=Session::get('pincode');
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and tm.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.Pincode = '$pincode'";
        }
        if(!empty($contact_no) && strlen($contact_no)<=6)
        {
            $whereTag .= " and tm.Pincode='$contact_no'";
        }
        if(!empty($contact_no) && strlen($contact_no)>6)
        {
            $whereTag .= " and tm.contact_no='$contact_no'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        
        if(empty($whereTag))
        {
             $tag_data_qr = "select tm.* from $from_table tagging_master tm $on_table where 1=1 and date(tm.created_at)=curdate();";  
        }
        else
        {
            $tag_data_qr = "select tm.* from $from_table tagging_master tm $on_table where 1=1  $whereTag";     
        }
        
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/view-pdf';
        return view('pdf-tag-view')
            ->with('pin_master',$pin_master)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('pincode',$pincode)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('DataArr',$DataArr)
                ->with('se_arr',$se_arr)
                ->with('url', $url)
                ->with('whereTag',$whereTag); 
                
    }
    
    public function view_pen_case(Request $request)
    {
        $TagId = $request->input('TagId'); 
        $whereTag = $request->input('whereTag'); 
        $data_json = TaggingMaster::whereRaw("TagId = '$TagId' ")->first();
        $data = json_decode($data_json,true);
        
        
        $ProductMaster_json = ProductMaster::whereRaw("product_status='1'")->get();
        $ProductMaster = json_decode($ProductMaster_json,true);
        //print_r($data); exit;
        return view('view-pen-case')
                ->with('data',$data)
                ->with('TagId',$TagId)
                ->with('whereTag',$whereTag)
                ->with('ProductMaster',$ProductMaster);
    }
    
    
    public function index(Request $request)
    {
        Session::put("page-title","ASC Reservation List");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        
        $whereUser = "";
        $se_str = "se_id is null";
        
        if($UserType=='ASM' || $UserType=='RSM' )
        {
            $reg_det = RegionalManagerMaster::whereRaw("LogIn_Id='$UserId'")->first();
            $reg_man_id = $reg_det->reg_man_id;
            $whereUser = " and rmap.reg_man_id='$reg_man_id'";
        }
        else if($UserType=='NSM')
        {
            
        }
        else if($UserType=='ServiceEngineer')
        {
            $se =ServiceEngineer::whereRaw("LogIn_Id='$UserId'")->first();
            $se_id = $se->se_id;
            $whereUser = "and tm.se_id='$se_id' and tm.center_id='$Center_Id'";
            $whereSe = " and se_id='$UserId'";
        }
        else if($UserType!='Admin')
        {
            $whereUser = "and tm.center_id='$Center_Id'";
            $whereTag1 = "and tm.center_id='$Center_Id'";
        }
        
        
        
        if($UserType=='ASM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.dist_id = tm.dist_id and rmap.reg_man_id='$reg_man_id'";
            $on_table_pin = " ON rmap.dist_id = pm.dist_id ";
            
            $sel_center_ar = "SELECT center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc ON rmap.dist_id = sc.dist_id
WHERE rmap.reg_man_id = '$reg_man_id'";
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType=='RSM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.region_id = tm.region_id ";
            $on_table_pin2 = " AND rmap.region_id = st.region_id ";
            
            $sel_center_ar = "SELECT sc.center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc 
INNER JOIN state_master st ON sc.state_id = st.state_id AND rmap.region_id = st.region_id
WHERE rmap.reg_man_id = '$reg_man_id'"; 
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType!='Admin' && $UserType!='NSM')
        {
            $from_table2 = "`tbl_service_centre_pins` sc INNER JOIN  ";
            $on_table_pin = " ON pm.pincode = sc.pincode AND pm.state_id = sc.state_id and sc.center_id='$Center_Id' ";
            $center_qr = " and center_id ='$Center_Id'"; 
        }
        
        $qr1 = "SELECT pm.state_id,state_name,pm.pincode FROM $from_table2 `pincode_master` pm $on_table_pin
INNER JOIN state_master st ON pm.state_id = st.state_id $on_table_pin2
WHERE 1=1 "; 
        $vendor_pin_json           =   DB::select($qr1); 
        
        $state_master = $pin_master = array();
        foreach($vendor_pin_json as $vpin)
        {
            $state_master[$vpin->state_id] = $vpin->state_name;
            $pin_master[$vpin->pincode] = $vpin->pincode;
        }
        ksort($pin_master);
        //get method request
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        
        $whereTag = "";
        $VendorPincode=Session::get('pincode');
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and tm.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.Pincode = '$pincode'";
        }
        if(!empty($contact_no) && strlen($contact_no)<=6)
        {
            $whereTag .= " and tm.Pincode='$contact_no'";
        }
        if(!empty($contact_no) && strlen($contact_no)>6)
        {
            $whereTag .= " and tm.contact_no='$contact_no'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        
        
        
        if(empty($whereTag))
        {
            $tag_data_qr = "select tm.* from $from_table tagging_master tm $on_table where job_reject='0' and se_id is null $whereUser  and observation is null and date(tm.created_at)=curdate();"; 
        }
        else
        {
            $tag_data_qr = "select tm.* from $from_table tagging_master tm $on_table where job_reject='0' and se_id is null $whereUser  and observation is null  $whereTag";    
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/vendor-tag-view';
        return view('vendor-tag-view')
            ->with('pin_master',$pin_master)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('pincode',$pincode)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('DataArr',$DataArr)
                
                ->with('url', $url)
                ->with('whereTag',$whereTag); 
                
    }
    
    public function allocate_case(Request $request)
    {
        Session::put("page-title","Allocate SE");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $whereUser = "";
        $se_str = "se_id is null";
        
        if($UserType=='ASM' || $UserType=='RSM' )
        {
            $reg_det = RegionalManagerMaster::whereRaw("LogIn_Id='$UserId'")->first();
            $reg_man_id = $reg_det->reg_man_id;
            $whereUser = " and rmap.reg_man_id='$reg_man_id'";
        }
        else if($UserType=='RSM')
        {
            
        }
        else if($UserType=='ServiceEngineer')
        {
            $se =ServiceEngineer::whereRaw("LogIn_Id='$UserId'")->first();
            $se_id = $se->se_id;
            $whereUser = "and tm.se_id='$se_id' and tm.center_id='$Center_Id'";
            $whereSe = " and se_id='$UserId'";
        }
        else if($UserType!='Admin')
        {
            $whereUser = "and tm.center_id='$Center_Id'";
            $whereTag1 = "and tm.center_id='$Center_Id'";
        }
        
        
        
        if($UserType=='ASM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.dist_id = tm.dist_id and rmap.reg_man_id='$reg_man_id'";
            $on_table_pin = " ON rmap.dist_id = pm.dist_id ";
            
            $sel_center_ar = "SELECT center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc ON rmap.dist_id = sc.dist_id
WHERE rmap.reg_man_id = '$reg_man_id'";
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType=='RSM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.region_id = tm.region_id ";
            $on_table_pin2 = " AND rmap.region_id = st.region_id ";
            
            $sel_center_ar = "SELECT sc.center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc 
INNER JOIN state_master st ON sc.state_id = st.state_id AND rmap.region_id = st.region_id
WHERE rmap.reg_man_id = '$reg_man_id'"; 
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType!='Admin' && $UserType!='NSM')
        {
            $from_table2 = "`tbl_service_centre_pins` sc INNER JOIN  ";
            $on_table_pin = " ON pm.pincode = sc.pincode AND pm.state_id = sc.state_id and sc.center_id='$Center_Id' ";
            $center_qr = " and center_id ='$Center_Id'"; 
        }
        
        $qr1 = "SELECT pm.state_id,state_name,pm.pincode FROM $from_table2 `pincode_master` pm $on_table_pin
INNER JOIN state_master st ON pm.state_id = st.state_id $on_table_pin2
WHERE 1=1 "; 
        $vendor_pin_json           =   DB::select($qr1); 
        
        $qr2 = "SELECT * FROM `tbl_service_engineer` WHERE se_status='1' $center_qr $whereSe";
        $se_arr           =   DB::select($qr2); 
        //print_r($vendor_pin_json); exit;
        
        $state_master = $pin_master = array();
        foreach($vendor_pin_json as $vpin)
        {
            $state_master[$vpin->state_id] = $vpin->state_name;
            $pin_master[$vpin->pincode] = $vpin->pincode;
        }
        ksort($pin_master);
        ksort($state_master);
        //get method request
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        
        $whereTag = "";
        $VendorPincode=Session::get('pincode');
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and tm.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.Pincode = '$pincode'";
        }
        if(!empty($contact_no) && strlen($contact_no)<=6)
        {
            $whereTag .= " and tm.Pincode='$contact_no'";
        }
        if(!empty($contact_no) && strlen($contact_no)>6)
        {
            $whereTag .= " and tm.contact_no='$contact_no'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        
        
        
        if(empty($whereTag))
        {
            $tag_data_qr = "select tm.* from $from_table tagging_master tm $on_table where job_accept='1' and se_id is null $whereUser and case_tag ='0' and tm.observation is null and date(tm.created_at)=curdate();"; 
        }
        else
        {
            $tag_data_qr = "select tm.* from $from_table tagging_master tm $on_table where job_accept='1' and se_id is null $whereUser and case_tag ='0' and tm.observation is null  $whereTag";    
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/allocate-se';
        return view('center-alloc-se')
            ->with('pin_master',$pin_master)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('pincode',$pincode)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('DataArr',$DataArr)
                ->with('se_arr',$se_arr)
                ->with('url', $url)
                ->with('whereTag',$whereTag); 
                
    }
    
    public function allocate_se(Request $request)
    {
        $se_id = $_POST['se_id'];
        $case_arr = $_POST['case'];
        $whereTag = base64_decode($request->input('whereTag')); 
        $UserId = Session::get('UserId');
       
        
        if(empty($case_arr))
        {
            Session::flash('error', "Please Select Case First");
            Session::flash('alert-class', 'alert-danger'); 
           return back();
        }
        
        $allocation_date = date('Y-m-d H:i:s');
        foreach($case_arr as $case_id)    
        {
            TaggingMaster::whereRaw("TagId='$case_id' and se_id is null and case_close is null")
                    ->update(
                    array('se_id'=>$se_id,
                'allocation_date'=>$allocation_date,
                'allocate_by'=>$UserId));
        }
        
        Session::flash('message', "Case Allocate To Service Engineer");
            Session::flash('alert-class', 'alert-success'); 
        
        return redirect("allocate-se?".$whereTag);
    }
    
    public function observation(Request $request)
    {
        Session::put("page-title","Observation Entry");
        $TagId = $request->input('TagId'); 
        $whereTag = $request->input('whereTag'); 
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        
        $whereTag1 =$whereUser = "";
        if($UserType!='Admin')
        {
            $whereUser = "and center_id='$Center_Id'";
        }
        
        if($UserType=='ServiceCenter')
        {
            $whereTag1 = "and center_id='$Center_Id'";
        }
        
        $data_json = TaggingMaster::whereRaw("TagId = '$TagId' $whereTag1 and case_close is null ")->first();
        $data = json_decode($data_json,true);
        
        
        
        
        $qr1 = "SELECT state_id,state_name FROM state_master st order by state_name";
        $state_json           =   DB::select($qr1); 
        
        
        
        
        
        $qr2 = "SELECT brand_id,brand_name  FROM  brand_master where brand_status='1' ";
        $brand_json           =   DB::select($qr2); 
        $brand_id = $data['brand_id'];
        $qr11 = ProductCategoryMaster::whereRaw("brand_id='$brand_id' and category_status='1' ")->get();
        $ProductDetailMaster           =   json_decode($qr11,true);
        
        
        $product_category_id = $data['product_category_id'];
        $ProductMaster_json = ProductMaster::whereRaw("brand_id='$brand_id' and product_category_id='$product_category_id' and product_status='1'")->get();
        $ProductMaster = json_decode($ProductMaster_json,true);
        
        $product_id = $data['product_id'];
        $qr6 = "SELECT model_id,model_name FROM `model_master` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_status='1'";
        $model_json           =   DB::select($qr6);
        
        
        
        $model_id = $data['model_id'];
        //echo "SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_status='1'";exit;
        $part_arr           =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_status='1' ");
        
        
        
        $qr3 = "SELECT field_name,field_code FROM `tbl_symptom` where sypt_status='1' ";
        $symptom_json           =   DB::select($qr3);
        
        $qr4 = "SELECT field_name,sub_field_name FROM `condition_master` 
WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and con_status='1'
order BY field_name,sub_field_name";
        $con_json           =   DB::select($qr4);
        
        $qr5 = "SELECT Acc_Id,acc_name FROM `tbl_accessories` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and acc_status='1'";
        $acc_json           =   DB::select($qr5);
        
        $qr7 = "SELECT asc_code FROM `tbl_service_centre` WHERE sc_status='1' $whereTag1";
        $asc_json           =   DB::select($qr7);
        
        $qr8 = "SELECT warranty_name FROM `tbl_warranty` WHERE warranty_status='1'";
        $warranty_json           =   DB::select($qr8);
        
        $brand_master = $state_master = $sypt_master = $set_con_master = $acc_master = array();
        $state_Name = $data['State'];
        $state_id = "";
        foreach($state_json as $state)
        {
            $state_master[$state->state_id] = $state->state_name;
            if($state->state_name==$state_Name)
            {
                $state_id = $state->state_id;
            }
        }
        
        
        $qr9 = "SELECT pin_id,pincode FROM pincode_master WHERE state_id='$state_id'";
        $pin_json           =   DB::select($qr9);
        $pin_master = array();
        foreach($pin_json as $pin)
        {
            $pin_master[$pin->pin_id] = $pin->pincode;
        }
        $pincode = $data['Pincode'];
        $qr10 = "SELECT pin_id,place FROM pincode_master WHERE pincode='$pincode'";
        $area_json           =   DB::select($qr10);
        
        $area_master = array();
        foreach($area_json as $area)
        {
            $area_master[$area->pin_id] = $area->place;
        }
        
        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }
        
        foreach($symptom_json as $sympt)
        {
            $sypt_master[$sympt->field_code] = $sympt->field_name;
        }
        
        foreach($con_json as $set_con)
        {
            $set_con_master[$set_con->field_name][] = $set_con->sub_field_name;
        }
        
        foreach($acc_json as $acc)
        {
            $acc_master[$acc->Acc_Id] = $acc->acc_name;
        }
        
        
        foreach($model_json as $model)
        {
            $model_master[$model->model_id] = $model->model_name;
        }
        
        foreach($acc_json as $acc)
        {
            $acc_master[$acc->Acc_Id] = $acc->acc_name;
        }
        
        foreach($asc_json as $asc)
        {
            $asc_master[$asc->asc_code] = $asc->asc_code;
        }
        foreach($warranty_json as $warran)
        {
            $warranty_master[$warran->warranty_name] = $warran->warranty_name;
        }
        
        
        
        
        
        $url = $_SERVER['APP_URL'].'/vendor-tag-view';
        //print_r($data); exit;
        return view('observation-entry')
                ->with('data',$data)
                ->with('TagId',$TagId)
                ->with('whereTag',$whereTag)
                ->with('brand_master',$brand_master)
                ->with('ProductDetailMaster',$ProductDetailMaster)
                ->with('model_master',$model_master)
                ->with('state_master',$state_master)
               ->with('sypt_master',$sypt_master)
               ->with('set_con_master',$set_con_master)
               ->with('acc_master',$acc_master)
                ->with('part_arr', $part_arr)
                ->with('url', $url)
                ->with('asc_master',$asc_master)
                ->with('pin_master',$pin_master)
                ->with('area_master',$area_master)
                ->with('warranty_master',$warranty_master)
                ->with('ProductMaster',$ProductMaster);
    }
    
    
    public function save_observation(Request $request)
    {
        $TagId = addslashes($request->input('TagId')); 
        $Customer_Group = addslashes($request->input('Customer_Group'));
        $Customer_Name = addslashes($request->input('Customer_Name'));
        $Contact_No = addslashes($request->input('Contact_No'));
        $Alt_No = addslashes($request->input('Alt_No'));
        $Customer_Address = addslashes($request->input('Customer_Address'));
        $Landmark = addslashes($request->input('Landmark'));
        $call_rcv_frm = addslashes($request->input('call_rcv_frm'));
        $state = addslashes($request->input('state'));
        $Gst_No = addslashes($request->input('Gst_No'));
        $email = addslashes($request->input('email'));
        $pincode = addslashes($request->input('pincode'));
        
        $service_type = addslashes($request->input('service_type'));
        $warranty_type = addslashes($request->input('warranty_type'));
        $warranty_category = addslashes($request->input('warranty_category'));
        $Brand = addslashes($request->input('Brand'));
        $Product_Detail = addslashes($request->input('Product_Detail'));
        $Product = addslashes($request->input('Product'));
        $Model = addslashes($request->input('Model'));
        $Serial_No = addslashes($request->input('Serial_No'));
        $man_ser_no = addslashes($request->input('man_ser_no'));
        $dealer_name = addslashes($request->input('dealer_name'));
        $Bill_Purchase_Date = addslashes($request->input('Bill_Purchase_Date'));
        $warranty_card = addslashes($request->input('warranty_card'));
        $invoice = addslashes($request->input('invoice'));
        $invoice_no = addslashes($request->input('invoice_no'));
        //$asc_code = addslashes($request->input('asc_code'));
        $entry_type = addslashes($request->input('entry_type'));
        $Symptom = addslashes($request->input('Symptom'));
        $add_cmnt = addslashes($request->input('add_cmnt'));
        $accesories_list_arr = $request->input('accesories_list');
        $accesories_list_json = json_encode($accesories_list_arr);
        $accesories_list = addslashes($accesories_list_json);
        $set_conditions_arr = $request->input('set_conditions');
        $set_conditions_json = json_encode($set_conditions_arr);
        $set_conditions = addslashes($set_conditions_json);
        
        $brand_det = BrandMaster::whereRaw("brand_id='$Brand'")->first();
        $brand_name = $brand_det->brand_name;
        
        $product_catedet = ProductCategoryMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail'")->first();
        $category_name = $product_catedet->category_name;
        
        $product_det = ProductMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product'")->first();
        $product_name = $product_det->product_name;
        
        $model_det = ModelMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model'")->first();
        $model_name = $model_det->model_name;
        
        
        
        
        $accesories_list_arr = $request->input('accesories_list');
        $accesories_list_json = json_encode($accesories_list_arr);
        $accesories_list = addslashes($accesories_list_json);
        $set_conditions_arr = $request->input('set_conditions');
        $set_conditions_json = json_encode($set_conditions_arr);
        $set_conditions = addslashes($set_conditions_json);
        
        //for part array
        $SparePart_arr = $request->input('SparePart');
        $part_name_arr_len = count($SparePart_arr['part_name']);
        //print_r($part_name_arr_len); exit;
        
        
         
        
        //$SparePart_json = json_encode($SparePart_arr);
        //$SparePart = addslashes($SparePart_json);
        
        
        
        $ob_date = date('Y-m-d H:i:s');
         $UserId = Session::get('UserId');
        $call_status = addslashes($request->input('call_status'));
        $se_voc = addslashes($request->input('se_voc'));
        $se_remark = addslashes($request->input('se_remark'));
        $observation = addslashes($request->input('observation'));
        //$part_code = addslashes($request->input('part_code'));
        
        $whereTag = base64_decode($request->input('whereTag')); 
        
        
        $taggingArr = array();
        $UserType = Session::get('UserType');
        
        $taggingArr->Customer_Group=$Customer_Group;
        $taggingArr->Customer_Name=$Customer_Name;
        $taggingArr->Contact_No=$Contact_No;
        $taggingArr->Alt_No= $Alt_No;
        $taggingArr->Customer_Address=$Customer_Address;
        $taggingArr->Landmark=$Landmark;
        $taggingArr->call_rcv_frm=$call_rcv_frm;
        $taggingArr->state=$state;
        
        $state_code_arr = StateMaster::whereRaw("state_name='$state'")->first();
        $state_code = $state_code_arr->state_code;
        $region_id = $state_code_arr->region_id;
        
        $brand_det = BrandMaster::whereRaw("brand_id='$Brand'")->first();
        $brand_name = $brand_det->brand_name;
        
        $product_catedet = ProductCategoryMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail'")->first();
        $category_name = $product_catedet->category_name;
        
        $product_det = ProductMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product'")->first();
        $product_name = $product_det->product_name;
        
        $model_det = ModelMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model'")->first();
        $model_name = $model_det->model_name;
        
        
        $dist_details =  PincodeMaster::whereRaw("pincode='$pincode'")->first();
        $dist_id = $dist_details->dist_id;
        
        
        
        $taggingArr['state_code']=$state_code;
        $taggingArr['pincode']=$pincode;
        $taggingArr['region_id']=$region_id;
        $taggingArr['dist_id']=$dist_id;
        $taggingArr['service_type']=$service_type;
        $taggingArr['warranty_type']=$warranty_type;
        $taggingArr['warranty_category']=$warranty_category;
        $taggingArr['brand_id']=$Brand;
        $taggingArr['product_category_id']=$Product_Detail;
        $taggingArr['product_id']=$Product;
        $taggingArr['model_id']=$Model;
        $taggingArr['Brand']=$brand_name;
        $taggingArr['Product_Detail']=$category_name;
        $taggingArr['Product']=$product_name;
        $taggingArr['Model']=$model_name;
        
        $taggingArr['Gst_No']=$Gst_No;
        $taggingArr['email']=$email;
        $taggingArr['Serial_No']=$Serial_No;
        $taggingArr['man_ser_no']=$man_ser_no;
        $taggingArr['dealer_name']=$dealer_name;
        
        
        $taggingArr['Bill_Purchase_Date']=$Bill_Purchase_Date;
        //$taggingArr['asc_code']=$asc_code;
        $taggingArr['warranty_card']=$warranty_card;
        $taggingArr['invoice']=$invoice;

        //$taggingArr['report_fault']=$report_fault;
        //$taggingArr['service_required']=$service_required;
        $taggingArr['Symptom']=$Symptom;
        $taggingArr['add_cmnt']=$add_cmnt;
        //$taggingArr['estmt_charge']=$estmt_charge;
        
        $taggingArr['set_conditions']=$set_conditions;
        $taggingArr['accesories_list']=$accesories_list;
        $taggingArr['invoice_no']=$invoice_no;
        $taggingArr['observation']=$observation;
        $taggingArr['call_status']=$call_status;
        $taggingArr['ob_date']=date('Y-m-d H:i:s');
        
        
        $taggingArr['case_tag']="1"; 
        
        $taggingArr['service_required']=$service_required;
        $taggingArr['Symptom']=$Symptom;
        $taggingArr['add_cmnt']=$add_cmnt;
        //$taggingArr['service_type']=$service_type;
       // $taggingArr['estmt_charge']=$estmt_charge;
        
        $taggingArr['set_conditions']=$set_conditions;
        $taggingArr['accesories_list']=$accesories_list;
        
        /*if(!empty($_FILES['warranty_card_copy']['name']))
        {
            
            $ext= $today_date.substr($_FILES['warranty_card_copy']['name'],strrpos($_FILES['warranty_card_copy']['name'],'.'),strlen($_FILES['warranty_card_copy']['name'])); ;
            Storage::disk('supreme')->put("$TagId/warranty_card_copy$ext", file_get_contents($_FILES['warranty_card_copy']['tmp_name']));
            $taggingArr['warranty_card_copy']="warranty_card_copy$ext";
        }*/
        
        
        $part_status='not required';
         if($observation=='Part Required')
        {
            $taggingArr['part_status']=1;
            $part_status='pending';
        }
        else
        {
            $taggingArr['case_close']=1;
        }
        $taggingArr['ob_by']=$UserId;
        $Center_Id = Auth::user()->table_id;
        if(TaggingMaster::whereRaw("TagId='$TagId' and case_close is null")->update($taggingArr))
            {
            $st = '<table border="1"><tr><th>Spare Part</th><th>Status</th></tr>';
                for($a = 0; $a<$part_name_arr_len; $a++)
                {
                    $TagPart  =   new TagPart();
                    $part_name = $SparePart_arr['part_name'][$a];
                    $part_no = $SparePart_arr['part_no'][$a];
                    $hsn_code = $SparePart_arr['hsn_code'][$a];
                    
                    $st .="<tr>";
                    if($observation=='Out of Warranty' && !empty($part_name) && !empty($part_no) && !empty($hsn_code))
                    {
                        $TagPart->tag_id = $TagId;
                        $TagPart->part_name = $part_name;
                        $TagPart->part_no = $part_no;
                        $TagPart->hsn_code = $hsn_code;
                        $TagPart->part_status = $part_status;
                        $TagPart->created_by=$UserId;
                        $TagPart->created_at=$ob_date;
                        
                        $st .= "<td>$part_name </td>";
                        if($UserType=='ServiceCenter')
                        {
                            $st .= "<td>Pending For Approval </td>";
                            $TagPart->center_id=Auth::user()->table_id;
                        }
                        $TagPart->save();
                    }
                    else if($observation=='Part Required')
                    {
                        $qry1 = "SELECT SUM(stock_qty) stock_qty FROM `tbl_inventory_center` tic where center_id='$Center_Id' and
                        brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'"; 
                        $stock_arr = DB::select($qry1);
                        $stock_qty = $stock_arr[0]->stock_qty;
                        $qry2 = "SELECT allocation_id center_id,part_name,part_no,hsn_code,count(1) cnsptn FROM tbl_inventory_part WHERE   allocation_id='$Center_Id' and
                        brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code' group by allocation_id,Part_Name,Part_No,hsn_code"; 
                        $consumption_arr           =   DB::select($qry2);
                        $userd_stock = $consumption_arr[0]->cnsptn;
                
                        $balance_qty = $stock_qty - $userd_stock;
                        
                        if($balance_qty>0)
                        {
                            $InvPart                    = new InvPart();
                            $InvPart->spare_id          = $spare_id;
                            $InvPart->brand_id = $Brand;
                            $InvPart->product_category_id = $Product_Detail;
                            $InvPart->product_id = $Product;
                            $InvPart->model_id = $Model;
                            $InvPart->allocation_id         = $Center_Id;
                            $InvPart->allocation_type   = 'center';
                            $InvPart->tag_id            = $TagId;
                            $InvPart->part_name         = $part_name;
                            $InvPart->part_no           = $part_no;
                            $InvPart->hsn_code          = $hsn_code;
                            $InvPart->part_status       = 'provided';
                            $InvPart->pending_status    = $pending_status;
                            $InvPart->approval_date     = $ob_date;
                            $InvPart->approve_by        = $UserId;
                            $st .= "<td>$part_name </td>";
                    
                            if($InvPart->save())
                            {
                                $st .= "<td>Available </td>";
                            }
                            
                        }
                        else
                        {
                            $TagPart->tag_id = $TagId;
                            $TagPart->brand_id = $Brand;
                            $TagPart->product_category_id = $Product_Detail;
                            $TagPart->product_id = $Product;
                            $TagPart->model_id = $Model;
                            $TagPart->part_name = $part_name;
                            $TagPart->part_no = $part_no;
                            $TagPart->hsn_code = $hsn_code;
                            $TagPart->part_status = $part_status;
                            $TagPart->created_by=$UserId;
                            $TagPart->created_at=$ob_date;
                            $TagPart->request_to_ho=1;
                            $TagPart->request_to_ho_date=$ob_date;
                            $TagPart->request_to_ho_by=$UserId;
                            $TagPart->stock_status='stock not available';
                            
                            $st .= "<td>$part_name </td>";
                            if($UserType=='ServiceCenter')
                            {
                                $TagPart->center_id=Auth::user()->table_id;
                            }
                            if($UserType=='ServiceEngineer')
                            {
                                $TagPart->center_id=Auth::user()->table_id;
                            }
                            if($TagPart->save())
                            {
                                $st .= "<td>Not Available </td>";
                            }
                        }
                                                
                    }
                    $st .="</tr>";
                }
            
                $TagPart_Exist = TagPart::whereRaw("tag_id = '$TagId'")->first();
                if($TagPart_Exist->spare_id)
                {
                   $st .='<tr><td colspan="2">Job Case Pending due to Part Pending.</td></tr>';
                }
                else
                {
                     $st .='<tr><td colspan="2">Job Case Closed Successfully.</td></tr>';
                    $taggingArr['case_close']=1;
                    $taggingArr['case_close_date']=$approval_date;
                    TaggingMaster::whereRaw("TagId='$TagId' and case_close is null")->update($taggingArr);
                }
                  //exit;
                $st .="</table>";
                Session::flash('message', "Observation Details Updated Successfully.");
                Session::flash('st', "$st");
                Session::flash('alert-class', 'alert-success');
            }
            else
            {
                Session::flash('error', "Observation Details Update Failed. Please Try Again.");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect("vendor-tag-view?TagId=$TagId&".$whereTag);
    }
    
    public function view_ce_alloc(Request $request)
    {
        Session::put("page-title","Center Allocation");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $whereUser = "";
        
        if($UserType=='ASM' || $UserType=='RSM' )
        {
            $reg_det = RegionalManagerMaster::whereRaw("LogIn_Id='$UserId'")->first();
            $reg_man_id = $reg_det->reg_man_id;
            $whereUser = " and rmap.reg_man_id='$reg_man_id'";
        }
        else if($UserType=='RSM')
        {
            
        }
        else if($UserType=='ServiceEngineer')
        {
            $se =ServiceEngineer::whereRaw("LogIn_Id='$UserId'")->first();
            $se_id = $se->se_id;
            $whereUser = "and tm.se_id='$se_id' and tm.center_id='$Center_Id'";
            $whereSe = " and se_id='$UserId'";
        }
        else if($UserType!='Admin')
        {
            $whereUser = "and tm.center_id='$Center_Id'";
            //$whereTag1 = "and tm.center_id='$Center_Id'";
        }
        
        if($UserType=='ASM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.dist_id = tm.dist_id and rmap.reg_man_id='$reg_man_id'";
            $on_table_pin = " ON rmap.dist_id = pm.dist_id ";
            
            $sel_center_ar = "SELECT center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc ON rmap.dist_id = sc.dist_id
WHERE rmap.reg_man_id = '$reg_man_id'";
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType=='RSM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.region_id = tm.region_id ";
            $on_table_pin2 = " AND rmap.region_id = st.region_id ";
            
            $sel_center_ar = "SELECT sc.center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc 
INNER JOIN state_master st ON sc.state_id = st.state_id AND rmap.region_id = st.region_id
WHERE rmap.reg_man_id = '$reg_man_id'"; 
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType!='Admin' && $UserType!='NSM')
        {
            $from_table2 = "`tbl_service_centre_pins` sc INNER JOIN  ";
            $on_table_pin = " ON pm.pincode = sc.pincode AND pm.state_id = sc.state_id and sc.center_id='$Center_Id' ";
            $center_qr = " and center_id ='$Center_Id'"; 
        }
        
        $qr1 = "SELECT pm.state_id,state_name,pm.pincode FROM $from_table2 `pincode_master` pm $on_table_pin
INNER JOIN state_master st ON pm.state_id = st.state_id $on_table_pin2
WHERE 1=1 "; 
        $vendor_pin_json           =   DB::select($qr1); 
        
        $qr2 = "SELECT tsc.center_id,center_name FROM tbl_service_centre  tsc
INNER JOIN users us ON tsc.email_id = us.email
WHERE sc_status='1' $center_qr order by center_name";
        $se_arr           =   DB::select($qr2); 
        //print_r($vendor_pin_json); exit;
        
        $state_master = $pin_master = array();
        foreach($vendor_pin_json as $vpin)
        {
            $state_master[$vpin->state_id] = $vpin->state_name;
            $pin_master[$vpin->pincode] = $vpin->pincode;
        }
        ksort($pin_master);
        ksort($state_master);
        //get method request
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        
        $whereTag = "";
        
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and tm.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.Pincode = '$pincode'";
        }
        if(!empty($contact_no) && strlen($contact_no)<=6)
        {
            $whereTag .= " and tm.Pincode='$contact_no'";
        }
        if(!empty($contact_no) && strlen($contact_no)>6)
        {
            $whereTag .= " and tm.contact_no='$contact_no'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        
        
        if(empty($whereTag))
        {
            $tag_data_qr = "select * from $from_table tagging_master  tm $on_table where tm.center_id is null and tm.case_tag ='0' and tm.observation is null and date(tm.created_at)=curdate();"; 
        }
        else
        {
            $tag_data_qr = "select * from $from_table tagging_master tm $on_table where tm.center_id is null and tm.case_tag ='0' and tm.observation is null  $whereTag";  
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/center-alloc-view';
        return view('ce-alloc-view')
            ->with('pin_master',$pin_master)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('pincode',$pincode)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('DataArr',$DataArr)
                ->with('se_arr',$se_arr)
                ->with('url', $url)
                ->with('whereTag',$whereTag); 
                
    }
    
    public function allocate_ce(Request $request)
    {
        $center_id = $_POST['se_id'];
        $case_arr = $_POST['case'];
        $whereTag = base64_decode($request->input('whereTag')); 
        $UserId = Session::get('UserId');
       
        
        if(empty($case_arr))
        {
            Session::flash('error', "Please Select Case First");
            Session::flash('alert-class', 'alert-danger'); 
           return back();
        }
        
        $allocation_date = date('Y-m-d H:i:s');
        foreach($case_arr as $case_id)    
        {
            TaggingMaster::whereRaw("TagId='$case_id' and center_id is null and case_close is null")
                    ->update(
                    array('center_id'=>$center_id,
                'center_allocation_date'=>$allocation_date,
                'center_allocation_by'=>$UserId));
        }
        
        Session::flash('message', "Case Allocate To Service Center");
            Session::flash('alert-class', 'alert-success'); 
        
        return redirect("center-alloc-view?".$whereTag);
    }
    
    public function view_ce_realloc(Request $request)
    {
        Session::put("page-title","Center Re-Allocation");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $whereUser = "";
        
        if($UserType=='ASM' || $UserType=='RSM' )
        {
            $reg_det = RegionalManagerMaster::whereRaw("LogIn_Id='$UserId'")->first();
            $reg_man_id = $reg_det->reg_man_id;
            $whereUser = " and rmap.reg_man_id='$reg_man_id'";
        }
        else if($UserType=='RSM')
        {
            
        }
        else if($UserType=='ServiceEngineer')
        {
            $se =ServiceEngineer::whereRaw("LogIn_Id='$UserId'")->first();
            $se_id = $se->se_id;
            $whereUser = "and tm.se_id='$se_id' and tm.center_id='$Center_Id'";
            $whereSe = " and se_id='$UserId'";
        }
        else if($UserType!='Admin')
        {
            $whereUser = "and tm.center_id='$Center_Id'";
            //$whereTag1 = "and tm.center_id='$Center_Id'";
        }
        
        if($UserType=='ASM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.dist_id = tm.dist_id and rmap.reg_man_id='$reg_man_id'";
            $on_table_pin = " ON rmap.dist_id = pm.dist_id ";
            
            $sel_center_ar = "SELECT center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc ON rmap.dist_id = sc.dist_id
WHERE rmap.reg_man_id = '$reg_man_id'";
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType=='RSM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.region_id = tm.region_id ";
            $on_table_pin2 = " AND rmap.region_id = st.region_id ";
            
            $sel_center_ar = "SELECT sc.center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc 
INNER JOIN state_master st ON sc.state_id = st.state_id AND rmap.region_id = st.region_id
WHERE rmap.reg_man_id = '$reg_man_id'"; 
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType!='Admin' && $UserType!='NSM')
        {
            $from_table2 = "`tbl_service_centre_pins` sc INNER JOIN  ";
            $on_table_pin = " ON pm.pincode = sc.pincode AND pm.state_id = sc.state_id and sc.center_id='$Center_Id' ";
            $center_qr = " and center_id ='$Center_Id'"; 
        }
        
        $qr1 = "SELECT pm.state_id,state_name,pm.pincode FROM $from_table2 `pincode_master` pm $on_table_pin
INNER JOIN state_master st ON pm.state_id = st.state_id $on_table_pin2
WHERE 1=1 "; 
        $vendor_pin_json           =   DB::select($qr1); 
        
        $qr2 = "SELECT tsc.center_id,center_name FROM tbl_service_centre  tsc
INNER JOIN users us ON tsc.email_id = us.email
WHERE sc_status='1' $center_qr order by center_name";
        $se_arr           =   DB::select($qr2); 
        //print_r($vendor_pin_json); exit;
        
        $state_master = $pin_master = array();
        foreach($vendor_pin_json as $vpin)
        {
            $state_master[$vpin->state_id] = $vpin->state_name;
            $pin_master[$vpin->pincode] = $vpin->pincode;
        }
        ksort($pin_master);
        ksort($state_master);
        //get method request
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        
        $whereTag = "";
        
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and tm.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.Pincode = '$pincode'";
        }
        if(!empty($contact_no) && strlen($contact_no)<=6)
        {
            $whereTag .= " and tm.Pincode='$contact_no'";
        }
        if(!empty($contact_no) && strlen($contact_no)>6)
        {
            $whereTag .= " and tm.contact_no='$contact_no'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        
        
        if(empty($whereTag))
        {
            $tag_data_qr = "select tm.*,center_name from $from_table tagging_master  tm $on_table inner join tbl_service_centre tsc on tm.center_id=tsc.center_id where tm.center_id is not null and tm.observation is null and if(job_reject='1',date(tm.job_reject_date)=curdate(),date(tm.created_at)=curdate());"; 
        }
        else
        {
            $tag_data_qr = "select tm.*,center_name from $from_table tagging_master tm $on_table inner join tbl_service_centre tsc on tm.center_id=tsc.center_id where tm.center_id is not null  and tm.observation is null  $whereTag";  
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/center-realloc-view';
        return view('ce-realloc-view')
            ->with('pin_master',$pin_master)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('pincode',$pincode)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('DataArr',$DataArr)
                ->with('se_arr',$se_arr)
                ->with('url', $url)
                ->with('whereTag',$whereTag); 
                
    }
    
    public function reallocate_ce(Request $request)
    {
        $center_id = $_POST['se_id']; 
        $case_arr = $_POST['case'];
        //print_r($case_arr);exit;
        $whereTag = base64_decode($request->input('whereTag')); 
        $UserId = Session::get('UserId');
        $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
        $center_name = $center_det->center_name;
        $asc_code = $center_det->asc_code;
        if(empty($case_arr))
        {
            Session::flash('error', "Please Select Case First");
            Session::flash('alert-class', 'alert-danger'); 
           return back();
        }
        
        $allocation_date = date('Y-m-d H:i:s');
        foreach($case_arr as $case_id)    
        {
            TaggingMaster::whereRaw("TagId='$case_id' and center_id is not null and case_close is null")
                    ->update(
                    array('center_id'=>$center_id,
                        'job_accept'=>'0',
                        'job_accept_by'=>null,
                        'asc_code'=>$asc_code,
                        'job_accept_date'=>null,
                        'job_reject_by'=>null,
                        'job_reject_date'=>null,
                        'job_reject'=>'0',
                'center_allocation_date'=>$allocation_date,
                'center_allocation_by'=>$UserId));
        }
        
        Session::flash('message', "Case Re-Allocate To Service Center $center_name");
            Session::flash('alert-class', 'alert-success'); 
        
        return redirect("center-realloc-view?".$whereTag);
    }
    
    public function view_allocate_case(Request $request)
    {
        Session::put("page-title","Case Re-Allocate");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $whereUser = "";
        $se_str = "se_id is null";
        
        if($UserType=='ASM' || $UserType=='RSM' )
        {
            $reg_det = RegionalManagerMaster::whereRaw("LogIn_Id='$UserId'")->first();
            $reg_man_id = $reg_det->reg_man_id;
            $whereUser = " and rmap.reg_man_id='$reg_man_id'";
        }
        else if($UserType=='RSM')
        {
            
        }
        else if($UserType=='ServiceEngineer')
        {
            $se =ServiceEngineer::whereRaw("LogIn_Id='$UserId'")->first();
            $se_id = $se->se_id;
            $whereUser = "and tm.se_id='$se_id' and tm.center_id='$Center_Id'";
            $whereSe = " and se_id='$UserId'";
        }
        else if($UserType!='Admin')
        {
            $whereUser = "and tm.center_id='$Center_Id'";
            $whereTag1 = "and tm.center_id='$Center_Id'";
        }
        
        
        
        if($UserType=='ASM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.dist_id = tm.dist_id and rmap.reg_man_id='$reg_man_id'";
            $on_table_pin = " ON rmap.dist_id = pm.dist_id ";
            
            $sel_center_ar = "SELECT center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc ON rmap.dist_id = sc.dist_id
WHERE rmap.reg_man_id = '$reg_man_id'";
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType=='RSM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.region_id = tm.region_id ";
            $on_table_pin2 = " AND rmap.region_id = st.region_id ";
            
            $sel_center_ar = "SELECT sc.center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc 
INNER JOIN state_master st ON sc.state_id = st.state_id AND rmap.region_id = st.region_id
WHERE rmap.reg_man_id = '$reg_man_id'"; 
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType!='Admin' && $UserType!='NSM')
        {
            $from_table2 = "`tbl_service_centre_pins` sc INNER JOIN  ";
            $on_table_pin = " ON pm.pincode = sc.pincode AND pm.state_id = sc.state_id and sc.center_id='$Center_Id' ";
            $center_qr = " and center_id ='$Center_Id'"; 
        }
        
        $qr1 = "SELECT pm.state_id,state_name,pm.pincode FROM $from_table2 `pincode_master` pm $on_table_pin
INNER JOIN state_master st ON pm.state_id = st.state_id $on_table_pin2
WHERE 1=1 "; 
        $vendor_pin_json           =   DB::select($qr1); 
        
        $qr2 = "SELECT * FROM `tbl_service_engineer` WHERE se_status='1' $center_qr $whereSe";
        $se_arr           =   DB::select($qr2); 
        //print_r($vendor_pin_json); exit;
        
        $state_master = $pin_master = array();
        foreach($vendor_pin_json as $vpin)
        {
            $state_master[$vpin->state_id] = $vpin->state_name;
            $pin_master[$vpin->pincode] = $vpin->pincode;
        }
        ksort($pin_master);
        ksort($state_master);
        //get method request
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        
        $whereTag = "";
        $VendorPincode=Session::get('pincode');
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and tm.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.Pincode = '$pincode'";
        }
        if(!empty($contact_no) && strlen($contact_no)<=6)
        {
            $whereTag .= " and tm.Pincode='$contact_no'";
        }
        if(!empty($contact_no) && strlen($contact_no)>6)
        {
            $whereTag .= " and tm.contact_no='$contact_no'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        
        
        if(empty($whereTag))
        {
            $tag_data_qr = "select * from $from_table tagging_master tm $on_table inner join tbl_service_engineer se on tm.se_id = se.se_id where job_accept='1' and case_tag ='0' $whereUser and tm.se_id is not null  and date(tm.created_at)=curdate();"; 
        }
        else
        {
            $tag_data_qr = "select * from $from_table tagging_master tm $on_table inner join tbl_service_engineer se on tm.se_id = se.se_id where job_accept='1' and case_tag ='0' $whereUser and tm.se_id is not null  $whereTag";  
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/view-se-allocate'; 
        return view('vendor-tag-re')
            ->with('pin_master',$pin_master)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('pincode',$pincode)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('DataArr',$DataArr)
                ->with('se_arr',$se_arr)
                ->with('url', $url)
                ->with('whereTag',$whereTag); 
                
    }
    
    public function reallocate_se(Request $request)
    {
         $se_id = $_POST['se_id'];
        $case_arr = $_POST['case'];
        $whereTag = base64_decode($request->input('whereTag')); 
        $UserId = Session::get('UserId');
       
        
        if(empty($case_arr))
        {
            Session::flash('error', "Please Select Case First");
            Session::flash('alert-class', 'alert-danger'); 
           return back();
        }
        $allocation_date = date('Y-m-d H:i:s');
        foreach($case_arr as $case_id)    
        {
            TaggingMaster::whereRaw("TagId='$case_id' and se_id is not null and case_close is null")->update(array('se_id'=>$se_id,'re_allocation_date'=>$allocation_date,'re_allocate_by'=>$UserId));
        }
        
        Session::flash('message', "Case Re-Allocate To Service Engineer");
            Session::flash('alert-class', 'alert-success'); 
        
        return redirect("view-se-allocate?".$whereTag);
                
    }
    
    public function add_part(Request $request)
    {  
        $random_no = rand(10,1000);
        $brand_id   = addslashes($request->input('brand_id'));
        $product_category_id   = addslashes($request->input('product_category_id'));
        $product_id   = addslashes($request->input('product_id'));
        $model_id   = addslashes($request->input('model_id'));
        $part_arr           =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id'  and  part_status='1' ");
    
    
    ?>
<div class="form-row" id="part_div<?php echo $random_no;?>">
                                                        <div class="col-md-3">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Spare Part </label>
                                                                <select id="part_name<?php echo $random_no;?>" name="SparePart[part_name][]" class="form-control" onchange="get_partno('<?php echo $random_no;?>',this.value)" >
                                                                    <option value="">Select</option>
                                                                    <?php
                                                                            foreach($part_arr as $part)
                                                                            {
                                                                                ?>       <option value="<?php echo $part->part_name; ?>"><?php echo $part->part_name; ?></option>     
                                                                    <?php   }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Part No. </label>
                                                                <select id="part_no<?php echo $random_no;?>" name="SparePart[part_no][]" class="form-control" onchange="get_hsn_code('<?php echo $random_no;?>',this.value)" >
                                                                    <option value="">Select</option>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">HSN Code </label>
                                                                <select id="hsn_code<?php echo $random_no;?>" name="SparePart[hsn_code][]" class="form-control" >
                                                                    <option value="">Select</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="position-relative form-group"><br/><br/>
                                                                <span class="fa fa-plus"  onclick="add_part();"></span>
                                                                <span class="fa fa-minus"  onclick="del_part('part_div<?php echo $random_no;?>');"></span>
                                                            </div>
                                                        </div>
                                                
                                            </div>
    <?php exit; }
    
    public function view_audit(Request $request)
    {
        Session::put("page-title","Audit View");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        
        $whereUser = "";
        $se_str = "se_id is null";
        
        if($UserType=='ASM' || $UserType=='RSM' )
        {
            $reg_det = RegionalManagerMaster::whereRaw("LogIn_Id='$UserId'")->first();
            $reg_man_id = $reg_det->reg_man_id;
            $whereUser = " and rmap.reg_man_id='$reg_man_id'";
        }
        else if($UserType=='NSM')
        {
            
        }
        else if($UserType=='ServiceEngineer')
        {
            $se =ServiceEngineer::whereRaw("LogIn_Id='$UserId'")->first();
            $se_id = $se->se_id;
            $whereUser = "and tm.se_id='$se_id' and tm.center_id='$Center_Id'";
            $whereSe = " and se_id='$UserId'";
        }
        else if($UserType!='Admin')
        {
            $whereUser = "and tm.center_id='$Center_Id'";
            $whereTag1 = "and tm.center_id='$Center_Id'";
        }
        
        
        
        if($UserType=='ASM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.dist_id = tm.dist_id and rmap.reg_man_id='$reg_man_id'";
            $on_table_pin = " ON rmap.dist_id = pm.dist_id ";
            
            $sel_center_ar = "SELECT center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc ON rmap.dist_id = sc.dist_id
WHERE rmap.reg_man_id = '$reg_man_id'";
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType=='RSM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.region_id = tm.region_id ";
            $on_table_pin2 = " AND rmap.region_id = st.region_id ";
            
            $sel_center_ar = "SELECT sc.center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc 
INNER JOIN state_master st ON sc.state_id = st.state_id AND rmap.region_id = st.region_id
WHERE rmap.reg_man_id = '$reg_man_id'"; 
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType!='Admin' && $UserType!='NSM')
        {
            $from_table2 = "`tbl_service_centre_pins` sc INNER JOIN  ";
            $on_table_pin = " ON pm.pincode = sc.pincode AND pm.state_id = sc.state_id and sc.center_id='$Center_Id' ";
            $center_qr = " and center_id ='$Center_Id'"; 
        }
        
        $qr1 = "SELECT pm.state_id,state_name,pm.pincode FROM $from_table2 `pincode_master` pm $on_table_pin
INNER JOIN state_master st ON pm.state_id = st.state_id $on_table_pin2
WHERE 1=1 "; 
        $vendor_pin_json           =   DB::select($qr1); 
        
        $state_master = $pin_master = array();
        foreach($vendor_pin_json as $vpin)
        {
            $state_master[$vpin->state_id] = $vpin->state_name;
            $pin_master[$vpin->pincode] = $vpin->pincode;
        }
        ksort($pin_master);
        ksort($state_master);
        //get method request
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        
        $whereTag = "";
        $VendorPincode=Session::get('pincode');
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and tm.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.Pincode = '$pincode'";
        }
        if(!empty($contact_no) && strlen($contact_no)<=6)
        {
            $whereTag .= " and tm.Pincode='$contact_no'";
        }
        if(!empty($contact_no) && strlen($contact_no)>6)
        {
            $whereTag .= " and tm.contact_no='$contact_no'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        
        
        if(empty($whereTag))
        {
            $tag_data_qr = "select * from $from_table tagging_master tm $on_table where  tm.case_tag ='1' and tm.case_close is null  and date(tm.created_at)=curdate();"; 
        }
        else
        {
            $tag_data_qr = "select * from $from_table tagging_master tm $on_table where  tm.case_tag ='1' and tm.case_close is null   $whereTag"; 
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        
        return view('vendor-audit-view')
            ->with('pin_master',$pin_master)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('pincode',$pincode)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('DataArr',$DataArr)
                ->with('se_arr',$se_arr)
                ->with('whereTag',$whereTag); 
                
    }
    
    
   public function view_img(Request $request)     
   {
       return view('img-view');
   }
    
     
    
}

