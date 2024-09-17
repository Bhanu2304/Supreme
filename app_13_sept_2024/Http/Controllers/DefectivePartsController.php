<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\BrandMaster;
use App\SparePart;
use App\Inventory;
use App\RequestNo;
use App\InventoryCenter;
use App\RequestInventoryPart;
use App\RequestInventory;
use App\TagDamagePart;
use App\TagDamagePartDispatch;
use DB;
use Auth;
use Session;


class DefectivePartsController extends Controller
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
        Session::put("page-title","Defective Parts");
        $part_arr =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);
        $find_max_request_no=RequestNo::selectRaw('request_no')->whereRaw("request_date=curdate()")->first();

        $new_request_no = "";
        $request_no_date = date('Y/m/d/');
        if(empty($find_max_request_no))
        {
            //$request_entry_arr = new RequestNo();
            //$request_entry_arr->request_date = date('Y-m-d');
            //$request_entry_arr->request_no = '1';
            //$request_entry_arr->save();

            $new_request_no = "Sup/$request_no_date".'00001';
        }
        else
        {
            $str_no = "00000";
            $no = $find_max_request_no->request_no;
            $no = $no+1;
            $len = strlen($str_no);
            $newlen = strlen("$no");
            $new_no = substr_replace($str_no, $no, $len-$newlen,$newlen);
            $new_request_no = "Sup/$request_no_date".$new_no;
        }

        $brand_id = $request->input('brand_id');
        $product_category = $request->input('product_category');
        $product = $request->input('product');
        $model = $request->input('model');
        $part_code = $request->input('part_code');
        $po_sr_no = $request->input('po_sr_no');
        $po_status = $request->input('po_status');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $whereTag = "";
        if(!empty($brand_id))
        {   
            $whereTag .= " and tdp.brand_id = '$brand_id' ";
        }

        if(!empty($product_category))
        {   
            $whereTag .= " and tdp.product_category_id = '$product_category' ";
        }

        if(!empty($product))
        {   
            $whereTag .= " and tdp.product_id = '$product' ";
        }

        if(!empty($model))
        {   
            $whereTag .= " and tdp.model_id = '$model' ";
        }

        if(!empty($part_code))
        {   
            $whereTag .= " and tdp.spare_id = '$part_code' ";
        }

        if(!empty($po_sr_no))
        {   
            $whereTag .= " and ri.req_no = '$po_sr_no' ";
        }

        if(!empty($po_status))
        {   
            
            if($po_status == "Pending")
            {
                $whereTag .= "and pending_status='1'";

            }else if($po_status == "Approved")
            {
                $whereTag .= "and pending_status='0'";
            }
           
            #$whereTag .= " and ri.req_no = '$po_status' ";
        }

        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tdp.created_at) between '$from_date1' and '$to_date1'";
        }


        if(empty($whereTag))
        {
            
            $req_arr           =   DB::select("SELECT tdp.*,brand_name,category_name,product_name,model_name,ticket_no,job_no FROM tagging_damage_part tdp
            INNER JOIN tagging_master tm ON tdp.tag_id = tm.TagId 
            INNER JOIN brand_master bm ON tdp.brand_id = bm.brand_id
            INNER JOIN product_category_master cat ON tdp.product_category_id = cat.product_category_id
            INNER JOIN product_master pm ON tdp.product_id = pm.product_id
            INNER JOIN model_master mm ON tdp.model_id = mm.model_id
            INNER JOIN tbl_spare_parts tsp ON tdp.spare_id = tsp.spare_id
            WHERE  1=1  and date(tdp.created_at)= curdate()");

        }else{
           
            $req_arr           =   DB::select("SELECT tdp.*,brand_name,category_name,product_name,model_name,ticket_no,job_no FROM tagging_damage_part tdp
            INNER JOIN tagging_master tm ON tdp.tag_id = tm.TagId 
            INNER JOIN brand_master bm ON tdp.brand_id = bm.brand_id
            INNER JOIN product_category_master cat ON tdp.product_category_id = cat.product_category_id
            INNER JOIN product_master pm ON tdp.product_id = pm.product_id
            INNER JOIN model_master mm ON tdp.model_id = mm.model_id
            INNER JOIN tbl_spare_parts tsp ON tdp.spare_id = tsp.spare_id
            WHERE  1=1  $whereTag");
        }

        $category_master = DB::select("SELECT pm.product_category_id,pm.category_name FROM product_category_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND category_status='1'
        WHERE bm.brand_id='$brand_id'");


        $qry = "SELECT pm.product_id,pm.product_name FROM product_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND product_status='1'
        WHERE  pm.brand_id='$brand_id' and product_category_id='$product_category'";#die;
        $model_master = DB::select($qry);
        
        
        $url = $_SERVER['APP_URL'].'/defective-pending-asc';
        return view('defective-pending-asc')
        ->with('new_request_no', $new_request_no)
        ->with('brand_arr', $brand_arr)
        ->with('category_master', $category_master)
        ->with('part_arr', $part_arr)
        ->with('req_arr', $req_arr)    
        ->with('url', $url)
        ->with('brand_id',$brand_id)
        ->with('product_category', $product_category)
        ->with('model_master', $model_master)
        ->with('product', $product)
        ->with('po_sr_no',$po_sr_no)
        ->with('po_status',$po_status)
        ->with('from_date',$from_date)
        ->with('to_date',$to_date);
    }


    public function def_pending_accept(Request $request)
    {
        $dispatch_id =  $request->input('dispatch_id');
        $UserId = Session::get('UserId');

        $taggingArr['part_status']= "accept";
        $taggingArr['pending_status']= 0;
        if(TagDamagePart::whereRaw("dpart_id='$dispatch_id'")->update($taggingArr))
        {
            echo "1";
        }
        else
        {
            echo '0';
        }
        exit;

    }


    public function defective_part_dispatch(Request $request)
    {
        Session::put("page-title","Defective Dispatch");

        $challan_no = $request->input('challan_no');
        $job_no = $request->input('job_no');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $whereTag = "";

        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        #echo $UserType;die;
        if(strtolower($UserType)!=strtolower('Admin'))
        {
            $whereTag = "and tm.center_id='$Center_Id'";
        }

        #echo $whereTag;die;
        if(!empty($challan_no))
        {   
            #$whereTag .= " and oidp.challan_no = '$challan_no' ";
        }

        if(!empty($job_no))
        {   
            $whereTag .= " and tm.job_no = '$job_no' ";
        }

        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tdp.created_at) between '$from_date1' and '$to_date1'";
        }
        
        $dispatch_master =   DB::select("SELECT tdp.*,brand_name,category_name,product_name,model_name,ticket_no,job_no,center_name,tdpd.eway_bill_no,tdpd.doc_no,tdpd.veh_doc_no,tdpd.transportation_charge,tdpd.dispatch_ref_no,tdpd.no_of_cases,tdpd.dispatch_comments FROM tagging_damage_part tdp
            INNER JOIN tagging_master tm ON tdp.tag_id = tm.TagId 
            INNER JOIN tbl_service_centre tsc ON tdp.center_id = tsc.center_id 
            INNER JOIN brand_master bm ON tdp.brand_id = bm.brand_id
            INNER JOIN product_category_master cat ON tdp.product_category_id = cat.product_category_id
            INNER JOIN product_master pm ON tdp.product_id = pm.product_id
            INNER JOIN model_master mm ON tdp.model_id = mm.model_id
            INNER JOIN tbl_spare_parts tsp ON tdp.spare_id = tsp.spare_id
            LEFT JOIN tagging_damage_part_dispatch tdpd ON tdp.dpart_id = tdpd.dpart_id 
            WHERE  pending_status='1'  $whereTag");
        #print_r($dispatch_master);die;
        #$dispatch_master =   DB::select("SELECT * FROM outward_inventory_dispatch dm 
        #INNER JOIN outward_inventory_dispatch_particulars oidp ON dm.dispatch_id= oidp.dispatch_id WHERE dm.dispatch='1' $whereTag");

        $url = $_SERVER['APP_URL'].'/defective-part-dispatch';
        return view('defective-dispatch')
        ->with('UserType', $UserType)
        ->with('invoice_master', $invoice_master)
        ->with('dispatch_master', $dispatch_master)
        ->with('job_no', $job_no)
        ->with('product', $product)
        ->with('from_date',$from_date)
        ->with('to_date',$to_date)      
        ->with('url', $url);
    }


    public function edit_defective_dispatch(Request $request)
    {
        Session::put("page-title","Edit Defective Dispatch");
        $dispatch_id =  $request->input('dispatch_id');

        $dispatch_det =   DB::select("SELECT *,tdp.dpart_id FROM tagging_damage_part tdp
            INNER JOIN tbl_service_centre tsc ON tdp.center_id = tsc.center_id
            LEFT JOIN tagging_damage_part_dispatch tdpd ON tdp.dpart_id = tdpd.dpart_id where tdp.dpart_id='$dispatch_id' ");
        
        $url = $_SERVER['APP_URL'].'/defective-part-dispatch';
        return view('defective-dispatch-edit')
        ->with('invoice_det', $invoice_det)
        ->with('dispatch_det', $dispatch_det)        
        ->with('url', $url);
    }


    public function save_defective_dispatch(Request $request)
    {
       # echo $request->input('transportation_charges');die;
       #print_r($request->all());die;
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $invoice_id =  $request->input('invoice_id');
        $DispatchInventory_det = TagDamagePart::whereRaw("dpart_id='$invoice_id'")->first();
        $part_po_no = $DispatchInventory_det->part_po_no;
        
        $eway_bill_no = addslashes($request->input('eway_bill_no'));
        $remarks = addslashes($request->input('remarks'));
        $doc_no = addslashes($request->input('doc_no'));
        $veh_doc_no = addslashes($request->input('veh_doc_no'));
        $transportation_charge = addslashes($request->input('transportation_charge'));
        
        $dispatch_ref_no = addslashes($request->input('dispatch_ref_no'));
        $no_of_cases = addslashes($request->input('no_of_cases'));
        

        $DispatchInventory['part_id'] = $DispatchInventory_det->part_id;
        $DispatchInventory['po_no'] = $DispatchInventory_det->part_po_no;
        $DispatchInventory['po_date'] = $DispatchInventory_det->part_po_date;
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

        
       
        if(TagDamagePartDispatch::updateOrCreate(['dpart_id' => $invoice_id], $DispatchInventory))
        {
            $taggingArr1['request_to_ho']= 1;
            $taggingArr1['request_to_ho_date']= $created_at;
            $taggingArr1['request_to_ho_by']= $created_by;
            
            
            TagDamagePart::whereRaw("dpart_id='$invoice_id'")->update($taggingArr1);
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

    public function defective_approval(Request $request)
    {
        Session::put("page-title","Defective Parts Approval");
        $part_arr =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);
        $find_max_request_no=RequestNo::selectRaw('request_no')->whereRaw("request_date=curdate()")->first();

        $new_request_no = "";
        $request_no_date = date('Y/m/d/');
        if(empty($find_max_request_no))
        {


            $new_request_no = "Sup/$request_no_date".'00001';
        }
        else
        {
            $str_no = "00000";
            $no = $find_max_request_no->request_no;
            $no = $no+1;
            $len = strlen($str_no);
            $newlen = strlen("$no");
            $new_no = substr_replace($str_no, $no, $len-$newlen,$newlen);
            $new_request_no = "Sup/$request_no_date".$new_no;
        }

        $brand_id = $request->input('brand_id');
        $product_category = $request->input('product_category');
        $product = $request->input('product');
        $model = $request->input('model');
        $part_code = $request->input('part_code');
        $po_sr_no = $request->input('po_sr_no');
        $po_status = $request->input('po_status');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $whereTag = "";
        if(!empty($brand_id))
        {   
            $whereTag .= " and tdp.brand_id = '$brand_id' ";
        }

        if(!empty($product_category))
        {   
            $whereTag .= " and tdp.product_category_id = '$product_category' ";
        }

        if(!empty($product))
        {   
            $whereTag .= " and tdp.product_id = '$product' ";
        }

        if(!empty($model))
        {   
            $whereTag .= " and tdp.model_id = '$model' ";
        }

        if(!empty($part_code))
        {   
            $whereTag .= " and tdp.spare_id = '$part_code' ";
        }

        if(!empty($po_sr_no))
        {   
            $whereTag .= " and ri.req_no = '$po_sr_no' ";
        }

        if(!empty($po_status))
        {   
            
            if($po_status == "Pending")
            {
                $whereTag .= "and tdp.approve='0' and  tdp.reject='0'";

            }else if($po_status == "Approved")
            {
                $whereTag .= "and tdp.approve='1'";
            }
            else if($po_status == "Cancelled")
            {
                $whereTag .= "and tdp.reject='1'";
            }
            #$whereTag .= " and ri.req_no = '$po_status' ";
        }

        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tdp.created_at) between '$from_date1' and '$to_date1'";
        }


        if(empty($whereTag))
        {

            $req_arr           =   DB::select("SELECT tdp.*,brand_name,category_name,product_name,model_name,ticket_no,job_no FROM tagging_damage_part tdp
            inner JOIN tagging_damage_part_dispatch tdpd ON tdp.dpart_id = tdpd.dpart_id
            INNER JOIN tagging_master tm ON tdp.tag_id = tm.TagId 
            INNER JOIN brand_master bm ON tdp.brand_id = bm.brand_id
            INNER JOIN product_category_master cat ON tdp.product_category_id = cat.product_category_id
            INNER JOIN product_master pm ON tdp.product_id = pm.product_id
            INNER JOIN model_master mm ON tdp.model_id = mm.model_id
            INNER JOIN tbl_spare_parts tsp ON tdp.spare_id = tsp.spare_id
            WHERE tdp.request_to_ho='1' and  tdpd.dispatch='1'  and date(tdp.created_at)= curdate()");

        }else{
           
            $req_arr           =   DB::select("SELECT tdp.*,brand_name,category_name,product_name,model_name,ticket_no,job_no FROM tagging_damage_part tdp
            inner JOIN tagging_damage_part_dispatch tdpd ON tdp.dpart_id = tdpd.dpart_id
            INNER JOIN tagging_master tm ON tdp.tag_id = tm.TagId 
            INNER JOIN brand_master bm ON tdp.brand_id = bm.brand_id
            INNER JOIN product_category_master cat ON tdp.product_category_id = cat.product_category_id
            INNER JOIN product_master pm ON tdp.product_id = pm.product_id
            INNER JOIN model_master mm ON tdp.model_id = mm.model_id
            INNER JOIN tbl_spare_parts tsp ON tdp.spare_id = tsp.spare_id
            WHERE tdp.request_to_ho='1' and tdpd.dispatch='1'  $whereTag");
        }

        $category_master = DB::select("SELECT pm.product_category_id,pm.category_name FROM product_category_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND category_status='1'
        WHERE bm.brand_id='$brand_id'");


        $qry = "SELECT pm.product_id,pm.product_name FROM product_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND product_status='1'
        WHERE  pm.brand_id='$brand_id' and product_category_id='$product_category'";#die;
        $model_master = DB::select($qry);
        
        
        $url = $_SERVER['APP_URL'].'/defective-approval';
        return view('defective-pending-approval')
        ->with('new_request_no', $new_request_no)
        ->with('brand_arr', $brand_arr)
        ->with('category_master', $category_master)
        ->with('part_arr', $part_arr)
        ->with('req_arr', $req_arr)    
        ->with('url', $url)
        ->with('brand_id',$brand_id)
        ->with('product_category', $product_category)
        ->with('model_master', $model_master)
        ->with('product', $product)
        ->with('po_sr_no',$po_sr_no)
        ->with('po_status',$po_status)
        ->with('from_date',$from_date)
        ->with('to_date',$to_date);
    }


    public function def_approval_accept(Request $request)
    {
        $dispatch_id =  $request->input('dispatch_id');
        $status =  $request->input('status');
        $UserId = Session::get('UserId');


        if($status == "1")
        {
            $taggingArr['approve']= 1;
            $taggingArr['approve_by']= $UserId;
            $taggingArr['approve_date']= date('Y-m-d H:i:s');

        }else{

            $taggingArr['reject']= 1;
            $taggingArr['reject_by']= $UserId;
            $taggingArr['reject_date']= date('Y-m-d H:i:s');


        }

        if(TagDamagePart::whereRaw("dpart_id='$dispatch_id'")->update($taggingArr))
        {
            echo $status;
        }
        else
        {
            echo '0';
        }
        exit;

    }

    

    
    public function view(Request $request)
    {
        Session::put("page-title","View Inventory Request");
        
        $req_id     = base64_decode($request->input('req_id'));  
        $req_det  = RequestInventory::where("req_id",$req_id)->first();
        $data_part_arr  = RequestInventoryPart::where("req_id",$req_id)->get();
        
        $brand_arr   = DB::select("SELECT brand_id,brand_name FROM brand_master WHERE brand_status='1' ");
        $brand_id = $req_det->brand_id;
        $product_category_id =$req_det->product_category_id;
        $product_id = $req_det->product_id;
        $product_det =  DB::select("SELECT product_category_id,category_name FROM product_category_master WHERE brand_id='$brand_id' and category_status='1' ");
        $product_mas =  DB::select("SELECT product_id,product_name FROM product_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_status='1' ");
        $model_det =  DB::select("SELECT model_id,model_name FROM model_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and  model_status='1' ");
        

       //print_r($record); exit;
        $url = $_SERVER['APP_URL'].'/view-req-inv';
        return view('view-req-inv')
                ->with('req_det',$req_det)
                ->with('data_part_arr', $data_part_arr)
                ->with('brand_arr', $brand_arr)
                ->with('product_det', $product_det)
                ->with('product_mas', $product_mas)
                ->with('model_det', $model_det)
                ->with('url',$url);
    }
    
    public function edit(Request $request)
    {
        Session::put("page-title","Edit Purchase Order");
        
        $req_id     = base64_decode($request->input('req_id'));  
        $req_det  = RequestInventory::where("req_id",$req_id)->first();
        $data_part_arr  = RequestInventoryPart::where("req_id",$req_id)->get();
        
        $brand_arr   = DB::select("SELECT brand_id,brand_name FROM brand_master WHERE brand_status='1' ");
        $brand_id = $req_det->brand_id;
        $product_category_id =$req_det->product_category_id;
        $product_id = $req_det->product_id;
        $product_det =  DB::select("SELECT product_category_id,category_name FROM product_category_master WHERE brand_id='$brand_id' and category_status='1' ");
        $product_mas =  DB::select("SELECT product_id,product_name FROM product_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_status='1' ");
        $model_det =  DB::select("SELECT model_id,model_name FROM model_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and  model_status='1' ");
        

       //print_r($record); exit;
        $url = $_SERVER['APP_URL'].'/view-req-inv';
        return view('edit-req-inv')
                ->with('req_det',$req_det)
                ->with('data_part_arr', $data_part_arr)
                ->with('brand_arr', $brand_arr)
                ->with('product_det', $product_det)
                ->with('product_mas', $product_mas)
                ->with('model_det', $model_det)
                ->with('url',$url);
    }
    
    
    public function update(Request $request)
    {
        $updated_by     =   Auth::User()->id;
        $updated_at     =   date('Y-m-d H:i:s');
        
        $spare_part =  $request->input('SparePart');
        $brand = '';
        $product_category_id = '';
        $product_id = '';
        $model_id='';
        $qty = '';
        $total = '';
        //$brand = $request->input('brand');
        //$part_name =  $spare_part['part_name'];
        $remarks = addslashes($request->input('remarks'));
        $req_id =  $request->input('req_id');
             
        $po_order = array(); $index = 0;
        foreach($spare_part as $spare_id=>$part)
        {
            if(empty($part['part_name']))
            {
                Session::flash('message', "Part Name should Not be Empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            } 
            else if(empty($part['color']))
            {
                Session::flash('message', "Color should not be empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }  
            else if(empty($part['current_qty']))
            {
                Session::flash('message', "Current Stock Quantity should not be empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['req_qty']))
            {
                Session::flash('message', "Required Quantity should not be empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['po_amt']))
            {
                Session::flash('message', "Purchase Amount should not be empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            
            $SparePart = SparePart::whereRaw("spare_id='$spare_id'")->first();
            
            $po_order[$index]['brand_id'] = $brand_id = $SparePart->brand_id;
            $po_order[$index]['product_category_id'] = $product_category_id = $SparePart->product_category_id;
            $po_order[$index]['product_id'] = $product_id =$SparePart->product_id;
            $po_order[$index]['model_id'] = $model_id = $SparePart->model_id;
            $po_order[$index]['spare_id'] = $spare_id;
            $po_order[$index]['part_name'] = $SparePart->part_name;
            $po_order[$index]['part_no'] = $SparePart->part_no;
            $po_order[$index]['color'] = $part['color'];
            $po_order[$index]['current_qty'] = $part['current_qty'];
            $po_order[$index]['req_qty'] = $part['req_qty'];
            $po_order[$index]['curr_amt'] = $part['curr_amt'];
            $po_order[$index]['po_amt'] = $part['po_amt'];
            $po_order[$index]['created_at'] = $updated_at;
            $po_order[$index]['created_by'] = $updated_by;
            $qty +=  $part['req_qty'];
            $total += ($part['req_qty']*$part['po_amt']);
            $index++;
        }
        
        if(empty($po_order))
        {
            Session::flash('error', "Please Fill All Details.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        
        
            $RequestInventory = array();
            
            $RequestInventory['brand_id']= $brand_id;
            $RequestInventory['product_category_id']= $product_category_id;
            $RequestInventory['product_id']= $product_id;
            $RequestInventory['model_id']= $model_id;
            
            $RequestInventory['part_required']= count($po_order);
            $RequestInventory['qty']= $qty;
            $RequestInventory['total']= $total;
            $RequestInventory['part_status_pending']= 1;
            $RequestInventory['remarks']= $remarks;
            //$RequestInventory['created_at']= $created_at;
            //$RequestInventory->created_by= $created_by;
            
            $RequestInventory['updated_at']= $updated_at;
            $RequestInventory['updated_by']= $updated_by;
            
            
            if(RequestInventory::whereRaw("req_id='$req_id'")->update($RequestInventory))
            {
                $req_det  = RequestInventory::where("req_id",$req_id)->first();
                $new_request_no = $req_det->req_no;
                $request_arr = array();
                foreach($po_order as $index=>$part)
                {
                    $part['req_id'] = $req_id;
                    $part['req_no'] = $new_request_no;
                    $request_arr[] = $part;
                }
        
                $RequestInventoryPart = new RequestInventoryPart();
                
                if(RequestInventoryPart::whereRaw("req_id='$req_id'")->delete())
                {
                    if($RequestInventoryPart->insert($request_arr))
                    {                       
                        Session::flash('message', "Purchase Order updated Successfully.");
                        Session::flash('alert-class', 'alert-success');
                    }
                    else
                    {
                        Session::flash('error', "Purchase Order updated failed");
                        Session::flash('alert-class', 'alert-danger');
                        return back();
                    }
                }
                
                
            }
            return redirect('req-inv-entry');           
    }
    
    

    
    
}

