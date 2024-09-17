<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PDF;
use App\TaggingMaster;
use App\InvoicePart;
use App\SparePart;
use App\ServiceCenter;
use App\StateMaster;
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


}