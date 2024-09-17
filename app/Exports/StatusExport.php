<?php

namespace App\Exports;

use App\StatusMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Auth;
use Session;

class StatusExport implements FromCollection, WithHeadings,ShouldAutoSize,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $client_id = Session::get('Client_Id');
        $dataArr =  StatusMaster::selectRaw("cproj.Project_Name,Status_Scenario1_Name ,
            Status_Scenario2_Name Scenario2,
            Status_Scenario3_Name Scenario3,
            Status_Scenario4_Name Scenario4,
            Status_Scenario5_Name Scenario5,
            Status_Scenario6_Name Scenario6,
            Status_Name,Status_Auto_Closure,
if(Status_Status='1','Active','De-Active') Status")
                ->leftjoin('client_project as cproj','Status_Project_Id','=','cproj.Project_Id')
                ->where('Status_Client_Id',$client_id)
                ->get();
        
        
        
       return $dataArr;
    }
    public function headings(): array
    {
        return [
             'Project_Name','Scenario 1','Scenario 2','Scenario 3','Scenario 4','Scenario 5','Scenario 6','Ticket Status',
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
