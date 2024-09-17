<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\StateMaster;
use App\TaggingMaster;
use App\ProductMaster;
use App\RegionMaster;
use App\BrandMaster;

use DB;
use Session;


class ReportController extends Controller
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
    
    public function export_job_report(Request $request)
    {
        $from_date = $request->input("from_date");
        $to_date = $request->input("to_date");
        $region = $request->input("region");
        $brand = $request->input("brand");
        $product = $request->input("product");
        $model = $request->input("model");
        $state = $request->input("state");
        $dealer = $request->input("asc_code");
        $number = $request->input("number");
        $call_status = $request->input("call_status");
        $mobno = $request->input("mobno");
        $report_type = $request->input("report_type");
        
        $UserType = Session::get('UserType');
        
        $PermissionableField = Session::get('PermissionableField');
        
        //print_r($PermissionableField); exit;
        
        
        $UserId = Session::get('UserId');
        
        $whereUser = "";
        if($UserType!='Admin')
        {
            $whereUser = "and tm.center_id ='$center_id'";
        }
        
        $from_date              =   date('Y-m-d',strtotime($from_date));
        $to_date              =   date('Y-m-d',strtotime($to_date));
        
        $qry = "where  date(tm.created_at) between '$from_date' and '$to_date' $whereUser";
        
        if(!empty($Region))
        {
            //$qry.=" and tm.state in ('DELHI') ";
        }
        if(!empty($brand))
        {
            $qry.=" and tm.brand ='$brand' ";
        }
        if(!empty($Model))
        {
            $qry.=" and tm.model ='$Model' ";
        }
        if(!empty($state))
        {
            $qry.=" and tm.state ='$state' ";
        }
        if(!empty($dealer))
        {
            $qry.=" and tm.dealer_name ='$dealer' ";
        }
        if(!empty($number))
        {
            $qry.=" and tm.product_serial_no ='$number' ";
        }
        if(!empty($call_status))
        {
            $qry.=" and tm.call_status ='$call_status' ";
        }
        if(!empty($mobno))
        {
            $qry.=" and tm.contact_no ='$mobno' ";
        }
        
        $html = "";
        $select = "SELECT * FROM tagging_master tm  $qry "; 
        $data = DB::select($select);
        
        $class = "";
        if($report_type=='export')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=open-call-report.xls");
            header("Pragma: no-cache");
            header("Expires: 0"); 
            
        }
        else
        {
            $class = 'class="table table-striped table-bordered"';
            $PermissionableField = array('Customer_Name'=>'Customer Name','Pincode'=>'Pincode',
                'Contact_No'=>'Contact No','State'=>'State','City'=>'City','email'=>'Email',
                'Product'=>'Product','call_status'=>'Call Status');
        }
        if(empty($data))
        {
          echo  $html = 'No Records Found'; exit;
        }
        else
        {
            $html .= '<table border="2" '.$class.'>';
                $html .= '<tr>';
                foreach($PermissionableField as $Label)
                {
                    $html .= "<th>$Label</th>";
                }
                $html .= "<th>Status</th>";
                $html .= '</tr>';  //exit;

                
                foreach($data as $record)
                {
                    $html .= '<tr>';
                    foreach($PermissionableField as $field=>$Label)
                    {
                        if($field == 'Contact_No' && $report_type=='view')
                        {
                            $html .= '<td><a href="report-case-view?TagId='.$record->TagId.'">'.$record->$field."</a></td>";
                        }
                        else
                        {
                            $html .= "<td>".$record->$field."</td>"; 
                        }
                    }
                    $html .= "<td>".$record->final_status."</td>";
                    $html .= '</tr>'; 
                }
                
                
                
           echo $html .= '</table>'; exit;
        }
        
    }   
    
    public function export_pending_report(Request $request)
    {
        $from_date = $request->input("from_date");
        $to_date = $request->input("to_date");
        $Region = $request->input("Region");
        $brand = $request->input("brand");
        $state = $request->input("state");
        $pending = $request->input("pending");
        $call_status = $request->input("call_status");
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $report_type = $request->input("report_type"); 
        $whereUser = "";
        
        $VendorPincode=Session::get('pincode');
        $whereUser = "";
        if($UserType!='Admin')
        {
            $whereUser = "and tm.pincode in ('$VendorPincode')";
        }
        
        $from_date              =   date('Y-m-d',strtotime($from_date));
        $to_date              =   date('Y-m-d',strtotime($to_date));
        
        
        $qry = "where 1=1 $whereUser and date(tm.created_at) between '$from_date' and '$to_date' ";
        
        $PermissionableField = Session::get('PermissionableField');
        
        if(!empty($Region) && $Region!='All')
        {
            //$qry.=" and tm.state in ('DELHI') ";
        }
        if(!empty($brand))
        {
            $qry.=" and tm.brand ='$brand' ";
        }
        
        if(!empty($state))
        {
            $qry.=" and tm.state ='$state' ";
        }
        
        if(!empty($pending))
        {
            $qry.=" and tm.case_close is null ";
        }
        if(!empty($call_status))
        {
            $qry.=" and tm.call_status ='$call_status' ";
        }
        
        $select = "SELECT *,
            if(case_close='1','Close',
            IF(se_id IS NULL,'Allocation Pending',
	IF(case_tag=0,'Field Pending',
		IF(ob_date IS NULL,'Audit Pending',
			IF(call_status='Part Pending' ,'Dispatch Pending',
				IF(call_status='RMA','Approval Pending','close') ))))) final_status FROM tagging_master tm $qry"; 
        $data = DB::select($select);
        
         $class = "";
        if($report_type=='export')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=export-job.xls");
            header("Pragma: no-cache");
            header("Expires: 0"); 
        }
        else
        {
            $class = 'class="table table-striped table-bordered"';
            $PermissionableField = array('Customer_Name'=>'Customer Name',
                'Pincode'=>'Pincode','Contact_No'=>'Contact No','State'=>'State',
                'City'=>'City','email'=>'Email','Product'=>'Product','call_status'=>'Call Status');
        }
        
        if(empty($data))
        {
          echo  $html = 'No Records Found'; exit;
        }
        else
        {
            $html .= '<table border="2" '.$class.'>';
                $html .= '<tr>';
                foreach($PermissionableField as $Label)
                {
                    $html .= "<th>$Label</th>";
                }
                $html .= "<th>Status</th>";
                $html .= '</tr>';  //exit;

                
                foreach($data as $record)
                {
                    $html .= '<tr>';
                    foreach($PermissionableField as $field=>$Label)
                    {
                        if($field == 'Contact_No' && $report_type=='view')
                        {
                            $html .= '<td><a href="report-case-view?TagId='.$record->TagId.'" target="_blank">'.$record->$field."</a></td>";
                        }
                        else
                        {
                            $html .= "<td>".$record->$field."</td>";
                        }
                    }
                    $html .= "<td>".$record->final_status."</td>";
                    $html .= '</tr>'; 
                }
                
                
                
            echo $html .= '</table>'; exit;
        }
        
    }   
    
    public function index()
    {
        $state_json           =   StateMaster::whereRaw("country_id='1'")->orderByRaw('state_name ASC')->get(); 
        $state_master = json_decode($state_json);
        
        $region_master = RegionMaster::whereRaw("region_status='1'")->get();
        $brand_master = BrandMaster::whereRaw("brand_status='1'")->get();
        
        
        return view('open-calls-report')
                ->with('region_master',$region_master)
                ->with('brand_master',$brand_master)
                ->with('state_master',$state_master);
    }
    
    public function view_case(Request $request)
    {
        $TagId = $request->input('TagId'); 
        $data_json = TaggingMaster::where("TagId",$TagId)->first();
        $data = json_decode($data_json,true);
        
        
        $ProductMaster_json = ProductMaster::whereRaw("product_status='1'")->get();
        $ProductMaster = json_decode($ProductMaster_json,true);
        //print_r($data); exit;
        return view('report-case-view')
                ->with('data',$data)
                ->with('TagId',$TagId)
                ->with('ProductMaster',$ProductMaster);
    }
    
    public function report_pending()
    {
        $state_json           =   StateMaster::whereRaw("country_id='1'")->orderByRaw('state_name ASC')->get(); 
        $state_master = json_decode($state_json);
        return view('report-pending1')->with('state_master',$state_master);
    }
    
}

