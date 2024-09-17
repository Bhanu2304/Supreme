<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\BrandMaster;
use App\SparePart;
use App\DispatchInventory;
use App\DispatchInventoryParticulars;
use App\OutwardInvoice;
use App\TagPart;
use App\ModelMaster;
use App\ServiceCenter;
use App\TaggingMaster;
use DB;
use Auth;
use Session;
//Checking an Event Listener
use App\Events\HODispatched;

class HODispatchInventoryController extends Controller
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
        Session::put("page-title","Dispatch Inventory");

        $challan_no = $request->input('challan_no');
        $job_no = $request->input('job_no');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $whereTag = "";

        if(!empty($challan_no))
        {   
            #$whereTag .= " and oidp.challan_no = '$challan_no' ";
        }

        if(!empty($job_no))
        {   
            $whereTag .= " and oidp.job_no = '$job_no' ";
        }

        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(dm.created_at) between '$from_date1' and '$to_date1'";
        }
        
        $invoice_master =   DB::select("SELECT * FROM outward_inventory_dispatch oid   WHERE `dispatch`='0'");
        //$dispatch_master =   DB::select("SELECT * FROM outward_inventory_dispatch  where dispatch='1'");
        $dispatch_master =   DB::select("SELECT * FROM outward_inventory_dispatch dm 
        INNER JOIN outward_inventory_dispatch_particulars oidp ON dm.dispatch_id= oidp.dispatch_id WHERE dm.dispatch='1' $whereTag");

        
        $url = $_SERVER['APP_URL'].'/ho-dispatch-po';
        return view('ho-dispatch-po')
        ->with('invoice_master', $invoice_master)
        ->with('dispatch_master', $dispatch_master)
        ->with('job_no', $job_no)
        ->with('product', $product)
        ->with('from_date',$from_date)
        ->with('to_date',$to_date)      
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
        
        $eway_bill_no = addslashes($request->input('eway_bill_no'));
        $remarks = addslashes($request->input('remarks'));
        $doc_no = addslashes($request->input('doc_no'));
        $veh_doc_no = addslashes($request->input('veh_doc_no'));
        $transportation_charge = addslashes($request->input('transportation_charges'));
        
        $dispatch_ref_no = addslashes($request->input('dispatch_ref_no'));
        $no_of_cases = addslashes($request->input('no_of_cases'));
        
        $invoice_det = OutwardInvoice::whereRaw("invoice_id='$invoice_id'")->first();
        $DispatchInventory = array();
        //$DispatchInventory['invoice_id'] = $invoice_id;
        //$DispatchInventory['invoice_no'] = $invoice_det->invoice_no;
        //$DispatchInventory['center_id'] = $invoice_det->center_id;
        //$DispatchInventory['asc_name'] = $invoice_det->asc_name;
        //$DispatchInventory['asc_code'] = $invoice_det->asc_code;
        $DispatchInventory['po_id'] = $invoice_det->po_id;
        $DispatchInventory['po_no'] = $invoice_det->po_no;
        $DispatchInventory['po_date'] = $invoice_det->po_date;
        $DispatchInventory['eway_bill_no'] = $eway_bill_no;
        $DispatchInventory['dispatch_comments'] = $remarks;
        $DispatchInventory['doc_no'] = $doc_no;
        $DispatchInventory['veh_doc_no'] = $veh_doc_no;
        $DispatchInventory['transportation_charge'] = $transportation_charge;
        $DispatchInventory['dispatch_ref_no'] = $dispatch_ref_no;
        $DispatchInventory['no_of_cases'] = $no_of_cases;
        $DispatchInventory['created_at'] = $created_at;
        $DispatchInventory['created_by'] = $created_by;
        $DispatchInventory['dispatch'] = '1';
        $center_id = $invoice_det->center_id;
        DB::beginTransaction();
        $msg = "";
        if(DispatchInventory::
                whereRaw("dispatch_id='$dispatch_id' ")
                ->update($DispatchInventory)
                )
        {
            if(OutwardInvoice::whereRaw("invoice_id='$invoice_id' and dispatch='0'")
                    ->update(
                            array('dispatch'=>'1',
                                'dispatch_id'=>$dispatch_id,
                                'dispatch_date'=>$created_at,
                                'dispatch_by'=>$created_by
                                )))
            {
                    event(new HODispatched($center_id));
                    $msg = '1';
                    DB::commit();
            }    
            else
            {
                $msg = '2';
                DB::rollback();
            }
        }
        else
        {
            $msg = '3';
            DB::rollback();
        }
        echo $msg; exit;
    }
    
    public function view_dispatch(Request $request)
    {
        Session::put("page-title","View Dispatch");
        $dispatch_id =  $request->input('dispatch_id');
        $invoice_arr =   DispatchInventoryParticulars::whereRaw("`dispatch_id`='$dispatch_id'")->get();
        $dispatch_det =   DispatchInventory::whereRaw("`dispatch_id`='$dispatch_id'")->first();
        
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
        $invoice_det =   OutwardInvoice::whereRaw("`dispatch_id`='$dispatch_id'")->first();
        $dispatch_det =   DispatchInventory::whereRaw("`dispatch_id`='$dispatch_id'")->first();
        
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

