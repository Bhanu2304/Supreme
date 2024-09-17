<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SCInwardInventory;
use App\SCInwardInventoryPart;
use App\ReturnInvoicePart;
use App\DispatchInventory;
use App\DispatchInventoryParticulars;
use App\OutwardInvoice;
use App\InvPart;
use App\TagPart;
use App\ModelMaster;
use App\ServiceCenter;
use App\TaggingMaster;
use App\InventoryCenter;
use App\DispatchInventoryScParticulars;
use App\ScDispatchInventory;
use App\OutwardInventoryPart;
use DB;
use Auth;
use Session;
use Illuminate\Support\Facades\Storage;

class SCReceiveInventoryController extends Controller
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
    
    public function get_scinw_no($sr_no,$po_type,$center_id,$fin_year)
    {
        $new_request_no = "";
        if(empty($sr_no))
        {
            $sr_no = 1;
            $new_request_no = "$po_type".'/'.$center_id.'/'.'000001';
            $no = '1';
        }
        else
        {
            $str_no = "000000";
            $no = $sr_no;
            $no = $no+1;
            $len = strlen($str_no);
            $newlen = strlen("$no");
            $new_no = substr_replace($str_no, $no, $len-$newlen,$newlen);
            $new_request_no = "$po_type".'/'.$center_id.'/'.$new_no;
        }
    
        $inw_srno_det = SCInwardInventory::whereRaw("center_id='$center_id' and fin_year='$fin_year' and inwsc_no='$new_request_no'")->first();
        //print_r($inw_srno_det); exit;
        if(!empty($inw_srno_det))
        {
            return $this->get_scinw_no($no,$po_type,$center_id,$fin_year);
        }
        else
        {
            return array('new_request_no'=>$new_request_no,'sr_no'=>$no);
        }
    }
    
    public function index(Request $request)
    {
        Session::put("page-title","Receive PO");
        $center_id = Auth::user()->table_id;
        //$invoice_master =   DB::select("SELECT * FROM outward_inventory_invoice  WHERE `dispatch`='1'");
        $dispatch_master =   DB::select("SELECT * FROM outward_inventory_dispatch where center_id='$center_id' and dispatch='1' ");
        $receive_master =   DB::select("SELECT * FROM outward_inventory_dispatch where center_id='$center_id' and (dispatch='2' || dispatch='3')");
        
        $url = $_SERVER['APP_URL'].'/sc-receive-po';
        return view('sc-rec-po') 
        ->with('invoice_master', $invoice_master)
        ->with('dispatch_master', $dispatch_master)        
        ->with('receive_master', $receive_master)        
        ->with('url', $url);
    }
    
    public function view_dispatch(Request $request)
    {
        Session::put("page-title","View Dispatch");
        $dispatch_id =  $request->input('dispatch_id');
        $invoice_arr =   DispatchInventoryParticulars::whereRaw("`dispatch_id`='$dispatch_id'")->get();
        $dispatch_det =   DispatchInventory::whereRaw("`dispatch_id`='$dispatch_id'")->first();
        //print_r($dispatch_det->all());exit;
        //$invoice_arr =   DispatchInventoryScParticulars::whereRaw("`dispatch_id`='$dispatch_id'")->get();
        //$dispatch_det =   ScDispatchInventory::whereRaw("`dispatch_id`='$dispatch_id'")->first();
        
        $url = $_SERVER['APP_URL'].'/sc-view-dispatch';
        return view('view-dispatch-sc')
        ->with('invoice_arr', $invoice_arr)
        ->with('dispatch_det', $dispatch_det)        
        ->with('url', $url);
    }
    
    public function save_dispatch(Request $request)
    {
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $dispatch_id =  $request->input('dispatch_id');
        
        
        $dispatch_det = DispatchInventory::whereRaw("dispatch_id='$dispatch_id' and dispatch='1'")->first();
        if(!empty($dispatch_det))
        {
            $SCInwardInventory = new SCInwardInventory();
            $SCInwardInventory->invoice_id      = $dispatch_det->invoice_id;
            $invoice_id = $dispatch_det->invoice_id;
            $OutwardInvoice = OutwardInvoice::whereRaw("invoice_id='$invoice_id'")->first();

            $SCInwardInventory->invoice_no      = $OutwardInvoice->invoice_no;
            $SCInwardInventory->invoice_date    = $OutwardInvoice->created_at;
            $SCInwardInventory->center_id       = $dispatch_det->center_id;
            $SCInwardInventory->asc_name        = $dispatch_det->asc_name;
            $SCInwardInventory->asc_code        = $dispatch_det->asc_code;
            $SCInwardInventory->po_id           = $dispatch_det->po_id;
            $SCInwardInventory->po_no           = $dispatch_det->po_no;
            $SCInwardInventory->po_date         = $dispatch_det->po_date;
            $SCInwardInventory->no_of_cases     = $dispatch_det->no_of_cases;
            $SCInwardInventory->created_at      = $created_at;
            $SCInwardInventory->created_by      = $created_by;
        
            $center_id = $dispatch_det->center_id;
            $po_type = $OutwardInvoice->po_type;

            $fin_year = '';
            $month = date('M'); 
            if(strtolower($month)=='jan' || strtolower($month)=='feb' || strtolower($month)=='mar')
            {
                $currentYear = date('Y');
                $lastYear = $currentYear-1;
                $fin_year = "$lastYear-".substr($currentYear,-2);    
            }
            else
            {
                $currentYear = date('Y');
                $NextYear = $currentYear+1;
                $fin_year = "$currentYear-".substr($NextYear,-2); 
            }
        
        
            $inw_date = date('Y-m-d');
            //$fin_year = date('Y');
            $sr_no_arr = DB::select("SELECT max(sr_no) srno FROM `inward_inventory_sc` where center_id='$center_id' and fin_year='$fin_year'");

            $sr_no = 0;
            foreach($sr_no_arr as $sr_det)
            {
                $sr_no = $sr_det->srno;
            }
         
            $sr_arr = $this->get_scinw_no($sr_no,$po_type,$center_id,$fin_year);
            $new_request_no = $sr_arr['new_request_no'];
            $SCInwardInventory->inwsc_no = $new_request_no;
            $SCInwardInventory->inwsc_date = $inw_date;
            $SCInwardInventory->sr_no = $sr_arr['sr_no'];
            $SCInwardInventory->fin_year = $fin_year;
            //$DispatchInventory = DispatchInventory::whereRaw("dispatch_id='$dispatch_id'")->first();

            $SCInwardInventory->eway_bill_no    = $dispatch_det->eway_bill_no;
            $SCInwardInventory->doc_no          = $dispatch_det->doc_no;
            $SCInwardInventory->veh_doc_no      = $dispatch_det->veh_doc_no;
            $SCInwardInventory->dispatch_ref_no = $dispatch_det->dispatch_ref_no;
                
        
        
            DB::beginTransaction();
            $msg = "";
            if($SCInwardInventory->save())
            {
                $inw_id = $SCInwardInventory->id;

                $dispatch_part_det = DispatchInventoryParticulars::whereRaw("dispatch_id='$dispatch_id'")->get();
                $inward_part_arr = array();
                $inventory_centerAdd = array();
                $inventory_centerUpdate = array();
                $job_part_allocation = array();
                
                
                foreach($dispatch_part_det as $det)
                {
                    $part_record = array();

                    $part_record['inwsc_id']                =     $inw_id;
                    $part_record['inw_ser_no']              =     $new_request_no;
                    $part_record['brand_id']                =     $det->brand_id;

                    $model_master = ModelMaster::whereRaw("model_id='{$det->model_id}'")->first();

                    $part_record['product_category_id']     =     $model_master->product_category_id;
                    $part_record['product_id']              =     $model_master->product_id;
                    $part_record['model_id']                =     $model_master->model_id;
                    $part_record['spare_id']                =     $det->spare_id;
                    $part_record['part_no']                 =     $det->part_no;
                    $part_record['part_name']               =     $det->part_name;
                    $part_record['item_color']              =     $det->color;
                    $part_record['hsn_code']                =     $det->hsn_code;
                    $part_record['gst']                     =     $det->gst;
                    $part_record['item_qty']                =     $det->dispatch_qty;
                    $part_record['bin_no']                  =     $det->bin_no;
                    $part_record['asc_amount']              =     $det->asc_amount;
                    $part_record['customer_amount']         =     $det->customer_amount;                              
                    //$part_record['discount']                =     $det->discount;
                    
                    
                    ///Add Inventory To Center works Starts From Here. ///
                    $inward_part_arr[] = $part_record;
                    $inventory_record = array();
                    $inventory_record['center_id']                   =     $center_id;
                    $inventory_record['brand_id']                   =     $det->brand_id;
                    $inventory_record['product_category_id']        =     $model_master->product_category_id;
                    $inventory_record['product_id']                 =     $model_master->product_id;
                    $inventory_record['model_id']                   =     $model_master->model_id;
                    $inventory_record['part_no']                    =     $det->part_no;
                    $inventory_record['spare_id']                   =     $det->spare_id;
                    $inventory_record['part_name']                  =     $det->part_name;
                    $inventory_record['item_color']                 =     $det->color;
                    $inventory_record['hsn_code']                   =     $det->hsn_code;
                    $inventory_record['customer_amount']            =     $det->customer_amount;
                    $inventory_record['avail_qty']                  =     $det->dispatch_qty;
                    $inventory_record['stock_qty']                  =     $det->dispatch_qty;
                    $inventory_record['created_at']                 =     $created_at;
                    $inventory_record['created_by']                 =     $created_by;

                    $spare_id = $det->spare_id; 
                    $InventoryCenterExist = InventoryCenter::whereRaw("spare_id='$spare_id'")->first();
                    if(empty($InventoryCenterExist))
                    {
                        $inventory_centerAdd[] = $inventory_record;
                    }
                    else
                    {
                        $inventory_centerUpdate[] =  $inventory_record;
                    }
                    ///Add Inventory To Center works End Here. ///
                    
                    
                    //Automatic allocation of Part PO Works Starts From Here
                    if(!empty($det->job_id))
                    {
                        $job_part_allocation_record = array();
                        $job_part_allocation_record['job_id'] = $det->job_id;
                        $job_part_allocation_record['po_id'] = $det->po_id;
                        $job_part_allocation_record['dispatch_part_id'] = $det->dispatch_part_id;
                        $job_part_allocation[] = $job_part_allocation_record;
                    }
                    
                    
                }

                if(SCInwardInventoryPart::insert($inward_part_arr))
                {
                    if(DispatchInventory::whereRaw("dispatch_id='$dispatch_id'")
                        ->update(
                                array('dispatch'=>'2',
                                    'receive_date'=>date('Y-m-d H:i:s'),
                                    'receive_by'=>$created_by
                                    )
                                )
                       )
                    {

                        $flag_inventory_add = true;
                        if(!empty($inventory_centerAdd))
                        {
                            if(InventoryCenter::insert($inventory_centerAdd))
                            {
                                $flag_inventory_add = true;
                            }
                            else
                            {
                                $flag_inventory_add = false;
                            }
                        }

                        foreach($inventory_centerUpdate as $inv)
                        {
                            $spare_id = $inv['spare_id'];
                            $avail_qty = $inv['avail_qty'];
                            $new_avail_qty = 0;
                            $total_stock_qty = 0;
                            if(!empty($avail_qty))
                            {
                                $old_inv_det = InventoryCenter::whereRaw("spare_id='$spare_id'")->first();
                                $new_avail_qty = $old_inv_det->avail_qty;
                                $new_avail_qty += (int)  $avail_qty;
                                $total_stock_qty = $old_inv_det->stock_qty;
                                $total_stock_qty += (int)  $avail_qty;
                                
                            }
                            if(InventoryCenter::whereRaw("spare_id='$spare_id'")->update(array('avail_qty'=>"$new_avail_qty",'stock_qty'=>"$total_stock_qty")))
                            {
                                $flag_inventory_add = true;
                            }
                            else
                            {
                                $flag_inventory_add = false;
                            }
                        }

                        if($flag_inventory_add)
                        {
                            $msg = '1';
                            DB::commit();
                            
                            DB::beginTransaction();
                            
                            foreach($job_part_allocation as $po_job_part_id)
                            {
                                $part_id = $po_job_part_id['po_id'];
                                $dispatch_part_id = $po_job_part_id['dispatch_part_id'];
                                $tag_id = $po_job_part_id['job_id'];
                                
                                $part_det = TagPart::whereRaw("part_id='$part_id'")->first();
                                $center_id = $part_det->center_id;
                                $spare_id = $part_det->spare_id;
                                $pending_parts = $part_det->pending_parts;
                                
                                $tag_det = TaggingMaster::whereRaw("TagId='$tag_id'")->first();
                                $job_part_pending =(int) $tag_det->part_pending;
                                if(empty($job_part_pending))
                                {
                                    $job_part_pending = 0;
                                }

                                $new_pending_parts = $pending_parts;
                                
                                $InventoryCenter = InventoryCenter::whereRaw("center_id='$center_id' and spare_id='$spare_id' and avail_qty>0")->first();
                                if(!empty($InventoryCenter))
                                {
                                    DB::beginTransaction();
                                    $avail_qty = (int)$InventoryCenter->avail_qty;
                                    $part_provided = 0;
                                    if($avail_qty>=$new_pending_parts)
                                    {
                                       $avail_qty -=  $new_pending_parts;
                                       $part_provided = $new_pending_parts;
                                       $job_part_pending -= $new_pending_parts; 
                                       $new_pending_parts = 0;
                                    }
                                    else
                                    {
                                        $new_pending_parts -=  $avail_qty;
                                        $part_provided = $avail_qty;
                                        $job_part_pending -= $avail_qty; 
                                        $avail_qty = 0;
                                    }

                                    if(InventoryCenter::whereRaw("center_id='$center_id' and spare_id='$spare_id'")->update(array('avail_qty'=>$avail_qty)))
                                    {
                                        $InvPart                    =   new InvPart();
                                        $InvPart->part_id           =   $part_id;
                                        $InvPart->spare_id          =   $spare_id;
                                        $InvPart->part_po_no        =   $part_det->part_po_no;
                                        $InvPart->part_po_date      =   $part_det->part_po_date;
                                        $InvPart->sr_no             =   $part_det->sr_no;
                                        $InvPart->center_id         =   $part_det->center_id;
                                        $InvPart->tag_id            =   $part_det->tag_id;
                                        $InvPart->allocation_type   =   'HO';
                                        $InvPart->brand_id          =   $part_det->brand_id;
                                        $InvPart->product_category_id   =   $part_det->product_category_id;
                                        $InvPart->product_id        =   $part_det->product_id;
                                        $InvPart->model_id          = $part_det->model_id;
                                        $InvPart->part_name         = $part_det->part_name;
                                        $InvPart->part_no           = $part_det->part_no;
                                        $InvPart->color             = $part_det->color;
                                        $InvPart->hsn_code          = $InventoryCenter->hsn_code;
                                        $InvPart->part_allocated    = $part_provided;
                                        $InvPart->part_required     = $pending_parts;
                                        $InvPart->remarks           = $part_det->remarks;

                                        if($InvPart->save())
                                        {
                                            if($new_pending_parts==0)
                                            {
                                                if(TagPart::whereRaw("part_id='$part_id'")->update(array("delete_status='1'")))
                                                {
                                                    $tagging_arr = array();
                                                    $tagging_arr['part_pending'] = $job_part_pending;
                                                    if($job_part_pending==0)
                                                    {
                                                        $tagging_arr['part_status'] =0;
                                                    }
                                                    else
                                                    {
                                                        $tagging_arr['part_status'] =1;
                                                    }
                                                    
                                                    if(TaggingMaster::whereRaw("TagId='$tag_id'")->update($tagging_arr))
                                                    {
                                                        DB::commit();
                                                    }
                                                    else
                                                    {
                                                        DB::rollback();
                                                    }
                                                }
                                                else
                                                {
                                                    DB::rollback();
                                                }
                                            }
                                            else
                                            {
                                                if(TagPart::whereRaw("part_id='$part_id'")->update(array('pending_parts'=>$new_pending_parts)))
                                                {
                                                    DB::commit();
                                                }
                                                else
                                                {
                                                    DB::rollback();
                                                }
                                            }
                                        }
                                        else
                                        {
                                            DB::rollback();
                                        }
                                    }
                                    else
                                    {
                                        DB::rollback();
                                    }
                                }
                            }
                            
                            
                            
                        }
                        else
                        {
                            $msg = '2';
                            DB::rollback();
                        }
                    }
                    else
                    {
                        $msg = '2';
                        DB::rollback();
                    }
                }
                else
                {
                    $msg = '3';
                    DB::rollback();
                }

            }
            else
            {
                $msg = '3';
                DB::rollback();
            }
            echo $msg; exit;
        }
        else
        {
            echo $msg = '1';exit;
        }
    
    } 
    
    public function get_dispatch_parts(Request $request)
    {
        $dispatch_id =  $request->input('dispatch_id');
        $dispatch_part_det = DispatchInventoryParticulars::whereRaw("dispatch_id='$dispatch_id'")->get();
        $ReturnInvoicePart_arr = ReturnInvoicePart::whereRaw("dispatch_id='$dispatch_id'")->get();
    ?>
    <table border="1">
        <tr>
            <th>Sr. No.</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Part Name</th>
            <th>Part Code</th>
            <th>Color</th>
            <th>Issued Qty.</th>
            <th>Reason</th>
            <th>No. of Short Parts</th>
            <th>Remarks</th>
            <th>Action</th>
        </tr>
        
        <?php $i = 1;
            foreach($dispatch_part_det as $dis)
            {   


            ?>

        <tr>

        <td><?php echo $i++; ?></td>    

        <td><?php  echo $dis->brand_name;?></td>
        <td><?php  echo $dis->model_name;?></td>
        <td><?php  echo $dis->part_name;?></td>
        <td><?php  echo $dis->part_no;?></td>
        <td><?php  echo $dis->color;?></td>
        <td><?php  echo $dis->issued_qty;?></td>

        <td><form id="form<?php echo $dis->dispatch_part_id; ?>" action="return-dispatch-sc" method="post">
            <select name="reason" id="reason<?php echo $dis->dispatch_part_id; ?>" required="" >
                <option value="Short">Short</option>
                <option value="Mismatch">Mismatch</option>
                <option value="Any Other">Any Other</option>
            </select>
            </form>
        </td>
        <td>
            <input form="form<?php echo $dis->dispatch_part_id; ?>" type="text" id="miss_qty<?php echo $dis->dispatch_part_id; ?>" name="miss_qty" style="width: 80px;" onblur="validate_parts(<?php echo $dis->dispatch_part_id; ?>);" onkeypress="return checkNumber(this.value,event,'<?php echo $dis->dispatch_part_id; ?>')"  value="">
        </td>
        <td>
            <textarea form="form<?php echo $dis->dispatch_part_id; ?>" name="remarks" id="remarks<?php echo $dis->dispatch_part_id; ?>" required="" placeholder="Remarks"><?php echo $remarks; ?></textarea>
        </td>        
        <td>
            <input form="form<?php echo $dis->dispatch_part_id; ?>" type="hidden"  id="dispatch_qty<?php echo $dis->dispatch_part_id; ?>"   value="<?php echo $dis->dispatch_qty; ?>">
            <input form="form<?php echo $dis->dispatch_part_id; ?>" type="hidden"  name="dispatch_id"   value="<?php echo $dis->dispatch_id; ?>">    
            <input form="form<?php echo $dis->dispatch_part_id; ?>" type="hidden"  name="dispatch_part_id"   value="<?php echo $dis->dispatch_part_id; ?>">
            <?php if($dis->dispatch=='0') { ?>
            <button form="form<?php echo $dis->dispatch_part_id; ?>" type="submit" onclick="return save_cancel_dispatch('<?php echo $dis->dispatch_part_id; ?>','<?php echo $dis->dispatch_id; ?>')" class="mt-2 btn btn-success">Return</button>
            <?php } else { ?>
            Returned Generated.
            <?php } ?>
        </td>    
        </tr>

            <?php } ?>
    </table>

    <br/><br/><br/>

    <table class="table">
        <tr>
            <th>Sr. No.</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Part Name</th>
            <th>Part Code</th>
            <th>Color</th>
            <th>Issued Qty.</th>
            <th>Reason</th>
            <th>No. of Parts</th>
            <th>Remarks</th>
        </tr>
        <?php $i = 1;
            foreach($ReturnInvoicePart_arr as $dis)
            {   
            ?>

        <tr>

        <td><?php echo $i++; ?></td>    
        <td><?php  echo $dis->brand_name;?></td>
        <td><?php  echo $dis->model_name;?></td>
        <td><?php  echo $dis->part_name;?></td>
        <td><?php  echo $dis->part_no;?></td>
        <td><?php  echo $dis->color;?></td>
        <td><?php  echo $dis->issued_qty;?></td>
        <td><?php  echo $dis->return_type;?></td>
        <td><?php  echo $dis->miss_qty;?></td>
        <td><?php  echo $dis->remarks;?></td>
        </tr>

            <?php } ?>


    </table>

<?php  
    
    }
    
    public function get_srn_parts(Request $request)
    {
        $dispatch_id =  $request->input('dispatch_id');
        $dispatch_part_det = DispatchInventoryParticulars::whereRaw("dispatch_id='$dispatch_id'")->get();
        $ReturnInvoicePart_arr = ReturnInvoicePart::whereRaw("dispatch_id='$dispatch_id'")->get();
        
        $str_server = str_replace('public', '', $_SERVER['APP_URL']);
    ?>
    <table border="1" style="font-size:12px;word-break: break-all;">
        <tr>
            <th>Sr. No.</th>
            <th>PO Date</th>
            <th>Part PO No.</th>
            <th>PO Type</th>
            <th>ASC Name</th>
            <th>ASC Code</th>
            <th>Job No.</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Part Name</th>
            <th>Part Code</th>
            <th>Color</th>
            <th>Issued Qty.</th>
            <th>Reason</th>
            <th>SRN Qty.</th>
            <th>Remarks</th>
            <th>Upload Image 1</th>
            <th>Upload Image 2</th>
            <th>Action</th>
        </tr>
        
        <?php $i = 1;
            foreach($dispatch_part_det as $dis)
            {   
            ?>
        <tr>

        <td><?php echo $i++; ?></td>    
        <td><?php  echo $dis->po_date;?></td>
        <td><?php  echo $dis->po_no;?></td>
        <td><?php  echo $dis->po_type;?></td>
        <td><?php  echo $dis->asc_name;?></td>
        <td><?php  echo $dis->asc_code;?></td>
        <td><?php  echo $dis->job_no;?></td>
        <td><?php  echo $dis->brand_name;?></td>
        <td><?php  echo $dis->model_name;?></td>
        <td><?php  echo $dis->part_name;?></td>
        <td><?php  echo $dis->part_no;?></td>
        <td><?php  echo $dis->color;?></td>
        <td><?php  echo $dis->issued_qty;?></td>

        <td><form id="form<?php echo $dis->dispatch_part_id; ?>" action="return-srn-sc" method="post" enctype="multipart/form-data">
            <select name="reason" id="reason<?php echo $dis->dispatch_part_id; ?>" required="" >
                <option value="Faulty">Faulty</option>
                <option value="Mismatch">Mismatch</option>
                <option value="Defective">Defective</option>
            </select>
            </form>
        </td>
        <td>
            <input style="width:50px;" form="form<?php echo $dis->dispatch_part_id; ?>" type="text" id="miss_qty<?php echo $dis->dispatch_part_id; ?>" name="miss_qty" style="width: 80px;" onblur="validate_parts(<?php echo $dis->dispatch_part_id; ?>);" onkeypress="return checkNumber(this.value,event,'<?php echo $dis->dispatch_part_id; ?>')"  value="">
        </td>
        <td>
            <textarea form="form<?php echo $dis->dispatch_part_id; ?>" name="remarks" id="remarks<?php echo $dis->dispatch_part_id; ?>" required="" placeholder="Remarks"><?php echo $remarks; ?></textarea>
        </td>
        <td>
            <input type="file" style="width:80px;" form="form<?php echo $dis->dispatch_part_id; ?>" name="image1" id="image1_<?php echo $dis->dispatch_part_id; ?>" >
        </td>
        <td>
            <input type="file" style="width:80px;" form="form<?php echo $dis->dispatch_part_id; ?>" name="image2" id="image2_<?php echo $dis->dispatch_part_id; ?>" >
        </td>
        <td>
            <input form="form<?php echo $dis->dispatch_part_id; ?>" type="hidden"  id="dispatch_qty<?php echo $dis->dispatch_part_id; ?>"   value="<?php echo $dis->dispatch_qty; ?>">
            <input form="form<?php echo $dis->dispatch_part_id; ?>" type="hidden"  name="dispatch_id"   value="<?php echo $dis->dispatch_id; ?>">    
            <input form="form<?php echo $dis->dispatch_part_id; ?>" type="hidden"  name="dispatch_part_id"   value="<?php echo $dis->dispatch_part_id; ?>">
            <?php if($dis->dispatch=='0') { ?>
            <button style="width:80px;" form="form<?php echo $dis->dispatch_part_id; ?>" type="submit" onclick="return save_srn_dispatch('<?php echo $dis->dispatch_part_id; ?>','<?php echo $dis->dispatch_id; ?>')" class="mt-2 btn btn-success">Apply</button>
            <?php } else { ?>
            SRN Generated.
            <?php } ?>
        </td>    
        </tr>
      <?php } ?>
    </table>

    <br/><br/><br/>

    <table class="table" style="font-size:12px;word-break: break-all;">
        <tr>
            <th>Sr. No.</th>
            <th>PO Date</th>
            <th>Part PO No.</th>
            <th>PO Type</th>
            <th>ASC Name</th>
            <th>ASC Code</th>
            <th>Job No.</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Part Name</th>
            <th>Part Code</th>
            <th>Color</th>
            <th>Issued Qty.</th>
            <th>Reason</th>
            <th>SRN Parts</th>
            <th>Remarks</th>
            <th>Upload Image 1</th>
            <th>Upload Image 2</th>
        </tr>
        <?php $i = 1;
            foreach($ReturnInvoicePart_arr as $dis)
            {   
            ?>

        <tr>

        <td><?php echo $i++; ?></td>    
        <td><?php  echo $dis->po_date;?></td>
        <td><?php  echo $dis->po_no;?></td>
        <td><?php  echo $dis->po_type;?></td>
        <td><?php  echo $dis->asc_name;?></td>
        <td><?php  echo $dis->asc_code;?></td>
        <td><?php  echo $dis->job_no;?></td>
        <td><?php  echo $dis->brand_name;?></td>
        <td><?php  echo $dis->model_name;?></td>
        <td><?php  echo $dis->part_name;?></td>
        <td><?php  echo $dis->part_no;?></td>
        <td><?php  echo $dis->color;?></td>
        <td><?php  echo $dis->issued_qty;?></td>
        <td><?php  echo $dis->return_type;?></td>
        <td><?php  echo $dis->miss_qty;?></td>
        <td><?php  echo $dis->remarks;?></td>
        <td>
           <?php if(!empty($dis->image1)) { ?>
            <a href="<?php echo "{$str_server}/storage/app/srn_file/".$dis->return_id."/".$dis->image1; ?>" target="_blank">image1</a>
           <?php } ?>
        </td>
        <td>
           <?php if(!empty($dis->image2)) { ?>
            <a href="<?php echo "{$str_server}/storage/app/srn_file/".$dis->return_id."/".$dis->image2; ?>" target="_blank">image2</a>
           <?php } ?>
        </td>
        </tr>

            <?php } ?>


    </table>

<?php  
    
    }
    
    public function get_cancel_det(Request $request)
    {
        $dispatch_id =  $request->input('dispatch_id');
        
        $ReturnInvoicePart_arr = ReturnInvoicePart::whereRaw("dispatch_id='$dispatch_id'")->get();
    ?>
        <table class="table">
            <tr>
                <th>Sr. No.</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Part Name</th>
                <th>Part Code</th>
                <th>Color</th>
                <th>Issued Qty.</th>
                <th>Reason</th>
                <th>No. of Parts</th>
                <th>Remarks</th>
            </tr>
            <?php $i = 1;
                foreach($ReturnInvoicePart_arr as $dis)
                {   
                ?>
                
            <tr>
                
            <td><?php echo $i++; ?></td>    
            <td><?php  echo $dis->brand_name;?></td>
            <td><?php  echo $dis->model_name;?></td>
            <td><?php  echo $dis->part_name;?></td>
            <td><?php  echo $dis->part_no;?></td>
            <td><?php  echo $dis->color;?></td>
            <td><?php  echo $dis->issued_qty;?></td>
            <td><?php  echo $dis->return_type;?></td>
            <td><?php  echo $dis->miss_qty;?></td>
            <td><?php  echo $dis->remarks;?></td>
            </tr>
            
                <?php } ?>
            
            
        </table>


    <?php  }
    
    public function return_dispatch_sc(Request $request)
    {
        $dispatch_id =  $request->input('dispatch_id');
        $dispatch_part_id = $request->input('dispatch_part_id');
        $reason =  addslashes($request->input('reason'));
        $remarks =  addslashes($request->input('remarks'));
        $miss_qty = addslashes($request->input('miss_qty'));
        //print_r($request->all()); exit;
        $dispatch_det = DispatchInventory::whereRaw("dispatch_id='$dispatch_id'")->first();
        
        $ReturnInvoicePart = new ReturnInvoicePart();
        $ReturnInvoicePart->invoice_id = $dispatch_det->invoice_id;
        $ReturnInvoicePart->invoice_no = $dispatch_det->invoice_no;
        $ReturnInvoicePart->center_id = $dispatch_det->center_id;
        $ReturnInvoicePart->asc_name = $dispatch_det->asc_name;
        $ReturnInvoicePart->asc_code = $dispatch_det->asc_code;
        $ReturnInvoicePart->po_date = $dispatch_det->po_date;
        $ReturnInvoicePart->po_id = $dispatch_det->po_id;
        $ReturnInvoicePart->po_no = $dispatch_det->po_no;
        
        $dispatch_part_det = DispatchInventoryParticulars::whereRaw("dispatch_part_id='$dispatch_part_id' and dispatch='0'")->first();
        
        $avail_qty = $dispatch_part_det->dispatch_qty;
        if($miss_qty>$avail_qty)
        {
            echo '3';exit;
        }
        
        $avail_qty -= $miss_qty;
        
        $ReturnInvoicePart->return_type = $reason;
        $ReturnInvoicePart->remarks = $remarks;
        $ReturnInvoicePart->miss_qty = $miss_qty;
        
        
        $ReturnInvoicePart->case_type = $dispatch_part_det->case_type;
        $ReturnInvoicePart->req_id = $dispatch_part_det->req_id;
        $ReturnInvoicePart->po_type = $dispatch_part_det->po_type;
        $ReturnInvoicePart->job_id = $dispatch_part_det->job_id;
        $ReturnInvoicePart->job_no = $dispatch_part_det->job_no;
        
        $ReturnInvoicePart->brand_id = $dispatch_part_det->brand_id;
        $ReturnInvoicePart->brand_name = $dispatch_part_det->brand_name;
        $ReturnInvoicePart->model_id = $dispatch_part_det->model_id;
        $ReturnInvoicePart->model_name = $dispatch_part_det->model_name;
        $ReturnInvoicePart->spare_id = $dispatch_part_det->spare_id;
        $ReturnInvoicePart->part_no = $dispatch_part_det->part_no;
        $ReturnInvoicePart->part_name = $dispatch_part_det->part_name;
        $ReturnInvoicePart->hsn_code = $dispatch_part_det->hsn_code;
        $ReturnInvoicePart->gst = $dispatch_part_det->gst;
        $ReturnInvoicePart->color = $dispatch_part_det->color;
        $ReturnInvoicePart->req_qty = $dispatch_part_det->req_qty;
        $ReturnInvoicePart->issued_qty = $dispatch_part_det->issued_qty;
        $ReturnInvoicePart->miss_qty = $miss_qty;
        $ReturnInvoicePart->asc_amount = $dispatch_part_det->asc_amount;
        $ReturnInvoicePart->customer_amount = $dispatch_part_det->customer_amount;
        $ReturnInvoicePart->discount = $dispatch_part_det->discount;
        $ReturnInvoicePart->total = $dispatch_part_det->total;
        $ReturnInvoicePart->discount_amount = $dispatch_part_det->discount_amount;
        $ReturnInvoicePart->net_bill = $dispatch_part_det->net_bill;
        $ReturnInvoicePart->gst_amount = $dispatch_part_det->gst_amount;
        $ReturnInvoicePart->grand_total = $dispatch_part_det->grand_total;
        $ReturnInvoicePart->grand_total = $dispatch_part_det->grand_total;
        $ReturnInvoicePart->dispatch_part_id = $dispatch_part_id;
        $ReturnInvoicePart->dispatch_id = $dispatch_id;
        
        $dispatch = 0;
        if($avail_qty==0)
        {
            $dispatch = 2;
        }
        
        DB::beginTransaction();
        if($ReturnInvoicePart->save())
        {
            if(DispatchInventoryParticulars::whereRaw("dispatch_part_id='$dispatch_part_id'")
                    ->update(
                            array('dispatch'=>$dispatch,
                                'dispatch_qty'=>$avail_qty,
                                'return_reason'=>$reason,
                                'return_remarks'=>$remarks)))
            {
                if(!DispatchInventoryParticulars::whereRaw("dispatch_id='$dispatch_id' and dispatch='0'")->first())
                {
                    if(DispatchInventory::whereRaw("dispatch_id='$dispatch_id'")
                    ->update(array('dispatch'=>'3')))
                    {
                        DB::commit();
                        echo 'Spare Part '.$reason.' Request Send Successfully.';exit;
                    }
                    else
                    {
                        DB::rollback();
                                    echo 'Spare Part '.$reason.' Request Failed.';exit;
                    }
                }
                else
                {
                    DB::commit();
                    echo 'Spare Part '.$reason.' Request Send Successfully.';exit;
                }
            }
            else
            {
                DB::rollback();
                echo 'Spare Part '.$reason.' Request Failed.';exit;
            }
        }
        else
        {
            DB::rollback();
            echo 'Spare Part '.$reason.' Request Failed.';exit;
        }
    }
    
    public function return_srn_sc(Request $request)
    {
        $dispatch_id =  $request->input('dispatch_id');
        $dispatch_part_id = $request->input('dispatch_part_id');
        $reason =  addslashes($request->input('reason'));
        $remarks =  addslashes($request->input('remarks'));
        $miss_qty = addslashes($request->input('miss_qty'));
        //print_r($request->all()); 
        //print_r($_FILES); exit;
        $dispatch_det = DispatchInventory::whereRaw("dispatch_id='$dispatch_id'")->first();
        
        $ReturnInvoicePart = new ReturnInvoicePart();
        $ReturnInvoicePart->invoice_id = $dispatch_det->invoice_id;
        $ReturnInvoicePart->invoice_no = $dispatch_det->invoice_no;
        $ReturnInvoicePart->center_id = $dispatch_det->center_id;
        $ReturnInvoicePart->asc_name = $dispatch_det->asc_name;
        $ReturnInvoicePart->asc_code = $dispatch_det->asc_code;
        $ReturnInvoicePart->po_date = $dispatch_det->po_date;
        $ReturnInvoicePart->po_id = $dispatch_det->po_id;
        $ReturnInvoicePart->po_no = $dispatch_det->po_no;
        
        $dispatch_part_det = DispatchInventoryParticulars::whereRaw("dispatch_part_id='$dispatch_part_id' and dispatch='0'")->first();
        
        $avail_qty = $dispatch_part_det->dispatch_qty;
        if($miss_qty>$avail_qty)
        {
            echo '3';exit;
        }
        
        $avail_qty -= $miss_qty;
        
        $ReturnInvoicePart->return_type = $reason;
        $ReturnInvoicePart->remarks = $remarks;
        $ReturnInvoicePart->miss_qty = $miss_qty;
        
        
        $ReturnInvoicePart->case_type = $dispatch_part_det->case_type;
        $ReturnInvoicePart->req_id = $dispatch_part_det->req_id;
        $ReturnInvoicePart->po_type = $dispatch_part_det->po_type;
        $ReturnInvoicePart->job_id = $dispatch_part_det->job_id;
        $ReturnInvoicePart->job_no = $dispatch_part_det->job_no;
        
        $ReturnInvoicePart->brand_id = $dispatch_part_det->brand_id;
        $ReturnInvoicePart->brand_name = $dispatch_part_det->brand_name;
        $ReturnInvoicePart->model_id = $dispatch_part_det->model_id;
        $ReturnInvoicePart->model_name = $dispatch_part_det->model_name;
        $ReturnInvoicePart->spare_id = $dispatch_part_det->spare_id;
        $ReturnInvoicePart->part_no = $dispatch_part_det->part_no;
        $ReturnInvoicePart->part_name = $dispatch_part_det->part_name;
        $ReturnInvoicePart->hsn_code = $dispatch_part_det->hsn_code;
        $ReturnInvoicePart->gst = $dispatch_part_det->gst;
        $ReturnInvoicePart->color = $dispatch_part_det->color;
        $ReturnInvoicePart->req_qty = $dispatch_part_det->req_qty;
        $ReturnInvoicePart->issued_qty = $dispatch_part_det->issued_qty;
        $ReturnInvoicePart->miss_qty = $miss_qty;
        $ReturnInvoicePart->asc_amount = $dispatch_part_det->asc_amount;
        $ReturnInvoicePart->customer_amount = $dispatch_part_det->customer_amount;
        $ReturnInvoicePart->discount = $dispatch_part_det->discount;
        $ReturnInvoicePart->total = $dispatch_part_det->total;
        $ReturnInvoicePart->discount_amount = $dispatch_part_det->discount_amount;
        $ReturnInvoicePart->net_bill = $dispatch_part_det->net_bill;
        $ReturnInvoicePart->gst_amount = $dispatch_part_det->gst_amount;
        $ReturnInvoicePart->grand_total = $dispatch_part_det->grand_total;
        $ReturnInvoicePart->dispatch_part_id = $dispatch_part_id;
        $ReturnInvoicePart->dispatch_id = $dispatch_id;
        
        $dispatch = 0;
        if($avail_qty==0)
        {
            $dispatch = 2;
        }
        
        DB::beginTransaction();
        if($ReturnInvoicePart->save())
        {
            $return_id = $ReturnInvoicePart->id;
            if(DispatchInventoryParticulars::whereRaw("dispatch_part_id='$dispatch_part_id' and dispatch='0'")
                    ->update(
                            array('dispatch'=>$dispatch,
                                'dispatch_qty'=>$avail_qty,
                                'return_reason'=>$reason,
                                'return_remarks'=>$remarks)))
            {
                $file_arr = array('image1'=>'image1','image2'=>'image2');
                foreach($file_arr as $inputName=>$file_name)
                {
                //print_r($_FILES[$inputName]); exit;
                    if(!empty($_FILES[$inputName]['name']))
                    {
                        $today_date = date('Y_m_d_h_i_s');
                        $ext= $today_date.substr($_FILES[$inputName]['name'],strrpos($_FILES[$inputName]['name'],'.'),strlen($_FILES[$inputName]['name'])); ;
                        Storage::disk('srn')->put("$return_id/$file_name"."$ext", file_get_contents($_FILES[$inputName]['tmp_name']));
                        $returnArr = array();
                        $returnArr[$file_name]=$file_name."$ext"; 
                        ReturnInvoicePart::where('return_id',$return_id)->update($returnArr);
                    }
                }
                if(!DispatchInventoryParticulars::whereRaw("dispatch_id='$dispatch_id' ")->first())
                {
                    DB::commit();
                    echo 'Spare Part '.$reason.' Request Send Successfully.';exit;
                }
                else
                {
                    DB::commit();
                    echo 'Spare Part '.$reason.' Request Send Successfully.';exit;
                }
            }
            else
            {
                DB::rollback();
                echo 'Spare Part '.$reason.' Request Failed.';exit;
            }
        }
        else
        {
            DB::rollback();
            echo 'Spare Part '.$reason.' Request Failed.';exit;
        }
    }
    
    public function view_return_srn_ob()
    {
        $po_inv_qry = "SELECT tsp.*,tm.*,tsp.part_name,tsc.center_name,tsp.part_status,tsc.center_name,tm.job_no FROM `tbl_inventory_part` tsp
            INNER JOIN tagging_master tm ON tsp.tag_id = tm.tagid
            INNER JOIN tbl_service_centre tsc on tsp.center_id = tsc.center_id
            WHERE  tsp.part_po_no is not null and srn_status='1'";         
        $po_inv_arr = DB::select($po_inv_qry); 
        
        $url = $_SERVER['APP_URL'].'/view-return-sc-ob';
        return view('view-return-sc-ob')
        ->with('po_inv_arr', $po_inv_arr)        
        ->with('url', $url);
        
    }
    
    public function return_srn_part_sc(Request $request)
    {
        $part_allocate_id =  $request->input('part_id');
        
        //print_r($request->all()); exit;
        $part_det = InvPart::whereRaw("part_allocate_id='$part_allocate_id'")->first();
        $center_id = $part_det->center_id;
        $tag_id = $part_det->tag_id;
        $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
        $tag_det = TaggingMaster::whereRaw("TagId = '$tag_id' ")->first();
        
        $ReturnInvoicePart = new ReturnInvoicePart();
        $ReturnInvoicePart->invoice_id = '0';
        $ReturnInvoicePart->invoice_no = '0';
        $ReturnInvoicePart->center_id = $part_det->center_id;
        $ReturnInvoicePart->asc_name = $center_det->center_name;
        $ReturnInvoicePart->asc_code = $center_det->asc_code;
        $ReturnInvoicePart->po_date = $part_det->part_po_date;
        $ReturnInvoicePart->po_id = $part_det->part_allocate_id;
        $ReturnInvoicePart->po_no = $part_det->part_po_no;
        
        
        
        $ReturnInvoicePart->return_type = $part_det->srn_type;
        $ReturnInvoicePart->remarks = $part_det->srn_remarks;
        $ReturnInvoicePart->miss_qty = $part_det->part_allocated;
        
        
        $ReturnInvoicePart->case_type ='Job Case';
        //$ReturnInvoicePart->req_id = $dispatch_part_det->req_id;
        $ReturnInvoicePart->po_type = $part_det->po_type;
        $ReturnInvoicePart->job_id = $tag_det->TagId;
        $ReturnInvoicePart->job_no = $tag_det->job_no;
        
        $ReturnInvoicePart->brand_id = $part_det->brand_id;
        $ReturnInvoicePart->brand_name = $tag_det->Brand;
        $ReturnInvoicePart->model_id = $part_det->model_id;
        $ReturnInvoicePart->model_name = $tag_det->Model;
        $ReturnInvoicePart->spare_id = $part_det->spare_id;
        $ReturnInvoicePart->part_no = $part_det->part_no;
        $ReturnInvoicePart->part_name = $part_det->part_name;
        $ReturnInvoicePart->hsn_code = $part_det->hsn_code;
        $ReturnInvoicePart->gst = $part_det->gst;
        $ReturnInvoicePart->color = $part_det->color;
        $ReturnInvoicePart->req_qty = $part_det->part_required;
        $ReturnInvoicePart->issued_qty = $part_det->part_allocated;
        $ReturnInvoicePart->miss_qty = $part_det->part_allocated;
        $ReturnInvoicePart->approval_status = '3';
        
        $dispatch = 0;
        if($avail_qty==0)
        {
            $dispatch = 2;
        }
        $UserId = Session::get('UserId');
        DB::beginTransaction();
        if($ReturnInvoicePart->save())
        {
            if(InvPart::whereRaw("part_allocate_id='$part_allocate_id'")
                    ->update(
                            array('request_to_ho'=>'1',
                                'request_to_ho_date'=>"'".date('Y-m-d H:i:s')."'",
                                'request_to_ho_by'=>$UserId,
                                'srn_status'=>'2')))
            {
                DB::commit();
                echo '1';exit;
                
            }
            
            DB::rollback();
            echo 'Spare Part '.$reason.' Request Failed.';exit;
        }
        else
        {
            DB::rollback();
            echo 'Spare Part '.$reason.' Request Failed.';exit;
        }
    }
}

