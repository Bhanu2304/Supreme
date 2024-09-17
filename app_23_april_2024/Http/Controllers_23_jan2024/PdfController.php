<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PDF;
use App\TaggingMaster;
use App\InvoicePart;
use App\SparePart;
use App\ServiceCenter;
use App\StateMaster;
use App\OutwardInvoice;
use App\ApproveRequestInventory;
use App\ApproveRequestInventoryPart;
use DB;

class PdfController extends Controller

{

    public function generatePDF(Request $request)

    {
        $TagId = $request->input('TagId');
        $tag = TaggingMaster::whereRaw("TagId = '$TagId' ")->first();
        $data = ['data'=>$tag];
        //print_r($data); exit;
        if($tag->Brand=='JVC')
        {
            $pdf = PDF::loadView('jvcPDF', $data); 
            return $pdf->download('jvc-'.$TagId.'.pdf');
            //return view('jvcPDF')->with('tag',$tag);
        }
        else
        {
            $qr4 = "SELECT field_name,sub_field_name FROM `condition_master` 
WHERE con_status='1'
order BY field_name,sub_field_name";
        $con_json           =   DB::select($qr4);
        $set_con_master = array();
        foreach($con_json as $set_con)
        {
            $set_con_master[$set_con->field_name][] = $set_con->sub_field_name;
        }
            $data['set_cndn'] = $set_con_master;
            $pdf = PDF::loadView('pioneer', $data); 
            
            //return view('pioneer')->with('data',$tag)->with('set_cndn',$set_con_master);
            return $pdf->download('pioneer-'.$TagId.'.pdf');
        }
        
    // return $pdf->download('hdtuto.pdf');
	//return view('jvcPDF');
    }

    public function view_pdf(Request $request)

    {
        $TagId = $request->input('TagId');
        $tag = TaggingMaster::whereRaw("TagId = '$TagId' ")->first();
        $data = ['data'=>$tag];
        //print_r($data); exit;
        if($tag->Brand=='JVC')
        {
            $pdf = PDF::loadView('jvcPDF', $data); 
            //return $pdf->download('jvc-'.$TagId.'.pdf');
            return view('jvcPDF')->with('tag',$tag);
        }
        else
        {
            $qr4 = "SELECT field_name,sub_field_name FROM `condition_master` 
WHERE con_status='1'
order BY field_name,sub_field_name";
        $con_json           =   DB::select($qr4);
        $set_con_master = array();
        foreach($con_json as $set_con)
        {
            $set_con_master[$set_con->field_name][] = $set_con->sub_field_name;
        }
            $data['set_cndn'] = $set_con_master;
            $pdf = PDF::loadView('pioneer', $data); 
            
            return view('pioneer')->with('data',$tag)->with('set_cndn',$set_con_master);
            //return $pdf->download('pioneer-'.$TagId.'.pdf');
        }
        
    // return $pdf->download('hdtuto.pdf');
	//return view('jvcPDF');
    }
    
    
public function generateTXInvoice(Request $request)

    {
            $TagId = $request->input('TagId');
            $tag = TaggingMaster::whereRaw("TagId = '$TagId' ")->first();
            $invoice = InvoicePart::whereRaw("Tag_Id = '$TagId' ")->get();
            $data = ['data'=>$tag,'Invo'=>$invoice];

            $pdf = PDF::loadView('txinvoice', $data); 
            //return view('pioneer');
            //   return $pdf->download('pioneer-'.$TagId.'.pdf');
        
            return $pdf->download('invoice-'.$TagId.'.pdf');
            //return view('txinvoice');
    }

