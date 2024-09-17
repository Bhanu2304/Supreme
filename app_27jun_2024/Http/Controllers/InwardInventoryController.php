<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\BrandMaster;
use App\SparePart;
use App\InwardNo;
use App\Inventory;
use App\ModelMaster;
use App\InwardInventoryPart;
use App\InwardInventory;
use App\InventoryItemList;
use DB;
use Auth;
use Session;


class InwardInventoryController extends Controller
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
        Session::put("page-title","Inward Stock");
        $part_arr =   DB::select("SELECT spare_id,part_name FROM `tbl_spare_parts` WHERE part_status='1' ");
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);
        
        
        $req_arr           =   DB::select("SELECT * FROM `inward_inventory` inw ");        
                
                
        $url = $_SERVER['APP_URL'].'/inward-inv-entry';
        return view('inw-inv-entry')
        ->with('brand_arr', $brand_arr)
        ->with('part_arr', $part_arr)
        ->with('req_arr', $req_arr)
        ->with('url', $url);
    }
    
    public function save_req_inv(Request $request)
    {
        #print_r($request->all());die;
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $spare_part =  $request->input('sparepart');
        $Itemlist =  $request->input('Itemlist');
        $supplier_name = addslashes($request->input('supplier_name'));
        $voucher_no = addslashes($request->input('voucher_no'));
        $invoice_date = addslashes($request->input('invoice_date'));
        $no_of_case = addslashes($request->input('no_of_case'));
        $veh_doc_no = addslashes($request->input('veh_doc_no'));
        $qty = '';
        $total = '';
        //$brand = $request->input('brand');
        //$part_name =  $spare_part['part_name'];
        //$remarks = addslashes($request->input('remarks'));
         
        //print_r($spare_part); exit;
        
        
        $po_order = array(); $index = 0;
        DB::beginTransaction();
        foreach($spare_part as $spare_id=>$part)
        {
            if(empty($part['brand']))
            {
                Session::flash('message', "Please Select Brand");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['model']))
            {
                Session::flash('message', "Please Select Model");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['part_code']))
            {
                Session::flash('message', "Please Select Part Code");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['part_name']))
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
            else if(empty($part['hsn_code']))
            {
                Session::flash('message', "HSN Code should not be empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['gst']))
            {
                Session::flash('message', "GST should not be empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['item_qty']))
            {
                Session::flash('message', "Item Quantity should not be empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['bin_no']))
            {
                Session::flash('message', "Please fill Bin or Raw No.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['purchase_amt']))
            {
                Session::flash('message', "Please fill Purchase Amount");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['asc_amt']))
            {
                Session::flash('message', "Please fill ASC Amount");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['cust_amt']))
            {
                Session::flash('message', "Please fill Customer Amount");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }

            $brand_id = $part['brand'];
            $model_id = $part['model'];
            $part_no = $part['part_code'];
            $SparePart = SparePart::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$part_no'")->first();
            $spare_id = $SparePart->spare_id;
            
            $po_order[$index]['supplier_name'] = addslashes($supplier_name);
            $po_order[$index]['voucher_no'] =  addslashes($voucher_no);
            $po_order[$index]['invoice_date'] =  $invoice_date;
            $po_order[$index]['no_of_case'] =  addslashes($no_of_case);
            $po_order[$index]['veh_doc_no'] =  addslashes($veh_doc_no);
            $po_order[$index]['brand_id'] =  $SparePart->brand_id;
            $po_order[$index]['product_category_id'] =  $SparePart->product_category_id;
            $po_order[$index]['product_id'] = $SparePart->product_id;
            $po_order[$index]['model_id'] =  $SparePart->model_id;
            $po_order[$index]['spare_id'] = $spare_id;
            $po_order[$index]['part_name'] = addslashes($SparePart->part_name);
            $po_order[$index]['part_no'] = $SparePart->part_no;
            $po_order[$index]['item_color'] = addslashes($part['color']);
            $po_order[$index]['hsn_code'] = addslashes($part['hsn_code']);
            $po_order[$index]['gst'] = $part['gst'];
            $po_order[$index]['item_qty'] = $part['item_qty'];
            $po_order[$index]['bin_no'] = addslashes($part['bin_no']);
            $po_order[$index]['purchase_amt'] = $part['purchase_amt'];
            $po_order[$index]['asc_amount'] = $part['asc_amt'];
            $po_order[$index]['customer_amount'] = $part['cust_amt'];
            $po_order[$index]['remarks'] = addslashes($part['remarks']);
            $po_order[$index]['created_at'] = $created_at;
            $po_order[$index]['created_by'] = $created_by;
            $qty +=  $part['item_qty'];
            //$total += ($part['req_qty']*$part['po_amt']);
            $index++;
            
            
        } 
        
        if(empty($po_order))
        {
            Session::flash('error', "Please Fill All Details.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
            $RequestInventory = new InwardInventory();
            
           // $RequestInventory->center_id= $Center_Id;
            $RequestInventory->supplier_name= $supplier_name;
            $RequestInventory->voucher_no= $voucher_no;
            $RequestInventory->invoice_date= $invoice_date;
            $RequestInventory->no_of_case= $no_of_case;
            $RequestInventory->veh_doc_no= $veh_doc_no;
            $RequestInventory->part_added= count($po_order);
            $RequestInventory->qty= $qty;
            
            $RequestInventory->created_at= $created_at;
            $RequestInventory->created_by= $created_by;
            
            $month = date('M'); 
            //$month = 'jan';
            $request_no_date = '';
            if(strtolower($month)=='jan' || strtolower($month)=='feb' || strtolower($month)=='mar')
            {
                $currentYear = date('Y');
                $lastYear = $currentYear-1;
                $request_no_date = "$lastYear-".substr($currentYear,-2);    
            }
            else
            {
                $currentYear = date('Y');
                $NextYear = $currentYear+1;
                $request_no_date = "$currentYear-".substr($NextYear,-2); 
            }
            $find_max_request_no=InwardNo::selectRaw('request_no')->whereRaw("request_date='$request_no_date'")->first();

            $new_request_no = "";
            if(empty($find_max_request_no))
            {
                $request_entry_arr = new InwardNo();
                $request_entry_arr->request_date = $request_no_date;
                $request_entry_arr->request_no = '1';
                $request_entry_arr->save();

                $new_request_no = "Sup/$request_no_date/".'0001';
            }
            else
            {
                $str_no = "0000";
                $no = $find_max_request_no->request_no;
                $no = $no+1;
                $len = strlen($str_no);
                $newlen = strlen("$no");
                $new_no = substr_replace($str_no, $no, $len-$newlen,$newlen);
                $new_request_no = "Sup/$request_no_date/".$new_no;

                InwardNo::whereRaw("request_date='$request_no_date'")->update(array('request_no'=>$no));
            }
            
            //echo $new_request_no; exit;
                $RequestInventory->inw_no= $new_request_no;
                $RequestInventory->inw_date= date('Y-m-d');
                
            
            if($RequestInventory->save())
            {
                $inw_id = $RequestInventory->id;
                
                $request_arr = array();
                foreach($po_order as $index=>$part)
                {
                    $part['inw_id'] = $inw_id;
                    $part['inw_ser_no'] = $new_request_no;
                    $request_arr[] = $part;
                }

                $request_arr_item = array();
                #print_r($item_list);
                foreach($item_list as $index)
                {
                    #echo $index;
                    $item['part_inw_id'] = $inw_id;
                    $item['inw_po_no'] = $new_request_no;
                    $item['srno']=$index['sr_no'];
                    $request_arr_item[] = $item;
                }

                $InventoryItemList = new InventoryItemList();
                $InventoryItemList->insert($request_arr_item);
        
                $RequestInventoryPart = new InwardInventoryPart();
                if($RequestInventoryPart->insert($request_arr))
                {
                    
                    foreach($request_arr as $part)
                    {
                        $spare_id = $part['spare_id'];
                        $stock_qty = $part['item_qty'];
                        $spare_det_exist = Inventory::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$spare_id' ")->first();
                        if($spare_det_exist)
                        {
                            $main_inv = array();
                            $old_qty = $spare_det_exist->stock_qty;
                            $bal_qty = $spare_det_exist->bal_qty;
                            if(empty($old_qty))
                            {
                                $old_qty = 0;
                            }

                            if(empty($bal_qty))
                            {
                                $bal_qty = 0;
                            }

                            $main_inv['stock_qty']=$stock_qty+$old_qty;
                            $main_inv['bal_qty']=$stock_qty+$bal_qty;
                            $main_inv['hsn_code']=$part['hsn_code'];
                            $main_inv['landing_cost']=$part['purchase_amt'];
                            $main_inv['asc_amount']=$part['asc_amount'];
                            $main_inv['customer_price']=$part['customer_amount'];
                            //$main_inv['discount']=$discount;

                            if(Inventory::whereRaw("inv_id='{$spare_det_exist->inv_id}'")->update($main_inv))
                            {
                                
                            }
                            else
                            {
                                DB::rollback();
                            }
                        }
                        else
                        {
                            $inv            =   new Inventory();
                            $inv->brand_id=$brand_id;
                            $inv->product_category_id=$SparePart->product_category_id;
                            $inv->product_id=$SparePart->product_id;
                            $inv->model_id=$model_id;
                            $inv->spare_id=$spare_id;
                            $inv->part_name=addslashes($SparePart->part_name);
                            $inv->part_no=addslashes($SparePart->part_no);
                            $inv->hsn_code=$part['hsn_code'];
                            $inv->stock_qty=$stock_qty;
                            $inv->bal_qty=$stock_qty;
                            $inv->landing_cost=$part['purchase_amt'];
                            $inv->asc_amount=$part['asc_amount'];
                            $inv->customer_price=$part['customer_amount'];;
                            //$inv->discount=$discount;
                            if($inv->save())
                            {
                                
                            }
                            else
                            {
                                DB::rollback();
                            }
                        }
                    }
                    
                  

                    DB::commit();
                    Session::flash('message', "Stock Inward $new_request_no Saved Successfully.");
                    Session::flash('alert-class', 'alert-success');
                }
                else
                {
                    Session::flash('error', "Stock Inward Saved Failed. Please Try Again");
                    Session::flash('alert-class', 'alert-danger');
                    return back();
                }
            }
            //exit;
            return redirect('inward-inv-entry');           
    }
    
    
    public function view(Request $request)
    {
        Session::put("page-title","View Inward Stock");
        $inw_id     = base64_decode($request->input('inw_id'));  
        $inw_det  = InwardInventory::where("inw_id",$inw_id)->first();
        $data_part_arr  = InwardInventoryPart::where("inw_id",$inw_id)->get();
        
        $brand_arr   = DB::select("SELECT brand_id,brand_name FROM brand_master ");
        $brand_master = array();
        foreach($brand_arr as $brand)
        {
            //print_r($brand); exit;
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }
        //print_r($brand_master); exit;
        $data_part = array();
        foreach($data_part_arr as $part)
        {
            $model_id = $part->model_id;
            $model_det = ModelMaster::whereRaw("model_id='$model_id'")->first();
            //print_r($part); exit;
            $part->model = $model_det->model_name;
            $part->brand = $brand_master[$part->brand_id];
            $data_part[] = $part;
        }
        
        $url = $_SERVER['APP_URL'].'/inward-inv-entry';
        return view('view-inw-inv-entry')
        ->with('inw_det',$inw_det)
        ->with('data_part_arr', $data_part)
        ->with('brand_master', $brand_master)        
        ->with('url', $url);
    }
    
    public function edit(Request $request)
    {
        Session::put("page-title","Edit Inward Stock");
        
        $inw_id     = base64_decode($request->input('inw_id'));  
        $inw_det  = InwardInventory::where("inw_id",$inw_id)->first();
        $data_part_arr  = InwardInventoryPart::where("inw_id",$inw_id)->get();
        
        $brand_arr   = DB::select("SELECT brand_id,brand_name FROM brand_master ");
        
        
        $data_part = array();
        foreach($data_part_arr as $part)
        {
            $brand_id = $part->brand_id;
            $model_id = $part->model_id;
            $model_arr = ModelMaster::whereRaw("brand_id='$brand_id' ")->get();
            $part->model_master = $model_arr;
            $parts_arr = SparePart::whereRaw("brand_id='$brand_id' and model_id='$model_id'")->get();
            $part->part_master = $parts_arr;
            $data_part[] = $part;
        }
        
       //print_r($record); exit;
        $url = $_SERVER['APP_URL'].'/inward-inv-entry';
        return view('edit-inw-entry')
                ->with('inw_det',$inw_det)
        ->with('data_part_arr', $data_part)
        ->with('brand_arr', $brand_arr)        
        ->with('url', $url);
                
    }
    
    
    public function update(Request $request)
    {
        $updated_by     =   Auth::User()->id;
        $updated_at     =   date('Y-m-d H:i:s');
        $inw_id = $request->input('inw_id');
        
        $created_by     =   Auth::User()->id;
        $created_at     =   date('Y-m-d H:i:s');
        //$Center_Id = Auth::user()->table_id;
        $spare_part =  $request->input('sparepart');
        $supplier_name = addslashes($request->input('supplier_name'));
        $voucher_no = addslashes($request->input('voucher_no'));
        $invoice_date = addslashes($request->input('invoice_date'));
        $no_of_case = addslashes($request->input('no_of_case'));
        $veh_doc_no = addslashes($request->input('veh_doc_no'));
        $qty = '';
        $total = '';
        //$brand = $request->input('brand');
        //$part_name =  $spare_part['part_name'];
        //$remarks = addslashes($request->input('remarks'));
         
        //print_r($spare_part); exit;
        
        
        $po_order = array(); $index = 0;
        foreach($spare_part as $spare_id=>$part)
        {
            if(empty($part['brand']))
            {
                Session::flash('message', "Please Select Brand");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['model']))
            {
                Session::flash('message', "Please Select Model");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['part_code']))
            {
                Session::flash('message', "Please Select Part Code");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['part_name']))
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
            else if(empty($part['hsn_code']))
            {
                Session::flash('message', "HSN Code should not be empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['gst']))
            {
                Session::flash('message', "GST should not be empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['item_qty']))
            {
                Session::flash('message', "Item Quantity should not be empty.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['bin_no']))
            {
                Session::flash('message', "Please fill Bin or Raw No.");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['purchase_amt']))
            {
                Session::flash('message', "Please fill Purchase Amount");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['asc_amt']))
            {
                Session::flash('message', "Please fill ASC Amount");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }
            else if(empty($part['cust_amt']))
            {
                Session::flash('message', "Please fill Customer Amount");
                Session::flash('alert-class', 'alert-danger');
                return back();
            }

            $brand_id = $part['brand'];
            $model_id = $part['model'];
            $part_no = $part['part_code'];
            $SparePart = SparePart::whereRaw("brand_id='$brand_id' and model_id='$model_id' and spare_id='$part_no'")->first();
            //print_r($SparePart); exit;
            
            $po_order[$index]['supplier_name'] = addslashes( $supplier_name);
            $po_order[$index]['voucher_no'] =  addslashes($voucher_no);
            $po_order[$index]['invoice_date'] =  $invoice_date;
            $po_order[$index]['no_of_case'] =  addslashes($no_of_case);
            $po_order[$index]['veh_doc_no'] =  addslashes($veh_doc_no);
            $po_order[$index]['brand_id'] =  $SparePart->brand_id;
            $po_order[$index]['product_category_id'] =  $SparePart->product_category_id;
            $po_order[$index]['product_id'] = $SparePart->product_id;
            $po_order[$index]['model_id'] =  $SparePart->model_id;
            $po_order[$index]['spare_id'] = $part_no;
            $po_order[$index]['part_name'] = addslashes($SparePart->part_name);
            $po_order[$index]['part_no'] = $SparePart->part_no;
            $po_order[$index]['item_color'] = addslashes($part['color']);
            $po_order[$index]['hsn_code'] = addslashes($part['hsn_code']);
            $po_order[$index]['gst'] = $part['gst'];
            $po_order[$index]['item_qty'] = $part['item_qty'];
            $po_order[$index]['bin_no'] = addslashes($part['bin_no']);
            $po_order[$index]['purchase_amt'] = $part['purchase_amt'];
            $po_order[$index]['asc_amount'] = $part['asc_amt'];
            $po_order[$index]['customer_amount'] = $part['cust_amt'];
            $po_order[$index]['remarks'] = addslashes($part['remarks']);
            $po_order[$index]['created_at'] = $created_at;
            $po_order[$index]['created_by'] = $created_by;
            $qty +=  $part['item_qty'];
            //$total += ($part['req_qty']*$part['po_amt']);
            $index++;
        } 
        
        if(empty($po_order))
        {
            Session::flash('error', "Please Fill All Details.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        $RequestInventory = array();
            
        $RequestInventory['supplier_name']= $supplier_name;
        $RequestInventory['voucher_no']= $voucher_no;
        $RequestInventory['invoice_date']= $invoice_date;
        $RequestInventory['no_of_case']= $no_of_case;
        $RequestInventory['veh_doc_no']= $veh_doc_no;
        $RequestInventory['part_added']= count($po_order);
        $RequestInventory['qty']= $qty;
        $RequestInventory['updated_at']= $created_at;
        $RequestInventory['updated_by']= $created_by;

        if(InwardInventory::whereRaw("inw_id='$inw_id'")->update($RequestInventory))
        {       
            $request_arr = array();
            foreach($po_order as $index=>$part)
            {
                $part['inw_id'] = $inw_id;
                $part['inw_ser_no'] = $new_request_no;
                $request_arr[] = $part;
            }

            $RequestInventoryPart = new InwardInventoryPart();
            if(InwardInventoryPart::whereRaw("inw_id='$inw_id'")->delete())
            {
                if($RequestInventoryPart->insert($request_arr))
                {
                    Session::flash('message', "Stock Inward Updated Successfully.");
                    Session::flash('alert-class', 'alert-success');
                }
                else
                {
                    Session::flash('error', "Stock Inward Update Failed. Please Try Again");
                    Session::flash('alert-class', 'alert-danger');
                    return back();
                }
            }
            
        }
            //exit;
            return redirect('inward-inv-entry');           
    
            
    }
    
    
    
    
    public function add_inw_part(Request $request)
    {  
      $row_no =   $request->input('row_no');
     $brand_id = $request->input('brand_id');
     $model_id = $request->input('model_id');
     $part_code = $request->input('part_code');
     $part_name = $request->input('part_name');
     $color = $request->input('color');
     $hsn_code = $request->input('hsn_code');
     $gst = $request->input('gst');
     $item_qty = $request->input('item_qty');
     $bin_no = $request->input('bin_no');
     $purchase_amt = $request->input('purchase_amt');
     $asc_amt = $request->input('asc_amt');
     $cust_amt = $request->input('cust_amt');
     $remarks = $request->input('remarks');
    
     $row_no = (int)$row_no+1;
     $a = rand(10,10000);
     
     
     $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
     $brand_arr = json_decode($brand_json,true);
     
     $model_master = DB::select("SELECT mm.model_id,mm.model_name FROM model_master mm 
       WHERE mm.model_status='1' and model_id='$model_id' ");
     
     $part_master = DB::select("SELECT spare_id,part_no FROM `tbl_spare_parts` WHERE brand_id='$brand_id' and model_id='$model_id'  AND part_status='1'");
     
     $part_det = SparePart::whereRaw("spare_id='$part_code'")->first();
        
        
     
    ?>
        <tr id="row<?php echo $a; ?>">
                <th id="sr<?php echo $a; ?>"><?php echo $row_no;?></th>
                <td>
                    <select  required="" id="brand<?php echo $a;?>" name="sparepart[<?php echo $a;?>][brand]"  onchange="get_model('<?php echo $a;?>',this.value)">
                        <option value="">Select</option>
                        <?php foreach($brand_arr as $brand){?>       
                            <option value="<?php echo $brand['brand_id']; ?>" <?php if($brand['brand_id']==$brand_id) { echo 'selected';} ?>><?php echo $brand['brand_name']; ?></option>     
                        <?php }?>
                    </select>
                </td>
                <td>
                    <select  required="" id="model<?php echo $a;?>" name="sparepart[<?php echo $a;?>][model]"  onchange="get_partcode('<?php echo $a;?>',this.value)">
                        <option value="">Select</option>
                        <?php foreach($model_master as $model)
                            {
                                echo '<option value="';
                                echo $model->model_id.'" '; if($model->model_id==$model_id) { echo 'selected';}
                                echo '>'.$model->model_name.'</option>';
                            }?>
                    </select>
                </td>
                <td>
                    <select  required="" id="part_code<?php echo $a;?>" name="sparepart[<?php echo $a;?>][part_code]"  onchange="get_part_name('<?php echo $a;?>',this.value)">
                        <option value="">Select</option>
                        <?php foreach($part_master as $part)
                        {
                            echo '<option value="';
                            echo $part->spare_id.'" '; if($part->spare_id==$part_code) { echo 'selected';}
                            echo ">".$part->part_no.'</option>';
                        } ?>
                    </select> 
                </td>
                <td><input type="text" required="" id="part_name<?php echo $a;?>" name="sparepart[<?php echo $a;?>][part_name]" value="<?php echo $part_det->part_name; ?>" placeholder="Part Name" ></td>
                <td><input type="text" required="" id="color<?php echo $a;?>" name="sparepart[<?php echo $a;?>][color]" value="<?php echo $color; ?>" placeholder="Color" ></td>
                <td><input type="text" required="" id="hsn_code<?php echo $a;?>" name="sparepart[<?php echo $a;?>][hsn_code]" value="<?php echo $hsn_code; ?>" placeholder="HSN" ></td>
                <td><input type="text" required="" id="gst<?php echo $a;?>" name="sparepart[<?php echo $a;?>][gst]" value="<?php echo $gst; ?>" placeholder="GST No." ></td>
                <td><input type="text" required="" onkeypress="return checkNumber(this.value,event)" id="item_qty<?php echo $a;?>" name="sparepart[<?php echo $a;?>][item_qty]" value="<?php echo $item_qty; ?>" placeholder="Qty." ></td>
                <td><input type="text" required=""  id="bin_no<?php echo $a;?>" name="sparepart[<?php echo $a;?>][bin_no]" value="<?php echo $bin_no; ?>" placeholder="Rack No." ></td>
                <td><input type="text" required="" onkeypress="return checkNumber(this.value,event)" id="purchase_amt<?php echo $a;?>" name="sparepart[<?php echo $a;?>][purchase_amt]" value="<?php echo $purchase_amt; ?>" placeholder="Amount" ></td>
                <td><input type="text" required="" onkeypress="return checkNumber(this.value,event)" id="asc_amt<?php echo $a;?>" name="sparepart[<?php echo $a;?>][asc_amt]" value="<?php echo $asc_amt; ?>" placeholder="Amount" ></td>
                <td><input type="text" required="" onkeypress="return checkNumber(this.value,event)" id="cust_amt<?php echo $a;?>" name="sparepart[<?php echo $a;?>][cust_amt]" value="<?php echo $cust_amt; ?>" placeholder="Amount" ></td>
                <td><input type="text" id="remarks<?php echo $a;?>" name="sparepart[<?php echo $a;?>][remarks]" value="<?php echo $remarks; ?>" placeholder="Remarks" ></td>
                <td><button type="button" onclick="remove_row('<?php echo $a;?>','<?php echo $row_no;?>')"  value="Delete">Delete</button></td>
    </tr>
    <tr>
        <th colspan="14" style="text-align: center;">Add SrNo.</th>
    </tr>
    <?php for ($i = 0; $i < $item_qty; $i++) {?>
    
    <tr>
        <td></td>
        <td>
            <?php foreach($brand_arr as $brand){     
                if($brand['brand_id']==$brand_id) { echo $brand['brand_name'];}   
            }?>
        </td>
        <td>
            <?php foreach($model_master as $model){
                if($model->model_id==$model_id) { echo $model->model_name;}
            }?>
        </td>
        <td>
            <?php foreach($part_master as $part){
                if($part->spare_id==$part_code) { echo $part->part_no;}
            } ?>
        </td>
        <td><input type="text" name='Itemlist[<?php echo $i;?>][sr_no]' placeholder="Sr No." /></td>
       
    </tr>
    <?php }


    exit; }
    
}

