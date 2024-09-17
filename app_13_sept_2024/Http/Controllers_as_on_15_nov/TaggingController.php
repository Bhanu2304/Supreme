<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;



use DB;
use Auth;
use Session;
use App\User;
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
    
    
    public function index(Request $request)
    {
        $mobile = $request->input('Contact_No');
        $data_json           =   TaggingMaster::whereRaw("Contact_No='$mobile'")->get(); 
        $data = json_decode($data_json,true);
        
        $url = $_SERVER['APP_URL'].'/tagging-master';
        
        return view('tagging-details')
                ->with('DataArr', $data)
                ->with('contact_no', $mobile)->with('url', $url); 
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
        //$entry_type = $request->input('entry_type');
        
        
        $whereTag = "";
        if($UserType=='ServiceCenter')
        {
            $whereTag .= " and center_id ='$center_id'";
        }
        
        $qr1 = "SELECT state_id,state_name FROM state_master st order by state_name";
        $state_json           =   DB::select($qr1); 
 
        $qr2 = "SELECT brand_id,brand_name  FROM  brand_master where brand_status='1' ";
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
        
        
        $brand_master = $state_master = $sypt_master = $set_con_master = $acc_master = array();
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
        
        
     $url = $_SERVER['APP_URL'].'/tagging-master';
 
        $tag_type = $request->input('tag_type');
       return view('tagging-data1')
               ->with('tag_type',$tag_type)
               ->with('brand_master',$brand_master)
                ->with('state_master',$state_master)
               ->with('sypt_master',$sypt_master)
               ->with('set_con_master',$set_con_master)
               ->with('acc_master',$acc_master)
               ->with('asc_master',$asc_master)
               ->with('url', $url);
        
    }
    
    public function save_tagging(Request $request)
    {
        //print_r($_FILES); exit;
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
        
        
        $accesories_list_arr = $request->input('accesories_list');
        $accesories_list_json = json_encode($accesories_list_arr);
        $accesories_list = addslashes($accesories_list_json);
        $set_conditions_arr = $request->input('set_conditions');
        $set_conditions_json = json_encode($set_conditions_arr);
        $set_conditions = addslashes($set_conditions_json);
            
            
        $taggingArr            =   new TaggingMaster();

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
        
        $region_det = RegionMaster::whereRaw("region_id='$region_id'")->first();
        $region = $region_det->region_name;
        
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
        
        $place_det = PincodeMaster::whereRaw("pin_id='$place_id'")->first();
        $place = $place_det->place;
        
        
        $taggingArr->state_code=$state_code;
        $taggingArr->place_id=$place_id;
        $taggingArr->place=$place;
        $taggingArr->pincode=$pincode;
        $taggingArr->region_id=$region_id;
        $taggingArr->region=$region;
        $taggingArr->dist_id=$dist_id;
        $taggingArr->service_type=$service_type;
        $taggingArr->warranty_type=$warranty_type;
        $taggingArr->warranty_category=$warranty_category;
        $taggingArr->brand_id=$Brand;
        $taggingArr->product_category_id=$Product_Detail;
        $taggingArr->product_id=$Product;
        $taggingArr->model_id=$Model;
        $taggingArr->Brand=$brand_name;
        $taggingArr->Product_Detail=$category_name;
        $taggingArr->Product=$product_name;
        $taggingArr->Model=$model_name;
        
        $taggingArr->Gst_No=$Gst_No;
        $taggingArr->email=$email;
        $taggingArr->Serial_No=$Serial_No;
        $taggingArr->man_ser_no=$man_ser_no;
        $taggingArr->dealer_name=$dealer_name;
        
        
        $taggingArr->Bill_Purchase_Date=$Bill_Purchase_Date;
        //$taggingArr->asc_code=$asc_code;
        $taggingArr->warranty_card=$warranty_card;
        $taggingArr->invoice=$invoice;

        //$taggingArr->report_fault=$report_fault;
        //$taggingArr->service_required=$service_required;
        $taggingArr->Symptom=$Symptom;
        $taggingArr->add_cmnt=$add_cmnt;
        //$taggingArr->estmt_charge=$estmt_charge;
        
        $taggingArr->set_conditions=$set_conditions;
        $taggingArr->accesories_list=$accesories_list;
        $taggingArr->invoice_no=$invoice_no;
        
        $taggingArr->entry_type = $entry_type;
        
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
            $alloc_qry = "And Case Allocated To ASC ".$center_det->asc_code;
        }
        else
        {
            $map_product_exist = SCProductMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model'")->first();
            $pincode_exist = SCPinMaster::whereRaw("pincode='$pincode'")->first();
            
            if(!empty($map_product_exist) && !empty($pincode_exist))
            {
                $center_id=$map_product_exist->center_id;
                $taggingArr->center_id=$map_product_exist->center_id;
                $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
            //$taggingArr->center_allocation_date=date('Y-m-d H:i:s');
                $taggingArr->asc_code=$center_det->asc_code;
                $alloc_qry = "And Case Allocated To ASC ".$center_det->center_name;
            }
            
            
        }
        
        $year = date('y');
        $month = date('m');
        $day = date('d');
        //$month_name = strtoupper(date('M'));
        
        $qr_max_no = "SELECT MAX(tsr_no) srno FROM `tagging_master` WHERE  ticket_year='$year' AND ticket_month='$month'";
        $max_json           =   DB::select($qr_max_no);
        $max = $max_json[0];
        $sr_no = $max->srno;
        
        $str_no = "00000";
        $sr_no = $sr_no+1;
        $len = strlen($str_no);
        $newlen = strlen("$sr_no");
        $new_no = substr_replace($str_no, $sr_no, $len-$newlen,$newlen);
        $short_brand_name = substr($brand_name, 0, 2);
        $ticket_no = "$short_brand_name"."$month"."$day"."$year".$new_no;
         
        
        $taggingArr->ticket_no=$ticket_no;
        $taggingArr->job_year=$year;
        $taggingArr->job_month=$month;
        $taggingArr->sr_no=$sr_no;
        
            
        
        
            if($taggingArr->save()){
                $TagId = $taggingArr->id;
                //exit;
                //print_r($_FILES); exit;
                
                
                //if($entry_type=='walking')
                
                //$warranty_card_copy = addslashes($request->input('warranty_card_copy'));
                $file_arr = array('wrrn'=>'warranty_card_copy','prcs'=>'purchase_copy','srl'=>'serial_no_copy','smtm1'=>'symptom_photo1','smtm2'=>'symptom_photo2','smtm3'=>'symptom_photo3');
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
                }
                   
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
    
    
    
    
}

