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

class AllocationManagementController extends Controller
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
    

    public function index(Request $request)
    {
        Session::put("page-title","ASC Reservation Allocation");
        
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
        $region_id = $request->input('region_id');
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        $cust_add = $request->input('cust_add');
        $asc_code = $request->input('asc_code');
        $job_no = $request->input('job_no');
        $ticket_no = $request->input('ticket_no');
        $warranty_category = $request->input('warranty_category');
        $service_type = $request->input('service_type');
        $submit = $request->input('submit');
        
        $whereTag = "";
        
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and tm.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.Pincode = '$pincode'";
        }
        if(!empty($cust_add))
        {
            $whereTag .= " and tm.Customer_Address = '$cust_add'";
        }
        if(!empty($contact_no) && strlen($contact_no)>6)
        {
            $whereTag .= " and tm.contact_no='$contact_no'";
        }
        if(!empty($asc_code) && $asc_code!='All')
        {
            $whereTag .= " and tm.center_id = '$asc_code'";
        }
        if(!empty($region_id) && $region_id!='All')
        {
            $whereTag .= " and tm.region_id = '$region_id'";
        }
        if(!empty($job_no))
        {
            $whereTag .= " and tm.job_no = '$job_no'";
        }
        if(!empty($ticket_no))
        {
            $whereTag .= " and tm.ticket_no = '$ticket_no'";
        }
        if(!empty($warranty_category))
        {
            $whereTag .= " and tm.warranty_category = '$warranty_category'";
        }
        if(!empty($service_type))
        {
            $whereTag .= " and tm.service_type = '$service_type'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        
        
        
        if(empty($submit)) 
        {
           // $tag_data_qr = "select tm.* from $from_table tagging_master tm $on_table where job_reject='0' and se_id is null $whereUser  and observation is null and date(tm.created_at)=curdate();"; 
        }
        else
        {
           $tag_data_qr = "select tm.*,center_name from $from_table tagging_master tm $on_table left join tbl_service_centre  tsc on tm.center_id=tsc.center_id where job_reject='0' and se_id is null $whereUser  and observation is null  $whereTag";
           $DataArr = DB::select($tag_data_qr); 
        }
        
        
        //print_r($tag_data); exit;
        $whereTag1 = base64_encode(http_build_query($request->all()));
        
        $region_Qry = "SELECT * FROM `region_master` ORDER BY region_name";
        $region_list = DB::select($region_Qry);
        
        $qr2 = "SELECT tsc.center_id,center_name,asc_code FROM tbl_service_centre  tsc
INNER JOIN users us ON tsc.email_id = us.email
WHERE sc_status='1' $center_qr order by center_name"; 
        $asc_master           =   DB::select($qr2); 
        
        if($submit=='Download Report' || $submit=='Download In Excel')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=dash-report.xls");
            header("Pragma: no-cache");
            header("Expires: 0"); 
            ?>
            <table border="2">
              <thead>
                 <tr>
                    <thead>
                     <tr>
                        <th>Sr.</th>
                        <th>Ticket No.</th>
                        <th>Action</th>
                        <th>Cust. Gr.</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>State</th>
                        <th>Mobile No.</th>
                        <th>Pincode</th>
                        <th>Product</th>
                     </tr>
                  </thead>
                 </tr>
              </thead>
              <tbody>

                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            echo '<tr>';
                                            echo '<td>';
                                            echo $srno++.'</td>';
                                                echo '<td>'.$record->ticket_no.'</td>';
                                                echo '<td id="td'.$record->TagId.'">';
                                                if($record->job_accept=='1')
                                                {
                                                    echo 'Accepted';
                                                }
                                                else if($record->job_accept=='0')
                                                {
                                                    echo 'Pending';
                                                }
                                                else if($record->job_accept=='2')
                                                {
                                                    echo 'Cancel';
                                                }
                                                echo '</td>';
                                                
                                                echo '<td>'.$record->Customer_Group.'</td>';
                                                echo '<td>'.$record->Customer_Name.'</td>';
                                                echo '<td>'.$record->Customer_Address.'</td>';
                                                echo '<td>'.$record->State.'</td>';
                                                echo '<td>'.$record->Contact_No.'</td>';
                                                echo '<td>'.$record->Pincode.'</td>';
                                                echo '<td>'.$record->Product.'</td>';
                                                
                                            echo '</tr>'; 
                                        }
                 ?>
              </tbody>
            </table>
       <?php     exit;
        }
        
        
        
        //print_r($region_list); exit; 
        $url = $_SERVER['APP_URL'].'/ho-alloc-view';
        return view('ho-alloc-view')
            ->with('pin_master',$pin_master)
                ->with('region_list', $region_list)
                ->with('region_id', $region_id)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('asc_master', $asc_master)
                ->with('pincode',$pincode)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('service_type',$service_type)
                ->with('DataArr',$DataArr)

                ->with('back', 'ho-alloc-view')
                ->with('whereTag',$whereTag1); 
                
    }
    
    public function se_view(Request $request)
    {
        Session::put("page-title","Allocate to Engineer");
        
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
        $region_id = $request->input('region_id');
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        $cust_add = $request->input('cust_add');
        $asc_code = $request->input('asc_code');
        $job_no = $request->input('job_no');
        $ticket_no = $request->input('ticket_no');
        $warranty_category = $request->input('warranty_category');
        $service_type = $request->input('service_type');
        $submit = $request->input('submit');
        
        $whereTag = "";
        
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and tm.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.Pincode = '$pincode'";
        }
        if(!empty($cust_add))
        {
            $whereTag .= " and tm.Customer_Address = '$cust_add'";
        }
        if(!empty($contact_no) && strlen($contact_no)>6)
        {
            $whereTag .= " and tm.contact_no='$contact_no'";
        }
        if(!empty($asc_code) && $asc_code!='All')
        {
            $whereTag .= " and tm.center_id = '$asc_code'";
        }
        if(!empty($region_id) && $region_id!='All')
        {
            $whereTag .= " and tm.region_id = '$region_id'";
        }
        if(!empty($job_no))
        {
            $whereTag .= " and tm.job_no = '$job_no'";
        }
        if(!empty($ticket_no))
        {
            $whereTag .= " and tm.ticket_no = '$ticket_no'";
        }
        if(!empty($warranty_category))
        {
            $whereTag .= " and tm.warranty_category = '$warranty_category'";
        }
        if(!empty($service_type))
        {
            $whereTag .= " and tm.service_type = '$service_type'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        
        
        
        if(empty($submit)) 
        {
           // $tag_data_qr = "select tm.* from $from_table tagging_master tm $on_table where job_reject='0' and se_id is null $whereUser  and observation is null and date(tm.created_at)=curdate();"; 
        }
        else
        {
           $tag_data_qr = "select tm.*,center_name,se_name from $from_table tagging_master tm $on_table left join tbl_service_centre  tsc on tm.center_id=tsc.center_id inner join tbl_service_engineer se on tm.se_id = se.se_id where job_reject='0' and se.se_id is not null $whereUser  and observation is null  $whereTag";
           $DataArr = DB::select($tag_data_qr); 
        }
        
        
        //print_r($tag_data); exit;
        $whereTag1 = base64_encode(http_build_query($request->all()));
        
        $region_Qry = "SELECT * FROM `region_master` ORDER BY region_name";
        $region_list = DB::select($region_Qry);
        
        $qr2 = "SELECT tsc.center_id,center_name,asc_code FROM tbl_service_centre  tsc
INNER JOIN users us ON tsc.email_id = us.email
WHERE sc_status='1' $center_qr order by center_name"; 
        $asc_master           =   DB::select($qr2); 
        
        $qr2 = "SELECT * FROM `tbl_service_engineer` WHERE se_status='1' $center_qr $whereSe";
        $se_arr           =   DB::select($qr2); 
        
        if( $submit=='Download In Excel')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=dash-report.xls");
            header("Pragma: no-cache");
            header("Expires: 0"); 
            ?>
            <table border="2">
              <thead>
                 <tr>
                    <thead>
                     <tr>
                        <th>Sr.</th>
                        <th>Ticket No.</th>
                        <th>Job No.</th>
                        <th>Center</th>
                        <th>SE</th>
                        <th>Cust. Gr.</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>State</th>
                        <th>Mobile No.</th>
                        <th>Pincode</th>
                        <th>Product</th>
                     </tr>
                  </thead>
                 </tr>
              </thead>
              <tbody>

                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            echo '<tr>';
                                            echo '<td>';
                                            echo $srno++.'</td>';
                                                echo '<td>'.$record->ticket_no.'</td>';
                                                echo '<td>'.$record->job_no.'</td>';
                                                echo '<td>'.$record->center_name.'</td>';
                                                echo '<td>'.$record->se_name.'</td>';
                                                echo '<td>'.$record->Customer_Group.'</td>';
                                                echo '<td>'.$record->Customer_Name.'</td>';
                                                echo '<td>'.$record->Customer_Address.'</td>';
                                                echo '<td>'.$record->State.'</td>';
                                                echo '<td>'.$record->Contact_No.'</td>';
                                                echo '<td>'.$record->Pincode.'</td>';
                                                echo '<td>'.$record->Product.'</td>';
                                                
                                            echo '</tr>'; 
                                        }
                 ?>
              </tbody>
            </table>
       <?php     exit;
        }
        
        
        //print_r($asc_master);
        //print_r($region_list); exit; 
        $url = $_SERVER['APP_URL'].'/ho-alloc-se-view';
        return view('ho-alloc-se-view')
            ->with('pin_master',$pin_master)
                ->with('region_list', $region_list)
                ->with('region_id', $region_id)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('asc_master', $asc_master)
                ->with('pincode',$pincode)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('service_type',$service_type)
                ->with('DataArr',$DataArr)
                ->with('se_arr',$se_arr)
                ->with('url', $url)
                ->with('back', 'ho-alloc-se-view')
                ->with('whereTag',$whereTag1); 
                
    }
    
    public function view_ob(Request $request)
    {
        Session::put("page-title","View Job Case");
        $TagId = $request->input('TagId'); 
        $whereTag = $request->input('whereTag');
        $back = $request->input('back');
        
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
        $part_arr           =   DB::select("SELECT spare_id,part_no FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_status='1' ");
        
        
        
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
        
        $TagPart_arr = TagPart::whereRaw("Tag_Id='$TagId'")->get();
        $TagPart = array();
        foreach($TagPart_arr as $part_tag)
        {
            $part_no = $part_tag->part_no;
            $part_name_arr = DB::select("SELECT part_name FROM `tbl_spare_parts` WHERE model_id='$model_id' and part_no='$part_no'");
            $part_tag->part_name_arr = $part_name_arr;
            $TagPart[] = $part_tag;
        }
        
        $url = $_SERVER['APP_URL'].'/ho-tag-view';
        //print_r($data); exit;
        return view('ho-tag-view')
                ->with('data',$data)
                ->with('data',$data)
                ->with('TagPart',$TagPart)
                ->with('whereTag',$whereTag)
                ->with('back', $back)
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

    public function view_ob_cl(Request $request)
    {
        Session::put("page-title","View Job Case");
        $TagId = $request->input('TagId'); 
        $whereTag = $request->input('whereTag');
        $back = $request->input('back');
        
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
        $qr6 = "SELECT model_id,model_name FROM `model_master` WHERE brand_id='$brand_id' and product_id='$product_id' and model_status='1'";
        $model_json           =   DB::select($qr6);
        
        
        
        $model_id = $data['model_id'];
        //echo "SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_status='1'";exit;
        $part_arr           =   DB::select("SELECT spare_id,part_no FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_status='1' ");
        
        
        
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

        // $product_id = $data['product_id'];
        // $qry = "SELECT mm.model_id,mm.model_name FROM model_master mm 
        //     INNER JOIN product_master pm ON mm.product_id = pm.product_id AND pm.product_status='1' AND mm.model_status='1'
        //     WHERE  mm.product_id='$product_id'";
        // $model_master = DB::select($qry);


        
        
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

        $qr8 = "SELECT product_id,product_name  FROM  product_master where brand_id='4' and product_status='1' ";
        $clarion_json           =   DB::select($qr8); 

        $clarion_product_master = $reg_master = array();

        foreach($reg_json as $reg)
        {
            $reg_master[$reg->region_id] = $reg->region_name; 
        }

        foreach($clarion_json as $brand)
        {
            $clarion_product_master[$brand->product_id] = $brand->product_name;
        }
        
        $TagPart_arr = TagPart::whereRaw("Tag_Id='$TagId'")->get();
        $TagPart = array();
        foreach($TagPart_arr as $part_tag)
        {
            $part_no = $part_tag->part_no;
            $part_name_arr = DB::select("SELECT part_name FROM `tbl_spare_parts` WHERE model_id='$model_id' and part_no='$part_no'");
            $part_tag->part_name_arr = $part_name_arr;
            $TagPart[] = $part_tag;
        }
        #print_r($model_master);die;
        $url = $_SERVER['APP_URL'].'/ho-tag-view-cl';
        //print_r($data); exit;
        return view('ho-tag-view-cl')
                ->with('data',$data)
                ->with('data',$data)
                ->with('TagPart',$TagPart)
                ->with('whereTag',$whereTag)
                ->with('back', $back)
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
                ->with('reg_master',$reg_master)
                ->with('clarion_product_master',$clarion_product_master)
                ->with('ProductMaster',$ProductMaster);
    }
    
    public function allocate_se(Request $request)
    {
        $se_id = $_POST['se_id'];
        $se_no = $_POST['se_no'];
        $case_id = $_POST['case_id'];
        
        $whereTag = base64_decode($request->input('whereTag')); 
        $UserId = Session::get('UserId');
       
        if(empty($case_id))
        {
            echo "Please Select Case First";exit;
        }
        
        $allocation_date = date('Y-m-d H:i:s');
        
        if($se_no=='1')
        {
            if(TaggingMaster::whereRaw("TagId='$case_id' and se_id is null and observation is null")
                    ->update(
                    array('se_id'=>$se_id,
                'allocation_date'=>$allocation_date,
                'allocate_by'=>$UserId)))
            {
                echo "Case Allocated To Service Engineer"; exit;
            }
            else
            {
                echo "Case Allready Allocated"; exit;
            }
        }
        else
        {
           if(TaggingMaster::whereRaw("TagId='$case_id' and se_id is not null and observation is null")
                    ->update(
                    array('se_id'=>$se_id,
                'allocation_date'=>$allocation_date,
                'allocate_by'=>$UserId)))
            {
                echo "Case Reallocate To Service Engineer"; exit;
            }
            else
            {
                echo "Case Allready Reallocated "; exit;
            }
        }
            
        
        
        exit;
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

