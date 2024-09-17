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
use App\ApproveRequestInventory;
use App\OutwardInventoryPart;

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

        
        $challan_no = $request->input('challan_no2');
        $from_date2 = $request->input('from_date2');
        $to_date2 = $request->input('to_date2');
        $tab1 = $request->input('tab1');
        $tab2 = $request->input('tab2');


        $whereTag = "";

        $whereTag2 = "";

       

        if(!empty($job_no))
        {   
            $whereTag .= " and ari.req_no = '$job_no' ";
        }

        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(dm.created_at) between '$from_date1' and '$to_date1'";
        }


        if(!empty($challan_no))
        {   
            $whereTag2 .= " and ari.challan_no = '$challan_no' ";
        }

        if(!empty($from_date2) && !empty($to_date2))
        {   
            $from_date_arr1 = explode('-',$from_date2);  krsort($from_date_arr1); $from_date3 = implode('-',$from_date_arr1);
            $to_date_arr1 = explode('-',$to_date2);  krsort($to_date_arr1); $to_date3 = implode('-',$to_date_arr1);
            $whereTag2 .= " and date(ari.challan_date) between '$from_date3' and '$to_date3'";
        }else{
            $whereTag2 .= " and date(ari.challan_date) = curdate()";
        }
        $search_qry = "SELECT ari.approve_id,ari.e_way_no,ari.created_at as challan_date,ari.challan_no,bm.brand_name,ari.net_total,ari.req_no,asc1.center_name FROM 
         `approve_request_inventory` ari          
         left join brand_master bm on ari.brand_id = bm.brand_id
         left join tbl_service_centre asc1 on ari.center_id =asc1.center_id
        WHERE ari.status='1' $whereTag2  ";
        //echo $search_qry;exit;
        
        $invoice_master =   DB::select($search_qry);
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
        $eway_bill_no = addslashes($request->input('eway_bill_no'));
        $remarks = addslashes($request->input('remarks'));
        $doc_no = addslashes($request->input('doc_no'));
        $veh_doc_no = addslashes($request->input('veh_doc_no'));
        $transportation_charge = addslashes($request->input('transportation_charges'));
        
        $dispatch_ref_no = addslashes($request->input('dispatch_ref_no'));
        $no_of_cases = addslashes($request->input('no_of_cases'));
        //$Center_Id = Auth::user()->table_id;
        $approve_id =  $request->input('dispatch_id');
        $out_det  = ApproveRequestInventory::whereRaw("approve_id='$approve_id'")->first();
        $out_id = $out_det->out_id;
        
        $po_arr = DB::select("SELECT * FROM `outward_inventory` WHERE out_id = '$out_id'  LIMIT 1 "); 
        if(count($po_arr))
        {    
            foreach($po_arr as $po) 
            {
                $issued_qty = $po->issued_qty;
                $asc_amount = $po->asc_amount;
                $discount = $po->discount;
                $gst = $po->gst;
                $po_no = $po->po_no;
                $total_bill = $asc_amount*$issued_qty;
                $discount_amount = round($total_bill*$discount/100,2);
                $net_bill   = round($total_bill-$discount_amount,2);
                $gst_amount = round($net_bill*$gst/100,2);
                $grand_total = round($net_bill+$gst_amount,2);

                $column_arr = array('out_id','out_no','po_date','case_type','req_id','po_id','po_no','po_type','job_id','job_no','center_id','asc_name','asc_code','brand_id','brand_name',
            'model_id','model_name','model_id','spare_id','part_no','part_name','hsn_code',
            'gst','color','req_qty','issued_qty','asc_amount','customer_amount','discount','remarks');
                
                $DispatchInventoryParticulars = new DispatchInventoryParticulars();
                foreach($column_arr as $col)
                {
                    $DispatchInventoryParticulars->$col =  addslashes($po->$col);
                }
                $record = OutwardInvoice::whereRaw("po_no='$po_no'")->first();;
                $DispatchInventoryParticulars->dispatch_qty = $po->issued_qty;
                $DispatchInventoryParticulars->total = $record->total ;
                $DispatchInventoryParticulars->discount_amount =$record->discount_amount;
                $DispatchInventoryParticulars->net_bill =$record->net_bill;
                $DispatchInventoryParticulars->gst_amount =$record->gst_amount;
                $DispatchInventoryParticulars->grand_total =$record->grand_total;
                $DispatchInventoryParticulars->created_by =$created_by;
                $DispatchInventoryParticulars->created_at =$created_by;
                $po_type = $po->po_type;
                DB::beginTransaction();
                
                
                //print_r($sr_arr);exit;
                $new_request_no = $record->sr_no;
                //echo $new_request_no;exit;
                
                $DispatchInventoryParticulars->invoice_no = $record->invoice_no;
                $DispatchInventoryParticulars->sr_no =$record->sr_no;
                $center_id = $po->center_id;
                $sc_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
                $sc_name = $sc_det->center_name;
                $asc_code = $sc_det->asc_code;

                $invoice_id = $record->id;
                $DispatchInventoryParticulars->invoice_id = $invoice_id;
                $DispatchInventory =  new DispatchInventory();
                $DispatchInventory->invoice_id = $invoice_id;
                $DispatchInventory->invoice_no = $new_request_no;
                $DispatchInventory->center_id = $center_id;
                $DispatchInventory->asc_name = $sc_name;
                $DispatchInventory->asc_code = $asc_code;
                $DispatchInventory->po_id = $record->po_id;
                $DispatchInventory->po_no = $record->po_no;
                $DispatchInventory->po_date = $record->po_date;
                $DispatchInventory->eway_bill_no = $eway_bill_no;
                $DispatchInventory->dispatch_comments = $remarks;
                $DispatchInventory->doc_no = $doc_no;
                $DispatchInventory->veh_doc_no = $veh_doc_no;
                $DispatchInventory->transportation_charge = $transportation_charge;
                $DispatchInventory->dispatch_ref_no = $dispatch_ref_no;
                $DispatchInventory->no_of_cases = $no_of_cases;
                $DispatchInventory->created_at = $created_at;
                $DispatchInventory->created_by = $created_by;
                $DispatchInventory->dispatch = '1';
                //$DispatchInvPart->created_at = $created_at;
                //$DispatchInvPart->created_by = $created_by;

                if($DispatchInventory->save())
                {
                    $dispatch_id = $DispatchInventory->id;
                    $DispatchInventoryParticulars->dispatch_id = $dispatch_id;
                    $record_upd = array('dispatch'=>'1',
                    'dispatch_id'=>$dispatch_id,
                    'dispatch_date'=>$created_at,
                    'dispatch_by'=>$created_by
                    );

                    

                    if($DispatchInventoryParticulars->save())
                    {
                        ApproveRequestInventory::whereRaw("approve_id='$approve_id'")->update(array('status'=>'2'));
                        DB::raw('UNLOCK TABLES');
                        DB::commit();
                       echo "1";exit;
                    }
                    else
                    {
                        DB::raw('UNLOCK TABLES');
                        DB::rollback();
                        echo "2";exit;
                    }
                    
                    


                }
                else
                {
                    DB::raw('UNLOCK TABLES');
                    DB::rollback();
                    echo 'PO Not Found';exit;
                }
            }
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

