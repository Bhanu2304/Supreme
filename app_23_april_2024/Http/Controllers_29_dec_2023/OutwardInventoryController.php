<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\BrandMaster;
use App\SparePart;
use App\OutwardInventoryPart;
use App\TagPart;
use App\ModelMaster;
use App\ServiceCenter;
use App\TaggingMaster;
use App\InwardInventoryPart;
use App\InwardInventory;
use App\SCRequestInventoryPart;
use App\SCRequestInventory;
use App\Inventory;
use DB;
use Auth;
use Session;


class OutwardInventoryController extends Controller
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
        Session::put("page-title","Outward Stock");
        $po_job_arr =   DB::select("SELECT * FROM tagging_master WHERE part_status='1' ");
        $po_sc_arr =   DB::select("SELECT * FROM `sc_request_inventory` WHERE part_status_pending ='1'");
        
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 

        $brand_master = array();
        
        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }
        
        $data_arr = DB::select("SELECT * FROM `outward_inventory`");
        
        //print_r($brand_master); exit;
        
        $url = $_SERVER['APP_URL'].'/outward-view-po';
        return view('outward-po-view')
        ->with('po_job_arr', $po_job_arr)
        ->with('po_sc_arr', $po_sc_arr)
        ->with('brand_master', $brand_master)
        ->with('data_arr', $data_arr)        
        ->with('url', $url);
    }
    
    public function part_po_order_view(Request $request)
    {
        Session::put("page-title","Outward Stock");
        
       $tag_id = base64_decode($request->input('tag_id'));    
       $part_arr =   DB::select("SELECT * FROM `tagging_spare_part` WHERE tag_id='$tag_id' and part_po_no is not null and pending_status ='1' and ho_reject='0'");
        //$brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        //$brand_arr = json_decode($brand_json,true);
        $po_part_master = array();
        foreach($part_arr as $part)
        {
            $brand_id = $part->brand_id;
            $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
            $part->brand = $brand_det->brand_name;
            
            $model_id = $part->model_id;
            $model_det = ModelMaster::whereRaw("model_id='$model_id'")->first();
            $part->model = $model_det->model_name;
            
            $spare_id = $part->spare_id;
            $part_det = SparePart::whereRaw("spare_id='$spare_id'")->first();
            $part->part_name = $part_det->part_name;
            
            
            $center_id = $part->center_id;
            $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
            $part->center_name = $center_det->center_name; 
            $part->asc_code = $center_det->asc_code;
            
            $TagId = $part->tag_id;
            $tag_det = TaggingMaster::whereRaw("TagId='$TagId'")->first();
            $part->job_no = $tag_det->job_no; 
            
            
            //$TagId = $part->tag_id;
            //echo "brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'"; exit;
            $Inventory = Inventory::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
            $part->bal_qty = $Inventory->bal_qty;
            
            
            $inward_det = InwardInventoryPart::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
            $part->hsn_code = $inward_det->hsn_code; 
            $part->gst = $inward_det->gst; 
            $part->purchase_amt = $inward_det->purchase_amt; 
            $part->asc_amount = $inward_det->asc_amount; 
            $part->customer_amount = $inward_det->customer_amount; 
            $part->remarks = $inward_det->remarks; 
            
            $po_part_master[] = $part;
            
        }
                
                
        $url = $_SERVER['APP_URL'].'/outward-po-view';
        return view('outward-part-po-job')
        ->with('po_part_master', $po_part_master)
        ->with('url', $url);
    }
    
    public function approve_part_po(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $part_id =  $request->input('part_id');
        $issued_qty = addslashes($request->input('issued_qty'));
        $discount = addslashes($request->input('discount'));
        $remarks = addslashes($request->input('remarks'));
        
        $part_po_det = TagPart::whereRaw("part_id='$part_id'")->first();
        $required_part = (int)$part_po_det->pending_parts;
        $issued_qty = (int) $issued_qty;
        $pending_part = $required_part-$issued_qty;
        
        $OutwardInventoryPart = new OutwardInventoryPart();
        $OutwardInventoryPart->po_no = $part_po_det->part_po_no;
        $OutwardInventoryPart->po_date = $part_po_det->part_po_date;
        $OutwardInventoryPart->po_type = $part_po_det->po_type;
        $OutwardInventoryPart->case_type = 'Job Case';
        $OutwardInventoryPart->job_id = $part_po_det->tag_id;
        $TagId = $part_po_det->tag_id;
        $tag_det = TaggingMaster::whereRaw("TagId='$TagId'")->first();
        $OutwardInventoryPart->job_no = $tag_det->job_no; 
        
        $center_id = $part_po_det->center_id;
        $OutwardInventoryPart->center_id = $center_id;
        $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
        $OutwardInventoryPart->asc_name = $center_det->center_name; 
        $OutwardInventoryPart->asc_code = $center_det->asc_code;
        
        $brand_id = $part_po_det->brand_id;
        $OutwardInventoryPart->brand_id =  $brand_id;
        $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
        $OutwardInventoryPart->brand_name =  $brand_det->brand_name;
        //$OutwardInventoryPart->product_category_id = $part_po_det->product_category_id;
        //$OutwardInventoryPart->product_id = $part_po_det->product_id; 
        
        $model_id = $part_po_det->model_id;
        $OutwardInventoryPart->model_id = $model_id;
        $model_det = ModelMaster::whereRaw("model_id='$model_id'")->first();
        $OutwardInventoryPart->model_name = $model_det->model_name;
        $OutwardInventoryPart->spare_id = $part_po_det->spare_id; 
        $OutwardInventoryPart->part_name = $part_po_det->part_name;
        $OutwardInventoryPart->part_no = $part_po_det->part_no;
        $OutwardInventoryPart->color = $part_po_det->color;
        
        $spare_id = $part_po_det->spare_id;
        $inward_det = InwardInventoryPart::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
        $OutwardInventoryPart->hsn_code = $inward_det->hsn_code; 
        $OutwardInventoryPart->gst = $inward_det->gst; 
        $OutwardInventoryPart->req_qty = $required_part; 
        $OutwardInventoryPart->issued_qty = $issued_qty;
        $OutwardInventoryPart->discount = $discount;
        $OutwardInventoryPart->remarks = $remarks;
        $OutwardInventoryPart->req_id = $inward_det->part_inw_id;
        $OutwardInventoryPart->po_id = $inward_det->inw_id;
        //$OutwardInventoryPart->purchase_amt = $inward_det->purchase_amt; 
        $OutwardInventoryPart->asc_amount = $inward_det->asc_amount; 
        $OutwardInventoryPart->customer_amount = $inward_det->customer_amount; 
        
        
        DB::beginTransaction();
        
        if($OutwardInventoryPart->save())
        {
            $msg = "";
            if($pending_part<=0)
            {
                TagPart::whereRaw("part_id='$part_id'")->update(array('pending_parts'=>'0','pending_status'=>'0','part_status'=>'approved'));
                $msg = "1";
            }
            else if($pending_part>0)
            {
                TagPart::whereRaw("part_id='$part_id'")->update(array('pending_parts'=>$pending_part));
                $msg = "2";
            }
            else
            {
                $msg = " Part Details Not Found.";
            }
         
            $balance_part_det = TagPart::whereRaw("tag_id='$TagId'")->get();

            $part_required = 0;
            foreach($balance_part_det as $bal_det)
            {
                $part_required +=$bal_det->pending_parts;
            }

            $Inventory = Inventory::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
            $bal_qty = $Inventory->bal_qty;
            
            if($bal_qty>=$issued_qty)
            {
                if(!TaggingMaster::whereRaw("TagId='$TagId'")->update(array('part_pending'=>$part_required)))
                {
                    DB::rollback();
                    echo 'Job Case Details Not Found';exit;
                }
                
                if($part_required==0)
                {
                    if(!TaggingMaster::whereRaw("TagId='$TagId'")->update(array('part_status'=>'0')))
                    {
                        DB::rollback();
                        echo 'Failed To Update Job Details';exit;
                    }
                }
                $new_bal_qty = $bal_qty-$issued_qty;
                if(Inventory::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->update(array('bal_qty'=>$new_bal_qty)))
                {
                    
                    DB::commit();
                    echo $msg;exit;
                }
                else
                {
                    DB::rollback();
                        echo 'Inventory Details For Part Not Found';exit;
                }
                
                
            }
            else
            {
                $msg = "Stock Not Available";
                DB::rollback();
            }
        }
        else
        {
            $msg = "Stock Approval Failed. Please Try Again.";
            DB::rollback();
        }
        
        echo $msg; exit;
    }
    
    public function cancel_part_po(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $part_id =  $request->input('part_id');
        $issued_qty = addslashes($request->input('issued_qty'));
        $discount = addslashes($request->input('discount'));
        $remarks = addslashes($request->input('remarks'));
        
        $part_po_det = TagPart::whereRaw("part_id='$part_id'")->first();
        $required_part = (int)$part_po_det->pending_parts;
        //$issued_qty = (int) $issued_qty;
        //$pending_part = $required_part-$issued_qty;
        
        $OutwardInventoryPart = new OutwardInventoryPart();
        $OutwardInventoryPart->po_no = $part_po_det->part_po_no;
        $OutwardInventoryPart->po_date = $part_po_det->part_po_date;
        $OutwardInventoryPart->po_type = $part_po_det->po_type;
        $OutwardInventoryPart->case_type = 'Job Case';
        $OutwardInventoryPart->job_id = $part_po_det->tag_id;
        $TagId = $part_po_det->tag_id;
        $tag_det = TaggingMaster::whereRaw("TagId='$TagId'")->first();
        $OutwardInventoryPart->job_no = $tag_det->job_no; 
        
        $center_id = $part_po_det->center_id;
        $OutwardInventoryPart->center_id = $center_id;
        $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
        $OutwardInventoryPart->asc_name = $center_det->center_name; 
        $OutwardInventoryPart->asc_code = $center_det->asc_code;
        
        $brand_id = $part_po_det->brand_id;
        $OutwardInventoryPart->brand_id =  $brand_id;
        $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
        $OutwardInventoryPart->brand_name =  $brand_det->brand_name;
        //$OutwardInventoryPart->product_category_id = $part_po_det->product_category_id;
        //$OutwardInventoryPart->product_id = $part_po_det->product_id; 
        
        $model_id = $part_po_det->model_id;
        $OutwardInventoryPart->model_id = $model_id;
        $model_det = ModelMaster::whereRaw("model_id='$model_id'")->first();
        $OutwardInventoryPart->model_name = $model_det->model_name;
        $OutwardInventoryPart->spare_id = $part_po_det->spare_id; 
        $OutwardInventoryPart->part_name = $part_po_det->part_name;
        $OutwardInventoryPart->part_no = $part_po_det->part_no;
        $OutwardInventoryPart->color = $part_po_det->color;
        
        $spare_id = $part_po_det->spare_id;
        $inward_det = InwardInventoryPart::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
        $OutwardInventoryPart->hsn_code = $inward_det->hsn_code; 
        $OutwardInventoryPart->gst = $inward_det->gst; 
        $OutwardInventoryPart->req_qty = $required_part; 
        $OutwardInventoryPart->issued_qty = $issued_qty;
        $OutwardInventoryPart->discount = $discount;
        $OutwardInventoryPart->remarks = $remarks;
        $OutwardInventoryPart->reject = '1';
        //$OutwardInventoryPart->purchase_amt = $inward_det->purchase_amt; 
        $OutwardInventoryPart->asc_amount = $inward_det->asc_amount; 
        $OutwardInventoryPart->customer_amount = $inward_det->customer_amount; 
        
        
        DB::beginTransaction();
        
        $OutwardInventoryPart->save();
        
        
        
        $msg = "";
        if(TagPart::whereRaw("part_id='$part_id'")->update(array('ho_reject'=>'1','part_status'=>'reject')))
        {
            $msg = "1";
        }
        
        else
        {
            $msg = "3";
        }
         
        $balance_part_det = TagPart::whereRaw("tag_id='$TagId' and ho_reject='0'")->get();
        
        $part_required = 0;
        foreach($balance_part_det as $bal_det)
        {
            $part_required +=$bal_det->pending_parts;
        }
        
        TaggingMaster::whereRaw("TagId='$TagId' ")->update(array('part_pending'=>$part_required));
        
        if($part_required==0)
        {
            TaggingMaster::whereRaw("TagId='$TagId'")->update(array('part_status'=>'0'));
        }
        
        DB::commit();
        
        echo $msg; exit;
    }
    
    public function sc_po_order_view(Request $request)
    {
        Session::put("page-title","Outward Stock");
        
       $req_id = base64_decode($request->input('req_id'));    
        $part_arr =   DB::select("SELECT * FROM sc_request_inventory_particulars WHERE req_id='$req_id' and pending_status ='1' and qty_cancel='0'");
        //$brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        //$brand_arr = json_decode($brand_json,true);
        $po_part_master = array();
        foreach($part_arr as $part)
        {
            $brand_id = $part->brand_id;
            $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
            $part->brand = $brand_det->brand_name;
            
            $model_id = $part->model_id;
            $model_det = ModelMaster::whereRaw("model_id='$model_id'")->first();
            $part->model = $model_det->model_name;
            
            $spare_id = $part->spare_id;
            $part_det = SparePart::whereRaw("spare_id='$spare_id'")->first();
            $part->part_name = $part_det->part_name;
            
            
            $center_id = $part->center_id;
            $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
            $part->center_name = $center_det->center_name; 
            $part->asc_code = $center_det->asc_code;
            
            $TagId = $part->tag_id;
            $tag_det = TaggingMaster::whereRaw("TagId='$TagId'")->first();
            $part->job_no = $tag_det->job_no; 
            
            
            //$TagId = $part->tag_id;
            //echo "brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'"; exit;
            
            $Inventory = Inventory::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
            $part->bal_qty = $Inventory->bal_qty;
            
            $inward_det = InwardInventoryPart::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
            $part->hsn_code = $inward_det->hsn_code; 
            $part->gst = $inward_det->gst; 
            $part->purchase_amt = $inward_det->purchase_amt; 
            $part->asc_amount = $inward_det->asc_amount; 
            $part->customer_amount = $inward_det->customer_amount; 
            $part->remarks = $inward_det->remarks; 
            
            $po_part_master[] = $part;
            
        }
                
                
        $url = $_SERVER['APP_URL'].'/outward-po-view';
        return view('outward-part-po-sc')
        ->with('po_part_master', $po_part_master)
        ->with('url', $url);
    }
    
    public function approve_part_po_sc(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $part_id =  $request->input('part_id');
        $issued_qty = addslashes($request->input('issued_qty'));
        $discount = addslashes($request->input('discount'));
        $remarks = addslashes($request->input('remarks'));
        
        $part_po_det = SCRequestInventoryPart::whereRaw("req_part_id='$part_id' ")->first();
        $required_part = (int)$part_po_det->qty_pending;
        $issued_qty = (int) $issued_qty;
        $pending_part = $required_part-$issued_qty; 
        
        $OutwardInventoryPart = new OutwardInventoryPart();
        $OutwardInventoryPart->po_no = $part_po_det->req_no;
        $OutwardInventoryPart->po_date = $part_po_det->req_date;
        $OutwardInventoryPart->po_type = $part_po_det->po_type;
        $OutwardInventoryPart->case_type = 'PO';
        $OutwardInventoryPart->job_id = $part_po_det->req_id;
        $OutwardInventoryPart->color = $part_po_det->color; 
        $req_id = $part_po_det->req_id;
        //$tag_det = TaggingMaster::whereRaw("TagId='$TagId'")->first();
        //$OutwardInventoryPart->job_no = $tag_det->job_no; 
        
        $center_id = $part_po_det->center_id;
        $OutwardInventoryPart->center_id = $center_id;
        $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
        $OutwardInventoryPart->asc_name = $center_det->center_name; 
        $OutwardInventoryPart->asc_code = $center_det->asc_code;
        
        $brand_id = $part_po_det->brand_id;
        $OutwardInventoryPart->brand_id =  $brand_id;
        $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
        $OutwardInventoryPart->brand_name =  $brand_det->brand_name;
        //$OutwardInventoryPart->product_category_id = $part_po_det->product_category_id;
        //$OutwardInventoryPart->product_id = $part_po_det->product_id; 
        
        $model_id = $part_po_det->model_id;
        $OutwardInventoryPart->model_id = $model_id;
        $model_det = ModelMaster::whereRaw("model_id='$model_id'")->first();
        $OutwardInventoryPart->model_name = $model_det->model_name;
        $OutwardInventoryPart->spare_id = $part_po_det->spare_id; 
        $OutwardInventoryPart->part_name = $part_po_det->part_name;
        $OutwardInventoryPart->part_no = $part_po_det->part_no;
        $OutwardInventoryPart->color = $part_po_det->color;
        
        $spare_id = $part_po_det->spare_id;
        $inward_det = InwardInventoryPart::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
        $OutwardInventoryPart->hsn_code = $inward_det->hsn_code; 
        $OutwardInventoryPart->gst = $inward_det->gst; 
        $OutwardInventoryPart->req_id = $inward_det->part_inw_id;
        $OutwardInventoryPart->po_id = $inward_det->inw_id;
        $OutwardInventoryPart->req_qty = $required_part; 
        $OutwardInventoryPart->issued_qty = $issued_qty;
        $OutwardInventoryPart->discount = $discount;
        $OutwardInventoryPart->remarks = $remarks;
        //$OutwardInventoryPart->purchase_amt = $inward_det->purchase_amt; 
        $OutwardInventoryPart->asc_amount = $inward_det->asc_amount; 
        $OutwardInventoryPart->customer_amount = $inward_det->customer_amount; 
        
        
        DB::beginTransaction();
        
        if($OutwardInventoryPart->save())
        {
            $msg = "";
            if($pending_part<=0)
            {
                SCRequestInventoryPart::whereRaw("req_part_id='$part_id'")->update(array('qty_pending'=>'0','pending_status'=>'0'));
                $msg = "1";
            }
            else if($pending_part>0)
            {
                SCRequestInventoryPart::whereRaw("req_part_id='$part_id'")->update(array('qty_pending'=>$pending_part));
                $msg = "2";
            }
            else
            {
                $msg = "3";
            }

            $balance_part_det = SCRequestInventoryPart::whereRaw("req_id='$req_id'")->get();

            $part_required = 0;
            foreach($balance_part_det as $bal_det)
            {
                $part_required +=$bal_det->qty_pending;
            }

            SCRequestInventory::whereRaw("req_id='$req_id'")->update(array('qty_pending'=>$part_required));
            if($part_required==0)
            {
                SCRequestInventory::whereRaw("req_id='$req_id'")->update(array('part_status_pending'=>'0'));
            }
            
            $Inventory = Inventory::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
            $bal_qty = (int)$Inventory->bal_qty;
            
            
            if($bal_qty>=$issued_qty)
            {
                $new_bal_qty = $bal_qty-$issued_qty;
                if(Inventory::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->update(array('bal_qty'=>$new_bal_qty)))
                {
                    DB::commit();
                    echo $msg;exit;
                }
                else
                {
                    DB::rollback();
                    echo 'Inventory Details For Part Not Found';exit;
                }
            }
            else
            {
                DB::rollback();
                echo 'Less Stock Available.';exit;
            }
            
            
        }
        else
        {
            DB::rollback();
            echo 'PO Details Not Found';exit;
            
        }
        
        
        
        
        
        echo $msg; exit;
    }
    
    public function cancel_part_po_sc(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $part_id =  $request->input('part_id');
        //$issued_qty = addslashes($request->input('issued_qty'));
        //$discount = addslashes($request->input('discount'));
        $remarks = addslashes($request->input('remarks'));
        
        $part_po_det = SCRequestInventoryPart::whereRaw("req_part_id='$part_id'")->first();
        $required_part = (int)$part_po_det->qty_pending;
        //$issued_qty = (int) $issued_qty;
        //$pending_part = $required_part-$issued_qty; 
        
        $OutwardInventoryPart = new OutwardInventoryPart();
        $OutwardInventoryPart->po_no = $part_po_det->req_no;
        $OutwardInventoryPart->po_date = $part_po_det->req_date;
        $OutwardInventoryPart->po_type = $part_po_det->po_type;
        $OutwardInventoryPart->case_type = 'PO';
        $OutwardInventoryPart->job_id = $part_po_det->req_id;
        $OutwardInventoryPart->color = $part_po_det->color; 
        $req_id = $part_po_det->req_id;
        //$tag_det = TaggingMaster::whereRaw("TagId='$TagId'")->first();
        //$OutwardInventoryPart->job_no = $tag_det->job_no; 
        
        $center_id = $part_po_det->center_id;
        $OutwardInventoryPart->center_id = $center_id;
        $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
        $OutwardInventoryPart->asc_name = $center_det->center_name; 
        $OutwardInventoryPart->asc_code = $center_det->asc_code;
        
        $brand_id = $part_po_det->brand_id;
        $OutwardInventoryPart->brand_id =  $brand_id;
        $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
        $OutwardInventoryPart->brand_name =  $brand_det->brand_name;
        //$OutwardInventoryPart->product_category_id = $part_po_det->product_category_id;
        //$OutwardInventoryPart->product_id = $part_po_det->product_id; 
        
        $model_id = $part_po_det->model_id;
        $OutwardInventoryPart->model_id = $model_id;
        $model_det = ModelMaster::whereRaw("model_id='$model_id'")->first();
        $OutwardInventoryPart->model_name = $model_det->model_name;
        $OutwardInventoryPart->spare_id = $part_po_det->spare_id; 
        $OutwardInventoryPart->part_name = $part_po_det->part_name;
        $OutwardInventoryPart->part_no = $part_po_det->part_no;
        $OutwardInventoryPart->color = $part_po_det->color;
        
        $spare_id = $part_po_det->spare_id;
        $inward_det = InwardInventoryPart::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
        $OutwardInventoryPart->hsn_code = $inward_det->hsn_code; 
        $OutwardInventoryPart->gst = $inward_det->gst; 
        
        $OutwardInventoryPart->req_qty = $required_part; 
        $OutwardInventoryPart->issued_qty = $issued_qty;
        $OutwardInventoryPart->discount = $discount;
        $OutwardInventoryPart->remarks = $remarks;
        $OutwardInventoryPart->reject = '1';
        //$OutwardInventoryPart->purchase_amt = $inward_det->purchase_amt; 
        $OutwardInventoryPart->asc_amount = $inward_det->asc_amount; 
        $OutwardInventoryPart->customer_amount = $inward_det->customer_amount; 
        
        
        DB::beginTransaction();
        
        $OutwardInventoryPart->save();
        
        
        
        $msg = "";
        if(SCRequestInventoryPart::whereRaw("req_part_id='$part_id'")->update(array('qty_cancel'=>'1')))
        {
            $msg = "1";
        }
        
        else
        {
            $msg = "3";
        }
         
        $balance_part_det = SCRequestInventoryPart::whereRaw("req_id='$req_id' and qty_cancel='0'")->get();
        
        $part_required = 0;
        foreach($balance_part_det as $bal_det)
        {
            $part_required +=$bal_det->qty_pending;
        }
        
        SCRequestInventory::whereRaw("req_id='$req_id'")->update(array('qty_pending'=>$part_required));
        
        if($part_required==0)
        {
            SCRequestInventory::whereRaw("req_id='$req_id'")->update(array('part_status_pending'=>'0'));
        }
        
        DB::commit();
        
        echo $msg; exit;
    }
}

