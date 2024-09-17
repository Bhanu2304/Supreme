<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SparePart;

use App\PincodeMaster;
use App\TagPart;
use App\InvPart;
use App\InventoryCenter;
use App\ServiceCenter;
use App\TaggingMaster;
use App\RegionalManagerMaster;
use App\StateMaster;
use App\InvoicePart;
use App\ProductMaster;

use DB;
use Auth;
use Session;


class CollectionController extends Controller
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
    
    public function view_invoice(Request $request)
    {
        Session::put("page-title","View Payment Status");
        
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
        //$VendorPincode=Session::get('pincode');
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and tm.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.Pincode = '$pincode'";
        }
        if(strlen($contact_no)==6)
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
            $tag_data_qr = "select * from $from_table tagging_master tm $on_table where  tm.payment_entry='1'  $whereTag1 and tm.case_close='1'  and date(tm.created_at)=curdate();"; 
        }
        else
        {
            $tag_data_qr = "select * from $from_table tagging_master tm $on_table where tm.payment_entry='1'  $whereTag1 and tm.case_close='1'   $whereUser"; 
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        
        $url = $_SERVER['APP_URL'].'/view-invoice';
        return view('view-payment-status')
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
    
    
    public function index(Request $request)
    {
        Session::put("page-title","Collection Creation");
        $TagId = $request->input('TagId'); 
        //$TagId = 39;
        $whereTag = $request->input('whereTag'); 
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $ServiceCenter = ServiceCenter::whereRaw("center_id='$Center_Id'")->first();
        $state_id_center = $ServiceCenter->state;
        $StateMaster = StateMaster::whereRaw("state_id='$state_id_center'")->first();
        $state_code_center = $StateMaster->state_code;
        
        
        $whereTag1 =$whereUser = "";
        if($UserType=='Admin' || $UserType=='ASM' || $UserType=='RSM' || $UserType=='NSM')
        {
            
        }
        else
        {
            $whereUser = "and center_id='$Center_Id'";
        }
        
        
        $data_json = TaggingMaster::whereRaw("TagId = '$TagId' $whereUser ")->first();
        $data = json_decode($data_json,true);
        $state_name_client = $data['State']; 
        $StateMaster = StateMaster::whereRaw("state_name='$state_name_client'")->first();
        $state_code_client = $StateMaster->state_code;
        
        $ProductMaster_json = ProductMaster::whereRaw("product_status='1'")->get();
        $ProductMaster = json_decode($ProductMaster_json,true);
        
        $part_arr           =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        
         $qr1 = "SELECT state_id,state_name FROM state_master st order by state_name";
        $state_json           =   DB::select($qr1); 
        
       
        
        
        
        $qr2 = "SELECT tsp.spare_id,tsp.part_name,tsp.part_no,tsp.hsn_code FROM `tbl_inventory_part` tsp
INNER JOIN `tbl_spare_parts` tsrt ON tsp.part_name = tsrt.part_name 
AND tsp.part_no = tsrt.part_no 
AND tsp.hsn_code = tsrt.hsn_code
WHERE tsp.tag_id='$TagId'
 ";
        $spare_object           =   DB::select($qr2); 
        
        $spare_req_master = array();
        foreach($spare_object as $obj)
        {
            
            $part_name        =   $obj->part_name;
            $part_no          =   $obj->part_no;
            $hsn_code         =   $obj->hsn_code;
            $key = $part_name.'##'.$part_no.'##'.$hsn_code;
            
            $spare_part = SparePart::whereRaw("part_name = '$part_name' and part_no='$part_no' and hsn_code='$hsn_code'")->first();
            
            if(in_array($key,array_keys($spare_req_master)))
            {
                $spare_req_master[$key]['qty'] +=1;
            }
            else
            {
                $spare_req_master[$key]['part_name']  = $part_name;
                $spare_req_master[$key]['part_no']  = $part_no;
                $spare_req_master[$key]['hsn_code']  = $hsn_code;
                $spare_req_master[$key]['qty']  = 1;
                $spare_req_master[$key]['part_rate']  = $spare_part->part_rate;
                $spare_req_master[$key]['part_tax']  = $spare_part->part_tax;
            }
        }
        
        
        
        $qr3 = "SELECT field_name,field_code FROM `tbl_symptom` where sypt_status='1' ";
        $symptom_json           =   DB::select($qr3);
        
        
        
        
        
        
        
        $brand_master = $state_master = $sypt_master = $set_con_master = $acc_master = array();
        
        
       
        
        foreach($symptom_json as $sympt)
        {
            $sypt_master[$sympt->field_code] = $sympt->field_name;
        }
        
        
        
        $TagPart = TagPart::whereRaw("Tag_Id")->get();
        
        
        $url = $_SERVER['APP_URL'].'/view-invoice';
        
        //print_r($data); exit;
        return view('upd-payment')
                ->with('data',$data)
                ->with('TagId',$TagId)
                ->with('whereTag',$whereTag)
                ->with('sc',$ServiceCenter)
                ->with('spare_req_master',$spare_req_master)
                ->with('model_master',$model_master)
                ->with('state_master',$state_master)
               ->with('sypt_master',$sypt_master)
               ->with('set_con_master',$set_con_master)
               ->with('acc_master',$acc_master)
                ->with('part_arr', $part_arr)
                ->with('url', $url)
                ->with('asc_master',$asc_master)
                ->with('state_code_client',$state_code_client)
                ->with('state_code_center',$state_code_center)
                ->with('ProductMaster',$ProductMaster);
                
    }
    
    public function save_payment(Request $request)
    {
        
        $tag_id = $request->input('tag_id');
        $UserId = Auth::user()->id;    
        $created_at=date("Y-m-d H:i:s"); 
        $payment_status = $request->input('payment_status');
        $payment_source = $request->input('payment_source');
        
        
        
        $taggingArr = array();
        
        $taggingArr['payment_status']=$payment_status;
        $taggingArr['payment_source']=$payment_source;
        
        
        $taggingArr['payment_entry']='0';
        $taggingArr['payment_entry_date']=$created_at;
        $taggingArr['payment_entry_by']=$UserId;
                    
        if(TaggingMaster::whereRaw("TagId='$tag_id'")->update($taggingArr))
        {
            Session::flash('message', "Payment Detail Saved Successfully.");
            Session::flash('alert-class', 'alert-success');
        }
        else
        {
            Session::flash('error', "Payment Detail Saved. Please Try Again.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        return redirect('view-invoice');
       
    }
    
    public function view_payment(Request $request)
    {
        Session::put("page-title","View Payment");
        
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
        //$VendorPincode=Session::get('pincode');
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and tm.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.Pincode = '$pincode'";
        }
        if(strlen($contact_no)==6)
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
            $tag_data_qr = "select * from $from_table tagging_master tm $on_table where  tm.payment_entry='0' and tm.final_symptom_status='1'  and tm.case_close='1'  and date(tm.created_at)=curdate();"; 
        }
        else
        {
            $tag_data_qr = "select * from $from_table tagging_master tm $on_table where tm.payment_entry='0' and tm.final_symptom_status='1'  and tm.case_close='1'   $whereTag"; 
        }
        # echo $tag_data_qr;die;
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/view-payment';
        return view('view-payment')
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
    
    public function add_symptom(Request $request)
    {
        Session::put("page-title","Payment Creation");
        $TagId = $request->input('TagId'); 
        //$TagId = 39;
        $whereTag = $request->input('whereTag'); 
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $ServiceCenter = ServiceCenter::whereRaw("center_id='$Center_Id'")->first();
        $state_id_center = $ServiceCenter->state;
        $StateMaster = StateMaster::whereRaw("state_id='$state_id_center'")->first();
        $state_code_center = $StateMaster->state_code;
        
        
        $whereTag1 =$whereUser = "";
        if($UserType=='Admin' || $UserType=='ASM' || $UserType=='RSM' || $UserType=='NSM')
        {
            
        }
        else
        {
            $whereUser = "and center_id='$Center_Id'";
        }
        
        $data_json = TaggingMaster::whereRaw("TagId = '$TagId' $whereUser ")->first();
        $data = json_decode($data_json,true);
        $state_name_client = $data['State']; 
        $StateMaster = StateMaster::whereRaw("state_name='$state_name_client'")->first();
        $state_code_client = $StateMaster->state_code;
        
        
        
        $part_arr           =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        
         $qr1 = "SELECT state_id,state_name FROM state_master st order by state_name";
        $state_json           =   DB::select($qr1); 
        
       
        
        
        
        $qr2 = "SELECT tsp.spare_id,tsp.part_name,tsp.part_no,tsp.hsn_code FROM `tbl_inventory_part` tsp
INNER JOIN `tbl_spare_parts` tsrt ON tsp.part_name = tsrt.part_name 
AND tsp.part_no = tsrt.part_no 
AND tsp.hsn_code = tsrt.hsn_code
WHERE tsp.tag_id='$TagId'
 ";
        $spare_object           =   DB::select($qr2); 
        
        $spare_req_master = array();
        foreach($spare_object as $obj)
        {
            
            $part_name        =   $obj->part_name;
            $part_no          =   $obj->part_no;
            $hsn_code         =   $obj->hsn_code;
            $key = $part_name.'##'.$part_no.'##'.$hsn_code;
            
            $spare_part = SparePart::whereRaw("part_name = '$part_name' and part_no='$part_no' and hsn_code='$hsn_code'")->first();
            
            if(in_array($key,array_keys($spare_req_master)))
            {
                $spare_req_master[$key]['qty'] +=1;
            }
            else
            {
                $spare_req_master[$key]['part_name']  = $part_name;
                $spare_req_master[$key]['part_no']  = $part_no;
                $spare_req_master[$key]['hsn_code']  = $hsn_code;
                $spare_req_master[$key]['qty']  = 1;
                $spare_req_master[$key]['part_rate']  = $spare_part->part_rate;
                $spare_req_master[$key]['part_tax']  = $spare_part->part_tax;
            }
        }
        
        //print_r($spare_req_master); exit;
        
        
        
        $qr3 = "SELECT field_name,field_code FROM `tbl_symptom` where sypt_status='1' ";
        $symptom_json           =   DB::select($qr3);
        
         
       
        
        foreach($symptom_json as $sympt)
        {
            $sypt_master[$sympt->field_code] = $sympt->field_name;
        }
        
        
        
        $TagPart = TagPart::whereRaw("Tag_Id")->get();
        
        
        
        
        //print_r($data); exit;
        return view('upd-payment-symptom')
                ->with('data',$data)
                ->with('TagId',$TagId)
                ->with('whereTag',$whereTag)
                ->with('sc',$ServiceCenter)
                ->with('spare_req_master',$spare_req_master)
                ->with('model_master',$model_master)
                ->with('state_master',$state_master)
               ->with('sypt_master',$sypt_master)
               ->with('set_con_master',$set_con_master)
               ->with('acc_master',$acc_master)
                ->with('part_arr', $part_arr)
                ->with('asc_master',$asc_master)
                ->with('state_code_client',$state_code_client)
                ->with('state_code_center',$state_code_center)
                ->with('ProductMaster',$ProductMaster);
                
    }
    
    public function save_symptom_payment(Request $request)
    {
        
        $tag_id = $request->input('tag_id');
        $UserId = Auth::user()->id;    
        $created_at=date("Y-m-d H:i:s"); 
        $Symptom = $request->input('Symptom');
        
        $taggingArr = array();
        
        $taggingArr['final_symptom']=$Symptom;
        
        
        
        $taggingArr['final_symptom_status']='0';
        $taggingArr['final_symptom_date']=$created_at;
        $taggingArr['final_symptom_by']=$UserId;
                    
        if(TaggingMaster::whereRaw("TagId='$tag_id'")->update($taggingArr))
        {
            Session::flash('message', "Payment Detail Saved Successfully.");
            Session::flash('alert-class', 'alert-success');
        }
        else
        {
            Session::flash('error', "Payment Detail Saved. Please Try Again.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        return redirect('view-payment');
       
    }
}

