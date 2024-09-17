<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\ProductMaster;
use App\ProductCategoryMaster;
use App\ModelMaster;
use App\BrandMaster;
use App\RegionMaster;
use App\AccessoriesMaster;
use App\ConditionMaster;
use App\User;
use Session;
use DB;
use Auth;
use Illuminate\Support\Facades\Hash;

class ProductController extends Controller
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
        Session::put("page-title","Product");
        
        
        
        
        
        $data           =   DB::select("SELECT pm.product_id,pm.brand_id,pm.product_name,pm.product_status,DATE_FORMAT(pm.created_at,'%d-%b-%Y') created_at,
brand_name,category_name
FROM  
`product_master` pm 
INNER JOIN `brand_master` bm ON pm.brand_id = bm.brand_id and brand_status='1' 
INNER JOIN `product_category_master` cm ON pm.product_category_id = cm.product_category_id and category_status='1'
WHERE 1=1 
ORDER BY brand_name,category_name,product_name"); 
        
        $brand_json           =   BrandMaster::orderByRaw('brand_name ASC')->get(); 
        $brand_master = json_decode($brand_json,true);
        $url = $_SERVER['APP_URL'].'/add-product';
        
        return view('add-product')
        ->with('url', $url)
                ->with('brand_search', $brand_search)
                ->with('product_category_search', $product_category_search)
                ->with('brand_master',$brand_master)
                ->with('DataArr', $data); 
    }
    
    public function save_product(Request $request)
    {
        $product_name = addslashes($request->input('product_name'));
        $brand_id   = addslashes($request->input('brand_id'));
        $product_category_id   = addslashes($request->input('product_category_id'));
        
        $data_emjson = ProductMaster::whereRaw("brand_id='$brand_id' and product_category_id='$product_category_id' and product_name='$product_name'")->first();
        $data_em = json_decode($data_emjson,true);
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Product Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $productArr            =   new ProductMaster();
            $productArr->brand_id=$brand_id;
            $productArr->product_category_id=$product_category_id;
            $productArr->product_name=$product_name;
            $UserId = Auth::user()->id;    
            $productArr->created_by=$UserId; 
            $productArr->created_at=date("Y-m-d H:i:s"); 
            
            
            if($productArr->save()){
                Session::flash('message', "Product Added Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Product Not Added. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('add-product');
        }
        
        
    }
    
    
    
    public function edit_product(Request $request)
    {
        Session::put("page-title","Edit Product");
        $product_id = base64_decode($request->input('product_id')); 
        $data_json = ProductMaster::where("product_id",$product_id)->first();
        $data = json_decode($data_json,true);
        $brand_json           =   BrandMaster::orderByRaw('brand_name ASC')->get(); 
        $brand_master = json_decode($brand_json,true);
        $brand_id = $data['brand_id'];
        $category_json           =   ProductCategoryMaster::whereRaw("brand_id='$brand_id'")->orderByRaw('category_name ASC')->get(); 
        $category_master = json_decode($category_json,true);
        
        $url = $_SERVER['APP_URL'].'/add-product';
        //print_r($data); exit;
        return view('edit-product')
        ->with('url', $url)
                ->with('brand_master',$brand_master)
                ->with('category_master',$category_master)
                ->with('data',$data);
    }
    
    public function update_product(Request $request)
    {
        $product_name = $request->input('product_name');
        $brand_id = $request->input('brand_id');
        $product_id = $request->input('product_id'); 
        $product_category_id   = addslashes($request->input('product_category_id'));
        $product_status = $request->input('product_status'); 
        
        $data_emjson = ProductMaster::whereRaw("product_id!='$product_id' and product_category_id='$product_category_id' and brand_id='$brand_id' and product_name='$product_name'")->first();
        $data_em = json_decode($data_emjson,true);
        
        
        
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Product Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $productArr            =   array();
            $productArr['brand_id']=$brand_id;
            $productArr['product_name']=$product_name;
            $productArr['product_category_id']=$product_category_id;
            $productArr['product_status']=$product_status;
            $UserId = Auth::user()->id;    
            $productArr['updated_by']=$UserId; 
            $productArr['updated_at']=date('Y-m-d H:i:s'); 
            
            if(ProductMaster::whereRaw("product_id='$product_id'")->update($productArr)){
                
                
                
                Session::flash('message', "Product Details Updated Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Product Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('edit-product?product_id='. base64_encode($product_id));
        }
    }
    
    public function get_product_by_brand_name(Request $request)
    {
        //$country_id = $request->input('country_id');
        $brand_name = $request->input('brand_name');
        
        $qry = "";
        
        
        
        if($brand_name!='All')
        {
            
            $qry .= " and brand_name= '$brand_name'";
        }
        
        $brand_master = DB::select("SELECT pm.product_id,pm.product_name FROM product_master pm 
INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND product_status='1'
WHERE brand_name='$brand_name'");
        
        
        if(empty($brand_master))
        {
            echo '<option value="">No Product Found</option>'; exit;
        }
        
        echo '<option value="">Product</option>'; 
        
        
        foreach($brand_master as $brand)
        {
            echo '<option value="';
                echo $brand->product_name.'">';
                echo $brand->product_name.'</option>';
        }
        exit;
    }
    
    public function get_product_by_brand_id(Request $request)
    {
        //$country_id = $request->input('country_id');
        $brand_id = $request->input('brand_id');
        $product_category_id = $request->input('product_category_id');
        $all = $request->input('all');
        
        $str = "";
        if($brand_id!='All')
        {
            $str .= " and pm.brand_id='$brand_id'";
        }
        if($product_category_id!='All')
        {
            $str .= " and product_category_id='$product_category_id'";
        }
        $product_master = DB::select("SELECT pm.product_id,pm.product_name FROM product_master pm 
INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND product_status='1'
WHERE 1=1 $str");
        
        
        if(empty($product_master))
        {
            echo '<option value="">No Model Found</option>'; exit;
        }
        
        echo '<option value="">Model</option>'; 
        if($all=='1')
        {
            echo '<option value="All">All</option>'; 
        }
        
        foreach($product_master as $brand)
        {
            echo '<option value="';
                echo $brand->product_id.'">';
                echo $brand->product_name.'</option>';
        }
        exit;
    }
    
    public function search_product(Request $request)
    {
        $brand_search = $request->input('brand_id');
        $product_category_search = $request->input('product_category_id');
        
        $whereRaw = "";
        
        if(!empty($brand_search))
        {
            $whereRaw = " and pm.brand_id='$brand_search'";
        }
        if(!empty($product_category_search))
        {
            $whereRaw .= " and pm.product_category_id='$product_category_search'";
        }
        
        
        
        $data           =   DB::select("SELECT pm.product_id,pm.brand_id,pm.product_name,pm.product_status,DATE_FORMAT(pm.created_at,'%d-%b-%Y') created_at,
brand_name,category_name
FROM  
`product_master` pm 
INNER JOIN `brand_master` bm ON pm.brand_id = bm.brand_id and brand_status='1' 
INNER JOIN `product_category_master` cm ON pm.product_category_id = cm.product_category_id and category_status='1'
WHERE 1=1 $whereRaw
ORDER BY brand_name,category_name,product_name"); 
    ?>

     <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>Brand</th>
                                    <th>Product Detail</th>
                                    <th>Product Name</th>
                                    <th>Create Date</th>
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
                                    <td class="Officer"><?php echo $Data->created_at;?></td>
                                    <td class="Status"><?php if($Data->product_status=='1') {echo 'Active';} else {echo 'De-Active';} ?></td>
                                    <td class="Officer"><a href="edit-product?product_id=<?php echo base64_encode($Data->product_id); ?>" >Edit</a></td>
                                 </tr>
                              <?php  } ?>
                                
                              </tbody>   
        
        
<?php        
        exit;
    }
    
    public function add_model(Request $request)
    {
        Session::put("page-title","Model");
        
        $brand_search = $request->input('brand_search');
        $product_category_search = $request->input('product_category_search');
        $product_search = $request->input('product_search');
        
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
        
        
        
        
        
        $product_json           =   ProductMaster::whereRaw("product_status='1'")->orderByRaw('product_name DESC')->get(); 
        $product_arr = json_decode($product_json);
        
        $data           =   DB::select("SELECT mm.model_id,mm.model_name,pm.product_name,bm.brand_name,cm.category_name,mm.model_det,
mm.model_status,DATE_FORMAT(mm.created_at,'%d-%b-%Y') created_at
FROM  `model_master` mm 
INNER JOIN brand_master bm ON mm.brand_id = bm.brand_id AND brand_status='1' 
INNER JOIN product_category_master cm ON mm.product_category_id = cm.product_category_id AND category_status='1'
INNER JOIN product_master pm ON mm.product_id = pm.product_id AND product_status='1'
WHERE 1=1 $whereRaw
ORDER BY brand_name,category_name,product_name,model_name"); 
        
        $brand_json           =   BrandMaster::orderByRaw('brand_name ASC')->get(); 
        $brand_master = json_decode($brand_json,true);
        
        
        
        $url = $_SERVER['APP_URL'].'/add-model';
        
        return view('add-model')
                ->with('product_arr', $product_arr)
                ->with('brand_master',$brand_master)
                ->with('url', $url)
                ->with('DataArr', $data); 
    }
    
    public function save_model(Request $request)
    {
        $model_name = addslashes($request->input('model_name'));
        $model_det = addslashes($request->input('model_det'));
        $product_id   = addslashes($request->input('product_id'));
        $brand_id   = addslashes($request->input('brand_id'));
        $product_category_id   = addslashes($request->input('product_category_id'));
        
        
        
        $data_emjson = ModelMaster::whereRaw("brand_id='$brand_id' and product_category_id='$product_category_id' and model_name='$model_name' and product_id='$product_id'")->first();
        $data_em = json_decode($data_emjson,true);
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Model Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $modelArr            =   new ModelMaster();
            $modelArr->brand_id=$brand_id;
            $modelArr->product_category_id=$product_category_id;
            $modelArr->product_id=$product_id;
            $modelArr->model_name=$model_name;
            $modelArr->model_det=$model_det;
            $UserId = Auth::user()->id;    
            $modelArr->created_by=$UserId; 
            $modelArr->created_at=date("Y-m-d H:i:s"); 
            
            
            if($modelArr->save()){
                Session::flash('message', "Model Added Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Model Not Added. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('add-model');
        }
        
        
    }
    
    public function view_model()
    {
        Session::put("page-title","Model");
        $data           =   DB::select("SELECT mm.model_id,mm.model_name,pm.product_name,
mm.model_status,DATE_FORMAT(mm.created_at,'%d-%b-%Y') created_at
FROM  `model_master` mm 
INNER JOIN product_master pm 
ON mm.product_id = pm.product_id
WHERE model_status='1'
ORDER BY model_name,product_name"); 
       // $data = json_decode($data_json); 
        
        
        
        
        return view('view-model')->with('DataArr', $data);  
    }
    
    public function edit_model(Request $request)
    {
        Session::put("page-title","Edit Model");
        $model_id = base64_decode($request->input('model_id')); 
        $data_json = ModelMaster::where("model_id",$model_id)->first();
        $data = json_decode($data_json,true);
        
        $brand_json           =   BrandMaster::orderByRaw('brand_name ASC')->get(); 
        $brand_master = json_decode($brand_json,true);
        $brand_id = $data['brand_id'];
        $category_json           =   ProductCategoryMaster::whereRaw("brand_id='$brand_id'")->orderByRaw('category_name ASC')->get(); 
        $category_master = json_decode($category_json,true);
        $product_category_id = $data['product_category_id'];
        
        $product_json           =   ProductMaster::whereRaw("product_category_id ='$product_category_id' and product_status='1'")->orderByRaw('product_name ASC')->get(); 
        $product_arr = json_decode($product_json);
        
        //print_r($data); exit;
        $url = $_SERVER['APP_URL'].'/add-model';
        return view('edit-model')
                ->with('data',$data)
                ->with('brand_master',$brand_master)
                ->with('category_master',$category_master)
                ->with('product_arr', $product_arr)
                ->with('url', $url);
    }
    
    public function update_model(Request $request)
    {
        $model_name = addslashes($request->input('model_name'));
        $model_det = addslashes($request->input('model_det'));
        $product_id = $request->input('product_id');
        $model_id = $request->input('model_id'); 
        $model_status = $request->input('model_status'); 
        $brand_id   = addslashes($request->input('brand_id'));
        $product_category_id   = addslashes($request->input('product_category_id'));
        
        $data_emjson = ModelMaster::whereRaw("model_id!='$model_id' and brand_id='$brand_id' and product_category_id='$product_category_id' and model_name='$model_name' and product_id='$product_id'")->first();
        $data_em = json_decode($data_emjson,true);
        
        
        
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Model Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $modelArr            =   array();
            $modelArr['brand_id']=$brand_id;
            $modelArr['product_category_id']=$product_category_id;
            $modelArr['product_id']=$product_id;
            $modelArr['model_name']=$model_name;
            $modelArr['model_det']=$model_det;
            $modelArr['model_status']=$model_status;
            $UserId = Auth::user()->id;    
            $modelArr['updated_by']=$UserId; 
            $modelArr['updated_at']=date('Y-m-d H:i:s'); 
            
            if(ModelMaster::whereRaw("model_id='$model_id'")->update($modelArr)){
                
                
                
                Session::flash('message', "Model Details Updated Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Model Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('edit-model?model_id='. base64_encode($model_id));
        }
    }
    
    public function get_model_by_product_name(Request $request)
    {
        //$country_id = $request->input('country_id');
        $product_name = $request->input('product_name');
        
        $qry = "";
        
        
        
        if($brand_name!='All')
        {
            
            $qry .= " and product_name= '$product_name'";
        }
        
        
        
        
        $model_master = DB::select("SELECT mm.model_id,mm.model_name FROM model_master mm 
INNER JOIN product_master pm ON mm.product_id = pm.product_id AND pm.product_status='1' AND mm.model_status='1'
WHERE pm.product_name='$product_name'");
        
        
        if(empty($model_master))
        {
            echo '<option value="">No Product Found</option>'; exit;
        }
        
        echo '<option value="">Product</option>'; 
        
        
        foreach($model_master as $model)
        {
            echo '<option value="';
                echo $model->model_name.'">';
                echo $model->model_name.'</option>';
        }
        exit;
    }
    
    public function get_model_by_product_id(Request $request)
    {
        $brand_id = $request->input('brand_id');
        $product_category_id = $request->input('product_category_id');
        $product_id = $request->input('product_id');
        $all = $request->input('all');
        
        $str = "";
        if($brand_id!='All')
        {
            $str .= " and mm.brand_id='$brand_id'";
        }
        if($product_category_id!='All')
        {
            $str .= " and mm.product_category_id='$product_category_id'";
        }
        if($product_id!='All')
        {
            $str .= " and  mm.product_id='$product_id'";
        }
        
        $model_master = DB::select("SELECT mm.model_id,mm.model_name FROM model_master mm 
INNER JOIN product_master pm ON mm.product_id = pm.product_id AND pm.product_status='1' AND mm.model_status='1'
WHERE 1=1   $str");
        
        
        if(empty($model_master))
        {
            echo '<option value="">No Model Found</option>'; exit;
        }
        
        echo '<option value="">Model</option>'; 
        if($all=='1')
        {
            echo '<option value="All">All</option>'; 
        }
        
        foreach($model_master as $model)
        {
            echo '<option value="';
                echo $model->model_id.'">';
                echo $model->model_name.'</option>';
        }
        exit;
    }
    
    public function get_cl_model_by_product_id(Request $request)
    {
        
        $product_id = $request->input('product_id');
        $all = $request->input('all');
        
        $str = "";
        
        if($product_id!='All')
        {
            $str .= " and  mm.product_id='$product_id'";
        }
        
        $qry = "SELECT mm.model_id,mm.model_name FROM model_master mm 
INNER JOIN product_master pm ON mm.product_id = pm.product_id AND pm.product_status='1' AND mm.model_status='1'
WHERE 1=1   $str";
        $model_master = DB::select($qry);
        
        
        if(empty($model_master))
        {
            echo '<option value="">No Model Found</option>'; exit;
        }
        
        echo '<option value="">Model</option>'; 
        if($all=='1')
        {
            echo '<option value="All">All</option>'; 
        }
        
        foreach($model_master as $model)
        {
            echo '<option value="';
                echo $model->model_id.'">';
                echo $model->model_name.'</option>';
        }
        exit;
    }
    
    public function get_model_by_brand_id(Request $request)
    {
        $brand_id = $request->input('brand_id');
        $all = $request->input('all');
        
        $str = "";
        if($brand_id!='All')
        {
            $str .= " and mm.brand_id='$brand_id'";
        }
        
        
        $model_master = DB::select("SELECT mm.model_id,mm.model_name FROM model_master mm 
       WHERE mm.model_status='1'   $str");
        
        
        if(empty($model_master))
        {
            echo '<option value="">No Model Found</option>'; exit;
        }
        
        echo '<option value="">Model</option>'; 
        if($all=='1')
        {
            echo '<option value="All">All</option>'; 
        }
        
        foreach($model_master as $model)
        {
            echo '<option value="';
                echo $model->model_id.'">';
                echo $model->model_name.'</option>';
        }
        exit;
    }
    
    public function search_model(Request $request)
    {
        $brand_search = $request->input('brand_id');
        $product_category_search = $request->input('product_category_id');
        $product_search = $request->input('product_id');
        
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
        
        $qr = "SELECT mm.model_id,mm.model_name,pm.product_name,bm.brand_name,cm.category_name,mm.model_det,
mm.model_status,DATE_FORMAT(mm.created_at,'%d-%b-%Y') created_at
FROM  `model_master` mm 
INNER JOIN brand_master bm ON mm.brand_id = bm.brand_id AND brand_status='1' 
INNER JOIN product_category_master cm ON mm.brand_id=cm.brand_id AND mm.product_category_id = cm.product_category_id and category_status='1'
INNER JOIN product_master pm ON mm.brand_id=pm.brand_id AND mm.product_category_id = pm.product_category_id and mm.product_id = pm.product_id AND product_status='1' 
WHERE 1=1 $whereRaw
ORDER BY brand_name,category_name,product_name,model_name";
        
        $data           =   DB::select($qr); 
    ?>

     <thead>
                                 <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>Brand</th>
                                    <th>Product Details</th>
                                    <th>Product Name</th>
                                    <th>Model Name</th>
                                    <th>Details</th>
                                    <th>Create Date</th>
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
                                    <td><?php echo $Data->model_det; ?></td>
                                    <td class="Officer"><?php echo $Data->created_at;?></td>
                                    <td class="Status"><?php if($Data->model_status=='1') {echo 'Active';} else {echo 'De-Active';} ?></td>
                                    <td class="Officer"><a href="edit-model?model_id=<?php echo base64_encode($Data->model_id); ?>" >Edit</a></td>
                                 </tr>
                              <?php  } ?>
                                
                              </tbody>   
        
        
<?php        
        exit;
    }
    
    public function add_brand()
    {
        
        Session::put("page-title","Brand");
        $data           =   DB::select("SELECT *,DATE_FORMAT(created_at,'%d-%b-%Y') created_at from brand_master"); 
        $url = $_SERVER['APP_URL'].'/add-brand';
        return view('add-brand')->with('DataArr', $data)->with('url', $url);  
    }
    
    public function save_brand(Request $request)
    {
        $brand_name = addslashes($request->input('brand_name'));
        
        
        $data_emjson = BrandMaster::whereRaw("brand_name='$brand_name'")->first(); 
        $data_em = json_decode($data_emjson,true);
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Brand Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $brandArr            =   new BrandMaster();
            
            $brandArr->brand_name=$brand_name;
            $UserId = Auth::user()->id;    
            $brandArr->created_by=$UserId; 
            $brandArr->created_at=date("Y-m-d H:i:s"); 
            
            
            if($brandArr->save()){
                Session::flash('message', "Brand Added Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Brand Not Added. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('add-brand');
        }
        
        
    }
    
    public function view_brand()
    {
        $data           =   DB::select("SELECT * from brand_master"); 
       // $data = json_decode($data_json); 
        Session::put("page-title","Brand");
        
        
        
        return view('view-brand')->with('DataArr', $data);  
    }
    
    public function edit_brand(Request $request)
    {
        Session::put("page-title","Edit Brand");
        $brand_id = base64_decode($request->input('brand_id')); 
        $data_json = BrandMaster::where("brand_id",$brand_id)->first();
        $data = json_decode($data_json,true);
        
        //$product_json           =   ProductMaster::whereRaw("product_status='1'")->orderByRaw('product_name DESC')->get(); 
        //$product_arr = json_decode($product_json);
        
        //print_r($data); exit;
        $url = $_SERVER['APP_URL'].'/add-brand';
        return view('edit-brand')->with('data',$data)->with('url', $url);
    }
    
    public function update_brand(Request $request)
    {
        $brand_name = addslashes($request->input('brand_name'));
        $brand_id = $request->input('brand_id'); 
        $brand_status = $request->input('brand_status'); 
        
        $data_emjson = BrandMaster::whereRaw("brand_id!='$brand_id' and brand_name='$brand_name'")->first();
        $data_em = json_decode($data_emjson,true);
        
        
        
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Brand Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $modelArr            =   array();
            
            $modelArr['brand_name']=$brand_name;
            $modelArr['brand_status']=$brand_status;
            $UserId = Auth::user()->id;    
            $modelArr['updated_by']=$UserId; 
            $modelArr['updated_at']=date('Y-m-d H:i:s'); 
            
            //print_r($modelArr); exit;
            
            if(BrandMaster::whereRaw("brand_id='$brand_id'")->update($modelArr)){
                
                
                
                Session::flash('message', "Brand Details Updated Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Brand Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('edit-brand?brand_id='. base64_encode($brand_id));
        }
    }
    
    public function add_region()
    {
        Session::put("page-title","Region");
        $data           =   DB::select("SELECT * from region_master"); 
        $url = $_SERVER['APP_URL'].'/add-region';
        return view('add-region')->with('DataArr', $data)->with('url', $url);  
    }
    
    public function save_region(Request $request)
    {
        
        $region_name = addslashes($request->input('region_name'));
        
        
        $data_emjson = RegionMaster::whereRaw("region_name='$region_name'")->first(); 
        $data_em = json_decode($data_emjson,true);
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Region Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $regionArr            =   new RegionMaster();
            
            $regionArr->region_name=$region_name;
            $UserId = Auth::user()->id;    
            $regionArr->created_by=$UserId; 
            $regionArr->created_at=date("Y-m-d H:i:s"); 
            
            
            if($regionArr->save()){
                Session::flash('message', "Region Added Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Region Not Added. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('add-region');
        }
        
        
    }
    
    public function view_region()
    {
        Session::put("page-title","Region");
        $data           =   DB::select("SELECT * from region_master"); 
       // $data = json_decode($data_json); 
        
        
        
        
        return view('view-region')->with('DataArr', $data);  
    }
    
    public function edit_region(Request $request)
    {
        Session::put("page-title","Edit Region");
        $region_id = base64_decode($request->input('region_id')); 
        $data_json = RegionMaster::where("region_id",$region_id)->first();
        $data = json_decode($data_json,true);
        $url = $_SERVER['APP_URL'].'/add-region';
        //print_r($data); exit;
        //$product_json           =   ProductMaster::whereRaw("product_status='1'")->orderByRaw('product_name DESC')->get(); 
        //$product_arr = json_decode($product_json);
        
        //print_r($data); exit;
        return view('edit-region')->with('data',$data)->with('url', $url);
    }
    
    public function update_region(Request $request)
    {
        $region_name = addslashes($request->input('region_name'));
        $region_id = $request->input('region_id'); 
        $region_status = $request->input('region_status'); 
        
        $data_emjson = RegionMaster::whereRaw("region_id!='$region_id' and region_name='$region_name'")->first();
        $data_em = json_decode($data_emjson,true);
        
        
        
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Region Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $modelArr            =   array();
            
            $modelArr['region_name']=$region_name;
            $modelArr['region_status']=$region_status;
            $UserId = Auth::user()->id;    
            $modelArr['updated_by']=$UserId; 
            $modelArr['updated_at']=date('Y-m-d H:i:s'); 
            
            //print_r($modelArr); exit;
            
            if(RegionMaster::whereRaw("region_id='$region_id'")->update($modelArr)){
                
                
                
                Session::flash('message', "Region Details Updated Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Region Details Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('edit-region?region_id='. base64_encode($region_id));
        }
    }
    
    
    
     
    
}

