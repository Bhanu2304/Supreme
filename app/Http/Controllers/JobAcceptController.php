<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SparePart;
use App\StateMaster;
use App\PincodeMaster;
use Auth;
use Session;
use DB;
use App\TaggingMaster;
use App\RegionalManagerMaster;
use App\InvPart;
use App\TagPart;
use App\BrandMaster;
use App\ProductCategoryMaster;
use App\ProductMaster;
use App\ModelMaster;

use App\ServiceEngineer;
use Illuminate\Support\Facades\Storage;
use App\Exports\VendorExport;
use Maatwebsite\Excel\Facades\Excel;

class JobAcceptController extends Controller
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
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $TagId = $request->input('tagId'); 
        
        $tagDet = TaggingMaster::whereRaw("TagId='$TagId'")->first();
        $brand_name = $tagDet->Brand;
        $job_no_exist = $tagDet->job_no;
        $taggingArr = array();
        if(empty($job_no_exist))
        {
            $year = date('y');
            $month = date('m');
            $day = date('d');
            
            $new_month = date('M');
            switch ($new_month) {
                case 'Jan':
                    $mvalue ="A";
                    break;

                    case 'Feb':
                        $mvalue ="B";
                        break;

                        case 'Mar':
                            $mvalue ="C";
                            break;

                            case 'Apr':
                                $mvalue ="D";
                                break;

                                case 'May':
                         $mvalue ="E";
                           break;

                           case 'Jun':
                          $mvalue ="F";
                                break;

                             case 'Jul':
                                   $mvalue ="G";
                                 break;

                                 case 'Aug':
                                   $mvalue ="H";
                                   break;

                                 case 'Sep':
                                   $mvalue ="I";
                                   break;

                                 case 'Oct':
                                   $mvalue ="J";
                                   break;

                                 case 'Nov':
                                   $mvalue ="K";
                                   break;
                
                default:
                    $mvalue ="L";
                    break;
            }

            $qr_max_no = "SELECT MAX(sr_no) srno FROM `tagging_master` WHERE  job_year='$year' AND job_month='$month'";
            $max_json           =   DB::select($qr_max_no);
            $max = $max_json[0];
            $sr_no = $max->srno;

            $str_no = "00000";
            $sr_no = $sr_no+1;
            $len = strlen($str_no);
            $newlen = strlen("$sr_no");
            $new_no = substr_replace($str_no, $sr_no, $len-$newlen,$newlen);
            $short_brand_name = substr($brand_name, 0, 2);
            //$job_no = "$short_brand_name"."$month"."$day"."$year".$new_no;
            $brand_name = strtoupper($short_brand_name);
            $job_no = "$brand_name"."$year"."$mvalue"."$day".$new_no; 

            $taggingArr['job_no']=$job_no;
            $taggingArr['job_year']=$year;
            $taggingArr['job_month']=$month;
        }
        else
        {
            $job_no = $job_no_exist;
        }
        
        $taggingArr['sr_no']=$sr_no;
        $taggingArr['job_accept']=1;
        $taggingArr['job_accept_by']=$UserId;
        $taggingArr['job_accept_date']=date('Y-m-d H:i:s');
        
        //print_r($taggingArr); exit;
        
        if(TaggingMaster::whereRaw("TagId='$TagId' and job_accept='0'")->update($taggingArr))
        {
            echo json_encode(array('resp_id'=>'1',"job_no"=>$job_no));exit;
        }
        else
        {
            echo json_encode(array('resp_id'=>'2',"job_no"=>""));exit;
        }
        
    }
    
    public function reject(Request $request)
    {   
        $UserId = Session::get('UserId');
        $Center_Id = Auth::user()->table_id;
        $TagId = $request->input('tagId'); 
        $reason = $request->input('reason'); 

        $taggingArr['job_reject']=1;
        $taggingArr['job_accept']='0';
        $taggingArr['job_reject_by']=$UserId;
        $taggingArr['job_reject_date']=date('Y-m-d H:i:s');
        $taggingArr['job_reject_reason']= $reason;
        $tagDet = TaggingMaster::whereRaw("TagId='$TagId'")->first();
        if(!empty($tagDet->job_no))
        {$job_no = $tagDet->job_no;}
        else
        {
            $job_no = $tagDet->ticket_no;
        }
        //print_r($taggingArr); exit;
        //$qry = TaggingMaster::whereRaw("TagId='$TagId' and job_reject='0' and se_id is null and observation is null")->get();
        //print_r($qry);exit;
        if(TaggingMaster::whereRaw("TagId='$TagId' and job_reject='0' and se_id is null and observation is null")->update($taggingArr))
        {
            echo json_encode(array('resp_id'=>'1',"job_no"=>$job_no));exit;
        }
        else
        {
            echo json_encode(array('resp_id'=>'2',"job_no"=>""));exit;
        }
        
    }
    
    
     
    
}

