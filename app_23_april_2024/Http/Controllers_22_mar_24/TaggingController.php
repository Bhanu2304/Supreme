<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use DB;
use Auth;
use Session;
use App\User;
use App\TagImage;
use App\SCPinMaster;
use App\BrandMaster;
use App\RegionMaster;
use App\StateMaster;
use App\ModelMaster;
use App\ServiceCenter;
use App\TaggingMaster;
use App\PincodeMaster;
use App\ProductMaster;
use App\SCProductMaster;
use App\ProductCategoryMaster;
use Illuminate\Support\Facades\Storage;

class TaggingController extends Controller
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
    
    public function get_ticket_date(Request $request)
    {

        $ticket_no = $request->input("ticket_no");
        $ser = TaggingMaster::whereRaw("ticket_no='$ticket_no'")->first();
        echo $ser->created_at;exit;
        
        exit;
    }
    
    public function allocate_center($Brand,$Product_Detail,$Product,$Model,$pincode,$sc_id)
    {
        $center_qry = "";
        if(!empty($sc_id))
        {
            $sc_id_str = implode(",",$sc_id);
            $center_qry = " and center_id not in ($sc_id_str)";
        }
        //echo "brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model' and UserActive='1' $center_qry"; exit;
        $map_product_exist = SCProductMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model' and UserActive='1' $center_qry")->first();
        $center_id = $map_product_exist->center_id; 
        
        
        if(!empty($center_id))
        {
            $pincode_exist = SCPinMaster::whereRaw("pincode='$pincode' and UserActive='1' and center_id='$center_id' $center_qry")->first();
            if(!empty($pincode_exist))
            {
                $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
                if(empty($center_det))
                {
                    $sc_id[] = $center_id;
                    return $this->allocate_center($Brand,$Product_Detail,$Product,$Model,$pincode,$sc_id);
                }
                else
                {
                    return $center_det;
                }
            }
            else
            {
                $sc_id[] = $center_id;
                return $this->allocate_center($Brand,$Product_Detail,$Product,$Model,$pincode,$sc_id);
            } 
        }
        else
        {
            return array();
        }
    }
    
    public function index(Request $request)
    {
        Session::put("page-title","Tagging Master");
        $mobile = $request->input('Contact_No');
        $data_json           =   TaggingMaster::whereRaw("Contact_No='$mobile'")->get(); 
        $data = json_decode($data_json,true);
        $random_no = 'R'.rand(100000,999999);
        $url = $_SERVER['APP_URL'].'/tagging-master';
        
        return view('tagging-details')
                ->with('DataArr', $data)
                ->with('TagId', $random_no)
                ->with('contact_no', $mobile)
                ->with('url', $url); 
    }
    
   /* public function walking(Request $request)
    {
        $mobile = $request->input('Contact_No');
        $data_json           =   TaggingMaster::whereRaw("Contact_No='$mobile'")->get(); 
        $data = json_decode($data_json,true);
        
        $url = $_SERVER['APP_URL'].'/walking-master';
        
        return view('walking-details')
                ->with('DataArr', $data)
                ->with('contact_no', $mobile)->with('url', $url); 
    }*/
    
    public function tagging_data(Request $request)
    {
     
        Session::put("page-title","Tagging Master");
        
        $center_id=Auth::user()->table_id;
        $UserType = Session::get('UserType');
        $TagId = $request->input('TagId');
        //$entry_type = $request->input('entry_type');
        
        
        $whereTag = "";
        if($UserType=='ServiceCenter')
        {
            $whereTag .= " and center_id ='$center_id'";
        }
        
        $qr1 = "SELECT state_id,state_name FROM state_master st order by state_name";
        $state_json           =   DB::select($qr1); 
 
        $qr2 = "SELECT brand_id,brand_name  FROM  brand_master where brand_name!='Clarion' and brand_status='1' ";
        $brand_json           =   DB::select($qr2); 
               
        $qr3 = "SELECT field_name,field_code FROM `tbl_symptom` where sypt_status='1' ";
        $symptom_json           =   DB::select($qr3);
        
        $qr4 = "SELECT field_name,sub_field_name FROM `condition_master` 
WHERE con_status='1'
order BY field_name,sub_field_name";
        $con_json           =   DB::select($qr4);
        
        $qr5 = "SELECT Acc_Id,acc_name FROM `tbl_accessories` WHERE acc_status='1'";
        $acc_json           =   DB::select($qr5);
        
        $qr6 = "SELECT asc_code FROM `tbl_service_centre` WHERE sc_status='1' $whereTag";
        $asc_json           =   DB::select($qr6);
        
        $qr7 = "SELECT region_id,region_name FROM region_master WHERE region_status='1' order by region_name";
        $reg_json           =   DB::select($qr7);
        
        $qr8 = "SELECT product_id,product_name  FROM  product_master where brand_id='4' and product_status='1' ";
        $clarion_json           =   DB::select($qr8); 
        
        $brand_master = $state_master = $sypt_master = $set_con_master = $acc_master = $asc_master = $reg_master = array();
        $clarion_product_master = $state_master = $sypt_master = $set_con_master = $acc_master = $asc_master = $reg_master = array();
        
        foreach($state_json as $state)
        {
            $state_master[$state->state_id] = $state->state_name;
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
        foreach($asc_json as $acc)
        {
            $asc_master[$acc->center_id] = $acc->asc_code;
        }
        foreach($reg_json as $reg)
        {
            $reg_master[$reg->region_id] = $reg->region_name; 
        }
        
        foreach($clarion_json as $brand)
        {
            $clarion_product_master[$brand->product_id] = $brand->product_name;
        }
        
        //print_r($reg_master);exit;
     $url = $_SERVER['APP_URL'].'/tagging-master';
 
        $tag_type = $request->input('tag_type');
       return view('tagging-data1')
        ->with('TagId',$TagId)
               ->with('tag_type',$tag_type)
               ->with('brand_master',$brand_master)
                ->with('state_master',$state_master)
               ->with('sypt_master',$sypt_master)
               ->with('set_con_master',$set_con_master)
               ->with('acc_master',$acc_master)
               ->with('asc_master',$asc_master)
               ->with('reg_master',$reg_master)
               ->with('clarion_product_master',$clarion_product_master)
               ->with('url', $url);
        
    }
    
    public function save_tagging(Request $request)
    {
       // print_r($request->all()); exit;
        
        $tagging_type = $request->input('tagging_type');
        $taggingArr            =   new TaggingMaster();
        
        if($tagging_type=='clarion')
        {
            $RTagId = $request->input('TagId');
            $dealer_name = $request->input('DealerName');
            $Landmark = $request->input('location');
            $region_id = $request->input('region_id');
            $state = $request->input('state');
            $pincode = $request->input('pincode');
            $Customer_Name = $request->input('Customer_Name');
            $Contact_No = $request->input('Contact_No');
            
            $man_ser_no = $request->input('man_ser_no');
            $ccsc = $request->input('ccsc');
            $vehicle_sale_date = $request->input('vehicle_sale_date');
            $vin_no = $request->input('vin_no');
            $mielage = $request->input('mielage');
            $warranty_type = $request->input('warranty_type');        
            $system_sw_version = $request->input('system_sw_version');
            $job_card = $request->input('job_card');
            $videos = $request->input('videos');
            $crf = $request->input('crf');
            $ftir = $request->input('ftir');
            $ftir_no = $request->input('ftir_no');
            $supr_analysis = $request->input('supr_analysis');        
            $remarks = $request->input('remarks');
            $issue_type = addslashes($request->input('issue_type'));
            $issue_cat = addslashes($request->input('issue_cat'));
            $mobile_handset_model = addslashes($request->input('mobile_handset_model'));
            $def_part_alt_no = addslashes($request->input('def_part_alt_no'));
            $visit_type = addslashes($request->input('visit_type'));
            $asc_person_name = addslashes($request->input('asc_person_name'));
            $asc_location = addslashes($request->input('asc_location'));
            $part_replace = addslashes($request->input('part_replace'));
            $sr_da2 = addslashes($request->input('sr_da2'));
            $issue_resolved_date = addslashes($request->input('issue_resolved_date'));
            $cil_log_date = addslashes($request->input('cil_log_date'));
            $job_status = addslashes($request->input('job_status'));
            $dispatch_date = addslashes($request->input('dispatch_date'));
            $part_dispatch_det_to_asc = addslashes($request->input('part_dispatch_det_to_asc'));
            $tat_delay_remarks = addslashes($request->input('tat_delay_remarks'));
            $defective_part_rcv = addslashes($request->input('defective_part_rcv'));
            $def_sys_det_from_asc_supreme = addslashes($request->input('def_sys_det_from_asc_supreme'));
            $def_sys_dis_det_from_asc_to_cil = addslashes($request->input('def_sys_dis_det_from_asc_to_cil'));
            $final_job_close_date = addslashes($request->input('final_job_close_date'));
            $final_job_status = addslashes($request->input('final_job_status'));
            
            $taggingArr->vehicle_sale_date  =  $vehicle_sale_date; 
            $taggingArr->vin_no  =  $vin_no; 
            $taggingArr->mielage  =  $mielage; 
            $taggingArr->warranty_type  =  $warranty_type; 
            $taggingArr->system_sw_version  =  $system_sw_version; 
            $taggingArr->job_card  =  $job_card; 
            $taggingArr->videos  =  $videos; 
            $taggingArr->crf  =  $crf ;
            $taggingArr->ftir  =  $ftir ;
            $taggingArr->ftir_no  =  $ftir_no ;
            $taggingArr->supr_analysis  =  $supr_analysis ;
            $taggingArr->remarks  =  $remarks ;
            $taggingArr->issue_type  =  $issue_type ;
            $taggingArr->issue_cat  =  $issue_cat ;
            $taggingArr->mobile_handset_model  =  $mobile_handset_model ;
            $taggingArr->def_part_alt_no  =  $def_part_alt_no ;
            $taggingArr->visit_type  =  $visit_type ;
            $taggingArr->asc_person_name  =  $asc_person_name ;
            $taggingArr->asc_location  =  $asc_location ;
            $taggingArr->part_replace  =  $part_replace ;
            $taggingArr->sr_da2  =  $sr_da2 ;
            $taggingArr->issue_resolved_date  =  $issue_resolved_date ;
            $taggingArr->cil_log_date  =  $cil_log_date ;
            $taggingArr->job_status  =  $job_status ;
            $taggingArr->dispatch_date  =  $dispatch_date ;
            $taggingArr->part_dispatch_det_to_asc  =  $part_dispatch_det_to_asc ;
            $taggingArr->tat_delay_remarks  =  $tat_delay_remarks; 
            $taggingArr->defective_part_rcv  =  $defective_part_rcv ;
            $taggingArr->def_sys_det_from_asc_supreme  =  $def_sys_det_from_asc_supreme; 
            $taggingArr->def_sys_dis_det_from_asc_to_cil  =  $def_sys_dis_det_from_asc_to_cil ;
            $taggingArr->final_job_close_date  =  $final_job_close_date ;
            $taggingArr->final_job_status  =  $final_job_status ;
            $taggingArr->brand_id=4;

            
            
            
            $Product = addslashes($request->input('Product'));
            $Model = addslashes($request->input('Model'));
            
            $product_det = ProductMaster::whereRaw(" product_id='$Product'")->first();
            $product_name = $product_det->product_name;
        
            $model_det = ModelMaster::whereRaw("product_id='$Product' and model_id='$Model'")->first();
            $model_name = $model_det->model_name;
            
            $brand_name = 'CLARION';
            #$taggingArr->brand_id=$Brand;
            $UserId = Auth::user()->id;    
            $taggingArr->created_by=$UserId;
            $taggingArr->created_at=date('Y-m-d H:i:s'); 
            #$UserType = Session::get('UserType');
            $alloc_qry = "Case Not Allocated.";

            $center_id = "154";
            $taggingArr->center_id=$center_id;
            $taggingArr->center_allocation_date=date('Y-m-d H:i:s');
            $taggingArr->center_allocation_by=$UserId;
            
            $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
            //$taggingArr->center_allocation_date=date('Y-m-d H:i:s');
            $taggingArr->asc_code=$center_det->asc_code;
            $alloc_qry = "And Case Allocated To ASC ".$center_det->center_name;
                
            
        }
        else
        {
            $RTagId = $request->input('TagId');
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
            $place_id = addslashes($request->input('place'));


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
            $ccsc = addslashes($request->input('ccsc'));

            $accesories_list_arr = $request->input('accesories_list');
            $accesories_list_json = json_encode($accesories_list_arr);
            $accesories_list = addslashes($accesories_list_json);
            $set_conditions_arr = $request->input('set_conditions');
            $set_conditions_json = json_encode($set_conditions_arr);
            $set_conditions = addslashes($set_conditions_json);
            $taggingArr->Customer_Group=$Customer_Group;
            $taggingArr->Alt_No= $Alt_No;
            $taggingArr->Customer_Address=$Customer_Address;
            $taggingArr->call_rcv_frm=$call_rcv_frm;
            $brand_det = BrandMaster::whereRaw("brand_id='$Brand'")->first();
            $brand_name = $brand_det->brand_name;

            $product_catedet = ProductCategoryMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail'")->first();
            $category_name = $product_catedet->category_name;

            $product_det = ProductMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product'")->first();
            $product_name = $product_det->product_name;

            $model_det = ModelMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model'")->first();
            $model_name = $model_det->model_name;

            $place_det = PincodeMaster::whereRaw("pin_id='$place_id'")->first();
            $place = $place_det->place;
            $taggingArr->place_id=$place_id;
            $taggingArr->place=$place;
            $taggingArr->warranty_category=$warranty_category;
            $taggingArr->Gst_No=$Gst_No;
            $taggingArr->email=$email;
            $taggingArr->service_type=$service_type;
            $taggingArr->Bill_Purchase_Date=$Bill_Purchase_Date;
            $taggingArr->warranty_card=$warranty_card;
            $taggingArr->invoice=$invoice;
            $taggingArr->Symptom=$Symptom;
            $taggingArr->add_cmnt=$add_cmnt;
            //$taggingArr->estmt_charge=$estmt_charge;
        
            $taggingArr->set_conditions=$set_conditions;
            $taggingArr->accesories_list=$accesories_list;
            $taggingArr->invoice_no=$invoice_no;
            $taggingArr->brand_id=$Brand;

            $UserId = Auth::user()->id;    
            $taggingArr->created_by=$UserId;
            $taggingArr->created_at=date('Y-m-d H:i:s'); 
            $UserType = Session::get('UserType');
            $alloc_qry = "Case Not Allocated.";
            if($UserType=='ServiceCenter')
            {
                $center_id = Auth::user()->table_id;
                $taggingArr->center_id=$center_id;
                $taggingArr->center_allocation_date=date('Y-m-d H:i:s');
                $taggingArr->center_allocation_by=$UserId;
                
                $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
                //$taggingArr->center_allocation_date=date('Y-m-d H:i:s');
                $taggingArr->asc_code=$center_det->asc_code;
                $alloc_qry = "And Case Allocated To ASC ".$center_det->center_name;
                
            }
            else
            {
                $center_det = $this->allocate_center($Brand,$Product_Detail,$Product,$Model,$pincode,array());
                //print_r($center_det); exit;
                if(!empty($center_det))
                {
                    $center_id=$center_det->center_id;
                    $taggingArr->center_id=$center_id;
                    $taggingArr->asc_code=$center_det->asc_code;
                    $taggingArr->center_allocation_date=date('Y-m-d H:i:s');
                    
                    $alloc_qry = "And Case Allocated To ASC ".$center_det->center_name;
                }
            }

        }
        
        
        $taggingArr->Customer_Name=$Customer_Name;
        $taggingArr->Contact_No=$Contact_No;                
        $taggingArr->Landmark=$Landmark;        
        $taggingArr->state=$state;
        
        $state_code_arr = StateMaster::whereRaw("state_name='$state'")->first();
        $state_code = $state_code_arr->state_code;
        $region_id = $state_code_arr->region_id;
        
        $region_det = RegionMaster::whereRaw("region_id='$region_id'")->first();
        $region = $region_det->region_name;
        
        
        
        
        $dist_details =  PincodeMaster::whereRaw("pincode='$pincode'")->first();
        $dist_id = $dist_details->dist_id;
        
        
        
        
        $taggingArr->state_code=$state_code;
        
        $taggingArr->pincode=$pincode;
        $taggingArr->region_id=$region_id;
        $taggingArr->region=$region;
        $taggingArr->dist_id=$dist_id;
        
        $taggingArr->warranty_type=$warranty_type;
        
        
        $taggingArr->product_category_id=$Product_Detail;
        $taggingArr->product_id=$Product;
        $taggingArr->model_id=$Model;
        $taggingArr->Brand=$brand_name;
        $taggingArr->Product_Detail=$category_name;
        $taggingArr->Product=$product_name;
        $taggingArr->Model=$model_name;
        
        $taggingArr->Serial_No=$Serial_No;
        $taggingArr->man_ser_no=$man_ser_no;
        $taggingArr->dealer_name=$dealer_name;
        $taggingArr->ccsc=$ccsc;
        
        
        
        #print_r($taggingArr);die;
        //$taggingArr->asc_code=$asc_code;
       
        //$taggingArr->report_fault=$report_fault;
        //$taggingArr->service_required=$service_required;
        
        
        $taggingArr->tagging_type = $tagging_type;
        
        
        
        $year = date('y');
        $month = date('m');

        $new_month = date('M');
            switch ($new_month) {
                case 'Jan':
                    $mvalue ="A";
                    break;

                    case 'Feb':
                        $mvalue ="B";
                        break;

                        case 'Mar':
                            $mvalue ="C";
                            break;

                            case 'Apr':
                                $mvalue ="D";
                                break;

                                case 'May':
                         $mvalue ="E";
                           break;

                           case 'Jun':
                          $mvalue ="F";
                                break;

                             case 'Jul':
                                   $mvalue ="G";
                                 break;

                                 case 'Aug':
                                   $mvalue ="H";
                                   break;

                                 case 'Sep':
                                   $mvalue ="I";
                                   break;

                                 case 'Oct':
                                   $mvalue ="J";
                                   break;

                                 case 'Nov':
                                   $mvalue ="K";
                                   break;
                
                default:
                    $mvalue ="L";
                    break;
            }
        //echo $mvalue;exit;

        $day = date('d');
        $month_name = strtoupper(date('M'));
        //$year = strtoupper(date('y'));
        
        #$qr_max_no = "SELECT MAX(tsr_no) srno FROM `tagging_master` WHERE  ticket_year='$year' AND ticket_month='$month'";
        $qr_max_no = "SELECT MAX(sr_no) srno FROM `tagging_master` WHERE  job_year='$year' AND job_month='$month'";

        $max_json           =   DB::select($qr_max_no);
        $max = $max_json[0];
        $sr_no = $max->srno;
        
        $str_no = "000000";
        $sr_no = $sr_no+1;
        $len = strlen($str_no);
        $newlen = strlen("$sr_no");
        $new_no = substr_replace($str_no, $sr_no, $len-$newlen,$newlen);
        $short_brand_name = substr($brand_name, 0, 2);
        //$ticket_no = "$short_brand_name"."$month"."$day"."$year".$new_no;  
        //$ticket_no = "S".$year.$mvalue.$day.$new_no;
        $subcode = 'SP';
         if(strtolower($brand_name)=='pioneer')
         {
             $subcode = 'PI';
         }
         else if(strtolower($brand_name)=='clarion')
         {
             $subcode = 'CL';
         }
         
        $ticket_no  = "{$subcode}{$year}{$month}{$new_no}"; 
        $taggingArr->ticket_no=$ticket_no;
        $taggingArr->job_year=$year;
        $taggingArr->job_month=$month;
        $taggingArr->sr_no=$sr_no;
        
        
        
        echo "<br>";
     
        
            if($taggingArr->save()){
                $TagId = $taggingArr->id;
                $old_dir_name = "/var/www/html/supreme/storage/app/supreme/$RTagId";
                $new_dir_name = "/var/www/html/supreme/storage/app/supreme/$TagId";
                $rename = rename( $old_dir_name, $new_dir_name);
                
                if($rename)
                {
                   TagImage::whereRaw("RTagId='$RTagId'")->update(array('TagId'=>$TagId,'created_by'=>$UserId)); 
                }
                
                //exit;
                //print_r($_FILES); exit;
                
                
                //if($entry_type=='walking')
                
                //$warranty_card_copy = addslashes($request->input('warranty_card_copy'));
                //$image_arr =TagImage::whereRaw("RTagId='$RTagId'")->get();
                
                /*$file_arr = array('wrrn'=>'warranty_card_copy','prcs'=>'purchase_copy','srl'=>'serial_no_copy','smtm1'=>'symptom_photo1','smtm2'=>'symptom_photo2','smtm3'=>'symptom_photo3');
                foreach($file_arr as $inputName=>$file_name)
                {
                    if(!empty($_FILES[$inputName]['name']))
                    {
                        //$wrrn = 'wrrn';
                        //$file_name = 'warranty_card_copy';
                        $today_date = date('Y_m_d_h_i_s');
                        $ext= $today_date.substr($_FILES[$inputName]['name'],strrpos($_FILES[$inputName]['name'],'.'),strlen($_FILES[$inputName]['name'])); ;
                        Storage::disk('supreme')->put("$TagId/$file_name"."$ext", file_get_contents($_FILES[$inputName]['tmp_name']));
                        $taggingArr = array();
                        $taggingArr[$file_name]=$file_name."$ext";
                        TaggingMaster::where('tagid',$TagId)->update($taggingArr);
                    }
                }*/
                   
                    /*if(!empty($_FILES['invoice_copy']['name']))
                    {

                        $ext= $today_date.substr($_FILES['invoice_copy']['name'],strrpos($_FILES['invoice_copy']['name'],'.'),strlen($_FILES['invoice_copy']['name'])); ;
                        Storage::disk('supreme')->put("$TagId/invoice_copy"."$ext", file_get_contents($_FILES['invoice_copy']['tmp_name']));
                        $taggingArr = array();
                        $taggingArr['invoice_copy']="invoice_copy$ext";
                        TaggingMaster::where('tagid',$TagId)->update($taggingArr);
                    }
                    if(!empty($_FILES['model_no_copy']['name']))
                    {

                        $ext= $today_date.substr($_FILES['model_no_copy']['name'],strrpos($_FILES['model_no_copy']['name'],'.'),strlen($_FILES['model_no_copy']['name'])); ;
                        Storage::disk('supreme')->put("$TagId/model_no_copy"."$ext", file_get_contents($_FILES['model_no_copy']['tmp_name']));
                        $taggingArr = array();
                        $taggingArr['model_no_copy']="model_no_copy$ext";
                        TaggingMaster::where('tagid',$TagId)->update($taggingArr);
                    }
                    if(!empty($_FILES['serial_no_copy']['name']))
                    {

                        $ext= $today_date.substr($_FILES['serial_no_copy']['name'],strrpos($_FILES['serial_no_copy']['name'],'.'),strlen($_FILES['serial_no_copy']['name'])); ;
                        Storage::disk('supreme')->put("$TagId/serial_no_copy"."$ext", file_get_contents($_FILES['serial_no_copy']['tmp_name']));
                        $taggingArr = array();
                        $taggingArr['serial_no_copy']="serial_no_copy$ext";
                        TaggingMaster::where('tagid',$TagId)->update($taggingArr);
                    }
                    if(!empty($_FILES['symptom_photo1']['name']))
                    {

                        $ext= $today_date.substr($_FILES['symptom_photo1']['name'],strrpos($_FILES['symptom_photo1']['name'],'.'),strlen($_FILES['symptom_photo1']['name'])); ;
                        Storage::disk('supreme')->put("$TagId/symptom_photo1"."$ext", file_get_contents($_FILES['symptom_photo1']['tmp_name']));
                        $taggingArr = array();
                        $taggingArr['symptom_photo1']="symptom_photo1$ext";
                        TaggingMaster::where('tagid',$TagId)->update($taggingArr);
                    }
                    if(!empty($_FILES['symptom_photo2']['name']))
                    {

                        $ext= $today_date.substr($_FILES['symptom_photo2']['name'],strrpos($_FILES['symptom_photo2']['name'],'.'),strlen($_FILES['symptom_photo2']['name'])); ;
                        Storage::disk('supreme')->put("$TagId/symptom_photo2"."$ext", file_get_contents($_FILES['symptom_photo2']['tmp_name']));
                        $taggingArr = array();
                        $taggingArr['symptom_photo2']="symptom_photo2$ext";
                        TaggingMaster::where('tagid',$TagId)->update($taggingArr);
                    }
                    if(!empty($_FILES['symptom_photo3']['name']))
                    {

                        $ext= $today_date.substr($_FILES['symptom_photo3']['name'],strrpos($_FILES['symptom_photo3']['name'],'.'),strlen($_FILES['symptom_photo3']['name'])); ;
                        Storage::disk('supreme')->put("$TagId/symptom_photo3"."$ext", file_get_contents($_FILES['symptom_photo3']['tmp_name']));
                        $taggingArr = array();
                        $taggingArr['symptom_photo3']="symptom_photo3$ext";
                        TaggingMaster::where('tagid',$TagId)->update($taggingArr);
                    }*/
                
                
                    
                Session::flash('message', "Ticket No. $ticket_no Added Successfully For $Customer_Name.".$alloc_qry);
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                
                Session::flash('error', "$tag_type Not Added. Please Try Again.");
                Session::flash('alert-class', 'alert-danger');
            } 
            
                return redirect('tagging-master');
            
            
      //  }
        
    }
    
    
    
    public function edit_details(Request $request)
    {
        $TagId = $request->input('TagId'); 
        $data_json = TaggingMaster::where("TagId",$TagId)->first();
        $data = json_decode($data_json,true);
        
        $qr1 = "SELECT state_id,state_name FROM state_master st order by state_name";
        $state_json           =   DB::select($qr1); 
        
        $state_master  = $pin_master = array();
        foreach($state_json as $state)
        {
            $state_master[$state->state_id] = $state->state_name;
        }
        
        $state_name = $data['State']; 
        $state = StateMaster::whereRaw("state_name = '$state_name'")->first();
        $state_id = $state->state_id;
        
        $pin_array = DB::select("SELECT Pin_Id,pincode from pincode_master where state_id='$state_id'");
        foreach($pin_array as $pin)
        {
            $pin_master[$pin->Pin_Id] = $pin->pincode;
        }
        //print_r($pin_master ); exit;
        return view('edit-tagging-details')
            ->with('state_master',$state_master)
            ->with('pin_master',$pin_master)    
                ->with('data',$data)->with('TagId',$TagId);
    }
    
    public function update_details(Request $request)
    {
        $TagId = addslashes($request->input('TagId'));
        $Customer_Name = addslashes($request->input('Customer_Name'));
        $Contact_No = addslashes($request->input('Contact_No'));
        $Customer_Address = addslashes($request->input('Customer_Address'));
        $state = addslashes($request->input('state'));
        $pincode = addslashes($request->input('pincode'));
        
        $taggingArr = array();
        $taggingArr['Customer_Name']=$Customer_Name;
        $taggingArr['Contact_No']=$Contact_No;
        $taggingArr['Customer_Address']=$Customer_Address;
        $taggingArr['state']=$state;
        $taggingArr['pincode']=$pincode;
        
        
        
        
            
            
            
            if(TaggingMaster::whereRaw("TagId='$TagId'")->update($taggingArr))
            {
                Session::flash('message', "Tagging Details Updated Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else
            {
                Session::flash('error', "Tagging Details Update Failed. Please Try Again.");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('tagging-master?Contact_No='. $Contact_No);
        
    }
    
    public function get_image_field(Request $request)
    {       
            $file_arr = array('wrrn'=>'Warranty card','prcs'=>'Purchase Invoice','mdl'=>'model_no_copy','srl'=>'Serial No. Image','smtm1'=>'Symptom Image 1','smtm2'=>'Any special Approval','smtm3'=>'symptom_photo3','job_card'=>'Job Card','crf'=>'CRF','ftir'=>'FTIR');
            $image_type = $request->input('img');
            $tagid = $request->input('TagId');
            $random_no = rand(100000,999999);
            $image_no = $random_no;
            $image_name = $file_arr[$image_type];
            $html = '<div id="'.$image_no.'_cntr" class="float-container">';
            $html .= '<div  class="float-child" > 
                                                        <div class="float-container">
                                                            <div class="float-child"> 
                                                                
                                                                <br>
                                                                <br>
                                                                <br>
                                                                <div class="form-row">
                                                                    <div class="col-md-12">
                                                                        <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: ';
            $html .= $image_name;
            $html .='</b></label>
                                                                            <button type="button" style="width:100%" id="remove_';
            $html .=$image_no;
            $html .='" name="remove_'.$image_no.'" onclick="remove_img(';
            $html .="'".$image_no."'";
            $html .=')" class="mt-2 btn btn-primary">Remove</button>
                                                                        </div>';
            
            $html .= '<div class="position-relative form-group">';
            $html .='<button type="button" style="width:100%" onclick="download_img('."'$image_no'".')" class="btn btn-primary">Download</button></div>';
                                                                                
                                                                                
                                                                                
            $html .='                                                                </div></div>
                                                                </div>  
                                                            </div>
                                                            <div class="float-child" style="height:250px;" id="';
            $html .=$image_no.'_img_disp">';
            $html .='<form id="form'.$image_no.'" class="pop" enctype="multipart/form-data">';
            $html .='<input type="hidden" name="TagId" value="'.$tagid.'" >';
            $html .='<input type="hidden" name="image_name" id="'.$image_no.'_img_type" value="'.$image_name.'" >';
            $html .= '<button type="button" id="save_'.$image_no.'" class="btn btn-primary" name="submit" onclick="save_image('."'$image_no'".')" style="float:right;margin-right: 70px;">save</button>';
            
            $html .='</form></div> 
                                                        </div>
                                                    </div>
                                                    <div class="float-child" style="width:500px;"></div>
                                                    
                                                </div>';
            echo json_encode(array('image_no'=>$image_no,'html'=>$html));exit;
            }
    
            public function get_video_field(Request $request)
            {       
                $file_arr = array(
                    'video'=> 'Video'
                );
            
                $video_type = $request->input('video');
                $tagid = $request->input('TagId');
                $random_no = rand(100000, 999999);
                $video_no = $random_no;
                $video_name = $file_arr[$video_type];
            
                $html = '<div id="'.$video_no.'_cntr" class="float-container">';
                $html .= '<div class="float-child"> 
                            <div class="float-container">
                                <div class="float-child"> 
                                    <br><br><br>
                                    <div class="form-row">
                                        <div class="col-md-12">
                                            <div class="position-relative form-group">
                                                <label for="examplePassword11" class="">
                                                    <b>File Type: '.$video_name.'</b>
                                                </label>
                                                <button type="button" style="width:100%" id="remove_'.$video_no.'" name="remove_'.$video_no.'" onclick="remove_video('.$video_no.')" class="mt-2 btn btn-primary">
                                                    Remove
                                                </button>
                                            </div>
                                            <div class="position-relative form-group">
                                                <button type="button" style="width:100%" onclick="download_video('.$video_no.')" class="btn btn-primary">Download</button>
                                            </div>
                                        </div>
                                    </div>  
                                </div>
                                <div class="float-child" style="height:250px;" id="'.$video_no.'_video_disp">
                                    <form id="form'.$video_no.'" class="pop" enctype="multipart/form-data">
                                        <input type="hidden" name="TagId" value="'.$tagid.'" >
                                        <input type="hidden" name="video_name" id="'.$video_no.'_video_type" value="'.$video_name.'" >
                                        <button type="button" id="save_'.$video_no.'" class="btn btn-primary" name="submit" onclick="save_video('.$video_no.')" style="float:right;margin-right: 70px;">save</button>
                                    </form>
                                </div> 
                            </div>
                        </div>
                        <div class="float-child" style="width:500px;"></div>
                    </div>';
            
                echo json_encode(array('video_no' => $video_no, 'html' => $html));
                exit;
            }
    
    public function save_image(Request $request)
    {
        $tag_id=$request->input('TagId');
        $image_name=$request->input('image_name');
        //echo $image_name;die;
        #print_r($_FILES);die;
        $TagImage = new TagImage();
        $TagImage->RTagId=$tag_id;
        $TagImage->image_type=$image_name;
        
        $file_arr = array('wrrn'=>'warranty_card_copy','prcs'=>'purchase_copy','srl'=>'serial_no_copy','smtm1'=>'symptom_photo1','smtm2'=>'symptom_photo2','smtm3'=>'symptom_photo3','job_card'=>'Job Card','crf'=>'CRF','ftir'=>'FTIR');
        $today_date = date('Y_m_d_h_i_s');
        foreach($_FILES as $key=>$file)
        {
           $image_name=$_FILES[$key]['name'];
           $file_name=$file_arr[$key];
           $inputName = $key;
            $ext= $today_date.substr($_FILES[$inputName]['name'],strrpos($_FILES[$inputName]['name'],'.'),strlen($_FILES[$inputName]['name']));
    
            Storage::disk('supreme')->put("$tag_id/$file_name"."$ext", file_get_contents($_FILES[$inputName]['tmp_name']));
            $TagImage->img_url = $file_name."$ext";
            
            
        }        
        if($TagImage->save())
        {
            echo"1";

            // echo $image_name;
        }
        else{echo"0";}
    }


    public function save_video(Request $request)
    {
        $tag_id = $request->input('TagId');
        $video_name = $request->input('video_name');
        #print_r($_FILES);die;
        $TagVideo = new TagImage();
        $TagVideo->RTagId = $tag_id;
        $TagVideo->image_type = $video_name;

        $file_arr = array(
            'video'  => 'Video'
        );

        $today_date = date('Y_m_d_h_i_s');

        foreach ($_FILES as $key => $file) {
            $video_name = $_FILES[$key]['name'];
            $file_name = $file_arr[$key];
            $inputName = $key;
            $ext = $today_date . substr($_FILES[$inputName]['name'], strrpos($_FILES[$inputName]['name'], '.'), strlen($_FILES[$inputName]['name']));
            Storage::disk('supreme')->put("$tag_id/$file_name" . "$ext", file_get_contents($_FILES[$inputName]['tmp_name']));
            $TagVideo->img_url = $file_name . "$ext";
        }

        if ($TagVideo->save()) {
            echo "1";
        } else {
            echo "0";
        }
    }

}

