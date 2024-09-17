<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\BrandMaster;
use App\SparePart;
use App\Inventory;
use App\InventoryAdd;
use App\InwardInventoryPart;
use App\RequestNoSc;
use App\InventoryCenter;
use App\SCRequestInventoryPart;
use App\SCRequestInventory;
use App\SupplierMaster;
use App\ServiceCenter;
use App\ModelMaster;
use App\ChallanNo;
use App\StateMaster;
use App\ApproveRequestInventory;
use App\ApproveRequestInventoryPart;
use App\RequestInventory;
use App\OutwardInventoryPart;
use App\InventoryItemList;
use App\TaggingMaster;

use DB;
use Auth;
use Session;


class RequestInventoryCenterController extends Controller
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
        Session::put("page-title","Raise PO");
        $part_arr =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);
        $find_max_request_no=RequestNoSc::selectRaw('request_no')->whereRaw("request_date=curdate()")->first();


        $supplier_json           =   SupplierMaster::whereRaw(" active_status='1'")->orderByRaw('supplier_name ASC')->get(); 
        $supplier_arr = json_decode($supplier_json,true);

        $new_request_no = "";
        $request_no_date = date('Y/m/d/');
        if(empty($find_max_request_no))
        {
            //$request_entry_arr = new RequestNoSc();
            //$request_entry_arr->request_date = date('Y-m-d');
            //$request_entry_arr->request_no = '1';
            //$request_entry_arr->save();

            $new_request_no = "Sup/$request_no_date".'0001';
        }
        else
        {
            $str_no = "0000";
            $no = $find_max_request_no->request_no;
            $no = $no+1;
            $len = strlen($str_no);
            $newlen = strlen("$no");
            $new_no = substr_replace($str_no, $no, $len-$newlen,$newlen);
            $new_request_no = "Sup/$request_no_date".$new_no;
        }
        #print_r($request->all());die;
        $brand_id = $request->input('brand_id');
        $product_category = $request->input('product_category');
        $product = $request->input('product');
        $model = $request->input('model');
        $part_code = $request->input('part_code');
        $po_sr_no = $request->input('po_sr_no');
        $po_type = $request->input('po_type');
        $po_status = $request->input('po_status');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $whereTag = "";
        if(!empty($brand_id))
        {   
            $whereTag .= " and ri.brand_id = '$brand_id' ";
        }

        if(!empty($product_category) && $product_category!='All')
        {   
            $whereTag .= " and ri.product_category_id = '$product_category' ";
            $product_qry_search = " and product_category_id='$product_category'";
        }

        if(!empty($product) && $product!='All')
        {   
            $whereTag .= " and ri.product_id = '$product' ";
            $model_qry_search = "  and product_id='$product'";
        }

        if(!empty($model) && $model!='All')
        {   
            $whereTag .= " and ri.model_id = '$model' ";
        }

        if(!empty($part_code))
        {   
            $whereTag .= " and ri.spare_id = '$part_code' ";
        }

        if(!empty($po_sr_no))
        {   
            $whereTag .= " and ri.req_no = '$po_sr_no' ";
        }

        if(!empty($po_type))
        {   
            $whereTag .= " and ri.po_type = '$po_type' ";
        }

        if(!empty($po_status))
        {   
            
            if($po_status == "Pending")
            {
                $whereTag .= "and ri.part_status_pending='1'";

            }else if($po_status == "Inward")
            {
                $whereTag .= "and ri.part_status_pending='0'";
            }
            else if($po_status == "Cancelled")
            {
                $whereTag .= "and ri.part_reject='1'";
            }
            #$whereTag .= " and ri.req_no = '$po_status' ";
        }
        #echo $whereTag;die;
        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(ri.created_at) between '$from_date1' and '$to_date1'";
        }

        if(empty($whereTag))
        {
            $req_arr    =   DB::select("SELECT *,brand_name,category_name,product_name,model_name,date_format(ri.req_date,'%d-%b-%y') created_at,sm.supplier_name FROM sc_request_inventory ri 
            inner join brand_master bm on ri.brand_id = bm.brand_id
            inner join product_category_master cat on ri.product_category_id = cat.product_category_id
            inner join product_master pm on ri.product_id = pm.product_id
            inner join model_master mm on ri.model_id = mm.model_id
            INNER JOIN `supplier_master` sm ON ri.supplier_id = sm.id
            WHERE  date(ri.created_at)= curdate()");
        }else{

            $req_arr    =   DB::select("SELECT *,brand_name,category_name,product_name,model_name,date_format(ri.req_date,'%d-%b-%y') created_at,sm.supplier_name FROM sc_request_inventory ri 
            inner join brand_master bm on ri.brand_id = bm.brand_id
            inner join product_category_master cat on ri.product_category_id = cat.product_category_id
            inner join product_master pm on ri.product_id = pm.product_id
            inner join model_master mm on ri.model_id = mm.model_id
            INNER JOIN `supplier_master` sm ON ri.supplier_id = sm.id
            WHERE 1=1 $whereTag");
        }


        $category_master = DB::select("SELECT pm.product_category_id,pm.category_name FROM product_category_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND category_status='1'
        WHERE bm.brand_id='$brand_id'");

        $qry_product = "SELECT pm.product_id,pm.product_name FROM product_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND product_status='1'
        WHERE  pm.brand_id='$brand_id' $product_qry_search";#die;
        //echo $qry_product;exit;
        $product_master = DB::select($qry_product);

        $qry_model = "SELECT mm.model_id,mm.model_name FROM model_master mm 
        INNER JOIN brand_master bm ON mm.brand_id = bm.brand_id AND brand_status='1' AND model_status='1'
        WHERE  mm.brand_id='$brand_id' $model_qry_search";#die;
        //echo $qry_model;exit;
        $model_master = DB::select($qry_model);
        
        $url = $_SERVER['APP_URL'].'/req-inv-entry-sc';
        return view('req-inv-entry-sc')
        ->with('new_request_no', $new_request_no)
        ->with('brand_arr', $brand_arr)
        ->with('supplier_arr', $supplier_arr)
        ->with('part_arr', $part_arr)
        ->with('req_arr', $req_arr)  
        ->with('brand_id',$brand_id)
        ->with('product_category', $product_category)
        ->with('product_master', $product_master)
        ->with('model_master', $model_master)
        ->with('product', $product)
        ->with('model_id', $model)
        ->with('po_sr_no',$po_sr_no)
        ->with('po_type',$po_type)
        ->with('po_status',$po_status)
        ->with('from_date',$from_date)
        ->with('to_date',$to_date)  
        ->with('url', $url);
    }
    
    public function save_req_inv(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        $center_id = Auth::user()->table_id;
        $spare_part =  $request->input('SparePart');
        $po_type =  $request->input('po_type');
        $brand = '';
        $product_category_id = '';
        $product_id = '';
        $model_id='';
        $qty = 0;
        $total = '';
        //$brand = $request->input('brand');
        //$part_name =  $spare_part['part_name'];
        $remarks = addslashes($request->input('remarks'));
        $supplier_name =  $request->input('supplier_name');
         
        #print_r($request->all()); exit;
        
        
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
            else if($part['current_qty']=='')
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
            // else if(empty($part['curr_amt']))
            // {
            //     Session::flash('message', "Current Purchase Amount should not be empty.");
            //     Session::flash('alert-class', 'alert-danger');
            //     return back();
            // }
            
            $SparePart = SparePart::whereRaw("spare_id='$spare_id'")->first();
            
            $po_order[$index]['center_id'] = $center_id;
            $po_order[$index]['brand_id'] = $brand_id = $SparePart->brand_id;
            $po_order[$index]['product_category_id'] = $product_category_id = $SparePart->product_category_id;
            $po_order[$index]['product_id'] = $product_id =$SparePart->product_id;
            $po_order[$index]['model_id'] = $model_id = $SparePart->model_id;
            $po_order[$index]['spare_id'] = $spare_id;
            $po_order[$index]['part_name'] = $SparePart->part_name;
            $po_order[$index]['part_no'] = $SparePart->part_no;
            $po_order[$index]['color'] = $part['color'];
            $po_order[$index]['sr_no'] = $part['sr_no'];
            $po_order[$index]['current_qty'] = $part['current_qty'];
            $po_order[$index]['req_qty'] = $part['req_qty'];
            $po_order[$index]['qty_pending'] = $part['req_qty'];

            $po_order[$index]['previous_qty'] = $part['previous_qty'];
            $po_order[$index]['total_qty'] = $part['total_qty'];
            
            #$po_order[$index]['curr_amt'] = $part['curr_amt'];
            $po_order[$index]['po_amt'] = $part['po_amt'];
            $po_order[$index]['po_type'] = $po_type;
            $po_order[$index]['created_at'] = $created_at;
            $po_order[$index]['created_by'] = $created_by;
            $qty +=  $part['req_qty'];
            $total += ($part['req_qty']*$part['po_amt']);
            $index++;
           
        }
        
        if(empty($po_order))
        {
            Session::flash('error', "Please Fill All Details.");
            Session::flash('alert-class', 'alert-danger');
            Session::flash('tab', "1");
            return back();
            
        }
        
            $SCRequestInventory = new SCRequestInventory();
            $SCRequestInventory->supplier_id= $supplier_name;
            $SCRequestInventory->center_id= $center_id;
            $SCRequestInventory->brand_id= $brand_id;
            $SCRequestInventory->product_category_id= $product_category_id;
            $SCRequestInventory->product_id= $product_id;
            $SCRequestInventory->model_id= $model_id;
            
            $SCRequestInventory->part_required= count($po_order);
            $SCRequestInventory->qty_pending= $qty;
            $SCRequestInventory->qty= $qty;
            $SCRequestInventory->total= $total;
            $SCRequestInventory->part_status_pending= 1;
            $SCRequestInventory->remarks= $remarks;
            $SCRequestInventory->po_type= $po_type;
            $SCRequestInventory->created_at= $created_at;
            $SCRequestInventory->created_by= $created_by;
            
            $currentYear = date('Y');
            $lastYear = $currentYear-1;
            $request_no_date = date('ym'); 
            $request_no_brand = '';
            if($brand_id=='2')
            {
                $request_no_brand = 'PI';
            }
            else if($brand_id=='4')
            {
                $request_no_brand = 'CL';
            }
            else
            {
                $request_no_brand = 'SP';
            }
            
            //$request_no_date = date('Y/m/d/');

            $find_max_request_no=RequestNoSc::selectRaw('request_no')->whereRaw("request_date=curdate()")->first();

            $new_request_no = "";
            if(empty($find_max_request_no))
            {
                $request_entry_arr = new RequestNoSc();
                $request_entry_arr->request_date = date('Y-m-d');
                $request_entry_arr->request_no = '1';
                $request_entry_arr->save();

                $new_request_no = "{$request_no_brand}-{$request_no_date}"."/$center_id/".$po_type.'00001';
            }
            else
            {
                $str_no = "00000";
                $no = $find_max_request_no->request_no;
                $no = $no+1;
                $len = strlen($str_no);
                $newlen = strlen("$no");
                $new_no = substr_replace($str_no, $no, $len-$newlen,$newlen);
                $new_request_no = $new_request_no = "{$request_no_brand}-{$request_no_date}"."/$center_id/".$po_type.$new_no;

                RequestNoSc::whereRaw("request_date=curdate()")->update(array('request_no'=>$no));
            }
            $req_date = date('Y-m-d');
                $SCRequestInventory->req_no= $new_request_no;
                $SCRequestInventory->req_date= $req_date;
                
            
            if($SCRequestInventory->save())
            {
                $req_id = $SCRequestInventory->id;
                
                $request_arr = array();
                foreach($po_order as $index=>$part)
                {
                    $part['req_id'] = $req_id;
                    $part['req_no'] = $new_request_no;
                    $part['req_date'] = $req_date;
                    $request_arr[] = $part;
                }
        
                $SCRequestInventoryPart = new SCRequestInventoryPart();
                if($SCRequestInventoryPart->insert($request_arr))
                {                       
                    Session::flash('message', "Purchase Order $new_request_no Raised Successfully.");
                    Session::flash('alert-class', 'alert-success');
                }
                else
                {
                    Session::flash('error', "Purchase Order Failed. Please Try Again");
                    Session::flash('alert-class', 'alert-danger');
                    return back();
                }
            }
            //exit;
            return redirect('req-inv-entry-sc');           
    }
    
    
    public function view(Request $request)
    {
        Session::put("page-title","View Inventory Request");
        
        $req_id     = base64_decode($request->input('req_id'));  
        $req_det  = SCRequestInventory::where("req_id",$req_id)->first();
        $data_part_arr  = SCRequestInventoryPart::where("req_id",$req_id)->get();
        
        $brand_arr   = DB::select("SELECT brand_id,brand_name FROM brand_master WHERE brand_status='1' ");
        $brand_id = $req_det->brand_id;
        $product_category_id =$req_det->product_category_id;
        $product_id = $req_det->product_id;
        $product_det =  DB::select("SELECT product_category_id,category_name FROM product_category_master WHERE brand_id='$brand_id' and category_status='1' ");
        $product_mas =  DB::select("SELECT product_id,product_name FROM product_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_status='1' ");
        $model_det =  DB::select("SELECT model_id,model_name FROM model_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and  model_status='1' ");
        

        $supplier_json           =   SupplierMaster::whereRaw(" active_status='1'")->orderByRaw('supplier_name ASC')->get(); 
        $supplier_arr = json_decode($supplier_json,true);

       //print_r($record); exit;
        $url = $_SERVER['APP_URL'].'/view-req-inv-sc';
        return view('view-req-inv-sc')
                ->with('req_det',$req_det)
                ->with('data_part_arr', $data_part_arr)
                ->with('supplier_arr', $supplier_arr)
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
        $req_det  = SCRequestInventory::where("req_id",$req_id)->first();
        $data_part_arr  = SCRequestInventoryPart::where("req_id",$req_id)->get();
        
        $brand_arr   = DB::select("SELECT brand_id,brand_name FROM brand_master WHERE brand_status='1' ");
        $brand_id = $req_det->brand_id;
        $product_category_id =$req_det->product_category_id;
        $product_id = $req_det->product_id;
        $product_det =  DB::select("SELECT product_category_id,category_name FROM product_category_master WHERE brand_id='$brand_id' and category_status='1' ");
        $product_mas =  DB::select("SELECT product_id,product_name FROM product_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_status='1' ");
        $model_det =  DB::select("SELECT model_id,model_name FROM model_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and  model_status='1' ");
        
        $supplier_json           =   SupplierMaster::whereRaw(" active_status='1'")->orderByRaw('supplier_name ASC')->get(); 
        $supplier_arr = json_decode($supplier_json,true);

       //print_r($record); exit;
        $url = $_SERVER['APP_URL'].'/view-req-inv-sc';
        return view('edit-req-inv-sc')
                ->with('req_det',$req_det)
                ->with('data_part_arr', $data_part_arr)
                ->with('supplier_arr', $supplier_arr)
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
         $center_id = Auth::user()->table_id;
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
        $supplier_name =  $request->input('supplier_name');
             
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
            else if($part['current_qty']=='')
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
            else if(empty($part['curr_amt']))
            {
                Session::flash('message', "Purchase Amount should not be empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            
            $SparePart = SparePart::whereRaw("spare_id='$spare_id'")->first();
            
            $po_order[$index]['brand_id'] = $brand_id = $SparePart->brand_id;
            $po_order[$index]['center_id'] = $center_id;
            $po_order[$index]['product_category_id'] = $product_category_id = $SparePart->product_category_id;
            $po_order[$index]['product_id'] = $product_id =$SparePart->product_id;
            $po_order[$index]['model_id'] = $model_id = $SparePart->model_id;
            $po_order[$index]['spare_id'] = $spare_id;
            $po_order[$index]['part_name'] = $SparePart->part_name;
            $po_order[$index]['part_no'] = $SparePart->part_no;
            $po_order[$index]['color'] = $part['color'];
            $po_order[$index]['current_qty'] = $part['current_qty'];
            $po_order[$index]['req_qty'] = $part['req_qty'];
            $po_order[$index]['qty_pending'] = $part['req_qty'];
            
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
        
        
        
            $SCRequestInventory = array();
            $SCRequestInventory['supplier_id']= $supplier_name;
            $SCRequestInventory['brand_id']= $brand_id;
            $SCRequestInventory['product_category_id']= $product_category_id;
            $SCRequestInventory['product_id']= $product_id;
            $SCRequestInventory['model_id']= $model_id;
            
            $SCRequestInventory['part_required']= count($po_order);
            $SCRequestInventory['qty']= $qty;
            $SCRequestInventory['qty_pending']= $qty;
            $SCRequestInventory['total']= $total;
            $SCRequestInventory['part_status_pending']= 1;
            $SCRequestInventory['remarks']= $remarks;
            //$SCRequestInventory['created_at']= $created_at;
            //$SCRequestInventory->created_by= $created_by;
            
            $SCRequestInventory['updated_at']= $updated_at;
            $SCRequestInventory['updated_by']= $updated_by;
            
            
            if(SCRequestInventory::whereRaw("req_id='$req_id'")->update($SCRequestInventory))
            {
                $req_det  = SCRequestInventory::where("req_id",$req_id)->first();
                $new_request_no = $req_det->req_no;
                $new_request_date = $req_det->req_date;
                $new_po_type = $req_det->po_type;
                $request_arr = array();
                foreach($po_order as $index=>$part)
                {
                    $part['req_id'] = $req_id;
                    $part['req_no'] = $new_request_no;
                    $part['req_date'] = $new_request_date;
                    $part['po_type'] = $new_po_type;
                    $request_arr[] = $part;
                }
        
                $SCRequestInventoryPart = new SCRequestInventoryPart();
                
                if(SCRequestInventoryPart::whereRaw("req_id='$req_id'")->delete())
                {
                    if($SCRequestInventoryPart->insert($request_arr))
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
            return redirect('req-inv-entry-sc');           
    }
    
    
    public function get_rate_by_spare_part(Request $request)
    {
        
        $brand_id =    addslashes($request->input('brand_id'));
        $product_category_id =    addslashes($request->input('product_category_id'));
        $product_id =    addslashes($request->input('product_id'));
        $model_id =    addslashes($request->input('model_id'));
        $part_name = $request->input('part_name');
        $part_no = $request->input('part_no');
        $hsn_code = $request->input('hsn_code');
        
        //echo "brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code' AND part_status='1'";exit;
        $part_rate = SparePart::whereRaw("brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code' AND part_status='1'")->first();
        
        if(empty($part_rate->customer_price))
        {
            echo '0'; exit;
        }
        else
        {
            $customer_price = $part_rate->customer_price;
            $landing_cost = $part_rate->landing_cost;
            $discount = $part_rate->discount;
            
            if(!empty($discount))
            {
                $balance = $customer_price-$landing_cost;
                $total_discount = round($balance*$discount/100,2);
                $center_price = $customer_price-$total_discount;
                echo $center_price; exit;
            }
            else
            {
                echo $customer_price;
            }
            //echo $part_rate->part_rate; exit;
        }
        
        exit;
    }
    
    public function add_req_part(Request $request)
    {  
        $center_id = Auth::user()->table_id;
        $random_no = $request->input('sp_id');
        $brand_id = $request->input('brand_id');
        $product_category_id = $request->input('product_category_id');
        $product_id = $request->input('product_id');
        $model_id = $request->input('model_id');
        
        $filter_qry = "";
        if($brand_id!='All')
        {
            $filter_qry .= " and brand_id='$brand_id'";
        }
        if($product_category_id!='All')
        {
            $filter_qry .= " and product_category_id='$product_category_id'";
        }
        if($product_id!='All')
        {
            $filter_qry .= " and product_id='$product_id'";
        }
        if($model_id!='All')
        {
            $filter_qry .= " and model_id='$model_id'";
        }
        if($random_no!='All')
        {
            $filter_qry .= " and spare_id='$random_no'";
        }
        //echo $filter_qry; exit;
        $part_det = SparePart::whereRaw("1=1 $filter_qry  and part_status='1'")->first();
        $part_name = $part_det->part_name;
        $part_code = $part_det->part_no;
        
        $Inventory = InventoryCenter::whereRaw("center_id='$center_id' and brand_id='$brand_id'  and spare_id='$random_no'")->first();
        $bal_qty = $Inventory->avail_qty;
        
        if(empty($bal_qty) || $bal_qty < 0)
        {
            $bal_qty=0;
        }

        
        $inv_add = InventoryAdd::whereRaw("brand_id='$brand_id'  and spare_id='$random_no'")->first();
        $inw_part = InwardInventoryPart::whereRaw("brand_id='$brand_id'  and spare_id='$random_no'")->first(); 
        
        $landing_cost = $inw_part->asc_amount;
        if(empty($landing_cost))
        {            
            $landing_cost = $inv_add->customer_price;
        }
        
        $last_purchase_amount = 0;
        $last_det = SCRequestInventoryPart::whereRaw("center_id='$center_id' and brand_id='$brand_id'  and spare_id='$random_no'")->orderBy('req_part_id', 'desc')->first(); 
        $last_purchase_amount = $last_det->po_amt;


        $po_inv_parts = SCRequestInventoryPart::whereRaw("brand_id='$brand_id'  and spare_id='$random_no'")->latest()->first();
        $inw_parts_qty = InwardInventoryPart::whereRaw("brand_id='$brand_id'  and spare_id='$random_no'")->latest()->first();
        $qty = $po_inv_parts->qty;

        $inw_qty = $inw_parts_qty->item_qty;

        $previous_qty = ($qty-$inw_qty); 
        if ($previous_qty < 0) 
        {
            $previous_qty = 0;
        }?>
        <div class="form-row" id="part_div<?php echo $random_no;?>">
    
            <div class="col-md-2">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Part Name <font color="red">*</font></label>
                    <select id="part_name<?php echo $random_no;?>" name="SparePart[<?php echo $random_no;?>][part_name]" class="form-control"  >
                        <option value="<?php echo $part_name; ?>"><?php echo $part_name.' - '.$part_code; ?></option>
                    </select>
                </div>
            </div>
    
            <div class="col-md-1">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Color <font color="red">*</font></label>
                    <input type="text" autocomplete="off" name="SparePart[<?php echo $random_no;?>][color]" id="color<?php echo $random_no;?>" value=""  class="form-control" required="" />
                </div>
            </div>
    
            <div class="col-md-2">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Current Stock Qty. <font color="red">*</font></label>
                    <input type="text" autocomplete="off" name="SparePart[<?php echo $random_no;?>][current_qty]" id="current_qty<?php echo $random_no;?>" value="<?php echo $bal_qty; ?>" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" readonly/>
                </div>
            </div>

            <div class="col-md-2">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Required Qty. <font color="red">*</font></label>
                    <input type="text" autocomplete="off" name="SparePart[<?php echo $random_no;?>][req_qty]" id="req_qty<?php echo $random_no;?>" value=""  oninput="calculateTotal(<?php echo $random_no;?>)" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                </div>
            </div>

            <div class="col-md-2">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Previous Qty. <font color="red">*</font></label>
                    <input type="text" autocomplete="off" name="SparePart[<?php echo $random_no;?>][previous_qty]" id="previous_qty<?php echo $random_no;?>" value="<?php echo $previous_qty; ?>" oninput="calculateTotal(<?php echo $random_no;?>)" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                </div>
            </div>

            <div class="col-md-1">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Total Qty. <font color="red">*</font></label>
                    <input type="text" autocomplete="off" name="SparePart[<?php echo $random_no;?>][total_qty]" id="total_qty<?php echo $random_no;?>" value="<?php echo $stock_qty;?>" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                </div>
            </div>
            <!-- <div class="col-md-2">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Current Purchase Amount <font color="red">*</font></label>
                    <input type="text" autocomplete="off" name="SparePart[<?php //echo $random_no;?>][curr_amt]" id="curr_amt<?php //echo $random_no;?>" value="<?php //echo $landing_cost; ?>" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                </div>
            </div> -->
            <div class="col-md-2">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Last Purchase Amount <font color="red">*</font></label>
                    <input type="text" autocomplete="off" name="SparePart[<?php echo $random_no;?>][po_amt]" id="po_amt<?php echo $random_no;?>" value="<?php echo $last_purchase_amount; ?>" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                </div>
            </div>
        </div>
        <?php exit; 
    }



    public function issue_without_po(Request $request)
    {
        Session::put("page-title","Issue Part Without PO");
        $part_arr =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);
        $find_max_request_no=RequestNoSc::selectRaw('request_no')->whereRaw("request_date=curdate()")->first();

        $supplier_json           =   SupplierMaster::whereRaw(" active_status='1'")->orderByRaw('supplier_name ASC')->get(); 
        $supplier_arr = json_decode($supplier_json,true);

        $UserType = Session::get('UserType');
        $Center_Id = Auth::user()->table_id;

        

        $qr2 = "SELECT tsc.center_id,center_name,asc_code,tsc.city,sm.state_name FROM tbl_service_centre  tsc
            INNER JOIN users us ON tsc.email_id = us.email
            INNER JOIN state_master sm ON tsc.state = sm.state_id
            WHERE sc_status='1'  order by center_name"; 
        $asc_master           =   DB::select($qr2); 

        $new_request_no = "";
        $request_no_date = date('Y/m/d/');
        if(empty($find_max_request_no))
        {
            //$request_entry_arr = new RequestNoSc();
            //$request_entry_arr->request_date = date('Y-m-d');
            //$request_entry_arr->request_no = '1';
            //$request_entry_arr->save();

            $new_request_no = "Sup/$request_no_date".'0001';
        }
        else
        {
            $str_no = "0000";
            $no = $find_max_request_no->request_no;
            $no = $no+1;
            $len = strlen($str_no);
            $newlen = strlen("$no");
            $new_no = substr_replace($str_no, $no, $len-$newlen,$newlen);
            $new_request_no = "Sup/$request_no_date".$new_no;
        }

    

        $qry_product = "SELECT pm.product_id,pm.product_name FROM product_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND product_status='1'
        WHERE  pm.brand_id='$brand_id' $product_qry_search";#die;
        //echo $qry_product;exit;
        $product_master = DB::select($qry_product);

        $qry_model = "SELECT mm.model_id,mm.model_name FROM model_master mm 
        INNER JOIN brand_master bm ON mm.brand_id = bm.brand_id AND brand_status='1' AND model_status='1'
        WHERE  mm.brand_id='$brand_id' $model_qry_search";#die;
        //echo $qry_model;exit;
        $model_master = DB::select($qry_model);
        
                
        $url = $_SERVER['APP_URL'].'/issue-without-po';
        return view('issue-without-po')
        ->with('new_request_no', $new_request_no)
        ->with('brand_arr', $brand_arr)
        ->with('supplier_arr', $supplier_arr)
        ->with('asc_master', $asc_master)
        ->with('part_arr', $part_arr)
        ->with('brand_id',$brand_id)
        ->with('product_category', $product_category)
        ->with('product_master', $product_master)
        ->with('model_master', $model_master)
        ->with('product', $product)
        ->with('po_status',$po_status)
        ->with('url', $url);
    }


    public function save_issue_without_po(Request $request)
    {
        
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        $center_id = $request->input('asc_code');
        $srno = $request->input('srno');
        //$spare_id =  $request->input('part_code');
        $po_type =  $request->input('po_type');
        $job_no = $request->input('job_no');
        
        $customer_amount = $request->input('customer_amount');
        $asc_amount = $request->input('asc_amount');
        $color = $request->input('color');
        $discount = $request->input('discount');
        $gst = $request->input('gst');
        $net_total = $request->input('net_total');
        
        $case_type = '';
        $qty = 0;
        $total = '';
        //$brand = $request->input('brand');
        //$part_name =  $spare_part['part_name'];
        $remarks = addslashes($request->input('remarks'));
        //$supplier_name =  $request->input('supplier_name');
         
        #print_r($request->all()); exit;
        
        
        $po_order = array(); $index = 0;
        
        
            if(empty($srno))
            {
                Session::flash('message', "Please Select Serial No.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            $job_id = '';
            if(!empty($job_no))
            {
                $tag_det = TaggingMaster::whereRaw("job_no='$job_no'")->first();
                if(empty($tag_det))
                {
                    Session::flash('message', "Job No. Not Matched.");
                    Session::flash('alert-class', 'alert-danger');
                    return back();
                }
                $job_id = $tag_det->TagId;
                $case_type = 'Job Case';
            }
            
           // echo $srno;exit;
            $Srno_item = InventoryItemList::whereRaw("id='$srno'")->first();
            $spare_id = $Srno_item->part_id; 
            $SparePart = SparePart::whereRaw("spare_id='$spare_id'")->first();
            //print_r($SparePart);exit;
            $index = 1;
            $po_order[$index]['center_id'] = $center_id;
            $po_order[$index]['brand_id'] = $brand_id = $SparePart->brand_id;
            $po_order[$index]['product_category_id'] = $product_category_id = $SparePart->product_category_id;
            $po_order[$index]['product_id'] = $product_id =$SparePart->product_id;
            $po_order[$index]['model_id'] = $model_id = $SparePart->model_id;
            $po_order[$index]['spare_id'] = $spare_id;
            
            $po_order[$index]['part_name'] = $SparePart->part_name;
            $po_order[$index]['part_no'] = $SparePart->part_no;
            $po_order[$index]['color'] = $color;
            $po_order[$index]['sr_no'] = $part['sr_no'];
            $po_order[$index]['current_qty'] = $part['current_qty'];
            $po_order[$index]['req_qty'] = $part['req_qty'];
            $po_order[$index]['qty_pending'] = $part['req_qty'];
            
            $po_order[$index]['curr_amt'] = $part['curr_amt'];
            $po_order[$index]['po_amt'] = $part['po_amt'];
            $po_order[$index]['po_type'] = $po_type;
            $po_order[$index]['created_at'] = $created_at;
            $po_order[$index]['created_by'] = $created_by;
            $qty +=  $part['req_qty'];
            $total += ($part['req_qty']*$part['po_amt']);
            $index++;
           
        
        
        if(empty($po_order))
        {
            Session::flash('error', "Please Fill All Details.");
            Session::flash('alert-class', 'alert-danger');
            Session::flash('tab', "1");
            return back();
            
        }
        
        $inward_det = InwardInventoryPart::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
        //HERE WORKS STARTS FOR OutwardInventoryPart
        $OutwardInventoryPart = new OutwardInventoryPart();
        $OutwardInventoryPart->po_no = 'Issue without PO';
        $OutwardInventoryPart->po_date = $created_at;
        $OutwardInventoryPart->po_type = $po_type;
        $OutwardInventoryPart->case_type = $case_type;
        $OutwardInventoryPart->job_id = $job_id;
        
        $OutwardInventoryPart->job_no = $job_no; 
        $OutwardInventoryPart->center_id = $center_id;
        $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
        $OutwardInventoryPart->asc_name = $center_det->center_name; 
        $OutwardInventoryPart->asc_code = $center_det->asc_code;
        
        
        $OutwardInventoryPart->brand_id =  $brand_id;
        $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
        $OutwardInventoryPart->brand_name =  $brand_det->brand_name;
        //$OutwardInventoryPart->product_id = $product_id; 
        
        
        $OutwardInventoryPart->model_id = $model_id;
        $model_det = ModelMaster::whereRaw("model_id='$model_id'")->first();
        $OutwardInventoryPart->model_name = $model_det->model_name;
        $OutwardInventoryPart->spare_id = $spare_id; 
        $OutwardInventoryPart->part_name = $SparePart->part_name;
        $OutwardInventoryPart->part_no = $SparePart->part_no;
        $OutwardInventoryPart->color = $color;
        
        
        #print_r($inward_det);die;
        $OutwardInventoryPart->hsn_code = $inward_det->hsn_code; 
        $OutwardInventoryPart->gst = $gst; 
        $OutwardInventoryPart->req_qty = 1; 
        $OutwardInventoryPart->issued_qty = 1;
        
        $OutwardInventoryPart->remarks = $remarks;
        $OutwardInventoryPart->req_id = $inward_det->part_inw_id;
        $OutwardInventoryPart->po_id = $inward_det->inw_id;
        
        //$OutwardInventoryPart->purchase_amt = $inward_det->purchase_amt; 
        $OutwardInventoryPart->discount = $discount;
        $OutwardInventoryPart->asc_amount = $asc_amount; 
        $OutwardInventoryPart->customer_amount = $customer_amount; 
        $OutwardInventoryPart->save();
        
        $out_id = $OutwardInventoryPart->id;
            
        $req_date = date('Y-m-d');
        $req_id = '';        

        $request_arr = array();
        $outward_arr = array();
        $approved_arr = array();
        
        foreach($po_order as $index=>$part)
        {
            //print_r($part);exit;
            $part['req_id'] = $req_id;
            $part['req_no'] = $new_request_no;
            $part['req_date'] = $req_date;
            $request_arr[] = $part;

            $out['job_id'] = $job_id;
            $out['po_no'] = $new_request_no;
            $out['po_date'] = $req_date;
            $out['po_type'] = $part['po_type'];
            $out['case_type'] = 'Issue without PO';
            //$out['color'] = $part['color'];
            $out['center_id'] = $part['center_id'];
            $center_id =  $part['center_id'];
            $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
            $out['asc_name']  = $center_det->center_name; 
            $out['asc_code'] = $center_det->asc_code;

            $out['brand_id'] =  $part['brand_id'];
            $brand_id = $part['brand_id'];
            $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
            $out['brand_name'] =  $brand_det->brand_name;

            $out['model_id'] = $part['model_id'];
            $model_id = $part['model_id'];
            $model_det = ModelMaster::whereRaw("model_id='$model_id'")->first();
            $out['model_name'] = $model_det->model_name;
            $out['spare_id'] = $part['spare_id'];
            $out['part_name'] = $part['part_name'];

            $spare_id = $part['spare_id'];
            $part_name = $part['part_name'];

            #echo "brand_id='$brand_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and spare_id='$spare_id'";die;
            $check_spare_part_rate = SparePart::whereRaw("brand_id='$brand_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and spare_id='$spare_id'")->first();
            $out['part_no'] = $check_spare_part_rate->part_no;
            $out['color'] = $color;

            $part_no = $check_spare_part_rate->part_no;



            $inward_det = InwardInventoryPart::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();

            $out['hsn_code'] = $inward_det->hsn_code; 
            $out['gst'] = $inward_det->gst; 
            $out['req_id'] = $inward_det->part_inw_id;
            $out['po_id'] = $inward_det->inw_id;
            $out['req_qty'] = $part['req_qty']; 
            $out['issued_qty'] = $part['req_qty'];
            $out['remarks'] = $remarks;
            $out['created_at']= $created_at;
            $out['created_by']= $created_by;

            $out['asc_amount'] = $inward_det->asc_amount; 
            $out['customer_amount'] = $inward_det->customer_amount;

            $outward_arr[] = $out;

            $approved['req_id']= 0;
            $approved['brand_id']= $brand_id;
            $approved['center_id']= $part['center_id'];
            $approved['po_type']= $po_type;
            
            if(strtolower($po_type)=='foc')
            {
                $approved['invoice_status']= '0';
            }
            
            
            $approved['remarks']= $remarks;
            $approved['created_at']= $created_at;
            $approved['created_by']= $created_by;
            $approved['req_no']= $job_no;
            $approved['case_type']= $case_type;
            
            

            
            $approved['asc_code']= $center_det->asc_code;
            $approved['pincode']= $center_det->pincode;
            $state_id = $center_det->state;
            $state = StateMaster::whereRaw("state_id='$state_id'")->first();
            $approved['state']= $state->state_name;
            $approved['center_name']= $center_det->center_name;
            

            $request1 = array();
            $req_id = 0;
            $center_id = $request1['center_id']   = $part['center_id'];
            $request1['req_id']      = $req_id;

            $request1['brand_id']    = $brand_id;
            $request1['product_id']  = $product_id;
            $request1['model_id']    = $model_id;
            $request1['part_name']   = $part_name;
            $request1['part_no']     = $part_no;
            $request1['qty']    = 1; 
            #echo "brand_id='$brand_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and spare_id='$spare_id'";die;
            $landing_cost = $check_spare_part_rate->landing_cost;
            $customer_price = $check_spare_part_rate->customer_price;
            $discount = $check_spare_part_rate->discount;
            $rate = $asc_amount - ($asc_amount*$discount/100);
            
            $hsn_code = $hsn_code;


            $request1['landing_cost']        = $asc_amount;
            $request1['customer_price'] = $customer_amount;
            $request1['discount'] = $discount;
            $request1['part_tax'] = $gst;
            $request1['rate'] = $rate;

            $actual_price = $customer_price;
            if(!empty($discount))
            {
                $balance = $customer_price-$landing_cost;
                $total_discount = round($balance*$discount/100,2);
                $actual_price = $customer_price-$total_discount;

            }
            $request1['rate']    = $actual_price;
            
            $total_tax = round($rate*$gst/100,2);
            $net_total = round($total_tax+$rate,2);

            $request1['total']       = $total;
            $request1['total_tax']       = $total_tax;
            $request1['net_total']       = $net_total;
            $request1['created_at']  = $created_at;
            $request1['created_by']  = $created_by;
            $approved['part_required']= 1;
            $approved['qty']= 1;
            $approved['total']= $total;
            $approved['total_tax']= $total_tax; 
            $approved['net_total']= $net_total;
            $approved['out_id'] = $out_id;

            $approved_arr[] = $approved;


        }

        
        
        if($OutwardInventoryPart->save())
        {
            $approve_id = "";
            
            
            $ApproveRequestInventory = new ApproveRequestInventory();
            if($ApproveRequestInventory->insert($approved_arr))
            {
                #$approve_id = $ApproveRequestInventory->id;                
                $approve_id = DB::getPdo()->lastInsertId();
                $RequestInventoryPart = new ApproveRequestInventoryPart();

                $request1['approve_id'] = $approve_id;
                $request1['qty'] = $issued_qty;
                $request1['hsn_code'] = $hsn_code;
                $request_arr1[] = $request1;

                #print_r($request_arr1);die;
                if($RequestInventoryPart->insert($request_arr1))
                {
                    $UpdateInventory = array();
                    $UpdateInventory['part_status_pending'] = '0';
                    $UpdateInventory['part_reject'] = '0';
                    $UpdateInventory['part_approve'] = '1';
                    $UpdateInventory['approve_date'] = $created_at;
                    //ApproveRequestInventoryPart::whereRaw("req_id='$req_id'")->update(array('approve_id'=>$approve_id));
                }

            }
            DB::beginTransaction();
            #print_r($outward_arr);die;
            $Inventory = Inventory::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->first();
            $bal_qty = (int)$Inventory->bal_qty;



            $new_bal_qty = $bal_qty-$issued_qty;
            //echo "brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'";exit;
            if(Inventory::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id'")->update(array('bal_qty'=>$new_bal_qty)))
            {
                if(InventoryItemList::whereRaw("id='$srno'")->update(array('approve_id'=>$approve_id,'out_po_no'=>'Issued Without PO','asc_id'=>$center_id,'is_out'=>'1')))
                {
                    DB::commit();
                    Session::flash('message', "Issue Part $new_request_no  Successfully.");
                    Session::flash('alert-class', 'alert-success');
                }
                else
                {
                    DB::rollback();
                    Session::flash('error', "Part Details For Srno. not Found.");
                }

                #return back();
            }
            else
            {
                DB::rollback();
                 Session::flash('error', "Inventory Details For Part Not Found");
                #return back();
                //echo 'Inventory Details For Part Not Found';exit;
            }
        }
         else
            {
                DB::rollback();
                 Session::flash('error', "Part Details Not Saved. Please Try Again.");
                #return back();
                //echo 'Inventory Details For Part Not Found';exit;
            }   
                


            
            // $OutwardInventoryPart = new OutwardInventoryPart();
            // $SCRequestInventoryPart->insert($request_arr);            
            
        
        
            
            //exit;
            return redirect('issue-without-po');           
    }
    
}

