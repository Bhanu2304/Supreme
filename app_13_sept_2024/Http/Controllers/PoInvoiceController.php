<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\BrandMaster;
use App\SparePart;
use App\OutwardInventoryPart;
use App\OutwardInvoice;
use App\OutwardInvoicePart;

use App\ModelMaster;
use App\ServiceCenter;
use App\DispatchInventory;
use App\DispatchInventoryParticulars;
use App\DispatchInvPart;
use App\InwardInventoryPart;
use App\SCRequestInventoryPart;
use App\SCRequestInventory;
use DB;
use Auth;
use Session;


class PoInvoiceController extends Controller
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
        Session::put("page-title","Invoice Management");
        /*$po_job_arr =   DB::select("SELECT * FROM tagging_master WHERE part_status='1' ");
        $po_sc_arr =   DB::select("SELECT * FROM `sc_request_inventory` WHERE part_status_pending ='1'");
        
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 

        $brand_master = array();
        
        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }*/
        
        $invoice_no = $request->input('invoice_no');
        $brand_id = $request->input('brand_id');
        $product_category = $request->input('product_category');
        $product = $request->input('product');
        $po_type = $request->input('po_type');
        $center_id = $request->input('asc_name');
        $job_number = $request->input('job_number');
        $ticket_number = $request->input('ticket_number');
        
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $whereTag = "";

        if(!empty($invoice_no))
        {   
            $whereTag .= " and oidp.invoice_no = '$invoice_no' ";
        }

        if(!empty($brand_id))
        {   
            $whereTag .= " and oidp.brand_id = '$brand_id' ";
        }

        if(!empty($product_category))
        {   
            $whereTag .= " and tm.product_category_id = '$product_category' ";
        }

        if(!empty($product))
        {   
            $whereTag .= " and tm.product_id = '$product' ";
        }

        if(!empty($po_type))
        {   
            $whereTag .= " and oidp.po_type = '$po_type' ";
        }

        

        if(!empty($center_id) && $center_id != "All")
        {   
            $whereTag .= " and oidp.center_id = '$center_id' ";
        }

        if(!empty($job_number))
        {   
            $whereTag .= " and tm.job_no = '$job_number' ";
        }

        if(!empty($ticket_number))
        {   
            $whereTag .= " and tm.ticket_no = '$ticket_number' ";
        }


        $UserType = Session::get('UserType');
        $whereTag1 = ""; 
        $whereTag2 = ""; 
        if($UserType=='ServiceCenter')
        {
            $center_id = Auth::user()->table_id; 
            $whereTag1 .= " and oidp.center_id = '$center_id' ";
            $whereTag2 .= " and tsc.center_id = '$center_id' ";
        }

        

        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(oidp.created_at) between '$from_date1' and '$to_date1'";
        }
        
        $data_arr = DB::select("SELECT * FROM `outward_inventory` where reject='0' and invoice_created='0'");

        if(empty($whereTag))
        {
            $invoice_arr = DB::select("SELECT tm.*,oidp.invoice_no,oidp.po_date,oidp.po_no,oidp.po_type,oidp.asc_name,oidp.invoice_id,oidp.asc_code,oidp.brand_name,oidp.model_name,oidp.part_no,oidp.part_name,oidp.color,oidp.hsn_code,
            oidp.gst_amount,oidp.asc_amount,oidp.req_qty,oidp.issued_qty,oidp.discount,oidp.remarks FROM  outward_inventory_invoice oidp  
            INNER JOIN tagging_master tm ON  oidp.job_id = tm.tagid WHERE  1=1  and date(oidp.created_at)= curdate() $whereTag1 ");

            $invoice_arr2 = DB::select("SELECT tm.*,oidp.invoice_no,oidp.po_date,oidp.po_no,oidp.po_type,oidp.asc_name,oidp.invoice_id,oidp.asc_code,oidp.brand_name,oidp.model_name,oidp.part_no,oidp.part_name,oidp.color,oidp.hsn_code,
            oidp.gst_amount,oidp.asc_amount,oidp.req_qty,oidp.issued_qty,oidp.discount,oidp.remarks FROM  outward_inventory_invoice oidp  
            INNER JOIN tagging_master tm ON  oidp.job_id = tm.tagid WHERE  1=1  and date(oidp.created_at)= curdate() $whereTag1 ");

        }else{

            $invoice_arr = DB::select("SELECT tm.*,oidp.invoice_no,oidp.invoice_id,oidp.po_date,oidp.po_no,oidp.po_type,oidp.asc_name,oidp.asc_code,oidp.brand_name,oidp.model_name,oidp.part_no,oidp.part_name,oidp.color,oidp.hsn_code,
            oidp.gst_amount,oidp.asc_amount,oidp.req_qty,oidp.issued_qty,oidp.discount,oidp.remarks FROM  outward_inventory_invoice oidp  
            INNER JOIN tagging_master tm ON  oidp.job_id = tm.tagid WHERE  1=1  $whereTag $whereTag1");

            $invoice_arr2 = DB::select("SELECT tm.*,oidp.invoice_no,oidp.invoice_id,oidp.po_date,oidp.po_no,oidp.po_type,oidp.asc_name,oidp.asc_code,oidp.brand_name,oidp.model_name,oidp.part_no,oidp.part_name,oidp.color,oidp.hsn_code,
            oidp.gst_amount,oidp.asc_amount,oidp.req_qty,oidp.issued_qty,oidp.discount,oidp.remarks FROM  outward_inventory_invoice oidp  
            INNER JOIN tagging_master tm ON  oidp.job_id = tm.tagid WHERE  1=1  $whereTag $whereTag1");

        }

        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);

        $qr2 = "SELECT tsc.center_id,center_name,asc_code FROM tbl_service_centre  tsc
            INNER JOIN users us ON tsc.email_id = us.email
            WHERE sc_status='1' $whereTag2 order by center_name"; 
        $asc_master           =   DB::select($qr2); 

        $category_master = DB::select("SELECT pm.product_category_id,pm.category_name FROM product_category_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND category_status='1'
        WHERE bm.brand_id='$brand_id'");

        $qry = "SELECT pm.product_id,pm.product_name FROM product_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND product_status='1'
        WHERE  pm.brand_id='$brand_id' and product_category_id='$product_category'";#die;
        $model_master = DB::select($qry);
        
        $url = $_SERVER['APP_URL'].'/po-invoice';
        return view('po-invoice')
        ->with('data_arr', $data_arr)
        ->with('invoice_arr', $invoice_arr)    
        ->with('invoice_arr2', $invoice_arr2)    
        ->with('brand_arr', $brand_arr)
        ->with('asc_master', $asc_master)
        ->with('invoice_no', $invoice_no)
        ->with('brand_id', $brand_id)
        ->with('product_category', $product_category)
        ->with('category_master', $category_master)
        ->with('model_master', $model_master)
        ->with('product', $product)
        ->with('po_type', $po_type)
        ->with('job_number', $job_number)
        ->with('ticket_number', $ticket_number)
        ->with('from_date',$from_date)
        ->with('to_date',$to_date)
        ->with('url', $url);
    }
    
    public function get_invoice_no($sr_no,$po_type)
    {
        $new_request_no = "";
        if(empty($sr_no))
        {
            $sr_no = 1;
            $new_request_no = "$po_type".'000001';
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
            $new_request_no = "$po_type".$new_no;
        }
        
        $out_srno_det = OutwardInvoice::whereRaw("invoice_no='$new_request_no'")->first();
        //print_r($out_srno_det); exit;
        if(!empty($out_srno_det))
        {
            return $this->get_invoice_no($no,$po_type);
        }
        else
        {
            return array('new_request_no'=>$new_request_no,'sr_no'=>$no);
        }
    }
    public function create_invoice(Request $request)
    {
        Session::put("page-title","Invoice Creation");
        
        $out_id = $request->input('out_id'); 
        //print_r($chk_inv); exit;
        if(empty($out_id))
        {
            echo 'Please Select PO To create Invoice';exit;
        }
        
        $msg = "1";
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        
        $po_arr = DB::select("SELECT * FROM `outward_inventory` WHERE out_id = '$out_id' and invoice_created='0' LIMIT 1 "); 
        if(count($po_arr))
        {    
            foreach($po_arr as $po)
            {
                $issued_qty = $po->issued_qty;
                $asc_amount = $po->asc_amount;
                $discount = $po->discount;
                $gst = $po->gst;
                $total_bill = $asc_amount*$issued_qty;
                $discount_amount = round($total_bill*$discount/100,2);
                $net_bill   = round($total_bill-$discount_amount,2);
                $gst_amount = round($net_bill*$gst/100,2);
                $grand_total = round($net_bill+$gst_amount,2);

                $column_arr = array('out_id','out_no','po_date','case_type','req_id','po_id','po_no','po_type','job_id','job_no','center_id','asc_name','asc_code','brand_id','brand_name',
            'model_id','model_name','model_id','spare_id','part_no','part_name','hsn_code',
            'gst','color','req_qty','issued_qty','asc_amount','customer_amount','discount','remarks');
                $record = new OutwardInvoice();
                $DispatchInventoryParticulars = new DispatchInventoryParticulars();
                
                foreach($column_arr as $col)
                {
                    $record->$col =  addslashes($po->$col);
                    $DispatchInventoryParticulars->$col =  addslashes($po->$col);
                }

                $DispatchInventoryParticulars->dispatch_qty = $po->issued_qty;
                $DispatchInventoryParticulars->total = $record->total = round($total_bill,2);
                $DispatchInventoryParticulars->discount_amount =$record->discount_amount = round($discount_amount,2);
                $DispatchInventoryParticulars->net_bill =$record->net_bill = round($net_bill,2);
                $DispatchInventoryParticulars->gst_amount =$record->gst_amount = round($gst_amount,2);
                $DispatchInventoryParticulars->grand_total =$record->grand_total = round($grand_total,2);
                $DispatchInventoryParticulars->created_by =$record->created_by = $created_by;
                $DispatchInventoryParticulars->created_at =$record->created_at = $created_at;
                $po_type = $po->po_type;
                DB::beginTransaction();
                
                $sr_no_arr = DB::select("SELECT max(sr_no) srno FROM `outward_inventory_invoice` where po_type='$po_type'");
                DB::raw('LOCK TABLES outward_inventory_invoice WRITE');
                
                //print_r($sr_no_arr); exit;
                $sr_no = 0;
                foreach($sr_no_arr as $sr_det)
                {
                    $sr_no = $sr_det->srno;
                }
                $sr_arr = $this->get_invoice_no($sr_no,$po_type);
                //print_r($sr_arr);exit;
                $new_request_no = $sr_arr['new_request_no'];
                //echo $new_request_no;exit;
                
                $DispatchInventoryParticulars->invoice_no = $record->invoice_no = $sr_arr['new_request_no'];
                $DispatchInventoryParticulars->sr_no =$record->sr_no = $$sr_arr['sr_no'];
                $center_id = $po->center_id;
                $sc_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
                $sc_name = $sc_det->center_name;
                $asc_code = $sc_det->asc_code;

                if($record->save())
                {
                    $invoice_id = $record->id;
                    $DispatchInventoryParticulars->invoice_id = $invoice_id;
                    if(OutwardInventoryPart::whereRaw("out_id='$out_id'")->update(
                            array('invoice_created'=>'1',
                                'invoice_id'=>"$invoice_id",
                                'invoice_no'=>"$new_request_no",
                                'invoice_date'=>"$created_at",
                                'invoice_by'=>"$created_by")))
                    {
                        $DispatchInventory =  new DispatchInventory();
                        $DispatchInventory->invoice_id = $invoice_id;
                        $DispatchInventory->invoice_no = $new_request_no;
                        $DispatchInventory->center_id = $center_id;
                        $DispatchInventory->asc_name = $sc_name;
                        $DispatchInventory->asc_code = $asc_code;
                        //$DispatchInvPart->created_at = $created_at;
                        //$DispatchInvPart->created_by = $created_by;
                        
                        if($DispatchInventory->save())
                        {
                            $dispatch_id = $DispatchInventory->id;
                            $DispatchInventoryParticulars->dispatch_id = $dispatch_id;
                            if($DispatchInventoryParticulars->save())
                            {
                                DB::raw('UNLOCK TABLES');
                                DB::commit();
                                echo "Invoice Generated - $sc_name - <b>$new_request_no</b> For {$po->po_no}<br>";exit;
                            }
                            else
                            {
                                DB::raw('UNLOCK TABLES');
                                DB::rollback();
                                echo "Invoice Not Generated - $sc_name - <b>$new_request_no</b> For {$po->po_no}<br>";exit;
                            }
                        }
                        else
                        {
                            DB::raw('UNLOCK TABLES');
                            DB::rollback();
                            echo "Invoice Not Generated - $sc_name - <b>$new_request_no</b> For {$po->po_no}<br>";exit;
                        }
                        
                        
                    }
                    else
                    {
                        DB::raw('UNLOCK TABLES');
                        DB::rollback();
                        echo 'PO Not Found';exit;
                    }
                }
                else
                {
                    DB::raw('UNLOCK TABLES');
                    DB::rollback();
                }
            }
        }
        else
        {
            $po_arr = DB::select("SELECT * FROM `outward_inventory` WHERE out_id = '$out_id' and invoice_created='1' LIMIT 1 ");
            if(count($po_arr))
            {
                foreach($po_arr as $po){echo "Invoice No.<b> {$po->invoice_no}</b> Already Generated For {$po->po_no} <br>";exit;} 
            }
            else
            {
                echo 'Please Select PO To make Invoice.<br>';exit;
            }
            
        }
        exit;
        
    }
    
    public function create_invoice_multiple(Request $request)
    {
        Session::put("page-title","Invoice Creation");
        
        $out_str = $request->input('out_str'); 
        //print_r($chk_inv); exit;
        if(empty($out_str))
        {
            echo 'Please Select PO To create Invoice';exit;
        }
        
        $msg = "";
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        
        $out_arr = explode(',',$out_str);
        $center_arr = array(); $po_type_arr = array();
        $part_arr = array();
        foreach($out_arr as $out_id)
        {
            $outward_det = OutwardInvoice::whereRaw("out_id = '$out_id'")->first();
            if(!empty($outward_det->center_id))
            {
                $po_type = $outward_det->po_type;
                $po_type_arr[] = $po_type;
                $center_arr[] = $outward_det->center_id;
                $part_arr[$po_type][$outward_det->center_id][] = $outward_det;
            }
        }
        
        
        
        
        /*
        $invoice_no_arr = array();
        foreach($center_arr as $po_type=>$center)
        {
            $sr_no_arr = DB::select("SELECT max(sr_no) srno FROM `outward_inventory_invoice` where po_type='$po_type'");
        }*/
        
        //$po_arr = DB::select("SELECT * FROM `outward_inventory` WHERE out_id = '$out_id' and invoice_created='0' LIMIT 1 "); 
        
        
        
        if(count($part_arr))
        {    
            foreach($part_arr as $po_type=>$center_part_arr)
            {
                foreach($center_part_arr as $center_id=>$po_arr)
                {
                   //print_r($po_arr); exit;//echo $po_type;exit;
                    DB::beginTransaction();
                    DB::raw('LOCK TABLES outward_inventory_invoice WRITE');
                    $sr_no_arr = DB::select("SELECT max(sr_no) srno FROM `outward_inventory_invoice` where po_type='$po_type'");
                    //print_r($sr_no_arr); exit;
                    $sr_no = 0;
                    foreach($sr_no_arr as $sr_det)
                    {
                        $sr_no = $sr_det->srno;
                    }
                    $sr_arr = $this->get_invoice_no($sr_no,$po_type);
                    $new_request_no = $sr_arr['new_request_no'];
                    $sr_no = $sr_arr['sr_no'];
                    
                    $total_bill_all = 0;
                    $discount_amount_all = 0;
                    $net_bill_all = 0;
                    $gst_amount_all = 0;
                    $grand_total_all = 0;
                    $out_id_arr = array();
                    
                    $record_arr = array();
                    $po_n_arr = array();
                    
                    foreach($po_arr as $po)
                    {
                        $issued_qty = $po->issued_qty;
                        $asc_amount = $po->asc_amount;
                        $discount = $po->discount;
                        $gst = $po->gst;
                        $total_bill = $asc_amount*$issued_qty;
                        $discount_amount = 0;
                        if(!empty($discount))
                        {
                            $discount_amount = round($total_bill*$discount/100,2);
                        }
                        $net_bill   = round($total_bill-$discount_amount,2);
                        $gst_amount = round($net_bill*$gst/100,2);
                        $grand_total = round($net_bill+$gst_amount,2);
                        
                        $total_bill_all += $total_bill;
                        $discount_amount_all += $discount_amount;
                        $net_bill_all += $net_bill;
                        $gst_amount_all += $gst_amount;
                        $grand_total_all += $grand_total;

                        $column_arr = array('out_id','out_no','po_date','case_type','req_id','po_id','po_no','po_type','job_id','job_no','center_id','asc_name','asc_code','brand_id','brand_name',
                    'model_id','model_name','model_id','spare_id','part_no','part_name','hsn_code',
                    'gst','color','req_qty','issued_qty','asc_amount','customer_amount','discount','remarks');
                        $record = array();

                        foreach($column_arr as $col)
                        {
                            $record[$col] =  addslashes($po->$col);
                        }

                        $po_n_arr[$po->po_no]['po_no'] = addslashes($po->po_no);
                        $po_n_arr[$po->po_no]['po_date'] = addslashes($po->po_date);
                        $record['total'] = round($total_bill,2);
                        $record['discount_amount'] = round($discount_amount,2);
                        $record['net_bill'] = round($net_bill,2);
                        $record['gst_amount'] = round($gst_amount,2);
                        $record['grand_total'] = round($grand_total,2);
                        $record['created_by'] = $created_by;
                        $record['created_at'] = $created_at;
                        
                        //echo $new_request_no;exit;
                        
                        $record['invoice_no'] = $new_request_no;
                        $record['sr_no'] = $sr_no;
                        $center_id = $po->center_id;
                        $sc_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
                        $sc_name = $sc_det->center_name;
                        $record_arr[] = $record; 
                        $out_id_arr[] = $po->out_id;
                        
                    }
                    
                    $OutwardInvoice  = new OutwardInvoice();
                    $OutwardInvoice->sr_no = $sr_no;
                    $OutwardInvoice->invoice_no = $new_request_no;
                    $OutwardInvoice->po_type = $po_type;
                    $OutwardInvoice->po_no = json_encode($po_n_arr);
                    $OutwardInvoice->center_id = $center_id;
                    $sc_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
                    $OutwardInvoice->asc_name = $sc_det->center_name;
                    $OutwardInvoice->asc_code = $sc_det->asc_code;
                    $OutwardInvoice->total = round($total_bill,2);
                    $OutwardInvoice->discount_amount = round($discount_amount,2);
                    $OutwardInvoice->net_bill = round($net_bill,2);
                    $OutwardInvoice->gst_amount = round($gst_amount,2);
                    $OutwardInvoice->grand_total = round($grand_total,2);
                    $OutwardInvoice->created_by = $created_by;
                    $OutwardInvoice->created_at = $created_at;
                    
                    if($OutwardInvoice->save())
                    {
                        $invoice_id = $OutwardInvoice->id;
                        $record_master = array();
                        
                        foreach($record_arr as $record)
                        {
                            $record['invoice_id'] = $invoice_id;
                            $record_master[] = $record;
                        }
                        if(OutwardInvoicePart::insert($record_master))
                        {
                            $out_id_str = implode(",",$out_id_arr);
                            if(OutwardInventoryPart::whereRaw("out_id in ($out_id_str)")->update(
                                array('invoice_created'=>'1',
                                    'invoice_id'=>"$invoice_id",
                                    'invoice_no'=>"$new_request_no",
                                    'invoice_date'=>"$created_at",
                                    'invoice_by'=>"$created_by")))
                            {
                                DB::raw('UNLOCK TABLES');
                                DB::commit();
                                $msg .= "Invoice Generated - $sc_name - <b>$new_request_no</b> For {$po->po_no}<br>";
                            }
                            else
                            {
                                DB::raw('UNLOCK TABLES');
                                DB::rollback();
                                $msg .=  'PO Not Found';
                            }
                        }
                        else
                        {
                            DB::raw('UNLOCK TABLES');
                                DB::rollback();
                                $msg .=  'Spare Part Details Not Found. Please Try Again Later.';
                        }
                            
                    }
                    else
                    {
                        DB::raw('UNLOCK TABLES');
                                DB::rollback();
                                $msg .=  'PO Details Not Updated. Please Try Again Later.';
                    }
                }
            }
            
            echo $msg; exit;
        }
        else
        {
            $po_arr = DB::select("SELECT * FROM `outward_inventory` WHERE out_id = '$out_id' and invoice_created='1' LIMIT 1 ");
            if(count($po_arr))
            {
                foreach($po_arr as $po){echo "Invoice No.<b> {$po->invoice_no}</b> Already Generated For {$po->po_no} <br>";exit;} 
            }
            else
            {
                echo 'Please Select PO To make Invoice.<br>';exit;
            }
            
        }
        exit;
        
    }
    
}

