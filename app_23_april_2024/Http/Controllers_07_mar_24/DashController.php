<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\AgentMaster;
use App\TaggingMaster;
use App\RegionalManagerMaster;
use App\BrandMaster;
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
    
    
    private function center_dash($from_table,$on_table,$whereUser)
    {
        
            $dialer_open_total           =   DB::select("SELECT brand,count(1)  total from $from_table `tagging_master` tm $on_table  WHERE case_close is null  $whereUser group by brand");
            $tagging_perday_call           =   DB::select("SELECT brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE DATE(tm.created_at) = CURDATE()   $whereUser group by brand");
            $dialer_completed_call           =   DB::select("SELECT brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE  case_close='1' $whereUser group by brand");
            $total_pending           =   DB::select("SELECT brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE  call_status='Part Pending' $whereUser group by brand");
            $total_allocation_pending           =   DB::select("SELECT brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE  (tm.center_id is null || tm.center_id='') $whereUser group by brand");
            $total_cancel_call           =   DB::select("SELECT brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE DATE(tm.created_at) = CURDATE() and call_status='Cancel' $whereUser group by brand");
            
            $open_total = 0;
            if(!empty($dialer_open_total))
            {
                foreach($dialer_open_total as $tot)
                {
                    $open_total += $tot->total; 
                }
            }
            else
            {
                $open_total = 0;
            }

            $perday_call = 0;
            if(!empty($tagging_perday_call))
            {
                foreach($tagging_perday_call as $tot)
                {
                    $perday_call += $tot->total; 
                }
            }
            else
            {
                $perday_call = 0;
            }

            $completed_call = 0;
            if(!empty($dialer_completed_call))
            {
                foreach($dialer_completed_call as $tot)
                {
                    $completed_call += $tot->total; 
                }
            }
            else
            {
                $completed_call = 0;
            }

            $allocation_pending = 0;
            if(!empty($total_allocation_pending))
            {
                foreach($total_allocation_pending as $tot)
                {
                    $allocation_pending += $tot->total; 
                }
            }
            else
            {
                $allocation_pending = 0;
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
            return array('dialer_open_total'=>$dialer_open_total,'open_total'=>$open_total,
            'tagging_perday_call'=>$tagging_perday_call,'perday_call'=>$perday_call,
            'dialer_completed_call'=>$dialer_completed_call,'completed_call'=>$completed_call,
            'total_allocation_pending'=>$total_allocation_pending,'allocation_pending'=>$allocation_pending);
    }
    
    private function admin_dash($from_table,$on_table,$whereUser)
    {

            $dialer_open_total           =   DB::select("SELECT region brand,count(1)  total from $from_table `tagging_master` tm $on_table  WHERE case_close is null  $whereUser group by region");
            $tagging_perday_call           =   DB::select("SELECT region brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE DATE(tm.created_at) = CURDATE()   $whereUser group by region");
            $dialer_completed_call           =   DB::select("SELECT region brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE  case_close='1' $whereUser group by region");
            $total_pending           =   DB::select("SELECT region brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE  call_status='Part Pending' $whereUser group by region");
            $total_allocation_pending           =   DB::select("SELECT region brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE  (tm.center_id is null || tm.center_id='') $whereUser group by region");
            $total_cancel_call           =   DB::select("SELECT region brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE DATE(tm.created_at) = CURDATE() and call_status='Cancel' $whereUser group by region");
            $open_total = 0;
        if(!empty($dialer_open_total))
        {
            foreach($dialer_open_total as $tot)
            {
                $open_total += $tot->total; 
            }
        }
        else
        {
            $open_total = 0;
        }
        
        $perday_call = 0;
        if(!empty($tagging_perday_call))
        {
            foreach($tagging_perday_call as $tot)
            {
                $perday_call += $tot->total; 
            }
        }
        else
        {
            $perday_call = 0;
        }
        
        $completed_call = 0;
        if(!empty($dialer_completed_call))
        {
            foreach($dialer_completed_call as $tot)
            {
                $completed_call += $tot->total; 
            }
        }
        else
        {
            $completed_call = 0;
        }
        
        $allocation_pending = 0;
        if(!empty($total_allocation_pending))
        {
            foreach($total_allocation_pending as $tot)
            {
                $allocation_pending += $tot->total; 
            }
        }
        else
        {
            $allocation_pending = 0;
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
        
        return array('dialer_open_total'=>$dialer_open_total,'open_total'=>$open_total,
            'tagging_perday_call'=>$tagging_perday_call,'perday_call'=>$perday_call,
            'dialer_completed_call'=>$dialer_completed_call,'completed_call'=>$completed_call,
            'total_allocation_pending'=>$total_allocation_pending,'allocation_pending'=>$allocation_pending);
    }
    
    public function index(Request $request)
    {
        Session::put("page-title","Dashboard");
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        
        if($UserType=='ServiceEngineer')
        {
            return redirect("se-dash");
        }
        
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
        // if($UserType=='ServiceCenter')
        // {
        //    $whereUser = " and tm.center_id ='$Center_Id'";
        // }
        
        //get method request
        $region_id = $request->input('region_id');
        $state_name = $request->input('state_id');
        $pincode = $request->input('pincode');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $contact_no = $request->input('contact_no');
        $cust_add = $request->input('cust_add');
        $asc_code = $request->input('asc_code');
        $job_no = $request->input('job_no');
        $ticket_no = $request->input('ticket_no');
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
        if(!empty($cust_add))
        {
            $whereTag .= " and tm.Customer_Address = '$cust_add'";
        }
        if(!empty($contact_no) && strlen($contact_no)>6)
        {
            $whereTag .= " and tm.contact_no='$contact_no' or tm.alt_no='$contact_no' ";
        }
        if(!empty($asc_code) && $asc_code!='All')
        {
            $whereTag .= " and tm.center_id = '$asc_code'";
        }
        if(!empty($region_id) && $region_id!='All')
        {
            $whereTag .= " and tm.region_id = '$region_id'";
        }
        if(!empty($job_no))
        {
            $whereTag .= " and tm.job_no = '$job_no'";
        }
        if(!empty($ticket_no))
        {
            $whereTag .= " and tm.ticket_no = '$ticket_no'";
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
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
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
            /*$dialer_open_total           =   DB::select("SELECT brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE case_close is null $whereUser $whereTag group by brand");
            $tagging_perday_call           =   DB::select("SELECT brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE  1=1  $whereUser $whereTag group by brand");
            $dialer_completed_call           =   DB::select("SELECT brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE case_close='1' $whereUser $whereTag group by brand");
            $total_pending           =   DB::select("SELECT brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE 1=1 and call_status='Part Pending' $whereUser $whereTag group by brand");
            $total_allocation_pending           =   DB::select("SELECT brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE (tm.center_id is null || tm.center_id='') $whereUser $whereTag group by brand");
            $total_reservation_pending           =   DB::select("SELECT brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE (tm.center_id is null || tm.center_id='') $whereUser $whereTag group by brand");
            $total_cancel_call           =   DB::select("SELECT brand,count(1) total from $from_table `tagging_master` tm $on_table WHERE 1=1 and call_status='Cancel' $whereUser $whereTag group by brand");*/
            
                    
            $tag_data_qr = "select tm.*,center_name from $from_table tagging_master tm $on_table inner join tbl_service_centre sc on tm.center_id = sc.center_id where 1=1 $whereTag $whereUser   ";
            $DataArr = DB::select("$tag_data_qr");

        }
        else
        {
            //echo "SELECT count(1) total from $from_table `tagging_master` tm $on_table WHERE DATE(tm.created_at) = CURDATE() $whereUser"; exit;
            
            
            //$tag_data_qr = "select * from tagging_master tm where DATE(tm.created_at) = CURDATE()    "; 
        }
        
        
        $tat_wise_qry = "SELECT DATE_FORMAT(created_at,'%Y-%m-01') month,Brand,COUNT(1) cnt FROM tagging_master WHERE 1=1 
            GROUP BY DATE_FORMAT(created_at,'%Y-%m-01'), Brand ORDER BY 
            DATE_FORMAT(created_at,'%Y-%m-01'),Brand";
        $tat_brand_wise = DB::select($tat_wise_qry);
        
        $tat_month_arr = array();
        $tat_brand_arr = array();
        $tat_data = array();
        foreach($tat_brand_wise as $mb)
        {
            $mnt = date("M-y",strtotime($mb->month));
            $tat_brand_arr[] = $mb->Brand;
            $tat_month_arr[] = $mnt;
            $tat_arr_total[$mnt] += $mb->cnt;
            
        }
        
        foreach($tat_brand_wise as $mb)
        {
            $mnt = date('M-y',strtotime($mb->month));
            $tat_data[$mnt][$mb->Brand] = round($mb->cnt*100/$tat_arr_total[$mnt]).'%';
        }
        
        //repair 
        $repair_wise_qry = "SELECT DATE_FORMAT(created_at,'%Y-%m-01') month,Brand,sum(if(observation='Cancel',1,0))cncl,count(1) cnt FROM tagging_master WHERE 1=1 
            GROUP BY DATE_FORMAT(created_at,'%Y-%m-01'), Brand ORDER BY 
            DATE_FORMAT(created_at,'%Y-%m-01'),Brand";
        $repair_brand_wise = DB::select($repair_wise_qry);
        
        $rep_month_arr = array();
        $rep_brand_arr = array();
        $rep_data = array();
        foreach($repair_brand_wise as $mb)
        {
            $mnt = date("M-y",strtotime($mb->month));
            $rep_brand_arr[] = $mb->Brand;
            $rep_month_arr[] = $mnt;
            $rep_arr_total[$mnt] += $mb->cnt;
            
        }
        $rep_brand_arr[] = 'Cancel';
        foreach($repair_brand_wise as $mb)
        {
            $mnt = date('M-y',strtotime($mb->month));
            $rep_data[$mnt][$mb->Brand] = round($mb->cnt*100/$rep_arr_total[$mnt]).'%';
            $rep_data[$mnt]['Cancel'] = round($mb->cncl*100/$rep_arr_total[$mnt]).'%';
        }
        
        
        if($submit=='Download Report' || $submit=='Download In Excel')
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
                                    echo '<td>'.$record->warranty_type.'</td>';
                                    echo '<td>'.$record->service_type.'</td>';

                                    echo '<td>';

                                    if($record->warranty_card=='No' && $record->warranty_card=='No' && $record->observation=='Part Required')
                                    {
                                        if($record->case_close==='1' &&  $record->inv_status==='0' && $record->payment_entry==='0' && $record->final_symptom_status==='0')
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
                                        else if($record->case_close=='1' &&  $record->inv_status=='1')
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
        
        $dash_array = array();
        if(in_array($UserType,array('NSM','ZSM','RSM','Admin')))
        {
            $dash_array = $this->admin_dash($from_table,$on_table,$whereUser);
        }
        else if(in_array($UserType,array('ServiceCenter')))
        {
            $dash_array = $this->center_dash($from_table,$on_table,$whereUser);
        }
        
        
        $region_Qry = "SELECT * FROM `region_master` ORDER BY region_name";
        $region_list = DB::select($region_Qry);
        
        
        
        $url = $_SERVER['APP_URL'].'/dashboard';
        
        
            return view('dash')
                ->with('dialer_open_total', $dash_array['dialer_open_total'])
                ->with('open_total', $dash_array['open_total'])
                ->with('tagging_perday_call',$dash_array['tagging_perday_call'])
                ->with('perday_call',$dash_array['perday_call'])
                ->with('dialer_completed_call',$dash_array['dialer_completed_call'])
                ->with('completed_call',$dash_array['completed_call'])
                ->with('total_allocation_pending',$dash_array['total_allocation_pending'])
                ->with('allocation_pending',$dash_array['allocation_pending'])
                ->with('total_cancel_call',$total_cancel_call)
                ->with('total_pending',$total_pending)
                ->with('url', $url)
                ->with('asc_master', $asc_master)
                ->with('cust_add', $cust_add)
                ->with('region_list', $region_list)
                ->with('region_id', $region_id)
                ->with('whereTag',$whereTag)
                ->with('pin_master',$pin_master)
                ->with('state_master',$state_master)
                ->with('state',$state_name)
                ->with('pincode',$pincode)
                ->with('job_no',$job_no)
                ->with('ticket_no',$ticket_no)
                ->with('from_date',$from_date)
                ->with('to_date',$to_date)
                ->with('tat_month_arr',array_unique($tat_month_arr))
                ->with('tat_brand_arr',array_unique($tat_brand_arr))
                ->with('tat_data',$tat_data)
                ->with('rep_month_arr',array_unique($rep_month_arr))
                ->with('rep_brand_arr',array_unique($rep_brand_arr))
                ->with('rep_data',$rep_data)
                ->with('contact_no',$contact_no)
                ->with('DataArr',$DataArr);
    }
    
    public function se_dash(Request $request)
    {
        $state_id = $request->input('state_id');
        $warranty_category = $request->input('warranty_category');
        $service_type = $request->input('service_type');
        $job_status = $request->input('job_status');
        $pincode = $request->input('pincode');
        $job_no = $request->input('job_no');
        $ticket_no = $request->input('ticket_no');
        $contact_no = $request->input('contact_no');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        
        $submit = $request->input('submit');
        
        
        return view('dash-se');
    }


    public function brand_dashboard(Request $request)
    {
        Session::put("page-title","Brand Dashboard");
        $UserType = Session::get('UserType');
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;

        $date_of_register = $request->input('date_of_register');
        $ticket_no = $request->input('ticket_no');
        $job_no = $request->input('job_no');
        $dealer_name1 = $request->input('dealer_name');
        $location1 = $request->input('location');
        $contact_no = $request->input('contact_no');
        $vin_no = $request->input('vin_no');
        $vehicle_model = $request->input('vehicle_model');
        $da2_model = $request->input('da2_model');
        $ftir = $request->input('ftir');
        $ftir_no = $request->input('ftir_no');
        $visit_type = $request->input('visit_type');
        $asc_code = $request->input('asc_code');
        $job_status = $request->input('job_status');
        
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $submit = $request->input('submit');


        $whereTag = "";
        if(!empty($date_of_register))
        {   
            $date_register_arr = explode('-',$date_of_register);  krsort($date_register_arr); $from_date1 = implode('-',$date_register_arr);
        
            $whereTag .= " and date(tm.created_at) = '$from_date1' ";
        }

        if(!empty($ticket_no))
        {   
        
            $whereTag .= " and tm.ticket_no = '$ticket_no' ";
        }

        if(!empty($job_no))
        {   
        
            $whereTag .= " and tm.job_no = '$job_no' ";
        }

        if(!empty($dealer_name1))
        {   
        
            $whereTag .= " and tm.dealer_name = '$dealer_name1' ";
        }

        if(!empty($location1))
        {   
        
            $whereTag .= " and tm.Landmark = '$location1' ";
        }
        if(!empty($contact_no))
        {   
        
            $whereTag .= " and tm.Contact_No = '$contact_no' ";
        }


        if(!empty($vin_no))
        {   
        
            $whereTag .= " and tm.vin_no = '$vin_no' ";
        }

        if(!empty($vehicle_model))
        {   
        
            $whereTag .= " and tm.product_id = '$vehicle_model' ";
        }

        if(!empty($da2_model))
        {   
        
            $whereTag .= " and tm.model_id = '$da2_model' ";
        }
        if(!empty($ftir))
        {   
        
            $whereTag .= " and tm.ftir = '$ftir' ";
        }
        if(!empty($ftir_no))
        {   
        
            $whereTag .= " and tm.ftir_no = '$ftir_no' ";
        }

        if(!empty($visit_type))
        {   
        
            $whereTag .= " and tm.visit_type = '$visit_type' ";
        }
        if(!empty($asc_code) && $asc_code !="All")
        {   
        
            $whereTag .= " and tm.center_id = '$asc_code' ";
        }
        if(!empty($job_status))
        {   
        
            $whereTag .= " and tm.Job_Status = '$job_status' ";
        }
        

        

        #echo $whereTag;die;

        if(!empty($from_date) && !empty($to_date))
        {   
            $from_date_arr = explode('-',$from_date);  krsort($from_date_arr); $from_date1 = implode('-',$from_date_arr);
            $to_date_arr = explode('-',$to_date);  krsort($to_date_arr); $to_date1 = implode('-',$to_date_arr);
            $whereTag .= " and date(tm.created_at) between '$from_date1' and '$to_date1'";
        }

        
        $qry = "SELECT brand_id,brand_name  FROM  brand_master WHERE brand_status='1'";
        $brand_json           =   DB::select($qry);

        $brand_master = array();

        foreach($brand_json as $brand)
        {
            $brand_master[$brand->brand_id] = $brand->brand_name;
        }

        $g_brand_id = $request->input('brand_id');

        $model_master = array();
        $qr6 = "SELECT model_id,model_name FROM `model_master` WHERE brand_id='$g_brand_id'  and product_id='$vehicle_model' and model_status='1'";
        $model_json           =   DB::select($qr6);

        foreach($model_json as $model)
        {
            $model_master[$model->model_id] = $model->model_name;
        }


        $brand_det = BrandMaster::whereRaw("brand_id='$g_brand_id'")->first();
        $brand_name = $brand_det->brand_name;
        $brand_id = $brand_det->brand_id;
        $tagging_json = TaggingMaster::where("Brand",$brand_name)->get();
        
        $dealer_name = array();
        $location = array();

        foreach($tagging_json as $tagging)
        {
            if(!empty($tagging->dealer_name)) 
            {
                $dealer_name[$tagging->dealer_name] = $tagging->dealer_name;
            }
            if(!empty($tagging->Landmark)) 
            {
                $location[$tagging->Landmark] = $tagging->Landmark;
            }
        }

        asort($dealer_name);
        asort($location);

        $qr8 = "SELECT product_id,product_name  FROM  product_master where brand_id='$brand_id' and product_status='1' order by product_name ";
        $clarion_json           =   DB::select($qr8);

        foreach($clarion_json as $brand)
        {
            $clarion_product_master[$brand->product_id] = $brand->product_name;
        }

        $qr2 = "SELECT tsc.center_id,center_name,asc_code FROM tbl_service_centre  tsc
            INNER JOIN users us ON tsc.email_id = us.email
            WHERE sc_status='1'  order by center_name"; 
        $asc_master           =   DB::select($qr2); 


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

        $whereUser .= "and tm.Brand='$brand_name'";


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

        #echo $whereUser;die;
        $dash_array = array();
        if(in_array($UserType,array('NSM','ZSM','RSM','Admin')))
        {
            $dash_array = $this->admin_dash($from_table,$on_table,$whereUser);
        }
        else if(in_array($UserType,array('ServiceCenter')))
        {
            $dash_array = $this->center_dash($from_table,$on_table,$whereUser);
        }

        #echo $whereTag;die;

        if(!empty($whereTag))
        {
            $whereTag .= " and tm.Brand= '$brand_name'";
            $tag_data_qr = "select tm.*,center_name from $from_table tagging_master tm $on_table inner join tbl_service_centre sc on tm.center_id = sc.center_id where 1=1 $whereTag $whereUser   ";
            $DataArr = DB::select("$tag_data_qr");

        }
        else
        {
            
            //$tag_data_qr = "select * from tagging_master tm where DATE(tm.created_at) = CURDATE()    "; 
        }

        if($submit=='Download Report' || $submit=='Download In Excel')
        {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=dash-report.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            

            if($brand_id == "4"){ ?> 
            <table border="2">
                <thead>
                    <tr>
                    <th>Sr.No</th>
                    <th>Date Of Register</th>
                    <th>Ticket No.</th>
                    <th>Job No.</th>
                    <th>Dealer Name</th>
                    <th>Location</th>
                    <th>Contact No.</th>
                    <th>Vin No .</th>
                    <th>Vehicle Model</th>
                    <th>DA2- Model</th>
                    <th>System SW Version</th>
                    <th>FTIR</th>
                    <th>FTIR No.</th>
                    <th>Visit Type</th>
                    <th>Site Visit Date</th>
                    <th>Logs Taken</th>
                    <th>Issue Resolve Date</th>
                    <th>Part Replace</th>
                    <th>Date of Part Replace</th>
                    <th>Supreme 1st Analysis</th>
                    <th>Job Status</th>
                    <th>Remarks</th>
                    <th>ASC Name</th>
                    <th>ASC Location</th>
                    <th>Date of dispatch</th>
                    <th>Final Status of Job</th>
                    </tr>
                </thead>
                <tbody>

                    <?php $srno = 1;
                        foreach($DataArr as $record)
                        {
                            //$record = json_decode($record1,true);
                            echo '<tr>';
                            echo '<td>'.$srno++.'</td>';
                            echo '<td>'.date('d-m-Y', strtotime($record->created_at)).'</td>';
                            echo '<td>'.$record->ticket_no.'</td>';
                            
                            // echo '<a href="ho-tag-view?TagId='.$record->TagId.'">'.$record->ticket_no.'</a>';
                            //echo '</td>';
                            echo '<td>'.$record->job_no.'</td>';
                            echo '<td>'.$record->dealer_name.'</td>';
                            echo '<td>'.$record->Landmark.'</td>';
                            echo '<td>'.$record->Contact_No.'</td>';
                            echo '<td>'.$record->vin_no.'</td>';
                            echo '<td>'.$record->Product.'</td>';
                            echo '<td>'.$record->Model.'</td>';
                            echo '<td>'.$record->system_sw_version.'</td>';
                            echo '<td>'.$record->ftir.'</td>';
                            echo '<td>'.$record->ftir_no.'</td>';
                            echo '<td>'.$record->visit_type.'</td>';
                            echo '<td>'.$record->site_visit_date.'</td>';
                            echo '<td>'.$record->logs_taken.'</td>';
                            echo '<td>'.$record->issue_resolved_date.'</td>';
                            echo '<td>'.$record->part_replace.'</td>';
                            echo '<td>'.$record->part_replace_date.'</td>';
                            echo '<td>'.$record->supr_analysis.'</td>';
                            echo '<td>'.$record->Job_Status.'</td>';
                            echo '<td>'.$record->remarks.'</td>';
                            echo '<td>'.$record->center_name.'</td>';
                            echo '<td>'.$record->State.'</td>';
                            echo '<td>'.$record->dispatch_date.'</td>';
                            echo '<td>'.$record->final_job_status.'</td>'; 
                            echo '</tr>';
                        }
                    ?>

  

              </tbody>
            </table>
        <?php  }else{ ?>
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
                                echo '<td>'.$record->warranty_type.'</td>';
                                echo '<td>'.$record->service_type.'</td>';

                                echo '<td>';

                                if($record->warranty_card=='No' && $record->warranty_card=='No' && $record->observation=='Part Required')
                                {
                                    if($record->case_close==='1' &&  $record->inv_status==='0' && $record->payment_entry==='0' && $record->final_symptom_status==='0')
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
                                    else if($record->case_close=='1' &&  $record->inv_status=='1')
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
                                   
                            echo '</tr>';
                        }?>
                </tbody>
            </table>
       <?php }   exit;
        }
        
        
        #print_r($DataArr);die;
        #echo $ftir_no;die;
        return view('brand-dash')->with('brand_master',$brand_master)
                                    ->with('brand_id',$brand_id)
                                    ->with('dealer_name',$dealer_name)
                                    ->with('location',$location)
                                    ->with('asc_master',$asc_master)
                                    ->with('clarion_product_master',$clarion_product_master)
                                    ->with('dialer_open_total', $dash_array['dialer_open_total'])
                                    ->with('open_total', $dash_array['open_total'])
                                    ->with('tagging_perday_call',$dash_array['tagging_perday_call'])
                                    ->with('perday_call',$dash_array['perday_call'])
                                    ->with('dialer_completed_call',$dash_array['dialer_completed_call'])
                                    ->with('completed_call',$dash_array['completed_call'])
                                    ->with('total_allocation_pending',$dash_array['total_allocation_pending'])
                                    ->with('allocation_pending',$dash_array['allocation_pending'])
                                    ->with('DataArr',$DataArr)
                                    ->with('date_of_register',$date_of_register)
                                    ->with('ticket_no',$ticket_no)
                                    ->with('job_no',$job_no)
                                    ->with('dealer_name1',$dealer_name1)
                                    ->with('location1',$location1)
                                    ->with('contact_no',$contact_no)
                                    ->with('vin_no',$vin_no)
                                    ->with('vehicle_model',$vehicle_model)
                                    ->with('model_master',$model_master)
                                    ->with('da2_model',$da2_model)
                                    ->with('ftir',$ftir)
                                    ->with('ftir_no',$ftir_no)
                                    ->with('visit_type',$visit_type)
                                    ->with('asc_code',$asc_code)
                                    ->with('job_status',$job_status)
                                    ->with('from_date',$from_date)
                                    ->with('to_date',$to_date);
    }


    
}

