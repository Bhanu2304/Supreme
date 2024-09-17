<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\AgentMaster;
use App\TaggingMaster;
use App\RegionalManagerMaster;
use DB;
use Auth;
use Session;
 
class DashController extends Controller
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
    
    
    
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        
        
        $whereUser = "";
        
        if($UserType=='ASM' || $UserType=='RSM' )
        {
            $reg_det = RegionalManagerMaster::whereRaw("LogIn_Id='$UserId'")->first();
            $reg_man_id = $reg_det->reg_man_id;
            $whereUser = " and rmap.reg_man_id='$reg_man_id'";
        }
        else if($UserType=='RSM')
        {
            
        }
        else if($UserType!='Admin')
        {
            $whereUser .= " and tm.center_id ='$Center_Id'";
        }
        else if($UserType=='ServiceEngineer')
        {
            $whereUser = "and tm.se_id='$UserId' and tm.center_id='$Center_Id'";
        }
        
        //get method request
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        $asc_code = $request->input('asc_code');
        $job_id = $request->input('job_id');
        $warranty_category = $request->input('warranty_category');
        $service_type = $request->input('service_type');
        $submit = $request->input('submit');

        $whereTag = "";
        if(!empty($state_name) && $state_name!='All')
        {
            $whereTag .= " and tm.state = '$state_name'";
        }
        if(!empty($pincode))
        {
            $whereTag .= " and tm.Pincode = '$pincode'";
        }
        if(!empty($contact_no) && strlen($contact_no)>6)
        {
            $whereTag .= " and tm.contact_no='$contact_no'";
        }
        if(!empty($asc_code) && $asc_code!='All')
        {
            $whereTag .= " and tm.center_id = '$asc_code'";
        }
        if(!empty($job_id))
        {
            $whereTag .= " and tm.ticket_no = '$job_id'";
        }
        if(!empty($warranty_category))
        {
            $whereTag .= " and tm.warranty_category = '$warranty_category'";
        }
        if(!empty($service_type))
        {
            $whereTag .= " and tm.service_type = '$service_type'";
        }
        
        if(!empty($from_date) && !empty($to_date))
        {   $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }
        
        if($UserType=='ASM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.dist_id = tm.dist_id ";
            $on_table_pin = " ON rmap.dist_id = pm.dist_id ";
            
            $sel_center_ar = "SELECT center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc ON rmap.dist_id = sc.dist_id
WHERE rmap.reg_man_id = '$reg_man_id'";
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
            $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')";
        }
        else if($UserType=='RSM')
        {
            $from_table2 = $from_table = "tbl_region_area_map rmap INNER JOIN";
            $on_table = " ON rmap.region_id = tm.region_id ";
            $on_table_pin2 = " AND rmap.region_id = st.region_id ";
            
            $sel_center_ar = "SELECT sc.center_id FROM tbl_region_area_map rmap 
INNER JOIN `tbl_service_centre_pins` sc 
INNER JOIN state_master st ON sc.state_id = st.state_id AND rmap.region_id = st.region_id
WHERE rmap.reg_man_id = '$reg_man_id'"; 
            
            $center_arr_json = DB::select($sel_center_ar);
            $center_arr = array();
            foreach($center_arr_json as $center_det)
            {
                $center_arr[] = $center_det->center_id;
            }
             $center_qr = " and center_id in ('".implode("','",array_unique($center_arr))."')"; 
        }
        else if($UserType!='Admin' && $UserType!='NSM')
        {
            $from_table2 = "`tbl_service_centre_pins` sc INNER JOIN  ";
            $on_table_pin = " ON pm.pincode = sc.pincode AND pm.state_id = sc.state_id and sc.center_id='$Center_Id' ";
            $center_qr = " and center_id ='$Center_Id'"; 
        }
        
        if(!empty($whereTag))
        {
            //echo "SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE 1=1 $whereUser $whereTag"; exit;
            $dialer_open_total           =   DB::select("SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE case_close is null $whereUser $whereTag");
            $tagging_perday_call           =   DB::select("SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE  1=1  $whereUser $whereTag");
            $dialer_completed_call           =   DB::select("SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE case_close='1' $whereUser $whereTag");
            $total_pending           =   DB::select("SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE 1=1 and call_status='Part Pending' $whereUser $whereTag");
            $total_allocation_pending           =   DB::select("SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE (tm.center_id is null || tm.center_id='') $whereUser $whereTag");
            $total_cancel_call           =   DB::select("SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE 1=1 and call_status='Cancel' $whereUser $whereTag");
            
                    
            $tag_data_qr = "select tm.*,center_name from $from_table tagging_master tm $on_table inner join tbl_service_centre sc on tm.center_id = sc.center_id where 1=1 $whereTag    "; 
            $DataArr = DB::select("$tag_data_qr");
        }
        else
        {
            //echo "SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE DATE(tm.created_at) = CURDATE() $whereUser"; exit;
            $dialer_open_total           =   DB::select("SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE case_close is null  $whereUser");
            $tagging_perday_call           =   DB::select("SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE DATE(tm.created_at) = CURDATE()   $whereUser");
            $dialer_completed_call           =   DB::select("SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE  case_close='1' $whereUser");
            $total_pending           =   DB::select("SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE  call_status='Part Pending' $whereUser");
            $total_allocation_pending           =   DB::select("SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE  (tm.center_id is null || tm.center_id='') $whereUser");
            $total_cancel_call           =   DB::select("SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE DATE(tm.created_at) = CURDATE() and call_status='Cancel' $whereUser");
            
            //$tag_data_qr = "select * from tagging_master tm where DATE(tm.created_at) = CURDATE()    "; 
        }
        
        
        //$select_monthly_brand_wise_qry = "select ";
        
        
        if($submit=='Download Report')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=dash-report.xls");
            header("Pragma: no-cache");
            header("Expires: 0"); 
            ?>
            <table border="2">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>ASC Name</th>
                                    <th>State</th>
                                    <th>Ticket No.</th>
                                    <th>Job No.</th>
                                    <th>Customer Name</th>
                                    <th>Phone No.</th>
                                    <th>Pin Code</th>
                                    
                                    <th>Brand</th>
                                    <th>Model No.</th>
                                    <th>Serial No.</th>
                                    <th>Warranty Category</th>
                                    <th>Service Type</th>
                                    
                                    <th>Job Status</th>
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                   
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            //$record = json_decode($record1,true);
                                            echo '<tr>';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td>'.$record->center_name.'</td>';
                                            echo '<td>'.$record->State.'</td>';
                                                    echo '<td>'.$record->ticket_no.'</td>';
                                                    echo '<td>'.$record->job_no.'</td>';
                                                    echo '<td>'.$record->Customer_Name.'</td>';
                                                    echo '<td>'.$record->Contact_No.'</td>';
                                                    echo '<td>'.$record->Pincode.'</td>';
                                                    echo '<td>'.$record->Brand.'</td>';
                                                    echo '<td>'.$record->Model.'</td>';
                                                    echo '<td>'.$record->Serial_No.'</td>';
                                                    echo '<td>'.$record->warranty_category.'</td>';
                                                    echo '<td>'.$record->service_type.'</td>';
                                                    
                                                    echo '<td>';
                                                    
                                                    if($record->warranty_card=='No' && $record->warranty_card=='No' && $record->observation=='Part Required')
                                                    {
                                                        if($record->case_close=='1' &&  $record->inv_status=='0' && $record->payment_entry=='0' && $record->final_symptom_status=='0')
                                                        {
                                                            echo 'Job No. Closed';
                                                        }
                                                        else if($record->case_close=='1' &&  $record->inv_status=='0' && $record->payment_entry=='0' && $record->final_symptom_status!='0')
                                                        {
                                                            echo $record->payment_status;
                                                        }
                                                        else if($record->case_close=='1' &&  $record->inv_status=='0' && $record->payment_entry=='1' )
                                                        {
                                                            echo 'Payment Not Made';
                                                        }
                                                        else if($record->case_close=='1' &&  $record->inv_status=='1'  )
                                                        {
                                                            echo 'Invoice Not Made';
                                                        }
                                                        else if($record->case_close=='1' && $record->part_status=='1')
                                                        {
                                                            echo 'Part Allocation Pending';
                                                        }
                                                        else
                                                        {
                                                            echo 'Observation Pending';
                                                        }
                                                    }
                                                    else
                                                    {
                                                        echo 'Service Not Required';
                                                    }
                                                    
                                                    
                                                    echo '</td>';
                                                    
                                                    
                                                    
                                                    
                                                    //echo '<td>'.$record->Customer_Address.'</td>';
                                                    
                                                    
                                                    
                                            echo '</tr>';
                                        }
                                 
                                 
                                 ?>
                                  
                                 
                                
                              </tbody>
                           </table>
       <?php     exit;
        }
        
        if(!empty($dialer_open_total))
        {
            $dialer_open_total = $dialer_open_total[0]->total; 
        }
        else
        {
            $dialer_open_total = 0;
        }
        
        if(!empty($tagging_perday_call))
        {
            $tagging_perday_call = $tagging_perday_call[0]->total;
        }
        else
        {
            $tagging_perday_call = 0;
        }
        
        if(!empty($dialer_completed_call))
        {
            $dialer_completed_call = $dialer_completed_call[0]->total;
        }
        else
        {
            $dialer_completed_call = 0;
        }
        
        if(!empty($total_allocation_pending))
        {
            $total_allocation_pending = $total_allocation_pending[0]->total;
        }
        else
        {
            $total_allocation_pending = 0;
        }
        
        if(!empty($total_cancel_call))
        {
            $total_cancel_call = $total_cancel_call[0]->total;
        }
        else
        {
            $total_cancel_call = 0;
        }
        
        if(!empty($total_pending))
        {
            $total_pending = $total_pending[0]->total;
        }
        else
        {
            $total_pending = 0;
        }
        
          $qr1 = "SELECT pm.state_id,state_name,pm.pincode FROM $from_table2 `pincode_master` pm $on_table_pin
