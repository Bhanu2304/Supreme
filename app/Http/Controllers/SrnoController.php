<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SparePart;
use App\ProductMaster;
use App\ModelMaster;
use App\BrandMaster;
use App\ProductCategoryMaster;
use DB;
use Auth;
use Session;


class SrnoController extends Controller
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
        Session::put("page-title","Spare Parts");
        $part_arr           =   DB::select("select bm.brand_name,category_name,pm.product_name,mm.model_name,sp.spare_id,sp.part_name,sp.part_no,sp.hsn_code,
            sp.landing_cost,sp.customer_price,sp.discount,sp.part_rate,sp.part_tax,sp.part_status 
            from `tbl_spare_parts` sp 
inner join brand_master bm on sp.brand_id = bm.brand_id
inner join product_category_master pcm on sp.product_category_id = pcm.product_category_id
inner join product_master pm on sp.product_id = pm.product_id and sp.product_category_id = pm.product_category_id and sp.brand_id = pm.brand_id
inner join model_master mm on sp.model_id = mm.model_id and sp.product_id = mm.product_id and sp.product_category_id = mm.product_category_id and sp.brand_id = mm.brand_id
where brand_status='1' and category_status='1' and product_status='1' and model_status='1'
order by brand_name,category_name,product_name,model_name"); 
        $brand_json           =   BrandMaster::whereRaw(" brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);
        $url = $_SERVER['APP_URL'].'/add-part';
        
        return view('add-part')
        ->with('url', $url)
                ->with('brand_arr', $brand_arr)
                ->with('part_arr', $part_arr);
    }
    
   
    
    public function get_srno_by_part_code(Request $request)
    {
        //$country_id = $request->input('country_id');
//        $brand_id = $request->input('brand_id');
//        $product_category_id = $request->input('product_category_id');
//        $product_id = $request->input('product_id');
//        $model_id = $request->input('model_id');
        $spare_id = $request->input('spare_id');
        
        $filter_qry = "";
//        if($brand_id!='All')
//        {
//            $filter_qry .= " and brand_id='$brand_id'";
//        }
//        if($product_category_id!='All')
//        {
//            $filter_qry .= " and product_category_id='$product_category_id'";
//        }
//        if($product_id!='All')
//        {
//            $filter_qry .= " and product_id='$product_id'";
//        }
//        if($model_id!='All')
//        {
//            $filter_qry .= " and model_id='$model_id'";
//        }
        if($spare_id!='All')
        {
            $filter_qry .= " and part_id='$spare_id'";
        }
        
        
        
        $srno_master = DB::select("SELECT * FROM `tbl_inventory_item_list` WHERE is_out='0' $filter_qry ");
        
        if(empty($srno_master))
        {
            echo 'No SrNo Found.'; exit;
        }
        $select_option = "<option value=''>Select</option>";
        foreach($srno_master as $srno)
        {
            $select_option .= '<option value="'.$srno->id.'">'.$srno->srno.'</option>';
        }
        echo $select_option;
        exit;
    }
    
    public function get_part_name_by_part_code_select(Request $request)
    {
        //$country_id = $request->input('country_id');
        $brand_id = $request->input('brand_id');
        $product_category_id = $request->input('product_category_id');
        $product_id = $request->input('product_id');
        $model_id = $request->input('model_id');
        $part_code = $request->input('part_code');
        
        $filter_qry = "";
        if($brand_id!='All')
        {
            $filter_qry .= " and brand_id='$brand_id'";
        }
        if($product_category_id!='All')
        {
            $filter_qry .= " and product_category_id='$product_category_id'";
        }
        if($product_id!='All')
        {
            $filter_qry .= " and product_id='$product_id'";
        }
        if($model_id!='All')
        {
            $filter_qry .= " and model_id='$model_id'";
        }
        if($part_code!='All')
        {
            $filter_qry .= " and spare_id='$part_code'";
        }
        
        
        
        $part_master = DB::select("SELECT part_name FROM `tbl_spare_parts` WHERE 1=1 $filter_qry  and part_status='1' limit 1");
        
        if(empty($part_master))
        {
            echo 'No Spare Part Found'; exit;
        }
        foreach($part_master as $model)
        {
             echo '<option value="'.$model->part_name.'">'.$model->part_name.'</option>';
        }
        exit;
    }
    
    public function get_part_code_by_part_name_select(Request $request)
    {
        //$country_id = $request->input('country_id');
        $brand_id = $request->input('brand_id');
        $product_category_id = $request->input('product_category_id');
        $product_id = $request->input('product_id');
        $model_id = $request->input('model_id');
        $spare_id = $request->input('part_name');
        
        $filter_qry = "";
        if($brand_id!='All')
        {
            $filter_qry .= " and brand_id='$brand_id'";
        }
        if($product_category_id!='All' && $product_category_id!='')
        {
            $filter_qry .= " and product_category_id='$product_category_id'";
        }
        if($product_id!='All')
        {
            $filter_qry .= " and product_id='$product_id'";
        }
        if($model_id!='All')
        {
            $filter_qry .= " and model_id='$model_id'";
        }
        if($spare_id!='All')
        {
            $filter_qry .= " and spare_id='$spare_id'";
        }

        #echo $model_qr = "SELECT part_no FROM `tbl_spare_parts` WHERE 1=1 $filter_qry  and part_status='1' limit 1";die;
        
        $qr = "SELECT part_no FROM `tbl_spare_parts` WHERE 1=1 $filter_qry  and part_status='1' limit 1";
        
        $part_master = DB::select($qr);
        
        if(empty($part_master))
        {
            echo 'No Spare Part Found'; exit;
        }
        foreach($part_master as $model)
        {
             echo '<option value="'.$model->part_no.'">'.$model->part_no.'</option>';
        }
        exit;
    }
    
    public function get_part_name_by_model(Request $request)
    {
        //$country_id = $request->input('country_id');
        $brand_id = $request->input('brand_id');
        $product_category_id = $request->input('product_category_id');
        $product_id = $request->input('product_id');
        $model_id = $request->input('model_id');
        $part_code = $request->input('part_code');
        
        $filter_qry = "";
        if($brand_id!='All')
        {
            $filter_qry .= " and brand_id='$brand_id'";
        }
        if($product_category_id!='All')
        {
            $filter_qry .= " and product_category_id='$product_category_id'";
        }
        if($product_id!='All')
        {
            $filter_qry .= " and product_id='$product_id'";
        }
        if($model_id!='All')
        {
            $filter_qry .= " and model_id='$model_id'";
        }
        if($part_code!='All' && $part_code != "")
        {
            $filter_qry .= " and part_no='$part_code'";
        }
        
        $part_master = DB::select("SELECT part_name FROM `tbl_spare_parts` WHERE 1=1 $filter_qry  and part_status='1'");
        
        if(empty($part_master))
        {
            echo '<option value="">No Spare Part Found</option>'; exit;
        }
        
        
        
        echo '<option value="">Select</option>';
        foreach($part_master as $model)
        {
            echo '<option value="';
                echo $model->part_name.'">';
                echo $model->part_name.'</option>';
        }
        exit;
    }
    
    public function get_partno_by_part_name(Request $request)
    {
        //$country_id = $request->input('country_id');
        //$part_name = $request->input('part_name');
        $brand_id = $request->input('brand_id');
        $product_category_id = $request->input('product_category_id');
        $product_id = $request->input('product_id');
        $model_id = $request->input('model_id');
        $div_id = $request->input('div_id');
        
        $filter_qry = "";
        if($brand_id!='All')
        {
            $filter_qry .= " and brand_id='$brand_id'";
        }
        if($product_category_id!='All')
        {
            $filter_qry .= " and product_category_id='$product_category_id'";
        }
        if($product_id!='All')
        {
            $filter_qry .= " and product_id='$product_id'";
        }
        if($model_id!='All')
        {
            $filter_qry .= " and model_id='$model_id'";
        }
        #echo "SELECT spare_id,part_no FROM `tbl_spare_parts` WHERE 1=1 $filter_qry  AND part_status='1'";die;
        $qry =  "SELECT spare_id,part_no,part_name FROM `tbl_spare_parts` WHERE 1=1 $filter_qry  AND part_status='1'";
        $part_master = DB::select($qry);
        //echo $qry;exit;
        
        if(empty($part_master))
        {
            echo '<option value="">No Part Code Found</option>'; exit;
        }
        
        echo '<option value="">Part Code</option>';  
        
        
        foreach($part_master as $model)
        {
            
            echo '<option value="';
            echo $model->spare_id.'">';
            echo $model->part_name." ({$model->part_no})".'</option>';
         
        }
        exit;
    }
    
    public function get_part_detail_using_srno(Request $request)
    {

        $sr_no = $request->input('sr_no');
        $part_code = $request->input('part_code');

        $qry =  "SELECT * FROM `tbl_inventory_item_list` WHERE part_id='$part_code' AND id ='$sr_no'";
        $part_master = DB::select($qry);
        $part_inw_id = $part_master[0]->part_inw_id;


        $qry_inward =  "SELECT * FROM `inward_inventory_particulars` WHERE inw_id='$part_inw_id' ";
        $inward_part_master = DB::select($qry_inward);
        $item_color = $inward_part_master[0]->item_color;
        $asc_amount = $inward_part_master[0]->asc_amount;
        $customer_amount = $inward_part_master[0]->customer_amount;

        return response()->json([
            'item_color' => $item_color,
            'asc_amount' => $asc_amount,
            'customer_amount' => $customer_amount
        ]);
        #exit;
    }
}

