<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\BrandMaster;
use App\SparePart;
use App\Inventory;
use App\InventoryAdd;
use App\InventoryCenter;
use DB;
use Auth;
use Session;


class InventoryController extends Controller
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
        Session::put("page-title","Inventory");
        
        $data_arr           =   DB::select("SELECT bm.brand_name,cm.category_name,pm.product_name,mm.model_name,inv_id,Part_Name,Part_No,hsn_code,landing_cost,customer_price,discount,stock_qty stock_qty FROM tbl_inventory ti
INNER JOIN brand_master bm ON ti.brand_id = bm.brand_id and brand_status='1' 
inner join product_category_master cm on ti.product_category_id= cm.product_category_id and category_status='1' 
INNER JOIN product_master pm ON ti.product_id = pm.product_id AND product_status='1' 
INNER JOIN model_master mm ON ti.model_id = mm.model_id AND model_status='1'
WHERE inv_status='1'; ");
        $consumption_arr           =   DB::select("SELECT part_name,part_no,hsn_code,count(1) cnsptn FROM tbl_inventory_part WHERE allocation_type='ho' group by Part_Name,Part_No,hsn_code");
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);
        
        
        $cnpt_arr = array();
        foreach($consumption_arr as $cnpt)
        {
            $cnpt_arr[$cnpt->part_name][$cnpt->part_no][$cnpt->hsn_code] = $cnpt->cnsptn;
        }
        
        $url = $_SERVER['APP_URL'].'/add-inv';
        return view('add-inv')
        ->with('url', $url)
                ->with('brand_arr', $brand_arr)
                ->with('data_arr', $data_arr)
                ->with('cnpt_arr', $cnpt_arr)
                ;
    }
    
    public function save_inv(Request $request){
        $created_by     =   Auth::User()->id;
        
        $invArr            =   new InventoryAdd();
        
        $part_name = addslashes($request->input('part_name'));
        $part_no =    addslashes($request->input('part_no'));
        $hsn_code =    addslashes($request->input('hsn_code'));
        $stock_qty =    addslashes($request->input('stock_qty'));
        $raw_no =    addslashes($request->input('raw_no'));
        
        $brand_id =    addslashes($request->input('brand_id'));
        $product_category_id =    addslashes($request->input('product_category_id'));
        $product_id =    addslashes($request->input('product_id'));
        $model_id =    addslashes($request->input('model_id'));
        //echo "brand_id ='$brand_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'"; exit;
        $spare_part_det = SparePart::whereRaw("brand_id ='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'")->first();
        $landing_cost =    $spare_part_det->landing_cost; 
        $customer_price =    $spare_part_det->customer_price;
        $discount =    $spare_part_det->discount;
        
        //$avg_consmptn =    addslashes($request->input('avg_consmptn'));
        //$bal_qty =    addslashes($request->input('bal_qty'));
        //$mol =    addslashes($request->input('mol'));    
        $invArr->brand_id=$brand_id;
        $invArr->product_category_id=$product_category_id;
        $invArr->product_id=$product_id;
        $invArr->model_id=$model_id;
        $invArr->part_name=$part_name;
        $invArr->part_no=$part_no;
        $invArr->hsn_code=$hsn_code;
        $invArr->stock_qty=$stock_qty;
        $invArr->raw_no=$raw_no;
        
        $invArr->landing_cost=$landing_cost;
        $invArr->customer_price=$customer_price;
        $invArr->discount=$discount;
        
        
        
        //$spareArr->avg_consmptn=$avg_consmptn;
        //$spareArr->bal_qty=$bal_qty;
       // $spareArr->mol=$mol;
        
        //$spareArr->part_status='1';
        
        $UserId = Auth::user()->id;    
        $invArr->created_by=$UserId; 
        $invArr->created_at=date("Y-m-d H:i:s");
        
        
            
        if(empty($part_name))
        {
            Session::flash('message', "Spare Part Name Should Not be Empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(empty($part_no))
        {
            Session::flash('message', "HSN Code Should Not be Empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(empty($hsn_code))
        {
            Session::flash('message', "Part No. Should Not be Empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(empty($raw_no))
        {
            Session::flash('message', "Raw No. Should Not be Empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(empty($landing_cost))
        {
            Session::flash('message', "Landing Cost Should Not be Empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(empty($customer_price))
        {
            Session::flash('message', "Customer Price Should Not be Empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        
        else if($invArr->save())
        {
            $spare_det_exist = Inventory::whereRaw("brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'")->first();
            if($spare_det_exist)
            {
                $main_inv = array();
                $old_qty = $spare_det_exist->stock_qty;
                if(empty($old_qty))
                {
                    $old_qty = 0;
                }
                $main_inv['stock_qty']=$stock_qty+$old_qty;
                $main_inv['landing_cost']=$landing_cost;
                $main_inv['customer_price']=$customer_price;
                $main_inv['discount']=$discount;
                
                if(Inventory::whereRaw("inv_id='{$spare_det_exist->inv_id}'")->update($main_inv))
                {
                    Session::flash('message', "Inventory Added Successfully.");
                    Session::flash('alert-class', 'alert-success');
                }
                else
                {
                    Session::flash('message', "Inventory Addition Failed. Please Try Again");
                    Session::flash('alert-class', 'alert-danger');
                }
            }
            else
            {
                $inv            =   new Inventory();
                $inv->brand_id=$brand_id;
                $inv->product_category_id=$product_category_id;
                $inv->product_id=$product_id;
                $inv->model_id=$model_id;
                $inv->part_name=$part_name;
                $inv->part_no=$part_no;
                $inv->hsn_code=$hsn_code;
                $inv->stock_qty=$stock_qty;
                $inv->landing_cost=$landing_cost;
                $inv->customer_price=$customer_price;
                $inv->discount=$discount;
                if($inv->save())
                {
                    Session::flash('message', "Inventory Added Successfully.");
                    Session::flash('alert-class', 'alert-success');
                }
                else
                {
                    Session::flash('message', "Inventory Addition Failed. Please Try Again!");
                    Session::flash('alert-class', 'alert-danger');
                }
            }
        }
        else
        {
            Session::flash('error', "Inventory Addition Failed. Please Try Again");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }    
        
        return redirect('add-inv');   
    }
    
    
    public function inv_details(Request $request)
    {
        Session::put("page-title","Inventory Details");
        $inv_id = base64_decode($request->input('tag_id'));
        $spare_det_exist = Inventory::selectRaw("brand_id,product_category_id,product_id,model_id,part_name,part_no,hsn_code")->whereRaw("inv_id='$inv_id'")->first();
        $part_name = $spare_det_exist->part_name;
        $part_no = $spare_det_exist->part_no;
        $hsn_code = $spare_det_exist->hsn_code;
        $brand_id =    $spare_det_exist->brand_id;
        $product_category_id =    $spare_det_exist->product_category_id;
        $product_id =    $spare_det_exist->product_id;
        $model_id =    $spare_det_exist->model_id;
        
        $qr = "SELECT inv_id,part_name,part_no,hsn_code,stock_qty,raw_no,landing_cost,customer_price,
discount,DATE_FORMAT(ti.created_at,'%d-%b-%Y %H:%i') created_at,bm.brand_name,cm.category_name,pm.product_name,mm.model_name FROM `tbl_inventory_add` ti
INNER JOIN brand_master bm ON ti.brand_id = bm.brand_id and bm.brand_id='$brand_id' and brand_status='1' 
inner join product_category_master cm on ti.product_category_id= cm.product_category_id and cm.product_category_id='$product_category_id' and category_status='1' 
INNER JOIN product_master pm ON ti.product_id = pm.product_id  and pm.product_id='$product_id' AND product_status='1' 
INNER JOIN model_master mm ON ti.model_id = mm.model_id and mm.model_id='$model_id' AND model_status='1'

WHERE inv_status='1'  AND part_name='$part_name' AND part_no='$part_no' AND hsn_code='$hsn_code'
ORDER BY ti.created_at DESC";
        
        $inv_arr           =   DB::select($qr);
        
        
        $url = $_SERVER['APP_URL'].'/add-inv';
        return view('inv-details')
        ->with('url', $url)
                ->with('data_arr', $inv_arr);
    }
    
    public function edit_inv(Request $request)
    {
        Session::put("page-title","Inventory Edit");
        
        $inv_id = base64_decode($request->input('inv_id'));  
        $data_json = Inventory::where("inv_id",$inv_id)->first();
        $data = json_decode($data_json,true);
        $Part_Name = $data['Part_Name']; 
        $Part_No = $data['Part_No'];
        
        $part_arr           =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        $part_no_arr           =   DB::select("SELECT spare_id,part_no FROM `tbl_spare_parts` WHERE part_name='$Part_Name' and part_status='1' ");
        $hsn_no_arr           =   DB::select("SELECT spare_id,hsn_code FROM `tbl_spare_parts` WHERE part_no='$Part_No' and part_status='1' ");
        
        
        return view('edit-inv')
                ->with('data', $data)
                ->with('part_arr', $part_arr)
                ->with('part_no_arr', $part_no_arr)
                ->with('hsn_no_arr', $hsn_no_arr);
    }
    
    
    public function update_inv(Request $request){
        
        
        
        
        $inv_id = addslashes($request->input('inv_id'));
        $part_name = addslashes($request->input('part_name'));
        $part_no =    addslashes($request->input('part_no'));
        $hsn_code =    addslashes($request->input('hsn_code'));
        $stock_qty =    addslashes($request->input('stock_qty'));
        
        
       
        $avg_consmptn =    addslashes($request->input('avg_consmptn'));
        $bal_qty =    addslashes($request->input('bal_qty'));
        $mol =    addslashes($request->input('mol'));
        
            
        $spareArr['part_name']=$part_name;
        $spareArr['part_no']=$part_no;
        $spareArr['hsn_code']=$hsn_code;
        $spareArr['stock_qty']=$stock_qty;
        $spareArr['avg_consmptn']=$avg_consmptn;
        $spareArr['bal_qty']=$bal_qty;
        $spareArr['mol']=$mol;
        
        
        //$spareArr->part_status='1';
        
        $UserId = Auth::user()->id;    
        $spareArr->updated_by=$UserId; 
        $spareArr->updated_at=date("Y-m-d H:i:s");
        
        
            
        if(empty($part_name))
        {
            Session::flash('message', "Spare Part Name Should Not Empty.");
            Session::flash('alert-class', 'alert-danger');
        }
        else if(empty($part_no))
        {
            Session::flash('message', "HSN Code Should Not Empty.");
            Session::flash('alert-class', 'alert-danger');
        }
        else if(empty($hsn_code))
        {
            Session::flash('message', "Part No. Should Not Empty.");
            Session::flash('alert-class', 'alert-danger');
        }    
        if(Inventory::whereRaw("inv_id='$inv_id'")->update($spareArr)){
                Session::flash('message', "Inventory Details Updated Successfully.");
                Session::flash('alert-class', 'alert-danger'); 
            }
            else{
                Session::flash('error', "Inventory Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            }    
        
       return redirect('add-inv'); 
    }
    
    public function get_part_name_by_model(Request $request)
    {
        //$country_id = $request->input('country_id');
        $brand_id = $request->input('brand_id');
        $product_category_id = $request->input('product_category_id');
        $product_id = $request->input('product_id');
        $model_id = $request->input('model_id');
        
        $part_master = DB::select("SELECT part_name FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' AND part_status='1'");
        
        if(empty($part_master))
        {
            echo '<option value="">No Spare Part Found</option>'; exit;
        }
        
        echo '<option value="">SPare Part Name</option>'; 
        
        
        foreach($part_master as $model)
        {
            echo '<option value="';
                echo $model->part_name.'">';
                echo $model->part_name.'</option>';
        }
        exit;
    }
    
    public function get_partno_by_part_name(Request $request)
    {
        //$country_id = $request->input('country_id');
        $part_name = $request->input('part_name');
        $brand_id = $request->input('brand_id');
        $product_category_id = $request->input('product_category_id');
        $product_id = $request->input('product_id');
        $model_id = $request->input('model_id');
        
        $part_master = DB::select("SELECT part_no FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' AND part_name='$part_name' AND part_status='1'");
        
        
        if(empty($part_master))
        {
            echo '<option value="">No PartNo. Found</option>'; exit;
        }
        
        echo '<option value="">Part No.</option>'; 
        
        
        foreach($part_master as $model)
        {
            echo '<option value="';
                echo $model->part_no.'">';
                echo $model->part_no.'</option>';
        }
        exit;
    }
    
    public function get_hsnno_by_part_no(Request $request)
    {
        //$country_id = $request->input('country_id');
        $part_no = $request->input('part_no');
        $brand_id = $request->input('brand_id');
        $product_category_id = $request->input('product_category_id');
        $product_id = $request->input('product_id');
        $model_id = $request->input('model_id');
        $part_name = $request->input('part_name');
        $qry = "";
        
        
        
        
        
        
        
        
        $part_master = DB::select("SELECT hsn_code FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' AND part_name='$part_name' AND part_no='$part_no' AND part_status='1'");
        
        
        if(empty($part_master))
        {
            echo '<option value="">No HSN Code Found</option>'; exit;
        }
        
        echo '<option value="">HSN Code</option>'; 
        
        
        foreach($part_master as $model)
        {
            echo '<option value="';
                echo $model->hsn_code.'">';
                echo $model->hsn_code.'</option>';
        }
        exit;
    }
}

