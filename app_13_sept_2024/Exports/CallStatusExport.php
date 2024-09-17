<?php

namespace App\Exports;

use App\CallStatusMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Auth;
use Session;

class CallStatusExport implements FromCollection, WithHeadings,ShouldAutoSize,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $client_id = Session::get('Client_Id');
        $dataArr =  CallStatusMaster::selectRaw("c_camp.campaign_ids,Call_Status_Scenario1_Name ,
            Call_Status_Scenario2_Name Scenario2,
            Call_Status_Scenario3_Name Scenario3,
            Call_Status_Scenario4_Name Scenario4,
            Call_Status_Scenario5_Name Scenario5,
            Call_Status_Scenario6_Name Scenario6,
            Call_Status_Name,Call_Status_Auto_Closure,
if(Call_Status_Status='1','Active','De-Active') Status")
                ->leftjoin('client_campaign as c_camp','Call_Status_Campaign_Id','=','c_camp.Campaign_Id')
                ->where('Call_Status_Client_Id',$client_id)
                ->get();
        
        
        
       return $dataArr;
    }
    public function headings(): array
    {
        return [
             'Campaign Name','Scenario 1','Scenario 2','Scenario 3','Scenario 4','Scenario 5','Scenario 6','Call Status',
            'Auto Closure','Status'
            
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:J1'; // All headers
                $styleArray = [
    'borders' => [
        'outline' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
            'color' => ['argb' => 'FFFF0000'],
        ],
    ],
];
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(11)->setBold(true);;
                //$event->sheet->getStyle($cellRange)->applyFromArray($styleArray);
                
            },
        ];
    }
}
