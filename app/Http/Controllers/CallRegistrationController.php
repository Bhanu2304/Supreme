<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use DB;
use Auth;
use Session;
use App\User;
use App\TagImage;
use App\SCPinMaster;
use App\BrandMaster;
use App\RegionMaster;
use App\StateMaster;
use App\ModelMaster;
use App\ServiceCenter;
use App\TaggingMaster;
use App\PincodeMaster;
use App\ProductMaster;
use App\SCProductMaster;
use App\ProductCategoryMaster;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CallRegistrationController extends Controller
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
    
    public function get_ticket_date(Request $request)
    {

        $ticket_no = $request->input("ticket_no");
        $ser = TaggingMaster::whereRaw("ticket_no='$ticket_no'")->first();
        echo $ser->created_at;exit;
        
        exit;
    }
    
    public function allocate_center($Brand,$Product_Detail,$Product,$Model,$pincode,$sc_id)
    {
        $center_qry = "";
        if(!empty($sc_id))
        {
            $sc_id_str = implode(",",$sc_id);
            $center_qry = " and center_id not in ($sc_id_str)";
        }
        //echo "brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model' and UserActive='1' $center_qry"; exit;
        $map_product_exist = SCProductMaster::whereRaw("brand_id='$Brand' and product_category_id='$Product_Detail' and product_id='$Product' and model_id='$Model' and UserActive='1' $center_qry")->first();
        $center_id = $map_product_exist->center_id; 
        
        
        if(!empty($center_id))
        {
            $pincode_exist = SCPinMaster::whereRaw("pincode='$pincode' and UserActive='1' and center_id='$center_id' $center_qry")->first();
            if(!empty($pincode_exist))
            {
                $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
                if(empty($center_det))
                {
                    $sc_id[] = $center_id;
                    return $this->allocate_center($Brand,$Product_Detail,$Product,$Model,$pincode,$sc_id);
                }
                else
                {
                    return $center_det;
                }
            }
            else
            {
                $sc_id[] = $center_id;
                return $this->allocate_center($Brand,$Product_Detail,$Product,$Model,$pincode,$sc_id);
            }
        }
        else
        {
            return array();
        }
    }

    public function index(Request $request)
    {
        Session::put("page-title","Call Registration");

        if($request->isMethod('post'))
        {
            $file = $request->file('call_file');

            if ($file->getClientOriginalExtension() == 'pdf') {
                Session::flash('error', 'PDF files are not allowed.');
                return redirect("call-registration-form");
            }

            $data = Excel::toArray([], $file);
            $rowData = $data[0];
            $rowData = array_slice($rowData, 1);
            //print_r($rowData);die;
            $dealer_name = $rowData[3][1];
            $prodct_model = $rowData[3][5];
            $contact_person = $rowData[4][1];
            $model_variant = $rowData[4][5];
            $contact_no = $rowData[5][1];
            $vin_no = $rowData[5][5];

            $location = $rowData[6][1];
            $sale_date = $rowData[6][5];
            $sale_date_format = Date::excelToTimestamp($sale_date);
            $sale_date_for = date('d-m-Y', $sale_date_format);

            #print_r($rowData[7]);die;
            $claim_date = $rowData[7][1];
            $mielage = $rowData[7][5];
            $claim_date_format = Date::excelToTimestamp($claim_date);
            $claim_date_for = date('d-m-Y', $claim_date_format);

            $system_sw_version = $rowData[9][1];

            $phone_make = $rowData[9][5];
            $phone_model = $rowData[10][5];

            $serial_no = $rowData[12][1];
            $customer_complaint = $rowData[16][3];
            $super_analysis = $rowData[19][0];
            #print_r($rowData[15]);die;
            $super = explode(':', $super_analysis);
            $super_analysis1  = $super[1];
            $remarks = $rowData[15][0];
            $remark = explode(':', $remarks);
            $remarks1  = $remark[1];
            $error = "";
            $video_yes = $rowData[29][1];
            $video_no = $rowData[29][2];

            $savedata['DealerName']=$dealer_name;
            $savedata['Customer_Name']  = $contact_person;

            // $savedata['phone_make']  = $phone_make;
            // $savedata['phone_model']  = $phone_model;
            $savedata['mobile_handset_model'] = $phone_make . $phone_model;
            $Center_Id = Auth::user()->table_id;
            if(!empty($Center_Id))
            {
                $savedata['center_id']  = $Center_Id;
            }
            
            $video =  "";
            if($video_yes != "")
            {
                $video = "Yes";

            }else if($video_no != "")
            {
                $video = "No";
            }

            $visit_date = $rowData[34][1];
            $visit_date_format = Date::excelToTimestamp($visit_date);
            $visit_date_for = date('d-m-Y', $visit_date_format);

            
            #$product_qr = "SELECT product_id,product_name  FROM  product_master where brand_id='4' and product_status='1' ";
            $product_qr = "SELECT product_id, product_name,product_category_id FROM product_master WHERE brand_id='4' AND product_status='1' AND LOWER(product_name) = LOWER('$prodct_model') limit 1;";
            $clarion_json           =   DB::select($product_qr);
            
            // if (!empty($clarion_json)) 
            // {
            //     $Product = $clarion_json[0]->product_name;
            //     $product_id = $clarion_json[0]->product_id;
            //     $product_category_id = $clarion_json[0]->product_category_id;

            //     $product_catedet = ProductCategoryMaster::whereRaw("brand_id='4' and product_category_id='$product_category_id'")->first();
            //     $category_name = $product_catedet->category_name;
                
            //     $savedata['Product_Detail'] = $category_name;
            //     $savedata['product_category_id'] = $product_category_id;
            //     $savedata['Product'] = $Product; 
            //     $savedata['product_id'] = $product_id;

            // } else{
            //     $error .= "Invalid Vehicle Model '$prodct_model'<br>";
            // }
            
            $savedata['vehicle_variant'] = $prodct_model;
            $model_qry = "SELECT mm.model_id, mm.model_name FROM model_master mm 
                    INNER JOIN product_master pm ON mm.product_id = pm.product_id AND pm.product_status='1' AND mm.model_status='1'
                    WHERE mm.product_id = '$product_id' and LOWER(mm.model_name) = LOWER('$model_variant')";
            $model_master = DB::select($model_qry);


            // if(!empty($model_master)) 
            // {
            //     $model_name = $model_master[0]->model_name;
            //     $model_id = $model_master[0]->model_id;  

            //     $savedata['model_id'] = $model_id;
            //     $savedata['Model'] = $model_name;

            // } else{

            //     $error .= "Invalid Vehicle Variant '$model_variant'<br>";
            // }

            $savedata['Landmark']=$location;
            $savedata['Contact_No']= $contact_no;
            $savedata['vin_no']  =  $vin_no;
            $savedata['vehicle_sale_date']  =  $sale_date_for;
            $savedata['mielage']  =  $mielage;
            $savedata['system_sw_version']=$system_sw_version;
            $savedata['man_ser_no']=$serial_no;
            // $savedata['ccsc']=$customer_complaint;
            // $savedata['supr_analysis']  =  $super_analysis1;
            // $savedata['remarks']  =  $remarks1;

            $savedata['ccsc'] = $super_analysis1 . $remarks1;
            $savedata['remarks']  =  $customer_complaint;

            $savedata['videos']  =  $video;
            $savedata['site_visit_date']  =  $visit_date_for;
            
            $savedata['brand_id'] = 4;
            $savedata['Brand'] = 'CLARION';
            $savedata['tagging_type'] = 'CRF';
            
            $UserId = Auth::user()->id;    
            $savedata['created_by'] = $UserId;
            $savedata['created_at'] = date('Y-m-d H:i:s');

            if(!empty($error)) {
                Session::flash('error', $error);
                return redirect("call-registration-form");
            }else{
                #print_r($savedata);die;
                $exist_vin_no = TaggingMaster::whereRaw("vin_no='$vin_no'")->first();
                if(empty($exist_vin_no))
                {
                    $tagging_arr = new TaggingMaster($savedata);
                    $tagging_arr->save();

                    #print_r($tagging_arr);die;
                    $lastInsertTagid = $tagging_arr->id;
                    
                    #print_r($DataArr);die;
                    //Session::flash('message', " $ticket_no Tickets Generate Successfully".$alloc_qry);
                    $redirectUrl = route('ho-tag-view-cl') . '?TagId=' . $lastInsertTagid;
                    return redirect($redirectUrl);
                }else{
                    
                    $tagging_arr = new TaggingMaster($savedata);
                    $tagging_arr->save();
                    $lastInsertTagid = $tagging_arr->id;

                    #Session::flash('error', "model_open");
                    $url = $_SERVER['APP_URL'].'/call-registration-form';
                    #return redirect("call-registration-form")->with('lastInsertTagid', $lastInsertTagid);
                    return view('registration-form')->with('error','model_open')->with('last_insert_id',$lastInsertTagid);

                    // Session::flash('error', "model_open");
                    // return redirect("call-registration-form");
                }

                
                #ho-tag-view-cl?TagId=238
                #return redirect("call-registration-form");
            }
            
        }
    
        $url = $_SERVER['APP_URL'].'/registration-form';
        
        return view('registration-form')->with('url', $url);
    }
    
    public function index_old(Request $request)
    {
        Session::put("page-title","Call Registration");

        $fix_headers = [
            'Dealer Name', 'Location', 'Region', 'State', 'Pincode', 'Contact Person', 'Contact No',
            'Vehicle Sale Date', 'VIN No', 'Mielage Km. / PDI','Warranty Status','Vehicle Model', 'DA2 - Part number', 'System SW Version',
            'Manufacturer serial number', 'Customer Complaint', 'New Job Card', 'Videos', 'CRF', 'FTIR',
            'FTIR No', 'Supreme 1st Analysis', 'Remarks', 'Type of Issue Suspected', 'Issue Category',
            'Mobile Handset Model',
        ];

        if($request->isMethod('post'))
        {
            $file = $request->file('call_file');
            $filePath = $file->getRealPath();

            $handle = fopen($filePath, 'r');

            $header = fgetcsv($handle, 1000, ',');

            $missingHeaders = array_diff($fix_headers, $header);
            $extraHeaders = array_diff($header, $fix_headers);

            if(!empty($missingHeaders) || !empty($extraHeaders))
            {
                
                $error = "";
                if(!empty($missingHeaders))
                {
                    $error .= " Missing Headers: " . implode(', ', $missingHeaders) .'</br>';
                }
                if(!empty($extraHeaders))
                {
                    $error .= " Extra Headers: " . implode(', ', $extraHeaders) .'</br>';
                }
                fclose($handle);
                Session::flash('error', $error);
                Session::flash('alert-class', 'alert-danger');
                return redirect()->back();
            }
            $DataArr = array();
            $error = "";
            $rowNumber = 0;

            $region_qry = "SELECT region_id,region_name FROM region_master WHERE region_status='1' order by region_name";
            $reg_json           =   DB::select($region_qry);


            $state_qry = "SELECT state_id,state_code,state_name FROM state_master  order by state_name";
            $state_json           =   DB::select($state_qry);
            #print_r($reg_json);die;

            $product_qr = "SELECT product_id,product_name  FROM  product_master where brand_id='4' and product_status='1' ";
            $clarion_json           =   DB::select($product_qr);

            while(($row = fgetcsv($handle, 1000, ',')) !== false) 
            {
                $rowNumber++;
                $dealer_name = $row[0];
                $location = $row[1];
                #$region_name = $row[2];
                $region_name = ucfirst(strtolower($row[2]));
                $state_name = ucfirst(strtolower($row[3]));
                $pincode = $row[4];
                $contact_person = $row[5];
                $contact_no = $row[6];
                $number_format = '/^\d{1,10}$/';
                $vehicle_date = $row[7]; 
                $date_format = '/^\d{2}-\d{2}-\d{4}$/';
                $vin_no = $row[8];
                $mielage = $row[9];
                $warranty_type = $row[10];
                $warranty_type_upper = strtoupper($warranty_type);
                $allowed_warranty_types = ['IN', 'OUT'];
                $prodct_model = $row[11];
                $part_number = $row[12];
                $system_sw_version = $row[13];
                $man_ser_no = $row[14];
                $customer_complaint = $row[15];
                $job_card = ucfirst(strtolower($row[16]));
                $allowed_job_card = ['Yes', 'No'];

                $videos = ucfirst(strtolower($row[17]));
                $allowed_video = ['Yes', 'No'];

                $crf = ucfirst(strtolower($row[18]));
                $allowed_crf = ['Yes', 'No'];

                $ftir = ucfirst(strtolower($row[19]));
                $allowed_ftir = ['Yes', 'No'];

                $ftir_no = $row[20];
                $supr_analysis = $row[21];
                $remarks = $row[22];

                $issue_type = $row[23];
                $issue_type_lower = strtoupper($issue_type);
                $allowed_issue_type = ['HW', 'SW'];

                $issue_cat = $row[24];
                $handset_model = $row[25];

                if ($dealer_name !== "") {
                    $rowData['dealer_name'] = $dealer_name;
                    $rowData['DealerName'] = $dealer_name;
                } else {
                    $error .= "Dealer name is empty in row no. $rowNumber<br>";
                }

                if ($location !== "") {
                    $rowData['Landmark']=$location; 
                } else {
                    $error .= "Location is empty in row no. $rowNumber<br>";
                }

                $matchedRegion = null;

                foreach($reg_json as $region)
                {
                    if ($region->region_name == $region_name) {
                        $matchedRegion = $region;
                        break;
                    }
                }

                if($matchedRegion) 
                {

                    $rowData['region_id']  =  $matchedRegion->region_id;
                    $rowData['region']  =  $matchedRegion->region_name;
                    
                } else {
                
                    $error .= "Invalid region_name '$region_name' in row no. $rowNumber<br>";
                }


                $matchedState = null;

                foreach($state_json as $state)
                {
                    if ($state->state_name == $state_name) {
                        $matchedState = $state;
                        break;
                    }
                    #$state_id = $state->state_id;
                }
                
                if($matchedState) 
                {

                    $rowData['state']  =  $matchedState->state_name;
                    $rowData['state_code']  =  $matchedState->state_code;
                    
                } else {
                
                    $error .= "Invalid State '$state_name' in row no. $rowNumber<br>";
                }

                $state_id = $matchedState->state_id;
                $pin_master = DB::select("SELECT dist_id,pincode from pincode_master where state_id='$state_id'");

                $matchedpincode = null;
    
                foreach($pin_master as $pin)
                {

                    if ($pin->pincode == $pincode) {
                        $matchedpincode = $pin;
                        break;
                    }
                    #$state_id = $state->state_id;
                }
                #print_r($matchedpincode->pincode);die;

                if($matchedpincode) 
                {

                    $rowData['Pincode']=$matchedpincode->pincode;
                    $rowData['dist_id']=$matchedpincode->dist_id;
                    
                } else {
                
                    $error .= "Invalid Pincode '$pincode' in row no. $rowNumber<br>";
                }

                $rowData['Customer_Name']  = $contact_person;

                if (preg_match($number_format, $contact_no)) {
                  
                    $rowData['Contact_No']= $contact_no;

                } else {

                    $error .= "Invalid Contact Number '$pincode' in row $rowNumber: Length exceeds 10 digits<br>";
                }

                


                if(preg_match($date_format, $vehicle_date))
                {
                    $rowData['vehicle_sale_date']  =  $vehicle_date;
                    
                }else{
            
                    $error .= "Invalid Vehicle Sale Date format in row no. $rowNumber<br>";
                    Session::flash('error', $error);

                }


                if ($vin_no !== "") {
                    $rowData['vin_no']  =  $vin_no;

                } else {

                    $error .= "Vin No. is empty in row no. $rowNumber<br>";
                }

                if ($mielage !== "") {
                    $rowData['mielage']  =  $mielage; 
                     
                } else {

                    $error .= "Mielage Km. / PDI is empty in row no. $rowNumber<br>";
                }

                if (!empty($warranty_type) && in_array($warranty_type_upper, $allowed_warranty_types)) 
                {
                    $rowData['warranty_type']  =  $warranty_type;

                }else{
                    $error .= "Invalid Warranty Type at row $rowNumber: Should not be empty and must be 'IN' or 'OUT'<br>";
                }

                $matchedProduct = null;

                // Find matching product in $clarion_json
                foreach ($clarion_json as $clarion) {
                    if (strtolower($clarion->product_name) == strtolower($prodct_model)) {
                        $matchedProduct = $clarion;
                        break;
                    }
                }

                if ($matchedProduct) {
                    // Extract data for matching product
                    $rowData['Product'] = $matchedProduct->product_name; 
                    $rowData['product_id'] = $matchedProduct->product_id;
                } else {
                    $error .= "Invalid Vehicle Model '$prodct_model' in row no. $rowNumber<br>";
                }

                if ($matchedProduct) {
                    // Search for matching part number in the model_master
                    $qry = "SELECT mm.model_id, mm.model_name FROM model_master mm 
                            INNER JOIN product_master pm ON mm.product_id = pm.product_id AND pm.product_status='1' AND mm.model_status='1'
                            WHERE mm.product_id = '$matchedProduct->product_id'";
                    $model_master = DB::select($qry);

                    $matchedPart = null;

                    foreach ($model_master as $model) {
                        if (strtolower($model->model_name) == strtolower($part_number)) {
                            $matchedPart = $model;
                            break;
                        }
                    }

                    if ($matchedPart) {
                        // Extract data for matching part number
                        $rowData['model_id'] = $matchedPart->model_id;
                        $rowData['Model'] = $matchedPart->model_name;
                    } else {
                        $error .= "Invalid DA2 - Part number Not IN Model '$part_number' in row no. $rowNumber<br>";
                    }
                } else {
                    // Handle the case where $matchedProduct is not set
                    $error .= "Cannot find a matching product for '$prodct_model' in row no. $rowNumber<br>";
                }


                if ($system_sw_version !== "") {
                    $rowData['system_sw_version']=$system_sw_version; 
                } else {
                    $error .= "System SW Version is empty in row no. $rowNumber<br>";
                }

                if ($customer_complaint !== "") {
                    $rowData['ccsc']=$customer_complaint; 
                } else {
                    $error .= "Customer Complaint is empty in row no. $rowNumber<br>";
                }


                $rowData['man_ser_no']=$man_ser_no;

                if (!empty($job_card) && in_array($job_card, $allowed_job_card)) 
                {
                    $rowData['job_card']  =  $job_card; 

                }else{
                    $error .= "Invalid Job Card at row $rowNumber: Should not be empty and must be 'Yes' or 'No'<br>";
                }

                if (!empty($videos) && in_array($videos, $allowed_video)) 
                {
                    $rowData['videos']  =  $videos; 

                }else{
                    $error .= "Invalid Video at row $rowNumber: Should not be empty and must be 'Yes' or 'No'<br>";
                }

                if (!empty($crf) && in_array($crf, $allowed_crf)) 
                {
                    $rowData['crf']  =  $crf;

                }else{
                    $error .= "Invalid CRF at row $rowNumber: Should not be empty and must be 'Yes' or 'No'<br>";
                }

                if (!empty($ftir) && in_array($ftir, $allowed_ftir)) 
                {
                    $rowData['ftir']  =  $ftir ;

                }else{
                    $error .= "Invalid FTIR at row $rowNumber: Should not be empty and must be 'Yes' or 'No'<br>";
                }
                
                
                $rowData['ftir_no']  =  $ftir_no ;

                
                if ($supr_analysis !== "") {
                    $rowData['supr_analysis']  =  $supr_analysis;
                } else {
                    $error .= "Supreme 1st Analysis is empty in row no. $rowNumber\n";
                }

                
                $rowData['remarks']  =  $remarks;

            
                if(!empty($issue_type) && in_array($issue_type_lower, $allowed_issue_type)) 
                {
                    $rowData['issue_type']  =  $issue_type;

                }else{
                    $error .= "Invalid Type of Issue Suspected at row $rowNumber: Should not be empty and must be 'HW' or 'SW'<br>";
                }

                if ($issue_cat !== "") {

                    $rowData['issue_cat']  =  $issue_cat;
                } else {
                    $error .= "Issue Category is empty in row no. $rowNumber\n";
                }
                
                #$rowData['mobile_handset_model'] = $handset_model;
                $rowData['brand_id'] = 4;
                $rowData['Brand'] = 'CLARION';

                $UserId = Auth::user()->id;    
                $rowData['created_by']=$UserId;
                $rowData['created_at']=date('Y-m-d H:i:s'); 

                $UserType = Session::get('UserType');
                $alloc_qry = "Case Not Allocated.";
                if($UserType=='ServiceCenter')
                {
                    $center_id = Auth::user()->table_id;
                    $rowData['center_id']=$center_id;
                    $rowData['center_allocation_date']=date('Y-m-d H:i:s');
                    $rowData['center_allocation_by']=$UserId;
                    
                    $center_det = ServiceCenter::whereRaw("center_id='$center_id'")->first();
                    $rowData['asc_code']=$center_det->asc_code;
                    $alloc_qry = "And Case Allocated To ASC ".$center_det->center_name;
                    
                }else
                {
                    $center_det = $this->allocate_center('CLARION',$Product_Detail,$prodct_model,$part_number,$pincode,array());

                    if(!empty($center_det))
                    {
                        $center_id=$center_det->center_id;
                        $rowData['center_id']=$center_id;
                        $rowData['asc_code']=$center_det->asc_code;
                        $rowData['center_allocation_date']=date('Y-m-d H:i:s');
                        
                        $alloc_qry = "And Case Allocated To ASC ".$center_det->center_name;
                    }
                }

                $year = date('y');
                $month = date('m');

                $qr_max_no = "SELECT MAX(sr_no) srno FROM `tagging_master` WHERE  job_year='$year' AND job_month='$month'";
                $max_json           =   DB::select($qr_max_no);
                $max = $max_json[0];
                $sr_no = $max->srno;
                $str_no = "000000";
                $sr_no = $sr_no+$rowNumber;
                $len = strlen($str_no);
                $newlen = strlen("$sr_no");
                $new_no = substr_replace($str_no, $sr_no, $len-$newlen,$newlen);
                $subcode = 'CL';
                $ticket_no  = "{$subcode}{$year}{$month}{$new_no}";
                $rowData['ticket_no']=$ticket_no;
                $rowData['job_year']=$year;
                $rowData['job_month']=$month;
                $rowData['sr_no']=$sr_no;

                $DataArr[] = $rowData;
            }

            fclose($handle);
            
            if(!empty($error)) {
                Session::flash('error', $error);
                return redirect("call-registration-form");
            }else{
                #print_r($DataArr);die;
                foreach ($DataArr as $data) 
                {
                    #print_r($data);die;
                    $tagging_arr = new TaggingMaster($data);
                    $tagging_arr->save();
                }
                #print_r($DataArr);die;
                Session::flash('message', " $rowNumber Tickets Generate Successfully");
                return redirect("call-registration-form");
            }
            
        }
    
        $url = $_SERVER['APP_URL'].'/registration-form';
        
        return view('registration-form')->with('url', $url);
    }
    

}

