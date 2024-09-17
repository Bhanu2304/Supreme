<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SparePart;
use App\StateMaster;
use App\LabourCharge;
use Auth;
use Session;
use DB;
use App\TaggingMaster;
use App\RegionalManagerMaster;
use App\InvPart;
use App\TagPart;
use App\BrandMaster;
use App\ProductCategoryMaster;
use App\ProductMaster;
use App\ModelMaster;
use App\ServiceCenter;
use App\TaggingSparePart;

use App\ServiceEngineer;
use Illuminate\Support\Facades\Storage;


class NpcEstimateApprovalController extends Controller
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
        Session::put("page-title","View Estimate Request");
        // $part_arr =   DB::select("SELECT tm.*,count(tsp.part_id) part_count,sum(tsp.pending_parts) qty,center_name FROM tagging_master tm
        // INNER JOIN `tagging_spare_part` tsp ON tm.TagId = tsp.tag_id
        // INNER JOIN `tagging_labour_part` tlp ON tm.TagId = tlp.tag_id
        // INNER JOIN `tbl_service_centre` tsc ON tm.center_id = tsc.center_id
        // WHERE (tsp.npc_req='1' || tlp.npc_req='1') group by tm.TagId");

        $part_arr =   DB::select("SELECT tm.*,count(tsp.part_id) part_count,sum(tsp.pending_parts) qty,center_name FROM tagging_master tm
        INNER JOIN `tagging_spare_part` tsp ON tm.TagId = tsp.tag_id
        LEFT JOIN `tagging_labour_part` tlp ON tm.TagId = tlp.tag_id
        INNER JOIN `tbl_service_centre` tsc ON tm.center_id = tsc.center_id
        WHERE (tsp.npc_req='1' || tlp.npc_req='1') group by tm.TagId");
        
                
        $url = $_SERVER['APP_URL'].'/view-npc-job-request';
        return view('view-npc-job-request')
        ->with('part_arr', $part_arr)    
        ->with('url', $url);
    }
    
    public function approve_estimate_request(Request $request)
    {
        Session::put("page-title","Approve Estimate Request");
        
        $tag_id     = base64_decode($request->input('tag_id'));  
        $tagg_part = TagPart::whereRaw("tag_id='$tag_id' and npc_req='1'")->get();
        $labr_part = TaggingSparePart::whereRaw("tag_id='$tag_id' and npc_req='1'")->get();
        
        $data_json = TaggingMaster::whereRaw("TagId = '$tag_id'")->first();
        $data = json_decode($data_json,true);
        $brand_id = $data['brand_id'];
        $product_category_id = $data['product_category_id'];
        $product_id = $data['product_id'];
        $model_id = $data['model_id'];
        $part_qty = "SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_status='1' ";
        $part_arr           =   DB::select($part_qty);
        
        $lab_part = LabourCharge::selectRaw("distinct(symptom_type) symptom_type")->whereRaw("1=1")->get();
        
        $url = $_SERVER['APP_URL'].'/view-npc-job-request';
        return view('npc-job-estimate-approval')
                ->with('tagg_part',$tagg_part)
                ->with('labr_part',$labr_part)
                ->with('part_arr',$part_arr)
                ->with('lab_part',$lab_part)
                ->with('tag_id',$tag_id)
                ->with('url',$url);
    }
    
    public function save(Request $request)
    {
        $UserId = Session::get('UserId');
        $tag_id   = addslashes($request->input('tag_id'));
        $SparePart_arr   = $request->input('SparePart');
        $LabPart_arr   = $request->input('LabPart');
        $tag_details = TaggingMaster::whereRaw("TagId='$tag_id'")->first();
        
        $brand_id   = $tag_details->brand_id;
        $product_category_id   = $tag_details->product_category_id;
        $product_id   = $tag_details->product_id;
        $model_id   = $tag_details->model_id;
        
        //print_r($SparePart_arr); exit;
        $part_arr = array();
        $npc_date = date('Y-m-d H:i:s');
        $estimate_charge = 0;
        
        if(!empty($tag_details->estmt_charge))
        {
            $estimate_charge = $tag_details->estmt_charge;
        }
        $flag = true;
        
        foreach($SparePart_arr as $partId=>$part_det)
        {
            $TagPart = TagPart::whereRaw("part_id='$partId'")->first();
            $new_spare_id = addslashes($part_det['spare_id']);
            $color = addslashes($part_det['color']);
            $new_pending_parts = addslashes($part_det['pending_parts']);
            $charge_type = addslashes($part_det['charge_type']); 
            $customer_price = addslashes($part_det['customer_price']); 
            $gst_per = addslashes($part_det['gst']); 
            
            $gst = 0;
            $net_total = 0;
            if($charge_type=='Non Chargeable')
            {
                $customer_price = 0; 
                $gst = 0;
                $net_total = $total = 0;
            }
            else
            {
                $total = $customer_price*$new_pending_parts;
                $gst = round($total * $gst_per/100,2);
                $net_total = round($total+$gst);
            }
            
            $estimate_charge +=$net_total;
            
            $TagPart  =   array(); 
            $SparePart = SparePart::whereRaw("spare_id ='$new_spare_id'")->first();
            $TagPart['spare_id']= $SparePart['spare_id'];
            $TagPart['part_name']= $SparePart['part_name'];
            $TagPart['part_no']= $SparePart['part_no'];
            $TagPart['pending_parts']= $new_pending_parts;
            $TagPart['color']= $color;
            $TagPart['charge_type']= $charge_type;
            $TagPart['customer_price']= $customer_price;
            $TagPart['gst']= $gst_per;
            $TagPart['total']= $net_total;
            $TagPart['npc_req']= '2';
            $TagPart['stock_status']= 'npc esitmation made';
            $TagPart['npc_estimate_by']= $UserId;
            $TagPart['npc_estimate_date']= $npc_date;
            
            if(TagPart::whereRaw("part_id='$partId'")->update($TagPart))
            {
                
            }
            else
            {
                $flag = false;
            }
            
            
        }
        
        
        foreach($LabPart_arr as $partId=>$part_det)
        {
            //echo $partId;exit;
            $TagPart = TaggingSparePart::whereRaw("tlp_id='$partId'")->first();
            $new_spare_id = addslashes($part_det['part_no']); 
            $color = addslashes($part_det['color']);
            $new_pending_parts = addslashes($part_det['pending_parts']);
            $charge_type = addslashes($part_det['charge_type']); 
            $customer_price = addslashes($part_det['customer_price']); 
            $gst_per = addslashes($part_det['gst']); 
            
            $gst = 0;
            $net_total = 0;
            if($charge_type=='Non Chargeable')
            {
                $customer_price = 0; 
                $gst = 0;
                $net_total = $total = 0;
            }
            else
            {
                $total = $customer_price*$new_pending_parts;
                $gst = round($total * $gst_per/100,2);
                $net_total = round($total+$gst);
            }
            
            $estimate_charge +=$net_total;
            
            $TagPart  =   array(); 
            $SparePart = LabourCharge::whereRaw("lab_id ='$new_spare_id'")->first();
            $TagPart['lab_id']= $SparePart['lab_id'];
            $TagPart['symptom_type']= $SparePart['symptom_type'];
            $TagPart['symptom_name']= $SparePart['symptom_name'];
            $TagPart['pending_parts']= $new_pending_parts;
            $TagPart['color']= $color;
            $TagPart['charge_type']= $charge_type;
            $TagPart['customer_price']= $customer_price;
            $TagPart['gst']= $gst_per;
            $TagPart['total']= $net_total;
            $TagPart['npc_req']= '2';
            $TagPart['stock_status']= 'npc esitmation made';
            $TagPart['npc_estimate_by']= $UserId;
            $TagPart['npc_estimate_date']= $npc_date;
            
            if(TaggingSparePart::whereRaw("tlp_id='$partId'")->update($TagPart))
            {
                
            }
            else
            {
                $flag = false;
            }
            
            
        }
        //exit;
        if($flag)
        {
            TaggingMaster::whereRaw("TagId='$tag_id'")->update(array('estmt_charge'=>$estimate_charge));
            
            Session::flash('message', "Estimation Submitted Successfully.");
            Session::flash('alert-class', 'alert-success'); 
        }
        else
        {
           Session::flash('message', "Estimation Submition Failed.");
           Session::flash('alert-class', 'alert-success'); 
        }
        
        return redirect("view-npc-job-request"); 
        
     exit;
    }
    
    
    
    
}

