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


class SparePartController extends Controller
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
    
    public function save_part(Request $request){
        $created_by     =   Auth::User()->id;
        
        $spareArr            =   new SparePart();
        
        $brand_id =    addslashes($request->input('brand_id'));
        $product_category_id =    addslashes($request->input('product_category_id'));
        $product_id =    addslashes($request->input('product_id'));
        $model_id =    addslashes($request->input('model_id'));
        $serial_no =    addslashes($request->input('serial_no'));
        
        
        $part_name = addslashes($request->input('part_name'));
        $part_no =    addslashes($request->input('part_no'));
        $hsn_code =    addslashes($request->input('hsn_code'));
        
        $landing_cost =    addslashes($request->input('landing_cost'));
        $customer_price =    addslashes($request->input('customer_price'));
        $discount =    addslashes($request->input('discount'));
        
        //$part_rate =    addslashes($request->input('part_rate'));
        
        $part_tax =    addslashes($request->input('part_tax'));
        
            
        $spareArr->brand_id=$brand_id;
        $spareArr->product_category_id=$product_category_id;
        $spareArr->product_id=$product_id;
        $spareArr->model_id=$model_id;
        $spareArr->serial_no=$serial_no;
        $spareArr->part_name=$part_name;
        $spareArr->hsn_code=$hsn_code;
        $spareArr->part_no=$part_no;
        //$spareArr->part_rate=$part_rate;
        $spareArr->landing_cost=$landing_cost;
        $spareArr->customer_price=$customer_price;
        $spareArr->discount=$discount;
        $spareArr->part_tax=$part_tax;
        
        
        $spareArr->part_status='1';
        
        $UserId = Auth::user()->id;    
        $spareArr->created_by=$UserId; 
        $spareArr->created_at=date("Y-m-d H:i:s");
        
        $check_part_name = SparePart::whereRaw("brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name'")->first();
        $check_part_name = json_decode($check_part_name,true);
        
        $check_part_no = SparePart::whereRaw("brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no'")->first();
        $check_part_no = json_decode($check_part_no,true);
        
        $check_hsn_code = SparePart::whereRaw("brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'")->first();
        $check_hsn_code = json_decode($check_hsn_code,true);
        
        
            
        if(!empty($check_part_name))
        {
            Session::flash('message', "Spare Part Name Allready Exist.");
            Session::flash('alert-class', 'alert-danger');
        }
        else if(!empty($check_part_no))
        {
            Session::flash('message', "Part No. Allready Exist.");
            Session::flash('alert-class', 'alert-danger');
        } 
        else if(!empty($check_hsn_code))
        {
            Session::flash('message', "HSN Code Allready Exist.");
            Session::flash('alert-class', 'alert-danger');
        }
           
        else if($spareArr->save())
        {
            Session::flash('message', "Spare Part Added Successfully.");
            Session::flash('alert-class', 'alert-danger');
        }
        else
        {
            Session::flash('error', "Spare Part Creation Failed. Please Try Again");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }    
        
        return redirect('add-part');   
    }
    
    public function edit_part(Request $request)
    {
        $part_id = base64_decode($request->input('spare_id')); 
        $data_json = SparePart::where("spare_id",$part_id)->first();
        $record = json_decode($data_json,true);
        
        $brand_json           =   BrandMaster::whereRaw("brand_status='1'")->orderByRaw('brand_name ASC')->get(); 
        $brand_arr = json_decode($brand_json,true);
        
        $product_det_json           =   ProductCategoryMaster::whereRaw("brand_id='{$data_json->brand_id}' and category_status='1'")->orderByRaw('category_name ASC')->get(); 
        $product_det = json_decode($product_det_json,true);
        
        $product_json           =   ProductMaster::whereRaw("brand_id='{$data_json->brand_id}' and product_category_id='{$data_json->product_category_id}' and product_status='1'")->orderByRaw('product_name ASC')->get(); 
        $product_arr = json_decode($product_json,true);
        
        $model_json           =   ModelMaster::whereRaw("brand_id='{$data_json->brand_id}' and product_category_id='{$data_json->product_category_id}' and product_id='{$data_json->product_id}' and model_status='1'")->orderByRaw('model_name ASC')->get(); 
        $model_arr = json_decode($model_json,true);
        
        
        $url = $_SERVER['APP_URL'].'/add-part';
        //print_r($data); exit;
        return view('edit-part')
                ->with('record',$record)
                ->with('brand_arr', $brand_arr)
                ->with('product_det', $product_det)
                ->with('product_arr', $product_arr)
                ->with('model_arr', $model_arr)
                ->with('url', $url);
    }
    
    
    public function update_part(Request $request){
        
        
        
        $brand_id =    $request->input('brand_id');
        $product_category_id =    addslashes($request->input('product_category_id'));
        $product_id =    addslashes($request->input('product_id'));
        $model_id =    addslashes($request->input('model_id'));
        $part_name = addslashes($request->input('part_name'));
        $serial_no = addslashes($request->input('serial_no'));
        $part_no =    addslashes($request->input('part_no'));
        $hsn_code =    addslashes($request->input('hsn_code'));
        //$part_rate =    addslashes($request->input('part_rate'));
        $part_tax =    addslashes($request->input('part_tax'));
        $landing_cost =    addslashes($request->input('landing_cost'));
        $customer_price =    addslashes($request->input('customer_price'));
        $discount =    addslashes($request->input('discount'));
        $spare_id =    $request->input('spare_id');
        $part_status =    $request->input('part_status');
        
        
            
        $spareArr['brand_id']=$brand_id;
        $spareArr['product_category_id']=$product_category_id;
        $spareArr['product_id']=$product_id;
        $spareArr['model_id']=$model_id;
        $spareArr['hsn_code']=$hsn_code;
        $spareArr['part_no']=$part_no;
        $spareArr['part_name']=$part_name;
        $spareArr['serial_no']=$serial_no;
        //$spareArr['part_rate']=$part_rate;
        //$spareArr['part_tax']=$part_tax;
        $spareArr['landing_cost']=$landing_cost;
        $spareArr['customer_price']=$customer_price;
        $spareArr['discount']=$discount;
        $spareArr['part_tax']=$part_tax;
        $spareArr['part_status']=$part_status;
        
        $UserId = Auth::user()->id;    
        $spareArr['updated_by']=$UserId; 
        $spareArr['updated_at']=date("Y-m-d H:i:s");
        
        $check_part_name = SparePart::whereRaw("spare_id!='$spare_id' and brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name'")->first();
        $check_part_name = json_decode($check_part_name,true);
        
        $check_part_no = SparePart::whereRaw("spare_id!='$spare_id' and brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no'")->first();
        $check_part_no = json_decode($check_part_no,true);
        
        $check_hsn_code = SparePart::whereRaw("spare_id!='$spare_id' and brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and part_name='$part_name' and part_no='$part_no' and hsn_code='$hsn_code'")->first();
        $check_hsn_code = json_decode($check_hsn_code,true);
        
        //validate check starts from here whether entry is available or not.
        
        
        
        if(!empty($check_part_name))
        {
            Session::flash('message', "Spare Part Name Allready Exist.");
            Session::flash('alert-class', 'alert-danger');
        }
        else if(!empty($check_part_no))
        {
            Session::flash('message', "Part No. Allready Exist.");
            Session::flash('alert-class', 'alert-danger');
        }
        else if(!empty($check_hsn_code))
        {
            Session::flash('message', "HSN Code Allready Exist.");
            Session::flash('alert-class', 'alert-danger');
        }
        
        else if(SparePart::whereRaw("spare_id='$spare_id'")->update($spareArr))
        {
            Session::flash('message', "Spare Part Updated Successfully.");
            Session::flash('alert-class', 'alert-danger');
        }
        else
        {
            Session::flash('error', "Spare Part Update Failed. Please Try Again.");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        return redirect('add-part');   
    }
    
    public function search_part(Request $request)
    {
        $brand_search = $request->input('brand_id');
        $product_category_search = $request->input('product_category_id');
        $product_search = $request->input('product_id');
        $model_search = $request->input('model_id');
        
        $whereRaw = "";
        
        if(!empty($brand_search))
        {
            $whereRaw = " and mm.brand_id='$brand_search'";
        }
        if(!empty($product_category_search))
        {
            $whereRaw .= " and mm.product_category_id='$product_category_search'";
        }
        if(!empty($product_search))
        {
            $whereRaw .= " and mm.product_id='$product_search'";
        }
        if(!empty($model_search))
        {
            $whereRaw .= " and mm.model_id='$model_search'";
        }
        
        $qr = "SELECT sp.*,brand_name,category_name,product_name,model_name FROM tbl_spare_parts sp 
INNER JOIN brand_master bm ON sp.brand_id = bm.brand_id AND brand_status='1' 
INNER JOIN product_category_master cm ON sp.brand_id=cm.brand_id AND sp.product_category_id = cm.product_category_id and category_status='1'
INNER JOIN product_master pm ON sp.brand_id=pm.brand_id AND sp.product_category_id = pm.product_category_id and sp.product_id = pm.product_id AND product_status='1' 
INNER JOIN model_master mm ON sp.brand_id=mm.brand_id AND sp.product_category_id = mm.product_category_id and sp.product_id = mm.product_id and sp.model_id = mm.model_id AND mm.model_status='1'
WHERE 1=1 $whereRaw
ORDER BY brand_name,category_name,product_name,model_name,sp.part_name";
        
        $data           =   DB::select($qr); 
    ?>

     <thead>
                                 <thead>
                                 <tr>	
                                      <th>S.No.</th> 
                                      <th>Brand</th>
                                      <th>Product Detail</th>
                                      <th>Product</th>
                                      <th>Model</th>
                                      <th>Spare Part Name</th>
                                      <th>Part No.</th> 
                                      <th>HSN Code</th>
                                      <th>Landing Cost</th>
                                      <th>Customer Price</th>
                                      <th>Discount</th>
                                      <th>Tax</th>
                                      <th>Status</th>
                                      <th>Action</th> 
                                  </tr>
                              </thead>
                             
                              <tbody>
                                  <?php $i = 0; 
                                    foreach($data as $Data)
                                    {
                                  ?>
                                  
                                 <tr>
                                    <td><?php echo ++$i; ?></td>
                                    <td><?php echo $Data->brand_name; ?></td>
                                    <td><?php echo $Data->category_name; ?></td>
                                    <td><?php echo $Data->product_name; ?></td>
                                    <td><?php echo $Data->model_name; ?></td>
                                    <td><?php echo $Data->part_name;?></td>
                                          <td><?php echo $Data->part_no; ?></td>
                                          <td><?php echo $Data->hsn_code; ?></td>
                                          <td><?php echo $Data->landing_cost; ?></td>
                                          <td><?php echo $Data->customer_price; ?></td>
                                          <td><?php echo $Data->discount; ?></td>
                                          <td><?php echo $Data->part_tax; ?></td>
                                    
                                    <td class="Status"><?php if($Data->part_status=='1') {echo 'Active';} else {echo 'De-Active';} ?></td>
                                    <td class="Officer"><a href="edit-part?spare_id=<?php echo base64_encode($Data->spare_id); ?>" >Edit</a></td>
                                 </tr>
                              <?php  } ?>
                                
                              </tbody>   
        
        
<?php        
        exit;
    }
    
    public function get_part_name_by_part_code(Request $request)
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
            echo $model->part_name;exit;
        }
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
        $part_master = DB::select("SELECT spare_id,part_no FROM `tbl_spare_parts` WHERE 1=1 $filter_qry  AND part_status='1'");
        
        
        if(empty($part_master))
        {
            echo '<option value="">No Part Code Found</option>'; exit;
        }
        
        echo '<option value="">Part Code</option>';  
        
        
        foreach($part_master as $model)
        {
            
            echo '<option value="';
            echo $model->spare_id.'">';
            echo $model->part_no.'</option>';
         
        }
        exit;
    }
}

