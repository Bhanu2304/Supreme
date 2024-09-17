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
use App\RequestInventory;
use App\ApproveRequestInventory;
use App\ApproveRequestInventoryPart;
use App\ChallanNo;
use App\StateMaster;
use DB;
use Auth;
use Session;


class OutwardInventoryCenterController extends Controller
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
        Session::put("page-title","Outward Stock Center");

        $brand_id = $request->input('brand_id');
        $product_category = $request->input('product_category');
        $product = $request->input('product');
        $model = $request->input('model');
        $part_code = $request->input('part_code');
        $center_id = $request->input('asc_name');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $brand_id1 = $request->input('brand_id1');
        $product_category1 = $request->input('product_category1');
        $product1 = $request->input('product1');
        $model1 = $request->input('model1');
        $part_code1 = $request->input('part_code1');
        $center_id1 = $request->input('asc_name1');
        $from_date1 = $request->input('from_date1');
        $to_date1 = $request->input('to_date1');

        $tab1 = $request->input('tab1');
        $tab2 = $request->input('tab2');

        $UserType = Session::get('UserType');
        $Center_Id = Auth::user()->table_id;

        $whereUser = "";
        $whereUser2 = "";
        if($UserType!='admin' && $UserType!='Admin')
        {
            $whereUser .= " and oip.center_id ='$Center_Id'";
            $whereUser2 .= " and sri.center_id ='$Center_Id'";
            
        }

        $whereTag = "";
        $whereTag1 = "";
        if(!empty($brand_id))
        {   
            $whereTag .= " and tsp.brand_id = '$brand_id' ";
            $whereTag1 .= " and oip.brand_id = '$brand_id' ";
            
        }

        if(!empty($product_category))
        {   
            $whereTag .= " and tsp.product_category_id = '$product_category' ";
            $whereTag1 .= " and oip.product_category_id = '$product_category_id' ";
        }

        if(!empty($product))
        {   
            $whereTag .= " and tsp.product_id = '$product' ";
            $whereTag1 .= " and oip.product_id = '$product_id' ";
        }

        if(!empty($model))
        {   
            $whereTag .= " and tsp.model_id = '$model' ";
            $whereTag1 .= " and oip.model_id = '$model_id' ";
        }

        if(!empty($part_code))
        {   
            $whereTag .= " and tsp.spare_id = '$part_code' ";
            $whereTag1 .= " and oip.spare_id = '$part_code' ";
        }

        if(!empty($center_id) && $center_id != "All")
        {   
            $whereTag .= " and oip.center_id = '$center_id' ";
            $whereTag1 .= " and oip.center_id = '$center_id' ";
        }

        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(oip.created_at) between '$from_date1' and '$to_date1'";
            $whereTag1 .= " and date(oip.created_at) between '$from_date1' and '$to_date1'";
        }


        $whereTagtab = "";
        if(!empty($brand_id1))
        {   
            $whereTagtab .= " and oip.brand_id = '$brand_id1' ";
            
        }

        if(!empty($product_category1))
        {   
            $whereTagtab .= " and tsp.product_category_id = '$product_category1' ";
        }

        if(!empty($product1))
        {   

            $whereTagtab .= " and tsp.product_id = '$product1' ";
        }

        if(!empty($model1))
        {

            $whereTagtab .= " and oip.model_id = '$model1' ";
        }

        if(!empty($part_code1))
        {   
       
            $whereTagtab .= " and oip.spare_id = '$part_code1' ";
        }

        if(!empty($center_id1) && $center_id1 != "All")
        {   

            $whereTagtab .= " and sri.center_id = '$center_id1' ";
        }

        if(!empty($from_date1) && !empty($to_date1))
        {   
            $from_date_arr = explode('-',$from_date1);  krsort($from_date_arr); $from_date2 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date1);  krsort($to_date_arr); $to_date2 = implode('-',$to_date_arr);
            $whereTagtab .= " and date(sri.created_at) between '$from_date2' and '$to_date2'";
        }
        
        $po_job_qry = "";

        if(empty($whereTag))
        {
            $po_job_qry =   "SELECT oip.*,oip.created_at,tsp.spare_id,oip.po_no,oip.po_type,tsp.part_name,tsp.part_no,oip.color,tsp.hsn_code,oip.gst,
bm.brand_name,cat.category_name,pm.product_name,mm.model_name FROM outward_inventory_pending  oip INNER JOIN tbl_spare_parts tsp ON oip.spare_id = tsp.spare_id
            INNER JOIN brand_master bm ON tsp.brand_id = bm.brand_id
            INNER JOIN product_category_master cat ON tsp.product_category_id = cat.product_category_id
            INNER JOIN product_master pm ON tsp.product_id = pm.product_id
            INNER JOIN model_master mm ON tsp.model_id = mm.model_id
            WHERE oip.return_status='1'  AND DATE(oip.created_at)= CURDATE() $whereUser";

        }else{

            $po_job_qry =   "SELECT oip.*,oip.created_at,tsp.spare_id,oip.po_no,oip.po_type,tsp.part_name,tsp.part_no,oip.color,tsp.hsn_code,oip.gst,
bm.brand_name,cat.category_name,pm.product_name,mm.model_name FROM outward_inventory_pending  oip INNER JOIN tbl_spare_parts tsp ON oip.spare_id = tsp.spare_id
            INNER JOIN brand_master bm ON tsp.brand_id = bm.brand_id
            INNER JOIN product_category_master cat ON tsp.product_category_id = cat.product_category_id
            INNER JOIN product_master pm ON tsp.product_id = pm.product_id
            INNER JOIN model_master mm ON tsp.model_id = mm.model_id
            WHERE oip.return_status='1'   $whereTag $whereUser";
        }
        
        #echo $po_job_qry;exit;
        $po_job_arr =   DB::select($po_job_qry);
        foreach($po_job_arr as $part)
        {
            // echo "brand_id='$part->brand_id' and model_id='$part->model_id' and spare_id='$part->spare_id'";die;
            $inward_det = InwardInventoryPart::whereRaw("brand_id='$part->brand_id' and model_id='$part->model_id' and spare_id='$part->spare_id'")->first();
            $part->asc_amount = $inward_det->asc_amount; 
            #echo "center_id='$part->center_id'";die;
            $center_det = ServiceCenter::whereRaw("center_id='$part->center_id'")->first();
            #print_r($center_det->center_name);die;
            $part->center_name = $center_det->center_name;
            $part->hsn_code = $inward_det->hsn_code;
        }
        


        if(empty($whereTagtab))
        {
            $data_arr = DB::select("SELECT * FROM `outward_inventory_pending` sri where return_status ='0' and date(created_at) = curdate() $whereUser2");
        }else{
            
            $data_arr = DB::select("SELECT * FROM `outward_inventory_pending` sri where return_status ='0' $whereTagtab $whereUser2");
        }
        
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 

        $brand_master = array();
        
        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }

        $qry = "SELECT pm.product_id,pm.product_name FROM product_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND product_status='1'
        WHERE  pm.brand_id='$brand_id' and product_category_id='$product_category'";#die;
        $model_master = DB::select($qry);

        $qr2 = "SELECT tsc.center_id,center_name,asc_code FROM tbl_service_centre  tsc
            INNER JOIN users us ON tsc.email_id = us.email
            WHERE sc_status='1'  order by center_name"; 
        $asc_master           =   DB::select($qr2);

        $category_master = DB::select("SELECT pm.product_category_id,pm.category_name FROM product_category_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND category_status='1'
        WHERE bm.brand_id='$brand_id'");
        
        
        //print_r($brand_master); exit;
        
        $url = $_SERVER['APP_URL'].'/outward-view-po-sc';
        return view('outward-po-view-sc')
        ->with('po_job_arr', $po_job_arr)
        ->with('brand_master', $brand_master)
        ->with('brand_arr', $brand_arr)
        ->with('product_category', $product_category)
        ->with('category_master', $category_master)
        ->with('model_master', $model_master)
        ->with('product', $product)
        ->with('from_date',$from_date)
        ->with('to_date',$to_date)
        ->with('brand_id1',$brand_id1)
        ->with('from_date1',$from_date1)
        ->with('to_date1',$to_date1)
        ->with('asc_master', $asc_master)
        ->with('data_arr', $data_arr)
        ->with('tab1',$tab1)
        ->with('tab2',$tab2)    
        ->with('url', $url);
    }
    
    public function part_po_order_view(Request $request)
    {
        Session::put("page-title","Outward Stock");
        
       $tag_id = base64_decode($request->input('tag_id'));    
       $part_arr =   DB::select("SELECT * FROM `outward_inventory_pending` WHERE invoice_id='$tag_id' and return_status='1' ");
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
        
        #echo $pending_part;die;
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
        // $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        // $brand_arr = json_decode($brand_json,true);
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
            
            // $TagId = $part->tag_id;
            // echo "brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'"; exit;
            
            $Inventory = Inventory::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
            $part->bal_qty = $Inventory->bal_qty;
            
            $inward_det = InwardInventoryPart::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
            #$part->hsn_code = $inward_det->hsn_code; 
            $part->gst = $inward_det->gst; 
            $part->purchase_amt = $inward_det->purchase_amt; 
            $part->asc_amount = $inward_det->asc_amount; 
            #$part->customer_amount = $inward_det->customer_amount; 
            $part->remarks = $inward_det->remarks;

            $check_spare_part_rate = SparePart::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
            $landing_cost = $check_spare_part_rate->landing_cost;
            $part->customer_amount = $check_spare_part_rate->customer_price;
            $part->hsn_code = $check_spare_part_rate->hsn_code;
            $discount = $check_spare_part_rate->discount;
            $part_tax = $check_spare_part_rate->part_tax;
            
            $po_part_master[] = $part;
            
        }
                
        $url = $_SERVER['APP_URL'].'/outward-po-view';
        return view('outward-part-po-sc')
        ->with('po_part_master', $po_part_master)
        ->with('url', $url);
    }
    
    public function approve_part_po_sc(Request $request)
    {
        #print_r($request->all());die;
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $part_id =  $request->input('part_id');
        $remarks = addslashes($request->input('remarks'));

        $part_po_det = OutwardInventoryPart::whereRaw("invoice_id='$part_id'")->first();
        $required_part = (int)$part_po_det->req_qty;
        $issued_qty = (int)$part_po_det->issued_qty;
        $pending_part = $required_part-$issued_qty;
        #echo "update outward_inventory_pending set return_status='0' where invoice_id='$part_id'";die;
       
        $msg = "";
        
        if(DB::update("update outward_inventory_pending set return_status='0',remarks='$remarks' where invoice_id='$part_id'"))
        {

            $msg = "1";

        }
        else
        {
            $msg = "2";
        }
        
        echo $msg; exit;
    }
    
    public function cancel_part_po_sc(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $part_id =  $request->input('part_id');
        #print_r($request->all());die;
        
        $msg = "";


        if(DB::update("update outward_inventory_pending set return_status='2' where invoice_id='$part_id'"))
        {
            $msg = "1";
        }
        
        else
        {
            $msg = "2";
        }
         
        
        echo $msg; exit;
    }
}

