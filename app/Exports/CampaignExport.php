<?php

namespace App\Exports;

use App\CampaignMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Auth;
use Session;

class CampaignExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $mode='',$campaign_id='';
    
    public function __construct ($mode,$campaign_id)
    {
         $this->mode=$mode; 
        $this->campaign_id=$campaign_id;
    }
    
    public function collection()
    {
        $client_id = Session::get('Client_Id');
        $dataArr = array();
       // echo $this->mode; exit;
        
        if($this->mode=='single')
        {
            $selectRaw =  CampaignMaster::selectRaw("campaign_ids,import_fields")
                ->where('Campaign_Id',$this->campaign_id)
                ->first();
            $fetch_records = json_decode($selectRaw,true);
            
            //$dataArr[0] = $fetch_records['campaign_ids'];
            $dataArr[0] = array('MSISDN')+explode(",",$fetch_records['import_fields']);
        }
        else
        {
            $selectRaw =  CampaignMaster::selectRaw("campaign_ids,import_fields")
                ->where('client_id',$client_id)
                ->get();
            $fetch_records = json_decode($selectRaw,true);
            $i=0;
            foreach($fetch_records as $records)
            {
                //$dataArr[$i] = $fetch_records['campaign_ids'];
                $dataArr[$i++] = array('MSISDN')+explode(",",$records['import_fields']);
            }
        }
        
        //print_r($dataArr); exit;
        //$ExportRaw =  new CampaignMaster();
        //$ExportRaw->dataArr = $dataArr;
        
       return collect($dataArr);
    }
    
    
}
