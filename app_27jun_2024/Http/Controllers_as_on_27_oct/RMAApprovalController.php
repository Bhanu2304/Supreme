<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\VendorMaster;
use App\StateMaster;
use App\PincodeMaster;
use Auth;
use Session;
use DB;
use App\TaggingMaster;
use App\ProductMaster;
use App\ServiceEngineer;
use Illuminate\Support\Facades\Storage;
use App\Exports\VendorExport;
use Maatwebsite\Excel\Facades\Excel;

class RMAApprovalController extends Controller
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
        Session::put("page-title","RMA Approval");
        
        //$UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
//        $whereUser = "";
//        if($UserType!='Admin')
//        {
//            $whereUser = "and vendor_id='$UserId'";
//        }
        
        $qr1 = "SELECT pm.state_id,state_name,pincode FROM `pincode_master` pm 
INNER JOIN state_master st ON pm.state_id = st.state_id
WHERE 1=1";
        $vendor_pin_json           =   DB::select($qr1); 
        
        
        //print_r($vendor_pin_json); exit;
        
        $state_master = $pin_master = array();
        foreach($vendor_pin_json as $vpin)
        {
            $state_master[$vpin->state_id] = $vpin->state_name;
            $pin_master[$vpin->pincode] = $vpin->pincode;
        }
        
        //get method request
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        
        $whereTag = "";
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and Pincode = '$pincode'";
        }
        if(strlen($contact_no)==6)
        {
            $whereTag .= " and Pincode='$contact_no'";
        }
        if(!empty($contact_no) && strlen($contact_no)>6)
        {
            $whereTag .= " and contact_no='$contact_no'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(created_at) between '$from_date1' and '$to_date1'";
        }
        
        
        if(empty($whereTag))
        {
            $tag_data_qr = "select * from tagging_master where case_close is null and call_status='RMA' and date(created_at)=curdate();"; 
        }
        else
        {
            $tag_data_qr = "select * from tagging_master where case_close is null and call_status='RMA' $whereTag"; 
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        
        return view('rma-tag-view')
            ->with('pin_master',$pin_master)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('pincode',$pincode)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('DataArr',$DataArr)
                
                ->with('whereTag',$whereTag); 
                
    }
    
    public function view_tag_case(Request $request)
    {
        $TagId = $request->input('TagId'); 
        $whereTag = $request->input('whereTag'); 
        $data_json = TaggingMaster::whereRaw("TagId = '$TagId' and call_status='RMA' and case_close is null")->first();
        $data = json_decode($data_json,true);
        
        
        $ProductMaster_json = ProductMaster::whereRaw("product_status='1'")->get();
        $ProductMaster = json_decode($ProductMaster_json,true);
        //print_r($data); exit;
        return view('rma-tag-case-view')
                ->with('data',$data)
                ->with('TagId',$TagId)
                ->with('whereTag',$whereTag)
                ->with('ProductMaster',$ProductMaster);
    }
    
    
    public function save_rma(Request $request)
    {
        $rma_approve = $_POST['rma_approve'];
        $rma_remark = $_POST['rma_remark'];
        $whereTag = base64_decode($request->input('whereTag')); 
        $case_id = $_POST['TagId'];
       
        
        if(empty($case_id))
        {
            Session::flash('error', "Please Select Case First");
            Session::flash('alert-class', 'alert-danger'); 
           return back();
        }
        $cur_date = date('Y-m-d H:i:s');
        $UserId = Session::get('UserId');
            if($rma_approve=='Yes')
            {
                TaggingMaster::whereRaw("TagId='$case_id' and case_close is null")
                        ->update(array('rma_remark'=>$rma_remark,'rma_approve'=>$rma_approve,'rma_status'=>'2','rma_date'=>$cur_date,'rma_by'=>$UserId,'case_close'=>'1'));
            }
            else
            {
                TaggingMaster::whereRaw("TagId='$case_id' and case_close is null")
                        ->update(array('rma_remark'=>$rma_remark,'rma_approve'=>$rma_approve,'rma_status'=>'1','rma_date'=>$cur_date,'rma_by'=>$UserId,'case_close'=>'1'));
            }
            Session::flash('success', "Approval Has been done.");
            Session::flash('alert-class', 'alert-success'); 
        
        return redirect("rma-tag-view?".$whereTag); 
    }
    
    public function save_multi_rma(Request $request)
    {
        $rma_approve = $_POST['rma_approve'];
        $case_arr = $_POST['case'];
        $rma_remark = $_POST['rma_remark'];
        $whereTag = base64_decode($request->input('whereTag')); 
        
       
        
        if(empty($case_arr))
        {
            Session::flash('error', "Please Select Case First");
            Session::flash('alert-class', 'alert-danger'); 
           return back();
        }
        $cur_date = date('Y-m-d H:i:s');
        $UserId = Session::get('UserId');
        
        
        foreach($case_arr as $case_id)    
        {
            if($rma_approve=='Yes')
            {
                TaggingMaster::whereRaw("TagId='$case_id' and case_close is null")
                        ->update(array('rma_remark'=>$rma_remark,'rma_approve'=>$rma_approve,'rma_status'=>'2','rma_date'=>$cur_date,'rma_by'=>$UserId,'case_close'=>'1'));
            }
            else
            {
                TaggingMaster::whereRaw("TagId='$case_id' and case_close is null")
                        ->update(array('rma_remark'=>$rma_remark,'rma_approve'=>$rma_approve,'rma_status'=>'1','rma_by'=>$UserId,'rma_date'=>$cur_date,'case_close'=>'1'));
            }
            Session::flash('success', "Approval Has been done.");
            Session::flash('alert-class', 'alert-success'); 
        }
        return redirect("rma-tag-view?".$whereTag);
    } 
    
    
    
    
    
        
    
     
    
}

