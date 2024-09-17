<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SparePart;
use App\StateMaster;
use App\PincodeMaster;
use Auth;
use Session;
use DB;
use App\User;
use App\TaggingMaster;
use App\InventoryCenter;
use App\RegionalManagerMaster;
use App\InvPart;
use App\TagPart;
use App\BrandMaster;
use App\ProductCategoryMaster;
use App\ProductMaster;
use App\ModelMaster;
use App\JobSheet;
use App\ServiceEngineer;
use App\InwardInventoryPart;
use App\ReturnInvoicePart;
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
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
        Session::put("page-title","Fresh Stock Management");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
    
        $Center_Id = Auth::user()->table_id;

        $brand = $request->input('brand');
        $product_category = $request->input('product_category');
        $product = $request->input('product');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        
        $whereTag = "";
        
        if(!empty($brand))
        {
            $whereTag .= " and iip.brand_id='$brand'";

        }
        if(!empty($product_category) && $product_category != "All")
        {
            $whereTag .= " and iip.product_category_id='$product_category'";
        }
        if(!empty($product) && $product != "All")
        {
            $whereTag .= " and iip.product_id='$product'";
        }
        
        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and DATE(ii.inw_date) between '$from_date1' and '$to_date1'";
        }
        
        #echo  $whereTag;die;
        if(empty($whereTag))
        {

            $req_arr  =   DB::select("SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id");
        }
        else 
        {   #echo "SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id where 1=1 $whereTag";die;
            $req_arr  =   DB::select("SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id where 1=1 $whereTag");
        }
       

        $whereTag = base64_encode(http_build_query($request->all()));

        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);

        foreach($req_arr as $req)
        {
            $brand_id = $req->brand_id;
            $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
            $req->brand_name = $brand_det->brand_name;

            $product_id = $req->product_id;
            $product_det = ProductMaster::whereRaw("brand_id='$brand_id' and  product_id='$product_id'")->first();
            $req->product_name = $product_det->product_name;

            $model_id = $req->model_id;
            $model_det = ModelMaster::whereRaw("brand_id='$brand_id' and product_id='$product_id' and model_id='$model_id'")->first();
            $req->model_name = $model_det->model_name;
            
        }


        $category_master = DB::select("SELECT pm.product_category_id,pm.category_name FROM product_category_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND category_status='1'
        WHERE bm.brand_id='$brand'");



        $qry = "SELECT pm.product_id,pm.product_name FROM product_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND product_status='1'
        WHERE  pm.brand_id='$brand' and product_category_id='$product_category'";#die;
        $model_master = DB::select($qry);
        
        
        
        $url = $_SERVER['APP_URL'].'/fresh-stock';
        return view('fresh-stock-manage')
                ->with('brand_arr', $brand_arr)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('brand_id',$brand)
                ->with('req_arr', $req_arr)
                ->with('category_master', $category_master)
                ->with('model_master', $model_master)
                ->with('url', $url)
                ->with('product_category', $product_category)
                ->with('product', $product)
                ->with('job_status', $job_status)
                ->with('pincode', $pincode)
                ->with('job_no', $job_no)
                ->with('ticket_no', $ticket_no)
                ->with('back_url','se-raise-po')
                ->with('whereTag',$whereTag);
                
    }
    
    
    public function part_defective(Request $request)
    {   
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $TagId = $request->input('tagId'); 

        $taggingArr['defective']=1;
        $taggingArr['updated_by']=$UserId;
        $taggingArr['defective_date']=date('Y-m-d H:i:s');
        $tagDet = InwardInventoryPart::whereRaw("part_inw_id='$TagId'")->first();
        $part_no = $tagDet->part_no;
        //print_r($taggingArr); exit;
        
        if(InwardInventoryPart::whereRaw("part_inw_id='$TagId' and defective='0'")->update($taggingArr))
        {
            echo json_encode(array('resp_id'=>'1',"part_no"=>$part_no));exit;
        }
        else
        {
            echo json_encode(array('resp_id'=>'2',"part_no"=>""));exit;
        }
        
    }


    public function fresh_defective_stock(Request $request)
    {
        Session::put("page-title","Fresh Defective Stock Management");
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
    
        $Center_Id = Auth::user()->table_id;

        $brand = $request->input('brand');
        $product_category = $request->input('product_category');
        $product = $request->input('product');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        
        $whereTag = "";
        
        if(!empty($brand))
        {
            $whereTag .= " and iip.brand_id='$brand'";

        }
        if(!empty($product_category) && $product_category != "All")
        {
            $whereTag .= " and iip.product_category_id='$product_category'";
        }
        if(!empty($product) && $product != "All")
        {
            $whereTag .= " and iip.product_id='$product'";
        }
        
        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and DATE(ii.inw_date) between '$from_date1' and '$to_date1'";
        }
        #echo  $whereTag;die;
        if(empty($whereTag))
        {

            #$req_arr  =   DB::select("SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id");
            $req_arr = ReturnInvoicePart::whereRaw("return_type='Defective'")->get();
        }
        else 
        {   #echo "SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id where 1=1 $whereTag";die;
            #$req_arr  =   DB::select("SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id where 1=1 $whereTag");
            $req_arr = ReturnInvoicePart::whereRaw("return_type='Defective'")->get();
        }
       

        $whereTag = base64_encode(http_build_query($request->all()));

        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);



        $category_master = DB::select("SELECT pm.product_category_id,pm.category_name FROM product_category_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND category_status='1'
        WHERE bm.brand_id='$brand'");


        $qry = "SELECT pm.product_id,pm.product_name FROM product_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND product_status='1'
        WHERE  pm.brand_id='$brand' and product_category_id='$product_category'";#die;
        $model_master = DB::select($qry);

        
        $url = $_SERVER['APP_URL'].'/fresh-defective-stock';
        return view('fresh-defective-manage')
                ->with('brand_arr', $brand_arr)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('brand_id',$brand)
                ->with('req_arr', $req_arr)
                ->with('category_master', $category_master)
                ->with('model_master', $model_master)
                ->with('url', $url)
                ->with('product_category', $product_category)
                ->with('product', $product)
                ->with('job_status', $job_status)
                ->with('pincode', $pincode)
                ->with('job_no', $job_no)
                ->with('ticket_no', $ticket_no)
                ->with('back_url','se-raise-po')
                ->with('whereTag',$whereTag);
                
    }

    public function canbalize(Request $request)
    {   
        $return_id =  $request->input('return_id');
        $ReturnInvoicePart_arr = ReturnInvoicePart::whereRaw("return_id='$return_id'")->get();
        $all_canblized_Part_arr = ReturnInvoicePart::whereRaw("canbalized_status='1'")->get();?>
        <table border="1">
        <tr>
            <th>Sr No.</th>
            <th>Date of Def. Recd</th>
            <th>Name of Asc / Engineer</th>
            <th>Asc / Engineer Code</th>
            <th>Brand</th>
            <th>Product</th>
            <th>Model</th>
            <th>Part Code</th>
            <th>Job No.</th>
            <th>Defective Sr No.</th>
            <th>Action</th>
        </tr>
        
        <?php $i = 1;
            foreach($ReturnInvoicePart_arr as $req){ ?>

            <tr>
                <td><?php echo $i++; ?></td>    
                <td><?php echo date('d-m-Y',strtotime($req->po_date)); ?></td>
                <td><?php echo $req->asc_name; ?></td>
                <td><?php echo $req->asc_code; ?></td>
                <td><?php echo $req->brand_name; ?></td>
                <td><?php echo $req->model_name; ?></td>
                <td><?php echo $req->part_name; ?></td>
                <td><?php echo $req->part_no; ?></td>
                <td><?php echo $req->job_no; ?></td>
                <td><?php echo $req->po_no; ?></td>
                    
                <td>
                    <?php if($req->canbalized_status=='0') { 
                        if($req->scrap_status!='0'){?>
                        Part Already Scrap .
                        <?php } else{?>
                        <button  type="submit" onclick="return save_canbalized_part('<?php echo $req->return_id; ?>')" class="mt-2 btn btn-success">Canbalize</button>
                    <?php }} else { ?>
                        Canbalized Part.
                    <?php } ?>
                </td>
            </tr>

            <?php } ?>
    </table>

    <br/><br/><br/>
    
    <?php if(!$all_canblized_Part_arr->isEmpty()){ ?>
    <table class="table" style="font-size:12px;word-break: break-all;">
        <tr>
            <th>Sr No.</th>
            <th>Date of Def. Recd</th>
            <th>Name of Asc / Engineer</th>
            <th>Asc / Engineer Code</th>
            <th>Brand</th>
            <th>Product</th>
            <th>Model</th>
            <th>Part Code</th>
            <th>Job No.</th>
            <th>Defective Sr No.</th>
            <th>Canbalized By</th>
            <th>Canbalize Date</th>
        </tr>
        <?php $i = 1;
        foreach($all_canblized_Part_arr as $req){
            $user_arr = User::whereRaw("id ='$req->canbalized_by'")->first();
            $user_name = $user_arr->name;
                ?>

            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo date('d-m-Y',strtotime($req->po_date)); ?></td>
                <td><?php echo $req->asc_name; ?></td>
                <td><?php echo $req->asc_code; ?></td>
                <td><?php echo $req->brand_name; ?></td>
                <td><?php echo $req->model_name; ?></td>
                <td><?php echo $req->part_name; ?></td>
                <td><?php echo $req->part_no; ?></td>
                <td><?php echo $req->job_no; ?></td>
                <td><?php echo $req->po_no; ?></td>
                <td><?php echo $user_name; ?></td>
                <td><?php echo date('d-m-Y',strtotime($req->canbalized_date)) ?></td>
            </tr>

        <?php } ?>
    </table>
        <?php }
    }


    public function scrap(Request $request)
    {   
        $return_id =  $request->input('return_id');
        $ReturnInvoicePart_arr = ReturnInvoicePart::whereRaw("return_id='$return_id'")->get();
        $all_scrap_arr = ReturnInvoicePart::whereRaw("scrap_status='1'")->get();?>
        <table border="1">
            <tr>
                <th>Sr No.</th>
                <th>Date of Def. Recd</th>
                <th>Name of Asc / Engineer</th>
                <th>Asc / Engineer Code</th>
                <th>Brand</th>
                <th>Product</th>
                <th>Model</th>
                <th>Part Code</th>
                <th>Job No.</th>
                <th>Defective Sr No.</th>
                <th>Scrap Dte</th>
                <th>Action</th>
            </tr>
        
            <?php $i = 1;
            foreach($ReturnInvoicePart_arr as $req){ ?>

            <tr>
                <td><?php echo $i++; ?></td>    
                <td><?php echo date('d-m-Y',strtotime($req->po_date)); ?></td>
                <td><?php echo $req->asc_name; ?></td>
                <td><?php echo $req->asc_code; ?></td>
                <td><?php echo $req->brand_name; ?></td>
                <td><?php echo $req->model_name; ?></td>
                <td><?php echo $req->part_name; ?></td>
                <td><?php echo $req->part_no; ?></td>
                <td><?php echo $req->job_no; ?></td>
                <td><?php echo $req->po_no; ?></td>
                <td><input name="scrap_date" autocomplete="off" id="scrap_date" type="date" class="datepicker_new"></td>
                <td>
                    <?php if($req->scrap_status=='0') { 
                        if($req->canbalized_status!='0'){ ?>
                        Part Already Canbalized.
                        <?php }else{ ?>
                        <button type="submit" onclick="return save_scrap_part('<?php echo $req->return_id; ?>')" class="mt-2 btn btn-success">Scrap</button>
                    <?php }} else { ?>
                        Scrap Part.
                    <?php } ?>
                </td>
            </tr>

            <?php } ?>
        </table>

        <br/><br/><br/>
    
        <?php if(!$all_scrap_arr->isEmpty()){ ?>
        <table class="table" style="font-size:12px;word-break: break-all;">
            <tr>
                <th>Sr No.</th>
                <th>Date of Def. Recd</th>
                <th>Name of Asc / Engineer</th>
                <th>Asc / Engineer Code</th>
                <th>Brand</th>
                <th>Product</th>
                <th>Model</th>
                <th>Part Code</th>
                <th>Job No.</th>
                <th>Defective Sr No.</th>
                <th>Scrap By</th>
                <th>Scrap Date</th>
            </tr>
            <?php $i = 1;
            foreach($all_scrap_arr as $req){
                $user_arr = User::whereRaw("id ='$req->scrap_by'")->first();
                $user_name = $user_arr->name;
                ?>

                

            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo date('d-m-Y',strtotime($req->po_date)); ?></td>
                <td><?php echo $req->asc_name; ?></td>
                <td><?php echo $req->asc_code; ?></td>
                <td><?php echo $req->brand_name; ?></td>
                <td><?php echo $req->model_name; ?></td>
                <td><?php echo $req->part_name; ?></td>
                <td><?php echo $req->part_no; ?></td>
                <td><?php echo $req->job_no; ?></td>
                <td><?php echo $req->po_no; ?></td>
                <td><?php echo $user_name; ?></td>
                <td><?php echo date('d-m-Y',strtotime($req->scrap_date)) ?></td>
            </tr>

            <?php } ?>
        </table>
        <?php }
    }

    public function canbalize_save(Request $request)
    {
        $return_id =  $request->input('return_id');
        $UserId = Session::get('UserId');


        $taggingArr['canbalized_status']=1;
        $taggingArr['canbalized_by']=$UserId;
        $taggingArr['canbalized_date']=date('Y-m-d H:i:s');
        $tagDet = ReturnInvoicePart::whereRaw("return_id='$return_id'")->first();
        $part_no = $tagDet->part_no;

        if(ReturnInvoicePart::whereRaw("return_id='$return_id' and canbalized_status='0'")->update($taggingArr))
        {
            #echo json_encode(array('resp_id'=>'1',"part_no"=>$part_no));exit;
            echo $part_no." Canbalized Successfully";exit;
        }
        else
        {   
            echo "PLease Try Again!";exit;
            #echo json_encode(array('resp_id'=>'2',"part_no"=>""));exit;
        }
    }

    public function scrap_save(Request $request)
    {
        $return_id =  $request->input('return_id');
        $scrap_date =  $request->input('scrap_date');
        $UserId = Session::get('UserId');


        $taggingArr['scrap_status']=1;
        $taggingArr['scrap_by']=$UserId;
        $taggingArr['scrap_date']= $scrap_date;
        $tagDet = ReturnInvoicePart::whereRaw("return_id='$return_id'")->first();
        $part_no = $tagDet->part_no;

        if(ReturnInvoicePart::whereRaw("return_id='$return_id' and scrap_status='0'")->update($taggingArr))
        {
            #echo json_encode(array('resp_id'=>'1',"part_no"=>$part_no));exit;
            echo $part_no." Scrap Successfully";exit;
        }
        else
        {   
            echo "PLease Try Again!";exit;
            #echo json_encode(array('resp_id'=>'2',"part_no"=>""));exit;
        }
    }

    
    public function get_def_return(Request $request)
    {   
        $return_id =  $request->input('return_id');
        $ReturnInvoicePart_arr = ReturnInvoicePart::whereRaw("return_id='$return_id'")->get();
        $all_scrap_arr = ReturnInvoicePart::whereRaw("defective_status='1'")->get();?>
        <table border="1">
            <tr>
                <th>Sr No.</th>
                <th>Date of Def. Recd</th>
                <th>Brand</th>
                <th>Product</th>
                <th>Model</th>
                <th>Part Code</th>
                <th>Defective Spare Amount(Per item)</th>
                <th>No. Of Items</th>
                <th>Total Amount</th>
                <th>Grand Total</th>
                <th>Action</th>
            </tr>
        
            <?php $i = 1;
            foreach($ReturnInvoicePart_arr as $req){ ?>

            <tr>
                <td><?php echo $i++; ?></td>    
                <td><?php echo date('d-m-Y',strtotime($req->po_date)); ?></td>
                <td><?php echo $req->brand_name; ?></td>
                <td><?php echo $req->model_name; ?></td>
                <td><?php echo $req->part_name; ?></td>
                <td><?php echo $req->part_no; ?></td>
                <td><input type="text" name="def_amount_item" id="def_amount_item" onkeypress="return checkNumber(this.value,event);"></td>
                <td><?php echo $req->req_qty; ?></td>
                <td><?php echo $req->total; ?></td>
                <td><?php echo $req->grand_total; ?></td>
                <td>
                    <?php if($req->defective_status=='0') {

                        if ($req->canbalized_status != '0' || $req->scrap_status != '0') {
                            echo 'Part Already Canbalized or Scrap.';
                        } else {
                            echo '<button type="submit" onclick="return save_def_amount(\'' . $req->return_id . '\')" class="mt-2 btn btn-success">Defective Return</button>';
                        }

                        } else { ?>
                        Defective Part.
                    <?php } ?>
                </td>
            </tr>

            <?php } ?>
        </table>

        <br/><br/><br/>
    
        <?php if(!$all_scrap_arr->isEmpty()){ ?>
        <table class="table" style="font-size:12px;word-break: break-all;">
            <tr>
                <th>Sr No.</th>
                <th>Date of Def. Recd</th>
                <th>Brand</th>
                <th>Product</th>
                <th>Model</th>
                <th>Part Code</th>
                <th>Defective Spare Amount(Per item)</th>
                <th>No. Of Items</th>
                <th>Total Amount</th>
                <th>Grand Total</th>
        
            </tr>
            <?php $i = 1;
            foreach($all_scrap_arr as $req){
                $user_arr = User::whereRaw("id ='$req->scrap_by'")->first();
                $user_name = $user_arr->name;
                ?>

                

            <tr>
                <td><?php echo $i++; ?></td>    
                <td><?php echo date('d-m-Y',strtotime($req->po_date)); ?></td>
                <td><?php echo $req->brand_name; ?></td>
                <td><?php echo $req->model_name; ?></td>
                <td><?php echo $req->part_name; ?></td>
                <td><?php echo $req->part_no; ?></td>
                <td><?php echo $req->def_amount_item ; ?></td>
                <td><?php echo $req->req_qty; ?></td>
                <td><?php echo $req->total; ?></td>
                <td><?php echo $req->grand_total; ?></td>
            </tr>

            <?php } ?>
        </table>
        <?php }
    }

    
    public function defective_save(Request $request)
    {
        $return_id =  $request->input('return_id');
        $def_amount_item =  $request->input('def_amount_item');
        $UserId = Session::get('UserId');


        $taggingArr['defective_status']=1;
        $taggingArr['defective_by']=$UserId;
        $taggingArr['defective_date']= date('Y-m-d H:i:s');
        $taggingArr['def_amount_item']= $def_amount_item;
        
        $tagDet = ReturnInvoicePart::whereRaw("return_id='$return_id'")->first();
        $part_no = $tagDet->part_no;

        if(ReturnInvoicePart::whereRaw("return_id='$return_id' and scrap_status='0'")->update($taggingArr))
        {
            #echo json_encode(array('resp_id'=>'1',"part_no"=>$part_no));exit;
            echo $part_no." Defective Return Successfully";exit;
        }
        else
        {   
            echo "PLease Try Again!";exit;
            #echo json_encode(array('resp_id'=>'2',"part_no"=>""));exit;
        }
    }
    
    
    
}

