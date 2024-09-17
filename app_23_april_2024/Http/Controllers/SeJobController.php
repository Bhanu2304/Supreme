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
use App\InventoryCenter;
use App\RegionalManagerMaster;
use App\InvPart;
use App\TagPart;
use App\BrandMaster;
use App\ProductCategoryMaster;
use App\ProductMaster;
use App\ModelMaster;
use App\JobSheet;
use App\ServiceEngineer;
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Facades\Excel;

class SeJobController extends Controller
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
    
    public function get_part_po_no($sr_no,$po_type)
    {
        $fin_year = date('Y/m/d');
        $part_po_no = "";
        if(empty($sr_no))
        {
            $sr_no = 1;
            $part_po_no = "$fin_year/".'0001';
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
            $part_po_no = "$fin_year/".$new_no;
        }
        
        $out_srno_det = TagPart::whereRaw("part_po_no='$part_po_no'")->first();
        //print_r($out_srno_det); exit;
        if(!empty($out_srno_det))
        {
            return $this->get_part_po_no($no,$po_type);
        }
        else
        {
            return array('part_po_no'=>$part_po_no,'sr_no'=>$no);
        }
    }
    
    public function index(Request $request)
    {
        Session::put("page-title","SE Job List");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $SeDet = ServiceEngineer::whereRaw("LogIn_Id='$UserId'")->first();
        $SeId = $SeDet->se_id;
        $Center_Id = Auth::user()->table_id;
        
        
        
        $warranty_category = $request->input('warranty_category');
        $service_type = $request->input('service_type');
        $job_status = $request->input('job_status');
        $pincode = $request->input('pincode');
        $job_no = $request->input('job_no');
        $ticket_no = $request->input('ticket_no');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        
        $whereTag = "";
        
        if(!empty($warranty_category))
        {
            $whereTag .= " and tm.warranty_category='$warranty_category'";
        }
        if(!empty($service_type))
        {
            $whereTag .= " and tm.service_type='$service_type'";
        }
        if(!empty($job_status))
        {
            $whereTag .= " and tm.job_status='$job_status'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.pincode='$pincode'";
        }
        if(!empty($job_no))
        {
            $whereTag .= " and tm.job_no='$job_no'";
        }
        if(!empty($ticket_no))
        {
            $whereTag .= " and tm.ticket_no='$ticket_no'";
        }
        
        if(!empty($contact_no))
        {
            $whereTag .= " and tm.contact_no='$contact_no'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.allocation_date) between '$from_date1' and '$to_date1'";
        }
        
        if(empty($whereTag))
        {
            $tag_data_qr = "select tm.*,sc.center_name,dm.dist_name from tagging_master tm inner join tbl_service_centre sc on tm.center_id = sc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_accept='1' and tm.center_id='$Center_Id' and se_id ='$SeId' and (job_status is null or job_status !='Close') ";  //exit;
        }
        else
        {
            $tag_data_qr = "select tm.*,sc.center_name,dm.dist_name from tagging_master tm inner join tbl_service_centre sc on tm.center_id = sc.center_id left join district_master dm on tm.dist_id = dm.dist_id where job_accept='1' and tm.center_id='$Center_Id' and se_id ='$SeId' and (job_status is null or job_status !='Close')   $whereTag";    //exit;
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/se-job-view';
        return view('se-job-view')
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
                ->with('back_url','se-job-view')
                ->with('whereTag',$whereTag); 
                
    }
    
    
    public function reject(Request $request)
    {   
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $TagId = $request->input('tagId'); 
        
        $taggingArr['job_reject']=1;
        $taggingArr['job_accept']='0';
        $taggingArr['job_reject_by']=$UserId;
        $taggingArr['job_reject_date']=date('Y-m-d H:i:s');
        $tagDet = TaggingMaster::whereRaw("TagId='$TagId'")->first();
        $job_no = $tagDet->job_no;
        //print_r($taggingArr); exit;
        
        if(TaggingMaster::whereRaw("TagId='$TagId' and job_reject='0' and se_id is null and observation is null")->update($taggingArr))
        {
            echo json_encode(array('resp_id'=>'1',"job_no"=>$job_no));exit;
        }
        else
        {
            echo json_encode(array('resp_id'=>'2',"job_no"=>""));exit;    
        }
        
    }
    
    public function save_shd(Request $request)
    {   
        $UserId = Session::get('UserId');
        //$Center_Id = Auth::user()->table_id;
        $se_sdl_date = $request->input('job_date'); 
        $job_hour = $request->input('job_hour'); 
        $job_minute = $request->input('job_minute'); 
        $job_remarks = $request->input('job_remarks'); 
        $TagId = $request->input('tagId'); 
        $tagDet = TaggingMaster::whereRaw("TagId='$TagId'")->first();
          
        $taggingArr['se_sdl_job']=1;
        $taggingArr['job_date']="$se_sdl_date $job_hour:$job_minute:00";
        $taggingArr['se_sdl_remarks']=$job_remarks;
        $taggingArr['se_sdl_date']=date('Y-m-d H:i:s');
        
        $history_json = $tagDet->se_sdl_history;
        $job_no = $tagDet->job_no;
        
        $history_arr = array();
        if(!empty($history_json))
        {
            $history_arr = json_decode($history_json,true);
        }
        
            $record_new = array();
            $record_new['job_date'] = "$se_sdl_date $job_hour:$job_minute:00";
            $record_new['se_sdl_remarks'] = $job_remarks;
            $record_new['se_sdl_date'] = date('Y-m-d H:i:s');
            $record_new['user'] = Auth::user()->name;
            $history_arr[] = $record_new;
        
        $taggingArr['se_sdl_history']=json_encode($history_arr);
        
        if(TaggingMaster::whereRaw("TagId='$TagId'")->update($taggingArr))
        {
            $resp_table = '';
            $bg_color = array('0'=>'#b9f2ff','1'=>'#008b8b');
            $index_bg = 0;
            
            foreach($history_arr as $his)
            {
                $index_bg++;
                $index_cl = $bg_color[$index_bg%2];
                $entry_date = strtotime($his['se_sdl_date']);
                $entry_date_str = date('d/m/Y',$entry_date);
                $entry_time_str = date('h:i A',$entry_date);

                $job_date = strtotime($his['job_date']);
                $job_date_str = date('d/m/Y',$job_date);
                $job_time_str = date('h:i A',$job_date);

                $user = $his['user'];
                $resp_table .= "<tr style=\"background:$index_cl;\"><td>";     
                     $resp_table .= "<b>$entry_date_str at $entry_time_str by $user --</b> Customer appointment";
                $resp_table .= '</td></tr>';
                $resp_table .= "<tr style=\"background:$index_cl;\"><td>";
                    $resp_table .= "reschedule for $job_date_str at $job_time_str";
                $resp_table .= '</td></tr>';
                $resp_table .= "<tr style=\"background:$index_cl;\"><td>";
                     $reason = $his['se_sdl_remarks'];
                     $resp_table .= "<b>Reason of Reschedule -- </b>$reason";
                $resp_table .= '</td></tr>';
            }?>
        <?php    echo json_encode(array('resp_id'=>'1',"job_no"=>$job_no,'resp_table'=>$resp_table));exit;
        }
        else
        {
            echo json_encode(array('resp_id'=>'2',"job_no"=>""));exit;
        }
        
    }

    public function save_del(Request $request)
    {
        $UserId = Session::get('UserId');
        //$Center_Id = Auth::user()->table_id;
        $se_del_date = $request->input('delivery_date'); 
        //$job_hour = $request->input('job_hour'); 
        //$job_minute = $request->input('job_minute'); 
        $delivery_remarks = $request->input('delivery_remarks'); 
        $TagId = $request->input('tagId'); 
        $tagDet = TaggingMaster::whereRaw("TagId='$TagId'")->first();
          
        $taggingArr['se_del_job']=1;
        $taggingArr['delivery_date']="$se_del_date";
        $taggingArr['se_del_remarks']=$delivery_remarks;
        $taggingArr['se_del_date']=date('Y-m-d H:i:s');
        
        $history_json = $tagDet->se_del_history;
        $job_no = $tagDet->job_no;
        
        $history_arr = array();
        if(!empty($history_json))
        {
            $history_arr = json_decode($history_json,true);
        }
        
            $record_new = array();
            $record_new['delivery_date'] = "$se_del_date";
            $record_new['se_del_remarks'] = $delivery_remarks;
            $record_new['se_del_date'] = date('Y-m-d H:i:s');
            $record_new['user'] = Auth::user()->name;
            $history_arr[] = $record_new;
        
        $taggingArr['se_del_history']=json_encode($history_arr);
        
        if(TaggingMaster::whereRaw("TagId='$TagId'")->update($taggingArr))
        {
            $resp_table = '';
            $bg_color = array('0'=>'#b9f2ff','1'=>'#008b8b');
            $index_bg = 0;
            
            foreach($history_arr as $his)
            {
                $index_bg++;
                $index_cl = $bg_color[$index_bg%2];
                $entry_date = strtotime($his['se_del_date']);
                $entry_date_str = date('d/m/Y',$entry_date);
                $entry_time_str = date('h:i A',$entry_date);

                $job_date = strtotime($his['delivery_date']);
                $job_date_str = date('d/m/Y',$job_date);
                //$job_time_str = date('h:i A',$job_date);

                $user = $his['user'];
                $resp_table .= "<tr style=\"background:$index_cl;\"><td>";     
                     $resp_table .= "<b>$entry_date_str at $entry_time_str by $user --</b> Delivery Date  $job_date_str";
                $resp_table .= '</td></tr>';
                // $resp_table .= "<tr style=\"background:$index_cl;\"><td>";
                //     $resp_table .= "Delivery Date $job_date_str";
                // $resp_table .= '</td></tr>';
                $resp_table .= "<tr style=\"background:$index_cl;\"><td>";
                     $reason = $his['se_del_remarks'];
                     $resp_table .= "<b>Remarks -- </b>$reason";
                $resp_table .= '</td></tr>';
            }?>
        <?php    echo json_encode(array('resp_id'=>'1',"job_no"=>$job_no,'resp_table'=>$resp_table));exit;
        }
        else
        {
            echo json_encode(array('resp_id'=>'2',"job_no"=>""));exit;
        }

    }
     
    public function save_followup(Request $request)
    {   
        $UserId = Session::get('UserId');
        $se_followup_sub = $request->input('se_followup_sub'); 
        $se_followup_remarks = $request->input('se_followup_remarks'); 
        $TagId = $request->input('tagId'); 
        $tagDet = TaggingMaster::whereRaw("TagId='$TagId'")->first();
        
        $taggingArr['se_followup_sub']="$se_followup_sub";
        $taggingArr['se_followup_remarks']=$se_followup_remarks;
        $taggingArr['se_follow_date']=date('Y-m-d H:i:s');
        
        $history_json = $tagDet->se_followup_history;
        $job_no = $tagDet->job_no;
        
        $followup_history_arr = array();
        if(!empty($history_json))
        {
            $followup_history_arr = json_decode($history_json,true);
        }
        
            $record_new = array();
            $record_new['se_followup_sub'] = $se_followup_sub;
            $record_new['se_followup_remarks'] = $se_followup_remarks;
            $record_new['se_follow_date'] = date('Y-m-d H:i:s');
            $record_new['user'] = Auth::user()->name;
            $followup_history_arr[] = $record_new;
            $taggingArr['se_followup_history']=json_encode($followup_history_arr);
        
            if(TaggingMaster::whereRaw("TagId='$TagId'   ")->update($taggingArr))
            {
                $resp_table = '';
                $bg_color = array('0'=>'#b9f2ff','1'=>'#008b8b');
                $index_bg = 0;

                $bg_color = array('0'=>'#b9f2ff','1'=>'#008b8b');
                $index_bg = 0;
                foreach($followup_history_arr as $his)
                {
                    $index_bg++;
                    $index_cl = $bg_color[$index_bg%2];
                    $entry_date = strtotime($his['se_follow_date']);
                    $entry_date_str = date('d/m/Y',$entry_date);
                    $entry_time_str = date('h:i A',$entry_date);
                    $subject = $his['se_followup_sub'];
                    $remark = $his['se_followup_remarks'];
                    $user = $his['user'];
                    $resp_table .= "<tr style=\"background:$index_cl;\"><td>";     
                    $resp_table .= "<b>$subject";
                    $resp_table .= '</td></tr>';
                    $resp_table .= "<tr style=\"background:$index_cl;\"><td>";
                    $resp_table .= "<b>$entry_date_str at $entry_time_str by $user --</b> Updated to customer";
                    $resp_table .= '</td></tr>';
                    $resp_table .= "<tr style=\"background:$index_cl;\"><td>";
                    $resp_table .= "$remark";
                    $resp_table .= '</td></tr>';
                }
                echo json_encode(array('resp_id'=>'1','resp_table'=>$resp_table));exit;
            }
            else
            {
                echo json_encode(array('resp_id'=>'2',"resp_table"=>""));exit;
            }
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
        $data_json = TaggingMaster::whereRaw("TagId = '$TagId'")->first();
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
                ->with('back_url',$back_url)
                ->with('tagg_part',$tagg_part)
                ->with('warranty_master',$warranty_master)
                ->with('ProductMaster',$ProductMaster);
    }
    
    public function observation(Request $request)
    {
        Session::put("page-title","Observation Entry");
        $TagId = base64_decode($request->input('TagId')); 
        $whereTag = base64_decode($request->input('whereTag')); 
        $Center_Id = Auth::user()->table_id;
        $UserId = Session::get('UserId');
        $SeDet = ServiceEngineer::whereRaw("LogIn_Id='$UserId'")->first();
        $SeId = $SeDet->se_id;
        
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
        
        $url = $_SERVER['APP_URL'].'/se-job-ob';
        //print_r($data); exit;
        return view('se-observation-entry')
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
            
                $part_required_arr = "";
                
            
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
            
            return redirect("se-job-view?TagId=$TagId&".$whereTag);
    }
    
    public function se_view_part_pending(Request $request)
    {
        Session::put("page-title","SE Part Pending");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $SeDet = ServiceEngineer::whereRaw("LogIn_Id='$UserId'")->first();
        $SeId = $SeDet->se_id;
        
        $Center_Id = Auth::user()->table_id;
        
       
        //$se_str = "se_id is null";
        
        $warranty_category = $request->input('warranty_category');
        $service_type = $request->input('service_type');
        $job_status = $request->input('job_status');
        $pincode = $request->input('pincode');
        $job_no = $request->input('job_no');
        $ticket_no = $request->input('ticket_no');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        $po_no = $request->input('po_no');
        $search = $request->input('search');
        $search_po = $request->input('search_po');
        $whereTag = "";
        
        if(!empty($warranty_category))
        {
            $whereTag .= " and tm.warranty_category='$warranty_category'";
        }
        if(!empty($service_type))
        {
            $whereTag .= " and tm.service_type='$service_type'";
        }
        if(!empty($job_status))
        {
            $whereTag .= " and tm.job_status='$job_status'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.pincode='$pincode'";
        }
        if(!empty($job_no))
        {
            $whereTag .= " and tm.job_no='$job_no'";
        }
        if(!empty($ticket_no))
        {
            $whereTag .= " and tm.ticket_no='$ticket_no'";
        }
        if(!empty($po_no))
        {
            $whereTag .= " and tsp.part_po_no='$po_no'";
        }
        
        if(!empty($contact_no))
        {
            $whereTag .= " and tm.contact_no='$contact_no'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.allocation_date) between '$from_date1' and '$to_date1'";
        }
        
        if(empty($whereTag) && empty($search))
        {
            $tag_data_qr = "SELECT tsp.*,tm.*,tsp.part_name,tsc.center_name FROM `tagging_spare_part` tsp
            INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
            INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
            WHERE tsp.center_id='$Center_Id' and tm.se_id='$SeId' and tsp.pending_status='1' and tsp.part_po_no is null";  //exit;
        }
        else 
        {
            $tag_data_qr = "SELECT tsp.*,tm.*,tsp.part_name,tsc.center_name FROM `tagging_spare_part` tsp
            INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
            INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
            WHERE tsp.center_id='$Center_Id' and tm.se_id='$SeId' and tsp.pending_status='1' and tsp.part_po_no is null $whereTag";    //exit;
        }
       
        $DataArr = DB::select($tag_data_qr); 
        
        $whereTag2 = "";
        if($search_po=='Search')
        {
            $whereTag2 = $whereTag;
        }
        $po_data_qr = "SELECT tsp.*,tm.*,tsp.part_name,tsc.center_name,tsp.part_status FROM `tagging_spare_part` tsp
            INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
            INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
            WHERE tsp.center_id='$Center_Id' and tm.se_id='$SeId' and tsp.pending_status='1' and tsp.part_po_no is not null $whereTag2";  //exit;
        
        $po_data_arr = DB::select($po_data_qr);
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/se-po-raise';
        return view('se-raise-po')
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
                ->with('po_data_arr', $po_data_arr)
                ->with('back_url','se-raise-po')
                ->with('whereTag',$whereTag); 
                
    }
    
    
    public function raise_part_po(Request $request)
    {
        $updated_by     =   Auth::User()->id;
        $updated_at     =   date('Y-m-d H:i:s');
        $po_date = date('Y-m-d');
        
        //$Center_Id = Auth::user()->table_id;
        $part_id =  $request->input('part_id');
        $po_type = addslashes($request->input('po_type'));
        $color = addslashes($request->input('color'));
        $pending_parts = addslashes($request->input('pending_parts'));
        $remarks = addslashes($request->input('remarks'));
        
        $part_det = TagPart::whereRaw("part_id='$part_id' and delete_status='0'")->first();
        $tag_id = $part_det->tag_id;
        
        DB::beginTransaction();
        $job_status = "";
        $job_remarks = "";
        
        $sr_no = 0; $srno = '';
        $part_max_srno = DB::select("select max(sr_no) srno from tagging_spare_part where  date(created_at)=curdate()");
        
        foreach($part_max_srno as $mxno)
        {
            $sr_no = $mxno->srno;
        }
        
        $sr_arr = $this->get_part_po_no($sr_no,$po_type);
        $part_po_no = $sr_arr['part_po_no'];
        $sr_no =  $sr_arr['sr_no'];
        //print_r($sr_arr);exit;
        if(TagPart::whereRaw("part_id='$part_id'")->update(
                array('sr_no'=>$sr_no,
                    'part_po_no'=>$part_po_no,
                    'part_po_date'=>$po_date,
                    'pending_parts'=>$pending_parts,
                    'pending_status'=>'1',
                    'part_status'=>'pending',
                    'color'=>$color,
                    'po_type'=>$po_type,
                    'remarks'=>$remarks,
                    'updated_by'=>$updated_by,
                    'updated_at'=>$updated_at)))
        {
            $tag_det = TaggingMaster::whereRaw("TagId='$tag_id'")->first();
            $part_pen =(int) $tag_det->part_pending;
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

                        if($InvPart->save())
                        {
                            $jobsheet_appyly= false;
                            if($new_pending_parts==0)
                            {
                                if(TagPart::whereRaw("part_id='$part_id'")->update(array("delete_status='1'")))
                                {
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
                    //part not availabe in HO.
                }
                
                
                $job_status = "1";
                $job_remarks = "PO $part_po_no Generated Successfully.";
            }
            else
            {
                DB::rollback();
                $job_status = "2";
                $job_remarks = "PO Generation Failed.";
            }
        }
        else
        {
            DB::rollback();
            $job_status = "2";
            $job_remarks = "PO Generation Failed.";
        }
        echo json_encode(array('status'=>$job_status,'job_remarks'=>$job_remarks)); exit;
    }
    
    public function get_raise_po(Request $request)
    {
        $TagId = addslashes($request->input('TagId'));
        $po_raise = addslashes($request->input('po_raise'));
        if($po_raise=='1')
        {
            $tag_data_qr = "SELECT tsp.*,tm.*,tsp.part_name,tsc.center_name FROM `tagging_spare_part` tsp
            INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
            INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
            WHERE tsp.tag_id='$TagId'  and tsp.estmt_approve='1'  and delete_status='0'";  //exit;
               
        $DataArr = DB::select($tag_data_qr);
        
        $lab_data_qr = "SELECT tsp.*,tm.*,tsp.symptom_type,tsp.symptom_name,tsc.center_name FROM `tagging_labour_part` tsp
            INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
            INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
            WHERE tsp.tag_id='$TagId'   and tsp.estmt_approve='1'  and delete_status='0'";  //exit;
               
        $DataArr = DB::select($tag_data_qr);
        $DataArr2 = DB::select($lab_data_qr);
        }
        
        $po_inv_qry = "SELECT tsp.*,tm.*,tsp.part_name,tsc.center_name,tsp.part_status FROM `tbl_inventory_part` tsp
            INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
            INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
            WHERE tsp.tag_id='$TagId'  and tsp.part_po_no is not null and srn_status='0'";         
        $po_inv_arr = DB::select($po_inv_qry); 
        
        /*$po_data_qry = "SELECT tsp.*,tm.*,tsp.part_name,tsc.center_name,tsp.part_status FROM `tagging_spare_part` tsp
            INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
            INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
            WHERE tsp.tag_id='$TagId'  and tsp.part_po_no is not null ";         
        $po_data_arr = DB::select($po_data_qry); 
        
        $po_inv_qry = "SELECT tsp.*,tm.*,tsp.part_name,tsc.center_name,tsp.part_status FROM `tbl_inventory_part` tsp
            INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
            INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
            WHERE tsp.tag_id='$TagId'  and tsp.part_po_no is not null ";         
        $po_inv_arr = DB::select($po_inv_qry); */
        
        
        if(!empty($po_data_arr) || !empty($po_inv_arr)) {
    ?>    
    <table id="table_view" border="1">
                              <thead>
                                 <tr>
                                    <th>Sr.</th>
                                    <th>PO No.</th>
                                    <th>PO Date</th>
                                    <th>Job ID</th>
                                    
                                    <th>Center</th>
                                    <th>Cust. Gr.</th>
                                    <th>Name</th>
                                    <th>State</th>
                                    <th>Mobile No.</th>
                                    <th>Pincode</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Part Code</th>
                                    <th>Part Name</th>
                                    <th>PO Type</th>
                                    <th>Color</th>
                                    <th>Part Pending</th>
                                    <th>Remarks</th>
                                    <th> Status</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        /*foreach($po_data_arr as $record)
                                        {
                                            echo '<tr id="tr'.$record->part_id.'">'; 
                                            echo '<td>';echo $srno++.'</td>';
                                            echo '<td>'.$record->part_po_no.'</td>';
                                            echo '<td>'.$record->part_po_date.'</td>';
                                                echo '<td>'.$record->job_no.'</td>';
                                               
                                                echo '<td>'.$record->center_name.'</td>';
                                                echo '<td>'.$record->Customer_Group.'</td>';
                                                echo '<td>'.$record->Customer_Name.'</td>';
                                                
                                                echo '<td>'.$record->State.'</td>';
                                                echo '<td>';
                                                //echo '<a href="tel:'.$record->Contact_No.'">'.$record->Contact_No;
                                                echo $record->Contact_No;
                                                echo '</td>';
                                                echo '<td>'.$record->Pincode.'</td>';
                                                echo '<td>'.$record->Brand.'</td>';
                                                echo '<td>'.$record->Model.'</td>';
                                                echo '<td>'.$record->part_no.'</td>';
                                                echo '<td>'.$record->part_name.'</td>';
                                                echo '<td>'.$record->po_type.'</td>';
                                                echo '<td>'.$record->color.'</td>';
                                                echo '<td>'.$record->pending_parts.'</td>';
                                                echo '<td>'.$record->remarks.'</td>';
                                                echo '<td>'.$record->part_status.'</td>';
                                                echo '<td><a href="#" onclick="raise_po('."'".$record->part_id."'".')">Return</td>';
                                            echo '</tr>';
                                        }*/
                                        
                                       /* foreach($po_inv_arr as $record)
                                        {
                                            echo '<tr>'; 
                                            echo '<td>';echo $srno++.'</td>';
                                            echo '<td>'.$record->part_po_no.'</td>';
                                            echo '<td>'.$record->part_po_date.'</td>';
                                                echo '<td>'.$record->job_no.'</td>';
                                               
                                                echo '<td>'.$record->center_name.'</td>';
                                                echo '<td>'.$record->Customer_Group.'</td>';
                                                echo '<td>'.$record->Customer_Name.'</td>';
                                                
                                                echo '<td>'.$record->State.'</td>';
                                                echo '<td>';
                                                //echo '<a href="tel:'.$record->Contact_No.'">'.$record->Contact_No;
                                                echo $record->Contact_No;
                                                echo '</td>';
                                                echo '<td>'.$record->Pincode.'</td>';
                                                echo '<td>'.$record->Brand.'</td>';
                                                echo '<td>'.$record->Model.'</td>';
                                                echo '<td>'.$record->part_no.'</td>';
                                                echo '<td>'.$record->part_name.'</td>';
                                                echo '<td>'.$record->po_type.'</td>';
                                                echo '<td>'.$record->color.'</td>';
                                                echo '<td>'.$record->part_allocated.'</td>';
                                                
                                                echo '<td>'.$record->remarks.'</td>';
                                                echo '<td>Allocated</td>';
                                                //echo '<td><a href="#" onclick="raise_po('."'".$record->part_id."'".')">Raise PO</td>';
                                            echo '</tr>';
                                        }*/
                                 ?>
                              </tbody>
                           </table>


        <?php } if(!empty($DataArr) || !empty($po_inv_arr) || !empty($DataArr2)) { ?>
<br><br><br>

    <table id="table_id" border="1">
                              <thead>
                                 <tr>
                                    <th>Sr.</th>
                                    <th>Part Name</th>
                                    <th>Part Code</th>
                                    <th>Quantity </th>
                                    <th>Color</th>
                                    <th>Part Type</th>
                                    <th>Part Availability in ASC Stock</th>
                                    <th>PO No.</th>
                                    <th>PO Status</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                 //print_r($DataArr);
                                        foreach($DataArr as $record)
                                        {
                                            echo '<tr id="tr'.$record->part_id.'">';
                                            echo '<td>';
                                            echo $srno++.'</td>';
                                            echo '<td>'.$record->part_name.'</td>';
                                            echo '<td>'.$record->part_no.'</td>';
                                            echo '<td>'.$record->pending_parts.'</td>';
                                            echo '<td>'.$record->color.'</td>';
                                            echo '<td>'.$record->charge_type.'</td>';
                                            $center_id = $record->center_id;
                                            $spare_id = $record->spare_id;
                                            $pending_parts = $record->pending_parts;
                                            echo '<td>';
                                            if(InventoryCenter::whereRaw("center_id='$center_id' and spare_id='$spare_id' and avail_qty>='$pending_parts'")->first())
                                            {
                                                echo 'Available';
                                            }
                                            else
                                            {
                                                echo 'Pending For Order';
                                            }
                                            echo '</td>';
                                            //echo '<td>'.$record->status.'</td>';
                                                if($record->apply == '1')
                                                {
                                                 echo '<td>'.$record->part_po_no.'</td>';
                                                }else{
                                                    echo '<td></td>';
                                                }
                                                echo '<td>'.'Generated'.'</td>';
                                                if($record->apply == '0')
                                                {
                                                    echo '<td id="td'.$record->part_id.'"><a href="#" class="srn_return_class" onclick="return order_apply('."'".$record->part_id."'".')">Apply</td>';
                                                    
                                                }else{
                                                    echo '<td><a href="#" class="srn_return_class" onclick="return return_srn_fun('."'".$record->part_id."'".')">Return</td>';
                                                }
                                                
                                                
                                                //echo '<td></td>';
                                           
                                            echo '</tr>';
                                            }
                                            //print_r($DataArr2);
                                            foreach($DataArr2 as $record)
                                            {
                                                echo '<tr id="tr'.$record->tlp_id.'">';
                                                echo '<td>';
                                                echo $srno++.'</td>';
                                                echo '<td>'.$record->symptom_type.'</td>';
                                                echo '<td>'.$record->symptom_name.'</td>';
                                                echo '<td>'.$record->pending_parts.'</td>';
                                                echo '<td>'.$record->color.'</td>';
                                                echo '<td>'.$record->charge_type.'</td>';
                                                //$center_id = $record->center_id;
                                                //$spare_id = $record->spare_id;
                                                //$pending_parts = $record->pending_parts;
                                                echo '<td>';
                                                echo '</td>';
                                                echo '<td>';
                                                echo '</td>';
                                                //echo '<td>'.$record->status.'</td>';
                                                //echo '<td>'.$record->part_po_no.'</td>';
                                                    echo '<td>'.'Generated'.'</td>';
                                                    //echo '<td><a href="#" class="srn_return_class" onclick="return return_srn_fun('."'".$record->part_id."'".')">Return</td>';
                                                    echo '<td></td>';
                                                    ?>
                                                    
                                                    
                                                    <?php 
                                                    
                                                    
                                                echo '</tr>';
                                            }
                                        foreach($po_inv_arr as $record)
                                        {
                                            echo '<tr id="tr'.$record->part_id.'">';
                                            echo '<td>';
                                            echo $srno++.'</td>';
                                            echo '<td>'.$record->part_name.'</td>';
                                            echo '<td>'.$record->part_no.'</td>';
                                            echo '<td>'.$record->part_allocated.'</td>';
                                            echo '<td>'.$record->color.'</td>';
                                            echo '<td>'.$record->charge_type.'</td>';
                                            echo '<td>'.$record->status.'</td>';
                                            echo '<td>'.$record->part_po_no.'</td>';
                                                echo '<td>Generated</td>';
                                                
                                                //echo '<td><a href="#" class="srn_return_class" onclick="return_srn_fun('."'".$record->part_allocate_id."'".')">Return</td>';
                                                ?>

                                                <?php 
                                                
                                            echo '</tr>';
                                        }
                                 ?>
                              </tbody>
                           </table>    

        
        <?php  }  exit;        
    }
    
    public function return_part_srn(Request $request)
    {
        //print_r($request->all());die;
        $updated_by     =   Auth::User()->id;
        $updated_at     =   date('Y-m-d H:i:s');
        $return_date = date('Y-m-d');
        
        $part_id =  $request->input('srn_part_id');
        $remarks = addslashes($request->input('remarks_return'));
        $srn_type = addslashes($request->input('srn_type'));
        
        if(!empty($srn_type))
        {
            
        }
        
        $part_det = InvPart::whereRaw("part_allocate_id='$part_id' and srn_status='0'")->first();
        $tag_id = $part_det->tag_id;
        
        DB::beginTransaction();
        
        
        
        
        
        
        
        //print_r($sr_arr);exit;
        if(InvPart::whereRaw("part_allocate_id='$part_id'")->update(
                array('srn_type'=>$srn_type,
                    'srn_remarks'=>$remarks,
                    'srn_status'=>'1',
                    'srn_date'=>$updated_at,
                    'updated_by'=>$updated_by,
                    'updated_at'=>$updated_at)))
        {
            DB::commit();
            echo '1';exit;
        }
        else
        {
            DB::rollback();
            echo '0';exit;
        }
        
    }

    public function accept_order(Request $request)
    {   
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $part_id = $request->input('part_id'); 

        $taggingArr['apply']=1;
    
        if(TagPart::whereRaw("part_id='$part_id'")->update($taggingArr))
        {
            echo json_encode(array('resp_id'=>'1',"part_id"=>$part_id));exit;
        }
        else
        {
            echo json_encode(array('resp_id'=>'2',"part_id"=>""));exit;
        }
        
    }
    
}

