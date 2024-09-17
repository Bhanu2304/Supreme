<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\BrandMaster;
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
use App\DispatchInventory;
use App\ManualChallan;
use App\DispatchInventoryScParticulars;
use App\ScDispatchInventory;
use App\OutwardPending;
use DB;
use Auth;
use Session;
use PDF;


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
        $qr = "SELECT * FROM request_inventory  WHERE part_status_pending='1' ";
        $req_arr           =   DB::select($qr);
        $url = $_SERVER['APP_URL'].'/req-inv-entry-ho';
        return view('view-req-inv-ho')
        ->with('req_arr', $req_arr)
        ->with('url', $url);
    }
    
    public function view_case(Request $request)
    {
        Session::put("page-title","Approve Store Inventory Request");
        
        #Session::put("page-title","Edit Inventory Request");
        
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
                    #$hsn_code = $part->hsn_code;
                    $stock_required = $part->qty;
                    $spare_id = $part->spare_id;
                    #echo "brand_id='$brand_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$spare_id' and hsn_code='$hsn_code'";die;
                    
                    #$check_stock_exist = Inventory::whereRaw("brand_id='$brand_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$spare_id' and hsn_code='$hsn_code'")->first();
                    $check_stock_exist = Inventory::whereRaw("brand_id='$brand_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$spare_id'")->first();
                    if($check_stock_exist)
                    {
                        $stock_available = $check_stock_exist->stock_qty;
                        if($stock_required>$stock_available)
                        {
                            $bal_stock = $stock_required-$stock_available;
                            Session::flash('error', "Only Spare Part $stock_available Available.");
                            Session::flash('alert-class', 'alert-danger');
                            return back();
                        }
                    }
                    else
                    {
                        Session::flash('error', "Spare Part $part_name Not Available.");
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
                    #$request['hsn_code']    = $hsn_code;
                    $request['qty']    = $stock_required;


                    #$check_spare_part_rate = SparePart::whereRaw("brand_id='$brand_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'")->first();
                    $check_spare_part_rate = SparePart::whereRaw("brand_id='$brand_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no'")->first();
                    $landing_cost = $check_spare_part_rate->landing_cost;
                    $customer_price = $check_spare_part_rate->customer_price;
                    $discount = $check_spare_part_rate->discount;
                    $part_tax = $check_spare_part_rate->part_tax;
                    $spare_id = $check_spare_part_rate->spare_id;


                    $request['landing_cost']        = $landing_cost;
                    $request['customer_price'] = $customer_price;
                    $request['discount'] = $discount;
                    $request['part_tax'] = $part_tax;
                    $request['spare_id'] = $spare_id;

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
                $ApproveRequestInventory->req_no= $req_id;
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
        Session::put("page-title","Challan View");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        
        
     
        
        $qr1 = "SELECT pm.state_id,state_name,pincode FROM `pincode_master` pm INNER JOIN `state_master` st ON pm.state_id = st.state_id
        WHERE 1=1 order by state_name";
        $vendor_pin_json           =   DB::select($qr1);
        $state_master = $pin_master = array();
        foreach($vendor_pin_json as $vpin)
        {
            $state_master[$vpin->state_id] = $vpin->state_name;
            $pin_master[$vpin->pincode] = $vpin->pincode;
        }
        
        //get method request
        $brand_id = $request->input('brand_id');
        $product_category = $request->input('product_category');
        $product = $request->input('product');
        $model = $request->input('model');
        $part_code = $request->input('part_code');

        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $asc_code = $request->input('asc_code');
        $challan_no = $request->input('challan_no');
        $tab1 = $request->input('tab1');
        $tab2 = $request->input('tab2');
        


        $brand_id2 = $request->input('brand_id2');
        $product_category2 = $request->input('product_category2');
        $product2 = $request->input('product2');
        $model2 = $request->input('model2');
        $part_code2 = $request->input('part_code2');
        $from_date2 = $request->input('from_date2');
        $to_date2 = $request->input('to_date2');
        $challan_no2 = $request->input('sr_no2');

        
        $whereTag = "";
        $VendorPincode=Session::get('pincode');

        if(!empty($brand_id) && $brand_id!='All')
        {
            $whereTag .= " and bm.brand_id = '$brand_id'";
        }
        #cho $product_category;die;
        if(!empty($product_category) && $product_category!='All')
        {   
            $whereTag .= " and tsp.product_category_id = '$product_category' ";
            #$whereTag1 .= " and sri.product_category_id = '$product_category_id' ";
        }
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and ari.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and ari.pincode = '$pincode'";
        }
        if(!empty($asc_code))
        {
            $whereTag .= " and ari.asc_code='$asc_code'";
        }
        if(!empty($challan_no))
        {
            $whereTag .= " and ari.challan_no='$challan_no'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(ari.created_at) between '$from_date1' and '$to_date1'";
        }
        
        if(empty($whereTag))
        {
           $tag_data_qr = "select ari.*,ari.created_at as create_date,ari.state,bm.brand_name from approve_request_inventory ari 
           INNER JOIN approve_request_inventory_particulars arip  ON ari.approve_id = arip.approve_id 
           INNER JOIN brand_master bm ON arip.brand_id = bm.brand_id 
           
           
           where 1=1 and ari.status='0'   and date(ari.created_at)=curdate();";

           $outward_data_qr = "select ari.*,ari.created_at as create_date,ari.state,bm.brand_name from approve_request_inventory ari 
           INNER JOIN approve_request_inventory_particulars arip  ON ari.approve_id = arip.approve_id 
           INNER JOIN brand_master bm ON arip.brand_id = bm.brand_id 
           where  ari.status !='0'   and date(ari.created_at)=curdate();";
        }
        else
        {
            $tag_data_qr = "select *,ari.created_at as create_date,ari.state,bm.brand_name from approve_request_inventory ari 
            INNER JOIN approve_request_inventory_particulars arip  ON ari.approve_id = arip.approve_id 
            INNER JOIN brand_master bm ON arip.brand_id = bm.brand_id
            
            
            where 1=1  and ari.status='0'    $whereTag"; 
            
            $outward_data_qr = "select ari.*,ari.created_at as create_date,ari.state,bm.brand_name from approve_request_inventory ari 
           INNER JOIN approve_request_inventory_particulars arip  ON ari.approve_id = arip.approve_id 
           INNER JOIN brand_master bm ON arip.brand_id = bm.brand_id 
           where  ari.status !='0'    $whereTag";
        }

        #echo $tag_data_qr;die;
        $whereTag2 = "";
        #view challan 

        if(!empty($brand_id2) && $brand_id2!='All')
        {
            $whereTag2 .= " and bm.brand_id = '$brand_id2'";
        }
        #cho $product_category;die;
        if(!empty($product_category2) && $product_category2!='All')
        {   
            $whereTag2 .= " and tsp.product_category_id = '$product_category2' ";
            #$whereTag1 .= " and sri.product_category_id = '$product_category_id' ";
        }
        

        if(!empty($challan_no2))
        {
            $whereTag2 .= " and ari.challan_no='$challan_no2'";
        }
        if(!empty($from_date2) && !empty($to_date2))
        {   
            $from_date_arr2 = explode('-',$from_date2);  krsort($from_date_arr2); $from_date3 = implode('-',$from_date_arr2);
            $to_date_arr2 = explode('-',$to_date2);  krsort($to_date_arr2); $to_date3 = implode('-',$to_date_arr2);
            $whereTag2 .= " and date(ari.created_at) between '$from_date3' and '$to_date3'";
        }
        
        if(empty($whereTag2))
        {
           $tag_data_qr2 = "select *,ari.created_at as create_date from approve_request_inventory ari 
           INNER JOIN approve_request_inventory_particulars arip  ON ari.approve_id = arip.approve_id 
           INNER JOIN brand_master bm ON arip.brand_id = bm.brand_id where 1=1 and ari.status='1' $whereTag2  and date(ari.created_at)=curdate();";

           $outward_data_qr2 = "select *,ari.created_at as create_date,tm.ticket_no,tm.job_no,oii.invoice_no from outward_inventory_dispatch ari 
           INNER JOIN outward_inventory_dispatch_particulars arip  ON ari.dispatch_id = arip.dispatch_id 
           INNER JOIN outward_inventory_invoice oii ON arip.invoice_id = oii.invoice_id 
           LEFT JOIN tagging_master tm  ON arip.job_id = tm.tagid 
           INNER JOIN brand_master bm ON arip.brand_id = bm.brand_id where 1=1 and ari.status='1'  $whereTag2  and date(ari.created_at)=curdate();";

           $outward_manual_challan = "SELECT * FROM manual_challan ari INNER JOIN brand_master bm ON ari.brand_id = bm.brand_id
            left JOIN tbl_service_centre tsc ON ari.center_id = tsc.center_id where  DATE(ari.created_at)=CURDATE();";
        }
        else
        {
            $tag_data_qr2 = "select * from approve_request_inventory ari 
            INNER JOIN approve_request_inventory_particulars arip  ON ari.approve_id = arip.approve_id 
            INNER JOIN brand_master bm ON arip.brand_id = bm.brand_id
            where 1=1  and ari.status='1' $whereTag1    $whereTag2"; 
            
            $outward_data_qr2 = "select *,ari.created_at as create_date,tm.ticket_no,tm.job_no,oii.invoice_no from outward_inventory_dispatch ari 
            INNER JOIN outward_inventory_dispatch_particulars arip  ON ari.dispatch_id = arip.dispatch_id 
            INNER JOIN outward_inventory_invoice oii ON arip.invoice_id = oii.invoice_id 
            INNER JOIN brand_master bm ON arip.brand_id = bm.brand_id
            LEFT JOIN tagging_master tm  ON arip.job_id = tm.tagid 
            INNER JOIN `tbl_spare_parts` tsp ON tsp.spare_id= arip.spare_id where 1=1 and ari.status='1' $whereTag1    $whereTag2";

            $outward_manual_challan = "SELECT ari.*,bm.brand_name,tsc.center_name FROM manual_challan ari INNER JOIN brand_master bm ON ari.brand_id = bm.brand_id
            left JOIN tbl_service_centre tsc ON ari.center_id = tsc.center_id where  1=1 $whereTag2";
        }
        #echo $outward_data_qr;die;
        #echo $tag_data_qr2;die;
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);

        
        // echo $tag_data_qr;
        #echo $outward_data_qr;die;
        $DataArr = DB::select($tag_data_qr); 
        $DataArr2 = DB::select($outward_data_qr); 

        $manual_challan_arr = DB::select($outward_manual_challan);


        $DataArr_view = DB::select($tag_data_qr2); 
        $DataArr2_view = DB::select($outward_data_qr2); 

        #print_R($DataArr2);die;

        foreach($DataArr2 as $data)
        {
            $center_id = $data->center_id;
            $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
            $state_id = $center_det->state;
            $state_det = StateMaster::whereRaw("state_id='$state_id'")->first();
            #echo $center_det->pincode;die;
            $data->pincode = $center_det->pincode;
            $data->state_name = $state_det->state_name;
            
        }

        foreach($DataArr2_view as $data)
        {
            $center_id = $data->center_id;
            $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
            $state_id = $center_det->state;
            $state_det = StateMaster::whereRaw("state_id='$state_id'")->first();
            #echo $center_det->pincode;die;
            $data->pincode = $center_det->pincode;
            $data->state_name = $state_det->state_name;
            
        }
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        #print_r($DataArr);die;
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/search-challan';
        return view('search-challan')
            ->with('pin_master',$pin_master)
            ->with('state_master',$state_master)
            ->with('state',$state_name)
            ->with('pincode',$pincode)
            ->with('from_date',$from_date)
            ->with('to_date',$to_date)
            ->with('challan_no',$challan_no)
            ->with('DataArr',$DataArr)
            ->with('DataArr2',$DataArr2)
            ->with('DataArr_view',$DataArr_view)
            ->with('DataArr2_view',$DataArr2_view)
            ->with('manual_challan_arr',$manual_challan_arr)
            ->with('url', $url)
            ->with('tab1',$tab1)
            ->with('tab2',$tab2)
            ->with('from_date2',$from_date2)
            ->with('to_date2',$to_date2)
            ->with('brand_arr', $brand_arr)
            ->with('brand_id', $brand_id)
            ->with('brand_id2', $brand_id2)
            ->with('whereTag',$whereTag);
                
    }

    public function generate_challan(Request $request)
    {
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $invoice_id = $request->input('invoice_id'); 
        $app_data = ApproveRequestInventory::whereRaw("approve_id='$invoice_id'")->first();
        
        $tagDet = DispatchInventory::whereRaw("invoice_id='$invoice_id'")->first();

        if($app_data->invoice_status=='0')
        {   
            $brand_id = $app_data->brand_id;
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
        }else
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
            
            
            
            
            $invoice_no = $app_data->challan_no;
            $taggingArr1 = array();
            $taggingArr1['status']=1;
            $taggingArr1['challan_date']=date('Y-m-d H:i:s');
            $taggingArr1_part = array();
            $taggingArr1['challan_no']=addslashes($new_challan_no);
            $taggingArr1_part['challan_no']=addslashes($new_challan_no);
            if(ApproveRequestInventory::whereRaw("approve_id='$invoice_id' and status='0'")->update($taggingArr1))
            {
                ApproveRequestInventoryPart::whereRaw("approve_id='$invoice_id'")->update($taggingArr1_part);
                echo json_encode(array('resp_id'=>'1',"job_no"=>$new_challan_no));exit;
            }
            else
            {
                echo json_encode(array('resp_id'=>'2',"job_no"=>""));exit;
            }

        }
        else
        {
            echo json_encode(array('resp_id'=>'2',"job_no"=>""));exit;
        }
        
    }


    public function search_challan_sc(Request $request)
    {
        Session::put("page-title","Challan View");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        
        
        if(strtolower($UserType)!=strtolower('Admin'))
        {
            $whereTag1 = "and ari.center_id='$Center_Id'";
        }
        
        $qr1 = "SELECT pm.state_id,state_name,pincode FROM `pincode_master` pm INNER JOIN `state_master` st ON pm.state_id = st.state_id
        WHERE 1=1 order by state_name";
        $vendor_pin_json           =   DB::select($qr1);
        $state_master = $pin_master = array();
        foreach($vendor_pin_json as $vpin)
        {
            $state_master[$vpin->state_id] = $vpin->state_name;
            $pin_master[$vpin->pincode] = $vpin->pincode;
        }
        
        //get method request
        $brand_id = $request->input('brand_id');
        $product_category = $request->input('product_category');
        $product = $request->input('product');
        $model = $request->input('model');
        $part_code = $request->input('part_code');

        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $asc_code = $request->input('asc_code');
        $challan_no = $request->input('challan_no');
        $tab1 = $request->input('tab1');
        $tab2 = $request->input('tab2');
        


        $brand_id2 = $request->input('brand_id2');
        $product_category2 = $request->input('product_category2');
        $product2 = $request->input('product2');
        $model2 = $request->input('model2');
        $part_code2 = $request->input('part_code2');
        $from_date2 = $request->input('from_date2');
        $to_date2 = $request->input('to_date2');
        $challan_no2 = $request->input('sr_no2');

        
        $whereTag = "";
        $VendorPincode=Session::get('pincode');

        if(!empty($brand_id) && $brand_id!='All')
        {
            $whereTag .= " and bm.brand_id = '$brand_id'";
        }
        #cho $product_category;die;
        if(!empty($product_category) && $product_category!='All')
        {   
            $whereTag .= " and tsp.product_category_id = '$product_category' ";
            #$whereTag1 .= " and sri.product_category_id = '$product_category_id' ";
        }
        
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and ari.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and ari.pincode = '$pincode'";
        }
        if(!empty($asc_code))
        {
            $whereTag .= " and ari.asc_code='$asc_code'";
        }
        if(!empty($challan_no))
        {
            $whereTag .= " and ari.challan_no='$challan_no'";
        }
        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(ari.created_at) between '$from_date1' and '$to_date1'";
        }
        
        if(empty($whereTag))
        {


        //    $outward_data_qr = "select *,ari.created_at as create_date,tm.ticket_no,tm.job_no,oii.invoice_no from outward_inventory_pending ari 
        //    INNER JOIN outward_inventory_dispatch_particulars_sc arip  ON ari.dispatch_id = arip.dispatch_id 
        //    INNER JOIN outward_inventory_invoice_sc oii ON arip.invoice_id = oii.invoice_id 
        //    LEFT JOIN tagging_master tm  ON arip.job_id = tm.tagid 
        //    INNER JOIN brand_master bm ON arip.brand_id = bm.brand_id where 1=1 and ari.part_status='0'  $whereTag1  and date(ari.created_at)=curdate();";
           $outward_data_qr = "SELECT *,ari.created_at AS create_date FROM outward_inventory_pending ari 
            INNER JOIN brand_master bm ON ari.brand_id = bm.brand_id WHERE
            ari.return_status='0' AND ari.part_status is null $whereTag1 AND DATE(ari.created_at)=CURDATE();";
          
        }
        else
        {

            $outward_data_qr = "SELECT *,ari.created_at AS create_date FROM outward_inventory_pending ari 
            INNER JOIN brand_master bm ON ari.brand_id = bm.brand_id WHERE ari.return_status='0' and ari.part_status is null $whereTag1    $whereTag";
            
        }

        #echo $outward_data_qr;die;
        $DataArr2 = DB::select($outward_data_qr); 

        foreach($DataArr2 as $data)
        {
            $center_id = $data->center_id;
            $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
            $state_id = $center_det->state;
            $state_det = StateMaster::whereRaw("state_id='$state_id'")->first();
            #echo $center_det->pincode;die;
            $data->pincode = $center_det->pincode;
            $data->state_name = $state_det->state_name;
            
        }



        #echo $outward_data_qr;die;


        $whereTag2 = "";
        #view challan 

        if(!empty($brand_id2) && $brand_id2!='All')
        {
            $whereTag2 .= " and bm.brand_id = '$brand_id2'";
        }
        #cho $product_category;die;
        if(!empty($product_category2) && $product_category2!='All')
        {   
            $whereTag2 .= " and tsp.product_category_id = '$product_category2' ";
            #$whereTag1 .= " and sri.product_category_id = '$product_category_id' ";
        }
        

        if(!empty($challan_no2))
        {
            $whereTag2 .= " and ari.challan_no='$challan_no2'";
        }
        if(!empty($from_date2) && !empty($to_date2))
        {   
            $from_date_arr2 = explode('-',$from_date2);  krsort($from_date_arr2); $from_date3 = implode('-',$from_date_arr2);
            $to_date_arr2 = explode('-',$to_date2);  krsort($to_date_arr2); $to_date3 = implode('-',$to_date_arr2);
            $whereTag2 .= " and date(ari.created_at) between '$from_date3' and '$to_date3'";
        }
        
        if(empty($whereTag2))
        {
            // $outward_data_qr2 = "select *,ari.created_at as create_date,tm.ticket_no,tm.job_no,oii.invoice_no from outward_inventory_dispatch_sc ari 
            // INNER JOIN outward_inventory_dispatch_particulars_sc arip  ON ari.dispatch_id = arip.dispatch_id 
            // INNER JOIN outward_inventory_invoice_sc oii ON arip.invoice_id = oii.invoice_id 
            // LEFT JOIN tagging_master tm  ON arip.job_id = tm.tagid 
            // INNER JOIN brand_master bm ON arip.brand_id = bm.brand_id where 1=1 and ari.status='1'  $whereTag2  and date(ari.created_at)=curdate();";
            $outward_data_qr2 = "SELECT *,ari.created_at AS create_date FROM outward_inventory_pending ari 
            INNER JOIN brand_master bm ON ari.brand_id = bm.brand_id WHERE
            ari.return_status='0' AND ari.part_status='1' $whereTag2 AND DATE(ari.created_at)=CURDATE();";


            $outward_manual_challan = "SELECT * FROM manual_challan ari INNER JOIN brand_master bm ON ari.brand_id = bm.brand_id
            left JOIN tbl_service_centre tsc ON ari.center_id = tsc.center_id where  DATE(ari.created_at)=CURDATE();";



        }
        else
        {
            // $outward_data_qr2 = "select *,ari.created_at as create_date,tm.ticket_no,tm.job_no,oii.invoice_no from outward_inventory_dispatch_sc ari 
            // INNER JOIN outward_inventory_dispatch_particulars_sc arip  ON ari.dispatch_id = arip.dispatch_id 
            // INNER JOIN outward_inventory_invoice_sc oii ON arip.invoice_id = oii.invoice_id 
            // INNER JOIN brand_master bm ON arip.brand_id = bm.brand_id
            // LEFT JOIN tagging_master tm  ON arip.job_id = tm.tagid 
            // INNER JOIN `tbl_spare_parts` tsp ON tsp.spare_id= arip.spare_id where 1=1 and ari.status='1' $whereTag1    $whereTag2";

            $outward_data_qr2 = "SELECT *,ari.created_at AS create_date FROM outward_inventory_pending ari 
            INNER JOIN brand_master bm ON ari.brand_id = bm.brand_id WHERE
            ari.return_status='0' AND ari.part_status='1' $whereTag1    $whereTag2";

            $outward_manual_challan = "SELECT * FROM manual_challan ari INNER JOIN brand_master bm ON ari.brand_id = bm.brand_id
            left JOIN tbl_service_centre tsc ON ari.center_id = tsc.center_id where  1=1 $whereTag2";
            
        }
        #echo $outward_data_qr2;die;
        $DataArr2_view = DB::select($outward_data_qr2); 

        $manual_challan_arr = DB::select($outward_manual_challan); 


        foreach($DataArr2_view as $data)
        {
            $center_id = $data->center_id;
            $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
            $state_id = $center_det->state;
            $state_det = StateMaster::whereRaw("state_id='$state_id'")->first();
            #echo $center_det->pincode;die;
            $data->pincode = $center_det->pincode;
            $data->state_name = $state_det->state_name;
            
        }

        
        
        #echo $tag_data_qr2;die;

        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);


        foreach($DataArr as $dat)
        {
            $center_id = $dat->center_id;
            $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
            $state_id = $center_det->state;
            $state_det = StateMaster::whereRaw("state_id='$state_id'")->first();
            #echo $center_det->pincode;die;
            $dat->center_name = $center_det->center_name;
            $dat->pincode = $center_det->pincode;
            $dat->state_name = $state_det->state_name;
            
        }


        
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/search-challan-sc';
        return view('search-challan-sc')
            ->with('pin_master',$pin_master)
            ->with('state_master',$state_master)
            ->with('state',$state_name)
            ->with('pincode',$pincode)
            ->with('from_date',$from_date)
            ->with('to_date',$to_date)
            ->with('challan_no',$challan_no)
            ->with('DataArr2',$DataArr2)
            ->with('DataArr',$DataArr)
            ->with('DataArr_view',$DataArr_view)
            ->with('DataArr2_view',$DataArr2_view)
            ->with('manual_challan_arr',$manual_challan_arr)
            ->with('url', $url)
            ->with('tab1',$tab1)
            ->with('tab2',$tab2)
            ->with('from_date2',$from_date2)
            ->with('to_date2',$to_date2)
            ->with('brand_arr', $brand_arr)
            ->with('brand_id', $brand_id)
            ->with('brand_id2', $brand_id2)
            ->with('whereTag',$whereTag);
                
    }



    public function generate_challan_sc(Request $request)
    {
        #print_r($request->all());die;
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $invoice_id = $request->input('invoice_id'); 
        $e_way_no = $request->input('e_way_no'); 
        
        
        $tagDet = OutwardPending::whereRaw("invoice_id='$invoice_id'")->first();
        #print_r($tagDet);die;
        $invoice_no = $tagDet->po_no;
        $taggingArr = array();
        $taggingArr['part_status']=1;
        $taggingArr['eway_no']=$e_way_no;
        #$taggingArr['po_no']=$invoice_no;
        
        if(OutwardPending::whereRaw("invoice_id='$invoice_id'")->update($taggingArr))
        {
            echo json_encode(array('resp_id'=>'1',"job_no"=>$invoice_no));exit;
        }
        else
        {
            echo json_encode(array('resp_id'=>'2',"job_no"=>""));exit;
        }
    }


    public function generate_manual_challan(Request $request)
    {
        Session::put("page-title","Generate Manual Challan");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;

        if($request->isMethod('post'))
        {
            $data = $request->all();

            $brand_id = $data['Brand'];
            $center_id = $data['asc_code'];
            #print_r($data);die;
            $manual_arr            =   new ManualChallan();
            $manual_arr->brand_id = $data['Brand'];
            $manual_arr->party_type = $data['party_type'];
            $manual_arr->center_id = $data['asc_code'];
            $manual_arr->to = $data['to'];
            $manual_arr->description = $data['man_ser_no'];
            $manual_arr->part_name = $data['part_name'];
            $manual_arr->part_number = $data['part_number'];
            $manual_arr->ticket_number = $data['ticket_number'];
            $manual_arr->job_number = $data['job_number'];
            $manual_arr->serial_no = $data['sr_no'];
            $manual_arr->type_of_part = $data['type_of_part'];
            $manual_arr->issue_qty = $data['issue_qty'];
            $manual_arr->rate = $data['rate'];
            $manual_arr->gst = $data['gst'];
            $manual_arr->total = $data['total'];
            $manual_arr->grand_total = $data['grand_total'];
            $manual_arr->eway_bill = $data['eway_bill'];
            $manual_arr->remarks = $data['remarks'];
            $manual_arr->created_by = $UserId;

            

            #$record->challan_no = $challan_no;
            $challan_no_date = date('ym');

            $find_max_challan_no=ChallanNo::selectRaw('challan_no')->whereRaw("challan_date=curdate()")->first();

            $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
            $brand_name = $brand_det->brand_name;
            $brand_ser_name = strtoupper(substr($brand_name, 0, 2));

            $new_challan_no = "";
            if(empty($find_max_challan_no))
            {
                $challan_entry_arr = new ChallanNo();
                $challan_entry_arr->challan_date = date('Y-m-d');
                $challan_entry_arr->challan_no = '1';
                $challan_entry_arr->save();

                #$new_challan_no = "Sup/$challan_no_date".'0001';
                $new_challan_no = "$brand_ser_name-$challan_no_date".'0001';
                
            }else
            {
                $str_no = "0000";
                $no = $find_max_challan_no->challan_no;
                $no = $no+1;
                $len = strlen($str_no);
                $newlen = strlen("$no");
                $new_no = substr_replace($str_no, $no, $len-$newlen,$newlen);

                $new_challan_no = "$brand_ser_name-$challan_no_date".$new_no;
                #$new_challan_no = "Sup/$challan_no_date".$new_no;

                ChallanNo::whereRaw("challan_date=curdate()")->update(array('challan_no'=>$no));

            }

            $manual_arr->challan_no= $new_challan_no;
            $manual_arr->save();

            $center_details = ServiceCenter::whereRaw("center_id='$center_id'")->first();
			$state_id = $center_details->state; 
            $state = StateMaster::whereRaw("state_id='$state_id'")->first();

			$doc_arr[] = array('part_name'=> $data['part_name'],'hsn_code'=>$hsn_code,'qty'=>$data['issue_qty'],'rate'=>$data['rate'],'total'=>$data['grand_total']);
            $record->challan_no = $new_challan_no; 
			$data['record'] = $record;
            $data['doc_arr'] = $doc_arr;
			$data['sc_details'] = $center_details;
			$data['state'] = $state;

            $pdf = PDF::loadView('challan', $data); 
            #print_r($pdf);die;
           
            return $pdf->download('manual-challan-'.$new_challan_no.'.pdf');
            

            Session::flash('message', "Manual Challan Genrated Successfully");
            Session::flash('alert-class', 'alert-success');

            return redirect('generate-manual-challan');

        }
        
        
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);

        if($UserType!='Admin')
        {
            $whereTag1 = "and tsc.center_id='$Center_Id'";
        }


        $qr2 = "SELECT brand_id,brand_name  FROM  brand_master where  brand_status='1' ";
        $brand_json           =   DB::select($qr2); 

        $brand_master = array();
        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }
        

        $qr2 = "SELECT tsc.center_id,center_name,asc_code FROM tbl_service_centre  tsc
            INNER JOIN users us ON tsc.email_id = us.email
            WHERE sc_status='1'  order by center_name"; 
        $asc_master           =   DB::select($qr2); 
        #print_r($asc_master);die;
        $new_challan_no = "";
        $find_max_challan_no=ChallanNo::selectRaw('challan_no')->whereRaw("challan_date=curdate()")->first();
        $challan_no_date = date('ym');
        if(empty($find_max_challan_no))
        {
            $challan_entry_arr = new ChallanNo();
            $challan_entry_arr->challan_date = date('Y-m-d');
            $challan_entry_arr->challan_no = '1';
            #$challan_entry_arr->save();

            #$new_challan_no = "Sup/$challan_no_date".'0001';
            $new_challan_no = "$challan_no_date".'0001';
            
        }
        else
        {
            $str_no = "0000";
            $no = $find_max_challan_no->challan_no;
            $no = $no+1;
            $len = strlen($str_no);
            $newlen = strlen("$no");
            $new_no = substr_replace($str_no, $no, $len-$newlen,$newlen);

            $new_challan_no = "$challan_no_date".$new_no;
            #$new_challan_no = "Sup/$challan_no_date".$new_no;

            //ChallanNo::whereRaw("challan_date=curdate()")->update(array('challan_no'=>$no));

        }

        #echo $new_challan_no;die;
        
        //print_r($tag_data); exit;
        $whereTag = base64_encode(http_build_query($request->all()));
        
        //print_r($whereTag); exit;
        $url = $_SERVER['APP_URL'].'/manual-challan';
        return view('manual-challan')
            ->with('pin_master',$pin_master)
            ->with('brand_master',$brand_master)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('pincode',$pincode)
                ->with('new_challan_no',$new_challan_no)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('asc_master',$asc_master)
                ->with('contact_no',$contact_no)
                ->with('url', $url)
                ->with('whereTag',$whereTag); 
                
    }

    public function view_manual_Challan(Request $request)
    {
        

        $invoice_id = $request->input('invoice_id');

        $req_arr           =   DB::select("select * from manual_challan where id='$invoice_id'");

        if (!empty($req_arr) && is_object($req_arr[0])) {
            $data = (array) $req_arr[0];
        } 
        
        #print_r($data);die;

        #$data = $request->all();

        $brand_id = $data['Brand'];
        $center_id = $data['asc_code'];
        #print_r($data);die;
        
        $center_details = ServiceCenter::whereRaw("center_id='$center_id'")->first();
        $state_id = $center_details->state; 
        $state = StateMaster::whereRaw("state_id='$state_id'")->first();

        $doc_arr[] = array('part_name'=> $data['part_name'],'hsn_code'=>$hsn_code,'qty'=>$data['issue_qty'],'rate'=>$data['rate'],'total'=>$data['grand_total']);
        $record->challan_no = $new_challan_no; 
        $data['record'] = $record;
        $data['doc_arr'] = $doc_arr;
        $data['sc_details'] = $center_details;
        $data['state'] = $state;

        $pdf = PDF::loadView('challan', $data); 

        
        return $pdf->download('manual-challan-'.$new_challan_no.'.pdf');
        

        
    }


    public function get_center_detail(Request $request)
    {
        
        $center_id = $request->input('center_id'); 
        
        $centerDet = ServiceCenter::whereRaw("center_id='$center_id'")->first();
        #print_r($centerDet);die;
        $person_name = $centerDet->person_name;
        $contact_no = $centerDet->contact_no;

        echo $person_name. '-' .$contact_no;die;
        
    }
    
    
}