INNER JOIN state_master st ON pm.state_id = st.state_id $on_table_pin2
WHERE 1=1 "; 
        $vendor_pin_json           =   DB::select($qr1); 
        $state_master = $pin_master = array();
        foreach($vendor_pin_json as $vpin)
        {
            $state_master[$vpin->state_id] = $vpin->state_name;
            $pin_master[$vpin->pincode] = $vpin->pincode;
        }
        ksort($pin_master);
        ksort($state_master);
        
        $qr2 = "SELECT tsc.center_id,center_name,asc_code FROM tbl_service_centre  tsc
INNER JOIN users us ON tsc.email_id = us.email
WHERE sc_status='1' $center_qr order by center_name"; 
        $asc_master           =   DB::select($qr2); 
        
        $url = $_SERVER['APP_URL'].'/dashboard';
        
        return view('dash')
                ->with('dialer_open_total', $dialer_open_total)
                ->with('url', $url)
                ->with('asc_master', $asc_master)
                ->with('whereTag',$whereTag)
                ->with('tagging_perday_call',$tagging_perday_call)
                ->with('dialer_completed_call',$dialer_completed_call)
                ->with('total_allocation_pending',$total_allocation_pending)
                ->with('total_cancel_call',$total_cancel_call)
                ->with('total_pending',$total_pending)
                ->with('pin_master',$pin_master)
            ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('pincode',$pincode)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('contact_no',$contact_no)
                ->with('DataArr',$DataArr);
    }
    
    
    
    
}

