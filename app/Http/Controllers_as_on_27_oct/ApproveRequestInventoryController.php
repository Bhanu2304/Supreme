<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SparePart;
use App\Inventory;
use App\ChallanNo;
use App\StateMaster;
use App\ServiceCenter;
use App\InventoryCenter;
use App\RequestInventory;
use App\RequestInventoryPart;
use App\ApproveRequestInventory;
use App\ApproveRequestInventoryPart;
use DB;
use Auth;
use Session;


class ApproveRequestInventoryController extends Controller
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
    
    
    public function view()
    {
        $qr = "SELECT * FROM request_inventory ri inner join tbl_service_centre sc on ri.center_id=sc.center_id WHERE part_status_pending='1' ";
        $req_arr           =   DB::select($qr);
        $url = $_SERVER['APP_URL'].'/req-inv-entry-ho';
        return view('view-req-inv-ho')
        ->with('req_arr', $req_arr)
        ->with('url', $url);
    }
    
    public function view_case(Request $request)
    {
        Session::put("page-title","Approve Inventory Request");
        
        Session::put("page-title","Edit Inventory Request");
        
        $req_id     = base64_decode($request->input('req_id'));  
        $data_part_arr  = RequestInventoryPart::where("req_id",$req_id)->get();
        $req_det  = RequestInventory::where("req_id",$req_id)->first();
        $brand_arr   = DB::select("SELECT brand_id,brand_name FROM brand_master WHERE brand_status='1' ");
        
        $record = array();
        foreach($data_part_arr as $part)
        {
            $brand_id = $part->brand_id;
            $product_det =  DB::select("SELECT product_id,product_name FROM product_master WHERE brand_id='$brand_id' and product_status='1' ");
            
            foreach($product_det as $det)
            {
                $record['product_id'][$brand_id][$det->product_id] = $det->product_name;
            }
            
            $product_id = $part->product_id;
            $model_det =  DB::select("SELECT model_id,model_name FROM model_master WHERE product_id='$product_id' and  model_status='1' ");
            foreach($model_det as $det)
            {
                $record['model_id'][$product_id][$det->model_id] = $det->model_name;
            }
            
            $model_id = $part->model_id;
            $part_det =  DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE model_id='$model_id'");
            foreach($part_det as $det)
            {
                $record['part_name'][$model_id][] = $det->part_name;
            }
            
            $part_name = $part->part_name;
            $part_no_det =  DB::select("SELECT spare_id,part_no FROM `tbl_spare_parts` WHERE part_name='$part_name' ");
            
            foreach($part_no_det as $det)
            {
                $record['part_no'][$part_name][] = $det->part_no;
            }
            
            $part_no = $part->part_no;
            $part_no_det =  DB::select("SELECT spare_id,hsn_code FROM `tbl_spare_parts` WHERE part_name='$part_name' and part_no='$part_no'");
            
            foreach($part_no_det as $det)
            {
                $record['hsn_code'][$part_no][] = $det->hsn_code;
            }    
        }
        
        $url = $_SERVER['APP_URL'].'/req-inv-entry-ho';
        
        return view('approve-req-inv')
                ->with('data', $data)
                ->with('brand_arr', $brand_arr)
                ->with('record', $record)
                ->with('req_det',$req_det)
                ->with('url',$url)
                ->with('data_part_arr', $data_part_arr);
    }
    
    
    public function approve(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id      =   Auth::user()->table_id;
        $spare_part     =   $request->input('SparePart');
        $part_name      =   $spare_part['part_name'];
        $req_remarks        =   $request->input('req_remarks');
        $remarks        =   $request->input('remarks');
        $req_id         =   $request->input('req_id');
        $submit         =   $request->input('submit');
        
        DB::beginTransaction();
        
        try{
            if($submit=='Reject')
            {
                $RequestInventory = array();
                $RequestInventory['part_status_pending'] = '0';
                $RequestInventory['part_reject'] = '1';
                $RequestInventory['part_approve'] = '0';
                $RequestInventory['approve_date'] = $created_at;



                if(RequestInventory::whereRaw("req_id='$req_id'")->update($RequestInventory))
                {
                    Session::flash('error', "Inventory Request Rejected.");
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('req-inv-entry-ho'); 
                    DB::commit();
                }
                else
                {
                    DB::rollback();
                    Session::flash('error', "Inventory Request Reject Failed. Please Try Again");
                    Session::flash('alert-class', 'alert-danger');
                    return back();
                }
            }
            else
            {
                $qty = 0; $total = 0;

                $part_arr = RequestInventoryPart::whereRaw("req_id='$req_id'")->get();

                foreach($part_arr as $part)
                {
                    $brand_id = $part->brand_id;
                    $product_id = $part->product_id;
                    $model_id = $part->model_id;
                    $part_name = $part->part_name; 
                    $part_no = $part->part_no;
                    $hsn_code = $part->hsn_code;
                    $stock_required = $part->qty;

                    $check_stock_exist = Inventory::whereRaw("brand_id='$brand_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'")->first();
                    if($check_stock_exist)
                    {
                        $stock_available = $check_stock_exist->stock_qty;
                        if($stock_required>$stock_available)
                        {
                            $bal_stock = $stock_required-$stock_available;
                            Session::flash('message', "Only Spare Part $stock_available Available.");
                            Session::flash('alert-class', 'alert-danger');
                            return back();
                        }
                    }
                    else
                    {
                        Session::flash('message', "Spare Part $part_name Not Available.");
                        Session::flash('alert-class', 'alert-danger');
                        return back();
                    }

                    $request = array();
                    $center_id = $request['center_id']   = $part->center_id;
                    $request['req_id']      = $req_id;

                    $request['brand_id']    = $brand_id;
                    $request['product_id']  = $product_id;
                    $request['model_id']    = $model_id;
                    $request['part_name']   = $part_name;
                    $request['part_no']     = $part_no;
                    $request['hsn_code']    = $hsn_code;
                    $request['qty']    = $stock_required;


                    $check_spare_part_rate = SparePart::whereRaw("brand_id='$brand_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'")->first();
                    $landing_cost = $check_spare_part_rate->landing_cost;
                    $customer_price = $check_spare_part_rate->customer_price;
                    $discount = $check_spare_part_rate->discount;
                    $part_tax = $check_spare_part_rate->part_tax;


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


                    $request['created_at']  = $created_at;
                    $request['created_by']  = $created_by;
                    $request_arr[] = $request;



                    $grnd_qty += $stock_required;
                    $grnd_total +=$total ;
                    $grand_tax +=$total_tax ;
                    $grand_total +=$net_total;

                }



                $ApproveRequestInventory = new ApproveRequestInventory();
                $ApproveRequestInventory->req_id= $req_id;
                $ApproveRequestInventory->center_id= $center_id;
                $ApproveRequestInventory->part_required= count($part_arr);
                $ApproveRequestInventory->qty= $grnd_qty;
                $ApproveRequestInventory->total= $grnd_total;
                $ApproveRequestInventory->total_tax= $grand_tax;
                $ApproveRequestInventory->net_total= $grand_total;    
                $ApproveRequestInventory->remarks= $remarks;
                $ApproveRequestInventory->req_remarks= $req_remarks;
                $ApproveRequestInventory->created_at= $created_at;
                $ApproveRequestInventory->created_by= $created_by;
                $challan_no_date = date('Y/m/d/');

                $find_max_challan_no=ChallanNo::selectRaw('challan_no')->whereRaw("challan_date=curdate()")->first();

                $new_challan_no = "";
                if(empty($find_max_challan_no))
                {
                    $challan_entry_arr = new ChallanNo();
                    $challan_entry_arr->challan_date = date('Y-m-d');
                    $challan_entry_arr->challan_no = '1';
                    $challan_entry_arr->save();

                    $new_challan_no = "Sup/$challan_no_date".'00001';
                }
                else
                {
                    $str_no = "00000";
                    $no = $find_max_challan_no->challan_no;
                    $no = $no+1;
                    $len = strlen($str_no);
                    $newlen = strlen("$no");
                    $new_no = substr_replace($str_no, $no, $len-$newlen,$newlen);
                    $new_challan_no = "Sup/$challan_no_date".$new_no;

                    ChallanNo::whereRaw("challan_date=curdate()")->update(array('challan_no'=>$no));

                }
                $ApproveRequestInventory->challan_no= $new_challan_no;
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
                                if(ApproveRequestInventoryPart::whereRaw("req_id='$req_id'")->update(array('approve_id'=>$approve_id,'challan_no'=>$new_challan_no)))
                                {
                                    DB::commit();
                                    Session::flash('message', "PO Approved Successfully.");
                                    Session::flash('alert-class', 'alert-success');
                                }
                                else
                                {
                                    DB::rollback();
                                    Session::flash('error', "PO Failed. Please Try Again");
                                    Session::flash('alert-class', 'alert-danger');
                                    return back();
                                }
                            }
                            else
                            {
                                DB::rollback();
                                Session::flash('error', "PO Failed. Please Try Again");
                                Session::flash('alert-class', 'alert-danger');
                                return back();
                            }
                    }
                    else
                    {
                        DB::rollback();
                        Session::flash('error', "PO Failed. Please Try Again");
                        Session::flash('alert-class', 'alert-danger');
                        return back();
                    }
                }
                else
                {
                        DB::rollback();
                        Session::flash('error', "PO Failed. Please Try Again");
                        Session::flash('alert-class', 'alert-danger');
                        return back();
                }
            }
        }
        catch(Exception $e)
        {
                    DB::rollback();
                    Session::flash('error', "PO Failed. Please Try Again");
                    Session::flash('alert-class', 'alert-danger');
                    return back();
        }
        
        
            return redirect('req-inv-entry-ho');           
    }
    
    public function search_challan(Request $request)
    {
        Session::put("page-title","Invoice View");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        
        
        
        if($UserType!='Admin')
        {
            $whereTag1 = "and center_id='$Center_Id'";
        }
        
        $qr1 = "SELECT pm.state_id,state_name,pincode FROM `pincode_master` pm 
INNER JOIN state_master st ON pm.state_id = st.state_id
WHERE 1=1 order by state_name";
        $vendor_pin_json           =   DB::select($qr1); 
        $state_master = $pin_master = array();
        foreach($vendor_pin_json as $vpin)
        {
            $state_master[$vpin->state_id] = $vpin->state_name;
            $pin_master[$vpin->pincode] = $vpin->pincode;
        }
        
        //get method request
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $asc_code = $request->input('asc_code');
        $challan_no = $request->input('challan_no');
        
        $whereTag = "";
        $VendorPincode=Session::get('pincode');
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and pincode = '$pincode'";
        }
        if(!empty($asc_code))
        {
            $whereTag .= " and asc_code='$asc_code'";
        }
        if(!empty($challan_no))
        {
            $whereTag .= " and challan_no='$challan_no'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(created_at) between '$from_date1' and '$to_date1'";
        }
        
        
        if(empty($whereTag))
        {
            $tag_data_qr = "select * from approve_request_inventory where 1=1  $whereTag1  and date(created_at)=curdate();"; 
        }
        else
        {
            $tag_data_qr = "select * from approve_request_inventory where 1=1 $whereTag1    $whereTag"; 
        }
        
        $DataArr = DB::select($tag_data_qr); 
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/search-challan';
        return view('search-challan')
            ->with('pin_master',$pin_master)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('pincode',$pincode)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('DataArr',$DataArr)
                ->with('url', $url)
                ->with('whereTag',$whereTag); 
                
    }
    
    
}

