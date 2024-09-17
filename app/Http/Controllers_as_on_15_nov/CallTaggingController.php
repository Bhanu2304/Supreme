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

class CallTaggingController extends Controller
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
    
    
    
    
    public function search_audit_case(Request $request)
    {
        Session::put("page-title","View Partial Close Case");
        
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
        else 
        {
            
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
            $tag_data_qr = "select tm.*,DATE_FORMAT(tm.created_at,'%d-%b-%y') created_at from $from_table tagging_master tm $on_table where tm.observation is not null $whereUser and date(tm.created_at)=curdate();"; 
        }
        else
        {
            $tag_data_qr = "select tm.*,DATE_FORMAT(tm.created_at,'%d-%b-%y') created_at from $from_table tagging_master tm $on_table where tm.observation is not null $whereUser $whereTag"; 
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/view-partial-close-case';
        return view('view-partial-close-case')
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
    
    public function view_audit_case(Request $request)
    {
        Session::put("page-title","View Partial Close Case");
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
        
        $data_json = TaggingMaster::whereRaw("TagId = '$TagId' $whereTag1 and observation is not null ")->first();
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
        

        $url = $_SERVER['APP_URL'].'/view-partial-close-case';
        //print_r($data); exit;
        return view('partial-close-observation')
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
    
    
    
    
    
    
    
    
    
    
    
    
}

