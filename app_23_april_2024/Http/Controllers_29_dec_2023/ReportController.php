<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\StateMaster;
use App\TaggingMaster;
use App\ProductMaster;
use App\RegionMaster;
use App\BrandMaster;
use App\ServiceCenter;
use Auth;
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
    
    public function po_report()
    {
        $UserType = Session::get('UserType');
        $state_json           =   StateMaster::whereRaw("country_id='1'")->orderByRaw('state_name ASC')->get(); 
        $state_master = json_decode($state_json);
        
        //$sc_master = ServiceCenter::whereRaw("1=1")->get();
        $Center_Id = Auth::user()->table_id;
        $whereUser = "and 1=1 ";
        if($UserType!='Admin')
        {
            $whereUser .= " and se.center_id='$Center_Id'";

        }

       $qr1 = "select center_id,center_name from tbl_service_centre se
        WHERE  sc_status='1' $whereUser";
        $sc_master           =   DB::select($qr1); 

        $brand_master = BrandMaster::whereRaw("brand_status='1'")->get();
        
        
        return view('po-report')
                ->with('sc_master',$sc_master)
                ->with('brand_master',$brand_master)
                ->with('state_master',$state_master);
    }
    
    public function export_po_report(Request $request)
    {
        $from_date = $request->input("from_date");
        $to_date = $request->input("to_date");
        $center_id = $request->input("center_id");
        $brand_id = $request->input("brand_id");
       
//        $product = $request->input("product");
//        $model = $request->input("model");
//        $state = $request->input("state");
//        $dealer = $request->input("asc_code");
//        $number = $request->input("number");
//        $call_status = $request->input("call_status");
//        $mobno = $request->input("mobno");
        $report_type = $request->input("report_type");
//        
        $UserType = Session::get('UserType');
//        
//        $PermissionableField = Session::get('PermissionableField');
//        
//        //print_r($PermissionableField); exit;
//        
//        
//        $UserId = Session::get('UserId');
        
        $whereUser = "";
        $Center_Id = Auth::user()->table_id;
        if($UserType!='Admin')
        {
            $whereUser = "and scr.center_id ='$Center_Id'";
        }
        
        $from_date              =   date('Y-m-d',strtotime($from_date));
        $to_date              =   date('Y-m-d',strtotime($to_date));
        
        $qry = "where  date(scr.req_date) between '$from_date' and '$to_date' ";
        
        if(!empty($center_id))
        {
            $qry.=" and scr.center_id ='$center_id' ";
        }
        if(!empty($brand_id))
        {
            $qry.=" and scr.brand_id ='$brand_id' ";
        }
//        if(!empty($Model))
//        {
//            $qry.=" and tm.model ='$Model' ";
//        }
//        if(!empty($state))
//        {
//            $qry.=" and tm.state ='$state' ";
//        }
//        if(!empty($dealer))
//        {
//            $qry.=" and tm.dealer_name ='$dealer' ";
//        }
//        if(!empty($number))
//        {
//            $qry.=" and tm.product_serial_no ='$number' ";
//        }
//        if(!empty($call_status))
//        {
//            $qry.=" and tm.call_status ='$call_status' ";
//        }
//        if(!empty($mobno))
//        {
//            $qry.=" and tm.contact_no ='$mobno' ";
//        }
        
        $html = "";
       $select = "SELECT scr.*,srip.*,bm.brand_name,cm.category_name,pm.product_name,mm.model_name FROM `sc_request_inventory` scr
INNER JOIN `sc_request_inventory_particulars` srip ON scr.req_id= srip.req_id
INNER JOIN brand_master bm ON scr.brand_id = bm.brand_id
INNER JOIN `product_category_master` cm ON scr.product_category_id = cm.product_category_id
INNER JOIN `product_master` pm ON scr.product_id = pm.product_id
INNER JOIN `model_master` mm ON scr.model_id = mm.model_id   $qry $whereUser";
        
        //echo $select;exit;
        
        $data_arr = DB::select($select);
        
        $class = "";
        if($report_type=='export')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=po-report.xls");
            header("Pragma: no-cache");
            header("Expires: 0"); 
            
        }
        else
        {
            $class = 'class="table table-striped table-bordered"';
            
        }
        if(empty($data_arr))
        {
          echo  $html = 'No Records Found'; exit;
        }
        else
        {
            
            $html .= '<table border="2" '.$class.'>';
                $html .= '<tr>';
                $html .= "<th>SR No.</th>";
                $html .= "<th>Date</th>";
                $html .= "<th>PO No.</th>";
                $html .= "<th>Brand</th>";
                $html .= "<th>Product Category</th>";
                $html .= "<th>Model</th>";
                $html .= "<th>Model No.</th>";
                $html .= "<th>Part Code</th>";
                $html .= "<th>Part Name</th>";
                $html .= "<th>PO Type</th>";
                $html .= "<th>No. of Parts</th>";
                $html .= "<th>Color</th>";
                $html .= '</tr>';  //exit;

                    $i=1;
                foreach($data_arr as $record)
                {
                    $html .= '<tr>';
                    $html .= "<th>".$i++."</th>";
                    $html .= "<th>".$record->req_date."</th>";
                    $html .= "<th>".$record->req_no."</th>";
                    $html .= "<th>".$record->brand_name."</th>";
                    $html .= "<th>".$record->category_name."</th>";
                    $html .= "<th>".$record->product_name."</th>";
                    $html .= "<th>".$record->model_name."</th>";
                    $html .= "<th>".$record->part_no."</th>";
                    $html .= "<th>".$record->part_name."</th>";
                    $html .= "<th>".$record->po_type."</th>";
                    $html .= "<th>".$record->qty_approve."</th>";
                    $html .= "<th>".$record->color."</th>";
                    $html .= '</tr>';
                }
                
                
                
           echo $html .= '</table>'; exit;
        }
        
    }  
    
    public function mis_report()
    {
        $UserType = Session::get('UserType');
        $state_json           =   StateMaster::whereRaw("country_id='1'")->orderByRaw('state_name ASC')->get(); 
        $state_master = json_decode($state_json);
        
        //$sc_master = ServiceCenter::whereRaw("1=1")->get();
        $Center_Id = Auth::user()->table_id;
        $whereUser = "and 1=1 ";
        if($UserType!='Admin')
        {
            $whereUser .= " and se.center_id='$Center_Id'";

        }

       $qr1 = "select center_id,center_name from tbl_service_centre se
        WHERE  sc_status='1' $whereUser";
        $sc_master           =   DB::select($qr1); 

        $brand_master = BrandMaster::whereRaw("brand_status='1'")->get();
        
        
        return view('mis-report')
                ->with('sc_master',$sc_master)
                ->with('brand_master',$brand_master)
                ->with('state_master',$state_master);
    }

    public function export_mis_report(Request $request)
    {
        //print_r($request->all());die;
        $from_date = $request->input("from_date");
        $to_date = $request->input("to_date");

        $report_type = $request->input("report_type");
       
        $UserType = Session::get('UserType');

        
        $whereUser = "";
        $Center_Id = Auth::user()->table_id;
        // if($UserType!='Admin')
        // {
        //     $whereUser = "and scr.center_id ='$Center_Id'";
        // }
        
        $from_date              =   date('Y-m-d',strtotime($from_date));
        $to_date              =   date('Y-m-d',strtotime($to_date));
        
        $qry = "and DATE(tm.created_at) between '$from_date' and '$to_date' ";
        
        
        $html = "";
        $tag_data_qr = "select tm.*,center_name from  tagging_master tm  inner join tbl_service_centre sc on tm.center_id = sc.center_id where 1=1 $qry ";
        $data_arr = DB::select("$tag_data_qr");
    
        
        $class = "";
        if($report_type=='export')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=mis-report.xls");
            header("Pragma: no-cache");
            header("Expires: 0"); 
            
        }
        else
        {
            $class = 'class="table table-striped table-bordered"';
            
        }
        if(empty($data_arr))
        {
          echo  $html = 'No Records Found'; exit;
        }
        else
        {
            
            $html .= '<table border="2" '.$class.'>';
                $html .= '<tr>';
                $html .= "<th>Sr.No</th>";
                $html .= "<th>Ticket Created On</th>";
                $html .= "<th>Ticket Accept Date</th>";
                $html .= "<th>ASC Name</th>";
                $html .= "<th>State</th>";
                $html .= "<th>Ticket No.</th>";
                $html .= "<th>Job No.</th>";
                $html .= "<th>Customer Name</th>";
                $html .= "<th>Address</th>";
                $html .= "<th>Phone No.</th>";
                $html .= "<th>Pin Code</th>";
                
                $html .= "<th>Brand</th>";
                $html .= "<th>Product</th>";
                $html .= "<th>Model No.</th>";
                $html .= "<th>Serial No.</th>";
                $html .= "<th>Date of Purchase</th>";
                $html .= "<th>Warranty Card</th>";
                $html .= "<th>Invoice</th>";
                $html .= "<th>Problem Reported</th>";
                $html .= "<th>Warranty Category</th>";
                $html .= "<th>Service Type</th>";
                $html .= "<th>Part GST%</th>";
                $html .= "<th>Total Part Price</th>";
                $html .= "<th>Closure Date</th>";
                $html .= "<th>Job Status</th>";
                //$html .= "<th>Job PDF</th>";
                $html .= "<th>Invoice</th>";
                $html .= "<th>Delivery Date</th>";
                $html .= "<th>Remarks</th>";
                $html .= '</tr>';  //exit;

                    $i=1;
                foreach($data_arr as $record)
                {
                    $html .= '<tr>';
                    $html .= "<th>".$i++."</th>";
                    $html .= '<th>'.date('d-m-Y',strtotime($record->created_at)).'</th>';
                    $html .= '<th>'.date('d-m-Y',strtotime($record->job_accept_date)).'</th>';
                    $html .= '<th>'.$record->center_name.'</th>';
                    $html .= '<th>'.$record->State.'</th>';
                    $html .= '<th>'.$record->ticket_no.'</th>';
                    $html .= '<th>'.$record->job_no.'</th>';
                    $html .= '<th>'.$record->Customer_Name.'</th>';
                    $html .= '<th>'.$record->Customer_Address.'</th>';
                    $html .= '<th>'.$record->Contact_No.'</th>';
                    $html .= '<th>'.$record->Pincode.'</th>';
                    $html .= '<th>'.$record->Brand.'</th>';
                    $html .= '<th>'.$record->Product.'</th>';
                    $html .= '<th>'.$record->Model.'</th>';
                    $html .= '<th>'.$record->Serial_No.'</th>';
                    $html .= '<th>'.$record->Bill_Purchase_Date.'</th>';
                    $html .= '<th>'.$record->warranty_card.'</th>';
                    $html .= '<th>'.$record->invoice.'</th>';
                    $html .= '<th>'.$record->report_fault.'</th>';
                    $html .= '<th>'.$record->warranty_type.'</th>';
                    $html .= '<th>'.$record->service_type.'</th>';
                    $html .= '<th>'.$record->total_cgst.'</th>';
                    $html .= '<th>'.$record->grand_total.'</th>';
                    $html .= '<th>'.$record->case_close_date.'</th>';

                    $html .= '<th>';
                                                  
                            if($record->warranty_card=='No'  && $record->observation=='Part Required')
                            {
                                if($record->case_close=='1' &&  $record->inv_status=='0' && $record->payment_entry=='0' && $record->final_symptom_status=='0')
                                {
                                    $html .= 'Job No. Closed';
                                }
                                else if($record->case_close=='1' &&  $record->inv_status=='0' && $record->payment_entry=='0' && $record->final_symptom_status!='0')
                                {
                                    $html .= $record->payment_status;
                                }
                                else if($record->case_close=='1' &&  $record->inv_status=='0' && $record->payment_entry=='1' )
                                {
                                    $html .= 'Payment Not Made';
                                }
                                else if($record->case_close=='1' &&  $record->inv_status=='1'  )
                                {
                                    $html .= 'Invoice Not Made';
                                }
                                else if($record->case_close=='1' && $record->part_status=='1')
                                {
                                    $html .= 'Part Allocation Pending';
                                }
                                else
                                {
                                    $html .= 'Observation Pending';
                                }
                            }
                            else
                            {
                                $html .= 'Service Not Required';
                            }
                            
                            
                            $html .='</th>';

                            // $html .= '<th>';
                                                  
                            // $html .= '<a href="view-generate-pdf?TagId='.$record->TagId.'" target="_blank">View </a>';
                            // $html .= '<a href="generate-pdf?TagId='.$record->TagId.'">PDF </a>';
                            
                            // $html .= '</th>';

                            $html .= '<th>';

                                if($record->case_close=='1' &&  $record->inv_status=='0' )
                                    {
                                         $html .= '<a href="generate-TXInvoice?TagId='.$record->TagId.'">Invoice</a>';
                                    }
                                 $html .= '</th>';
                                
                                
                                //$html .= '<th>'.$record->Customer_Address.'</th>';
                                $html .= '<th>'.$record->delivery_date.'</th>';
                                $html .= '<th>'.$record->se_del_remarks.'</th>';
    

                    $html .= '</tr>';
                }
                
                
                
           echo $html .= '</table>'; exit;
        }
        
    }  
}

