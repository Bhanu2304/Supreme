<?php

namespace App\Imports;

use App\UploadCustomerDatabaseController;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;
use Session;
use App\ScenarioMaster;
use App\RequiredFieldMaster;
use App\CallMaster;
use App\UploadCustomerDataMaster;

class UploadCustomerDataMasterImport implements ToModel
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public  $clientId='',$user_id='',$Import_Project_Id='',$ImportId="",$ScenarioFetch="",$RequiredFetch="",$Import_Date='';
    
    public function __construct(String $Import_Project_Id,String $FileName)
    {
        $this->Import_Project_Id = $Import_Project_Id;
        $this->clientId = Session::get('Client_Id');
        $this->user_id = Auth::user()->id;
        $this->Import_Date = date("Y-m-d H:i:s");
        
        //For Scenario Project Id
            $ScenarioMaster = ScenarioMaster:: SelectRaw("MAX(Scenario_Level) Scenario")->whereRaw("Scenario_Project_Id = '$Import_Project_Id' ")->first();
            $ScenarioMasterFetch = json_decode($ScenarioMaster,true);
            $this->ScenarioFetch = ceil($ScenarioMasterFetch['Scenario']);

            $UploadCustomerDataMaster = new UploadCustomerDataMaster();
            $UploadCustomerDataMaster->Import_Client_Id=$this->clientId;
            $UploadCustomerDataMaster->Import_Project_Id=$Import_Project_Id;
            $UploadCustomerDataMaster->FileName=$FileName;
            $UploadCustomerDataMaster->created_by=$this->user_id;
            $UploadCustomerDataMaster->save();
            $this->ImportId = $UploadCustomerDataMaster->id;
            
            //For Required Download Master
            //$RequiredFieldMaster = RequiredFieldMaster:: SelectRaw("GROUP_CONCAT(Required_Field_Name ORDER BY Required_Call_Id) Required_Fields")->whereRaw("Scenario_Project_Id = '$Import_Project_Id'")->first();

            //For Required Import Master
            $RequiredFieldMaster = RequiredFieldMaster:: SelectRaw("GROUP_CONCAT(Required_Call_Id ORDER BY Required_Call_Id) Required_Fields")->whereRaw("Required_Field_Project_Id = '$Import_Project_Id'")->first();
            $RequiredFieldMasterFetch = json_decode($RequiredFieldMaster,true);
            $this->RequiredFetch = $RequiredFieldMasterFetch['Required_Fields'];
        
    }
    
    public function model(array $row)
    {
        $storeData = array();
        if(isset($row[1]) && !empty($row[1]))
        {
            
            $Import_Project_Id = $this->Import_Project_Id;
            $clientId = $this->clientId ;
            $user_id = $this->user_id;
            $ImportId = $this->ImportId;
            $RequiredFetch = explode(",",$this->RequiredFetch);
            $ScenarioFetch = $this->ScenarioFetch;


            $i=1;
            $storeData = array('Call_Client_Id'    => $clientId, 
                'Project_Id'        => $Import_Project_Id,
                'Import_Id'         => $ImportId,
                'Import_Date'         => $this->Import_Date,
                'MSISDN'         => isset($row[0])?$row[0]:'0');

            //print_r($row); exit;

            for(;$i<=$ScenarioFetch; $i++)
            {
                $storeData['Scenario'.$i] = isset($row[$i])?$row[$i]:'';
            }

            foreach($RequiredFetch as $require)
            {
                 $storeData['Field'.$require] = isset($row[$i])?$row[$i++]:'';
            }
            $storeData['Field'.$require] = $user_id;
            
            return new CallMaster($storeData);
        }
        //print_r($storeData); exit;
        
    }
}
