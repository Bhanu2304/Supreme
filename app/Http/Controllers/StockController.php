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
use App\TagDamagePart;
use App\RegionMaster;
use App\OutwardPending;
use App\JobBook;
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
        $spare_id = $request->input('part_code');
        $po_sr_no = $request->input('po_sr_no');
        $sr_no = $request->input('sr_no');
        
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
        if(!empty($spare_id) )
        {
            $whereTag .= " and iip.spare_id='$spare_id'";
        }
        if(!empty($po_sr_no) )
        {
            $whereTag .= " and ii.voucher_no='$po_sr_no'";
        }
        if(!empty($sr_no) )
        {
            $whereTag .= " and srno_list.srno='$sr_no'";
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

            $req_arr  =   DB::select("SELECT * FROM inward_inventory ii LEFT JOIN `tbl_inventory_item_list` srno_list ON ii.inw_id = srno_list.part_inw_id LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id");
        }
        else 
        {   //echo "SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id where 1=1 $whereTag";die;
            $req_arr  =   DB::select("SELECT * FROM inward_inventory ii LEFT JOIN `tbl_inventory_item_list` srno_list ON ii.inw_id = srno_list.part_inw_id LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id where 1=1 $whereTag");
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
                ->with('spare_id',$spare_id)
                ->with('po_sr_no',$po_sr_no)
                ->with('srno',$sr_no)
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
        $ticket_no = $request->input('ticket_no');
        $job_no = $request->input('job_no');
        $sr_no = $request->input('sr_no');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        
        $whereTag = "";
        
        if(!empty($brand))
        {
            $whereTag .= " and tm.brand_id='$brand'";
        }

        if(!empty($product_category) && $product_category != "All")
        {
            $whereTag .= " and tm.product_category_id='$product_category'";
        }
        
        if(!empty($product) && $product != "All")
        {
            $whereTag .= " and tm.product_id='$product'";
        }
        if(!empty($job_no))
        {
            $whereTag .= " and tm.job_no='$job_no'";
        }

        if(!empty($ticket_no))
        {
            $whereTag .= " and tm.ticket_no='$ticket_no'";
        }

        if(!empty($sr_no))
        {
            $whereTag .= " and tdp.part_po_no='$sr_no'";
        }
        
        
        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and DATE(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        #echo  $whereTag;die;
        if(empty($whereTag))
        {

            #$req_arr  =   DB::select("SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id");
            #$req_arr = ReturnInvoicePart::whereRaw("return_type='Defective'")->get();
            $qry = "SELECT tdp.*,brand_name,category_name,product_name,model_name,ticket_no,job_no,tdp.part_po_date,tsc.center_name,tsc.asc_code,tdp.part_po_no FROM tagging_damage_part tdp
            INNER JOIN tagging_master tm ON tdp.tag_id = tm.TagId 
            INNER JOIN tbl_service_centre tsc ON tm.center_id = tsc.center_id
            INNER JOIN brand_master bm ON tdp.brand_id = bm.brand_id
            INNER JOIN product_category_master cat ON tdp.product_category_id = cat.product_category_id
            INNER JOIN product_master pm ON tdp.product_id = pm.product_id
            INNER JOIN model_master mm ON tdp.model_id = mm.model_id
            INNER JOIN tbl_spare_parts tsp ON tdp.spare_id = tsp.spare_id
            WHERE tdp.approve='1' and date(tdp.created_at)= curdate()";
            $req_arr           =   DB::select($qry);
        }
        else 
        {   
            #echo "SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id where 1=1 $whereTag";die;
            #$req_arr  =   DB::select("SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id where 1=1 $whereTag");
            #$req_arr = ReturnInvoicePart::whereRaw("return_type='Defective'")->get();
            
            $req_arr           =   DB::select("SELECT tdp.*,brand_name,category_name,product_name,model_name,ticket_no,job_no,tdp.part_po_date,tsc.center_name,tsc.asc_code FROM tagging_damage_part tdp
            INNER JOIN tagging_master tm ON tdp.tag_id = tm.TagId 
            INNER JOIN tbl_service_centre tsc ON tm.center_id = tsc.center_id
            INNER JOIN brand_master bm ON tdp.brand_id = bm.brand_id
            INNER JOIN product_category_master cat ON tdp.product_category_id = cat.product_category_id
            INNER JOIN product_master pm ON tdp.product_id = pm.product_id
            INNER JOIN model_master mm ON tdp.model_id = mm.model_id
            INNER JOIN tbl_spare_parts tsp ON tdp.spare_id = tsp.spare_id
            WHERE  tdp.approve='1'  $whereTag");

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
                ->with('sr_no', $sr_no)
                ->with('ticket_no', $ticket_no)
                ->with('back_url','se-raise-po')
                ->with('whereTag',$whereTag);
                
    }

    public function canbalize(Request $request)
    {   
        $return_id =  $request->input('return_id');

        $ReturnInvoicePart_arr = DB::select("select tdp.*,tsc.center_name,tsc.asc_code,bm.brand_name,mm.model_name,tm.job_no from tagging_damage_part tdp 
        INNER JOIN tagging_master tm ON tdp.tag_id = tm.TagId 
        INNER JOIN tbl_service_centre tsc ON tdp.center_id = tsc.center_id 
        INNER JOIN brand_master bm ON tdp.brand_id = bm.brand_id
        INNER JOIN model_master mm ON tdp.model_id = mm.model_id
          where tdp.dpart_id='$return_id'");
        $all_canblized_Part_arr = DB::select("select tdp.*,tsc.center_name,tsc.asc_code,bm.brand_name,mm.model_name,tm.job_no from tagging_damage_part tdp
        INNER JOIN tagging_master tm ON tdp.tag_id = tm.TagId 
        INNER JOIN tbl_service_centre tsc ON tdp.center_id = tsc.center_id
        INNER JOIN brand_master bm ON tdp.brand_id = bm.brand_id
        INNER JOIN model_master mm ON tdp.model_id = mm.model_id  where tdp.canbalized_status='1'");

        #$ReturnInvoicePart_arr = ReturnInvoicePart::whereRaw("return_id='$return_id'")->get();
        #$all_canblized_Part_arr = ReturnInvoicePart::whereRaw("canbalized_status='1'")->get();?>
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
                <td><?php echo date('d-m-Y',strtotime($req->part_po_date)); ?></td>
                <td><?php echo $req->center_name; ?></td>
                <td><?php echo $req->asc_code; ?></td>
                <td><?php echo $req->brand_name; ?></td>
                <td><?php echo $req->model_name; ?></td>
                <td><?php echo $req->part_name; ?></td>
                <td><?php echo $req->part_no; ?></td>
                <td><?php echo $req->job_no; ?></td>
                <td><?php echo $req->part_po_no; ?></td>
                    
                <td>
                    <?php if($req->canbalized_status=='0') { 
                        if($req->scrap_status!='0'){?>
                        Part Already Scrap .
                        <?php } else{?>
                        <button  type="submit" onclick="return save_canbalized_part('<?php echo $req->dpart_id; ?>')" class="mt-2 btn btn-success">Canbalize</button>
                    <?php }} else { ?>
                        Canbalized Part.
                    <?php } ?>
                </td>
            </tr>

            <?php } ?>
    </table>

    <br/><br/><br/>
    
    <?php if(!empty($all_canblized_Part_arr)){ ?>
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
                <td><?php echo date('d-m-Y',strtotime($req->part_po_date)); ?></td>
                <td><?php echo $req->center_name; ?></td>
                <td><?php echo $req->asc_code; ?></td>
                <td><?php echo $req->brand_name; ?></td>
                <td><?php echo $req->model_name; ?></td>
                <td><?php echo $req->part_name; ?></td>
                <td><?php echo $req->part_no; ?></td>
                <td><?php echo $req->job_no; ?></td>
                <td><?php echo $req->part_po_no; ?></td>
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
        #$ReturnInvoicePart_arr = ReturnInvoicePart::whereRaw("return_id='$return_id'")->get();
        #$all_scrap_arr = ReturnInvoicePart::whereRaw("scrap_status='1'")->get();
        $ReturnInvoicePart_arr = DB::select("select tdp.*,tsc.center_name,tsc.asc_code,bm.brand_name,mm.model_name,tm.job_no from tagging_damage_part tdp 
        INNER JOIN tagging_master tm ON tdp.tag_id = tm.TagId 
        INNER JOIN tbl_service_centre tsc ON tdp.center_id = tsc.center_id 
        INNER JOIN brand_master bm ON tdp.brand_id = bm.brand_id
        INNER JOIN model_master mm ON tdp.model_id = mm.model_id
          where tdp.dpart_id='$return_id'");
        $all_scrap_arr = DB::select("select tdp.*,tsc.center_name,tsc.asc_code,bm.brand_name,mm.model_name,tm.job_no from tagging_damage_part tdp
        INNER JOIN tagging_master tm ON tdp.tag_id = tm.TagId 
        INNER JOIN tbl_service_centre tsc ON tdp.center_id = tsc.center_id
        INNER JOIN brand_master bm ON tdp.brand_id = bm.brand_id
        INNER JOIN model_master mm ON tdp.model_id = mm.model_id  where tdp.scrap_status='1'");
        
        ?>
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
                <td><?php echo date('d-m-Y',strtotime($req->part_po_date)); ?></td>
                <td><?php echo $req->center_name; ?></td>
                <td><?php echo $req->asc_code; ?></td>
                <td><?php echo $req->brand_name; ?></td>
                <td><?php echo $req->model_name; ?></td>
                <td><?php echo $req->part_name; ?></td>
                <td><?php echo $req->part_no; ?></td>
                <td><?php echo $req->job_no; ?></td>
                <td><?php echo $req->part_po_no; ?></td>
                <td><input name="scrap_date" autocomplete="off" id="scrap_date" type="date" class="datepicker_new"></td>
                <td>
                    <?php if($req->scrap_status=='0') { 
                        if($req->canbalized_status!='0'){ ?>
                        Part Already Canbalized.
                        <?php }else{ ?>
                        <button type="submit" onclick="return save_scrap_part('<?php echo $req->dpart_id; ?>')" class="mt-2 btn btn-success">Scrap</button>
                    <?php }} else { ?>
                        Scrap Part.
                    <?php } ?>
                </td>
            </tr>

            <?php } ?>
        </table>

        <br/><br/><br/>
    
        <?php if(!empty($all_scrap_arr)){ ?>
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
                <td><?php echo date('d-m-Y',strtotime($req->part_po_date)); ?></td>
                <td><?php echo $req->center_name; ?></td>
                <td><?php echo $req->asc_code; ?></td>
                <td><?php echo $req->brand_name; ?></td>
                <td><?php echo $req->model_name; ?></td>
                <td><?php echo $req->part_name; ?></td>
                <td><?php echo $req->part_no; ?></td>
                <td><?php echo $req->job_no; ?></td>
                <td><?php echo $req->part_po_no; ?></td>
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
        $tagDet = TagDamagePart::whereRaw("dpart_id='$return_id'")->first();
        $part_no = $tagDet->part_no;

        if(TagDamagePart::whereRaw("dpart_id='$return_id' and canbalized_status='0'")->update($taggingArr))
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
        $tagDet = TagDamagePart::whereRaw("dpart_id='$return_id'")->first();
        $part_no = $tagDet->part_no;

        if(TagDamagePart::whereRaw("dpart_id='$return_id' and scrap_status='0'")->update($taggingArr))
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
        #$ReturnInvoicePart_arr = ReturnInvoicePart::whereRaw("return_id='$return_id'")->get();
        #$all_scrap_arr = ReturnInvoicePart::whereRaw("defective_status='1'")->get();
        
        $ReturnInvoicePart_arr = DB::select("select tdp.*,tsc.center_name,tsc.asc_code,bm.brand_name,mm.model_name,tm.job_no from tagging_damage_part tdp 
        INNER JOIN tagging_master tm ON tdp.tag_id = tm.TagId 
        INNER JOIN tbl_service_centre tsc ON tdp.center_id = tsc.center_id 
        INNER JOIN brand_master bm ON tdp.brand_id = bm.brand_id
        INNER JOIN model_master mm ON tdp.model_id = mm.model_id
          where tdp.dpart_id='$return_id'");
        $all_scrap_arr = DB::select("select tdp.*,tsc.center_name,tsc.asc_code,bm.brand_name,mm.model_name,tm.job_no from tagging_damage_part tdp
        INNER JOIN tagging_master tm ON tdp.tag_id = tm.TagId 
        INNER JOIN tbl_service_centre tsc ON tdp.center_id = tsc.center_id
        INNER JOIN brand_master bm ON tdp.brand_id = bm.brand_id
        INNER JOIN model_master mm ON tdp.model_id = mm.model_id  where tdp.defective_status='1'");
        
        ?>
        <table border="1">
            <tr>
                <th>Sr No.</th>
                <th>Date of Def. Recd</th>
                <th>Brand</th>
                <th>Product</th>
                <th>Model</th>
                <th>Part Code</th>
                <th>Defective Spare Amount(Per item)</th>
                <!-- <th>No. Of Items</th> -->
                <th>Total Amount</th>
                <th>Grand Total</th>
                <th>Action</th>
            </tr>
        
            <?php $i = 1;
            foreach($ReturnInvoicePart_arr as $req){ ?>

            <tr>
                <td><?php echo $i++; ?></td>    
                <td><?php echo date('d-m-Y',strtotime($req->part_po_date)); ?></td>
                <td><?php echo $req->brand_name; ?></td>
                <td><?php echo $req->model_name; ?></td>
                <td><?php echo $req->part_name; ?></td>
                <td><?php echo $req->part_no; ?></td>
                <td><input type="text" name="def_amount_item" id="def_amount_item" onkeypress="return checkNumber(this.value,event);"></td>
                <!-- <td><?php //echo $req->req_qty; ?></td> -->
                <td><?php echo $req->total; ?></td>
                <td><?php echo $req->total; ?></td>
                <td>
                    <?php if($req->defective_status=='0') {

                        if ($req->canbalized_status != '0' || $req->scrap_status != '0') {
                            echo 'Part Already Canbalized or Scrap.';
                        } else {
                            echo '<button type="submit" onclick="return save_def_amount(\'' . $req->dpart_id . '\')" class="mt-2 btn btn-success">Defective Return</button>';
                        }

                        } else { ?>
                        Defective Part.
                    <?php } ?>
                </td>
            </tr>

            <?php } ?>
        </table>

        <br/><br/><br/>
    
        <?php if(!empty($all_scrap_arr)){ ?>
        <table class="table" style="font-size:12px;word-break: break-all;">
            <tr>
                <th>Sr No.</th>
                <th>Date of Def. Recd</th>
                <th>Brand</th>
                <th>Product</th>
                <th>Model</th>
                <th>Part Code</th>
                <th>Defective Spare Amount(Per item)</th>
                <!-- <th>No. Of Items</th> -->
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
                <td><?php echo date('d-m-Y',strtotime($req->part_po_date)); ?></td>
                <td><?php echo $req->brand_name; ?></td>
                <td><?php echo $req->model_name; ?></td>
                <td><?php echo $req->part_name; ?></td>
                <td><?php echo $req->part_no; ?></td>
                <td><?php echo $req->def_amount_item ; ?></td>
                <!-- <td><?php //echo $req->req_qty; ?></td> -->
                <td><?php echo $req->total; ?></td>
                <td><?php echo $req->total; ?></td>
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
        
        $tagDet = TagDamagePart::whereRaw("dpart_id='$return_id'")->first();
        $part_no = $tagDet->part_no;

        if(TagDamagePart::whereRaw("dpart_id='$return_id' and scrap_status='0'")->update($taggingArr))
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


    public function asc_stock(Request $request)
    {
        Session::put("page-title","Asc Stock Management");
        
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
            $whereTag .= " and ii.brand_id='$brand'";

        }
        if(!empty($product_category) && $product_category != "All")
        {
            $whereTag .= " and tbl.product_category_id='$product_category'";
        }
        if(!empty($product) && $product != "All")
        {
            $whereTag .= " and ii.product_id='$product'";
        }
        
        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and DATE(ii.created_at) between '$from_date1' and '$to_date1'";
        }
        
        #echo  $whereTag;die;
        if(empty($whereTag))
        {

            $req_arr  =   DB::select("SELECT * FROM outward_inventory_pending ii LEFT JOIN outward_inventory_dispatch iip ON ii.invoice_id = iip.invoice_id where defective_status='0' and date(ii.created_at)= curdate()");
        }
        else 
        {   #echo "SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id where 1=1 $whereTag";die;
            
            $req_arr  =   DB::select("SELECT ii.* FROM outward_inventory_pending ii INNER JOIN tbl_spare_parts tbl ON ii.spare_id = tbl.spare_id LEFT JOIN outward_inventory_dispatch iip ON ii.invoice_id = iip.invoice_id where defective_status='0'  $whereTag");
        }
       

        $whereTag = base64_encode(http_build_query($request->all()));

        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);

        foreach($req_arr as $req)
        {
            
            $part_det = SparePart::whereRaw("spare_id='$spare_id'")->first();
            #$req->part_name = $part_det->part_name;
            $brand_id = $part_det->brand_id;
            $product_id = $part_det->product_id;
            $product_det = ProductMaster::whereRaw("brand_id='$brand_id' and  product_id='$product_id'")->first();
            $req->product_name = $product_det->product_name;
            
            $req->hsn_code = $product_det->hsn_code;
            


            
        }


        $category_master = DB::select("SELECT pm.product_category_id,pm.category_name FROM product_category_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND category_status='1'
        WHERE bm.brand_id='$brand'");



        $qry = "SELECT pm.product_id,pm.product_name FROM product_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND product_status='1'
        WHERE  pm.brand_id='$brand' and product_category_id='$product_category'";#die;
        $model_master = DB::select($qry);

        $center_qry = "SELECT * FROM tbl_service_centre  WHERE sc_status='1' order by center_name"; 
        $asc_master           =   DB::select($center_qry); 
        
        
        #print_r($req_arr);die;
        $url = $_SERVER['APP_URL'].'/asc-stock';
        return view('asc-stock-manage')
        ->with('brand_arr', $brand_arr)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('brand_id',$brand)
                ->with('asc_master',$asc_master)
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

    public function part_asc_stock(Request $request)
    {
        $invoice_id =  $request->input('invoice_id');
        $remakrs =  $request->input('remakrs');
        #print_r($request->all());die;

        $taggingArr['defective_status']=1;
        $taggingArr['asc_remarks']=$remakrs;

        
        $tagDet = OutwardPending::whereRaw("invoice_id='$invoice_id'")->first();
        $part_no = $tagDet->po_no;

        if(OutwardPending::whereRaw("invoice_id='$invoice_id'")->update($taggingArr))
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

    public function job_book_settlement(Request $request)
    {
        Session::put("page-title","Job Book Settlement");

        $center_id = $request->input('center_id');

        $whereTag = "";
        
        if(!empty($center_id) && $center_id != "All")
        {
            $whereTag .= " and tsc.center_id='$center_id'";

        }

        $data_qry = "SELECT bm.brand_name,rm.region_name,sm.state_name,tsc.center_name,tsc.city,tsc.asc_code,tj.remarks,tj.created_at FROM tbl_jobbook tj 
        INNER JOIN `region_master` rm ON tj.region_id= rm.region_id
        INNER JOIN `brand_master` bm ON tj.brand_id= bm.brand_id
        INNER JOIN `state_master` sm ON tj.state= sm.state_id
        INNER JOIN `tbl_service_centre` tsc ON tj.center_id= tsc.center_id where 1=1 $whereTag"; 
        $req_arr           =   DB::select($data_qry); 

        $center_qry = "SELECT * FROM tbl_service_centre  WHERE sc_status='1' order by center_name"; 
        $asc_master           =   DB::select($center_qry); 

        $url = $_SERVER['APP_URL'].'/job-book-settlement';
        return view('job-book-settlement')->with('asc_master',$asc_master)->with('req_arr',$req_arr)->with('center_id',$center_id);
    }

    public function issue_job_book(Request $request)
    {
        Session::put("page-title","Issue Job Book");

        if($request->isMethod('post'))
        {
            $data = $request->all();
            #print_r($data);die;
            $UserId = Session::get('UserId');
            
            $jobbook_arr            =   new JobBook();
            $jobbook_arr->brand_id = $data['Brand'];
            $jobbook_arr->region_id = $data['region_id'];
            $jobbook_arr->state = $data['state_id'];
            $jobbook_arr->center_id = $data['center_id'];
            $jobbook_arr->remarks = $data['remarks'];
            $jobbook_arr->created_by = $UserId;
            $jobbook_arr->created_at = date('Y-m-d H:i:s');
            $jobbook_arr->save();

            Session::flash('message',"Job Book Genrated Successfully");
            Session::flash('alert-class', 'alert-success');

            return redirect('job-book-settlement');
        }

        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get();
        $brand_arr = json_decode($brand_json,true);

        $region_json  =   RegionMaster::orderByRaw('region_name ASC')->get();
        $region_master = json_decode($region_json,true);

        $center_qry = "SELECT * FROM tbl_service_centre  WHERE sc_status='1' order by center_name"; 
        $asc_master           =   DB::select($center_qry);

        $url = $_SERVER['APP_URL'].'/issue-job-book';
        return view('issue-job-book')
                ->with('asc_master',$asc_master)
                    ->with('brand_arr', $brand_arr)
                        ->with('region_master',$region_master);
    }


    public function get_state_by_region(Request $request)
    {

        $region_id = $request->input('region_id');
    
        $qry_state = "select * from state_master where region_id='$region_id'";
        $state_master           =   DB::select($qry_state);
        
        if(empty($state_master))
        {
            echo '<option value="">No State Name</option>'; exit;
        }
        echo '<option value="">Select</option>'; 
        
        foreach($state_master as $state)
        {
            echo '<option value="';
            echo $state->state_id.'">';
            echo $state->state_name;
            echo '</option>';
        }
        exit;
    }

    public function get_asc_name_by_state(Request $request)
    {
        $state_id = $request->input('state_id');
        
        $str = "";
        if($state_id!='All')
        {
            $str = " and tsc.state_id='$state_id'";
        }

        $qr2 = "SELECT * FROM tbl_service_centre where  state='$state_id' order by center_name"; 
        $asc_master           =   DB::select($qr2);
        
        
        if(empty($asc_master))
        {
            echo '<option value="">No Asc Name</option>'; exit;
        }
        echo '<option value="">Select</option>'; 
        
        foreach($asc_master as $asc)
        {
            echo '<option value="';
            echo $asc->center_id.'">';
            echo $asc->center_name .' - ';
            echo $asc->asc_code;
            echo '</option>';
        }
        exit;
    }


    
    
    
    
}

