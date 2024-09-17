<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\JobSheet;
use App\SparePart;
use App\OutwardInventoryPart;
use App\OutwardInvoice;
use App\OutwardInvoicePart;

use App\ModelMaster;
use App\ServiceCenter;
use App\LabourCharge;
use App\DispatchInventoryParticulars;
use App\DispatchInvPart;
use App\InwardInventoryPart;
use App\SCRequestInventoryPart;
use App\SCRequestInventory;
use DB;
use Auth;
use Session;


class JobsheetController extends Controller
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
    
    public function index()
    {
        Session::put("page-title","Jobsheet Creation");
        $Center_Id = Auth::user()->table_id;
        //$data_arr = DB::select("SELECT * FROM tagging_spare_part where reject='0' ");
        /*$tagging_arr = DB::select("Select * from tagging_master where TagId='101' and claim_status='1' and center_id is not null");
        
        $job_sheet_arr = array();
        foreach($tagging_arr as $tag)
        {
            $job_sheet_same = array();
            $center_id = $tag->center_id;
            $tag_id = $tag->TagId; 
            $ServiceCenter = ServiceCenter::whereRaw("center_id='$center_id'")->first();
            $job_sheet_same['asc_name'] = $ServiceCenter->center_name;
            $job_sheet_same['job_no'] = $tag->job_no;
            $job_sheet_same['brand_name'] = $tag->Brand;
            $job_sheet_same['model_name'] = $tag->Model;
            $job_sheet_same['warranty_type'] = $tag->warranty_type;
            $job_sheet_same['Serial_No'] = $tag->Serial_No;
            
            
            $job_sheet_record = array();
            $job_sheet_record = array_merge($job_sheet_record,$job_sheet_same);
            $job_sheet_record['part_name'] = '';
            $job_sheet_record['part_no'] = '';
            $job_sheet_record['qty'] = '';
            $job_sheet_record['part_charge_type'] = $tag->charge_type;
            $job_sheet_record['part_amount'] = $tag->part_amount;
            $job_sheet_record['labour_amount'] = $tag->labour_amount;
            $job_sheet_record['asc_po_no'] = $tag->asc_po_no;
            $job_sheet_record['claim_type'] = 'Labour';
            $job_sheet_arr[] = $job_sheet_record;
            
            $part_arr = DB::select("Select * from tbl_invoice_parts where tag_id='$tag_id' and claim_status='1'");
            
            foreach($part_arr as $part)
            {
                //part charge work starts from here.
                $job_sheet_record = array();
                $job_sheet_record = array_merge(array(),$job_sheet_same);
                $job_sheet_record['part_name'] = $part->part_name;
                $job_sheet_record['part_no'] = $part->part_no;
                $job_sheet_record['qty'] = $part->qty;
                $job_sheet_record['part_charge_type'] = '';
                $job_sheet_record['part_amount'] = $part->total;
                $job_sheet_record['labour_amount'] = '';
                $job_sheet_record['asc_po_no'] = $part->asc_po_no;
                $job_sheet_record['claim_type'] = 'Part';
                $job_sheet_arr[] = $job_sheet_record;
                
                //labour charge work starts from here.
                $job_sheet_record = array();
                $job_sheet_record = array_merge(array(),$job_sheet_same);
                $job_sheet_record['part_name'] = $part->part_name;
                $job_sheet_record['part_no'] = $part->part_no;
                $job_sheet_record['qty'] = $part->qty;
                $lab_id = $part->lab_id;
                $LabourCharge = LabourCharge::whereRaw("lab_id='$lab_id'")->first();
                $tag = json_encode($LabourCharge,true);
                $job_sheet_record['part_charge_type'] = $LabourCharge['symptom_name'];
                $job_sheet_record['part_amount'] = '';
                $job_sheet_record['labour_amount'] = '100';
                $job_sheet_record['asc_po_no'] = $part->asc_po_no;
                $job_sheet_record['claim_type'] = 'Labour';
                $job_sheet_arr[] = $job_sheet_record;

            }
        }*/
        
        $jobsheet_arr = DB::select("Select * from tbl_jobsheet where job_apply='0' and center_id ='$Center_Id'");
        $ServiceCenter = ServiceCenter::whereRaw("center_id='$Center_Id'")->first();
        
            
        $job_sheet_arr = array();
        foreach($jobsheet_arr as $job_det)
        {
             
            $job_sheet_record = array();
            $job_sheet_record['js_id'] = $job_det->js_id;
            $job_sheet_record['asc_name'] = $ServiceCenter->center_name;
            $job_sheet_record['job_no'] = $job_det->job_no;
            $job_sheet_record['brand_name'] = $job_det->brand_name;
            $job_sheet_record['model_name'] = $job_det->model_name;
            $job_sheet_record['warranty_type'] = $job_det->warranty_type;
            $job_sheet_record['Serial_No'] = $job_det->serial_no;
            $job_sheet_record['part_name'] = $job_det->part_name;
            $job_sheet_record['part_no'] = $job_det->part_no;
            $job_sheet_record['qty'] = $job_det->qty;
            $job_sheet_record['part_charge_type'] = $job_det->part_charge_type;
            $job_sheet_record['part_amount'] = $job_det->part_amt;
            $job_sheet_record['labour_amt'] = $job_det->labour_amt;
            $job_sheet_record['asc_po_no'] = $job_det->po_no;
            $job_sheet_record['claim_type'] = $job_det->claim_type;
            $job_sheet_arr[] = $job_sheet_record;
            
        }
        
        //print_r($job_sheet_arr); exit;
        
        //$invoice_arr = DB::select("SELECT * FROM outward_inventory_invoice ");
        $jobsheet_status_arr = DB::select("Select * from tbl_jobsheet where job_apply!='0' and center_id ='$Center_Id'");
        $url = $_SERVER['APP_URL'].'/job-sheet-view-ho';
        return view('jobsheet-view')
        ->with('job_sheet_arr', $job_sheet_arr)
       ->with('jobsheet_status_arr', $jobsheet_status_arr)        
        ->with('url', $url);
    }
    
    public function apply_job(Request $request)
    {
        //print_r($request->all()); exit;
        $job_apply = $request->input('job_apply');
        $chk_job = $request->input('chk_job'); 
        $reason = addslashes($request->input('reason')); 
        $remarks = addslashes($request->input('remarks')); 
         
        //print_r($chk_inv); exit;
        if(empty($chk_job))
        {
            echo 'Please Check To Apply Jobsheet';exit;
             return redirect("job-sheet-apply-sc");
        }
        
        $msg = "";
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        
        foreach($chk_job as $js_id)
        {
            $job_det = JobSheet::whereRaw("js_id = '$js_id' and job_apply='0'")->first();
            if(!empty($job_det->js_id))
            {
                $upd_arr = array();
                $upd_arr['job_apply'] = '1';
                $upd_arr['apply_by'] = $created_by;
                $upd_arr['apply_at'] = $created_at;
                
                
                if($job_apply==='special_approval')
                {
                    $upd_arr['reason'] = $reason;
                    $upd_arr['remarks'] = $remarks;
                    $upd_arr['special_approval'] = '1';
                }
                
                if(JobSheet::whereRaw("js_id='$js_id'")->update($upd_arr))
                {
                    Session::flash('message', "Job Applied Successfully.");
                    Session::flash('alert-class', 'alert-success');
                }
                else
                {
                    Session::flash('error', "Job Applied Failed. Please Try Again.");
                    Session::flash('alert-class', 'alert-danger');
                }
            }
        }
        return redirect("job-sheet-apply-sc");
        
    }
    
    
    
}

