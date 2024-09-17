<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\ConditionMaster;
use DB;
use Auth;
use Session;
use App\BrandMaster;
use App\ProductCategoryMaster;
use App\ProductMaster;
use App\ModelMaster;


class ConditionController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function add_cndn()
    {
        $cndn_json           =   ConditionMaster::whereRaw("con_status='1'")->orderByRaw('field_name,priority ASC')->get(); 
        $url = $_SERVER['APP_URL'].'/add-cndn';
        
        $brand_json           =   BrandMaster::orderByRaw('brand_name ASC')->get(); 
        $brand_master = json_decode($brand_json,true);
        
        $record = array();
        
        foreach($brand_master as $brand)
        {
            $record['brand_id'][$brand['brand_id']] = $brand['brand_name'];
        }
        
        foreach($cndn_json as $part)
        {
            $brand_id = $part->brand_id;
            $product_det =  DB::select("SELECT product_category_id,category_name FROM product_category_master WHERE brand_id='$brand_id'  and category_status='1' ");
            
            foreach($product_det as $det)
            {
                $record['product_category_id'][$brand_id][$det->product_category_id] = $det->category_name;
            }
            
            $product_category_id = $part->product_category_id;
            $product_master =  DB::select("SELECT product_id,product_name FROM product_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_status='1' ");
            
            foreach($product_master as $prod)
            {
                $record['product_id'][$brand_id.'##'.$product_category_id][$prod->product_id] = $prod->product_name;
            }
            
            $product_id = $part->product_id;
            $model_det =  DB::select("SELECT model_id,model_name FROM model_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and  model_status='1' ");
            foreach($model_det as $det)
            {
                $record['model_id'][$brand_id.'##'.$product_category_id.'##'.$product_id][$det->model_id] = $det->model_name;
            }
            
               
        }
        
        
        
        
        
        
        return view('add-cndn')
        ->with('cndn_arr',$cndn_json)
                ->with('brand_master',$brand_master)
                ->with('record',$record)
                ->with('url', $url);  
    }
    
    public function save_cndn(Request $request)
    {
        $field_name = addslashes($request->input('field_name'));
        $sub_field_name = addslashes($request->input('sub_field_name'));
        $opt = addslashes($request->input('opt'));
        $field_type = addslashes($request->input('field_type'));
        $con_remarks = addslashes($request->input('con_remarks'));
        $brand_id   = addslashes($request->input('brand_id'));
        $product_category_id   = addslashes($request->input('product_category_id'));
        $product_id   = addslashes($request->input('product_id'));
        $model_id   = addslashes($request->input('model_id'));
        
        $data_emjson = ConditionMaster::whereRaw("brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id'  and field_name='$field_name' and sub_field_name='$sub_field_name'")->first(); 
        $data_em = json_decode($data_emjson,true);
        
        
        if(empty($field_name) || empty($sub_field_name) || empty($opt) || empty($field_type))
        {
            echo 'Please Fill All Fields.';exit;
        }
        else if(!empty($data_em))
        {
            echo 'exist';exit;
        }
        
        else
        {
            $accArr            =   new ConditionMaster();
            $accArr->brand_id=$brand_id;
            $accArr->product_category_id=$product_category_id;
            $accArr->product_id=$product_id;
            $accArr->model_id=$model_id;
            $accArr->field_name=$field_name;
            $accArr->sub_field_name=$sub_field_name;
            $accArr->opt=$opt;
            $accArr->field_type=$field_type;
            $accArr->con_remarks=$con_remarks;
            
            $UserId = Auth::user()->id;    
            $accArr->created_by=$UserId; 
            $accArr->created_at=date("Y-m-d H:i:s");
            
            $max_qry = "SELECT MAX(priority) prior FROM `condition_master` where brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and model_id='$model_id' ";
            $Max = DB::select($max_qry);
            $prior = $Max[0]->prior;
            
            if(empty($prior))
            {
                $prior = 1;
            }
            else
            {
                $prior += 1;
            }
            
            $accArr->priority = "$prior";
            
            
            if($accArr->save()){
                //Session::flash('message', "Condition Added Successfully.");
                //Session::flash('alert-class', 'alert-danger');
                echo 'succ';exit;
            }
            else{
                //Session::flash('error', "Condition Not Added. Please Try Again");
                //Session::flash('alert-class', 'alert-danger');
                echo 'unsucc';exit;
            } 
            
            //return redirect('add-acc');
        }
        
        
    }
    
    
    public function update_cndn(Request $request)
    {
        $cndn_arr = $request->input('cndn');
        
        if(!empty($cndn_arr))
        {
            //$cndn_
            //print_r($cndn_arr); exit;
            //DB::beginTransaction();
            $cndn_keys = array_keys($cndn_arr);
            //print_r($cndn_keys); exit;
            $flag = true;
            foreach($cndn_keys as $key)
            {
                $cndn_record = $cndn_arr[$key];
                
                //print_r($cndn_arr); exit;
                
                $upd_arr = array();
                foreach($cndn_record as $key_at=>$key_at_value)
                {
                    $upd_arr[$key_at] = addslashes($key_at_value);
                }
                $UserId = Auth::user()->id;    
                $upd_arr['updated_by']=$UserId; 
                $upd_arr['updated_at']=date('Y-m-d H:i:s'); 
                
                //print_r($key); //exit;
                
                if(ConditionMaster::whereRaw("con_id='$key'")->update($upd_arr))
                {
                    //echo $key.'<br/>';
                }
                else
                {
                    $flag = false;
                }
            }
            
           // exit;
            
            if($flag)
            {
                DB::commit();
                Session::flash('message', " Details Updated Successfully.");
                Session::flash('alert-class', 'alert-danger');
            }
            else
            {
                DB::rollback();  
                Session::flash('message', " Details Not Updated Successfully.");
                Session::flash('alert-class', 'alert-danger');
                 
            }
            
        }
        return redirect('add-cndn');
        //print_r($field_name); exit;
        
        
        
        
    }
    
    
    public function delete_cndn(Request $request)
    {
        $cndn_id = $request->input('id');
        
        if(ConditionMaster::whereRaw("con_id='$cndn_id'")->delete())
        {
            echo 'succ';exit;
        }
        else
        {
            echo 'fail';exit;
        }
    }
    
