<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\StateMaster;
use App\TaggingMaster;
use App\ProductMaster;
use App\RegionMaster;
use App\BrandMaster;
use App\ServiceCenter;
use App\ReturnInvoicePart;
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
        Session::put("page-title","PO Report");
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
        
        if(!empty($center_id) && $center_id !='All')
        {
            $qry.=" and scr.center_id ='$center_id' ";
        }
        if(!empty($brand_id) && $brand_id !='All')
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


        $qry = "SELECT brand_id,brand_name  FROM  brand_master WHERE brand_status='1'";
        $brand_json           =   DB::select($qry);

        $brand_master = array();

        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }

        #$brand_master = BrandMaster::whereRaw("brand_status='1'")->get();
        
        
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
    
    public function clarion_report()
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


        $qry = "SELECT brand_id,brand_name  FROM  brand_master WHERE brand_status='1'";
        $brand_json           =   DB::select($qry);

        $brand_master = array();

        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }

        #$brand_master = BrandMaster::whereRaw("brand_status='1'")->get();
        
        
        return view('clarion-report')
                ->with('sc_master',$sc_master)
                ->with('brand_master',$brand_master)
                ->with('state_master',$state_master);
    }


    public function export_brand_report(Request $request)
    {
        #print_r($request->all());die;
        $brand_id = $request->input("brand_id");
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
        
        $qry = "and tm.brand_id='$brand_id' and DATE(tm.created_at) between '$from_date' and '$to_date' ";
        
        
        $html = "";
        $tag_data_qr = "select tm.*,center_name from  tagging_master tm  inner join tbl_service_centre sc on tm.center_id = sc.center_id where 1=1 $qry ";
        $data_arr = DB::select("$tag_data_qr");


        $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
        $brand_name = $brand_det->brand_name;
    
        
        $class = "";
        if($report_type=='export')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=".$brand_name."-mis-report.xls");
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

            if($brand_id==4)
            {
                $html .= '<table border="2" '.$class.'>';
                $html .= '<tr>';
                $html .= "<th>Sr.No</th>";
                $html .= "<th>Date Of Register</th>";
                $html .= "<th>Ticket No.</th>";
                $html .= "<th>Job No.</th>";
                $html .= "<th>Dealer Name</th>";
                $html .= "<th>Location</th>";
                $html .= "<th>Region</th>";
                $html .= "<th>State</th>";
                $html .= "<th>Contact Person</th>";
                $html .= "<th>Contact No.</th>";
                $html .= "<th>Vehicle Sale Date</th>";
                $html .= "<th>Vin No .</th>";
                $html .= "<th>Mielage Km. / PDI</th>";
                $html .= "<th>Warranty Status</th>";
                $html .= "<th>Vehicle Model</th>";
                $html .= "<th>DA2- Model</th>";
                $html .= "<th>System SW Version</th>";
                $html .= "<th>Job Card</th>";
                $html .= "<th>Videos</th>";
                $html .= "<th>CRF</th>";
                $html .= "<th>FTIR</th>";
                $html .= "<th>FTIR No.</th>";
                $html .= "<th>ASC/Supreme 1st Analysis</th>";
                $html .= "<th>Type of Issue Suspected</th>";
                $html .= "<th>Issue Category</th>";
                $html .= "<th>Mobile Handset Model</th>";
                $html .= "<th>Alternate No.</th>";
                $html .= "<th>Visit Type</th>";
                $html .= "<th>Site Visit Date</th>";
                $html .= "<th>Asc Name</th>";
                #$html .= "<th>Asc Location</th>";
                $html .= "<th>Part Replaced</th>";
                $html .= "<th>Date of Part Replaced</th>";
                $html .= "<th>Issue Resolve Date</th>";
                $html .= "<th>Status Of Job</th>";
                $html .= "<th>Remarks</th>";
                $html .= "<th>Date of Dispatch</th>";
                $html .= "<th>Tat (In Days)</th>";
                $html .= "<th>Tat Delay Remarks</th>";
                $html .= "<th>Defective Part Received</th>";
                $html .= "<th>Defective Received at CIL Date/Final Job Close Date</th>";
                $html .= "<th>Final Status of the Job</th>";
                
                $html .= '</tr>';  //exit;

                    $i=1;
                foreach($data_arr as $record)
                {
                    $html .= '<tr>';
                    $html .= "<th>".$i++."</th>";
                    $html .= '<th>'.date('d-m-Y',strtotime($record->created_at)).'</th>';
                    #$html .= '<th>'.date('d-m-Y',strtotime($record->job_accept_date)).'</th>';
                    $html .= '<th>'.$record->ticket_no.'</th>';
                    $html .= '<th>'.$record->job_no.'</th>';
                    $html .= '<th>'.$record->dealer_name.'</th>';
                    $html .= '<th>'.$record->Landmark.'</th>';
                    $html .= '<th>'.$record->region.'</th>';
                    $html .= '<th>'.$record->State.'</th>';
                    $html .= '<th>'.$record->Customer_Name.'</th>';
                    $html .= '<th>'.$record->Contact_No.'</th>';
                    $html .= '<th>'.date('d-m-Y',strtotime($record->vehicle_sale_date)).'</th>';
                    $html .= '<th>'.$record->vin_no.'</th>';
                    $html .= '<th>'.$record->mielage.'</th>';
                    $html .= '<th>'.$record->warranty_type.'</th>';
                    $html .= '<th>'.$record->Product.'</th>';
                    $html .= '<th>'.$record->Model.'</th>';
                    $html .= '<th>'.$record->system_sw_version.'</th>';
                    $html .= '<th>'.$record->job_card.'</th>';
                    $html .= '<th>'.$record->videos.'</th>';
                    $html .= '<th>'.$record->crf.'</th>';
                    $html .= '<th>'.$record->ftir.'</th>';
                    $html .= '<th>'.$record->ftir_no.'</th>';
                    $html .= '<th>'.$record->supr_analysis.'</th>';
                    $html .= '<th>'.$record->issue_type.'</th>';
                    $html .= '<th>'.$record->issue_cat.'</th>';
                    $html .= '<th>'.$record->mobile_handset_model.'</th>';
                    $html .= '<th>'.$record->Alt_No.'</th>';
                    $html .= '<th>'.$record->visit_type.'</th>';
                    $html .= '<th>'.date('d-m-Y',strtotime($record->site_visit_date)).'</th>';
                    $html .= '<th>'.$record->center_name.'</th>';
                    #$html .= '<th>'.$record->center_name.'</th>';
                    $html .= '<th>'.$record->part_replace.'</th>';
                    $html .= '<th>'.$record->part_replace_date.'</th>';
                    $html .= '<th>'.$record->issue_resolved_date.'</th>';
                    $html .= '<th>'.$record->Job_Status.'</th>';
                    $html .= '<th>'.$record->remarks.'</th>';
                    $html .= '<th>'.date('d-m-Y',strtotime($record->dispatch_date)).'</th>';
                    $html .= '<th>'.$record->tat.'</th>';
                    $html .= '<th>'.$record->tat_delay_remarks.'</th>';
                    $html .= '<th>'.$record->defective_part_rcv.'</th>';
                    $html .= '<th>'.$record->final_job_close_date.'</th>';
                    $html .= '<th>'.$record->final_job_status.'</th>';

                    $html .= '</tr>';
                }
                
                
                
              $html .= '</table>';

            }else{

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
                
                
                
            $html .= '</table>';

            }
            
            echo $html; exit;
        }
        
    }

    public function pending_reservation_report()
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

        return view('pending-reservation-report');
    }


    public function export_pending_reservation(Request $request)
    {
        #print_r($request->all());die;
        $from_date = $request->input("from_date");
        $to_date = $request->input("to_date");

        $report_type = $request->input("report_type");
       
        $UserType = Session::get('UserType');

        
        $whereUser = "";
        $Center_Id = Auth::user()->table_id;

        
        $from_date              =   date('Y-m-d',strtotime($from_date));
        $to_date              =   date('Y-m-d',strtotime($to_date));
        
        $qry = " and DATE(tm.created_at) between '$from_date' and '$to_date' ";
        
        
        $html = "";
        $tag_data_qr = "SELECT tm.*,dm.dist_name,sc.center_name FROM  tagging_master tm  INNER JOIN tbl_service_centre sc ON tm.center_id = sc.center_id 
        LEFT JOIN district_master dm ON tm.dist_id = dm.dist_id WHERE tm.job_accept='0' AND tm.job_reject='0' AND tm.se_id IS NULL $qry ";
        $data_arr = DB::select("$tag_data_qr");

    
        
        $class = "";
        if($report_type=='export')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=pending-reservation-report.xls");
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
                $html .= "<th>Ticket No.</th>";
                $html .= "<th>Customer Name</th>";
                $html .= "<th>State</th>";
                $html .= "<th>District</th>";
                $html .= "<th>Mobile No.</th>";
                $html .= "<th>Pincode</th>";
                $html .= "<th>Brand</th>";
                $html .= "<th>Product</th>";
                $html .= "<th>Model No.</th>";
                $html .= "<th>Serial No.</th>";
                $html .= "<th>Reported Issue</th>";
                $html .= "<th>Status</th>";
                $html .= "<th>Reason of Pendency / Rejection</th>";
                $html .= "</tr>";
    
                    $i=1;
                foreach($data_arr as $record)
                {
                    $html .= '<tr>';
                    $html .= "<th>".$i++."</th>";
                    $html .= '<th>'.$record->ticket_no.'</th>';
                    $html .= '<th>'.$record->Customer_Name.'</th>';
                    $html .= '<th>'.$record->State.'</th>';
                    $html .= '<th>'.$record->dist_name.'</th>';
                    $html .= '<th>'.$record->Contact_No.'</th>';
                    $html .= '<th>'.$record->Pincode.'</th>';
                    $html .= '<th>'.$record->Brand.'</th>';
                    $html .= '<th>'.$record->Product.'</th>';
                    $html .= '<th>'.$record->Model.'</th>';
                    $html .= '<th>'.$record->Serial_No.'</th>';
                    $html .= '<th></th>';
                    $html .= '<th>';
                    if($record->job_accept=='0')
                    {
                        $html .= 'Pending';
                    }
                    else
                    {
                        $html .=  'Accepted';
                    }
                    $html .= '</th>';
                    
                    $html .= '<th>'.$record->reason.'</th>';

                    $html .= '</tr>';
                }
                
                
                
              $html .= '</table>';

            
            echo $html; exit;
        }
        
    }



    public function open_pending_call_report()
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

        $qry = "SELECT brand_id,brand_name  FROM  brand_master WHERE brand_status='1'";
        $brand_json           =   DB::select($qry);

        $brand_master = array();

        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }

        return view('open-pending-call-report')->with('brand_master',$brand_master);
    }


    public function export_open_pending_call(Request $request)
    {
        #print_r($request->all());die;
        $brand_id = $request->input("brand_id");
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
        
        $qry = "and tm.brand_id='$brand_id' and DATE(tm.created_at) between '$from_date' and '$to_date' ";
        
        
        $html = "";
        $tag_data_qr = "select tm.*,center_name from  tagging_master tm  inner join tbl_service_centre sc on tm.center_id = sc.center_id where 1=1 $qry ";
        $data_arr = DB::select("$tag_data_qr");
        


        $brand_det = BrandMaster::whereRaw("brand_id='$brand_id'")->first();
        $brand_name = $brand_det->brand_name;
    
        
        $class = "";
        if($report_type=='export')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=".$brand_name."-open-pending-call-report.xls");
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

            if($brand_id==4)
            {
                $html .= '<table border="2" '.$class.'>';
                $html .= '<tr>';
                $html .= "<th>Sr.No</th>";
                $html .= "<th>Date Of Register</th>";
                $html .= "<th>Ticket No.</th>";
                $html .= "<th>Job No.</th>";
                $html .= "<th>Dealer Name</th>";
                $html .= "<th>Location</th>";
                $html .= "<th>Region</th>";
                $html .= "<th>State</th>";
                $html .= "<th>Contact Person</th>";
                $html .= "<th>Contact No.</th>";
                $html .= "<th>Vehicle Sale Date</th>";
                $html .= "<th>Vin No .</th>";
                $html .= "<th>Mielage Km. / PDI</th>";
                $html .= "<th>Warranty Status</th>";
                $html .= "<th>Vehicle Model</th>";
                $html .= "<th>DA2- Model</th>";
                $html .= "<th>System SW Version</th>";
                $html .= "<th>Job Card</th>";
                $html .= "<th>Videos</th>";
                $html .= "<th>CRF</th>";
                $html .= "<th>FTIR</th>";
                $html .= "<th>FTIR No.</th>";
                $html .= "<th>ASC/Supreme 1st Analysis</th>";
                $html .= "<th>Type of Issue Suspected</th>";
                $html .= "<th>Issue Category</th>";
                $html .= "<th>Mobile Handset Model</th>";
                $html .= "<th>Alternate No.</th>";
                $html .= "<th>Visit Type</th>";
                $html .= "<th>Site Visit Date</th>";
                $html .= "<th>Asc Name</th>";
                #$html .= "<th>Asc Location</th>";
                $html .= "<th>Part Replaced</th>";
                $html .= "<th>Date of Part Replaced</th>";
                $html .= "<th>Issue Resolve Date</th>";
                $html .= "<th>Status Of Job</th>";
                $html .= "<th>Remarks</th>";
                $html .= "<th>Date of Dispatch</th>";
                $html .= "<th>Tat (In Days)</th>";
                $html .= "<th>Tat Delay Remarks</th>";
                $html .= "<th>Defective Part Received</th>";
                $html .= "<th>Defective Received at CIL Date/Final Job Close Date</th>";
                $html .= "<th>Final Status of the Job</th>";
                
                $html .= '</tr>';  //exit;

                    $i=1;
                foreach($data_arr as $record)
                {
                    $html .= '<tr>';
                    $html .= "<th>".$i++."</th>";
                    $html .= '<th>'.date('d-m-Y',strtotime($record->created_at)).'</th>';
                    #$html .= '<th>'.date('d-m-Y',strtotime($record->job_accept_date)).'</th>';
                    $html .= '<th>'.$record->ticket_no.'</th>';
                    $html .= '<th>'.$record->job_no.'</th>';
                    $html .= '<th>'.$record->dealer_name.'</th>';
                    $html .= '<th>'.$record->Landmark.'</th>';
                    $html .= '<th>'.$record->region.'</th>';
                    $html .= '<th>'.$record->State.'</th>';
                    $html .= '<th>'.$record->Customer_Name.'</th>';
                    $html .= '<th>'.$record->Contact_No.'</th>';
                    $html .= '<th>'.date('d-m-Y',strtotime($record->vehicle_sale_date)).'</th>';
                    $html .= '<th>'.$record->vin_no.'</th>';
                    $html .= '<th>'.$record->mielage.'</th>';
                    $html .= '<th>'.$record->warranty_type.'</th>';
                    $html .= '<th>'.$record->Product.'</th>';
                    $html .= '<th>'.$record->Model.'</th>';
                    $html .= '<th>'.$record->system_sw_version.'</th>';
                    $html .= '<th>'.$record->job_card.'</th>';
                    $html .= '<th>'.$record->videos.'</th>';
                    $html .= '<th>'.$record->crf.'</th>';
                    $html .= '<th>'.$record->ftir.'</th>';
                    $html .= '<th>'.$record->ftir_no.'</th>';
                    $html .= '<th>'.$record->supr_analysis.'</th>';
                    $html .= '<th>'.$record->issue_type.'</th>';
                    $html .= '<th>'.$record->issue_cat.'</th>';
                    $html .= '<th>'.$record->mobile_handset_model.'</th>';
                    $html .= '<th>'.$record->Alt_No.'</th>';
                    $html .= '<th>'.$record->visit_type.'</th>';
                    $html .= '<th>'.date('d-m-Y',strtotime($record->site_visit_date)).'</th>';
                    $html .= '<th>'.$record->center_name.'</th>';
                    #$html .= '<th>'.$record->center_name.'</th>';
                    $html .= '<th>'.$record->part_replace.'</th>';
                    $html .= '<th>'.$record->part_replace_date.'</th>';
                    $html .= '<th>'.$record->issue_resolved_date.'</th>';
                    $html .= '<th>'.$record->Job_Status.'</th>';
                    $html .= '<th>'.$record->remarks.'</th>';
                    $html .= '<th>'.date('d-m-Y',strtotime($record->dispatch_date)).'</th>';
                    $html .= '<th>'.$record->tat.'</th>';
                    $html .= '<th>'.$record->tat_delay_remarks.'</th>';
                    $html .= '<th>'.$record->defective_part_rcv.'</th>';
                    $html .= '<th>'.$record->final_job_close_date.'</th>';
                    $html .= '<th>'.$record->final_job_status.'</th>';

                    $html .= '</tr>';
                }
                
                
                
              $html .= '</table>';

            }else if($brand_id==2)
            {
                $html .= '<table border="2" '.$class.'>';
                $html .= '<tr>';
                $html .= '<th>Sr. No.</th>';
                $html .= '<th>Set Received at ASC/Supreme</th>';
                $html .= '<th>Unit Available @ ASC/Supreme</th>';
                $html .= '<th>Ageing of set</th>';
                $html .= '<th>Sender Name</th>';
                $html .= '<th>JobNo.</th>';
                $html .= '<th>Sender Address</th>';
                $html .= '<th>Sender Contact No.</th>';
                $html .= '<th>Address</th>';
                $html .= '<th>Refered By</th>';
                $html .= '<th>Brand</th>';
                $html .= '<th>Product</th>';
                $html .= '<th>Model Name</th>';
                $html .= '<th>Set Serial No</th>';
                $html .= '<th>Date of Purchase</th>';
                $html .= '<th>Warranty Card</th>';
                $html .= '<th>Invoice</th>';
                $html .= '<th>Problem Reported</th>';
                $html .= '<th>Warranty Status</th>';
                $html .= '<th>Warranty Approval</th>';
                $html .= '<th>Engineer Observation</th>';
                $html .= '<th>Part Order By ASC on</th>';
                $html .= '<th>Part 1 Name</th>';
                $html .= '<th>Part 1 Code</th>';
                $html .= '<th>Part Type</th>';
                $html .= '<th>Qty</th>';
                $html .= '<th>Part 2 Name</th>';
                $html .= '<th>Part 2 Code</th>';
                $html .= '<th>Part Type</th>';
                $html .= '<th>Qty</th>';
                $html .= '<th>Part Status</th>';
                $html .= '<th>Part Order Status (Ordered/not ordered)</th>';
                $html .= '<th>Part Received in Supreme</th>';
                $html .= '<th>Part received from Pioneer OR Supreme Against DC/Challan Number</th>';
                $html .= '<th>Part issued to Engineer OR Dispatched to ASC On</th>';
                $html .= '<th>Docket Number</th>';
                $html .= '<th>Receiving date at ASC</th>';
                $html .= '<th>Remarks</th>';
                $html .= '<th>Closure Date</th>';
                $html .= '<th>Job Status</th>';
                $html .= '<th>Delivery Date</th>';
                $html .= '</tr>';

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
                
                
                
            $html .= '</table>';

            }
            else {

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
                
                
                
            $html .= '</table>';

            }
            
            echo $html; exit;
        }
        
    }

    public function pending_delivery_report()
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

        $qry = "SELECT brand_id,brand_name  FROM  brand_master WHERE brand_status='1'";
        $brand_json           =   DB::select($qry);

        $brand_master = array();

        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }

        return view('pending-delivery-report')->with('brand_master',$brand_master);
    }


    public function delivery_set_report()
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

        $qry = "SELECT brand_id,brand_name  FROM  brand_master WHERE brand_status='1'";
        $brand_json           =   DB::select($qry);

        $brand_master = array();

        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }

        return view('delivery-set-report')->with('brand_master',$brand_master);
    }


    public function cannibalized_report()
    {
        Session::put("page-title","Cannibalized Report");
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


        $qry = "SELECT brand_id,brand_name  FROM  brand_master WHERE brand_status='1'";
        $brand_json           =   DB::select($qry);

        $brand_master = array();

        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }

        #$brand_master = BrandMaster::whereRaw("brand_status='1'")->get();
        $url = $_SERVER['APP_URL'].'/cannibalized-report';
        return view('cannibalized-report')
                ->with('url', $url)
                ->with('sc_master',$sc_master)
                ->with('brand_master',$brand_master)
                ->with('state_master',$state_master);
    }

    public function export_cannibalized_report(Request $request)
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
        // $tag_data_qr = "select tm.*,center_name from  tagging_master tm  inner join tbl_service_centre sc on tm.center_id = sc.center_id where 1=1 $qry ";
        // $data_arr = DB::select("$tag_data_qr");

        
        $tag_data_qr = "SELECT oip.*,sm.state_name,pm.product_name,pcm.category_name,tsp.serial_no,pm.product_category_id,pm.product_id FROM outward_inventory_pending oip INNER JOIN tbl_service_centre sc ON oip.center_id = sc.center_id
            INNER JOIN `state_master` sm ON sc.state = sm.state_id
            INNER JOIN `model_master` mm ON mm.model_id = oip.model_id
            INNER JOIN `product_master` pm ON pm.product_id = mm.product_id
            INNER JOIN `product_category_master` pcm ON pm.product_category_id = pcm.product_category_id
            INNER JOIN `tbl_spare_parts` tsp ON tsp.spare_id = oip.spare_id
            WHERE oip.canbalized_status='1' ";
        $data_arr = DB::select("$tag_data_qr");

        #$all_canblized_Part_arr = ReturnInvoicePart::whereRaw("canbalized_status='1'")->get();
    
        
        $class = "";
        if($report_type=='export')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=cannibalized-report.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            
        }
        else
        {
            $class = 'class="table table-striped table-bordered"';
            
        }
        if(empty($data_arr))
        #if($all_canblized_Part_arr->isEmpty())
        {
          echo  $html = 'No Records Found'; exit;
        }
        else
        {
            
            $html .= '<table border="2" '.$class.'>';
            $html .= '<tr>';
            $html .= "<th>Sr. No.</th>";
            $html .= "<th>Date of Defc. Recd</th>";
            $html .= "<th>Recd. From (ASC name)</th>";
            $html .= "<th>Location</th>";
            $html .= "<th>Job No</th>";
            $html .= "<th>Brand</th>";
            $html .= "<th>Product Category</th>";
            $html .= "<th>Product Name</th>";
            $html .= "<th>Model</th>";
            $html .= "<th>Canablized Spare Part</th>";
            $html .= "<th>Canablized Spare Part Code</th>";
            $html .= "<th>New Sr. No.</th>";
            $html .= "<th>Old Sr. No.</th>";
            $html .= "<th>Part used in (Ticket No.)</th>";
            $html .= "<th>Part used in (Job No.)</th>";
            $html .= "<th>Status</th>";
            $html .= "<th>Date of Action</th>";
            $html .= '</tr>';

                    $i=1;
                foreach($data_arr as $record)
                {

                    $brand_id = $record->brand_id;
                    $product_category_id = $record->product_category_id;
                    $product_id = $record->product_id;
                    $model_id = $record->model_id;

                    $exist_tagging = "SELECT ticket_no,job_no,Brand,Product,Model,product_id FROM tagging_master tm 
                    INNER JOIN tbl_service_centre sc ON tm.center_id = sc.center_id 
                    LEFT JOIN district_master dm ON tm.dist_id = dm.dist_id 
                    WHERE tm.brand_id='$brand_id' AND product_category_id='$product_category_id' AND 
                    product_id='$product_id' AND model_id='$model_id' ";

                    $exist_arr = DB::select("$exist_tagging");

                    $ticket_nos = "";
                    $job_nos = "";
                    foreach ($exist_arr as $row) {
                      
                        $ticket_nos .= $row->ticket_no . ",";
                        $job_nos .= $row->job_no . ",";
                    }
                    $ticket_nos = rtrim($ticket_nos, ",");
                    $job_nos = rtrim($job_nos, ",");

                    #echo $record->product_id;die;
                    $html .= '<tr>';
                    $html .= "<th>".$i++."</th>";
                    $html .= '<th>'.date('d-m-Y',strtotime($record->po_date)).'</th>';
                    $html .= '<th>'.$record->asc_name.'</th>';
                    $html .= '<th>'.$record->state_name.'</th>';
                    $html .= '<th>'.$record->job_no.'</th>';
                    $html .= '<th>'.$record->brand_name.'</th>';
                    $html .= '<th>'.$record->category_name.'</th>';
                    $html .= '<th>'.$record->product_name.'</th>';
                    $html .= '<th>'.$record->model_name.'</th>';
                    $html .= '<th>'.$record->part_name.'</th>';
                    $html .= '<th>'.$record->part_no.'</th>';
                    $html .= '<th>'.$record->po_no.'</th>';
                    $html .= '<th>'.$record->serial_no.'</th>';
                    
                    $html .= '<th>'.$ticket_nos.'</th>';
                    $html .= '<th>'.$job_nos.'</th>';
                    $html .= '<th></th>';
                    $html .= '<th>'.date('d-m-Y', strtotime($record->canbalized_date)).'</th>';
                    $html .= '</tr>';
                }
                
                
                
           echo $html .= '</table>'; exit;
        }
        
    }

    public function defective_return_report()
    {
        Session::put("page-title","Defective Return Report");
        $UserType = Session::get('UserType');

        $Center_Id = Auth::user()->table_id;
        $whereUser = "and 1=1 ";
        if($UserType!='Admin')
        {
            $whereUser .= " and se.center_id='$Center_Id'";

        }

        $url = $_SERVER['APP_URL'].'/defective-return-report';
        return view('defective-return-report')
                ->with('url', $url);
    }

    public function export_defective_return(Request $request)
    {
        //print_r($request->all());die;
        $from_date = $request->input("from_date");
        $to_date = $request->input("to_date");

        $report_type = $request->input("report_type");
       
        $UserType = Session::get('UserType');

        
        $whereUser = "";
        $Center_Id = Auth::user()->table_id;

        $from_date              =   date('Y-m-d',strtotime($from_date));
        $to_date              =   date('Y-m-d',strtotime($to_date));
        
        $qry = "and DATE(tm.created_at) between '$from_date' and '$to_date' ";
        
        
        $html = "";

        $tag_data_qr = "SELECT oip.*,sm.state_name,pm.product_name,pcm.category_name,tsp.serial_no,pm.product_category_id,pm.product_id FROM outward_inventory_pending oip INNER JOIN tbl_service_centre sc ON oip.center_id = sc.center_id
            INNER JOIN `state_master` sm ON sc.state = sm.state_id
            INNER JOIN `model_master` mm ON mm.model_id = oip.model_id
            INNER JOIN `product_master` pm ON pm.product_id = mm.product_id
            INNER JOIN `product_category_master` pcm ON pm.product_category_id = pcm.product_category_id
            INNER JOIN `tbl_spare_parts` tsp ON tsp.spare_id = oip.spare_id
            WHERE oip.defective_status='1' GROUP BY 
    pm.product_name";
        $data_arr = DB::select("$tag_data_qr");

        
        $class = "";
        if($report_type=='export')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=defective-return-report.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            
        }
        else
        {
            $class = 'class="table table-striped table-bordered"';
            
        }
        if(empty($data_arr))
        #if($all_canblized_Part_arr->isEmpty())
        {
          echo  $html = 'No Records Found'; exit;
        }
        else
        {
            
            $html .= '<table border="2" '.$class.'>';
            $html .= '<tr>';
            $html .= "<th>Sr. No.</th>";
            $html .= "<th>Date of Defc. Recd</th>";
            $html .= "<th>Recd. From (ASC name)</th>";
            $html .= "<th>Location</th>";
            $html .= "<th>Job No</th>";
            $html .= "<th>Brand</th>";
            $html .= "<th>Product Category</th>";
            $html .= "<th>Product Name</th>";
            $html .= "<th>Model</th>";
            $html .= "<th>Spare Part</th>";
            $html .= "<th>Spare Part Code</th>";
            $html .= "<th>New Sr. No.</th>";
            $html .= "<th>Old Sr. No.</th>";
            $html .= "<th>Part used in (Ticket No.)</th>";
            $html .= "<th>Part used in (Job No.)</th>";
            $html .= "<th>Status</th>";
            $html .= "<th>Date of Action</th>";
            
            $html .= '</tr>';

                    $i=1;
                foreach($data_arr as $record)
                {

                    $brand_id = $record->brand_id;
                    $product_category_id = $record->product_category_id;
                    $product_id = $record->product_id;
                    $model_id = $record->model_id;

                    $exist_tagging = "SELECT ticket_no,job_no,Brand,Product,Model,product_id FROM tagging_master tm 
                    INNER JOIN tbl_service_centre sc ON tm.center_id = sc.center_id 
                    LEFT JOIN district_master dm ON tm.dist_id = dm.dist_id 
                    WHERE tm.brand_id='$brand_id' AND product_category_id='$product_category_id' AND 
                    product_id='$product_id' AND model_id='$model_id' ";

                    $exist_arr = DB::select("$exist_tagging");

                    $ticket_nos = "";
                    $job_nos = "";
                    foreach ($exist_arr as $row) {
                      
                        $ticket_nos .= $row->ticket_no . ",";
                        $job_nos .= $row->job_no . ",";
                    }
                    $ticket_nos = rtrim($ticket_nos, ",");
                    $job_nos = rtrim($job_nos, ",");

                    #echo $record->product_id;die;
                    $html .= '<tr>';
                    $html .= "<th>".$i++."</th>";
                    $html .= '<th>'.date('d-m-Y',strtotime($record->po_date)).'</th>';
                    $html .= '<th>'.$record->asc_name.'</th>';
                    $html .= '<th>'.$record->state_name.'</th>';
                    $html .= '<th>'.$record->job_no.'</th>';
                    $html .= '<th>'.$record->brand_name.'</th>';
                    $html .= '<th>'.$record->category_name.'</th>';
                    $html .= '<th>'.$record->product_name.'</th>';
                    $html .= '<th>'.$record->model_name.'</th>';
                    $html .= '<th>'.$record->part_name.'</th>';
                    $html .= '<th>'.$record->part_no.'</th>';
                    $html .= '<th>'.$record->po_no.'</th>';
                    $html .= '<th>'.$record->serial_no.'</th>';
                    
                    $html .= '<th>'.$ticket_nos.'</th>';
                    $html .= '<th>'.$job_nos.'</th>';
                    $html .= '<th></th>';
                    $html .= '<th>'.date('d-m-Y', strtotime($record->canbalized_date)).'</th>';
                    $html .= '</tr>';
                }
                
                
                
           echo $html .= '</table>'; exit;
        }
        
    }

    public function defective_scrap_report()
    {
        Session::put("page-title","Defective Scrap Report");
        $UserType = Session::get('UserType');

        $Center_Id = Auth::user()->table_id;
        $whereUser = "and 1=1 ";
        if($UserType!='Admin')
        {
            $whereUser .= " and se.center_id='$Center_Id'";

        }

        $url = $_SERVER['APP_URL'].'/defective-scrap-report';
        return view('defective-scrap-report')
                ->with('url', $url);
    }


    public function export_defective_scrap(Request $request)
    {
        //print_r($request->all());die;
        $from_date = $request->input("from_date");
        $to_date = $request->input("to_date");

        $report_type = $request->input("report_type");
       
        $UserType = Session::get('UserType');

        
        $whereUser = "";
        $Center_Id = Auth::user()->table_id;

        $from_date              =   date('Y-m-d',strtotime($from_date));
        $to_date              =   date('Y-m-d',strtotime($to_date));
        
        $qry = "and DATE(tm.created_at) between '$from_date' and '$to_date' ";
        
        
        $html = "";

        $tag_data_qr = "SELECT oip.*,sm.state_name,pm.product_name,pcm.category_name,tsp.serial_no,pm.product_category_id,pm.product_id FROM outward_inventory_pending oip INNER JOIN tbl_service_centre sc ON oip.center_id = sc.center_id
            INNER JOIN `state_master` sm ON sc.state = sm.state_id
            INNER JOIN `model_master` mm ON mm.model_id = oip.model_id
            INNER JOIN `product_master` pm ON pm.product_id = mm.product_id
            INNER JOIN `product_category_master` pcm ON pm.product_category_id = pcm.product_category_id
            INNER JOIN `tbl_spare_parts` tsp ON tsp.spare_id = oip.spare_id
            WHERE oip.scrap_status='1'";
        $data_arr = DB::select("$tag_data_qr");

        
        $class = "";
        if($report_type=='export')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=defective-scrap-report.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            
        }
        else
        {
            $class = 'class="table table-striped table-bordered"';
            
        }
        if(empty($data_arr))
        #if($all_canblized_Part_arr->isEmpty())
        {
          echo  $html = 'No Records Found'; exit;
        }
        else
        {
            
            $html .= '<table border="2" '.$class.'>';
            $html .= '<tr colspan="13">';
            $html .= "<th>Sr. No.</th>";
            $html .= "<th>Date of Defc. Recd</th>";
            $html .= "<th>Recd. From (ASC name)</th>";
            $html .= "<th>Location</th>";
            $html .= "<th>Job No</th>";
            $html .= "<th>Brand</th>";
            $html .= "<th>Product Category</th>";
            $html .= "<th>Product Name</th>";
            $html .= "<th>Model</th>";
            $html .= "<th>Spare Part</th>";
            $html .= "<th>Spare Part Code</th>";
            $html .= "<th>New Sr. No.</th>";
            $html .= "<th>Old Sr. No.</th>";
            $html .= '<th>Date of Scrap</th>';
            #$html .= "<th>No. of Parts Scraped</th>";
            $html .= '</tr>';

                    $i=1;
                    $total_scrap = 0;
                foreach($data_arr as $record)
                {

                    #echo $record->product_id;die;
                    $total_scrap += 1;
                    $html .= '<tr>';
                    $html .= "<th>".$i++."</th>";
                    $html .= '<th>'.date('d-m-Y',strtotime($record->po_date)).'</th>';
                    $html .= '<th>'.$record->asc_name.'</th>';
                    $html .= '<th>'.$record->state_name.'</th>';
                    $html .= '<th>'.$record->job_no.'</th>';
                    $html .= '<th>'.$record->brand_name.'</th>';
                    $html .= '<th>'.$record->category_name.'</th>';
                    $html .= '<th>'.$record->product_name.'</th>';
                    $html .= '<th>'.$record->model_name.'</th>';
                    $html .= '<th>'.$record->part_name.'</th>';
                    $html .= '<th>'.$record->part_no.'</th>';
                    $html .= '<th>'.$record->po_no.'</th>';
                    $html .= '<th>'.$record->serial_no.'</th>';
                    
                    $html .= '<th>'.date('d-m-Y', strtotime($record->scrap_date)).'</th>';
                    #$html .= '<th></th>';
                    $html .= '</tr>';
                }
                $html .= '<tr>';
                $html .= "<th colspan='13' style='text-align: center;'>Total</th>";
                $html .= '<th>'.$total_scrap.'</th>';
                $html .= '</tr>';
                
                
                
           echo $html .= '</table>'; exit;
        }
        
    }

    public function monthly_stock_report()
    {
        Session::put("page-title","Monthly Stock Report");
        $UserType = Session::get('UserType');

        $Center_Id = Auth::user()->table_id;
        $whereUser = "and 1=1 ";
        if($UserType!='Admin')
        {
            $whereUser .= " and se.center_id='$Center_Id'";

        }

        $url = $_SERVER['APP_URL'].'/monthly-stock-report';
        return view('monthly-stock-report')
                ->with('url', $url);
    }

    public function consumption_report()
    {
        Session::put("page-title","Consumption Report");
        $UserType = Session::get('UserType');

        $Center_Id = Auth::user()->table_id;
        $whereUser = "and 1=1 ";
        if($UserType!='Admin')
        {
            $whereUser .= " and se.center_id='$Center_Id'";

        }

        $url = $_SERVER['APP_URL'].'/consumption-report';
        return view('consumption-report')
                ->with('url', $url);
    }


    
    public function fresh_stock_report()
    {
        Session::put("page-title","Fresh Stock Report");
        $UserType = Session::get('UserType');

        $Center_Id = Auth::user()->table_id;
        $whereUser = "and 1=1 ";
        if($UserType!='Admin')
        {
            $whereUser .= " and se.center_id='$Center_Id'";

        }

        $url = $_SERVER['APP_URL'].'/npc-fresh-stock-report';
        return view('fresh-stock-report')->with('url', $url);
    }

    
    public function fresh_inventory_report()
    {
        Session::put("page-title","Fresh Inventory Report");
        $UserType = Session::get('UserType');

        $Center_Id = Auth::user()->table_id;
        $whereUser = "and 1=1 ";
        if($UserType!='Admin')
        {
            $whereUser .= " and se.center_id='$Center_Id'";

        }

        $url = $_SERVER['APP_URL'].'/asc-fresh-inventory-report';
        return view('fresh-inventory-report')
                ->with('url', $url);
    }

    
    public function asc_defective_report()
    {
        Session::put("page-title","ASC Defective Report");
        $UserType = Session::get('UserType');

        $Center_Id = Auth::user()->table_id;
        $whereUser = "and 1=1 ";
        if($UserType!='Admin')
        {
            $whereUser .= " and se.center_id='$Center_Id'";

        }

        $url = $_SERVER['APP_URL'].'/asc-defective-report';
        return view('asc-defective-report')->with('url', $url);
    }

    public function clarion_stock_report()
    {
        Session::put("page-title","Clarion Stock Report");
        $UserType = Session::get('UserType');

        $Center_Id = Auth::user()->table_id;
        $whereUser = "and 1=1 ";
        if($UserType!='Admin')
        {
            $whereUser .= " and se.center_id='$Center_Id'";

        }

        $url = $_SERVER['APP_URL'].'/clarion-stock-report';
        return view('clarion-stock-report')->with('url', $url);
    }
    

}

