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


class InventoryManagementController extends Controller
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
        
        $whereTag1 = "";
        if($UserType=='ServiceCenter')
        {
            $whereTag1 = "and tsp.center_id='$Center_Id'";
        }
        
        
        $part_arr             =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        $center_arr           =   DB::select("SELECT center_id,center_name FROM `tbl_service_centre`  WHERE sc_status='1' ");
        $stock_arr            =   DB::select("SELECT tic.*,tsc.center_name FROM `tbl_inventory_center` tic 
INNER JOIN tbl_service_centre tsc 
ON tic.center_id = tsc.center_id WHERE stock_status='1' ");
        
        $job_part_qry = "SELECT spare_id,tsp.center_id,tsp.tag_id,tsp.part_name,tsp.part_no,tsp.hsn_code,stock_status,request_to_ho  FROM `tagging_spare_part` tsp
WHERE tsp.pending_status='1' $whereTag1   order by tsp.tag_id";
        $job_part_pending = DB::select($job_part_qry); 
        
        $url = $_SERVER['APP_URL'].'/view-part-pending';
        
        return view('part-pending-approval')
                ->with('center_arr', $center_arr)
                ->with('job_part_pending', $job_part_pending)
                ->with('part_arr', $part_arr)
                ->with('url', $url)
                ->with('stock_arr',$stock_arr);
    }
    
    public function ho_part_request(Request $request)
    {   
        $spare_id = $request->input('spare_id');
        $srno = $request->input('srno');
        $request_date      = date("Y-m-d H:i:s");
        $UserId = Auth::user()->id;
        
        $taggingArr['request_to_ho']=1;
        $taggingArr['request_to_ho_date']=$request_date;
        $taggingArr['request_to_ho_by']=$UserId;
        
        if(TagPart::whereRaw("spare_id='$spare_id'")->update($taggingArr))
        {
            //echo $result = '1';exit;
            $Data = TagPart::whereRaw("spare_id='$spare_id'")->first();
            ?>
            
            <td><?php echo $srno; ?></td>
            <td><input type="checkbox" name="approve[]" value="<?php echo $Data->spare_id; ?>" />&nbsp;<a href="#" onclick="approve_part('<?php echo $Data->spare_id;?>')"> Approve</a></td>
            <td>
                <input type="checkbox" name="request[]" value="<?php echo $Data->spare_id;?>" />&nbsp;
                <?php if($Data->request_to_ho=='1') { ?>
                <font color="green"> Requested</font>
                <?php } else { ?>
                <a href="#" onclick="request_ho('<?php echo $Data->spare_id;?>','<?php echo $srno; ?>');">Request </a>
                <?php } ?>
            </td>
            <td><font color="<?php if($Data->stock_status=='stock not available') echo 'red'; else 'green'; ?>"><?php echo $Data->stock_status; ?></font></td>
            <td><?php echo $Data->part_name;?></td>
            <td><?php echo $Data->part_no;?></td>
            <td><?php echo $Data->hsn_code;?></td>
            <td><a href="#"><?php echo $Data->tag_id; ?></a></td>
                                 
        <?php }
        else
        {
            echo $result = '2';exit;
        }
        
        
        
        exit;
    }
    
    public function approve_part_pending(Request $request)
    {   
        $spare_id = $request->input('spare_part');
        
        $Center_Id = Auth::user()->table_id;
        $approval_date      = date("Y-m-d H:i:s");
        $UserId = Auth::user()->id;
        
        $result = 0;
        
            $TagPart = TagPart::whereRaw("spare_id = '$spare_id'")->first();
            //print_r($TagPart); exit;
            //$spare_id = $TagPart->spare_id;
            $center_id = $TagPart->center_id; 
            $tag_id = $TagPart->tag_id;
            $part_name = $TagPart->part_name;
            $part_no = $TagPart->part_no;
            $hsn_code = $TagPart->hsn_code;
            $part_status = $TagPart->part_status; //exit;
            $pending_status = $TagPart->pending_status;
            
            
            if($part_status=='pending' && $pending_status=='1')
            {
                $qry1 = "SELECT SUM(stock_qty) stock_qty FROM `tbl_inventory_center` tic where center_id='$Center_Id' and
                part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'"; 
                $stock_arr = DB::select($qry1);
                $stock_qty = $stock_arr[0]->stock_qty;
                $qry2 = "SELECT center_id,part_name,part_no,hsn_code,count(1) cnsptn FROM tagging_spare_part WHERE part_status='pending' and center_id='$Center_Id' and
                part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code' group by center_id,Part_Name,Part_No,hsn_code"; 
                $consumption_arr           =   DB::select($qry2);
                $userd_stock = $consumption_arr[0]->cnsptn;
                
                $balance_qty = $stock_qty - $userd_stock;
                
                if($balance_qty>=1)
                {
                    $InvPart                    = new InvPart();
                    $InvPart->spare_id          = $spare_id;
                    $InvPart->allocation_id         = $center_id;
                    $InvPart->allocation_type   = 'center';
                    $InvPart->tag_id            = $tag_id;
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
            
        
        
        exit;
    }
    
    public function approve_part_pending_multiple(Request $request)
    {   
        $spare_arr = $request->input('approve');
        $request_arr = $request->input('request');
        
        $stock_action = $request->input('stock_action');
        
        
        if(empty($spare_arr) && empty($request_arr))
        {
            Session::flash('message', "Please Select Check Box First.");
            Session::flash('alert-class', 'alert-danger');
            return redirect("view-part-pending");
        }
        
        
        if($stock_action=='Approve')
        {
            $Center_Id = Auth::user()->table_id;
            $approval_date      = date("Y-m-d H:i:s");
            $UserId = Auth::user()->id;
        
            //$result = 0;
            $st=array('part'=>array('Allocated'=>'0','case_close'=>'0','Case Pending'=>'0'),'case'=>array('case_close'=>array('0'),'case_pending'=>array('0')));
        
        
            if(!empty($spare_arr))
            {
                foreach($spare_arr as $spare_id)
                {
                    $TagPart = TagPart::whereRaw("spare_id = '$spare_id'")->first();
                    //print_r($TagPart); exit;
                    //$spare_id = $TagPart->spare_id;
                    $center_id = $TagPart->center_id; 
                    $tag_id = $TagPart->tag_id;
                    $part_name = $TagPart->part_name;
                    $part_no = $TagPart->part_no;
                    $hsn_code = $TagPart->hsn_code;
                    $part_status = $TagPart->part_status; //exit;
                    $pending_status = $TagPart->pending_status;


                    if($part_status=='pending' && $pending_status=='1')
                    {
                        $qry1 = "SELECT SUM(stock_qty) stock_qty FROM `tbl_inventory_center` tic where center_id='$Center_Id' and
                        part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'"; 
                        $stock_arr = DB::select($qry1);
                        $stock_qty = $stock_arr[0]->stock_qty;
                        $qry2 = "SELECT center_id,part_name,part_no,hsn_code,count(1) cnsptn FROM tagging_spare_part WHERE part_status='pending' and center_id='$Center_Id' and
                        part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code' group by center_id,Part_Name,Part_No,hsn_code"; 
                        $consumption_arr           =   DB::select($qry2);
                        $userd_stock = $consumption_arr[0]->cnsptn;

                        $balance_qty = $stock_qty - $userd_stock;

                        if($balance_qty>=1)
                        {
                            $InvPart                    = new InvPart();
                            $InvPart->spare_id          = $spare_id;
                            $InvPart->allocation_id         = $center_id;
                            $InvPart->allocation_type   = 'center';
                            $InvPart->tag_id            = $tag_id;
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
        
            if(!empty($request_arr))
            {
                foreach($request_arr as $spare_id)
                {
                    $taggingArr = array();
                    $taggingArr['request_to_ho']=1;
                    $taggingArr['request_to_ho_date']=$request_date;
                    $taggingArr['request_to_ho_by']=$UserId;



                    if(TagPart::whereRaw("spare_id='$spare_id'")->update($taggingArr))
                    {
                        $st['part']['ho request'] += 1;
                    }
                    unlink($taggingArr);
                }

            }
        }
        else
        {
            foreach($spare_arr as $spare_id)
            {    
                $taggingArr = array();
                $taggingArr['pending_status'] = 0;
                $taggingArr['reject_date'] = $approval_date;
                $taggingArr['reject_by'] = $UserId;
                
                if(TagPart::whereRaw("spare_id='$spare_id'")->update($taggingArr))
                {
                    $st['part']['case_reject']+= 1;
                }
                else
                {
                    $st['case']['case_reject_failed'] += 1;
                }
            }
            
            if(!empty($request_arr))
            {
                foreach($request_arr as $spare_id)
                {
                    $taggingArr = array();
                    $taggingArr['request_to_ho']=0;
                    $taggingArr['request_to_ho_date']=$request_date;
                    $taggingArr['request_to_ho_by']=$UserId;



                    if(TagPart::whereRaw("spare_id='$spare_id'")->update($taggingArr))
                    {
                        $st['part']['ho request_reject'] += 1;
                    }
                    unlink($taggingArr);
                }

            }
            
            
        }
          
       // print_r($st); exit;
        Session::flash('st', $st);
        
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
        
        return redirect("view-part-pending");
        
        
    }
    
    
}

