<?php

namespace App\Exports;

use App\ExecutiveMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Auth;
use Session;

class ExecutiveExport implements FromCollection, WithHeadings,ShouldAutoSize,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $client_id = Session::get('Client_Id');
        $dataArr =  ExecutiveMaster::selectRaw("Executive_Name UserName,Executive_MobileNo MobileNo,Executive_EmailId EmailId,Executive_Area_Of_Opps Area_Of_Opps,department_master.name Department,designation_master.name Designation,if(Executive_Status='1','Active','De-Active') Status")
                ->join('department_master','Executive_Department','=','department_master.id')
                ->join('designation_master','Executive_Designation','=','designation_master.id')
                ->where('Client_Id',$client_id)
                ->get();
        
        
        
       return $dataArr;
    }
    public function headings(): array
    {
        return [
             'User Name','Mobile No','Email ID','Area Of Opps','Department','Designation','Status'
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:G1'; // All headers
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
