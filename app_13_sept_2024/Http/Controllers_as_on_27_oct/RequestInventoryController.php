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
use DB;
use Auth;
use Session;


class RequestInventoryController extends Controller
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
        Session::put("page-title","Inventory Request");
        $part_arr =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);
        
        
        $url = $_SERVER['APP_URL'].'/req-inv-entry';
        return view('req-inv-entry')
        ->with('brand_arr', $brand_arr)
        ->with('part_arr', $part_arr)
        ->with('url', $url);
    }
    
    public function save_req_inv(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        $Center_Id = Auth::user()->table_id;
        $spare_part =  $request->input('SparePart');
        $part_name =  $spare_part['part_name'];
        $remarks = $request->input('remarks');
             
        $qty = 0; $total = 0;
        foreach($part_name as $key=>$part)
        {
            if(empty($part))
            {
                Session::flash('message', "Spare Part Name Should Not Empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($spare_part['part_no'][$key]))
            {
                Session::flash('message', "HSN Code Should Not Empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($spare_part['hsn_code'][$key]))
            {
                Session::flash('message', "HSN Code Should Not Empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            } 
            else if(empty($spare_part['qty'][$key]))
            {
                Session::flash('message', "Quantity Should not be 0.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            } 
            
            
            $qty += $spare_part['qty'][$key];
            $total += $spare_part['qty'][$key]*$spare_part['rate'][$key];
        }
        
        
        
            $RequestInventory = new RequestInventory();
            
            $RequestInventory->center_id= $Center_Id;
            $RequestInventory->part_required= count($part_name);
            $RequestInventory->qty= $qty;
            $RequestInventory->total= $total;
            $RequestInventory->part_status_pending= 1;
            $RequestInventory->remarks= $remarks;
            $RequestInventory->created_at= $created_at;
            $RequestInventory->created_by= $created_by;
            $request_no_date = date('Y/m/d/');

                $find_max_request_no=RequestNo::selectRaw('request_no')->whereRaw("request_date=curdate()")->first();

                $new_request_no = "";
                if(empty($find_max_request_no))
                {
                    $request_entry_arr = new RequestNo();
                    $request_entry_arr->request_date = date('Y-m-d');
                    $request_entry_arr->request_no = '1';
                    $request_entry_arr->save();

                    $new_request_no = "Sup/Req/$request_no_date".'00001';
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

                    RequestNo::whereRaw("request_date=curdate()")->update(array('request_no'=>$no));

                }
                $RequestInventory->req_no= $new_request_no;
                
                
            
            if($RequestInventory->save())
            {
                $req_id = $RequestInventory->id;
                
                $request_arr = array();
                foreach($part_name as $key=>$part)
                {
                    $request = array();
                    $request['center_id']   = $Center_Id;
                    $request['req_id']   = $req_id;
                    $request['brand_id']     = $spare_part['brand_id'][$key];
                    $request['product_category_id']    = $spare_part['product_category_id'][$key];
                    $request['product_id']    = $spare_part['product_id'][$key];
                    $request['model_id']     = $spare_part['model_id'][$key];
                    $request['part_name']   = $part;
                    $request['part_no']     = $spare_part['part_no'][$key];
                    $request['hsn_code']    = $spare_part['hsn_code'][$key];
                    $request['part_no']     = $spare_part['part_no'][$key];
                    $request['rate']        = $spare_part['rate'][$key];
                    $request['qty']         = $spare_part['qty'][$key];
                    $request['qty_pending'] = $spare_part['qty'][$key];
                    
                    $request['total']       = $spare_part['total'][$key];
                    $request['created_at']  = $created_at;
                    $request['created_by']  = $created_by;
                    $request_arr[] = $request;

                    
                }
        
                $RequestInventoryPart = new RequestInventoryPart();
                if($RequestInventoryPart->insert($request_arr))
                {                       
                    Session::flash('message', "Inventory Request Process Successfully.");
                    Session::flash('alert-class', 'alert-success');
                }
                else
                {
                    Session::flash('error', "Inventory Request Failed. Please Try Again");
                    Session::flash('alert-class', 'alert-danger');
                    return back();
                }
            }
            return redirect('req-inv-entry');           
    }
    
    
    public function view()
    {
        Session::put("page-title","View Inventory Request");
        $req_arr           =   DB::select("SELECT * FROM request_inventory ri 
        INNER JOIN tbl_service_centre sc ON ri.center_id=sc.center_id 
        WHERE part_status_pending='1' ");
        $url = $_SERVER['APP_URL'].'/view-req-inv';
        return view('view-req-inv')
        ->with('req_arr', $req_arr)
        ->with('url', $url);
    }
    
    public function edit(Request $request)
    {
        Session::put("page-title","Edit Inventory Request");
        
        $req_id     = base64_decode($request->input('req_id'));  
        $data_part_arr  = RequestInventoryPart::where("req_id",$req_id)->get();
        $req_det  = RequestInventory::where("req_id",$req_id)->first();
        $brand_arr   = DB::select("SELECT brand_id,brand_name FROM brand_master WHERE brand_status='1' ");
        
        
        //print_r($data_part_arr);exit;
        
        $record = array();
        foreach($data_part_arr as $part)
        {
            $brand_id = $part->brand_id;
            $product_det =  DB::select("SELECT product_category_id,category_name FROM product_category_master WHERE brand_id='$brand_id' and category_status='1' ");
            
            foreach($product_det as $det)
            {
                $record['product_category_id'][$brand_id][$det->product_category_id] = $det->category_name;
            }
            
            
            $product_category_id =$part->product_category_id;
            $product_mas =  DB::select("SELECT product_id,product_name FROM product_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_status='1' ");
            
            foreach($product_mas as $det)
            {
                $record['product_id'][$product_category_id][$det->product_id] = $det->product_name;
            }
            
            $product_id = $part->product_id;
            $model_det =  DB::select("SELECT model_id,model_name FROM model_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and  model_status='1' ");
            foreach($model_det as $det)
            {
                $record['model_id'][$product_id][$det->model_id] = $det->model_name;
            }
            
            $model_id = $part->model_id;
            $part_det =  DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id'");
            foreach($part_det as $det)
            {
                $record['part_name'][$model_id][] = $det->part_name;
            }
            
            $part_name = $part->part_name;
            $part_no_det =  DB::select("SELECT spare_id,part_no FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' ");
            
            foreach($part_no_det as $det)
            {
                $record['part_no'][$part_name][] = $det->part_no;
            }
            
            $part_no = $part->part_no;
            $part_no_det =  DB::select("SELECT spare_id,hsn_code FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no'");
            
            foreach($part_no_det as $det)
            {
                $record['hsn_code'][$part_no][] = $det->hsn_code;
            }    
        }
        
       //print_r($record); exit;
        $url = $_SERVER['APP_URL'].'/view-req-inv';
        return view('edit-req-inv')
                ->with('data', $data)
                ->with('brand_arr', $brand_arr)
                ->with('record', $record)
                ->with('req_det',$req_det)
                ->with('url',$url)
                ->with('data_part_arr', $data_part_arr);
    }
    
    
    public function update(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        $Center_Id = Auth::user()->table_id;
        $spare_part =  $request->input('SparePart');
        $part_name =  $spare_part['part_name'];
        $remarks = $request->input('remarks');
        $req_id =  $request->input('req_id');
             
        $qty = 0; $total = 0;
        foreach($part_name as $key=>$part)
        {
            if(empty($part))
            {
                Session::flash('message', "Spare Part Name Should Not Empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($spare_part['part_no'][$key]))
            {
                Session::flash('message', "HSN Code Should Not Empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($spare_part['hsn_code'][$key]))
            {
                Session::flash('message', "HSN Code Should Not Empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            } 
            else if(empty($spare_part['qty'][$key]))
            {
                Session::flash('message', "Quantity Should not be 0.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            } 
            
            
            $qty += $spare_part['qty'][$key];
            $total += $spare_part['qty'][$key]*$spare_part['rate'][$key];
        }
        
        
        
            $RequestInventory = array();
            
            $RequestInventory['center_id']= $Center_Id;
            $RequestInventory['part_required']= count($part_name);
            $RequestInventory['qty']= $qty;
            $RequestInventory['total']= $total;
            $RequestInventory['part_status_pending']= 1;
            $RequestInventory['remarks']= $remarks;
            $RequestInventory['created_at']= $created_at;
            $RequestInventory['created_by']= $created_by;
            
            
            if(RequestInventory::whereRaw("req_id='$req_id'")->update($RequestInventory))
            {
                
                
                $request_arr = array();
                foreach($part_name as $key=>$part)
                {
                    $request = array();
                    $request['center_id']   = $Center_Id;
                    $request['req_id']   = $req_id;
                    $request['brand_id']     = $spare_part['brand_id'][$key];
                    $request['product_category_id']    = $spare_part['product_category_id'][$key];
                    $request['product_id']    = $spare_part['product_id'][$key];
                    $request['model_id']     = $spare_part['model_id'][$key];
                    $request['part_name']   = $part;
                    $request['part_no']     = $spare_part['part_no'][$key];
                    $request['hsn_code']    = $spare_part['hsn_code'][$key];
                    $request['part_no']     = $spare_part['part_no'][$key];
                    $request['rate']        = $spare_part['rate'][$key];
                    $request['qty']         = $spare_part['qty'][$key];
                    $request['qty_pending'] = $spare_part['qty'][$key];
                    $request['total']       = $spare_part['total'][$key];
                    $request['created_at']  = $created_at;
                    $request['created_by']  = $created_by;
                    $request_arr[] = $request;

                    
                }
        
                $RequestInventoryPart = new RequestInventoryPart();
                
                if(RequestInventoryPart::whereRaw("req_id='$req_id'")->delete())
                {
                    if($RequestInventoryPart->insert($request_arr))
                    {                       
                        Session::flash('message', "Inventory Request Process Successfully.");
                        Session::flash('alert-class', 'alert-success');
                    }
                    else
                    {
                        Session::flash('error', "Inventory Request Failed. Please Try Again");
                        Session::flash('alert-class', 'alert-danger');
                        return back();
                    }
                }
                
                
            }
            return redirect('view-req-inv');           
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
    
    public function add_req_part()
    {  $random_no = rand(100,10000); 
        $brand_arr           =   DB::select("SELECT * FROM brand_master WHERE brand_status='1' ");
    
    
    ?>
<div class="form-row" id="part_div<?php echo $random_no;?>">
    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand <font color="red">*</font></label>
                                            <select id="brand_id<?php echo $random_no;?>" name="SparePart[brand_id][]" onchange="get_product_detail('<?php echo $random_no;?>',this.value)" class="form-control" required="">
                                                <option value="">Brand</option>
                                                <?php
                                                        foreach($brand_arr as $brand)
                                                        {
                                                            ?>       <option value="<?php echo $brand->brand_id; ?>"><?php echo $brand->brand_name; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product Detail<font color="red">*</font></label>
                                            <select id="product_category_id<?php echo $random_no;?>" name="SparePart[product_category_id][]" onchange="get_product('<?php echo $random_no;?>',this.value)" class="form-control" required="">
                                                <option value="">Product</option>
                                                
                                            </select>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product <font color="red">*</font></label>
                                            <select id="product_id<?php echo $random_no;?>" name="SparePart[product_id][]" onchange="get_model('<?php echo $random_no;?>',this.value)" class="form-control" required="">
                                                <option value="">Product</option>
                                                
                                            </select>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model <font color="red">*</font></label>
                                            <select id="model_id<?php echo $random_no;?>" name="SparePart[model_id][]" onchange="get_part_name('<?php echo $random_no;?>',this.value)" class="form-control" required="">
                                                <option value="">Model</option>
                                            </select>
                                        </div>
                                        
                                    </div>
    <div class="col-md-2">
        <div class="position-relative form-group"><label for="examplePassword11" class="">Spare Part </label>
            <select id="part_name<?php echo $random_no;?>" name="SparePart[part_name][]" class="form-control" onchange="get_partno('<?php echo $random_no;?>',this.value)" >
                <option value="">Select</option>
               
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="position-relative form-group"><label for="examplePassword11" class="">Part No. </label>
            <select id="part_no<?php echo $random_no;?>" name="SparePart[part_no][]" class="form-control" onchange="get_hsn_code('<?php echo $random_no;?>',this.value)" >
                <option value="">Select</option>

            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="position-relative form-group"><label for="examplePassword11" class="">HSN Code </label>
            <select id="hsn_code<?php echo $random_no;?>" name="SparePart[hsn_code][]" onchange="get_rate('rate<?php echo $random_no;?>','<?php echo $random_no;?>')" class="form-control" required="">
                <option value="">Select</option>
            </select>
        </div>
    </div>
    
    <div class="col-md-1">
        <div class="position-relative form-group"><label for="examplePassword11" class="">Rate </label>
            <input type="text" name="SparePart[rate][]" id="rate<?php echo $random_no;?>" value="0" onkeypress="return checkNumber(this.value,event)" onblur="cal_tot_val('<?php echo $random_no;?>')" class="form-control" required="" />
        </div>
    </div>
    
    <div class="col-md-1">
        <div class="position-relative form-group"><label for="examplePassword11" class="">Quantity </label>
            <input type="text" name="SparePart[qty][]" id="qty<?php echo $random_no;?>" value="0" onkeypress="return checkNumber(this.value,event)" onblur="cal_tot_val('<?php echo $random_no;?>')" class="form-control" required="" />
        </div>
    </div>

    <div class="col-md-2">
        <div class="position-relative form-group"><label for="examplePassword11" class="">Total </label>
            <input type="text" name="SparePart[total][]" id="total<?php echo $random_no;?>" value="0" class="form-control" required="" />
        </div>
    </div>
    <div class="col-md-1">
        <div class="position-relative form-group"><br/><br/>
            <span class="fa fa-plus"  onclick="add_part();"></span>
            <span class="fa fa-minus"  onclick="del_part('part_div<?php echo $random_no;?>');"></span>
        </div>
    </div>

</div>
    <?php exit; }
    
}

