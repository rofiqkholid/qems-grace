<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenbaStationMechSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['Area' => 'Assembly', 'DetailArea' => 'F-FILLER'],
            ['Area' => 'Assembly', 'DetailArea' => 'PSW-HPM'],
            ['Area' => 'Assembly', 'DetailArea' => 'PSW-MMKI'],
            ['Area' => 'Assembly', 'DetailArea' => 'PSW-SL'],
            ['Area' => 'Assembly', 'DetailArea' => 'PSW-Y4L'],
            ['Area' => 'Assembly', 'DetailArea' => 'PSW-YHA'],
            ['Area' => 'Assembly', 'DetailArea' => 'RBT-3K6'],
            ['Area' => 'Assembly', 'DetailArea' => 'RBT-3M0'],
            ['Area' => 'Assembly', 'DetailArea' => 'RBT-4L45'],
            ['Area' => 'Assembly', 'DetailArea' => 'RBT-5H45'],
            ['Area' => 'Assembly', 'DetailArea' => 'RBT-5J45'],
            ['Area' => 'Assembly', 'DetailArea' => 'RBT-CO'],
            ['Area' => 'Assembly', 'DetailArea' => 'RBT-D03B'],
            ['Area' => 'Assembly', 'DetailArea' => 'RBT-T86'],
            ['Area' => 'Assembly', 'DetailArea' => 'RBT-TG4'],
            ['Area' => 'Assembly', 'DetailArea' => 'RBT-Y4L'],
            ['Area' => 'Assembly', 'DetailArea' => 'RBT-YTB'],
            ['Area' => 'Assembly', 'DetailArea' => 'SSW-A'],
            ['Area' => 'Assembly', 'DetailArea' => 'SSW-B'],
            ['Area' => 'Assembly', 'DetailArea' => 'Standgun'],
            ['Area' => 'Assembly', 'DetailArea' => 'Muffler Assy'],
            ['Area' => 'Assembly', 'DetailArea' => 'Side Gate Top'],
            
            ['Area' => 'Big Press', 'DetailArea' => 'A1'],
            ['Area' => 'Big Press', 'DetailArea' => 'A2'],
            ['Area' => 'Big Press', 'DetailArea' => 'A3'],
            ['Area' => 'Big Press', 'DetailArea' => 'A4'],
            ['Area' => 'Big Press', 'DetailArea' => 'A5'],
            ['Area' => 'Big Press', 'DetailArea' => 'A6'],
            ['Area' => 'Big Press', 'DetailArea' => 'Repair Burry'],
            ['Area' => 'Big Press', 'DetailArea' => 'Metal Finish'],
            
            ['Area' => 'Medium Press', 'DetailArea' => 'B1'],
            ['Area' => 'Medium Press', 'DetailArea' => 'B2'],
            
            ['Area' => 'Small Press', 'DetailArea' => 'D14'],
            
            ['Area' => 'Warehouse', 'DetailArea' => 'PC Store Plant A'],
            ['Area' => 'Warehouse', 'DetailArea' => 'PC Store Plant B'],
            ['Area' => 'Warehouse', 'DetailArea' => 'PC Store Plant BC'],
            ['Area' => 'Warehouse', 'DetailArea' => 'PC Store Plant C'],
            ['Area' => 'Warehouse', 'DetailArea' => 'Incoming'],
            ['Area' => 'Warehouse', 'DetailArea' => 'WH Finish Good'],
            ['Area' => 'Warehouse', 'DetailArea' => 'Preparation Delivery'],
        ];

        \Illuminate\Support\Facades\DB::connection('sqlsrv')->table('GenbaStationMech')->insert($data);
    }
}
