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


class InvoiceController extends Controller
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
    
    public function view_close_case(Request $request)
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
            $tag_data_qr = "select * from $from_table tagging_master tm $on_table where  tm.payment_entry='1'   and tm.case_close='1'  and date(tm.created_at)=curdate();"; 
        }
        else
        {
            $tag_data_qr = "select * from $from_table tagging_master tm $on_table where tm.payment_entry='1'   and tm.case_close='1'   $whereTag"; 
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/close-case-view';
        return view('close-case-view')
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
        Session::put("page-title","Invoice Creation");
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
        
        $invoice_json = InvoicePart::whereRaw("tag_id = '$TagId'  ")->get();
        $data_invoice = json_decode($invoice_json,true);
        
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
        
        //print_r($spare_req_master); exit;
        
        $qr6 = "SELECT model_id,model_name FROM `model_master` WHERE model_status='1'";
        $model_json           =   DB::select($qr6);
        
        $qr3 = "SELECT field_name,field_code FROM `tbl_symptom` where sypt_status='1' ";
        $symptom_json           =   DB::select($qr3);
        
        $qr4 = "SELECT field_name,sub_field_name FROM `condition_master` 
WHERE con_status='1'
order BY field_name,sub_field_name";
        $con_json           =   DB::select($qr4);
        
        $qr5 = "SELECT Acc_Id,acc_name FROM `tbl_accessories` WHERE acc_status='1'";
        $acc_json           =   DB::select($qr5);
        
        
        
        $qr8 = "SELECT warranty_name FROM `tbl_warranty` WHERE warranty_status='1'";
        $warranty_json           =   DB::select($qr8);
        
        $brand_master = $state_master = $sypt_master = $set_con_master = $acc_master = array();
        foreach($state_json as $state)
        {
            $state_master[$state->state_id] = $state->state_name;
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
        
        $TagPart = TagPart::whereRaw("Tag_Id")->get();
        
        
        
        $url = $_SERVER['APP_URL'].'/close-case-view';
        //print_r($data); exit;
        return view('tax-invoice')
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
                ->with('url', $url)
                ->with('part_arr', $part_arr)
                ->with('asc_master',$asc_master)
                ->with('data_invoice',$data_invoice)
                ->with('state_code_client',$state_code_client)
                ->with('state_code_center',$state_code_center)
                ->with('ProductMaster',$ProductMaster);
                
    }
    
    public function save_invoice(Request $request)
    {
        $part_arr = $request->input('part_arr');
        $qty_arr = $request->input('qty');
        $rate_arr = $request->input('rate');
        $disc_arr = $request->input('disc');
        $tax_arr = $request->input('tax');
        $tax_type = $request->input('tax_type');
        $tag_id = $request->input('tag_id');
        $gstin = $request->input('gstin');
        $UserId = Auth::user()->id;    
        $created_at=date("Y-m-d H:i:s"); 
        
        $inv_master = array();
        $total_invoice = 0.0;
        $total_discount = 0.0;
        $total_taxable_value = 0.0;
        $total_cgst = 0.0;
        $total_sgst = 0.0;
        $total_igst = 0.0;
        $grand_total = 0.0;
        $round_off_value = 0.0;
        $total_payable = 0.0;
        
        //print_r($rate_arr); exit;
        foreach($part_arr as $part)
        {
            $inv_arr = array();
            $inv_arr['tag_id'] = $tag_id;
            if($part=='SERVICE CHARGES####9987')
            {
                $part_det = explode('##',$part);
                $part_name        =   $part_det[0];
                $part_no          =   $part_det[1];
                $hsn_code         =   $part_det[2];
                $inv_arr['sac_code'] = "";
                $rate = $rate_arr[$part];
            }
            else
            {
                $part_det = explode('##',$part);
                $part_name        =   $part_det[0];
                $part_no          =   $part_det[1];
                $hsn_code         =   $part_det[2];
                $inv_arr['sac_code'] = "";
                
                $spare_part = SparePart::whereRaw("part_name = '$part_name' and part_no='$part_no' and hsn_code='$hsn_code'")->first();
                $rate = $spare_part->part_rate;
                $tax = $spare_part->part_tax;
                
                
            }
                $inv_arr['created_by'] = $UserId;
                $inv_arr['created_at'] = $created_at;
                $inv_arr['part_name'] = $part_name;
                $inv_arr['part_no'] = $part_no;
                $inv_arr['hsn_code'] = $hsn_code;
            
            $qty = $qty_arr[$part];
            $disc = $disc_arr[$part];
            
            $inv_arr['qty']     = $qty;
            $inv_arr['rate']    = $rate;
            $inv_arr['total'] = $total = round($qty*$rate,2);
            $inv_arr['discount']   = $disc;
            $inv_arr['tax_type']   = $tax_type;
            $taxable_amt   = round($total-$disc,2);
            $inv_arr['taxable_amt']   = $taxable_amt;
            
            $cgst = 0;$sgst = 0;$igst = 0;
            if($tax_type=='1')
            {
                $inv_arr['cgst_per']   = $tax/2;
                $inv_arr['sgst_per']   = $tax/2;
                $tax_val = round(($tax*$taxable_amt)/(2*100),2);
                $inv_arr['cgst_amt'] = $cgst = $tax_val;
                $inv_arr['sgst_amt'] = $sgst  = $tax_val;
                
                $inv_arr['igst_per']    = 0;
                $inv_arr['igst_amt']    = 0;
            }
            else
            {
                $inv_arr['igst_per']   = $tax;
                $tax_val = round($tax*$taxable_amt/100,2);
                $inv_arr['igst_amt'] =$igst  = $tax_val;
                
                $inv_arr['cgst_per']   = 0;
                $inv_arr['sgst_per']   = 0;
                $inv_arr['cgst_amt'] = 0;
                $inv_arr['sgst_amt'] = 0;
                
            }
            
            $net_total= $taxable_amt+$cgst+$sgst+$igst;
            $inv_arr['net_total']  = $net_total;
            
            
            $total_invoice += $total;
            $total_discount += $disc;
            $total_taxable_value += $taxable_amt;
            $total_cgst += $cgst;
            $total_sgst += $sgst;
            $total_igst += $igst;
            $grand_total +=$net_total;
            
            
            
            $inv_master[] = $inv_arr;
        }
        
        InvoicePart::whereRaw("tag_id='$tag_id'")->delete();
        
        if(InvoicePart::insert($inv_master))
        {
            $taggingArr = array();
            
            $taggingArr['total_invoice']=$total_invoice;
            $taggingArr['total_discount']=$total_discount;
            $taggingArr['total_taxable_value']=$total_taxable_value;
            $taggingArr['total_cgst']=$total_cgst;
            $taggingArr['total_sgst']=$total_sgst;
            $taggingArr['total_igst']=$total_igst;
            $taggingArr['grand_total']=$grand_total;
            
            $total_payable = round($grand_total);
            $round_off_value = $grand_total - $total_payable;
            
            $taggingArr['round_off_value']=$round_off_value;
            $taggingArr['total_payable']=$total_payable;
            $taggingArr['gstin']=$gstin;
            
            $taggingArr['inv_status']='0';
            $taggingArr['inv_date']=$created_at;
            $taggingArr['inv_by']=$UserId;
                    
            TaggingMaster::whereRaw("TagId='$tag_id'")->update($taggingArr);
            
            Session::flash('message', "Invoice Detail Saved Successfully.");
            Session::flash('alert-class', 'alert-success');
        }
        else
        {
            Session::flash('error', "Invoice Detail Saved. Please Try Again.");
            Session::flash('alert-class', 'alert-danger');
            //return back();
            
            
        }
        
        return redirect('close-case-view');
       
    }
}