    public function generateChallan(Request $request)
    {
        $ApproveId = $request->input('approve_id');
        $record = ApproveRequestInventory::whereRaw("approve_id='$ApproveId'")->first();
        $center_id = $record->center_id; 
        $part_arr = ApproveRequestInventoryPart::whereRaw("approve_id='$ApproveId'")->get();
        $center_details = ServiceCenter::whereRaw("center_id='$center_id'")->first();
        $state_id = $center_details->state; 
        $state = StateMaster::whereRaw("state_id='$state_id'")->first();
        
        
        
        $data['record'] = $record;
        $data['part_arr'] = $part_arr;
        $data['sc_details'] = $center_details;
        $data['state'] = $state;
        
        
            $pdf = PDF::loadView('challan', $data); 
            //return view('pioneer');
         //   return $pdf->download('pioneer-'.$TagId.'.pdf');
        
            //return $pdf->download('challan.pdf');
            return view('challan')
                    ->with('record',$record)
                    ->with('part_arr',$part_arr)
                    ->with('sc_details',$center_details)
                    ->with('state',$state);
    }
    
    
public function convertNumberToWordsForIndia($strnum)
{
        $words = array(
        '0'=> '' ,'1'=> 'one' ,'2'=> 'two' ,'3' => 'three','4' => 'four','5' => 'five',
        '6' => 'six','7' => 'seven','8' => 'eight','9' => 'nine','10' => 'ten',
        '11' => 'eleven','12' => 'twelve','13' => 'thirteen','14' => 'fouteen','15' => 'fifteen',
        '16' => 'sixteen','17' => 'seventeen','18' => 'eighteen','19' => 'nineteen','20' => 'twenty',
        '30' => 'thirty','40' => 'fourty','50' => 'fifty','60' => 'sixty','70' => 'seventy',
        '80' => 'eighty','90' => 'ninty');
		
		//echo $strnum = "2070000"; 
		 $len = strlen($strnum);
		 $numword = "Rupees ";
		while($len!=0)
		{
			if($len>=8 && $len<= 9)
			{
				$val = "";
				
				
				if($len == 9)
				{
					$value1 = substr($strnum,0,1);
					$value2 = substr($strnum,1,1);
					$value = $value1 *10 + $value2;
					$value1 =$value1*10;
					$val  = $value;
					//$numword.= $words["$value"]." ";
					$len = 7;
					$strnum =   substr($strnum,2,7);
				}
				if($len== 8)
				{
					$value = substr($strnum,0,1);
					$val = $value;
					//$numword.= $words["$value"]." ";
					$len =7;
					$strnum =   substr($strnum,1,7);
				}
				if($value <=19)
				{
					$numword.= $words["$value"]." ";
				}
				else
				{
					$numword.= $words["$value1"]." ";
					$numword.= $words["$value2"]." ";
				}				
				if($val == 1)
				{
					$numword.=  "Crore ";
				}
				else if($val == 0)
				{
					
				}
				else
				{
				$numword.=  "Crores ";
				}
				
			}
			if($len>=6 && $len<= 7)
			{
				$val = "";
				
				
				if($len == 7)
				{
					$value1 = substr($strnum,0,1);
					$value2 = substr($strnum,1,1);
					$value = $value1 *10 + $value2;
					$value1 =$value1*10;
					$val  = $value;
					//$numword.= $words["$value"]." ";
					$len = 5;
					$strnum =   substr($strnum,2,7);
				}
				if($len== 6)
				{
					$value = substr($strnum,0,1);
					$val = $value;
					//$numword.= $words["$value"]." ";
					$len =5;
					$strnum =   substr($strnum,1,7);
				}
				if($value <=19)
				{
					$numword.= $words["$value"]." ";
				}
				else
				{
					$numword.= $words["$value1"]." ";
					$numword.= $words["$value2"]." ";
				}				
				if($val == 1)
				{
					$numword.=  "Lakh ";
				}
				else if($val == 0)
				{
					
				}
				else
				{
				$numword.=  "Lakhs ";
				}
				
			}
		
			if($len>=4 && $len<= 5)
			{
				$val = "";
				if($len == 5)
				{
					$value1 = substr($strnum,0,1);
					$value2 = substr($strnum,1,1);
					$value = $value1 *10 + $value2;
					$value1 =$value1*10;
					$val  = $value;
					//$numword.= $words["$value"]." ";
					$len = 3;
					$strnum =   substr($strnum,2,4);
				}
				if($len== 4)
				{
					$value = substr($strnum,0,1);
					$val = $value;
					//$numword.= $words["$value"]." ";
					$len =3;
					$strnum =   substr($strnum,1,3);
				}
				if($value <=19)
				{
					$numword.= $words["$value"]." ";
				}
				else
				{
					$numword.= $words["$value1"]." ";
					$numword.= $words["$value2"]." ";
				}				
				if($val == 1)
				{
					$numword.=  "Thousand ";
				}
				else if($val == 0)
				{
					
				}
				else
				{
					$numword.=  "Thousand ";
				}
			}
			if($len==3)
			{
				$val = "";
				$value = substr($strnum,0,1);
				$val  = $value;
				$numword.= $words["$value"]." ";
				$len = 2;
				$strnum =   substr($strnum,1,2);

				if($val == 1)
				{
					$numword.=  "Hundred ";
				}
				else if($val == 0)
				{
					
				}
				else
				{
					$numword.=  "Hundred ";
				}
			}
			if($len>=1 && $len<= 2)
			{
				if($len ==2)
				{
				$value = substr($strnum,0,1);
				$value = $value *10;
				$value1 = $value;
				$strnum =   substr($strnum,1,1);
				$value2 = substr($strnum,0,1);
				$value =$value1 + $value2;				
				}
				if($len ==1)
				{	
					$value = substr($strnum,0,1);
					
				}
				if($value <=19)
				{
					$numword.= $words["$value"]." ";
					$len =0;
				}
				else
				{
					$numword.= $words["$value1"]." ";
					$numword.= $words["$value2"]." ";
					$len =0;
				}
				$numword.=  "Only ";

			}
			
			break;
		}
		return ucwords(strtolower($numword));

}
    
    
    public function invoice_pdf(Request $request)
    {
        $invoice_id = $request->input('invoice_id');
        $record_arr_det = OutwardInvoice::whereRaw("invoice_id='$invoice_id'")->get();
        $record_arr = array();
        foreach($record_arr_det as $record)
        {
            $center_id = $record->center_id;  
            //echo $record->grand_total; exit;
            $record->uc_total = $this->convertNumberToWordsForIndia(round($record->grand_total));
            $record_arr[] = $record;
        }
        $center_details = ServiceCenter::whereRaw("center_id='$center_id'")->first();
        //$part_arr = ApproveRequestInventoryPart::whereRaw("approve_id='$ApproveId'")->get();
        
        $state_id = $center_details->state; 
        $state = StateMaster::whereRaw("state_id='$state_id'")->first();
        
        
        
        $data['record_arr'] = $record_arr;
        $data['part_arr'] = $part_arr;
        $data['sc_details'] = $center_details;
        $data['state'] = $state;
        
        
            $pdf = PDF::loadView('invoice', $data); 
            //return view('pioneer');
            return $pdf->download('invoice-'.$TagId.'.pdf'); exit;
        
            //return $pdf->download('challan.pdf');
            return view('invoice')
            ->with('sc_details',$center_details)
                   ->with('data',$data) ;
    }
    
public function invoice_mpart_pdf(Request $request)
    {
        $invoice_id = $request->input('invoice_id');
        $invoice_det = OutwardInvoice::whereRaw("invoice_id='$invoice_id'")->first();
        $center_id = $invoice_det->center_id;  
        $invoice_det->uc_total = $this->convertNumberToWordsForIndia($invoice_det->grand_total);
        $record_arr_det = OutwardInvoicePart::whereRaw("invoice_id='$invoice_id'")->get();
        /*$record_arr = array();
        foreach($record_arr_det as $record)
        {
            $record_arr[] = $record;
        }*/
        $center_details = ServiceCenter::whereRaw("center_id='$center_id'")->first();
        //$part_arr = ApproveRequestInventoryPart::whereRaw("approve_id='$ApproveId'")->get();
        
        $state_id = $center_details->state; 
        $state = StateMaster::whereRaw("state_id='$state_id'")->first();
        
        
        
        $data['record_arr'] = $invoice_det;
        $data['part_arr'] = $record_arr_det;
        $data['sc_details'] = $center_details;
        $data['state'] = $state;
        
        
            $pdf = PDF::loadView('invoice', $data); 
            //return view('pioneer');
            //return $pdf->download('invoice-'.$TagId.'.pdf'); exit;
        
            //return $pdf->download('challan.pdf');
            return view('invoice')
            ->with('sc_details',$center_details)
                   ->with('data',$data) ;
    }

}