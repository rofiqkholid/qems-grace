<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 5,
                'sequence_id' => 6,
                'level_menu_id' => 1,
                'group_id' => 5,
                'sub_group_id' => 5,
                'menu' => 'quality',
                'menu_name' => 'Quality',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M13.0021 10.9128V3.01281C13.0021 2.41281 13.5021 1.91281 14.1021 2.01281C16.1021 2.21281 17.9021 3.11284 19.3021 4.61284C20.7021 6.01284 21.6021 7.91285 21.9021 9.81285C22.0021 10.4129 21.5021 10.9128 20.9021 10.9128H13.0021Z" fill="black"/><path opacity="0.3" d="M11.0021 13.7128V4.91283C11.0021 4.31283 10.5021 3.81283 9.90208 3.91283C5.40208 4.51283 1.90209 8.41284 2.00209 13.1128C2.10209 18.0128 6.40208 22.0128 11.3021 21.9128C13.1021 21.8128 14.7021 21.3128 16.0021 20.4128C16.5021 20.1128 16.6021 19.3128 16.1021 18.9128L11.0021 13.7128Z" fill="black"/><path opacity="0.3" d="M21.9021 14.0128C21.7021 15.6128 21.1021 17.1128 20.1021 18.4128C19.7021 18.9128 19.0021 18.9128 18.6021 18.5128L13.0021 12.9128H20.9021C21.5021 12.9128 22.0021 13.4128 21.9021 14.0128Z" fill="black"/></svg>'
            ],
            [
                'id' => 85,
                'sequence_id' => 1,
                'level_menu_id' => 2,
                'group_id' => 5,
                'sub_group_id' => 85,
                'menu' => 'genba',
                'menu_name' => 'Genba Management',
                'icon' => '<span class="svg-icon svg-icon-primary menu-icon"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"viewBox="0 0 24 24" version="1.1"><defs /><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">  <polygon points="0 0 24 0 24 24 0 24" /><path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"fill="#000000" fill-rule="nonzero" opacity="0.3" /><path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero" /></g></svg><!--end::Svg Icon--></span>'
            ],
            [
                'id' => 86,
                'sequence_id' => 1,
                'level_menu_id' => 3,
                'group_id' => 5,
                'sub_group_id' => 85,
                'menu' => 'setup',
                'menu_name' => 'Setup',
                'icon' => '<span></span>'
            ],
            [
                'id' => 87,
                'sequence_id' => 1,
                'level_menu_id' => 4,
                'group_id' => 5,
                'sub_group_id' => 85,
                'menu' => 'team',
                'menu_name' => 'Team',
                'icon' => '<span></span>'
            ],
            [
                'id' => 88,
                'sequence_id' => 2,
                'level_menu_id' => 4,
                'group_id' => 5,
                'sub_group_id' => 85,
                'menu' => 'team_member',
                'menu_name' => 'Member',
                'icon' => '<span></span>'
            ],
            [
                'id' => 89,
                'sequence_id' => 2,
                'level_menu_id' => 3,
                'group_id' => 5,
                'sub_group_id' => 86,
                'menu' => 'activity',
                'menu_name' => 'Activity',
                'icon' => '<span></span>'
            ],
            [
                'id' => 90,
                'sequence_id' => 1,
                'level_menu_id' => 4,
                'group_id' => 5,
                'sub_group_id' => 86,
                'menu' => 'genba_management',
                'menu_name' => 'Genba Form',
                'icon' => '<span></span>'
            ],
            [
                'id' => 91,
                'sequence_id' => 2,
                'level_menu_id' => 4,
                'group_id' => 5,
                'sub_group_id' => 86,
                'menu' => 'genba_mng_management',
                'menu_name' => 'Findings Genba',
                'icon' => '<span></span>'
            ],
            [
                'id' => 92,
                'sequence_id' => 3,
                'level_menu_id' => 3,
                'group_id' => 5,
                'sub_group_id' => 87,
                'menu' => 'verification',
                'menu_name' => 'Summary',
                'icon' => '<span></span>'
            ],
            [
                'id' => 93,
                'sequence_id' => 1,
                'level_menu_id' => 4,
                'group_id' => 5,
                'sub_group_id' => 87,
                'menu' => 'spv_verification',
                'menu_name' => 'Findings Result',
                'icon' => '<span></span>'
            ],
            [
                'id' => 94,
                'sequence_id' => 2,
                'level_menu_id' => 4,
                'group_id' => 5,
                'sub_group_id' => 87,
                'menu' => 'verifikasi_genba',
                'menu_name' => 'Verifikasi Genba',
                'icon' => '<span></span>'
            ],
            [
                'id' => 95,
                'sequence_id' => 1,
                'level_menu_id' => 2,
                'group_id' => 6,
                'sub_group_id' => 95,
                'menu' => 'data-master',
                'menu_name' => 'Data Master',
                'icon' => '<span></span>'
            ],
            [
                'id' => 96,
                'sequence_id' => 1,
                'level_menu_id' => 3,
                'group_id' => 6,
                'sub_group_id' => 95,
                'menu' => 'data-master/line-checked',
                'menu_name' => 'Line Checked',
                'icon' => '<span></span>'
            ],
            [
                'id' => 97,
                'sequence_id' => 2,
                'level_menu_id' => 3,
                'group_id' => 6,
                'sub_group_id' => 95,
                'menu' => 'data-master/category',
                'menu_name' => 'Category',
                'icon' => '<span></span>'
            ],
            [
                'id' => 98,
                'sequence_id' => 3,
                'level_menu_id' => 3,
                'group_id' => 6,
                'sub_group_id' => 95,
                'menu' => 'data-master/department',
                'menu_name' => 'Departement',
                'icon' => '<span></span>'
            ],
            [
                'id' => 99,
                'sequence_id' => 4,
                'level_menu_id' => 3,
                'group_id' => 6,
                'sub_group_id' => 95,
                'menu' => 'data-master/check-item',
                'menu_name' => 'Check Item',
                'icon' => '<span></span>'
            ],
            [
                'id' => 100,
                'sequence_id' => 1,
                'level_menu_id' => 2,
                'group_id' => 7,
                'sub_group_id' => 100,
                'menu' => 'dashboard',
                'menu_name' => 'Dashboard',
                'icon' => '<span></span>'
            ],
            [
                'id' => 101,
                'sequence_id' => 1,
                'level_menu_id' => 3,
                'group_id' => 7,
                'sub_group_id' => 100,
                'menu' => 'dashboard-mng',
                'menu_name' => 'Genba Management',
                'icon' => '<span></span>'
            ],
            [
                'id' => 102,
                'sequence_id' => 2,
                'level_menu_id' => 3,
                'group_id' => 7,
                'sub_group_id' => 100,
                'menu' => 'dashboard-biq',
                'menu_name' => 'Genba BIQ',
                'icon' => '<span></span>'
            ],
            [
                'id' => 106,
                'sequence_id' => 3,
                'level_menu_id' => 3,
                'group_id' => 7,
                'sub_group_id' => 100,
                'menu' => 'dashboard-safety',
                'menu_name' => 'Genba Safety',
                'icon' => '<span></span>'
            ],
            [
                'id' => 104,
                'sequence_id' => 2,
                'level_menu_id' => 2,
                'group_id' => 8,
                'sub_group_id' => 104,
                'menu' => 'setting',
                'menu_name' => 'Setting',
                'icon' => '<span></span>'
            ],
            [
                'id' => 103,
                'sequence_id' => 1,
                'level_menu_id' => 3,
                'group_id' => 8,
                'sub_group_id' => 104,
                'menu' => 'user-management',
                'menu_name' => 'User Permission',
                'icon' => '<span></span>'
            ],
            [
                'id' => 105,
                'sequence_id' => 2,
                'level_menu_id' => 3,
                'group_id' => 8,
                'sub_group_id' => 104,
                'menu' => 'user-setting',
                'menu_name' => 'User Setting',
                'icon' => '<span></span>'
            ],
            [
                'id' => 107,
                'sequence_id' => 2,
                'level_menu_id' => 2,
                'group_id' => 9,
                'sub_group_id' => 107,
                'menu' => 'genba-internal',
                'menu_name' => 'Genba Internal',
                'icon' => '<span></span>'
            ],
            [
                'id' => 108,
                'sequence_id' => 1,
                'level_menu_id' => 3,
                'group_id' => 9,
                'sub_group_id' => 107,
                'menu' => 'genba-internal/internal-audit',
                'menu_name' => 'Internal Audit',
                'icon' => '<span></span>'
            ]
        ];

        foreach ($data as $item) {
            DB::table('t100_menus')->updateOrInsert(['id' => $item['id']], $item);
        }
    }
}
