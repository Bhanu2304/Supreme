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
use App\RequestInventoryPart;
use App\SCRequestInventory;
use App\SCRequestInventoryPart;
use App\Inventory;
use App\RequestInventory;
use App\ApproveRequestInventory;
use App\ApproveRequestInventoryPart;
use App\ChallanNo;
use App\StateMaster;
use App\OutwardInventorySc;
use App\InwardInventoryPartSc;
use App\OutwardInvoiceSc;
use App\DispatchInventoryScParticulars;
use App\ScDispatchInventory;
use App\RequestInventorySc;
use DB;
use Auth;
use Session;
use App\InventoryItemList;


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
    
    public function index(Request $request)
    {
        Session::put("page-title","Outward Stock");

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

        $whereTag = "";
        $whereTag1 = "";
        if(!empty($brand_id))
        {   
            $whereTag .= " and tsp.brand_id = '$brand_id' ";
            $whereTag1 .= " and sri.brand_id = '$brand_id' ";
            
        }

        if(!empty($product_category))
        {   
            $whereTag .= " and tsp.product_category_id = '$product_category' ";
            $whereTag1 .= " and sri.product_category_id = '$product_category_id' ";
        }

        if(!empty($product))
        {   
            $whereTag .= " and tsp.product_id = '$product' ";
            $whereTag1 .= " and sri.product_id = '$product_id' ";
        }

        if(!empty($model))
        {   
            $whereTag .= " and tsp.model_id = '$model' ";
            $whereTag1 .= " and sri.model_id = '$model_id' ";
        }

        if(!empty($part_code))
        {   
            $whereTag .= " and tsp.spare_id = '$part_code' ";
            $whereTag1 .= " and srip.spare_id = '$part_code' ";
        }

        if(!empty($center_id) && $center_id != "All")
        {   
            $whereTag .= " and tsp.center_id = '$center_id' ";
            $whereTag1 .= " and sri.center_id = '$center_id' ";
        }

        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tsp.created_at) between '$from_date1' and '$to_date1'";
            $whereTag1 .= " and date(sri.created_at) between '$from_date1' and '$to_date1'";
        }


        $whereTagtab = "";
        if(!empty($brand_id1))
        {   
            $whereTagtab .= " and sri.brand_id = '$brand_id1' ";
            
        }

        if(!empty($product_category1))
        {   
            $whereTagtab .= " and sri.product_category_id = '$product_category1' ";
        }

        if(!empty($product1))
        {   

            $whereTagtab .= " and sri.product_id = '$product1' ";
        }

        if(!empty($model1))
        {

            $whereTagtab .= " and sri.model_id = '$model1' ";
        }

        if(!empty($part_code1))
        {   
       
            $whereTagtab .= " and srip.spare_id = '$part_code1' ";
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
            $po_job_qry =   "SELECT tm.*,tm.created_at,tsp.part_id,tsp.part_po_no,tsp.po_type,tsp.part_name,tsp.part_no,tsp.color,tsp.hsn_code,tsp.gst,tsp.customer_price,tsp.spare_id,bm.brand_name,cat.category_name,pm.product_name,mm.model_name FROM tagging_master tm INNER JOIN  tagging_spare_part tsp ON tm.tagid = tsp.tag_id 
            inner join brand_master bm on tsp.brand_id = bm.brand_id
            inner join product_category_master cat on tsp.product_category_id = cat.product_category_id
            inner join product_master pm on tsp.product_id = pm.product_id
            inner join model_master mm on tsp.model_id = mm.model_id
            WHERE tm.part_status='1' AND tsp.part_po_no IS NOT NULL AND tsp.pending_status ='1' AND tsp.ho_reject='0' and date(tsp.created_at)= curdate() ";

        }else{

            $po_job_qry =   "SELECT tm.*,tm.created_at,tsp.part_id,tsp.part_po_no,tsp.po_type,tsp.part_name,tsp.part_no,tsp.color,tsp.hsn_code,tsp.gst,tsp.customer_price,tsp.spare_id,tsp.pending_parts,bm.brand_name,cat.category_name,pm.product_name,mm.model_name FROM tagging_master tm INNER JOIN  tagging_spare_part tsp ON tm.tagid = tsp.tag_id 
            inner join brand_master bm on tsp.brand_id = bm.brand_id
            inner join product_category_master cat on tsp.product_category_id = cat.product_category_id
            inner join product_master pm on tsp.product_id = pm.product_id
            inner join model_master mm on tsp.model_id = mm.model_id
            WHERE tm.part_status='1' AND tsp.part_po_no IS NOT NULL AND tsp.pending_status ='1' AND tsp.ho_reject='0' $whereTag ";
        }
        
        //echo $po_job_qry;exit;
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
            #$part->asc_code = $center_det->asc_code;
        }
        
        if(empty($whereTag1))
        {
           
            $po_sc_arr =   DB::select("SELECT *,sri.created_at,sri.qty FROM `sc_request_inventory`  sri
            inner join sc_request_inventory_particulars srip on sri.req_id = srip.req_id
            inner join brand_master bm on sri.brand_id = bm.brand_id
            inner join product_category_master cat on sri.product_category_id = cat.product_category_id
            inner join product_master pm on sri.product_id = pm.product_id
            inner join model_master mm on sri.model_id = mm.model_id
            WHERE part_status_pending ='1' and sri.part_reject is null and date(sri.created_at)= curdate()");

        }else{
            
            $po_sc_arr =   DB::select("SELECT *,sri.created_at,sri.qty FROM `sc_request_inventory`  sri
            inner join sc_request_inventory_particulars srip on sri.req_id = srip.req_id
            inner join brand_master bm on sri.brand_id = bm.brand_id
            inner join product_category_master cat on sri.product_category_id = cat.product_category_id
            inner join product_master pm on sri.product_id = pm.product_id
            inner join model_master mm on sri.model_id = mm.model_id
            WHERE part_status_pending ='1' and sri.part_reject is null $whereTag1");
        }


        if(empty($whereTagtab))
        {
            $data_arr = DB::select("SELECT * FROM `outward_inventory` where date(created_at) = curdate()");
        }else{
            
            $data_arr = DB::select("SELECT * FROM `outward_inventory` sri where 1=1 $whereTagtab");
        }
        

        foreach($po_sc_arr as $part)
        {
            #echo "brand_id='$part->brand_id' and model_id='$part->model_id' and spare_id='$part->spare_id'";die;

            $inward_det = InwardInventoryPart::whereRaw("brand_id='$part->brand_id' and model_id='$part->model_id' and spare_id='$part->spare_id'")->first();
            $part->gst = $inward_det->gst; 
            $part->purchase_amt = $inward_det->purchase_amt; 
            $part->asc_amount = $inward_det->asc_amount; 
            $part->remarks = $inward_det->remarks; 
            $part->hsn_code = $inward_det->hsn_code;
      
            $center_det = ServiceCenter::whereRaw("center_id='$part->center_id'")->first();
            $part->center_name = $center_det->center_name;
    
        }

        #print_r($po_sc_arr);die;
        
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 

        $brand_master = array();
        
        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }

        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);

        $category_master = DB::select("SELECT pm.product_category_id,pm.category_name FROM product_category_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND category_status='1'
        WHERE bm.brand_id='$brand_id'");


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
        
        $url = $_SERVER['APP_URL'].'/outward-view-po';
        return view('outward-po-view')
        ->with('po_job_arr', $po_job_arr)
        ->with('po_sc_arr', $po_sc_arr)
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
       #echo $tag_id;die; 
       #echo "SELECT * FROM `tagging_spare_part` WHERE tag_id='$tag_id' and part_po_no is not null and pending_status ='1' and ho_reject='0'";die;
       $part_arr =   DB::select("SELECT * FROM `tagging_spare_part` WHERE tag_id='$tag_id' and part_po_no is not null and pending_status ='1' and ho_reject='0'");
        //$brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        //$brand_arr = json_decode($brand_json,true);
        $sr_no_master = array();
        $po_part_master = array();
        $po_inv_parts2= array();
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
            
            $po_inv_parts2[] = $part;
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
            
            
            
            $srno_list = DB::select("SELECT * FROM `tbl_inventory_item_list` item
            INNER JOIN tbl_spare_parts tsp ON item.part_id = tsp.spare_id
            WHERE tsp.spare_id='$spare_id' and is_out=0");
            
            $part->srno_list =$srno_list; 
           $po_part_master[] = $part;
            
        }

        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);
                
                
        $url = $_SERVER['APP_URL'].'/outward-po-view';
        return view('outward-part-po-job')
        ->with('po_part_master', $po_part_master)
        ->with('po_inv_parts', $po_inv_parts2)
        ->with('brand_arr', $brand_arr)
        ->with('url', $url);
    }
    
    public function approve_part_po(Request $request)
    {
        #print_r($request->all());die;
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $part_id =  $request->input('part_id');
        $issued_qty = addslashes($request->input('issued_qty'));
        $discount = addslashes($request->input('discount'));
        $remarks = addslashes($request->input('remarks'));
        $asc_amount = $request->input('asc_amount');
        $customer_amount = $request->input('customer_amount');
        $srno_list = $request->all('srno_list');
        $srno_list2 = array_diff($srno_list['srno_list'],['on']);
        if($issued_qty!=count($srno_list2))
        {
            echo '4';exit;
        }
        

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
        #print_r($inward_det);die;
        $OutwardInventoryPart->hsn_code = $inward_det->hsn_code; 
        $OutwardInventoryPart->gst = $inward_det->gst; 
        $OutwardInventoryPart->req_qty = $required_part; 
        $OutwardInventoryPart->issued_qty = $issued_qty;
        $OutwardInventoryPart->discount = $discount;
        $OutwardInventoryPart->remarks = $remarks;
        $OutwardInventoryPart->req_id = $inward_det->part_inw_id;
        $OutwardInventoryPart->po_id = $inward_det->inw_id;
        //$OutwardInventoryPart->purchase_amt = $inward_det->purchase_amt; 
        $OutwardInventoryPart->asc_amount = $asc_amount; 
        $OutwardInventoryPart->customer_amount = $customer_amount; 
        
        $qty = 0; $total = 0;
       
        $part_arr =   DB::select("SELECT * FROM `tagging_spare_part` WHERE tag_id='$tag_id' and part_id='$part_id'");

        DB::beginTransaction();
        
        if($OutwardInventoryPart->save())
        {
            $msg = "";
            if($pending_part<=0)
            {   
                $out_id = $OutwardInventoryPart->id;
                $ApproveRequestInventory = new ApproveRequestInventory();
                $ApproveRequestInventory->req_id= $part_id;
                $ApproveRequestInventory->out_id= $out_id;
                $ApproveRequestInventory->center_id= $center_id;
                $ApproveRequestInventory->brand_id= $brand_id;
                $ApproveRequestInventory->req_no= $part_po_det->part_po_no;
                $ApproveRequestInventory->po_type= $part_po_det->po_type;
                $ApproveRequestInventory->case_type= 'Job';
                
                if(strtolower($part_po_det->po_type)=='foc')
                {
                    $ApproveRequestInventory->invoice_status= 0;
                }
                $ApproveRequestInventory->part_required= count($part_arr);
                $ApproveRequestInventory->qty= $grnd_qty;
                $ApproveRequestInventory->total= $grnd_total;
                $ApproveRequestInventory->total_tax= $grand_tax;
                $ApproveRequestInventory->net_total= $grand_total;    
                $ApproveRequestInventory->remarks= $remarks;
                $ApproveRequestInventory->req_remarks= $req_remarks;
                $ApproveRequestInventory->created_at= $created_at;
                $ApproveRequestInventory->created_by= $created_by;
                $challan_no_date = date('ym');

                $find_max_challan_no=ChallanNo::selectRaw('challan_no')->whereRaw("challan_date=curdate()")->first();

                $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
                $brand_name = $brand_det->brand_name;
                $brand_ser_name = strtoupper(substr($brand_name, 0, 2));

                
                
                $sc_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
                $ApproveRequestInventory->asc_code= $sc_det->asc_code;
                $ApproveRequestInventory->pincode= $sc_det->pincode;
                $state_id = $sc_det->state;
                $state = StateMaster::whereRaw("state_id='$state_id'")->first();
                $ApproveRequestInventory->state= $state->state_name;
                $ApproveRequestInventory->center_name= $state->center_name;

                if($ApproveRequestInventory->save())
                {
                    $approve_id = $ApproveRequestInventory->id;
                    $RequestInventoryPart = new ApproveRequestInventoryPart();

                    if($RequestInventoryPart->insert($request_arr))
                    {
                        $UpdateInventory = array();
                        $UpdateInventory['part_status_pending'] = '0';
                        $UpdateInventory['part_reject'] = '0';
                        $UpdateInventory['part_approve'] = '1';
                        $UpdateInventory['approve_date'] = $created_at;

                            if(RequestInventory::whereRaw("req_id='$req_id'")->update($UpdateInventory))
                            {
                                if(ApproveRequestInventoryPart::whereRaw("req_id='$req_id'")->update(array('approve_id'=>$approve_id)))
                                {
                                    
                                }
                            }
                            
                    }
                  
                }
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
                    $srno_list2_str = implode("','",$srno_list2);
                    $upd = InventoryItemList::whereRaw("id in ('$srno_list2_str') and is_out=0")->update(array('approve_id'=>$approve_id,
                        'out_po_id'=>$req_id,'out_po_no'=>"$part_po_det->req_no",'asc_id'=>$center_id,'is_out'=>'1'));
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
        $req_part_id =  $request->input('req_part_id');
        $msg = "";
        if(!empty($req_part_id))
        {
            if(DB::update("update sc_request_inventory set part_reject='1',updated_at='$created_at',updated_by='$created_by' where req_id='$req_part_id'"))
            {
                $msg = "1";
            }
            else
            {
                $msg = "2";
            }

        }else{
            
        
            if(DB::update("update tagging_spare_part set ho_reject='1',ho_reject_date='$created_at',ho_reject_by='$created_by' where part_id='$part_id'"))
            {
                $msg = "1";
            }
            else
            {
                $msg = "2";
            }
        }
        
        
        
        
        echo $msg; exit;
    }

    
    public function approve_part_po_center(Request $request)
    {
        //print_r($request->all());exit;
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $part_id =  $request->input('part_id');
        $issued_qty = addslashes($request->input('issued_qty'));
        $discount = addslashes($request->input('discount'));
        $remarks = addslashes($request->input('remarks'));
        $customer_amount = addslashes($request->input('customer_amount'));
        $asc_amount = addslashes($request->input('asc_amount'));
        
        
        $srno_list = $request->all('srno_list');
        $srno_list2 = array_diff($srno_list['srno_list'],['on']);
        if($issued_qty!=count($srno_list2))
        {
            echo '4';exit;
        }
        //print_r($srno_list2);exit;
        #echo "req_part_id='$part_id' ";die;
        $part_po_det = SCRequestInventoryPart::whereRaw("req_part_id='$part_id' ")->first();
        #print_r($part_po_det->qty_pending);die;
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
        #print_r($request->all());die;
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
        $product_id = $part_po_det->product_id;
        $model_id = $part_po_det->model_id;
        $OutwardInventoryPart->model_id = $model_id;
        $model_det = ModelMaster::whereRaw("model_id='$model_id'")->first();
        $OutwardInventoryPart->model_name = $model_det->model_name;
        $OutwardInventoryPart->spare_id = $part_po_det->spare_id; 
        $OutwardInventoryPart->part_name = $part_po_det->part_name;
        $OutwardInventoryPart->part_no = $part_po_det->part_no;
        $OutwardInventoryPart->color = $part_po_det->color;
        
        $spare_id = $part_po_det->spare_id;
        $part_name = $part_po_det->part_name;;
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
        $OutwardInventoryPart->asc_amount = $asc_amount; 
        $OutwardInventoryPart->customer_amount = $customer_amount; 
        
        $ApproveRequestInventory = new ApproveRequestInventory();
        $ApproveRequestInventory->req_id= $part_po_det->req_id;
        $ApproveRequestInventory->center_id= $center_id;
        $ApproveRequestInventory->brand_id= $brand_id;
        $ApproveRequestInventory->req_no= $part_po_det->part_po_no;
        $ApproveRequestInventory->po_type= $part_po_det->po_type;
        if(strtolower($part_po_det->po_type)=='foc')
        {
            $ApproveRequestInventory->invoice_status= 0;
        }
        //$ApproveRequestInventory->center_id= $center_id;
        //$ApproveRequestInventory->center_id= $center_id;
        $ApproveRequestInventory->part_required= $required_part;
        $ApproveRequestInventory->qty= $issued_qty;
        $ApproveRequestInventory->total= $inward_det->purchase_amt;
        $ApproveRequestInventory->total_tax= $inward_det->gst; 
        $ApproveRequestInventory->net_total= $inward_det->customer_amount;
        $ApproveRequestInventory->remarks= $remarks;
        $ApproveRequestInventory->created_at= $created_at;
        $ApproveRequestInventory->created_by= $created_by;

        #echo $issued_qty;die;
        
        $sc_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
        $ApproveRequestInventory->asc_code= $sc_det->asc_code;
        $ApproveRequestInventory->pincode= $sc_det->pincode;
        $state_id = $sc_det->state;
        $state = StateMaster::whereRaw("state_id='$state_id'")->first();
        $ApproveRequestInventory->state= $state->state_name;
        $ApproveRequestInventory->center_name= $state->center_name;

        $request = array();
        $req_id = $part_po_det->req_id;
        $center_id = $request['center_id']   = $center_id;
        $request['req_id']      = $part_po_det->req_id;

        $request['brand_id']    = $brand_id;
        $request['product_id']  = $product_id;
        $request['model_id']    = $model_id;
        $request['part_name']   = $part_name;
        $request['part_no']     = $part_no;
        #$request['hsn_code']    = $hsn_code;
        $request['qty']    = $stock_required;
        #echo "brand_id='$brand_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and spare_id='$spare_id'";die;
        $check_spare_part_rate = SparePart::whereRaw("brand_id='$brand_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and spare_id='$spare_id'")->first();
        $landing_cost = $check_spare_part_rate->landing_cost;
        $customer_price = $check_spare_part_rate->customer_price;
        $discount = $check_spare_part_rate->discount;
        $part_tax = $check_spare_part_rate->part_tax;
        $hsn_code = $check_spare_part_rate->hsn_code;


        $request['landing_cost']        = $landing_cost;
        $request['customer_price'] = $customer_price;
        $request['discount'] = $discount;
        $request['part_tax'] = $part_tax;

        $actual_price = $customer_price;
        if(!empty($discount))
        {
            $balance = $customer_price-$landing_cost;
            $total_discount = round($balance*$discount/100,2);
            $actual_price = $customer_price-$total_discount;

        }
        $request['rate']    = $actual_price;
        $total = round($actual_price*$stock_required,2);
        $total_tax = round($total*$part_tax/100,2);
        $net_total = round($total_tax+$total,2);

        $request['total']       = $total;
        $request['total_tax']       = $total_tax;
        $request['net_total']       = $net_total;

        $request['challan_no']    = $new_challan_no;
        $request['created_at']  = $created_at;
        $request['created_by']  = $created_by;
        
        
        #print_r($request_arr);die;
        $approve_id = "";
        if($ApproveRequestInventory->save())
        {
            $approve_id = $ApproveRequestInventory->id;
            $RequestInventoryPart = new ApproveRequestInventoryPart();

            $request['approve_id'] = $approve_id;
            $request['qty'] = $issued_qty;
            $request['hsn_code'] = $hsn_code;
            $request_arr[] = $request;

            if($RequestInventoryPart->insert($request_arr))
            {
                $UpdateInventory = array();
                $UpdateInventory['part_status_pending'] = '0';
                $UpdateInventory['part_reject'] = '0';
                $UpdateInventory['part_approve'] = '1';
                $UpdateInventory['approve_date'] = $created_at;

                if(RequestInventory::whereRaw("req_id='$req_id'")->update($UpdateInventory))
                {
                    ApproveRequestInventoryPart::whereRaw("req_id='$req_id'")->update(array('approve_id'=>$approve_id));
                    
                }
                   
            }
           
        }

        DB::beginTransaction();
        
        if($OutwardInventoryPart->save())
        {
            $msg = "";
            if($pending_part<=0)
            {
                SCRequestInventoryPart::whereRaw("req_part_id='$part_id'")->update(array('customer_amount1'=>$customer_amount,'asc_amount1'=>$asc_amount,'qty_pending'=>'0','pending_status'=>'0'));
                $msg = "1";
            }
            else if($pending_part>0)
            {
                SCRequestInventoryPart::whereRaw("req_part_id='$part_id'")->update(array('customer_amount1'=>$customer_amount,'asc_amount1'=>$asc_amount,'qty_pending'=>$pending_part));
                $msg = "2";
            }
            else
            {
                $msg = "3";
            }
            
            $out_id = $OutwardInventoryPart->id;
            ApproveRequestInventory::whereRaw("approve_id='$approve_id'")->update(array('out_id'=>$out_id));

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
            #echo "brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'";die;
            $Inventory = Inventory::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
            $bal_qty = (int)$Inventory->bal_qty;
            
            
            if($bal_qty>=$issued_qty)
            {
                $new_bal_qty = $bal_qty-$issued_qty;
                if(Inventory::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->update(array('bal_qty'=>$new_bal_qty)))
                {
                    $srno_list2_str = implode("','",$srno_list2);
                    $upd = InventoryItemList::whereRaw("id in ('$srno_list2_str') and is_out=0")->update(array('approve_id'=>$approve_id,
                        'out_po_id'=>$req_id,'out_po_no'=>"$part_po_det->req_no",'asc_id'=>$center_id,'is_out'=>'1'));
                    
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
    
    public function sc_po_order_view(Request $request)
    {
        Session::put("page-title","Outward Stock");
        
        $req_id = base64_decode($request->input('req_id'));
        #echo "SELECT * FROM sc_request_inventory_particulars WHERE req_id='$req_id' and pending_status ='1' and qty_cancel='0'";die;
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


    public function outward_center_part_po(Request $request)
    {
        Session::put("page-title","Outward Stock");
        
       $req_id = base64_decode($request->input('req_id'));    
        $part_arr =   DB::select("SELECT * FROM sc_request_inventory_particulars WHERE req_id='$req_id' and pending_status ='1' and qty_cancel='0'");
        //$brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        //$brand_arr = json_decode($brand_json,true);
        $po_part_master = array();
        $po_inv_parts2= array();
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
            
            $srno_list = DB::select("SELECT * FROM `tbl_inventory_item_list` item
            INNER JOIN tbl_spare_parts tsp ON item.part_id = tsp.spare_id
            WHERE tsp.spare_id='$spare_id' and is_out=0");
            
            $part->srno_list =$srno_list; 
            
            $po_part_master[] = $part;
            $po_inv_parts2[] = $part;
            
        }
        #print_r($po_inv_parts2) ;die;
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);        
                
        $url = $_SERVER['APP_URL'].'/outward-po-view';
        return view('outward-part-po-center')
        ->with('po_part_master', $po_part_master)
        ->with('po_inv_parts', $po_inv_parts2)
        ->with('brand_arr', $brand_arr)
        
        ->with('url', $url);
    }
    

}

