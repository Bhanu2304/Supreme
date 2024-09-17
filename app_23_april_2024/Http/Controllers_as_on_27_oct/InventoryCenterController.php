<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\BrandMaster;
use App\SparePart;
use App\Inventory;
use App\InventoryCenter;
use App\InventoryCenterAdd;
use App\ServiceCenter;
use DB;
use Auth;
use Session;


class InventoryCenterController extends Controller
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
    
    
    
    
    public function allocate_inv(Request $request)
    {
        Session::put("page-title","Allocate Inventory");
        $UserId = Session::get('UserId');
        $UserType = Session::get('UserType');
        $whereTag = ""; $whereTag1 = ""; 
        if($UserType=='ServiceCenter')
        {
            $center_id = Auth::user()->table_id; 
            $whereTag .=" and tic.center_id ='$center_id'";
            $whereTag1 .=" and allocation_id ='$center_id'";
        }
        
        
        
        
        //$part_arr           =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        $center_arr           =   DB::select("SELECT center_id,center_name FROM `tbl_service_centre` tic where 1=1 $whereTag");
        
        $qry1 = "SELECT *,tsc.center_name FROM tbl_inventory_center tic 
INNER JOIN tbl_service_centre tsc ON tic.center_id = tsc.center_id
INNER JOIN brand_master bm ON tic.brand_id = bm.brand_id  and brand_status='1'
inner join product_category_master cm on tic.product_category_id= cm.product_category_id  and cm.category_status='1'
INNER JOIN product_master pm ON tic.product_id = pm.product_id AND product_status='1'
INNER JOIN model_master mm ON tic.model_id = mm.model_id AND model_status='1'
WHERE 1=1  $whereTag;"; 
        $stock_arr           =   DB::select($qry1);
        $qry = "SELECT allocation_id center_id,part_name,part_no,hsn_code,count(1) cnsptn FROM tbl_inventory_part WHERE 1=1 $whereTag1 group by allocation_id,Part_Name,Part_No,hsn_code"; 
        $consumption_arr           =   DB::select($qry);
        
        
        
        $cnpt_arr = array();
        foreach($consumption_arr as $cnpt)
        {
            $cnpt_arr[$cnpt->center_id][$cnpt->part_name][$cnpt->part_no][$cnpt->hsn_code] = $cnpt->cnsptn;
        }
        //print_r($consumption_arr); exit;
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);
        
        
        $url = $_SERVER['APP_URL'].'/allocate-inv';
        return view('allocate-inv')
        ->with('brand_arr', $brand_arr)
                ->with('center_arr', $center_arr)
                ->with('part_arr', $part_arr)
                ->with('cnpt_arr', $cnpt_arr)
                ->with('url', $url)
                ->with('stock_arr',$stock_arr);
    }
    
    public function save_allocate_inv(Request $request)
    {   
        $part_name = addslashes($request->input('part_name'));
        $part_no =    addslashes($request->input('part_no'));
        $hsn_code =    addslashes($request->input('hsn_code'));
        $stock_qty =    addslashes($request->input('stock_qty'));
        $center_id =    addslashes($request->input('center_id'));
        $raw_no =    addslashes($request->input('raw_no'));
        $stock_type = addslashes($request->input('stock_type'));
        $brand_id =    addslashes($request->input('brand_id'));
        $product_category_id =    addslashes($request->input('product_category_id'));
        $product_id =    addslashes($request->input('product_id'));
        $model_id =    addslashes($request->input('model_id'));
        
        $spareArr            =   new InventoryCenterAdd();
        $spareArr->part_name=$part_name;
        $spareArr->part_no=$part_no;
        $spareArr->hsn_code=$hsn_code;
        $spareArr->stock_qty=$stock_qty;
        $spareArr->center_id=$center_id;
        $spareArr->brand_id=$brand_id;
        $spareArr->product_category_id=$product_category_id;
        $spareArr->product_id=$product_id;
        $spareArr->model_id=$model_id;
        $spareArr->stock_type=$stock_type;
        $spareArr->raw_no=$raw_no;
        //$spareArr->stock_qty=$move_qty;
        
        $UserId = Auth::user()->id;    
        $spareArr->created_by=$UserId; 
        $spareArr->created_at=date("Y-m-d H:i:s");
        
        if(empty($part_name))
        {
            Session::flash('message', "Spare Part Name Should Not be Empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(empty($part_no))
        {
            Session::flash('message', "Part No. Should Not be Empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        } 
        else if(empty($hsn_code))
        {
            Session::flash('message', "HSN Code Should Not be Empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else if(empty($stock_qty))
        {
            Session::flash('message', "Stock Quantity Should not be empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else if($spareArr->save())
        {
            $spare_det_exist = InventoryCenter::whereRaw("center_id='$center_id' and brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'")->first();
            if($spare_det_exist)
            {
                $main_inv = array();
                $old_qty = $spare_det_exist->stock_qty;
                if(empty($old_qty))
                {
                    $old_qty = 0;
                }
                $main_inv['stock_qty']=$stock_qty+$old_qty;
                
                
                if(InventoryCenter::whereRaw("inv_id='{$spare_det_exist->inv_id}'")->update($main_inv))
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
                $inv            =   new InventoryCenter();
                $inv->center_id=$center_id;
                $inv->brand_id=$brand_id;
                $inv->product_category_id=$product_category_id;
                $inv->product_id=$product_id;
                $inv->model_id=$model_id;
                $inv->part_name=$part_name;
                $inv->part_no=$part_no;
                $inv->hsn_code=$hsn_code;
                $inv->stock_qty=$stock_qty;
                
                
                
                if($inv->save())
                {
                    Session::flash('message', "Inventory Added To Center  Successfully.");
                    Session::flash('alert-class', 'alert-success');
                }
                else
                {
                    Session::flash('message', "Inventory Addition Failed. Please Try Again!");
                    Session::flash('alert-class', 'alert-danger');
                }
            }
            
            
            
            //Session::flash('message', "Inventory Added To Center  Successfully.");
            //Session::flash('alert-class', 'alert-danger');
        }
        return redirect('allocate-inv');
    }
    
    public function center_inv_details(Request $request)
    {
        Session::put("page-title","Inventory Details");
        $inv_id = base64_decode($request->input('inv_id'));
        $spare_det_exist = InventoryCenter::selectRaw("brand_id,product_category_id,product_id,model_id,part_name,part_no,hsn_code")->whereRaw("inv_id='$inv_id'")->first();
        $part_name = $spare_det_exist->part_name;
        $part_no = $spare_det_exist->part_no;
        $hsn_code = $spare_det_exist->hsn_code;
        $brand_id =    $spare_det_exist->brand_id;
        $product_category_id =    $spare_det_exist->product_category_id;
        $product_id =    $spare_det_exist->product_id;
        $model_id =    $spare_det_exist->model_id;
        
        $qr = "SELECT ti.*,tsc.center_name,DATE_FORMAT(ti.created_at,'%d-%b-%Y %H:%i') created_at,bm.brand_name,cm.category_name,pm.product_name,mm.model_name FROM `tbl_inventory_center_add` ti
INNER JOIN tbl_service_centre tsc ON ti.center_id = tsc.center_id and sc_status='1'
INNER JOIN brand_master bm ON ti.brand_id = bm.brand_id and bm.brand_id='$brand_id' and brand_status='1' 
inner join product_category_master cm on ti.product_category_id= cm.product_category_id and cm.product_category_id='$product_category_id' and category_status='1' 
INNER JOIN product_master pm ON ti.product_id = pm.product_id  and pm.product_id='$product_id' AND product_status='1' 
INNER JOIN model_master mm ON ti.model_id = mm.model_id and mm.model_id='$model_id' AND model_status='1'

WHERE  part_name='$part_name' AND part_no='$part_no' AND hsn_code='$hsn_code'
ORDER BY ti.created_at DESC";
        
        $inv_arr           =   DB::select($qr);
        
        
        $url = $_SERVER['APP_URL'].'/add-inv';
        return view('center-inv-details')
        ->with('url', $url)
                ->with('data_arr', $inv_arr);
    }
    
    public function get_stock(Request $request)
    {
        
        $part_name = $request->input('part_name');
        $part_no = $request->input('part_no');
        $hsn_code = $request->input('hsn_code');
        
        
        $inventory = Inventory::whereRaw("part_name = '$part_name' and part_no='$part_no' and hsn_code='$hsn_code'")->first();
        if(empty($inventory->stock_qty))
        {
            echo '0';exit;
        }
        else
        {
            echo $inventory->stock_qty;exit;
        }
        
        exit;
    }
    
    
    public function edit_allocate(Request $request)
    {
        Session::put("page-title","Edit Inventory Allocation");
        $inv_id = base64_decode($request->input('inv_id')); 
        $data = InventoryCenter::where("inv_id",$inv_id)->first();
        $Part_Name = $data['part_name']; 
        $Part_No = $data['part_no'];
        $hsn_code = $data['hsn_code'];
        
        $part_arr           =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        $center_arr           =   DB::select("SELECT center_id,center_name FROM `tbl_service_centre`  WHERE sc_status='1' ");
        $stock_arr           =   DB::select("SELECT tic.*,tsc.center_name FROM `tbl_inventory_center` tic 
INNER JOIN tbl_service_centre tsc 
ON tic.center_id = tsc.center_id WHERE stock_status='1'");
        $part_no_arr           =   DB::select("SELECT spare_id,part_no FROM `tbl_spare_parts` WHERE part_name='$Part_Name' and part_status='1' ");
        $hsn_no_arr           =   DB::select("SELECT spare_id,hsn_code FROM `tbl_spare_parts` WHERE part_no='$Part_No' and part_status='1' ");
        $inventory = Inventory::whereRaw("part_name = '$Part_Name' and part_no='$Part_No' and hsn_code='$hsn_code'")->first();
        $stock_qty = $inventory->stock_qty;
        
        $url = $_SERVER['APP_URL'].'/allocate-inv';
        return view('edit-allocate-inv')
                ->with('center_arr', $center_arr)
                ->with('part_arr', $part_arr)
                ->with('stock_arr',$stock_arr)
                ->with('part_no_arr', $part_no_arr)
                ->with('hsn_no_arr', $hsn_no_arr)
                ->with('stock_qty',$stock_qty)
                ->with('url', $url)
                ->with('data',$data);
    }
    
    
    public function update_allocate_inv(Request $request){
        
        
        
        
        $inv_id = addslashes($request->input('inv_id'));
        $part_name = addslashes($request->input('part_name'));
        $part_no =    addslashes($request->input('part_no'));
        $hsn_code =    addslashes($request->input('hsn_code'));
        $stock_qty =    addslashes($request->input('stock_qty'));
        $move_qty =    addslashes($request->input('move_qty'));
        $center_id =    addslashes($request->input('center_id'));
        
        $spareArr            =   array();
        $spareArr['part_name']=$part_name;
        $spareArr['part_no']=$part_no;
        $spareArr['hsn_code']=$hsn_code;
        $spareArr['avail_qty']=$stock_qty;
        $spareArr['center_id']=$center_id;
        $spareArr['stock_qty']=$move_qty;
        
        $UserId = Auth::user()->id;    
        $spareArr['created_by']=$UserId; 
        $spareArr['created_at']=date("Y-m-d H:i:s");
        
        
        
        if(empty($part_name))
        {
            Session::flash('message', "Spare Part Name Should Not Empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(empty($part_no))
        {
            Session::flash('message', "Part No. Should Not Empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        } 
        else if(empty($hsn_code))
        {
            Session::flash('message', "HSN Code Should Not Empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(empty($move_qty))
        {
            Session::flash('message', "Move Quantity Should Not Empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if(empty($stock_qty))
        {
            Session::flash('message', "Stock Quantity Should not be empty.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        else if($move_qty>$stock_qty)
        {
            Session::flash('message', "Allocate Quantity Should not more than Total Inventory.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        if(InventoryCenter::whereRaw("inv_id='$inv_id'")->update($spareArr)){
                Session::flash('message', "Inventory Allocate To Center Modified Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Inventory Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            }    
        
       return redirect('allocate-inv'); 
    }
    
    
}

