<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\InvPart;
use App\TagPart;
use App\SparePart;
use App\Inventory;
use App\TaggingMaster;
use App\InventoryCenter;
use DB;
use Auth;
use Session;


class HOInventoryManagementController extends Controller
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
    
    
    
    
    public function view_part_pending(Request $request)
    {
        Session::put("page-title","Allocate Inventory To Job");
        $UserId = Auth::user()->id;
        $Center_Id = Auth::user()->table_id;
        $UserType = Session::get('UserType');
        
        
        
        
        $part_arr             =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        $center_arr           =   DB::select("SELECT center_id,center_name FROM `tbl_service_centre`  WHERE sc_status='1' ");
        $stock_arr            =   DB::select("SELECT tic.* FROM `tbl_inventory` tic  WHERE inv_status='1' ");
        
        $job_part_qry = "SELECT tm.*,bm.brand_name,cm.category_name,pm.product_name,mm.model_name FROM tagging_master tm
INNER JOIN brand_master bm ON tm.brand_id = bm.brand_id AND brand_status='1' 
INNER JOIN product_category_master cm ON tm.product_category_id= cm.product_category_id AND category_status='1' 
INNER JOIN product_master pm ON tm.product_id = pm.product_id AND product_status='1' 
INNER JOIN model_master mm ON tm.model_id = mm.model_id AND model_status='1'
  WHERE  observation='Part Required' and part_status='1' AND case_close IS NULL";
        $job_part_pending = DB::select($job_part_qry); 
        
        
        $url = $_SERVER['APP_URL'].'/view-part-pending-ho';
        return view('part-approval-ho')
                ->with('url', $url)
                ->with('center_arr', $center_arr)
                ->with('job_part_pending', $job_part_pending)
                ->with('part_arr', $part_arr)
                ->with('stock_arr',$stock_arr);
    }
    
    public function part_pending_view(Request $request)
    {
        $tag_id = $request->input('tag_id');
        $part_arr             =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        $center_arr           =   DB::select("SELECT center_id,center_name FROM `tbl_service_centre`  WHERE sc_status='1' ");
        $stock_arr            =   DB::select("SELECT tic.* FROM `tbl_inventory` tic  WHERE inv_status='1' ");
        
        $job_part_qry = "SELECT spare_id,tm.job_id,tsp.center_id,tsp.tag_id,tsp.part_name,tsp.part_no,tsp.hsn_code,tsp.stock_status,bm.brand_name,cm.category_name,
 pm.product_name,mm.model_name   FROM `tagging_spare_part` tsp
 inner join tagging_master tm on tsp.tag_id = tm.TagId
INNER JOIN brand_master bm ON tsp.brand_id = bm.brand_id AND brand_status='1' 
INNER JOIN product_category_master cm ON tsp.product_category_id= cm.product_category_id AND category_status='1' 
INNER JOIN product_master pm ON tsp.product_id = pm.product_id AND product_status='1' 
INNER JOIN model_master mm ON tsp.model_id = mm.model_id AND model_status='1'
WHERE tsp.pending_status='1' $whereTag1   ORDER BY tsp.tag_id";
        $job_part_pending = DB::select($job_part_qry);
        
        return view('part-pending-job-view')
                ->with('center_arr', $center_arr)
                ->with('job_part_pending', $job_part_pending)
                ->with('part_arr', $part_arr)
                ->with('stock_arr',$stock_arr);
        
        
        
    }
    
    public function approve_part_pending(Request $request)
    {   
        $tag_id = $request->input('spare_part');
        
        //$Center_Id = Auth::user()->table_id;
        $approval_date      = date("Y-m-d H:i:s");
        $UserId = Auth::user()->id;
        
        $result = 0;
        
            $TagPartArr = TagPart::whereRaw("tag_id = '$tag_id'")->get();
            

            
            foreach($TagPartArr as $TagPart)
            {
                $center_id = $TagPart->center_id; 
                $tag_id = $TagPart->tag_id;
                $brand_id = $TagPart->brand_id;
                    $product_category_id =$TagPart->product_category_id;
                    $product_id =$TagPart->product_id;
                    $model_id =$TagPart->model_id;
                $part_name = $TagPart->part_name;
                $part_no = $TagPart->part_no;
                $hsn_code = $TagPart->hsn_code;
                $part_status = $TagPart->part_status; //exit;
                $pending_status = $TagPart->pending_status;


                if($part_status=='pending' && $pending_status=='1')
                {
                    $qry1 = "SELECT SUM(stock_qty) stock_qty FROM `tbl_inventory` tic where 
                        brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and
                    part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'"; 
                    $stock_arr = DB::select($qry1);
                    $stock_qty = $stock_arr[0]->stock_qty;
                    $qry2 = "SELECT part_name,part_no,hsn_code,count(1) cnsptn FROM tagging_spare_part WHERE part_status='pending'  and
                        brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and  
                    part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code' group by Part_Name,Part_No,hsn_code"; 
                    $consumption_arr           =   DB::select($qry2);
                    $userd_stock = $consumption_arr[0]->cnsptn;

                    $balance_qty = $stock_qty - $userd_stock;

                    if($balance_qty>=1)
                    {
                        $InvPart                    = new InvPart();
                        $InvPart->spare_id          = $spare_id;
                        //$InvPart->allocation_id         = $center_id;
                        $InvPart->allocation_type   = 'ho';
                        $InvPart->tag_id            = $tag_id;
                        $InvPart->brand_id = $brand_id;
                            $InvPart->product_category_id = $product_category_id;
                            $InvPart->product_id = $product_id;
                            $InvPart->model_id = $model_id;
                        $InvPart->part_name         = $part_name;
                        $InvPart->part_no           = $part_no;
                        $InvPart->hsn_code          = $hsn_code;
                        $InvPart->part_status       = $part_status;
                        $InvPart->pending_status    = $pending_status;
                        $InvPart->approval_date     = $approval_date;
                        $InvPart->approve_by        = $UserId;

                        if($InvPart->save())
                        {
                            $TagPart = TagPart::whereRaw("spare_id = '$spare_id'")->delete();
                            $TagPart_Exist = TagPart::whereRaw("tag_id = '$tag_id'")->first();
                            if($TagPart_Exist->spare_id)
                            {

                            }
                            else
                            {
                                $taggingArr['case_close']=1;
                                $taggingArr['case_close_date']=$approval_date;
                                TaggingMaster::whereRaw("TagId='$tag_id' and case_close is null")->update($taggingArr);
                            }

                            echo $result = '1';exit;
                            //Session::flash('message', "Inventory Added To Center  Successfully.");
                            //Session::flash('alert-class', 'alert-danger');
                        }
                    }
                    else
                    {
                        $taggingArr = array();
                        $taggingArr['stock_status']='stock not available';
                        TagPart::whereRaw("spare_id='$spare_id'")->update($taggingArr);
                        unset($taggingArr);
                        echo $result = '2';exit;
                    }

                }
                else
                {
                    echo $result = '3';exit;
                }
            }
            
            //print_r($TagPart); exit;
            //$spare_id = $TagPart->spare_id;
            
            
        
        
        exit;
    }
    
    public function approve_part_pending_multiple(Request $request)
    {   
        $tag_arr = $request->input('approve');        
        $stock_action = $request->input('stock_action');
        $approval_date      = date("Y-m-d H:i:s");
        $UserId = Auth::user()->id;
        
        //print_r($spare_arr); exit;
        
        if(empty($tag_arr))
        {
            Session::flash('message', "Please Select Check Box First.");
            Session::flash('alert-class', 'alert-danger');
            return redirect("view-part-pending");
        }
        
        if($stock_action=='Approve')
        {
            //$Center_Id = Auth::user()->table_id;
            $st=array('part'=>array('Allocated'=>'0','case_close'=>'0','Case Pending'=>'0'),'case'=>array('case_close'=>array('0'),'case_pending'=>array('0')));
            if(!empty($tag_arr))
            {
                foreach($tag_arr as $tag_id)
                {
                    $spare_arr = TagPart::whereRaw("tag_id = '$tag_id'")->get();
                foreach($spare_arr as $spare_det)
                {
                    //print_r($spare_det);exit;
                     $spare_id = $spare_det->spare_id; 
                    $TagPart = TagPart::whereRaw("spare_id = '$spare_id'")->first();
                    //print_r($TagPart); exit;
                    //$spare_id = $TagPart->spare_id;
                    $center_id = $TagPart->center_id; 
                    $tag_id = $TagPart->tag_id;
                    $brand_id = $TagPart->brand_id;
                    $product_category_id =$TagPart->product_category_id;
                    $product_id =$TagPart->product_id;
                    $model_id =$TagPart->model_id;
                    $part_name = $TagPart->part_name;
                    $part_no = $TagPart->part_no;
                    $hsn_code = $TagPart->hsn_code;
                    $part_status = $TagPart->part_status; //exit;
                    $pending_status = $TagPart->pending_status;


                    if($part_status=='pending' && $pending_status=='1')
                    {
                        $qry1 = "SELECT SUM(stock_qty) stock_qty FROM `tbl_inventory` tic where 
                            brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and
                        part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'"; 
                        $stock_arr = DB::select($qry1);
                        $stock_qty = $stock_arr[0]->stock_qty;
                        $qry2 = "SELECT part_name,part_no,hsn_code,count(1) cnsptn FROM tagging_spare_part WHERE part_status='pending'  and
                          brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and  
                        part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code' group by center_id,Part_Name,Part_No,hsn_code"; 
                        $consumption_arr           =   DB::select($qry2);
                        $userd_stock = $consumption_arr[0]->cnsptn;

                        $balance_qty = $stock_qty - $userd_stock;

                        if($balance_qty>=1)
                        {
                            $InvPart                    = new InvPart();
                            $InvPart->spare_id          = $spare_id;

                            $InvPart->allocation_type   = 'ho';
                            $InvPart->tag_id            = $tag_id;
                            $InvPart->brand_id = $brand_id;
                            $InvPart->product_category_id = $product_category_id;
                            $InvPart->product_id = $product_id;
                            $InvPart->model_id = $model_id;
                            $InvPart->part_name         = $part_name;
                            $InvPart->part_no           = $part_no;
                            $InvPart->hsn_code          = $hsn_code;
                            $InvPart->part_status       = $part_status;
                            $InvPart->pending_status    = $pending_status;
                            $InvPart->approval_date     = $approval_date;
                            $InvPart->approve_by        = $UserId;

                            if($InvPart->save())
                            {
                                $st['part']['Allocated'] += 1;
                                
                                
                                
                                $TagPart = TagPart::whereRaw("spare_id = '$spare_id'")->delete();
                                $TagPart_Exist = TagPart::whereRaw("tag_id = '$tag_id'")->first();
                                if($TagPart_Exist->spare_id)
                                {
                                    $st['case']['case_pending'][]= $tag_id;
                                }
                                else
                                {
                                    $taggingArr = array();
                                    $taggingArr['case_close']=1;
                                    $taggingArr['case_close_date']=$approval_date;
                                    if(TaggingMaster::whereRaw("TagId='$tag_id' and case_close is null")->update($taggingArr))
                                    {
                                        $st['case']['case_close'][]= $tag_id;
                                    }
                                    else
                                    {
                                        $st['case']['case_pending'][]= $tag_id;
                                    }
                                }
                            }
                            else
                            {
                                $st['part']['Not Available'][] = $part_name;
                            }
                        }
                        else
                        {
                            $taggingArr = array();
                            $taggingArr['stock_status']='stock not available';
                            TagPart::whereRaw("spare_id='$spare_id'")->update($taggingArr);
                            unset($taggingArr);
                            $st['part']['Not Available'][] = $part_name;
                        }

                    }

                }
                }
                
            }
            Session::flash('st', $st);
        }
        else
        {
            foreach($spare_arr as $spare_id)
            {    
                $taggingArr = array();
                $taggingArr['part_status'] = 'reject';
                $taggingArr['pending_status'] = 1;
                $taggingArr['ho_reject_date'] = $approval_date;
                $taggingArr['ho_reject_by'] = $UserId;
                
                if(TagPart::whereRaw("spare_id='$spare_id'")->update($taggingArr))
                {
                    $st['part']['case_reject']+= 1;
                }
                else
                {
                    $st['case']['case_reject_failed'] += 1;
                }
            }
        }    
        
        
        if(!empty($st))
        {
            Session::flash('message', "Spare Part Request Processed Successfully.");
            Session::flash('alert-class', 'alert-danger');
        }
        else
        {
            Session::flash('message', "Spare Part Request Not Processed.");
            Session::flash('alert-class', 'alert-danger');
        }
        
        return redirect("view-part-pending-ho");
        
        
    }
    
    
}

