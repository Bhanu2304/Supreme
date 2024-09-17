<?php

namespace App\Imports;

use App\ScenarioMasterTmp;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;

class ScenarioImport implements ToModel
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function model(array $row)
    {
        return new ScenarioMasterTmp([
            'Scenario1'     => $row[0],
            'Scenario2'    => $row[1], 
            'Scenario3' => $row[2],
            'Scenario4' => $row[3],
            'Scenario5' => $row[4],
            'Scenario6' => $row[5]
        ]);
    }
}
