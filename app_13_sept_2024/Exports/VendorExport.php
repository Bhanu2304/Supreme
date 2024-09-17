<?php

namespace App\Exports;

use App\VendorMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Auth;
use Session;
class VendorExport implements FromCollection, WithHeadings,ShouldAutoSize,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $client_id = Session::get('Client_Id');
        $dataArr =  VendorMaster::selectRaw("Vendor_Name,Vendor_EmailId,Vendor_MobileNo,Vendor_Communication_Address1,Vendor_Communication_Address2,
Vendor_Communication_Address3,sm1.State_Name StateName1,Vendor_Communication_Pincode,Vendor_Permanent_Address1,
Vendor_Permanent_Address2,Vendor_Permanent_Address3,sm2.State_Name StateName2,Vendor_Permanent_Pincode,Vendor_PanNo,Vendor_GSTNo,Vendor_Key_Contact_Name,
Vendor_Key_Contact_EmailId,Vendor_Key_MobileNo,
if(Vendor_Status='1','Active','De-Active') Status")
                ->leftjoin('state_master as sm1','Vendor_Communication_State','=','sm1.State_Id')
                ->leftjoin('state_master as sm2','Vendor_Permanent_State','=','sm2.State_Id')
                ->where('Client_Id',$client_id)
                ->get();
        
        
        
       return $dataArr;
    }
    public function headings(): array
    {
        return [
             'Vendor Name','Email ID','Mobile No','Communication Address1','Communication Address2','Communication Address3','State','Pincode',
            'Permanent Address1','Permanent Address2','Permanent Address3','State','Pincode','PanNo','GST_No','Contact Name','Contact Mobile No',
            'Contact EmailId','Status'
            
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
