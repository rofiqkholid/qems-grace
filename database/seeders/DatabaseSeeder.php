<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed GenbaDept if empty
        if (\Illuminate\Support\Facades\DB::table('GenbaDept')->count() == 0) {
            \Illuminate\Support\Facades\DB::table('GenbaDept')->insert([
                ['Key1' => 'QA', 'Desc' => 'Quality Assurance', 'CheckBox01' => 1],
                ['Key1' => 'PE', 'Desc' => 'Production Engineering', 'CheckBox01' => 1],
                ['Key1' => 'PRD', 'Desc' => 'Production', 'CheckBox01' => 1],
                ['Key1' => 'MAINT', 'Desc' => 'Maintenance', 'CheckBox01' => 1],
                ['Key1' => 'HRD', 'Desc' => 'Human Resources', 'CheckBox01' => 1],
            ]);
        }

        // Seed Genba_Area if empty
        if (\Illuminate\Support\Facades\DB::table('Genba_Area')->count() == 0) {
            \Illuminate\Support\Facades\DB::table('Genba_Area')->insert([
                ['Area_name' => 'Assembly Section', 'Process' => 'Welding'],
                ['Area_name' => 'Press Section', 'Process' => 'Stamping'],
                ['Area_name' => 'Quality Section', 'Process' => 'Audit'],
            ]);
        }

        $this->call([
            GenbaCategorySeeder::class,
            GenbaStationMechSeeder::class,
            MenuSeeder::class,
            UserMenuPermissionSeeder::class,
        ]);
    }
}
