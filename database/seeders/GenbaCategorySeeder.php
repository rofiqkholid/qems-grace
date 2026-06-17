<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenbaCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('GenbaCategory')->updateOrInsert(
            ['Category' => 'No Checksheet', 'Description' => 'Etc.'],
            ['Category' => 'No Checksheet', 'Description' => 'Etc.']
        );
        DB::table('GenbaCategory')->updateOrInsert(
            ['Category' => 'No Checksheet', 'Description' => 'Safety'],
            ['Category' => 'No Checksheet', 'Description' => 'Safety']
        );
    }
}
