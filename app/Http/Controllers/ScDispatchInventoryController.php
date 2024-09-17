<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\BrandMaster;
use App\SparePart;
use App\DispatchInventory;
use App\DispatchInventoryParticulars;
use App\DispatchInventoryScParticulars;
use App\ScDispatchInventory;
use App\OutwardInvoice;
use App\TagPart;
use App\ModelMaster;
use App\ServiceCenter;
use App\TaggingMaster;
use App\OutwardInvoiceSc;
use App\OutwardPending;
use DB;
use Auth;
use Session;
//Checking an Event Listener
use App\Events\HODispatched;

class ScDispatchInventoryController extends Controller
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
        Session::put("page-title","Dispatch Inventory Center");

        $challan_no = $request->input('challan_no');
        $job_no = $request->input('job_no');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        
        $challan_no = $request->input('challan_no2');
        $from_date2 = $request->input('from_date2');
        $to_date2 = $request->input('to_date2');
        $tab1 = $request->input('tab1');
        $tab2 = $request->input('tab2');


        $whereTag = "";

        $whereTag2 = "";

       

        if(!empty($job_no))
        {   
            $whereTag .= " and oid.job_no = '$job_no' ";
        }

        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(oid.created_at) between '$from_date1' and '$to_date1'";
        }


        if(!empty($challan_no))
        {   
            $whereTag2 .= " and ari.challan_no = '$challan_no' ";
        }

        if(!empty($from_date2) && !empty($to_date2))
        {   
            $from_date_arr1 = explode('-',$from_date2);  krsort($from_date_arr1); $from_date3 = implode('-',$from_date_arr1);
            $to_date_arr1 = explode('-',$to_date2);  krsort($to_date_arr1); $to_date3 = implode('-',$to_date_arr1);
            $whereTag2 .= " and date(dm.created_at) between '$from_date3' and '$to_date3'";
        }else{
            $whereTag2 .= " and date(dm.created_at) = curdate()";
        }
        #echo $whereTag;die;
        $invoice_master =   DB::select("SELECT oid.*,oids.doc_no,oids.veh_doc_no,oids.transportation_charge,oids.dispatch_ref_no,oids.no_of_cases FROM outward_inventory_pending oid
        left join outward_inventory_dispatch oids ON oids.invoice_id = oid.invoice_id
        WHERE oid.part_status is null  $whereTag");
        
       
        //$dispatch_master =   DB::select("SELECT * FROM outward_inventory_dispatch  where dispatch='1'");
        $dispatch_master =   DB::select("SELECT * FROM outward_inventory_pending dm 
        inner join outward_inventory_dispatch oid ON oid.invoice_id = dm.invoice_id
        inner JOIN outward_inventory_dispatch_particulars oidp ON dm.dispatch_id= oidp.dispatch_id WHERE dm.part_status='0' $whereTag2");
        
        #print_r($dispatch_master);die;

        
        $url = $_SERVER['APP_URL'].'/sc-dispatch-po';
        return view('sc-dispatch-po')
            ->with('invoice_master', $invoice_master)
            ->with('dispatch_master', $dispatch_master)
            ->with('job_no', $job_no)
            ->with('product', $product)
            ->with('from_date',$from_date)
            ->with('to_date',$to_date)
            ->with('challan_no',$challan_no)
            ->with('from_date2',$from_date2)
            ->with('to_date2',$from_date2)
            ->with('tab1',$tab1)
            ->with('tab2',$tab2)
            ->with('url', $url);
    }
    
    public function save_dispatch(Request $request)
    {
       # echo $request->input('transportation_charges');die;
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $dispatch_id =  $request->input('dispatch_id');
        $DispatchInventory_det = DispatchInventory::whereRaw("dispatch_id='$dispatch_id'")->first();
        $invoice_id = $DispatchInventory_det->invoice_id;
        
        
        #$invoice_det = OutwardInvoice::whereRaw("invoice_id='$invoice_id'")->first();

        $DispatchInventory = array();

        $DispatchInventory['part_status'] = '0';
        
        DB::beginTransaction();
        $msg = "";
        if(OutwardPending::whereRaw("dispatch_id='$dispatch_id'")->update($DispatchInventory))
        {
            $msg = '1';
            DB::commit();
        }
        else
        {
            $msg = '2';
            DB::rollback();
        }
        echo $msg; exit;
    }
    
    public function view_dispatch(Request $request)
    {
        Session::put("page-title","View Dispatch");
        echo "hello";die;
        $dispatch_id =  $request->input('dispatch_id');
        $invoice_arr =   DispatchInventoryScParticulars::whereRaw("`dispatch_id`='$dispatch_id'")->get();
        $dispatch_det =   ScDispatchInventory::whereRaw("`dispatch_id`='$dispatch_id'")->first();
        
        $url = $_SERVER['APP_URL'].'/ho-dispatch-po';
        return view('ho-dispatch-view-po')
        ->with('invoice_arr', $invoice_arr)
        ->with('dispatch_det', $dispatch_det)        
        ->with('url', $url);
    }
    public function edit_dispatch(Request $request)
    {
        Session::put("page-title","Edit Dispatch");
        $dispatch_id =  $request->input('dispatch_id');
        $invoice_det =   OutwardInvoiceSc::whereRaw("`dispatch_id`='$dispatch_id'")->first();
        $dispatch_det =   ScDispatchInventory::whereRaw("`dispatch_id`='$dispatch_id'")->first();
        
        $url = $_SERVER['APP_URL'].'/ho-dispatch-po';
        return view('ho-dispatch-edit-po')
        ->with('invoice_det', $invoice_det)
        ->with('dispatch_det', $dispatch_det)        
        ->with('url', $url);
    }
    
    public function update_dispatch(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $dispatch_id =  $request->input('dispatch_id');
        $eway_bill_no = addslashes($request->input('eway_bill_no'));
        $remarks = addslashes($request->input('remarks'));
        $doc_no = addslashes($request->input('doc_no'));
        $veh_doc_no = addslashes($request->input('veh_doc_no'));
        $dispatch_ref_no = addslashes($request->input('dispatch_ref_no'));
        $no_of_cases = addslashes($request->input('no_of_cases'));
        
        
        $DispatchInventory = array();
        $DispatchInventory['eway_bill_no'] = $eway_bill_no;
        $DispatchInventory['dispatch_comments'] = $remarks;
        $DispatchInventory['doc_no'] = $doc_no;
        $DispatchInventory['veh_doc_no'] = $veh_doc_no;
        $DispatchInventory['dispatch_ref_no'] = $dispatch_ref_no;
        $DispatchInventory['no_of_cases'] = $no_of_cases;
        $DispatchInventory['updated_at'] = $created_at;
        $DispatchInventory['updated_by'] = $created_by;
        
        DB::beginTransaction();
        $msg = "";
        if(DispatchInventory::whereRaw("dispatch_id='$dispatch_id'")->update($DispatchInventory))
        {
                $msg = '1';
                DB::commit();
        }
        else
        {
            $msg = '2';
            DB::rollback();
        }
        return redirect('ho-dispatch-po');   
    }
    
}

