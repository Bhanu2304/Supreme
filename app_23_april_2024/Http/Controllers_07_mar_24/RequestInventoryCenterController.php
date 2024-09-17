<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\BrandMaster;
use App\SparePart;
use App\Inventory;
use App\RequestNoSc;
use App\InventoryCenter;
use App\SCRequestInventoryPart;
use App\SCRequestInventory;
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

                $new_request_no = "";
                $request_no_date = date('Y/m/d/');
                if(empty($find_max_request_no))
                {
                    //$request_entry_arr = new RequestNoSc();
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
        
        $req_arr           =   DB::select("SELECT *,brand_name,category_name,product_name,model_name,date_format(ri.req_date,'%d_%m_%y') created_at FROM sc_request_inventory ri 
            inner join brand_master bm on ri.brand_id = bm.brand_id
            inner join product_category_master cat on ri.product_category_id = cat.product_category_id
            inner join product_master pm on ri.product_id = pm.product_id
            inner join model_master mm on ri.model_id = mm.model_id
        WHERE part_status_pending='1' ");        
                
        $url = $_SERVER['APP_URL'].'/req-inv-entry-sc';
        return view('req-inv-entry-sc')
        ->with('new_request_no', $new_request_no)
        ->with('brand_arr', $brand_arr)
        ->with('part_arr', $part_arr)
        ->with('req_arr', $req_arr)    
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
         
        //print_r($spare_part); exit;
        
        
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
                Session::flash('message', "Current Purchase Amount should not be empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            
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
           
        }
        
        if(empty($po_order))
        {
            Session::flash('error', "Please Fill All Details.");
            Session::flash('alert-class', 'alert-danger');
            Session::flash('tab', "1");
            return back();
            
        }
        
            $SCRequestInventory = new SCRequestInventory();
            
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
            $request_no_date = date('Y/m/d/');

            $find_max_request_no=RequestNoSc::selectRaw('request_no')->whereRaw("request_date=curdate()")->first();

            $new_request_no = "";
            if(empty($find_max_request_no))
            {
                $request_entry_arr = new RequestNoSc();
                $request_entry_arr->request_date = date('Y-m-d');
                $request_entry_arr->request_no = '1';
                $request_entry_arr->save();

                $new_request_no = "Sup/$request_no_date"."$center_id/".$po_type.'00001';
            }
            else
            {
                $str_no = "00000";
                $no = $find_max_request_no->request_no;
                $no = $no+1;
                $len = strlen($str_no);
                $newlen = strlen("$no");
                $new_no = substr_replace($str_no, $no, $len-$newlen,$newlen);
                $new_request_no = "Sup/$request_no_date"."$center_id/".$po_type.$new_no;

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
        

       //print_r($record); exit;
        $url = $_SERVER['APP_URL'].'/view-req-inv-sc';
        return view('view-req-inv-sc')
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
        $req_det  = SCRequestInventory::where("req_id",$req_id)->first();
        $data_part_arr  = SCRequestInventoryPart::where("req_id",$req_id)->get();
        
        $brand_arr   = DB::select("SELECT brand_id,brand_name FROM brand_master WHERE brand_status='1' ");
        $brand_id = $req_det->brand_id;
        $product_category_id =$req_det->product_category_id;
        $product_id = $req_det->product_id;
        $product_det =  DB::select("SELECT product_category_id,category_name FROM product_category_master WHERE brand_id='$brand_id' and category_status='1' ");
        $product_mas =  DB::select("SELECT product_id,product_name FROM product_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_status='1' ");
        $model_det =  DB::select("SELECT model_id,model_name FROM model_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and  model_status='1' ");
        

       //print_r($record); exit;
        $url = $_SERVER['APP_URL'].'/view-req-inv-sc';
        return view('edit-req-inv-sc')
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
            else if(empty($part['po_amt']))
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
    
    ?>
        <div class="form-row" id="part_div<?php echo $random_no;?>">
    
            <div class="col-md-2">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Part Name <font color="red">*</font></label>
                    <select id="part_name<?php echo $random_no;?>" name="SparePart[<?php echo $random_no;?>][part_name]" class="form-control"  >
                        <option value="<?php echo $part_name; ?>"><?php echo $part_name; ?></option>
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
                    <input type="text" autocomplete="off" name="SparePart[<?php echo $random_no;?>][current_qty]" id="current_qty<?php echo $random_no;?>" value="" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                </div>
            </div>

            <div class="col-md-2">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Required Qty. <font color="red">*</font></label>
                    <input type="text" autocomplete="off" name="SparePart[<?php echo $random_no;?>][req_qty]" id="req_qty<?php echo $random_no;?>" value="" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Current Purchase Amount <font color="red">*</font></label>
                    <input type="text" autocomplete="off" name="SparePart[<?php echo $random_no;?>][curr_amt]" id="curr_amt<?php echo $random_no;?>" value="" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                </div>
            </div>
            <div class="col-md-2">
                <div class="position-relative form-group"><label for="examplePassword11" class="">Last Purchase Amount <font color="red">*</font></label>
                    <input type="text" autocomplete="off" name="SparePart[<?php echo $random_no;?>][po_amt]" id="po_amt<?php echo $random_no;?>" value="" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                </div>
            </div>
    

</div>
    <?php exit; }
    
}

