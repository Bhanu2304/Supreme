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

use App\ServiceEngineer;
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Facades\Excel;

class JobViewController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
        
    public function index(Request $request)
    {
        Session::put("page-title","Job Details");
        //$Center_Id = Auth::user()->table_id;
        
        $contact_no = $request->input('contact_no');
        $brand_id = $request->input('brand_id');
        $product_category_id = $request->input('product_category_id');
        $product_id = $request->input('product_id');
        $model_id = $request->input('model_id');
        
        if(empty($contact_no))
        {
            
        }
        
        $tag_data_qr = "SELECT tm.*,sc.center_name,dm.dist_name FROM tagging_master tm 
INNER JOIN tbl_service_centre sc ON tm.center_id = sc.center_id 
LEFT JOIN district_master dm ON tm.dist_id = dm.dist_id 
WHERE tm.brand_id='$brand_id' AND product_category_id='$product_category_id' AND 
product_id='$product_id' AND model_id='$model_id' AND  tm.contact_no='$contact_no' ";  //exit;
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/se-job-view';
        return view('se-job-view-contact')
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('DataArr',$DataArr)
                ->with('url', $url)
                ->with('warranty_category', $warranty_category)
                ->with('service_type', $service_type)
                ->with('job_status', $job_status)
                ->with('pincode', $pincode)
                ->with('job_no', $job_no)
                ->with('ticket_no', $ticket_no)
                ->with('back_url','se-job-view-contact')
                ->with('whereTag',$whereTag); 
                
    }
    
    public function job_view(Request $request)
    {
        Session::put("page-title","Job Details");
        $TagId = base64_decode($request->input('TagId')); 
        $whereTag = base64_decode($request->input('whereTag')); 
        $back_url = $request->input('back_url');
        $Center_Id = Auth::user()->table_id;
        $UserId = Session::get('UserId');
        $SeDet = ServiceEngineer::whereRaw("LogIn_Id='$UserId'")->first();
        $SeId = $SeDet->se_id;
        //$data_json = TaggingMaster::whereRaw("TagId = '$TagId' and center_id='$Center_Id' and se_id='$SeId'  ")->first();
        $data_json = TaggingMaster::whereRaw("TagId = '$TagId' and center_id='$Center_Id' and se_id='$SeId'  ")->first();
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
        
        $qr9 = "SELECT * FROM `pincode_master` WHERE pin_status='1'";  
        $pincode_json           =   DB::select($qr9);
        
        $pincode = $data['Pincode'];
        $qr10 = "SELECT pin_id,place FROM pincode_master WHERE pincode='$pincode' order by place";
        $area_json           =   DB::select($qr10);
        
        $brand_master = $state_master = $pincode_arr = $sypt_master = $set_con_master = $acc_master = array();
          
        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }
        
        foreach($state_json as $state)
        {
            $state_master[$state->state_id] = $state->state_name;
        }
        
        foreach($pincode_json as $pin)
        {
            $pincode_arr[$pin->Pin_Id] = $pin->pincode;
        }
        
        $area_master = array();
        foreach($area_json as $area)
        {
            $area_master[$area->pin_id] = $area->place;
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
        
        $tagg_part = TagPart::whereRaw("tag_id='$TagId'")->get();
        //echo $_SERVER['APP_URL'];exit;
        $str_server = str_replace('public', '', $_SERVER['APP_URL']); 
        
        $url = $_SERVER['APP_URL'].'/se-job-view';
        //print_r($data); exit;
        return view('se-job-details')
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
                ->with('tagg_part',$tagg_part)
                ->with('back_url',$back_url)
                ->with('warranty_master',$warranty_master)
                ->with('ProductMaster',$ProductMaster);
    }

}

