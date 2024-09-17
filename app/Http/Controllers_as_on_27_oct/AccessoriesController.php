<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\AccessoriesMaster;
use DB;
use Auth;
use Session;
use App\BrandMaster;
use App\ProductCategoryMaster;
use App\ProductMaster;
use App\ModelMaster;


class AccessoriesController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function add_acc()
    {
        Session::put("page-title","Accessories");
        
        $brand_json           =   BrandMaster::orderByRaw('brand_name ASC')->get(); 
        $brand_master = json_decode($brand_json,true);
        
        $data           =   DB::select("SELECT ta.*,brand_name,category_name,product_name,model_name FROM tbl_accessories ta
INNER JOIN brand_master bm ON ta.brand_id = bm.brand_id AND brand_status='1' 
INNER JOIN product_category_master cm ON ta.product_category_id = cm.product_category_id AND category_status='1'
INNER JOIN product_master pm ON ta.product_id = pm.product_id AND product_status='1' 
INNER JOIN model_master mm ON ta.model_id = mm.model_id AND model_status='1'
ORDER BY model_name,category_name,product_name,model_name"); 
        $url = $_SERVER['APP_URL'].'/add-acc';
        return view('add-acc')->with('DataArr', $data)->with('brand_master',$brand_master)->with('url', $url);  
    }
    
    public function save_acc(Request $request)
    {
        $acc_name = addslashes($request->input('acc_name'));
        $brand_id   = addslashes($request->input('brand_id'));
        $product_category_id   = addslashes($request->input('product_category_id'));
        $product_id   = addslashes($request->input('product_id'));
        $model_id   = addslashes($request->input('model_id'));
        
        
        $data_emjson = AccessoriesMaster::whereRaw("brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id'  and acc_name='$acc_name'")->first(); 
        $data_em = json_decode($data_emjson,true);
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Accessories Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $accArr            =   new AccessoriesMaster();
            $accArr->brand_id=$brand_id;
            $accArr->product_category_id=$product_category_id;
            $accArr->product_id=$product_id;
            $accArr->model_id=$model_id;
            $accArr->acc_name=$acc_name;
            $UserId = Auth::user()->id;    
            $accArr->created_by=$UserId; 
            $accArr->created_at=date("Y-m-d H:i:s"); 
            
            
            if($accArr->save()){
                Session::flash('message', "Accessories Added Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Accessories Not Added. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('add-acc');
        }
        
        
    }
    
    public function view_acc()
    {
        $data           =   DB::select("SELECT * from tbl_accessories"); 
       // $data = json_decode($data_json); 
        
        
        
        
        return view('view-acc')->with('DataArr', $data);  
    }
    
    public function edit_acc(Request $request)
    {
        $acc_id = base64_decode($request->input('acc_id')); 
        $data_json = AccessoriesMaster::where("acc_id",$acc_id)->first();
        $data = json_decode($data_json,true);
        $brand_json           =   BrandMaster::orderByRaw('brand_name ASC')->get(); 
        $brand_master = json_decode($brand_json,true);
        $brand_id = $data['brand_id'];
        $category_json           =   ProductCategoryMaster::whereRaw("brand_id='$brand_id'")->orderByRaw('category_name ASC')->get(); 
        $category_master = json_decode($category_json,true);
        $product_category_id = $data['product_category_id'];
        $product_json           =   ProductMaster::whereRaw("product_category_id ='$product_category_id' and product_status='1'")->orderByRaw('product_name ASC')->get(); 
        $product_arr = json_decode($product_json);
        $product_id = $data['product_id'];
        $model_json           =   ModelMaster::whereRaw("product_id ='$product_id' and model_status='1'")->orderByRaw('model_name ASC')->get(); 
        $model_arr = json_decode($model_json);
        
        $url = $_SERVER['APP_URL'].'/add-acc';
        return view('edit-acc')
                ->with('data',$data)
                ->with('brand_master',$brand_master)
                ->with('category_master',$category_master)
                ->with('product_arr', $product_arr)
                ->with('model_arr', $model_arr)
                ->with('url', $url);
    }
    
    public function update_acc(Request $request)
    {
        $acc_name = addslashes($request->input('acc_name'));
        $acc_id = $request->input('acc_id'); 
        $acc_status = $request->input('acc_status'); 
        $brand_id   = addslashes($request->input('brand_id'));
        $product_category_id   = addslashes($request->input('product_category_id'));
        $product_id   = addslashes($request->input('product_id'));
        $model_id   = addslashes($request->input('model_id'));
        
        $data_emjson = AccessoriesMaster::whereRaw("acc_id!='$acc_id' and brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' and acc_name='$acc_name'")->first();
        $data_em = json_decode($data_emjson,true);
        
        
        
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Accessories Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $accArr            =   array();
            $accArr['brand_id']=$brand_id;
            $accArr['product_category_id']=$product_category_id;
            $accArr['product_id']=$product_id;
            $accArr['model_id']=$model_id;
            $accArr['acc_name']=$acc_name;
            $accArr['acc_status']=$acc_status;
            $UserId = Auth::user()->id;    
            $accArr['updated_by']=$UserId; 
            $accArr['updated_at']=date('Y-m-d H:i:s'); 
            
            //print_r($modelArr); exit;
            
            if(AccessoriesMaster::whereRaw("acc_id='$acc_id'")->update($accArr)){
                
                
                
                Session::flash('message', "Accessories Details Updated Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Accessories Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('add-acc?acc_id='. base64_encode($acc_id));
        }
    }
    
    public function search_acc(Request $request)
    {
        $brand_search = $request->input('brand_id');
        $product_category_search = $request->input('product_category_id');
        $product_search = $request->input('product_id');
        $model_search = $request->input('model_id');
        
        $whereRaw = "";
        
        if(!empty($brand_search))
        {
            $whereRaw = " and ta.brand_id='$brand_search'";
        }
        if(!empty($product_category_search))
        {
            $whereRaw .= " and ta.product_category_id='$product_category_search'";
        }
        if(!empty($product_search))
        {
            $whereRaw .= " and ta.product_id='$product_search'";
        }
        if(!empty($model_search))
        {
            $whereRaw .= " and ta.model_id='$model_search'";
        }
        
        $qr = "SELECT ta.*,brand_name,category_name,product_name,model_name FROM tbl_accessories ta 
INNER JOIN brand_master bm ON ta.brand_id = bm.brand_id AND brand_status='1' 
INNER JOIN product_category_master cm ON ta.brand_id=cm.brand_id AND ta.product_category_id = cm.product_category_id and category_status='1'
INNER JOIN product_master pm ON ta.brand_id=pm.brand_id AND ta.product_category_id = pm.product_category_id and ta.product_id = pm.product_id AND product_status='1' 
INNER JOIN model_master mm ON ta.brand_id=mm.brand_id AND ta.product_category_id = mm.product_category_id and ta.product_id = mm.product_id and ta.model_id = mm.model_id AND mm.model_status='1'
WHERE 1=1 $whereRaw
ORDER BY brand_name,category_name,product_name,model_name,ta.acc_name";
        
        $data           =   DB::select($qr); 
    ?>

     <thead>
                                 <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>Brand</td>
                                    <th>Product Detail</th>
                                    <th>Product</th>
                                    <th>Model</th>
                                    <th>Accessories Name</th>
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
                                    <td><?php echo $Data->acc_name; ?></td>
                                    
                                    <td class="Status"><?php if($Data->acc_status=='1') {echo 'Active';} else {echo 'De-Active';} ?></td>
                                    <td class="Officer"><a href="edit-acc?acc_id=<?php echo base64_encode($Data->Acc_Id); ?>" >Edit</a></td>
                                 </tr>
                              <?php  } ?>
                                
                              </tbody>   
        
        
<?php        
        exit;
    }
    
    public function get_acc(Request $request)
    {
        $brand_id = $request->input('brand_id');
        $product_category_id = $request->input('product_category_id');
        $product_id = $request->input('product_id');
        $model_id = $request->input('model_id');
        
        $whereRaw = "";
        
        if(!empty($brand_search))
        {
            $whereRaw = " and ta.brand_id='$brand_search'";
        }
        if(!empty($product_category_search))
        {
            $whereRaw .= " and ta.product_category_id='$product_category_search'";
        }
        if(!empty($product_search))
        {
            $whereRaw .= " and ta.product_id='$product_search'";
        }
        if(!empty($model_search))
        {
            $whereRaw .= " and ta.model_id='$model_search'";
        }
        
        $qr5 = "SELECT Acc_Id,acc_name FROM `tbl_accessories` ta WHERE acc_status='1'  $whereRaw";
        $acc_json           =   DB::select($qr5);  
        
        foreach($acc_json as $acc)
        {
            $acc_master[$acc->Acc_Id] = $acc->acc_name;
        }
        
    foreach($acc_master as $field_name=>$sub_field_name) { ?>                                         
    <div class="col-md-4">
        <div class="position-relative form-group"><label for="examplePassword11" class=""><?php echo $sub_field_name; ?></label>
            <select id="<?php echo $sub_field_name; ?>" name="accesories_list[<?php echo $sub_field_name; ?>]" class="form-control" >
                <option value="">Select</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>
    </div>    
<?php        
    }
    exit;
    }
    
    
    
    
    
    
}

