<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SparePart;
use App\StateMaster;
use App\PincodeMaster;
use Auth;
use Session;
use DB;
use App\JobSheet;
use App\TaggingMaster;
use App\RegionalManagerMaster;
use App\InvPart;
use App\TagPart;
use App\BrandMaster;
use App\ProductCategoryMaster;
use App\ProductMaster;
use App\ModelMaster;
use App\ServiceCenter;
use App\InventoryCenter;
use App\ServiceEngineer;
use Illuminate\Support\Facades\Storage;
use App\TagImage;
use App\TaggingSparePart;
use App\LabourCharge;
use App\ClosureCode;
use App\TagDamagePart;




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
    
    public function get_part_po_no($brand_name,$sr_no,$po_type)
    {
        $brand_ser_name = strtoupper(substr($brand_name, 0, 2));
        $fin_year = date('ym');
        $part_po_no = "";
        $po_tag = '';
        if($po_type == 'Paid')
        {
            $po_tag .= 'PO';

        }else{

            $po_tag .= 'FOC';
        }
        if(empty($sr_no))
        {
            $sr_no = 1;
            $part_po_no = $brand_ser_name."-$fin_year".'0001';
            $no = '1'; 
        }
        else
        {
            $str_no = "0000";
            $no = (int)$sr_no;
            $no = $no+1;
            $len = strlen($str_no);
            $newlen = strlen("$no");
            $new_no = substr_replace($str_no, $no, $len-$newlen,$newlen);
            $part_po_no = $brand_ser_name."-$fin_year".$new_no;
        }
        
        $out_srno_det = TagPart::whereRaw("part_po_no='$part_po_no'")->first();
        //print_r($out_srno_det); exit;
        if(!empty($out_srno_det))
        {
            return $this->get_part_po_no($brand_name,$no,$po_type);
        }
        else
        {
            return array('part_po_no'=>$part_po_no,'sr_no'=>$no);
        }
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
            //$tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='0' and se_id is null $whereUser   and date(tm.created_at)=curdate();";
           $tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_accept='0' and job_reject='0' and se_id is null $whereUser";
        }
        else
        {
            //$tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='0' and se_id is null $whereUser    $whereTag";    
           $tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='0' $whereUser    $whereTag";    
        }

        //echo $tag_data_qr;die;
        
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
                
                ->with('url',$url)
                ->with('whereTag',$whereTag); 
                
    }
    
    public function allocate_case(Request $request)
    {
        Session::put("page-title","Allocate SE");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $whereUser = "";
        //$se_str = "se_id is null";
        
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
        #echo $tag_data_qr;die;
        
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
            TaggingMaster::whereRaw("TagId='$case_id' and se_id is null and case_close is null")->update(array('se_id'=>$se_id,'allocation_date'=>$allocation_date,'allocate_by'=>$UserId));
        }
        
        Session::flash('message', "Case Allocated To Service Engineer");
        Session::flash('alert-class', 'alert-success');
        
        return redirect("allocate-se?".$whereTag);
    }
    
    public function view_complaint(Request $request)
    {
        Session::put("page-title","View Complaint");
        
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
           //$tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='1'  $whereUser  "; 
           $tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_accept='1' and case_close is null  $whereUser  ";
        }
        else
        {
            //$tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='1'  $whereUser    $whereTag"; 
            //$tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='0'  $whereUser    $whereTag";   
            $tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='0' and job_accept='1' and case_close is null   $whereUser    $whereTag";
        }

        //echo $tag_data_qr;die;
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/vendor-view-complaint'; 
        return view('vendor-view-complaint')
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
    
    public function observation(Request $request)
    {
        Session::put("page-title","Observation Entry");
        $TagId = $request->input('TagId'); 
        $approvaltype = $request->input('approvaltype'); 
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
        
       // $data_json = TaggingMaster::whereRaw("TagId = '$TagId' $whereTag1 and case_close is null ")->first();
       $data_json = TaggingMaster::whereRaw("TagId = '$TagId' $whereTag1 ")->first();
        $data = json_decode($data_json,true);
        //print_r($data);exit;
        
        
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
        //echo "SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_status='1' ";die;
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

        $tag_data_qr = "SELECT tsp.*,tm.*,tsp.part_name,tsc.center_name FROM `tagging_spare_part` tsp
        INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
        INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
        WHERE tsp.tag_id='$TagId'   and tsp.estmt_approve='1'  and delete_status='0'";  //exit;
        
        $DataArr = DB::select($tag_data_qr);

        $lab_data_qr = "SELECT tsp.*,tm.*,tsp.symptom_type,tsp.symptom_name,tsc.center_name FROM `tagging_labour_part` tsp
        INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
        INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
        WHERE tsp.tag_id='$TagId'   and tsp.estmt_approve='1'  and delete_status='0'";  //exit;
        
        $DataArr = DB::select($tag_data_qr);
        $DataArr2 = DB::select($lab_data_qr);


        $tag_data_qr1 = "SELECT tsp.*,tm.*,tsp.part_name,tsc.center_name FROM `tagging_spare_part` tsp
            INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
            INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
            WHERE tsp.tag_id='$TagId'   and tsp.estmt_approve='1'  and delete_status='0' and re_estmt ='0'";  //exit;

        $lab_data_qr1 = "SELECT tsp.*,tm.*,tsp.symptom_type,tsp.symptom_name,tsc.center_name FROM `tagging_labour_part` tsp
        INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
        INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
        WHERE tsp.tag_id='$TagId'   and tsp.estmt_approve='1'  and delete_status='0' and re_estmt ='0'";

        $SparePart_arr1 = DB::select($tag_data_qr1);
        $LabPart_arr1 = DB::select($lab_data_qr1);
        
        $tagg_part = TagPart::whereRaw("tag_id='$TagId' and npc_req='1'")->get();
        $tagg_part_npc = TagPart::whereRaw("tag_id='$TagId' and npc_req='2'")->get();
        $labr_part_npc = TaggingSparePart::whereRaw("tag_id='$TagId' and npc_req='2'")->get();
        $tagg_part_apr = TagPart::whereRaw("tag_id='$TagId' and estmt_approve='1'")->get();

        $closure_code = ClosureCode::whereRaw("status='1'")->get();
        //print_r($tagg_part_npc);exit;
        $str_server = str_replace('public', '', $_SERVER['APP_URL']); 
        //echo $str_server;exit;
        $lbr_charge_det = LabourCharge::selectRaw("distinct(symptom_type) symptom_type")->whereRaw("1=1")->get();
        
        $url = $_SERVER['APP_URL'].'/vendor-tag-view';
        $imagedata = TagImage::whereRaw("TagId='$TagId' ")->orderBy('ImagId','desc')->get();
        //print_r($tagg_part); exit;
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
                ->with('str_server', $str_server)
                ->with('pin_master',$pincode_arr)
                ->with('area_master',$area_master)
                ->with('asc_master',$asc_master)
                ->with('lbr_charge_det',$lbr_charge_det)
                ->with('tagg_part',$tagg_part)
                ->with('tagg_part_npc',$tagg_part_npc)
                ->with('labr_part_npc',$labr_part_npc)                
                ->with('tagg_part_apr',$tagg_part_apr)
                ->with('warranty_master',$warranty_master)
                ->with('ProductMaster',$ProductMaster)
                ->with('imagedata',$imagedata)
                ->with('DataArr',$DataArr)
                ->with('DataArr2',$DataArr2)
                ->with('SparePart_arr1',$SparePart_arr1)
                ->with('LabPart_arr1',$LabPart_arr1)
                ->with('closure_code',$closure_code)
                ->with('approvaltype',$approvaltype);
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
        $observation = addslashes($request->input('observation'));
        $ccsc = addslashes($request->input('ccsc'));
        $estmt_charge = addslashes($request->input('estmt_charge'));
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
        $closure_codes = addslashes($request->input('closure_codes'));
        
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

        $taggingArr['report_fault']=$report_fault;
        //$taggingArr['service_required']=$service_required;
        $taggingArr['Symptom']=$Symptom;
        $taggingArr['add_cmnt']=$add_cmnt;
        $taggingArr['estmt_charge']=$estmt_charge;
        
        $taggingArr['set_conditions']=$set_conditions;
        $taggingArr['accesories_list']=$accesories_list;
        $taggingArr['invoice_no']=$invoice_no;
        $taggingArr['observation']=$observation;
        $taggingArr['ccsc']=$ccsc;
        $taggingArr['closure_codes']=$closure_codes; 
        
        
        
        $taggingArr['job_status']=$observation;
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
        //echo $observation; exit;
        if($observation=='Part Pending')
        {
            //$taggingArr['part_status']=1;
            $part_status='pending';
        }
        else if($observation=='Close')
        {
            $taggingArr['case_close']=1;
        }
        $taggingArr['ob_by']=$UserId;
        $Center_Id = Auth::user()->table_id;
        if(TaggingMaster::whereRaw("TagId='$TagId' ")->update($taggingArr))
            {
            
                $file_arr = array('wrrn'=>'warranty_card_copy','prcs'=>'purchase_copy','mdl'=>'model_no_copy','srl'=>'serial_no_copy','smtm1'=>'symptom_photo1','smtm2'=>'symptom_photo2','smtm3'=>'symptom_photo3');
                foreach($file_arr as $inputName=>$file_name)
                {
                    if(!empty($_FILES[$inputName]['name']))
                    {
                        $today_date = date('Y_m_d_h_i_s');
                        $ext= $today_date.substr($_FILES[$inputName]['name'],strrpos($_FILES[$inputName]['name'],'.'),strlen($_FILES[$inputName]['name'])); ;
                        Storage::disk('supreme')->put("$TagId/$file_name"."$ext", file_get_contents($_FILES[$inputName]['tmp_name']));
                        $taggingArr = array();
                        $taggingArr[$file_name]=$file_name."$ext";
                        TaggingMaster::where('tagid',$TagId)->update($taggingArr);
                    }
                }
            
            
            
            
            $st = '<table border="1"><tr><th>Spare Part</th><th>Status</th></tr>';
                /*for($a = 0; $a<$part_name_arr_len; $a++)
                {
                    $TagPart  =   new TagPart();
                    $part_name = $SparePart_arr['part_name'][$a];
                    $spare_id = $SparePart_arr['part_no'][$a];
                    $pending_parts = $SparePart_arr['pending_parts'][$a];
                    //$hsn_code = $SparePart_arr['hsn_code'][$a];
                    //echo "brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model' and part_name='$part_name' and part_no='$part_no'"; exit;
                    $SparePart = SparePart::whereRaw("spare_id ='$spare_id'")->first();
                   //echo $part_no = $SparePart->part_no; exit;
                    
                    $st .="<tr>";
                     if($observation=='Part Pending')
                    {
                        $qry1 = "SELECT SUM(stock_qty) stock_qty FROM `tbl_inventory_center` tic where center_id='$Center_Id' and
                        brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'"; 
                        $stock_arr = DB::select($qry1);
                        $stock_qty = $stock_arr[0]->stock_qty;
                        $qry2 = "SELECT allocation_id center_id,part_name,part_no,hsn_code,count(1) cnsptn FROM tbl_inventory_part WHERE   allocation_id='$Center_Id' and
                        brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code' group by allocation_id,Part_Name,Part_No,hsn_code"; 
                        $consumption_arr           =   DB::select($qry2);
                        $userd_stock = $consumption_arr[0]->cnsptn;
                
                        //$balance_qty = $stock_qty - $userd_stock;
                        
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
                            if(!empty($SparePart))
                            {
                                $TagPart->tag_id = $TagId;
                                $TagPart->brand_id = $Brand;
                                $TagPart->product_category_id = $Product_Detail;
                                $TagPart->product_id = $Product;
                                $TagPart->model_id = $Model;
                                $TagPart->spare_id = $SparePart->spare_id;
                                $TagPart->part_name = $part_name;
                                $TagPart->part_no = $SparePart->part_no;
                                $TagPart->hsn_code = $hsn_code;
                                $TagPart->part_status = $part_status;
                                $TagPart->pending_parts = $pending_parts;
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
                                                
                    }
                    $st .="</tr>";
                }*/
            
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
            
            return redirect("vendor-view-complaint?TagId=$TagId&".$whereTag);
    }


    public function observation_cl(Request $request)
    {
        Session::put("page-title","Clarion Observation Entry");
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
        
       // $data_json = TaggingMaster::whereRaw("TagId = '$TagId' $whereTag1 and case_close is null ")->first();
       $data_json = TaggingMaster::whereRaw("TagId = '$TagId' $whereTag1 ")->first();
        $data = json_decode($data_json,true);
        //print_r($data);exit;
        
        
        $qr1 = "SELECT state_id,state_name FROM state_master st order by state_name";
        $state_json           =   DB::select($qr1); 
        
        
        $qr2 = "SELECT brand_id,brand_name  FROM  brand_master where brand_status='1' ";
        $brand_json           =   DB::select($qr2); 
        $brand_id = $data['brand_id'];
        $qr11 = ProductCategoryMaster::whereRaw("brand_id='$brand_id' and category_status='1' ")->get();
        $ProductDetailMaster           =   json_decode($qr11,true);

        $product_id = $data['product_id'];
        
        $cat_pro_qry = "SELECT product_category_id  FROM  product_master where product_id='$product_id' ";
        $cat_pro_find           =   DB::select($cat_pro_qry); 
        $product_category_id  = $cat_pro_find[0]->product_category_id;


        #$product_category_id = $data['product_category_id'];
        $ProductMaster_json = ProductMaster::whereRaw("brand_id='$brand_id' and product_category_id='$product_category_id' and product_status='1'")->get();
        $ProductMaster = json_decode($ProductMaster_json,true);
        
        
        #$qr6 = "SELECT model_id,model_name FROM `model_master` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_status='1'";
        $qr6 = "SELECT model_id,model_name FROM `model_master` WHERE brand_id='$brand_id'  and product_id='$product_id' and model_status='1'";
        $model_json           =   DB::select($qr6);
        
        
        
        $model_id = $data['model_id'];
        //echo "SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_status='1'";exit;
        #echo "SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_status='1' ";die;
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


        $qr_cl = "SELECT product_id,product_name  FROM  product_master where brand_id='4' and product_status='1' ";
        $clarion_json           =   DB::select($qr_cl); 

        $clarion_product_master = array();
        foreach($clarion_json as $brand)
        {
            $clarion_product_master[$brand->product_id] = $brand->product_name;
        }
        
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


        $qr7 = "SELECT region_id,region_name FROM region_master WHERE region_status='1' order by region_name";
        $reg_json           =   DB::select($qr7);

        $reg_master = array();

        foreach($reg_json as $reg)
        {
            $reg_master[$reg->region_id] = $reg->region_name; 
        }

        $tag_data_qr = "SELECT tsp.*,tm.*,tsp.part_name,tsc.center_name FROM `tagging_spare_part` tsp
        INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
        INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
        WHERE tsp.tag_id='$TagId'   and tsp.estmt_approve='1'  and delete_status='0'";  //exit;
        
        $DataArr = DB::select($tag_data_qr);

        $lab_data_qr = "SELECT tsp.*,tm.*,tsp.symptom_type,tsp.symptom_name,tsc.center_name FROM `tagging_labour_part` tsp
        INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
        INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
        WHERE tsp.tag_id='$TagId'   and tsp.estmt_approve='1'  and delete_status='0'";  //exit;
        
        $DataArr = DB::select($tag_data_qr);
        $DataArr2 = DB::select($lab_data_qr);


        $tag_data_qr1 = "SELECT tsp.*,tm.*,tsp.part_name,tsc.center_name FROM `tagging_spare_part` tsp
            INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
            INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
            WHERE tsp.tag_id='$TagId'   and tsp.estmt_approve='1'  and delete_status='0' and re_estmt ='0'";  //exit;

        $lab_data_qr1 = "SELECT tsp.*,tm.*,tsp.symptom_type,tsp.symptom_name,tsc.center_name FROM `tagging_labour_part` tsp
        INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
        INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
        WHERE tsp.tag_id='$TagId'   and tsp.estmt_approve='1'  and delete_status='0' and re_estmt ='0'";

        $SparePart_arr1 = DB::select($tag_data_qr1);
        $LabPart_arr1 = DB::select($lab_data_qr1);
        
        $tagg_part = TagPart::whereRaw("tag_id='$TagId' and npc_req='1'")->get();
        $tagg_part_npc = TagPart::whereRaw("tag_id='$TagId' and npc_req='2'")->get();
        $labr_part_npc = TaggingSparePart::whereRaw("tag_id='$TagId' and npc_req='2'")->get();
        $tagg_part_apr = TagPart::whereRaw("tag_id='$TagId' and estmt_approve='1'")->get();

        $closure_code = ClosureCode::whereRaw("status='1'")->get();
        //print_r($tagg_part_npc);exit;
        $str_server = str_replace('public', '', $_SERVER['APP_URL']); 
        //echo $str_server;exit;
        $lbr_charge_det = LabourCharge::selectRaw("distinct(symptom_type) symptom_type")->whereRaw("1=1")->get();
        
        $url = $_SERVER['APP_URL'].'/vendor-tag-view';
        $imagedata = TagImage::whereRaw("TagId='$TagId' ")->orderBy('ImagId','desc')->get();
        //print_r($tagg_part); exit;
        return view('observation-entry-cl')
                ->with('data',$data)
                ->with('TagId',$TagId)
                ->with('whereTag',$whereTag)
                ->with('brand_master',$brand_master)
                ->with('ProductDetailMaster',$ProductDetailMaster)
                ->with('model_master',$model_master)
                ->with('state_master',$state_master)
                ->with('brand_id',$brand_id)
                ->with('clarion_product_master',$clarion_product_master)
                ->with('reg_master',$reg_master)
               ->with('sypt_master',$sypt_master)
               ->with('set_con_master',$set_con_master)
               ->with('acc_master',$acc_master)
                ->with('part_arr', $part_arr)
                ->with('url', $url)
                ->with('str_server', $str_server)
                ->with('pin_master',$pin_master)
                ->with('area_master',$area_master)
                ->with('asc_master',$asc_master)
                ->with('lbr_charge_det',$lbr_charge_det)
                ->with('tagg_part',$tagg_part)
                ->with('tagg_part_npc',$tagg_part_npc)
                ->with('labr_part_npc',$labr_part_npc)                
                ->with('tagg_part_apr',$tagg_part_apr)
                ->with('warranty_master',$warranty_master)
                ->with('ProductMaster',$ProductMaster)
                ->with('imagedata',$imagedata)
                ->with('DataArr',$DataArr)
                ->with('DataArr2',$DataArr2)
                ->with('SparePart_arr1',$SparePart_arr1)
                ->with('LabPart_arr1',$LabPart_arr1)
                ->with('closure_code',$closure_code);
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
        
        // $qr2 = "SELECT tsc.center_id,center_name FROM tbl_service_centre  tsc
        // INNER JOIN users us ON tsc.email_id = us.email
        // WHERE sc_status='1' $center_qr order by center_name";

        $qr2 = "SELECT tsc.center_id,center_name,sm.state_name,city,pincode FROM tbl_service_centre tsc 
            INNER JOIN users us ON tsc.email_id = us.email 
            INNER JOIN state_master sm ON tsc.state =  sm.state_id
            WHERE sc_status='1' $center_qr ORDER BY center_name";
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

        #$tagging_json = TaggingMaster::whereRaw("Brand = '$brand_name' $filter_tag")->get();
        $tagging_json = TaggingMaster::select('Landmark')->distinct()->get();
        $location = array();

        foreach($tagging_json as $tagging)
        {
            if(!empty($tagging->Landmark)) 
            {
                $location[$tagging->Landmark] = $tagging->Landmark;
            }
        }
        asort($location);

        //get method request
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        $location1 = $request->input('location');
        
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
        if(!empty($location1))
        {
            $whereTag .= " and tm.Landmark='$location1'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        
        
        if(empty($whereTag))
        {
            $tag_data_qr = "select *,dist_name from $from_table tagging_master  tm $on_table left join district_master dm on tm.dist_id = dm.dist_id where tm.center_id is null and tm.case_tag ='0' and tm.observation is null "; 
        }
        else
        {
            $tag_data_qr = "select *,dist_name from $from_table tagging_master tm $on_table left join district_master dm on tm.dist_id = dm.dist_id where tm.center_id is null and tm.case_tag ='0' and tm.observation is null  $whereTag";  
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
                ->with('location1',$location1)
                ->with('location',$location)
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
        
        Session::flash('message', "Case Allocated To Service Center");
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
        
        // $qr2 = "SELECT tsc.center_id,center_name FROM tbl_service_centre  tsc
        // INNER JOIN users us ON tsc.email_id = us.email
        // WHERE sc_status='1' $center_qr order by center_name";

        $qr2 = "SELECT tsc.center_id,center_name,sm.state_name,city,pincode FROM tbl_service_centre tsc 
        INNER JOIN users us ON tsc.email_id = us.email 
        INNER JOIN state_master sm ON tsc.state =  sm.state_id
        WHERE sc_status='1' $center_qr ORDER BY center_name";
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

        $tagging_json = TaggingMaster::select('Landmark')->distinct()->get();
        $location = array();

        foreach($tagging_json as $tagging)
        {
            if(!empty($tagging->Landmark)) 
            {
                $location[$tagging->Landmark] = $tagging->Landmark;
            }
        }
        asort($location);

        //get method request
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        $location1 = $request->input('location');
        
        $whereTag = "";
        
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and tm.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.Pincode = '$pincode'";
        }
        if(!empty($location1))
        {
            $whereTag .= " and tm.Landmark = '$location1'";
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
           $tag_data_qr = "select tm.*,center_name,dm.dist_name from $from_table tagging_master  tm $on_table inner join tbl_service_centre tsc on tm.center_id=tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where tm.center_id is not null and tm.observation is null and if(job_reject='1',date(tm.job_reject_date)=curdate(),date(tm.created_at)=curdate());";
        }
        else
        {
            $tag_data_qr = "select tm.*,center_name,dm.dist_name from $from_table tagging_master tm $on_table inner join tbl_service_centre tsc on tm.center_id=tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where tm.center_id is not null  and tm.observation is null  $whereTag";  
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
                ->with('location1',$location1)
                ->with('location',$location)
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
                        'job_reject_reason'=>null,
                        'job_reject'=>'0',
                'center_allocation_date'=>$allocation_date,
                'center_allocation_by'=>$UserId));
        }
        
           Session::flash('message', "Case Re-Allocated To Service Center $center_name");
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
        
        $newpart_no = addslashes($request->input('part_no'));
        $new_part_name = addslashes($request->input('part_name'));
        $new_pending_parts = addslashes($request->input('pending_parts'));
        $TagId = addslashes($request->input('TagId'));
        $div_id = addslashes($request->input('div_id'));
        
        $TagPart  =   new TagPart();
        $SparePart = SparePart::whereRaw("spare_id ='$new_part_name'")->first();
        
        /*$new_part_id = '';
        
        if(!empty($SparePart))
        {
            $ob_date = date('Y-m-d H:i:s');
            $UserId = Session::get('UserId');
            $TagPart->tag_id = $TagId;
            $TagPart->brand_id = $brand_id;
            $TagPart->product_category_id = $product_category_id;
            $TagPart->product_id = $product_id;
            $TagPart->model_id = $model_id;
            $TagPart->spare_id = $SparePart->spare_id;
            $TagPart->part_name = $SparePart->part_name;
            $TagPart->part_no = $SparePart->part_no;
            
            $TagPart->part_status = 'pending';
            $TagPart->pending_parts = $new_pending_parts;
            $TagPart->created_by=$UserId;
            $TagPart->created_at=$ob_date;
            $TagPart->request_to_ho=1;
            $TagPart->request_to_ho_date=$ob_date;
            $TagPart->request_to_ho_by=$UserId;
            $TagPart->stock_status='not check';
            
            $data_json = TaggingMaster::whereRaw("TagId = '$TagId'")->first();
            $TagPart->center_id=$data_json->center_id;
            $TagPart->save();
            $new_part_id = $TagPart->id;
        }
        */
        $part_arr =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id'  and  part_status='1' ");
        $lbr_charge_det = LabourCharge::selectRaw("distinct(symptom_type) symptom_type")->whereRaw("1=1")->get();
    
    ?>
<tr id="tr<?php echo $random_no; ?>">
    <td id="rowno"><?php echo $i++;?></td>
    <td>
        
        <select id="spare_id<?php echo $random_no;?>" name="SparePart[<?php echo $random_no;?>][spare_id]" onchange="get_partno('<?php echo $random_no;?>',this.value)" class="form-control" required="">
        <option value="">Select</option>
        <?php
                foreach($part_arr as $part)
                {
                    ?>       <option value="<?php echo $part->spare_id; ?>"><?php echo $part->part_name; ?></option>     
        <?php   }
        foreach($lbr_charge_det as $lcd)
                    {
            ?>        <option value="lc-<?php echo $lcd->symptom_type; ?>"><?php echo $lcd->symptom_type; ?></option>    
            <?php   }
        ?>
    </select>
    </td>
    <td>
        <select id="part_no<?php echo $random_no;?>" name="SparePart[<?php echo $random_no;?>][part_no]" class="form-control"  required="">
            <option value="">Select</option>
        </select>
    </td>
    <td> 
        <input maxlength="5" required="" onKeyPress="return checkNumber(this.value,event);" id="no_pen_part" name="SparePart[<?php echo $random_no;?>][no_pen_part]" class="form-control"  type="text"  value="<?php echo $tpart->pending_parts;?>" >
    </td>
    <td>
        <input id="color<?php echo $random_no;?>" name="SparePart[<?php echo $random_no;?>][color]" class="form-control"  type="text"  value="" >
    </td>
    <td>
        <select id="charge_type<?php echo $random_no;?>" name="SparePart[<?php echo $random_no;?>][charge_type]" class="form-control"  >
            <option value="Chargeable">Chargeable</option>
            <option value="Non Chargeable">Non Chargeable</option>
        </select>
    </td>

    <td>
        
    </td>
    <td>
        
    </td>
    <td>
        
    </td>
    <td>
        <button type="button" class="mt-2 btn btn-danger remove_npc_part" onclick="del_part_temp('<?php echo $random_no; ?>');" >Remove</button>
    </td>
</tr>
<?php exit; ?>
        <!--<div class="form-row" id="part_div<?php echo $random_no;?>">
    
            <div class="col-md-3">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Part Name </label>
                    <select id="part_name<?php echo $random_no;?>" name="SparePart[part_name][]" onchange="get_partno('<?php echo $random_no;?>',this.value)" class="form-control"  >
                        <option value="">Select</option>
                        <?php
                                foreach($part_arr as $part)
                                {
                                    ?>       <option value="<?php echo $part->spare_id; ?>"><?php echo $part->part_name; ?></option>     
                        <?php   }
                        ?>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Part No. </label>
                    <select id="part_no<?php echo $random_no;?>" name="SparePart[part_no][]"  class="form-control"  >
                        <option value="">Select</option>
                        
                    </select>
                </div>
            </div>

            
            

            <div class="col-md-2">
                <div class="position-relative form-group"><label for="examplePassword11" class="">No. of Pending Parts </label>
                    <input maxlength="5" id="pending_parts<?php echo $random_no;?>" class="form-control" onkeypress="return checkNumber(this.value,event)" autocomplete="off" type="text" name="SparePart[pending_parts][]"   placeholder="No. of Pending Parts" >
                </div>
            </div>
                                
                                <div class="col-md-3">
                                    <div class="position-relative form-group"><br/><br/>
                                        <span class="fa fa-plus"  onclick="add_part();"></span>
                                        <span style="display:none;" id="del_id<?php echo $div_id;?>"><?php echo $new_part_id; ?></span>
                                    </div>
                                </div>
                        
        </div>-->
    <?php exit; }
    
    public function del_part_po(Request $request)
    {
        $part_id =  $request->input('part_id');
        if(TagPart::whereRaw("part_id='$part_id' and part_po_no is null")->delete())
        {
            echo '1';
        }
        else
        {
            echo '0';
        }
    }
    
    public function get_tag_parts(Request $request)
    {
        $tag_id =  $request->input('tag_id');
        $tagg_part = TagPart::whereRaw("tag_id='$tag_id' and estmt_approve='0' and delete_status='0'")->get();
        $lab_part = TaggingSparePart::whereRaw("tag_id='$tag_id' and estmt_approve='0' and delete_status='0'")->get();
        
            $tag_details = TaggingMaster::whereRaw("TagId='$tag_id'")->first();
            $brand_id = $tag_details->brand_id;
            $product_category_id = $tag_details->product_category_id;
            $product_id = $tag_details->product_id;
            $model_id = $tag_details->model_id;
            $part_arr           =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_status='1' ");
            $lab_arr           =   DB::select("SELECT DISTINCT(symptom_type) symptom_type FROM `labour_charge_master`  ");
        ?>
     <tr>
        <th>Sr. No.</th>
        <th>Part Name</th>
        <th>Part Number</th>
        <th>Quantity</th>
        <th>Color</th>
        <th>Part Type</th>
        <th>Customer Price Per Unit</th>
        <th>GST %</th>
        <th>Total Amount</th>
        <th>Action</th>
    </tr>  
<?php $i = 1; foreach($tagg_part as $tpart) { ?>
    <tr id="tr<?php echo $tpart->part_id; ?>">
        <td><?php echo $i++;?></td>
        <td>
        <select id="part_name<?php echo $tpart->part_id; ?>"   class="form-control" onchange="get_partno('<?php echo $tpart->part_id; ?>',this.value)" >
            <!-- <option value="">Select</option> -->
         <option value="<?php echo $tpart->spare_id; ?>"><?php echo $tpart->part_name; ?></option>     
            
        </select>
        </td>
        <td>
            <select id="part_no<?php echo $tpart->part_id; ?>"  class="form-control"  >
                <option value="<?php echo $tpart->part_no; ?>"><?php echo $tpart->part_no; ?></option>
            </select>
        </td>
        <td>
            <input maxlength="5" id="no_pen_part<?php echo $tpart->part_id; ?>" class="form-control" readonly="" type="text"  value="<?php echo $tpart->pending_parts;?>" >
        </td>
        <td>
            <input id="color<?php echo $tpart->part_id; ?>" class="form-control" readonly="" type="text"  value="<?php echo $tpart->color;?>" >
        </td>
        <td>
            <select id="charge_type<?php echo $tpart->part_id; ?>"  class="form-control"  >
                <!-- <option value="Chargeable" <?php //if($tpart->charge_type=='Chargeable') { echo 'selected';} ?>>Chargeable</option> -->
                <!-- <option value="Non Chargeable" <?php //if($tpart->charge_type=='Non Chargeable') { echo 'selected';} ?>>Non Chargeable</option> -->
                <option value="<?php echo $tpart->charge_type;?>"><?php echo $tpart->charge_type;?></option>
            </select>
        </td>

        <td>
            
        </td>
        <td>
            <?php echo $tpart->gst;?>
        </td>
        <td>
            <?php echo $tpart->total;?>
        </td>
        <td>
            <button type="button" class="mt-2 btn btn-danger remove_npc_part1" onclick="del_part('<?php echo $tpart->part_id; ?>');" >Remove</button>
        </td>
    </tr>


<?php    } 
foreach($lab_part as $tpart) { $tpart->part_id =$tpart->tlp_id;  ?>
    <tr id="tr<?php echo $tpart->part_id; ?>">
        <td><?php echo $i++;?></td>
        <td>
        <select id="part_name<?php echo $tpart->part_id; ?>"  class="form-control" onchange="get_partno('<?php echo $tpart->part_id; ?>',this.value)" >
            <!-- <option value="">Select</option> -->
            <?php  foreach($lab_arr as $lcd)
            { ?>        
            <option value="<?php echo $lcd->symptom_type; ?>" <?php if($lcd->symptom_type==$tpart->symptom_type) { echo 'selected';} ?>><?php echo $lcd->symptom_type; ?></option>    
            <?php   } ?>

        </select>
        </td>
        <td>
            <select id="part_no<?php echo $tpart->part_id; ?>"  class="form-control"  >
                <option value="<?php echo $tpart->symptom_name; ?>"><?php echo $tpart->symptom_name; ?></option>
            </select>
        </td>
        <td>
            <input maxlength="5" id="no_pen_part<?php echo $tpart->part_id; ?>" class="form-control" readonly="" type="text"  value="<?php echo $tpart->pending_parts;?>" >
        </td>
        <td>
            <input id="color<?php echo $tpart->part_id; ?>" class="form-control" readonly="" type="text"  value="<?php echo $tpart->color;?>" >
        </td>
        <td>
            <select id="charge_type<?php echo $tpart->part_id; ?>"  class="form-control"  >
                <!-- <option value="Chargeable" <?php //if($tpart->charge_type=='Chargeable') { echo 'selected';} ?>>Chargeable</option> -->
                <!-- <option value="Non Chargeable" <?php //if($tpart->charge_type=='Non Chargeable') { echo 'selected';} ?>>Non Chargeable</option> -->
                <option value="<?php echo $tpart->charge_type;?>"><?php echo $tpart->charge_type;?></option>
            </select>
        </td>

        <td>
            
        </td>
        <td>
            <?php echo $tpart->gst;?>
        </td>
        <td>
            <?php echo $tpart->total;?>
        </td>
        <td>
            <button type="button" class="mt-2 btn btn-danger remove_npc_part1" onclick="del_part('<?php echo $tpart->part_id; ?>');" >Remove</button>
        </td>
    </tr>


    <?php    } 
      exit;    
    }
    
    public function save_request_npc(Request $request)
    {  
        //print_r($request->all());die;
        $tag_id   = addslashes($request->input('tag_id'));
        $SparePart_arr   = $request->input('SparePart');
        $tag_details = TaggingMaster::whereRaw("TagId='$tag_id'")->first();
        $warranty_type = $tag_details->warranty_type;
        
        $brand_id   = $tag_details->brand_id;
        $product_category_id   = $tag_details->product_category_id;
        $product_id   = $tag_details->product_id;
        $model_id   = $tag_details->model_id;
        
        //print_r($SparePart_arr); exit;
        $part_arr = array();
        $lab_arr = array();
        $ob_date = date('Y-m-d H:i:s');

            foreach($SparePart_arr as $username) {
            $items = addslashes($username['spare_id']);
                if($items=='lc-Labour Fee')
                {
                    $part_type1 = "labour";
                }

            }
            //echo $part_type1;die;
            //print_r($items);die;
    
        foreach($SparePart_arr as $part_det)
        {
            //print_r($part_det);exit;
            $newpart_no = $part_det['part_no'];
            $new_spare_id = addslashes($part_det['spare_id']);
            $part_type = "part";
            $substr = substr($new_spare_id,0,3);
            // $exp = explode("-",$new_spare_id);
            //print_r($substr);exit;
            if($substr=='lc-')
            {
                $part_type = "labour";
                $new_spare_id = $exp[1];
            }
            //echo $new_spare_id;exit;
            $new_pending_parts = addslashes($part_det['no_pen_part']);
            $color = addslashes($part_det['color']);
            $charge_type = addslashes($part_det['charge_type']); 
            
            $UserId = Session::get('UserId');
            $labPart = TaggingSparePart::whereRaw("tag_id ='$tag_id'")->first();

            if($part_type=='part')
            {
                $SparePart = SparePart::whereRaw("spare_id ='$new_spare_id'")->first();
    
                if(!empty($SparePart))
                {   
                    $TagPart  =   array(); 
                    $TagPart['tag_id']= $tag_id;
                    $TagPart['brand_id']= $brand_id;
                    $TagPart['product_category_id']= $product_category_id;
                    $TagPart['product_id']= $product_id;
                    $TagPart['model_id']= $model_id;
                    $TagPart['spare_id']= $SparePart['spare_id'];
                    $TagPart['part_name']= $SparePart['part_name'];
                    $TagPart['part_no']= $SparePart['part_no'];
                    $TagPart['color']= $color;
                    $TagPart['charge_type']= $charge_type;
                    $TagPart['part_status']= 'pending';
                    $TagPart['pending_parts']= $new_pending_parts;
                    $TagPart['created_by']=$UserId;
                    $TagPart['created_at']=$ob_date;
                    /*$TagPart['request_to_ho']=1;
                    $TagPart['request_to_ho_date']=$ob_date;
                    $TagPart['request_to_ho_by']=$UserId;*/
                    $TagPart['stock_status']='send to npc'; 
                    $TagPart['npc_send_by']=$UserId;
                    $TagPart['npc_req']='1';
                    $TagPart['npc_send_date']=$ob_date;
                    $TagPart['center_id']=$tag_details->center_id;

                    // if($warranty_type=='Standard Warranty' && $charge_type=='Non Chargeable')
                    // {
                    //     $TagPart['customer_price']= 0;
                    //     $TagPart['gst']= 0;
                    //     $TagPart['total']= 0;
                    //     $TagPart['npc_req']= '2';
                    //     $TagPart['stock_status']= 'npc esitmation made';
                    //     $TagPart['npc_estimate_by']= $UserId;
                    //     $TagPart['npc_estimate_date']= $ob_date;
                        
                    // }
                    //$SparePart1 = LabourCharge::whereRaw("lab_id='1'")->first();
                    // if(empty($labPart) && $part_type1!= 'labour')
                    // {
                    //     $LabPart  =   array(); 
                    //     $SparePart1 = LabourCharge::whereRaw("lab_id='1'")->first();
                    //     //print_r($SparePart1);die;
                    //     $LabPart['tag_id']= $tag_id;
                    //     $LabPart['brand_id']= $brand_id;
                    //     $LabPart['product_category_id']= $product_category_id;
                    //     $LabPart['product_id']= $product_id;
                    //     $LabPart['model_id']= $model_id;
                    //     $LabPart['lab_id']= 1;
                    //     $LabPart['symptom_type']= $SparePart1['symptom_type'];
                    //     $LabPart['symptom_name']= $SparePart1['symptom_name'];
                    //     $LabPart['color']= $color;
                    //     $LabPart['charge_type']= $charge_type;
                    //     $LabPart['part_status']= 'pending';
                    //     $LabPart['pending_parts']= $new_pending_parts;
                    //     $LabPart['created_by']=$UserId;
                    //     $LabPart['created_at']=$ob_date;
                    //     $LabPart['stock_status']='send to npc'; 
                    //     $LabPart['npc_send_by']=$UserId;
                    //     $LabPart['npc_req']='1';
                    //     $LabPart['npc_send_date']=$ob_date;
                    //     $LabPart['center_id']=$tag_details->center_id;
                    //     if($warranty_type=='Standard Warranty' && $charge_type=='Non Chargeable')
                    //     {
                    //         $LabPart['customer_price']= 0;
                    //         $LabPart['gst']= 0;
                    //         $LabPart['total']= 0;
                    //         $LabPart['npc_req']= '2';
                    //         $LabPart['stock_status']= 'npc esitmation made';
                    //         $LabPart['npc_estimate_by']= $UserId;
                    //         $LabPart['npc_estimate_date']= $ob_date;
                            
                    //     }
                    //     //print_r($LabPart);die;
                    //     $save = TaggingSparePart::insert($LabPart);
                    // }
                    

                    $part_arr[] = $TagPart;
                }
            }
            
            else
            {
                    //echo $part_type;die;
                    $TagPart  =   array(); 
                    //echo $new_spare_id = $newpart_no; exit;
                    //echo $newpart_no;exit;
                   
                    $SparePart = LabourCharge::whereRaw("lab_id='$newpart_no'")->first();
                    $TagPart['tag_id']= $tag_id;
                    $TagPart['brand_id']= $brand_id;
                    $TagPart['product_category_id'] = $product_category_id;
                    $TagPart['product_id']= $product_id;
                    $TagPart['model_id']= $model_id;
                    $TagPart['lab_id']= $newpart_no;
                    $TagPart['symptom_type']= $SparePart['symptom_type'];
                    $TagPart['symptom_name']= $SparePart['symptom_name'];
                    $TagPart['color']= $color;
                    $TagPart['charge_type']= $charge_type;
                    $TagPart['part_status']= 'pending';
                    $TagPart['pending_parts']= $new_pending_parts;
                    $TagPart['created_by']=$UserId;
                    $TagPart['created_at']=$ob_date;
                    /*$TagPart['request_to_ho']=1;
                    $TagPart['request_to_ho_date']=$ob_date;
                    $TagPart['request_to_ho_by']=$UserId;*/
                    $TagPart['stock_status']='send to npc'; 
                    $TagPart['npc_send_by']=$UserId;
                    $TagPart['npc_req']='1';
                    $TagPart['npc_send_date']=$ob_date;
                    $TagPart['center_id']=$tag_details->center_id;
                    // if($warranty_type=='Standard Warranty' && $charge_type=='Non Chargeable')
                    // {
                    //     $TagPart['customer_price']= 0;
                    //     $TagPart['gst']= 0;
                    //     $TagPart['total']= 0;
                    //     $TagPart['npc_req']= '2';
                    //     $TagPart['stock_status']= 'npc esitmation made';
                    //     $TagPart['npc_estimate_by']= $UserId;
                    //     $TagPart['npc_estimate_date']= $ob_date;
                        
                    // }
                    $lab_arr = $TagPart;
                    //print_r($lab_arr);die;
            }
        
            
        }

        $msg = "0";
        if(!empty($part_arr))
        {
            if(TagPart::insert($part_arr))
            {
                $msg = "1";
                
            }
        }
        if(!empty($lab_arr))
        {
            if(TaggingSparePart::insert($lab_arr))
            {
                $msg = "1";
            }
        }
        echo $msg;exit;
        //return response()->json(['msg' => $msg,'charge_type' => $charge_type]);exit;
        //echo $charge_type;exit;
        
     
     
    }
    
    public function reestimate_request_npc(Request $request)
    {  
        //print_r($request->all());die;
        $tag_id   = addslashes($request->input('tag_id'));
        $UserId = Session::get('UserId');
         //$SparePart   = $request->input('SparePart');
        $tag_data_qr = "SELECT tsp.*,tm.*,tsp.part_name,tsc.center_name FROM `tagging_spare_part` tsp
            INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
            INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
            WHERE tsp.tag_id='$tag_id'   and tsp.estmt_approve='1'  and delete_status='0' and re_estmt ='0'";  //exit;

        $lab_data_qr = "SELECT tsp.*,tm.*,tsp.symptom_type,tsp.symptom_name,tsc.center_name FROM `tagging_labour_part` tsp
        INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
        INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
        WHERE tsp.tag_id='$tag_id'   and tsp.estmt_approve='1'  and delete_status='0' and re_estmt ='0'";

        $SparePart_arr = DB::select($tag_data_qr);
        $LabPart_arr = DB::select($lab_data_qr);
        //print_r($LabPart_arr);die;
        $tag_details = TaggingMaster::whereRaw("TagId='$tag_id'")->first();
        $warranty_type = $tag_details->warranty_type;
        
        $brand_id   = $tag_details->brand_id;
        $product_category_id   = $tag_details->product_category_id;
        $product_id   = $tag_details->product_id;
        $model_id   = $tag_details->model_id;
        
        //print_r($SparePart_arr); exit;
        $part_arr = array();
        $lab_arr = array();
        $ob_date = date('Y-m-d H:i:s');

      
    if(!empty($SparePart_arr))
    {
        foreach($SparePart_arr as $part_det)
        {
            //print_r($part_det);exit;
            $newpart_no = $part_det->part_no;
            $new_spare_id = addslashes($part_det->spare_id);
            $part_type = "part";
            $substr = substr($new_spare_id,0,3);
            // $exp = explode("-",$new_spare_id);
            //print_r($substr);exit;
            // if($substr=='lc-')
            // {
            //     $part_type = "labour";
            //     $new_spare_id = $exp[1];
            // }
            //echo $new_spare_id;exit;
            $new_pending_parts = addslashes($part_det->pending_parts);
            $color = addslashes($part_det->color);
            $charge_type = addslashes($part_det->charge_type); 
            
            $labPart = TaggingSparePart::whereRaw("tag_id ='$tag_id'")->first();

            if($part_type=='part')
            {
                $SparePart = SparePart::whereRaw("spare_id ='$new_spare_id'")->first();
    
                if(!empty($SparePart))
                {   
                    $TagPart  =   array(); 
                    $TagPart['tag_id']= $tag_id;
                    $TagPart['brand_id']= $brand_id;
                    $TagPart['product_category_id']= $product_category_id;
                    $TagPart['product_id']= $product_id;
                    $TagPart['model_id']= $model_id;
                    $TagPart['spare_id']= $SparePart['spare_id'];
                    $TagPart['part_name']= $SparePart['part_name'];
                    $TagPart['part_no']= $SparePart['part_no'];
                    $TagPart['color']= $color;
                    $TagPart['charge_type']= $charge_type;
                    $TagPart['part_status']= 'pending';
                    $TagPart['pending_parts']= $new_pending_parts;
                    $TagPart['created_by']=$UserId;
                    $TagPart['created_at']=$ob_date;
                    /*$TagPart['request_to_ho']=1;
                    $TagPart['request_to_ho_date']=$ob_date;
                    $TagPart['request_to_ho_by']=$UserId;*/
                    $TagPart['stock_status']='send to npc'; 
                    $TagPart['npc_send_by']=$UserId;
                    $TagPart['npc_req']='1';
                    $TagPart['npc_send_date']=$ob_date;
                    $TagPart['center_id']=$tag_details->center_id;
                    $TagPart['re_estmt'] = '1';
                    

                    $part_arr[] = $TagPart;
                }
            }
            
        }
    }
    if(!empty($LabPart_arr))
    {
        foreach($LabPart_arr as $part_det)
        {
            $newpart_no = $part_det->lab_id;
            $color = addslashes($part_det->color);
            $charge_type = addslashes($part_det->charge_type); 
            $new_pending_parts = addslashes($part_det->pending_parts);
            //echo $part_type;die;
            $TagPart  =   array(); 
            //echo $new_spare_id = $newpart_no; exit;
            //echo $newpart_no;exit;
            
            $SparePart = LabourCharge::whereRaw("lab_id='$newpart_no'")->first();
            $TagPart['tag_id']= $tag_id;
            $TagPart['brand_id']= $brand_id;
            $TagPart['product_category_id'] = $product_category_id;
            $TagPart['product_id']= $product_id;
            $TagPart['model_id']= $model_id;
            $TagPart['lab_id']= $newpart_no;
            $TagPart['symptom_type']= $SparePart['symptom_type'];
            $TagPart['symptom_name']= $SparePart['symptom_name'];
            $TagPart['color']= $color;
            $TagPart['charge_type']= $charge_type;
            $TagPart['part_status']= 'pending';
            $TagPart['pending_parts']= $new_pending_parts;
            $TagPart['created_by']=$UserId;
            $TagPart['created_at']=$ob_date;
            /*$TagPart['request_to_ho']=1;
            $TagPart['request_to_ho_date']=$ob_date;
            $TagPart['request_to_ho_by']=$UserId;*/
            $TagPart['stock_status']='send to npc'; 
            $TagPart['npc_send_by']=$UserId;
            $TagPart['npc_req']='1';
            $TagPart['npc_send_date']=$ob_date;
            $TagPart['center_id']=$tag_details->center_id;
            $TagPart['re_estmt'] = '1';
            
            $lab_arr = $TagPart;
            //print_r($lab_arr);die;
    
        }
    }

        $msg = "0";
        if(!empty($part_arr))
        {
            if(TagPart::insert($part_arr))
            {
                $msg = "1";
                
            }
        }
        if(!empty($lab_arr))
        {
            if(TaggingSparePart::insert($lab_arr))
            {
                $msg = "1";
            }
        }
        echo $msg;exit;
        //return response()->json(['msg' => $msg,'charge_type' => $charge_type]);exit;
        //echo $charge_type;exit;
     
    }

    public function estmt_approve(Request $request)
    {  
        $tag_id   = addslashes($request->input('tag_id'));
        $SparePart_arr   = $request->input('SparePart');
        $LabPart_arr   = $request->input('LabPart');
        $npc_cancel_remarks = $request->input('npc_cancel_remarks');
        $submit = $request->input('submit');        
        $npc_not_approve_remarks = $request->input('npc_not_approve_remarks');
        
        
         $flag = true;
        if($submit=='cancel' && $npc_cancel_remarks=='')
        {
            $msg= "Please Fill Cancel Remarks";
            $flag = false;
           return back();
        }
        else if($submit=='not approve' && $npc_not_approve_remarks=='')
        {
            $msg= "Please Fill Not Approve Remarks";
            $flag = false;
           return back();
        }
        
        
        $part_arr = array();
        $ob_date = date('Y-m-d H:i:s');
        
        
        foreach($SparePart_arr as $partId=>$part_det)
        {
            $tag_part = array();
            $po_type = '';
            $tag_det = TagPart::whereRaw("part_id='$partId'")->first();
            if($submit=='cancel')
            {
                $tag_part['estmt_approve'] = '0';
                $tag_part['estmt_cancel_remarks'] = $npc_cancel_remarks;
                $tag_part['delete_status'] = '1';
            }
            else if($submit=='not approve')
            {
                $tag_part['estmt_approve'] = '0';
                $tag_part['estmt_not_approve_remarks'] = $npc_not_approve_remarks;
                $tag_part['delete_status'] = '2';
            }
            else
            {
                $tag_part['estmt_approve'] = '1';
                if($tag_det->charge_type=='Non Chargeable')
                {
                    $po_type = $tag_part['po_type'] = 'FOC';
                }
                else
                {
                  $po_type =   $tag_part['po_type'] = 'Paid';
                }

                $tag_part['pending_status'] = '1';
                $tag_part['part_status'] = 'pending';
                $tag_part['estmt_date'] = $ob_date;
                $TagId = $tag_det->tag_id;
                $part_pen = $tag_det->pending_parts;
                if(TaggingMaster::whereRaw("TagId='$TagId'")->update(array('part_status'=>'1','part_pending'=>$part_pen)))
                {
                    $this->raise_po($partId,$po_type,$tag_det->color,$tag_det->pending_parts,$tag_det);
                }
                
            }
            $tag_part['npc_req'] = '3';
            
            if(TagPart::whereRaw("part_id='$partId'")->update($tag_part))
            {
                
            }
            else
            {
                $flag = false;
            }
        }
        
        foreach($LabPart_arr as $partId=>$part_det)
        {
            $tag_part = array();
            $po_type = '';
            $tag_det = TaggingSparePart::whereRaw("tlp_id='$partId'")->first();
            if($submit=='cancel')
            {
                $tag_part['estmt_approve'] = '0';
                $tag_part['estmt_cancel_remarks'] = $npc_cancel_remarks;
                $tag_part['delete_status'] = '1';
            }
            else if($submit=='not approve')
            {
                $tag_part['estmt_approve'] = '0';
                $tag_part['estmt_not_approve_remarks'] = $npc_not_approve_remarks;
                $tag_part['delete_status'] = '2';
            }
            else
            {
                $tag_part['estmt_approve'] = '1';
                if($tag_det->charge_type=='Non Chargeable')
                {
                    $po_type = $tag_part['po_type'] = 'FOC';
                }
                else
                {
                  $po_type =   $tag_part['po_type'] = 'Paid';
                }
                
                $tag_part['pending_status'] = '1';
                $tag_part['part_status'] = 'pending';
                $tag_part['estmt_date'] = $ob_date;
                $TagId = $tag_det->tag_id;
                $part_pen = $tag_det->pending_parts;
                if(TaggingMaster::whereRaw("TagId='$TagId'")->update(array('part_status'=>'1','part_pending'=>$part_pen)))
                {
                    //$this->raise_po($partId,$po_type,$tag_det->color,$tag_det->pending_parts,$tag_det);
                }
                
                
            }
            $tag_part['npc_req'] = '3';
            
            if(TaggingSparePart::whereRaw("tlp_id='$partId'")->update($tag_part))
            {
                
            }
            else
            {
                $flag = false;
            }
        }
        //exit;
        if($flag)
        {
            $tagg_part_npc = TagPart::whereRaw("tag_id='$TagId' and npc_req='3' and delete_status='0'")->get();
            $tagg_lbr_npc = TaggingSparePart::whereRaw("tag_id='$TagId' and npc_req='3' and delete_status='0'")->get();
            $tag_details = TaggingMaster::whereRaw("TagId='$TagId'")->first();
            $brand_id = $tag_details->brand_id;
            $product_category_id = $tag_details->product_category_id;
            $product_id = $tag_details->product_id;
            $model_id = $tag_details->model_id;
            
            $part_arr           =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_status='1' ");
            $lab_part = LabourCharge::selectRaw("distinct(symptom_type) symptom_type")->whereRaw("1=1")->get();
            $html ='<table border="1" id="tbl_part">
            <thead id="thead">
            <tr>
                <th>Sr. No.</th>
                <th>Part Name</th>
                <th>Part Number</th>
                <th>Quantity</th>
                <th>Color</th>
                <th>Part Type</th>
                <th>Customer Price Per Unit</th>
                <th>GST %</th>
                <th>Total Amount</th>

            </tr>';
            ?>
    
     
                <?php $i = 1; $total =0; $parts_name_str = array();
                foreach($tagg_part_npc as $tpart) {
                    $html .='<tr id="tr'.$tpart->part_id.'">';
                    $html .='<td>'.$i++.'</td>';
                    $html .='<td>';
                    $html .='<select form="estmt_approve_save" name="SparePart[';
                    $html .=$tpart->part_id;
                    $html .='][spare_id]" id="part_name';
                    $html .=$tpart->part_id;
                    $html .='"   class="form-control" onchange="get_partno('."'";
                    $html .=$tpart->part_id;
                    $html .= "'";
                    $html .= ',this.value)" >
                            <option value="">Select</option>';
                    foreach($part_arr as $part)
                    {
                        $html .= '<option value="';
                        $html .=$tpart->part_id.'" ';
                        if($part->spare_id==$tpart->spare_id) { $html .= 'selected';}
                        $html .='>';
                        $html .=$part->part_name;
                        //$parts_name_str[] =  $part->part_name;
                        $html .='</option>';
                    }
                    $parts_name_str[] =  $tpart->part_name;
                    $html .= '</select>
                        </td>
                        <td>
                            <select form="estmt_approve_save" id="part_no';
                    $html .=$tpart->part_id;
                    $html .='"  class="form-control"  >
                                <option value="';
                    $html .=$tpart->part_no.'">';
                    $html .=$tpart->part_no.'</option>';
                    $html .='</select>
                        </td>
                        <td>
                            <input form="estmt_approve_save" maxlength="5" id="no_pen_part';
                    $html .= $tpart->part_id;
                    $html .= '" class="form-control" readonly="" type="text"  value="';
                    $html .=$tpart->pending_parts.'" >';
                    $html .= '</td>
                        <td>
                            <input form="estmt_approve_save" id="color';
                    $html .= $tpart->part_id;
                    $html .= '" class="form-control" readonly="" type="text"  value="';
                    $html .=$tpart->color;
                    $html .= '" >
                        </td>
                        <td>
                            <select form="estmt_approve_save" id="charge_type';
                    $html .= $tpart->part_id;
                    $html .='"  class="form-control"  >';
                    $html .= '<option value="Chargeable" ';
                    if($tpart->charge_type=='Chargeable') { $html .= 'selected';}
                    $html .= '>Chargeable</option>';
                    $html .= '<option value="Non Chargeable" ';
                    if($tpart->charge_type=='Non Chargeable') { $html .= 'selected';}
                    $html .='>Non Chargeable</option>
                            </select>';
                    
                    $html .= '</td>';
                    $html .= '<td style="text-align:right;">'.number_format((float)$tpart->customer_price,2,'.','');
                    $html .= '</td>';
                    
                    $html .= '<td style="text-align:right;">'.number_format((float)$tpart->gst,2,'.','');
                    $html .= '</td>';
                    $html .= '<td style="text-align:right;">'.number_format((float)$tpart->total,2,'.','');
                    $html .= '</td></tr>';
                    $total +=$tpart->total;
                }
                
                foreach($tagg_lbr_npc as $tpart) {
                    $html .='<tr id="tr'.$tpart->tlp_id.'">';
                    $html .='<td>'.$i++.'</td>';
                    $html .='<td>';
                    $html .='<select form="estmt_approve_save" name="LabPart[';
                    $html .=$tpart->tlp_id;
                    $html .='][spare_id]" id="part_name';
                    $html .=$tpart->tlp_id;
                    $html .='"   class="form-control" onchange="get_partno('."'";
                    $html .=$tpart->tlp_id;
                    $html .= "'";
                    $html .= ',this.value)" >
                            <option value="">Select</option>';
                    foreach($lab_part as $part)
                    {
                        $html .= '<option value="';
                        $html .=$tpart->tlp_id.'" ';
                        if($part->symptom_type==$tpart->symptom_type) { $html .= 'selected';}
                        $html .='>';
                        $html .=$part->symptom_type;
                        //$parts_name_str[] =  $part->symptom_type;
                        $html .='</option>';
                    }
                    $html .= '</select>
                        </td>
                        <td>
                            <select form="estmt_approve_save" id="part_no';
                    $html .=$tpart->tlp_id;
                    $html .='"  class="form-control"  >
                                <option value="';
                    $html .=$tpart->symptom_name.'">';
                    $html .=$tpart->symptom_name.'</option>';
                    $html .='</select>
                        </td>
                        <td>
                            <input form="estmt_approve_save" maxlength="5" id="no_pen_part';
                    $html .= $tpart->tlp_id;
                    $html .= '" class="form-control" readonly="" type="text"  value="';
                    $html .='0'.'" >';
                    $html .= '</td>
                        <td>
                            <input form="estmt_approve_save" id="color';
                    $html .= $tpart->tlp_id;
                    $html .= '" class="form-control" readonly="" type="text"  value="';
                    $html .=$tpart->color;
                    $html .= '" >
                        </td>
                        <td>
                            <select form="estmt_approve_save" id="charge_type';
                    $html .= $tpart->tlp_id;
                    $html .='"  class="form-control"  >';
                    $html .= '<option value="Chargeable" ';
                    if($tpart->charge_type=='Chargeable') { $html .= 'selected';}
                    $html .= '>Chargeable</option>';
                    $html .= '<option value="Non Chargeable" ';
                    if($tpart->charge_type=='Non Chargeable') { $html .= 'selected';}
                    $html .='>Non Chargeable</option>
                            </select>';
                    
                    $html .= '</td>';
                    $html .= '<td style="text-align:right;">'.number_format((float)$tpart->customer_price,2,'.','');
                    $html .= '</td>';
                    
                    $html .= '<td style="text-align:right;">'.number_format((float)$tpart->gst,2,'.','');
                    $html .= '</td>';
                    $html .= '<td style="text-align:right;">'.number_format((float)$tpart->total,2,'.','');
                    $html .= '</td></tr>';
                    $total +=$tpart->total;
                }
                $html .= '</thead>
            <tbody>
                <tr>
                    <td colspan="6"></td>
                    <td colspan="2" style="text-align:right;">Total Estimation </td>
                    <td  align="right">';
                
                $html .= number_format((float)$total,2,'.','');
                $html .='</td>

                </tr>
            </tbody>
        </table>';
             $msg = date('d/m/Y',strtotime($ob_date))." at ".date('h:i',strtotime($ob_date)).' By '.Auth::user()->name.'--'.implode(",",$parts_name_str).' Required';          
            //Session::flash('message', "Estimation Approved Successfully.");
            //Session::flash('alert-class', 'alert-success'); 
        }
        else
        {
            $msg = 'Estimation Approval Failed.'; 
           //Session::flash('message', "Estimation Approval Failed.");
           //Session::flash('alert-class', 'alert-success'); 
        }
        if($submit=='cancel'|| $submit=='not approve')
        {
            echo json_encode(array('status'=>$flag,'msg'=>$msg,'html'=>$html));

            
            return redirect("vendor-observation?whereTag=&TagId=$tag_id");
        }else{

            echo json_encode(array('status'=>$flag,'msg'=>$msg,'html'=>$html)); exit; 

            return redirect("vendor-observation?whereTag=&TagId=$tag_id");exit;
        }

  
     
    }
    
    public function raise_po($part_id,$po_type,$color,$pending_parts,$tag_det)
    {
        DB::beginTransaction();
        //echo $pending_parts;exit;
        #print_r($tag_det);exit;
        $sr_no = 0; $srno = '';
        $part_max_srno = DB::select("select max(sr_no) srno from tagging_spare_part where  date(created_at)=curdate()");
        
        foreach($part_max_srno as $mxno)
        {
            $sr_no = $mxno->srno;
        }

        
        $brand_id = $tag_det->brand_id;
        $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
        $brand_name = $brand_det->brand_name;
        #echo $brand_name;die;
        $sr_arr = $this->get_part_po_no($brand_name,$sr_no,$po_type);
        $part_po_no = $sr_arr['part_po_no'];
        $sr_no =  $sr_arr['sr_no'];
        $po_date = date('Y-m-d');
        if(TagPart::whereRaw("part_id='$part_id'")->update(
                array('sr_no'=>$sr_no,
                    'part_po_no'=>$part_po_no,
                    'part_po_date'=>$po_date,
                    'pending_parts'=>$pending_parts,
                    'pending_status'=>'1',
                    'part_status'=>'pending',
                    'stock_status'=>'Request To HO',
                    'color'=>$color,
                    'po_type'=>$po_type,
                    'updated_by'=>$updated_by,
                    'updated_at'=>$updated_at)))
        {
            $tag_id = $tag_det->tag_id;
            $tag_det1 = TaggingMaster::whereRaw("TagId='$tag_id'")->first();
            $part_pen =(int) $tag_det1->part_pending;
            if(empty($part_pen))
            {
                $part_pen = 0;
            }
            
            $part_pen += $pending_parts;
            $new_pending_parts = $pending_parts;
            
            if(TaggingMaster::whereRaw("TagId='$tag_id'")->update(array('part_status'=>'1','part_pending'=>$part_pen)))
            {
                DB::commit();
                //DB::rollback();
                $part_det = TagPart::whereRaw("part_id='$part_id'")->first();
                $center_id = $part_det->center_id; 
                $spare_id = $part_det->spare_id;
                $InventoryCenter = InventoryCenter::whereRaw("center_id='$center_id' and spare_id='$spare_id' and avail_qty>0")->first();
                if(!empty($InventoryCenter))
                {
                    DB::beginTransaction();
                    $avail_qty = (int)$InventoryCenter->avail_qty; 
                    $part_provided = 0;
                    if($avail_qty>=$new_pending_parts)
                    {
                       $avail_qty -=  $new_pending_parts;
                       $part_provided = $new_pending_parts;
                       $new_pending_parts = 0;
                    }
                    else
                    {
                        $new_pending_parts -=  $avail_qty;
                        $part_provided = $avail_qty;
                        $avail_qty = 0;
                    }
                    
                    if(InventoryCenter::whereRaw("center_id='$center_id' and spare_id='$spare_id'")->update(array('avail_qty'=>$avail_qty)))
                    {
                        $part_det = TagPart::whereRaw("part_id='$part_id' and delete_status='0'")->first();
                        $spare_id = $part_det->spare_id;
                        $InvPart                    = new InvPart();
                        $InvPart->part_id          = $part_id;
                        $InvPart->spare_id          = $spare_id;
                        $InvPart->part_po_no = $part_det->part_po_no;
                        $InvPart->part_po_date = $part_det->part_po_date;
                        $InvPart->sr_no = $part_det->sr_no;
                        $InvPart->center_id = $part_det->center_id;
                        $InvPart->tag_id = $part_det->tag_id;
                        $InvPart->allocation_type = 'center';
                        $InvPart->brand_id = $part_det->brand_id;
                        $InvPart->product_category_id = $part_det->product_category_id;
                        $InvPart->product_id = $part_det->product_id;
                        $InvPart->model_id = $part_det->model_id;
                        $InvPart->spare_id = $part_det->spare_id;
                        $InvPart->part_name = $part_det->part_name;
                        $InvPart->part_no = $part_det->part_no;
                        $InvPart->color = $part_det->color;
                        $InvPart->hsn_code = $InventoryCenter->hsn_code;
                        $InvPart->part_allocated = $part_provided;
                        $InvPart->part_required = $pending_parts;
                        $InvPart->remarks = $part_det->remarks;
                        $InvPart->charge_type = $part_det->charge_type;

                        if($InvPart->save())
                        {
                            $jobsheet_appyly= false;
                            if($new_pending_parts==0)
                            {
                                if(TagPart::whereRaw("part_id='$part_id'")->update(array("delete_status"=>'1')))
                                {

                                    $part_det = TagPart::whereRaw("part_id='$part_id'")->first();
            
                                    $TagDamagePart                    = new TagDamagePart();
                                    $TagDamagePart->part_id          = $part_id;                        
                                    $TagDamagePart->part_po_no = $part_det->part_po_no;
                                    $TagDamagePart->part_po_date = $part_det->part_po_date;
                                    $TagDamagePart->sr_no = $part_det->sr_no;
                                    $TagDamagePart->center_id = $part_det->center_id;
                                    $TagDamagePart->tag_id = $part_det->tag_id;
                                    $TagDamagePart->allocation_type = 'center';
                                    $TagDamagePart->brand_id = $part_det->brand_id;
                                    $TagDamagePart->product_category_id = $part_det->product_category_id;
                                    $TagDamagePart->product_id = $part_det->product_id;
                                    $TagDamagePart->model_id = $part_det->model_id;
                                    $TagDamagePart->spare_id = $part_det->spare_id;
                                    $TagDamagePart->part_name = $part_det->part_name;
                                    $TagDamagePart->part_no = $part_det->part_no;
                                    $TagDamagePart->color = $part_det->color;
                                    $TagDamagePart->charge_type = $part_det->charge_type;
                                    $TagDamagePart->hsn_code = $part_det->hsn_code;
                                    $TagDamagePart->pending_parts = $part_det->pending_parts;            
                                    $TagDamagePart->customer_price = $part_det->customer_price;
                                    $TagDamagePart->gst = $part_det->gst;
                                    $TagDamagePart->total = $part_det->total;
                                    $TagDamagePart->part_status = $part_det->part_status;
                                    $TagDamagePart->pending_status = $part_det->pending_status;            
                                    $TagDamagePart->remarks = addslashes($part_det->remarks);
                                    $TagDamagePart->stock_status = $part_det->stock_status;
                                    $TagDamagePart->request_to_ho = $part_det->request_to_ho;
                                    $TagDamagePart->request_to_ho_date = $part_det->request_to_ho_date;
                                    $TagDamagePart->request_to_ho_by = $part_det->request_to_ho_by;
                                    $TagDamagePart->approve_by = $part_det->approve_by;
                                    $TagDamagePart->approve_date = $part_det->approve_date;
                                    $TagDamagePart->reject_by = $part_det->reject_by;
                                    $TagDamagePart->reject_date = $part_det->reject_date;
                                    $TagDamagePart->ho_reject = $part_det->ho_reject;
                                    $TagDamagePart->ho_reject_by = $part_det->ho_reject_by;
                                    $TagDamagePart->ho_reject_date = $part_det->ho_reject_date;
                                    $TagDamagePart->delete_status = $part_det->delete_status;
                                    $TagDamagePart->npc_req = $part_det->npc_req;
                                    $TagDamagePart->npc_send_date = $part_det->npc_send_date;
                                    $TagDamagePart->npc_send_by = $part_det->npc_send_by;
                                    $TagDamagePart->npc_estimate_by = $part_det->npc_estimate_by;
                                    $TagDamagePart->npc_estimate_date = $part_det->npc_estimate_date;
                                    $TagDamagePart->estmt_cancel_remarks = $part_det->estmt_cancel_remarks;
                                    $TagDamagePart->estmt_not_approve_remarks = $part_det->estmt_not_approve_remarks;
                                    $TagDamagePart->estmt_approve = $part_det->estmt_approve;
                                    $TagDamagePart->estmt_date = $part_det->estmt_date;            
                                    $TagDamagePart->re_estmt = $part_det->re_estmt;
                                    $TagDamagePart->apply = $part_det->apply;
                                    $TagDamagePart->created_at = $updated_at;
                                    $TagDamagePart->created_by = $updated_by;
                                    $TagDamagePart->save();

                                    $jobsheet_appyly = true;
                                    DB::commit();


                                }
                                else
                                {
                                    DB::rollback();
                                }
                            }
                            else
                            {
                                if(TagPart::whereRaw("part_id='$part_id'")->update(array('pending_parts'=>$new_pending_parts)))
                                {
                                    $jobsheet_appyly = true;
                                    DB::commit();
                                }
                                else
                                {
                                    DB::rollback();
                                }
                            }
                            
                            if($jobsheet_appyly)
                            {
                                $job_sheet_arr = array();
                                $ServiceCenter = ServiceCenter::whereRaw("center_id='$center_id'")->first();
                                $job_sheet_same['center_id'] = $center_id;
                                $job_sheet_same['asc_name'] = $ServiceCenter->center_name;
                                $job_sheet_same['job_id'] = $tag_det->TagId;
                                $job_sheet_same['job_no'] = $tag_det->job_no;
                                $job_sheet_same['brand_id'] = $tag_det->brand_id;
                                $job_sheet_same['brand_name'] = $tag_det->Brand;
                                $job_sheet_same['product_category_id'] = $tag_det->product_category_id;
                                $job_sheet_same['category_name'] = $tag_det->Product_Detail;
                                $job_sheet_same['product_id'] = $tag_det->product_id;
                                $job_sheet_same['product_name'] = $tag_det->Product;
                                $job_sheet_same['model_id'] = $tag_det->model_id;
                                $job_sheet_same['model_name'] = $tag_det->Model;
                                $job_sheet_same['warranty_type'] = $tag_det->warranty_type;
                                $job_sheet_same['serial_no'] = $tag_det->serial_no;
                                
                                $job_sheet_record = array_merge(array(),$job_sheet_same);
                                $job_sheet_record['spare_id'] = $part_det->spare_id;
                                $job_sheet_record['part_name'] = $part_det->part_name;
                                $job_sheet_record['part_no'] = $part_det->part_no;
                                $job_sheet_record['qty'] = $part_det->part_allocated;
                                $job_sheet_record['part_charge_type'] = '';
                                $job_sheet_record['part_amount'] = $part_det->total;
                                $job_sheet_record['labour_amount'] = '';
                                $job_sheet_record['asc_po_no'] = $part_det->asc_po_no;
                                $job_sheet_record['claim_type'] = 'Part';
                                $job_sheet_arr[] = $job_sheet_record;
                                
                                //labour charge work starts from here.
                                /*$job_sheet_record = array();
                                $job_sheet_record = array_merge(array(),$job_sheet_same);
                                $job_sheet_record['part_name'] = $part_det->part_name;
                                $job_sheet_record['part_no'] = $part_det->part_no;
                                $job_sheet_record['qty'] = $part_det->qty;
                                $lab_id = $part_det->lab_id;
                                $LabourCharge = LabourCharge::whereRaw("lab_id='$lab_id'")->first();
                                $tag = json_encode($LabourCharge,true);
                                $job_sheet_record['part_charge_type'] = $LabourCharge['symptom_name'];
                                $job_sheet_record['part_amount'] = '';
                                $job_sheet_record['labour_amount'] = '100';
                                $job_sheet_record['asc_po_no'] = $part_det->asc_po_no;
                                $job_sheet_record['claim_type'] = 'Labour';
                                $job_sheet_arr[] = $job_sheet_record;*/
                            }
                            
                            
                        }
                        else
                        {
                            DB::rollback();
                        }
                    }
                    else
                    {
                        DB::rollback();
                    }
                }
                else
                {
                    //exit;
                }
                
            }
            else
            {
               //do things here. 
                //echo 'no';exit;
            }
        }
    }
    
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
    
    // Customer Details

          
    public function save_customer_details_observation(Request $request)
    {
        $TagId = addslashes($request->input('TagId')); 
        $Customer_Group = addslashes($request->input('Customer_Group')); 
        $Customer_Name = addslashes($request->input('Customer_Name'));  
        $Contact_No = addslashes($request->input('Contact_No'));
        $Alt_No = addslashes($request->input('Alt_No'));
        $Customer_Address = $request->input('Customer_Address');
        $Landmark = addslashes($request->input('Landmark'));
        $call_rcv_frm = addslashes($request->input('call_rcv_frm'));
        $state = addslashes($request->input('state'));
        $Gst_No = addslashes($request->input('Gst_No'));
        $email = addslashes($request->input('email'));
        $pincode = addslashes($request->input('pincode'));
        $place = addslashes($request->input('place'));
    
        
    
        $taggingArr = array();
        // $UserType = Session::get('UserType');
        
        $taggingArr['Customer_Group']=$Customer_Group;
        $taggingArr['Customer_Name']=$Customer_Name;
        $taggingArr['Contact_No']=$Contact_No;
        $taggingArr['Alt_No']= $Alt_No;
        $taggingArr['Customer_Address']=$Customer_Address;
        $taggingArr['Landmark']=$Landmark;
        $taggingArr['call_rcv_frm']=$call_rcv_frm;
        $taggingArr['state']=$state;
        
        $state_code_arr = StateMaster::whereRaw("state_name='$state'")->first();
        $state_code = $state_code_arr->state_code;
        $region_id = $state_code_arr->region_id;
        
        
        
        $dist_details =  PincodeMaster::whereRaw("pincode='$pincode'")->first();
        $dist_id = $dist_details->dist_id;
        
        
        
        $taggingArr['state_code']=$state_code;
        $taggingArr['pincode']=$pincode;
        $taggingArr['region_id']=$region_id;
        $taggingArr['dist_id']=$dist_id;
        
        
        $taggingArr['Gst_No']=$Gst_No;
        $taggingArr['email']=$email;
        $taggingArr['place']=$place;
    
    
             //print_r($taggingArr);exit;
        $Tag = DB::update("update tagging_master set Customer_Group='$Customer_Group', Customer_Name='$Customer_Name', Contact_No='$Contact_No', Alt_No='$Alt_No', Customer_Address='$Customer_Address',
        Landmark='$Landmark', call_rcv_frm='$call_rcv_frm', state='$state', state_code='$state_code', pincode='$pincode', region_id='$region_id',
        dist_id='$dist_id', Gst_No='$Gst_No', email='$email', place='$place' where TagId ='$TagId'");
            
             if($Tag == '1')
             {
             echo "1";
             }
             else if($Tag == '0')
             {
             echo "2";
             }
             else
             {
             echo "0";
             }

          //  $Tag =TaggingMaster::whereRaw("TagId='$TagId' ")->update($taggingArr);
 
    
        //     if(TaggingMaster::whereRaw("TagId='$TagId' ")->update($taggingArr))
        //    {
        //     echo "1";
        //    }
        //    else
        //    {
        //     echo "0";
        //    }
                
                
        exit; 
      }

    // End Customer Details


    
    // Product Details

          
    public function save_product_details_observation(Request $request)
    {
        $TagId = addslashes($request->input('TagId')); 
      
      
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
        $observation = addslashes($request->input('observation'));
        $ccsc = addslashes($request->input('ccsc'));
        $report_fault = $request->input('report_fault');
        $add_cmnt = $request->input('add_cmnt');
        
        $brand_det = BrandMaster::whereRaw("brand_id='$Brand'")->first();
        $brand_name = $brand_det->brand_name;
        
        $product_catedet = ProductCategoryMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail'")->first();
        $category_name = $product_catedet->category_name;
        
        $product_det = ProductMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product'")->first();
        $product_name = $product_det->product_name;
        
        $model_det = ModelMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model'")->first();
        $model_name = $model_det->model_name;
        
        
        
          
        
    
        $taggingArr = array();
        // $UserType = Session::get('UserType');
        
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
        
        
        $taggingArr['Serial_No']=$Serial_No;
        $taggingArr['man_ser_no']=$man_ser_no;
        $taggingArr['dealer_name']=$dealer_name;
        
        
        $taggingArr['Bill_Purchase_Date']=$Bill_Purchase_Date;
        //$taggingArr['asc_code']=$asc_code;
        $taggingArr['warranty_card']=$warranty_card;
        $taggingArr['invoice']=$invoice;

        $taggingArr['report_fault']=$report_fault;
        //$taggingArr['service_required']=$service_required;

        $taggingArr['invoice_no']=$invoice_no;
        $taggingArr['observation']=$observation;
        $taggingArr['ccsc']=$ccsc;
        $taggingArr['add_cmnt']=$add_cmnt;
    
    
             //print_r($taggingArr);exit;
        $Tag = DB::update("update tagging_master set service_type='$service_type',warranty_type='$warranty_type',
        warranty_category='$warranty_category',brand_id='$Brand',product_category_id='$Product_Detail',product_id='$Product',
        model_id='$Model',Brand='$brand_name',Product_Detail='$category_name',Product='$product_name',Model='$model_name',
        Serial_No='$Serial_No',man_ser_no='$man_ser_no',dealer_name='$dealer_name',Bill_Purchase_Date='$Bill_Purchase_Date',
        warranty_card='$warranty_card',invoice='$invoice',report_fault='$report_fault',invoice_no='$invoice_no',observation='$observation',
        ccsc='$ccsc', add_cmnt='$add_cmnt'  where TagId ='$TagId'");
            
             if($Tag == '1')
             {
             echo "1";
             }
             else if($Tag == '0')
             {
             echo "2";
             }
             else
             {
             echo "0";
             }
    
        //     if(TaggingMaster::whereRaw("TagId='$TagId' ")->update($taggingArr))
        //    {
        //     echo "1";
        //    }
        //    else
        //    {
        //     echo "0";
        //    }
                
    
                
        exit; 
      }

    // End Product Details


 // Closure Code Details

          
    public function save_closure_code_observation(Request $request)
    {
        

        $TagId = addslashes($request->input('TagId')); 
        $closure_codes = addslashes($request->input('closure_codes')); 

        $taggingArr = array();
        // $UserType = Session::get('UserType');
     
        $taggingArr['closure_codes']=$closure_codes;
        $closure_date =date('Y-m-d H:i:s');
        $Job_Status = "Resolved";
        $final_job_status ="Closed";
     
        $Tag = DB::update("update tagging_master set closure_codes='$closure_codes',closure_date ='$closure_date',Job_Status='$Job_Status',final_job_status='$final_job_status',case_close='1' where TagId ='$TagId'");
            
        if($Tag == '1')
        {
            echo "1";
        }
        else if($Tag == '0')
        {
            echo "2";
        }
        else
        {
            echo "0";
        }
 
        //  if(TaggingMaster::whereRaw("TagId='$TagId' ")->update($taggingArr))
        // {
        //  echo "1";
        // }
        // else
        // {
        //  echo "0";
        // }
             
 
             
     exit; 
    }

 // End Closure Code Details 
    public function save_add_comment(Request $request)
    {
        $TagId = addslashes($request->input('TagId')); 
        $add_cmnt = addslashes($request->input('add_cmnt')); 
        $taggingArr = array();
     // $UserType = Session::get('UserType');
     
        $taggingArr['add_cmnt']=$add_cmnt;
        if(TaggingMaster::whereRaw("TagId='$TagId' ")->update($taggingArr))
        {
         echo "1";
        }
        else
        {
         echo "0";
        }
             
 
             
     exit; 
     
     
    }
//   code start here

    public function save_image(Request $request)
    {
        //print_r($_FILES);exit;
        $tag_id=$request->input('TagId');
        $file_type=$request->input('file_type');
        $file_arr = array('wrrn'=>'warranty_card_copy','prcs'=>'purchase_copy','srl'=>'serial_no_copy','smtm1'=>'symptom_photo1','smtm2'=>'symptom_photo2','smtm3'=>'symptom_photo3');
        $today_date = date('Y_m_d_h_i_s');
        $taggingArr = array();
        foreach($_FILES as $key=>$file)
        {
            $image_name=$_FILES[$key]['name'];
            $file_name=$file_arr[$key];
            $inputName = $key;
            $ext= $today_date.substr($_FILES[$inputName]['name'],strrpos($_FILES[$inputName]['name'],'.'),strlen($_FILES[$inputName]['name']));
            //echo $ext;die;
            Storage::disk('supreme')->put("$tag_id/$file_name"."$ext", file_get_contents($_FILES[$inputName]['tmp_name']));
            $taggingArr[$file_name]=$file_name."$ext";
        }            
               
            $file_name = $file_name.$ext;

            $UserId = Session::get('UserId');

            $data = array(
               "TagId" => $tag_id,
               "image_type" => $file_type,
               "img_url" => $file_name,
               "created_by" => $UserId,
               "created_at" => date('Y-m-d H:i:s')
            );

            $imageStatus = TagImage::create($data);

            $str_server = str_replace('public', '', $_SERVER['APP_URL']); 
            $image = $str_server.'/storage/app/supreme/'.$tag_id.'/'.$file_name;
  
            if($imageStatus) 
            {
                
                return response()->json(['image' => $image,'file_type' => $file_type]);
                
            }
            else{
                echo"0";
            }



    }


    public function save_video(Request $request)
    {
        #print_r($_FILES);exit;
        $tag_id=$request->input('TagId');
        $file_type=$request->input('file_type');
        $file_arr = array('video'=>'video');
        $today_date = date('Y_m_d_h_i_s');
        $taggingArr = array();
        foreach($_FILES as $key=>$file)
        {
            
            $image_name=$_FILES[$key]['name'];
            $file_name=$file_arr[$key];
            $inputName = $key;
            $ext= $today_date.substr($_FILES[$inputName]['name'],strrpos($_FILES[$inputName]['name'],'.'),strlen($_FILES[$inputName]['name']));
            Storage::disk('supreme')->put("$tag_id/$file_name"."$ext", file_get_contents($_FILES[$inputName]['tmp_name']));
            $taggingArr[$file_name]=$file_name."$ext";
        }            
               
            $file_name = $file_name.$ext;

            $UserId = Session::get('UserId');

            $data = array(
               "TagId" => $tag_id,
               "image_type" => $file_type,
               "img_url" => $file_name,
               "created_by" => $UserId,
               "created_at" => date('Y-m-d H:i:s')
            );

            $imageStatus = TagImage::create($data);

            $str_server = str_replace('public', '', $_SERVER['APP_URL']); 
            $image = $str_server.'/storage/app/supreme/'.$tag_id.'/'.$file_name;
  
            if($imageStatus) 
            {
                
                return response()->json(['image' => $image,'file_type' => $file_type]);
                
            }
            else{
                echo"0";
            }



    }
        
    public function destroy_TagImage($id){
   
        TagImage::find($id)->delete($id);
      
        return response()->json([
            'success' => 'Record deleted successfully!'
        ]);

        echo"1";
    }

    // Code End Here

    public function lc_symptom_name(Request $request)
    {
        $lab_str=$request->input('symptom_type');
        $lab_arr = explode('-',$lab_str);
        $symptom_type = $lab_arr[1];
        $lbr_charge_det = LabourCharge::whereRaw("symptom_type='$symptom_type'")->get();
        
        foreach($lbr_charge_det as $lcd)
        {
            echo '<option value="'.$lcd->lab_id.'">'.$lcd->symptom_name.'</option>';
        }
        exit;
    }

    public function view_complete_job(Request $request)
    {
        Session::put("page-title","View Complete Job");
        
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
           //$tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='1'  $whereUser  "; 
           $tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_accept='1' and case_close='1'  $whereUser  ";
        }
        else
        {
            //$tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='1'  $whereUser    $whereTag"; 
            //$tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='0'  $whereUser    $whereTag";   
            $tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where  case_close='1'   $whereUser    $whereTag";
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/vendor-view-complete-job'; 
        return view('vendor-view-complete-job')
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

    public function view_delivery_job(Request $request)
    {
        Session::put("page-title","View Delivery Job");
        
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
           //$tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='1'  $whereUser  "; 
           $tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_accept='1' and delivery_date IS NOT NULL $whereUser  ";
        }
        else
        {
            //$tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='1'  $whereUser    $whereTag"; 
            //$tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='0'  $whereUser    $whereTag";   
             $tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where delivery_date IS NOT NULL  $whereUser    $whereTag";
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/vendor-delivery-job'; 
        return view('vendor-delivery-job')
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

    public function dealer_save_observation(Request $request)
    {
        #print_r($request->all());die;

        $RTagId = addslashes($request->input('TagId'));
        $dealer_name = addslashes($request->input('DealerName'));
        $Landmark = addslashes($request->input('location'));
        $region_id = addslashes($request->input('region_id'));
        $state = addslashes($request->input('state'));
        $pincode = addslashes($request->input('pincode'));
        $Customer_Name = addslashes($request->input('Customer_Name'));
        $Contact_No = addslashes($request->input('Contact_No'));


        $taggingArr = array();
        // $UserType = Session::get('UserType');
        
        $taggingArr['dealer_name']=$dealer_name;
        $taggingArr['Landmark']=$Landmark;
        $taggingArr['Contact_No']=$Contact_No;
        $taggingArr['state']= $state;
        $taggingArr['pincode']=$pincode;
        $taggingArr['Customer_Name']=$Customer_Name;
        $taggingArr['state']=$state;
        
        $state_code_arr = StateMaster::whereRaw("state_name='$state'")->first();
        $state_code = $state_code_arr->state_code;
        #$region_id = $state_code_arr->region_id;
        
        
        
        $dist_details =  PincodeMaster::whereRaw("pincode='$pincode'")->first();
        $dist_id = $dist_details->dist_id;
        
        
        
        $taggingArr['state_code']=$state_code;
        $taggingArr['pincode']=$pincode;
        $taggingArr['region_id']=$region_id;
        $taggingArr['dist_id']=$dist_id;
    


        #print_r($taggingArr);exit;

        $Tag = DB::update("update tagging_master set dealer_name='$dealer_name', Customer_Name='$Customer_Name', Contact_No='$Contact_No', Alt_No='$Alt_No', Customer_Address='$Customer_Address',
        Landmark='$Landmark', call_rcv_frm='$call_rcv_frm', state='$state', state_code='$state_code', pincode='$pincode', region_id='$region_id',
        dist_id='$dist_id' where TagId ='$RTagId'");
            
                if($Tag == '1')
                {
                echo "1";
                }
                else if($Tag == '0')
                {
                echo "2";
                }
                else
                {
                echo "0";
                }
                
                
        exit; 
    }


    public function vehicle_save_observation(Request $request)
    {
        #print_r($request->all());die;

        $RTagId = addslashes($request->input('TagId'));
        $man_ser_no = addslashes($request->input('man_ser_no')); 
        $vehicle_sale_date = addslashes($request->input('vehicle_sale_date'));
        $vin_no = addslashes($request->input('vin_no'));
        $mielage = addslashes($request->input('mielage'));
        $warranty_type = addslashes($request->input('warranty_type'));        
        $system_sw_version = addslashes($request->input('system_sw_version'));


        $Product = addslashes($request->input('Product'));
        $Model = addslashes($request->input('Model'));

        $vehicle_variant = addslashes($request->input('vehicle_variant'));
        
        $product_det = ProductMaster::whereRaw(" product_id='$Product'")->first();
        $product_name = $product_det->product_name;
    
        $model_det = ModelMaster::whereRaw("product_id='$Product' and model_id='$Model'")->first();
        $model_name = $model_det->model_name;

        #print_r($taggingArr);exit;
        $Tag = DB::update("update tagging_master set man_ser_no='$man_ser_no', vehicle_sale_date='$vehicle_sale_date', vin_no='$vin_no', mielage='$mielage', warranty_type='$warranty_type',
        system_sw_version='$system_sw_version', Product='$product_name', Model='$model_name', product_id='$Product', model_id='$Model',vehicle_variant='$vehicle_variant' where TagId ='$RTagId'");
            
        if($Tag == '1')
        {
        echo "1";
        }
        else if($Tag == '0')
        {
        echo "2";
        }
        else
        {
        echo "0";
        }
                
                
        exit; 
    }

    public function complaint_save_observation(Request $request)
    {
        #print_r($request->all());die;

        $RTagId = addslashes($request->input('TagId'));
        $ccsc = addslashes($request->input('ccsc'));
        $job_card = addslashes($request->input('job_card'));
        $videos = addslashes($request->input('videos'));
        $crf = addslashes($request->input('crf'));
        $ftir = addslashes($request->input('ftir'));
        $ftir_no = addslashes($request->input('ftir_no'));
        $supr_analysis = addslashes($request->input('supr_analysis'));
        $remarks = addslashes($request->input('remarks'));
        $issue_type = addslashes($request->input('issue_type'));
        $issue_cat = addslashes($request->input('issue_cat'));
        $mobile_handset_model = addslashes($request->input('mobile_handset_model'));
        $Alt_No = addslashes($request->input('Alt_No'));
        $visit_type = addslashes($request->input('visit_type'));
        $site_visit_date = addslashes($request->input('site_visit_date'));
        $part_replace = addslashes($request->input('part_replace'));
        $part_replace_date = addslashes($request->input('part_replace_date'));
        $issue_resolved_date = addslashes($request->input('issue_resolved_date'));
        $Job_Status = addslashes($request->input('Job_Status'));
        $dispatch_date = addslashes($request->input('dispatch_date'));
        $tat = addslashes($request->input('tat'));
        $tat_delay_remarks = addslashes($request->input('tat_delay_remarks'));
        $defective_part_rcv = addslashes($request->input('defective_part_rcv'));
        $final_job_close_date = addslashes($request->input('final_job_close_date'));
        $final_job_status = addslashes($request->input('final_job_status'));

        $logs_taken = addslashes($request->input('logs_taken'));

        
        $type = addslashes($request->input('type'));
        
        if($type =="ExcelFormat")
        {   
            $UserType = Session::get('UserType');

            $center_id="154";
            $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
            $asc_code=$center_det->asc_code;
            $center_allocation_date =date('Y-m-d H:i:s');
            $alloc_qry = "And Case Allocated To ASC ".$center_det->center_name;

            $year = date('y');
            $month = date('m');

            $qr_max_no = "SELECT MAX(sr_no) srno FROM `tagging_master` WHERE  job_year='$year' AND job_month='$month'";
            $max_json           =   DB::select($qr_max_no);
            $max = $max_json[0];
            $sr_no = $max->srno;
            $str_no = "000000";
            $sr_no = $sr_no+1;
            $len = strlen($str_no);
            $newlen = strlen("$sr_no");
            $new_no = substr_replace($str_no, $sr_no, $len-$newlen,$newlen);
            $subcode = 'CL';
            $ticket_no  = "{$subcode}{$year}{$month}{$new_no}";

            $Tag = DB::update("update tagging_master set ticket_no='$ticket_no',job_year='$year',job_month='$month',sr_no='$sr_no',center_id='$center_id',asc_code='$asc_code',center_allocation_date='$center_allocation_date',ccsc='$ccsc', job_card='$job_card', videos='$videos', crf='$crf', ftir='$ftir',
            ftir_no='$ftir_no', supr_analysis='$supr_analysis', remarks='$remarks', issue_type='$issue_type', issue_cat='$issue_cat',mobile_handset_model='$mobile_handset_model',
            Alt_No='$Alt_No', visit_type='$visit_type', site_visit_date='$site_visit_date', part_replace='$part_replace', part_replace_date='$part_replace_date',issue_resolved_date='$issue_resolved_date',
            Job_Status='$Job_Status', dispatch_date='$dispatch_date', tat='$tat', tat_delay_remarks='$tat_delay_remarks', defective_part_rcv='$defective_part_rcv',final_job_close_date='$final_job_close_date',
            final_job_status='$final_job_status',logs_taken='$logs_taken' where TagId ='$RTagId'");

            echo "$ticket_no Tickets Generate Successfully! ".$alloc_qry;

        }else{

            $Tag = DB::update("update tagging_master set ccsc='$ccsc', job_card='$job_card', videos='$videos', crf='$crf', ftir='$ftir',
            ftir_no='$ftir_no', supr_analysis='$supr_analysis', remarks='$remarks', issue_type='$issue_type', issue_cat='$issue_cat',mobile_handset_model='$mobile_handset_model',
            Alt_No='$Alt_No', visit_type='$visit_type', site_visit_date='$site_visit_date', part_replace='$part_replace', part_replace_date='$part_replace_date',issue_resolved_date='$issue_resolved_date',
            Job_Status='$Job_Status', dispatch_date='$dispatch_date', tat='$tat', tat_delay_remarks='$tat_delay_remarks', defective_part_rcv='$defective_part_rcv',final_job_close_date='$final_job_close_date',
            final_job_status='$final_job_status',logs_taken='$logs_taken' where TagId ='$RTagId'");

            if($Tag == '1')
            {
                echo "1";
            }
            else if($Tag == '0')
            {
                echo "2";
            }
            else
            {
                echo "0";
            }
        }
  
        exit; 
    }

    public function save_special_approval(Request $request)
    {
        
        $TagId = $request->input('TagId');
        $selectedOption = $request->input('repairOption');

        $specialArr['special_approval_type']=$selectedOption;
        $specialArr['special_approval_status']=0;
        
        if(TaggingMaster::whereRaw("TagId='$TagId'")->update($specialArr))
        {
            echo "1";
        }
        else
        {
            echo '0';
        }
        exit;
    }


    public function ho_transfer_job(Request $request)
    {
        Session::put("page-title","ASC Transferred Job Approval ");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        
        $whereUser = "";


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
            
           $tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where special_approval_status='0' and date(tm.created_at)= curdate()";
        }
        else
        {
            
           $tag_data_qr = "select tm.*,dm.dist_name,tsc.center_name from $from_table tagging_master tm $on_table left join tbl_service_centre tsc on tm.center_id = tsc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_reject='0' and special_approval_status='0'    $whereTag";    
        }

        //echo $tag_data_qr;die;
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/ho-transfer-job';
        return view('ho-transfer-job')
            ->with('pin_master',$pin_master)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('pincode',$pincode)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('DataArr',$DataArr)
                
                ->with('url',$url)
                ->with('whereTag',$whereTag); 
                
    }

    

    public function approved_special_job(Request $request)
    {
        
        $TagId = $request->input('tagId');
        $UserId = Session::get('UserId');

        $specialArr['special_approval_status']=1;
        $specialArr['special_approval_by']=$UserId;
        $specialArr['special_approval_date']=date('Y-m-d H:i:s');
  
        if(TaggingMaster::whereRaw("tagId='$TagId'")->update($specialArr))
        {
            echo "1";
        }
        else
        {
            echo '0';
        }
        exit;
    }

    public function save_special_discount(Request $request)
    {
        
        $TagId = $request->input('TagId');
        $special_discount = $request->input('special_discount');

        $specialArr['special_discount']=$special_discount;

  
        if(TaggingMaster::whereRaw("tagId='$TagId'")->update($specialArr))
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