public function search_cndn(Request $request)
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
        
        $qr = "SELECT cndn.*,brand_name,category_name,product_name,model_name FROM condition_master cndn 
INNER JOIN brand_master bm ON cndn.brand_id = bm.brand_id AND brand_status='1' 
INNER JOIN product_category_master cm ON cndn.brand_id=cm.brand_id AND cndn.product_category_id = cm.product_category_id AND category_status='1'
INNER JOIN product_master pm ON cndn.brand_id=pm.brand_id AND cndn.product_category_id = pm.product_category_id AND cndn.product_id = pm.product_id AND product_status='1' 
INNER JOIN model_master mm ON cndn.brand_id=mm.brand_id AND cndn.product_category_id = mm.product_category_id AND cndn.product_id = mm.product_id AND cndn.model_id = mm.model_id AND mm.model_status='1'
WHERE 1=1 $whereRaw
ORDER BY brand_name,category_name,product_name,model_name";
        
        $cndn_arr           =   DB::select($qr);
        
        
        
        $brand_json           =   BrandMaster::orderByRaw('brand_name ASC')->get(); 
        $brand_master = json_decode($brand_json,true);
        
        $record = array();
        
        foreach($brand_master as $brand)
        {
            $record['brand_id'][$brand['brand_id']] = $brand['brand_name'];
        }
        
        foreach($cndn_arr as $part)
        {
            $brand_id = $part->brand_id;
            $product_det =  DB::select("SELECT product_category_id,category_name FROM product_category_master WHERE brand_id='$brand_id'  and category_status='1' ");
            
            foreach($product_det as $det)
            {
                $record['product_category_id'][$brand_id][$det->product_category_id] = $det->category_name;
            }
            
            $product_category_id = $part->product_category_id;
            $product_master =  DB::select("SELECT product_id,product_name FROM product_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_status='1' ");
            
            foreach($product_master as $prod)
            {
                $record['product_id'][$brand_id.'##'.$product_category_id][$prod->product_id] = $prod->product_name;
            }
            
            $product_id = $part->product_id;
            $model_det =  DB::select("SELECT model_id,model_name FROM model_master WHERE brand_id='$brand_id' and product_category_id='$product_category_id' and product_id='$product_id' and  model_status='1' ");
            foreach($model_det as $det)
            {
                $record['model_id'][$brand_id.'##'.$product_category_id.'##'.$product_id][$det->model_id] = $det->model_name;
            }
            
               
        }
        
        
        
    ?>

     
                                 <thead>
                                 <tr>
                                         <th>Brand</th>
                                         <th>Product Detail</th>
                                         <th>Product</th>
                                         <th>Model</th>
                                         <th>Field</th>
                                         <th>Option#1</th>
                                         <th>Option#2</th>
                                     </tr>
                              </thead>
                             
                              <tbody>
                                   
                                     
                                     
                                     <?php $i=0;  $field_exist = array();
                                            foreach($cndn_arr as $cndn)
                                            {
                                     ?>
                                     <tr>
                                         <td>
                                             <?php if(!in_array($cndn->field_name,$field_exist)) { 
                                                 
                                                 echo $record['brand_id'][$cndn->brand_id];
                                             }
                                        ?>
                                             
                                         </td>
                                         
                                         <td>
                                             <?php if(!in_array($cndn->field_name,$field_exist)) { 
                                                 echo $record['product_category_id'][$cndn->brand_id][$cndn->product_category_id];
                                             }
                                        ?>
                                         </td>
                                         
                                         <td>
                                             <?php if(!in_array($cndn->field_name,$field_exist)) {      
                                                 echo $record['product_id'][$cndn->brand_id.'##'.$cndn->product_category_id][$cndn->product_id];
                                             }
                                        ?>  
                                         </td>
                                         
                                         <td>
                                             <?php if(!in_array($cndn->field_name,$field_exist)) {      
                                                 echo $record['model_id'][$cndn->brand_id.'##'.$cndn->product_category_id.'##'.$cndn->product_id][$cndn->model_id];
                                             }
                                        ?>  
                                         </td>
                                         
                                         
                                         <td>
                                             <?php if(!in_array($cndn->field_name,$field_exist)) { 
                                                 $field_exist[] = $cndn->field_name;
                                                 echo $cndn->field_name;
                                             }
                                        ?>
                                             
                                         </td>
                                         <td><?php echo $cndn->sub_field_name; ?></td>
                                         <td>
                                             <select >
                                            <?php $opt_arr = explode('/',$cndn->opt); 
                                                    foreach($opt_arr as $opt)
                                                    {
                                                        echo '<option value="'.$opt.'">'.$opt.'</option>';
                                                    }
                                                 ?>
                                             </select>
                                             </td>
                                         
                                         
                                         
                                         
                                         
                                     </tr>
                                     <?php 
                                            }
                                     ?>
                                
                              </tbody>   
        
        
<?php        
        exit;
    }    
    
    
    public function get_cndn(Request $request)
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
        
        $qr4 = "SELECT field_name,sub_field_name FROM `condition_master` ta
WHERE con_status='1' $whereRaw
order BY field_name,sub_field_name";
        $con_json           =   DB::select($qr4);  
        
        foreach($con_json as $set_con)
        {
            $set_con_master[$set_con->field_name][] = $set_con->sub_field_name;
        }
        
        foreach($set_con_master as $field_name=>$sub_field_name) { ?>
                                            
                                            
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""><?php echo $field_name; ?></label>
                                                        <select id="<?php echo $field_name; ?>" name="set_conditions[<?php echo $field_name; ?>]" class="form-control" >
                                                            <option value="">Select</option>
                                                            <?php 
                                                                foreach($sub_field_name as $sub)
                                                                {
                                                                    echo '<option value="'.$sub.'">'.$sub.'</option>';
                                                                }
                                                        ?>
                                                        </select>
                                                    </div>
                                                </div>    
<?php        
    }
    exit;
    }
    
    
}

