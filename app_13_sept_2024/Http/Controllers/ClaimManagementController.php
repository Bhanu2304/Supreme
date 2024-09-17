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
use App\RegionMaster;
use App\ServiceCenter;
use App\ClosureCode;
use App\Disperse;
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Facades\Excel;

class ClaimManagementController extends Controller
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
        Session::put("page-title","Completed Job");
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
    
        $Center_Id = Auth::user()->table_id;

        $brand = $request->input('brand');
        $product_category = $request->input('product_category');
        $product = $request->input('product');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $ticket_no = $request->input('ticket_no');
        $job_no = $request->input('job_no');
        $center_name = $request->input('center_name');
        $closure_code = $request->input('closure_code');
        $service_type = $request->input('service_type');
        $claim_status = $request->input('job_status');
        
        $whereTag = "";

        if(!empty($ticket_no))
        {
            $whereTag .= " and tm.ticket_no='$ticket_no'";

        }

        if(!empty($job_no))
        {
            $whereTag .= " and tm.job_no='$job_no'";

        }
        
        if(!empty($brand) && $brand != "All")
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

        if(!empty($center_name) && $center_name != "All")
        {
            $whereTag .= " and tm.center_id='$center_name'";
        }

        if(!empty($closure_code))
        {
            $whereTag .= " and tm.closure_codes='$closure_code'";
        }

        if(!empty($service_type))
        {
            $whereTag .= " and tm.service_type='$service_type'";
        }

        if(!empty($claim_status))
        {
            $whereTag .= " and tm.claim_status='$claim_status'";
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
            $query = "SELECT tm.*,dm.dist_name,tsc.center_name FROM tagging_master tm  
            inner join tagging_damage_part tdp ON tm.tagid = tdp.tag_id 
            LEFT JOIN tbl_service_centre tsc ON tm.center_id = tsc.center_id 
            LEFT JOIN district_master dm ON tm.dist_id = dm.dist_id WHERE job_reject='0' and job_accept='1' AND closure_codes IS NOT NULL";
          
            $req_arr  =   DB::select($query);
        }
        else 
        {   #echo "SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id where 1=1 $whereTag";die;
            #$req_arr  =   DB::select("SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id where 1=1 $whereTag");
            $query = "SELECT tm.*,dm.dist_name,tsc.center_name FROM tagging_master tm  
            inner join tagging_damage_part tdp ON tm.tagid = tdp.tag_id
            LEFT JOIN tbl_service_centre tsc ON tm.center_id = tsc.center_id 
            LEFT JOIN district_master dm ON tm.dist_id = dm.dist_id WHERE job_reject='0' and job_accept='1' $whereTag AND closure_codes IS NOT NULL";
            $req_arr  =   DB::select($query);
        }
       

        $whereTag = base64_encode(http_build_query($request->all()));

        $qr2 = "SELECT tsc.center_id,center_name,asc_code FROM tbl_service_centre  tsc
            INNER JOIN users us ON tsc.email_id = us.email
            WHERE sc_status='1' $center_qr order by center_name"; 
        $asc_master           =   DB::select($qr2); 

        $closure_qr = "SELECT * FROM `closure_codes` WHERE STATUS='1' order by closure_code"; 
        $closure_master           =   DB::select($closure_qr); 

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

            $closure_code = $req->closure_codes;
            $closure_det = ClosureCode::whereRaw("id='$closure_code'")->first();
            $req->closure_name = $closure_det->closure_code;
            $req->closure_amount = $closure_det->amount;
            
        }


        $category_master = DB::select("SELECT pm.product_category_id,pm.category_name FROM product_category_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND category_status='1'
        WHERE bm.brand_id='$brand'");

        $qry = "SELECT pm.product_id,pm.product_name FROM product_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND product_status='1'
        WHERE  pm.brand_id='$brand' and product_category_id='$product_category'";#die;
        $model_master = DB::select($qry);
        
        
        
        $url = $_SERVER['APP_URL'].'/claim-complete-job';
        return view('claim-complete-job')
                ->with('brand_arr', $brand_arr)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('brand_id',$brand)
                ->with('closure_master',$closure_master)
                ->with('asc_master',$asc_master)
                ->with('req_arr', $req_arr)
                ->with('category_master', $category_master)
                ->with('model_master', $model_master)
                ->with('url', $url)
                ->with('product_category', $product_category)
                ->with('product', $product)
                ->with('closure_code', $closure_code)
                ->with('service_type', $service_type)
                ->with('claim_status', $claim_status)
                ->with('pincode', $pincode)
                ->with('job_no', $job_no)
                ->with('ticket_no', $ticket_no)
                ->with('back_url','se-raise-po')
                ->with('whereTag',$whereTag);
                
    }

    public function retainership(Request $request)
    {
        Session::put("page-title","Retainer Ship");

        $region_json  =   RegionMaster::orderByRaw('region_name ASC')->get();
        $region_master = json_decode($region_json);

        $qr2 = "SELECT tsc.center_id,center_name,asc_code FROM tbl_service_centre  tsc
            INNER JOIN users us ON tsc.email_id = us.email
            WHERE sc_status='1' $center_qr order by center_name"; 
        $asc_master           =   DB::select($qr2); 

        $url = $_SERVER['APP_URL'].'/retainership';
        return view('retainer-ship')
        ->with('asc_master',$asc_master)
        ->with('region_master',$region_master);

    }


    public function get_asc_retainer(Request $request)
    {
        $region_id = $request->input('region_id');
        $center_id = $request->input('center_id');
        $type = $request->input('type');

        
        $qry = "";
    
        if(!empty($region_id))
        {
            
            $qry .= " and tsc.region= '$region_id'";
        }

        if(!empty($center_id) && $center_id != 'All')
        {
            
            $qry .= " and tsc.center_id= '$center_id'";
        }

        $select = "SELECT tsc.center_id,tsc.center_name,tsc.asc_code,tsc.retainership_amount,rm.region_name,sm.state_name FROM `tbl_service_centre` tsc 
                LEFT JOIN `region_master` rm ON tsc.region=rm.region_id
                LEFT JOIN `state_master` sm ON tsc.state=sm.state_id WHERE 1=1 $qry order by tsc.center_name";
    
        $retainer_master = DB::select($select);
        
        if(empty($retainer_master))
        {
            echo 'No Records Found'; exit;
        }

        if($type == "Search")
        {
            echo '<table id="table1" class="table table-striped table-bordered" style="width:100%">';
            
            echo '<tr>';
                echo '<th>Sr. No.</th>';
                
                echo '<th>Zone</th>';
                echo '<th>State</th>';
                echo '<th>ASC Name</th>';
                echo '<th>ASC Code</th>';
                echo '<th>Retainership Amount</th>';
                echo '<th>Action</th>';
            echo '</tr>';
            $srno=1;
            foreach($retainer_master as $pin)
            {
                echo '<tr>';
                    echo '<td>'.$srno++.'</td>';
                    echo '<td>'.$pin->region_name.'</td>';
                    echo '<td>'.$pin->state_name.'</td>';
                    echo '<td>'.$pin->center_name.'</td>';
                    echo '<td>'.$pin->asc_code.'</td>';
                    echo '<td><input type="text" name="retainership_amount" onkeypress="return checkNumber(this.value,event)" maxlength="6" id="retainership_amount" value="'.$pin->retainership_amount.'"></td>';
                    $center_id = $pin->center_id;
                    echo '<td><a href="#" onclick="update_amount('."'$center_id'".')">Update</a></td>';
                echo '</tr>';
            }
            echo '</table>';
            exit;
        }else{

            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=retainer-ship.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            ob_start();

            echo '<table id="table1" class="table table-striped table-bordered" style="width:100%" border="1">';
            
            echo '<tr>';
                echo '<th>Sr. No.</th>';
                echo '<th>Zone</th>';
                echo '<th>State</th>';
                echo '<th>ASC Name</th>';
                echo '<th>ASC Code</th>';
                echo '<th>Retainership Amount</th>';
            echo '</tr>';

            $srno=1;
            foreach($retainer_master as $pin)
            {
                echo '<tr>';
                    echo '<td>'.$srno++.'</td>';
                    echo '<td>'.$pin->region_name.'</td>';
                    echo '<td>'.$pin->state_name.'</td>';
                    echo '<td>'.$pin->center_name.'</td>';
                    echo '<td>'.$pin->asc_code.'</td>';
                    echo '<td>'.$pin->retainership_amount.'</td>';
                echo '</tr>';
            }
            echo '</table>';
            $fileData = ob_get_clean();
            echo $fileData;
            exit;
        }
            
        
    }

    public function update_amount(Request $request)
    {

        $center_id = $request->input("center_id");
        $amount = $request->input("retainership_amount");

        $taggingArr['retainership_amount']=$amount;
        if(ServiceCenter::whereRaw("center_id='$center_id'")->update($taggingArr))
        {
            echo "1";
        }
        else
        {
            echo '0';
        }
        exit;
    }

    public function claim_settlement(Request $request)
    {
        Session::put("page-title","Claim Settlement");


        $region_json  =   RegionMaster::orderByRaw('region_name ASC')->get();
        $region_master = json_decode($region_json);

        $qr2 = "SELECT tsc.center_id,center_name,asc_code FROM tbl_service_centre  tsc
            INNER JOIN users us ON tsc.email_id = us.email
            WHERE sc_status='1' $center_qr order by center_name"; 
        $asc_master           =   DB::select($qr2); 


        $url = $_SERVER['APP_URL'].'/claim-settlement';
        return view('claim-settlement')
            ->with('region_master',$region_master)
            ->with('asc_master',$asc_master);
    }


    public function get_job_claim(Request $request)
    {
        $region_id = $request->input('region_id');
        $center_id = $request->input('center_id');
        $type = $request->input('type');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        
        $qry = "";
    
        if(!empty($region_id) && $region_id!= 'All')
        {
            
            $qry .= " and tsc.region= '$region_id'";
        }

        if(!empty($center_id) && $center_id!= 'All')
        {
            
            $qry .= " and tsc.center_id= '$center_id'";
        }

        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $qry .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }

        $select = "SELECT tm.*,tsc.center_id,tsc.center_name,tsc.asc_code,tsc.retainership_amount,rm.region_name,sm.state_name,cc.amount FROM tagging_master tm  
            LEFT JOIN tbl_service_centre tsc ON tm.center_id = tsc.center_id 
            LEFT JOIN `region_master` rm ON tsc.region=rm.region_id
            LEFT JOIN `closure_codes` cc ON tm.closure_codes=cc.id
            LEFT JOIN `state_master` sm ON tsc.state=sm.state_id WHERE claim_status='0' $qry order by tsc.center_name";

        
        $retainer_master = DB::select($select);
        
        if(empty($retainer_master))
        {
            echo 'No Records Found'; exit;
        }

        if($type == "Search")
        {
            echo '<table id="table1" class="table table-striped table-bordered" style="width:100%">';
            
            echo '<tr>';
                echo '<th>Sr. No.</th>';
                echo '<th>Zone</th>';
                echo '<th>State</th>';
                echo '<th>ASC Name</th>';
                echo '<th>ASC Code</th>';
                echo '<th>Retainership Amount</th>';
                echo '<th>Ticket No.</th>';
                echo '<th>Job Amount</th>';
                echo '<th>Total Amount</th>';
                echo '<th>Final Amount</th>';
                echo '<th>Action</th>';
            echo '</tr>';
            $srno=1;
            foreach($retainer_master as $pin)
            {
                echo '<tr>';
                    echo '<td>'.$srno++.'</td>';
                    echo '<td>'.$pin->region_name.'</td>';
                    echo '<td>'.$pin->state_name.'</td>';
                    echo '<td>'.$pin->center_name.'</td>';
                    echo '<td>'.$pin->asc_code.'</td>';
                    echo '<td>'.$pin->retainership_amount.'</td>';
                    echo '<td>'.$pin->ticket_no.'</td>';
                    echo '<td>'.$pin->Brand.' - '.$pin->amount.'</td>';
                    #$total_amount = ($pin->retainership_amount + $pin->amount);
                    
                    if ($pin->retainership_amount > $pin->amount)
                    {
                        echo '<td>'.$pin->retainership_amount.'</td>';

                    }elseif ($pin->amount > $pin->retainership_amount)
                    {
                        echo '<td>'.$pin->amount.'</td>';
                    }
                    echo '<td>'.$pin->amount.'</td>';
                    $center_id = $pin->center_id;
                    echo '<td>';
                    //<a href="#" onclick="update_amount('."'$center_id'".')">Generate</a>
                    echo '<a href="view-claim-pdf?TagId='.$pin->TagId.'" target="_blank" onclick="return showAlert()">Generate </a>';
                    #echo '<a href="generate-pdf?TagId='.$pin->TagId.'">PDF </a>';
                    echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
            exit;
        }else{

        }
    }

    public function claim_pending(Request $request)
    {
    
        Session::put("page-title","Pending For Approval");
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
    
        $Center_Id = Auth::user()->table_id;

        $brand = $request->input('brand');
        $product_category = $request->input('product_category');
        $product = $request->input('product');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        
        $whereTag = "";
        
        if(!empty($brand) && $brand != "All")
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
            $query = "SELECT tm.*,dm.dist_name,tsc.center_name FROM tagging_master tm  
            -- inner JOIN tagging_damage_part tdp ON tm.tagid = tdp.tag_id 
            LEFT JOIN tbl_service_centre tsc ON tm.center_id = tsc.center_id 
            LEFT JOIN district_master dm ON tm.dist_id = dm.dist_id WHERE job_reject='0' and job_accept='1' and claim_status='1' AND closure_codes IS NOT NULL order  by created_at desc";
            #echo $query;die;
            $req_arr  =   DB::select($query);
        }
        else {
            $query = "SELECT tm.*,dm.dist_name,tsc.center_name FROM tagging_master tm  
            -- inner JOIN tagging_damage_part tdp ON tm.tagid = tdp.tag_id 
            LEFT JOIN tbl_service_centre tsc ON tm.center_id = tsc.center_id 
            LEFT JOIN district_master dm ON tm.dist_id = dm.dist_id WHERE job_reject='0' and job_accept='1' and claim_status='1' AND closure_codes IS NOT NULL $whereTag";
            $req_arr  =   DB::select($query);
            #echo $query;die;
            #$req_arr  =   DB::select("SELECT * FROM inward_inventory ii LEFT JOIN inward_inventory_particulars iip ON ii.inw_id = iip.inw_id where 1=1 $whereTag");
        }
       
        #print_r($req_arr);die;
        $whereTag = base64_encode(http_build_query($request->all()));

        $qr2 = "SELECT tsc.center_id,center_name,asc_code FROM tbl_service_centre  tsc
            INNER JOIN users us ON tsc.email_id = us.email
            WHERE sc_status='1' $center_qr order by center_name"; 
        $asc_master           =   DB::select($qr2); 

        $closure_qr = "SELECT * FROM `closure_codes` WHERE STATUS='1' order by closure_code"; 
        $closure_master           =   DB::select($closure_qr); 

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

            $closure_code = $req->closure_codes;
            $closure_det = ClosureCode::whereRaw("id='$closure_code'")->first();
            $req->closure_name = $closure_det->closure_code;
            $req->closure_amount = $closure_det->amount;
            
        }


        $category_master = DB::select("SELECT pm.product_category_id,pm.category_name FROM product_category_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND category_status='1'
        WHERE bm.brand_id='$brand'");


        $qry = "SELECT pm.product_id,pm.product_name FROM product_master pm 
        INNER JOIN brand_master bm ON pm.brand_id = bm.brand_id AND brand_status='1' AND product_status='1'
        WHERE  pm.brand_id='$brand' and product_category_id='$product_category'";#die;
        $model_master = DB::select($qry);
        
        
        
        $url = $_SERVER['APP_URL'].'/claim-pending';
        return view('claim-approval-job')
                ->with('brand_arr', $brand_arr)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('brand_id',$brand)
                ->with('closure_master',$closure_master)
                ->with('asc_master',$asc_master)
                ->with('req_arr', $req_arr)
                ->with('category_master', $category_master)
                ->with('model_master', $model_master)
                ->with('url', $url)
                ->with('product_category', $product_category)
                ->with('product', $product)
                ->with('job_status', $job_status)
                ->with('job_no', $job_no)
                ->with('ticket_no', $ticket_no)
                ->with('whereTag',$whereTag);
                
    }

    
    public function claim_approval(Request $request)
    {

        $tag_id = $request->input("tagId");

        $taggingArr['claim_status']='0';
        $taggingArr['claim_gen_date']=date('Y-m-d H:i:s');
        $taggingArr['claim_gen_by']=Auth::User()->id;

        
        if(TaggingMaster::whereRaw("TagId='$tag_id'")->update($taggingArr))
        {
            echo "1";
        }
        else
        {
            echo '0';
        }
        exit;
    }

    
    public function claim_reject(Request $request)
    {
        $tag_id = $request->input("tag_id");
        $reject_type = $request->input("reject_type");
        $remarks = $request->input("remarks");

        if($reject_type == "Correction")
        {
            $taggingArr['claim_status']='2'; 
            
        }else{
            $taggingArr['claim_status']='3'; 
        }
        
        $taggingArr['claim_remarks']=$remarks;
        $taggingArr['claim_rej_date']=date('Y-m-d H:i:s');
        $taggingArr['claim_rej_by']=Auth::User()->id;
        
        if(TaggingMaster::whereRaw("TagId='$tag_id'")->update($taggingArr))
        {
            echo "Claim ".$reject_type." Succesfully";
        }
        else
        {
            echo 'Please Try Again';
        }
        exit;
    }

    public function get_disperse_claim(Request $request)
    {
        $region_id = $request->input('region_id');
        $center_id = $request->input('center_id');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $disperse_on = $request->input('disperse_on');
        $transaction_id = $request->input('transaction_id');

        
        $qry = "";
    
        if(!empty($region_id) && $region_id != 'All')
        {
            
            $qry .= " and tsc.region= '$region_id'";
        }

        if(!empty($center_id) && $center_id != 'All')
        {
            
            $qry .= " and tsc.center_id= '$center_id'";
        }

        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $qry .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        if(!empty($transaction_id))
        {
            $qry .= " and td.transaction_id= '$transaction_id'";
        }

        $select = "SELECT tm.*,tsc.center_id,tsc.center_name,tsc.asc_code,tsc.retainership_amount,tsc.acc_no,tsc.ifsc,rm.region_name,sm.state_name,cc.amount,td.disperse_amount,td.disperse_date,td.transaction_id FROM tagging_master tm  
                LEFT JOIN tbl_service_centre tsc ON tm.center_id = tsc.center_id 
                LEFT JOIN `region_master` rm ON tsc.region=rm.region_id
                LEFT JOIN `closure_codes` cc ON tm.closure_codes=cc.id
                LEFT JOIN `tbl_disperse` td ON tm.TagId=td.tag_id 
                LEFT JOIN `state_master` sm ON tsc.state=sm.state_id WHERE claim_status='0' $qry order by tsc.center_name";

        
        $retainer_master = DB::select($select);
        
        if(empty($retainer_master))
        {
            echo 'No Records Found'; exit;
        }

        echo '<table id="table1" class="table table-striped table-bordered" style="width:100%">';
        
        echo '<tr>';
            echo '<th>Sr. No.</th>';
            
            echo '<th>Zone</th>';
            echo '<th>State</th>';
            echo '<th>ASC Name</th>';
            echo '<th>ASC Code</th>';
            echo '<th>Account No.</th>';
            echo '<th>IFSC Code.</th>';
            echo '<th>Retainership Amount</th>';
            echo '<th>Ticket No.</th>';
            echo '<th>Job Amount</th>';
            echo '<th>Total Amount</th>';
            echo '<th>Final Amount</th>';
            echo '<th>Amount Disperse</th>';
            echo '<th>Disperse Date</th>';
            echo '<th>Transaction Id</th>';
            echo '<th>Action</th>';
        echo '</tr>';
        $srno=1;
        foreach($retainer_master as $pin)
        {
            echo '<tr>';
                echo '<td>'.$srno++.'</td>';
                echo '<td>'.$pin->region_name.'</td>';
                echo '<td>'.$pin->state_name.'</td>';
                echo '<td>'.$pin->center_name.'</td>';
                echo '<td>'.$pin->asc_code.'</td>';
                echo '<td>'.$pin->acc_no.'</td>';
                echo '<td>'.$pin->ifsc.'</td>';
                echo '<td>'.$pin->retainership_amount.'</td>';
                echo '<td>'.$pin->Brand.' - '.$pin->ticket_no.'</td>';
                echo '<td>'.$pin->amount.'</td>';
                #$total_amount = ($pin->retainership_amount + $pin->amount);
        
                $final_amount = 0 ;
                if ($pin->retainership_amount > $pin->amount) {
                    $final_amount = $pin->retainership_amount;
                    #echo '<td>'.$pin->retainership_amount.'</td>';
                } elseif ($pin->amount > $pin->retainership_amount) {
                    $final_amount = $pin->amount;
                    #echo '<td>'.$total_amount.'</td>';
                }
                echo '<td>'.$pin->amount.'</td>';
                echo '<td>'.$final_amount.'</td>';
                $center_id = $pin->center_id;
                $tag_id = $pin->TagId;
               
                if(empty($pin->disperse_amount))
                {
                    $disperse_amount = $final_amount;
                }else{
                    $disperse_amount = $pin->disperse_amount;
                }
                echo '<td>';
                echo '<input type="text" name="disperse_amount" onkeypress="return checkNumber(this.value,event)" maxlength="6" id="disperse_amount" value="'.$disperse_amount.'">';
                echo '</td>';
                echo '<td>';
                echo '<input type="date" name="disperse_date"  id="disperse_date" value="'.$pin->disperse_date.'">';
                echo '</td>';
                echo '<td>';
                echo '<input type="text" name="transaction_id"  id="transaction_id_input" value="'.$pin->transaction_id.'">';
                echo '</td>';
                echo '<td>';
                echo '<a href="#" onclick="update_amount('."'$tag_id'".')">Update</a>';
                echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
        
    }

    public function claim_disperse(Request $request)
    {
     
        $tag_id = $request->input("tag_id");
        $disperse_amount = $request->input("disperse_amount");
        $disperse_date = $request->input("disperse_date");
        $transaction_id = $request->input("transaction_id");
        $user_id = Auth::User()->id;

        $closure_det = Disperse::where('tag_id', $tag_id)->first();

        if($closure_det) {
            
            $closure_det->disperse_amount = $disperse_amount;
            $closure_det->disperse_date = $disperse_date;
            $closure_det->transaction_id = $transaction_id;
            $closure_det->created_by = $user_id;
            $closure_det->save(); 
        } else {
            
            $closure_det = new Disperse();
            $closure_det->tag_id = $tag_id;
            $closure_det->disperse_amount = $disperse_amount;
            $closure_det->disperse_date = $disperse_date;
            $closure_det->transaction_id = $transaction_id;
            $closure_det->created_by = $user_id;
            $closure_det->save(); 
        }
        echo "1";
        exit;

    }

    
    public function disperse_export(Request $request)
    {
        $region_id = $request->input('region_id');
        $center_id = $request->input('center_id');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $disperse_on = $request->input('disperse_on');
        $transaction_id = $request->input('transaction_id');

        
        $qry = "";
    
        if(!empty($region_id))
        {
            
            $qry .= " and tsc.region= '$region_id'";
        }

        if(!empty($center_id))
        {
            
            $qry .= " and tsc.center_id= '$center_id'";
        }

        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $qry .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        if(!empty($transaction_id))
        {
            $qry .= " and td.transaction_id= '$transaction_id'";
        }

        $select = "SELECT tm.*,tsc.center_id,tsc.center_name,tsc.asc_code,tsc.retainership_amount,tsc.acc_no,tsc.ifsc,rm.region_name,sm.state_name,cc.amount,td.disperse_amount,td.disperse_date,td.transaction_id FROM tagging_master tm  
                LEFT JOIN tbl_service_centre tsc ON tm.center_id = tsc.center_id 
                LEFT JOIN `region_master` rm ON tsc.region=rm.region_id
                LEFT JOIN `closure_codes` cc ON tm.closure_codes=cc.id
                LEFT JOIN `tbl_disperse` td ON tm.TagId=td.tag_id 
                LEFT JOIN `state_master` sm ON tsc.state=sm.state_id WHERE claim_status='0' $qry order by tsc.center_name";

        
        $retainer_master = DB::select($select);
        
        if(empty($retainer_master))
        {
            echo 'No Records Found'; exit;
        }

        echo '<table id="table1" class="table table-striped table-bordered" border="1" style="width:100%">';
        
        echo '<tr>';
            echo '<th>Sr. No.</th>';
            echo '<th>Zone</th>';
            echo '<th>State</th>';
            echo '<th>ASC Name</th>';
            echo '<th>ASC Code</th>';
            echo '<th>Account No.</th>';
            echo '<th>IFSC Code.</th>';
            echo '<th>Retainership Amount</th>';
            echo '<th>Ticket No.</th>';
            echo '<th>Job Amount</th>';
            echo '<th>Total Amount</th>';
            echo '<th>Final Amount</th>';
            echo '<th>Amount Disperse</th>';
            echo '<th>Disperse Date</th>';
            echo '<th>Transaction Id</th>';
        echo '</tr>';
        $srno=1;
        foreach($retainer_master as $pin)
        {
            echo '<tr>';
                echo '<td>'.$srno++.'</td>';
                echo '<td>'.$pin->region_name.'</td>';
                echo '<td>'.$pin->state_name.'</td>';
                echo '<td>'.$pin->center_name.'</td>';
                echo '<td>'.$pin->asc_code.'</td>';
                echo '<td>'.$pin->acc_no.'</td>';
                echo '<td>'.$pin->ifsc.'</td>';
                echo '<td>'.$pin->retainership_amount.'</td>';
                echo '<td>'.$pin->ticket_no.'</td>';
                echo '<td>'.$pin->amount.'</td>';
                #$total_amount = ($pin->retainership_amount + $pin->amount);
                

                $final_amount = 0 ;
                if ($pin->retainership_amount > $pin->amount) {
                    $final_amount = $pin->retainership_amount;
                    #echo '<td>'.$pin->retainership_amount.'</td>';
                } elseif ($pin->amount > $pin->retainership_amount) {
                    $final_amount = $pin->amount;
                    #echo '<td>'.$pin->amount.'</td>';
                }
                echo '<td>'.$pin->amount.'</td>';
                echo '<td>'.$final_amount.'</td>';
                $center_id = $pin->center_id;
                $tag_id = $pin->TagId;
               
                if(empty($pin->disperse_amount))
                {
                    $disperse_amount = $final_amount;
                }else{
                    $disperse_amount = $pin->disperse_amount;
                }
                echo '<td>';
                echo $disperse_amount;
                echo '</td>';
                echo '<td>';
                echo $pin->disperse_date;
                echo '</td>';
                echo '<td>';
                echo $pin->transaction_id;
                echo '</td>';
              
            echo '</tr>';
        }
        echo '</table>';
        exit;
        
    }


    public function get_asc_name(Request $request)
    {

        $region_id = $request->input('region_id');
        
        $str = "";
        if($region_id!='All')
        {
            $str = " and tsc.region='$region_id'";
        }

        $qr2 = "SELECT tsc.center_id,center_name,asc_code FROM tbl_service_centre  tsc
                INNER JOIN users us ON tsc.email_id = us.email WHERE sc_status='1' $str order by center_name"; 
        $asc_master           =   DB::select($qr2);
        
        
        if(empty($asc_master))
        {
            echo '<option value="">No Asc Name</option>'; exit;
        }
        echo '<option value="">Select</option>'; 
        echo '<option value="All">All</option>'; 
        
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


