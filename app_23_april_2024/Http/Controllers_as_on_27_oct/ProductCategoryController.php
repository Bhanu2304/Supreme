<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
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


class ProductCategoryController extends Controller
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
        Session::put("page-title","Product Category");
        
        $brand_search = $request->input('brand_search');
        $whereRaw = "";
        
        if(!empty($brand_search))
        {
            $whereRaw = " and pm.brand_id='$brand_search'";
        }
        
        $data           =   DB::select("SELECT pm.product_category_id,pm.brand_id,pm.category_name,pm.category_status,DATE_FORMAT(pm.created_at,'%d-%b-%Y') created_at,
brand_name
FROM  
`product_category_master` pm 
INNER JOIN `brand_master` bm ON pm.brand_id = bm.brand_id and brand_status='1'
WHERE 1=1 $whereRaw
ORDER BY category_name"); 
        
        $brand_json           =   BrandMaster::orderByRaw('brand_name ASC')->get(); 
        $brand_master = json_decode($brand_json,true);
        $url = $_SERVER['APP_URL'].'/add-product-category';
        
        return view('add-product-category')
        ->with('url', $url)
                ->with('brand_search',$brand_search)
                ->with('brand_master',$brand_master)
                ->with('DataArr', $data); 
    }
    
    public function save_product_category(Request $request)
    {
        $category_name = addslashes($request->input('category_name'));
        $brand_id   = addslashes($request->input('brand_id'));
        
        $data_emjson = ProductCategoryMaster::whereRaw("category_name='$category_name' and brand_id='$brand_id'")->first();
        $data_em = json_decode($data_emjson,true);
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Product Detail Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            $productArr            =   new ProductCategoryMaster();
            $productArr->brand_id  =   $brand_id;
            $productArr->category_name = $category_name;
            $UserId = Auth::user()->id;    
            $productArr->created_by=$UserId; 
            $productArr->created_at=date("Y-m-d H:i:s"); 
            
            
            if($productArr->save()){
                Session::flash('message', "Product Detail Added Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Product Detail Not Added. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('add-product-category');
        }
        
        
    }
    
    
    
    public function edit_product_category(Request $request)
    {
        Session::put("page-title","Edit Product Category");
        $product_category_id = base64_decode($request->input('product_category_id')); 
        $data_json = ProductCategoryMaster::where("product_category_id",$product_category_id)->first();
        $data = json_decode($data_json,true);
        $brand_json           =   BrandMaster::orderByRaw('brand_name ASC')->get(); 
        $brand_master = json_decode($brand_json,true);
        $url = $_SERVER['APP_URL'].'/add-product-category';
        //print_r($data); exit;
        return view('edit-product-category')
        ->with('url', $url)
                ->with('brand_master',$brand_master)
                ->with('data',$data);
    }
    
    public function update_product_category(Request $request)
    {
        $category_name = $request->input('category_name');
        $brand_id = $request->input('brand_id');
        $product_category_id = $request->input('product_category_id'); 
        $category_status = $request->input('category_status'); 
        
        $data_emjson = ProductCategoryMaster::whereRaw("product_category_id!='$product_category_id' and brand_id='$brand_id' and category_name='$category_name'")->first();
        $data_em = json_decode($data_emjson,true);
        
        
        
        
        
        if(!empty($data_em))
        {
            Session::flash('error', "Product Detail Allready Exist");
            Session::flash('alert-class', 'alert-danger');
            return back();
        }
        
        else
        {
            
            
            $productArr            =   array();
            $productArr['brand_id']=$brand_id;
            $productArr['category_name']=$category_name;
            $productArr['category_status']=$category_status;
            $UserId = Auth::user()->id;    
            $productArr['updated_by']=$UserId; 
            $productArr['updated_at']=date('Y-m-d H:i:s'); 
            
            if(ProductCategoryMaster::whereRaw("product_category_id='$product_category_id'")->update($productArr)){
                
                
                
                Session::flash('message', "Product Detail Updated Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else{
                Session::flash('error', "Product  Detail Update Failed. Please Try Again");
                Session::flash('alert-class', 'alert-danger');
            } 
            
            return redirect('edit-product-category?product_category_id='. base64_encode($product_category_id));
        }
    }
    
    
    
    public function get_product_category_by_brand_name(Request $request)
    {
        //$country_id = $request->input('country_id');
        $brand_name = $request->input('brand_name');
        
        $category_master = DB::select("SELECT pm.product_category_id,pm.category_name FROM product_category_master pm 
INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND category_status='1'
WHERE brand_name='$brand_name'");
        
        
        if(empty($category_master))
        {
            echo '<option value="">No Product Category Found</option>'; exit;
        }
        
        echo '<option value="">Product Category</option>'; 
        
        
        foreach($category_master as $category)
        {
            echo '<option value="';
                echo $category->product_category_id.'">';
                echo $category->category_name.'</option>';
        }
        exit;
    }
    
    public function get_product_category_by_brand_id(Request $request)
    {
        //$country_id = $request->input('country_id');
        $brand_id = $request->input('brand_id');
        $all = $request->input('all');
        
        $str = "";
        if($brand_id!='All')
        {
            $str = " and bm.brand_id='$brand_id'";
        }
        
        $category_master = DB::select("SELECT pm.product_category_id,pm.category_name FROM product_category_master pm 
INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND category_status='1'
WHERE 1=1 $str");
        
        
        if(empty($category_master))
        {
            echo '<option value="">No Product Detail Found</option>'; exit;
        }
        
        
        echo '<option value="">Product Detail</option>'; 
        if($all=='1')
        {
            echo '<option value="All">All</option>'; 
        }
        
        foreach($category_master as $category)
        {
            echo '<option value="';
                echo $category->product_category_id.'">';
                echo $category->category_name.'</option>';
        }
        exit;
    }
    
    
    
     
    
}

