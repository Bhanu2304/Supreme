<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\BrandMaster;
use App\SparePart;
use App\DispatchInventory;
use App\DispatchInventoryParticulars;
use App\OutwardInvoice;
use App\TagPart;
use App\InvPart;
use App\ModelMaster;
use App\ServiceCenter;
use App\ReturnInvoicePart;




use DB;
use Auth;
use Session;


class HOReturnApprovalPendingController extends Controller
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
        Session::put("page-title","Approval Pending Cases");
        
        $short_return_master =   DB::select("SELECT * FROM outward_inventory_pending  where return_type='short' and approval_status='0'");
        $fault_return_master =   DB::select("SELECT * FROM outward_inventory_pending  where (return_type='faulty' or return_type='mismatch') and approval_status='0'");
        
        $url = $_SERVER['APP_URL'].'/ho-return-approval-pending';
        return view('view-return-po-sc')
        ->with('short_return_master', $short_return_master)
        ->with('fault_return_master', $fault_return_master)        
        ->with('url', $url);
    }
    
    public function approve_short(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $return_id =  $request->input('return_id');
        $ReturnInvoicePart = ReturnInvoicePart::whereRaw("return_id='$return_id'")->first();
        $invoice_id = $ReturnInvoicePart->invoice_id;
        
        $invoice_det = ReturnInvoicePart::whereRaw("invoice_id='$invoice_id'")->first();
        $DispatchInventoryParticulars = new DispatchInventoryParticulars();
        $DispatchInventoryParticulars->invoice_id = $invoice_id;
        $DispatchInventoryParticulars->invoice_no = $invoice_det->invoice_no;
        $DispatchInventoryParticulars->sr_no = $invoice_det->sr_no;
        $DispatchInventoryParticulars->out_id = $invoice_det->out_id;
        $DispatchInventoryParticulars->out_no = $invoice_det->out_no;
        $DispatchInventoryParticulars->po_date = $invoice_det->po_date;
        $DispatchInventoryParticulars->case_type = $invoice_det->case_type;
        $DispatchInventoryParticulars->req_id = $invoice_det->req_id;
        $DispatchInventoryParticulars->po_id = $invoice_det->po_id;
        $DispatchInventoryParticulars->po_no = $invoice_det->po_no;
        $DispatchInventoryParticulars->po_type = $invoice_det->po_type;
        $DispatchInventoryParticulars->job_id = $invoice_det->job_id;
        $DispatchInventoryParticulars->job_no = $invoice_det->job_no;
        $DispatchInventoryParticulars->center_id = $invoice_det->center_id;
        
        $DispatchInventoryParticulars->asc_name = $invoice_det->asc_name;
        $DispatchInventoryParticulars->asc_code = $invoice_det->asc_code;
        $DispatchInventoryParticulars->brand_id = $invoice_det->brand_id;
        $DispatchInventoryParticulars->brand_name = $invoice_det->brand_name;
        $DispatchInventoryParticulars->model_id = $invoice_det->model_id;
        $DispatchInventoryParticulars->model_name = $invoice_det->model_name;
        $DispatchInventoryParticulars->spare_id = $invoice_det->spare_id;
        $DispatchInventoryParticulars->part_no = $invoice_det->part_no;
        $DispatchInventoryParticulars->part_name = $invoice_det->part_name;
        $DispatchInventoryParticulars->hsn_code = $invoice_det->hsn_code;
        $DispatchInventoryParticulars->gst = $invoice_det->gst;
        $DispatchInventoryParticulars->color = $invoice_det->color;
        $DispatchInventoryParticulars->req_qty = $invoice_det->req_qty;
        
        $DispatchInventoryParticulars->issued_qty = $invoice_det->issued_qty;
        $DispatchInventoryParticulars->asc_amount = $invoice_det->asc_amount;
        $DispatchInventoryParticulars->dispatch_qty = $invoice_det->dispatch_qty;
        $DispatchInventoryParticulars->customer_amount = $invoice_det->customer_amount;
        $DispatchInventoryParticulars->discount = $invoice_det->discount;
        $DispatchInventoryParticulars->total = $invoice_det->total;
        $DispatchInventoryParticulars->discount_amount = $invoice_det->discount_amount;
        $DispatchInventoryParticulars->net_bill = $invoice_det->net_bill;
        $DispatchInventoryParticulars->gst_amount = $invoice_det->gst_amount;
        $DispatchInventoryParticulars->grand_total = $invoice_det->grand_total;
        $DispatchInventoryParticulars->remarks = $invoice_det->remarks;
        
        
        $DispatchInventoryParticulars->created_at = $created_at;
        $DispatchInventoryParticulars->created_by = $created_by;
        
        $DispatchInventory =  new DispatchInventory();
        $DispatchInventory->invoice_id = $invoice_id;
        $DispatchInventory->invoice_no = $invoice_det->invoice_no;
        $DispatchInventory->center_id = $invoice_det->center_id;
        $DispatchInventory->asc_name = $invoice_det->asc_name;
        $DispatchInventory->asc_code = $invoice_det->asc_code;
        
        
        DB::beginTransaction();
        $msg = "";
        if($DispatchInventory->save())
        {
            $dispatch_id = $DispatchInventory->id;
            $DispatchInventoryParticulars->dispatch_id = $dispatch_id;
            
            
            if($DispatchInventoryParticulars->save())
            {
                if(ReturnInvoicePart::whereRaw("return_id='$return_id'")->update(array('approval_status'=>'1','short_approve_by'=>$created_by,'short_approve_date'=>$created_at)))
                {
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
    
    
    public function cancel_short(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $return_id =  $request->input('return_id');
        $msg = "";
        if(ReturnInvoicePart::whereRaw("return_id='$return_id'")->update(array('approval_status'=>'2','short_cancel_date'=>$created_at,'short_cancel_by'=>$created_by)))
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
    
    public function approve_fault(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $return_id =  $request->input('return_id');
        DB::beginTransaction();
        $msg = "";
        if(ReturnInvoicePart::whereRaw("return_id='$return_id'")->update(array('approval_status'=>'3','fault_approve_by'=>$created_by,'fault_approve_date'=>$created_at)))
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
    
    public function cancel_fault(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $return_id =  $request->input('return_id');
        $msg = "";
        if(ReturnInvoicePart::whereRaw("return_id='$return_id'")->update(array('approval_status'=>'2','fault_cancel_by'=>$created_by,'fault_cancel_date'=>$created_at)))
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
    
    public function srn_dispatch(Request $request)
    {
        Session::put("page-title","SRN Dispatched Cases");
        
        $srn_master =   DB::select("SELECT * FROM outward_inventory_pending  where (return_type='Defective' or return_type='faulty' or return_type='mismatch' or return_type='Mismatched') and approval_status='3'");
        $srn_master_view =   DB::select("SELECT * FROM outward_inventory_pending  where (return_type='Defective' or return_type='faulty' or return_type='mismatch' or return_type='Mismatched')  and approval_status='4'");
        
        $url = $_SERVER['APP_URL'].'/ho-srn-dispatch-cases';
        return view('ho-srn-dispatch-cases')
        ->with('short_return_master', $srn_master)
        ->with('srn_master_view', $srn_master_view)               
        ->with('url', $url);
    }
    
    public function approve_srn(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $return_id =  $request->input('return_id');
        $part_status =  addslashes($request->input('part_status'));
        $bin_no =  addslashes($request->input('bin_no'));
        
        
        
        DB::beginTransaction();
         $msg = "";
        if(ReturnInvoicePart::whereRaw("return_id='$return_id'")->update(array('approval_status'=>'4','part_status'=>$part_status,'bin_no'=>$bin_no,'srn_approve_by'=>$created_by,'srn_approve_date'=>$created_at)))
        {
            $return_det = ReturnInvoicePart::whereRaw("return_id='$return_id'")->first();
            $case_type = $return_det->case_type;
            if($case_type=='Job Case')
            {
               $inv_id =  $return_det->po_id;
               if(InvPart::whereRaw("part_allocate_id='$part_allocate_id'")
                    ->update(
                            array('request_to_ho'=>'2',
                                'approval_date'=>"'".date('Y-m-d H:i:s')."'",
                                'part_allocated'=>'0',
                                'srn_status'=>'3')))
               {
                   $msg = '1';
            DB::commit();
               }
            }
            $msg = '2';
            DB::rollback();
        }
        else
        {
            
        }
        
        echo $msg; exit;
       
        if($DispatchInventory->save())
        {
            $dispatch_id = $DispatchInventory->id;
            $DispatchInventoryParticulars->dispatch_id = $dispatch_id;
            
            
            if($DispatchInventoryParticulars->save())
            {
                if(ReturnInvoicePart::whereRaw("return_id='$return_id'")->update(array('approval_status'=>'4')))
                {
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
    
    
    public function cancel_srn(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $return_id =  $request->input('return_id');
        $msg = "";
        if(ReturnInvoicePart::whereRaw("return_id='$return_id'")->update(array('approval_status'=>'2')))
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
}

