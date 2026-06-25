<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't100_menus';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sequence_id',
        'level_menu_id',
        'group_id',
        'sub_group_id',
        'menu',
        'menu_name',
        'icon',
    ];

    /**
     * Get menus ordered by sequence
     */
    public static function getOrderedMenus()
    {
        return self::orderBy('sequence_id', 'asc')->get();
    }

    /**
     * Define the hierarchical menu structure configuration
     */
    public static function getMenuStructureConfig()
    {
        return [
            'label' => 5,
            'mainMenus' => [
                [
                    'menu' => 100,
                    'children' => [
                        ['menu' => 101, 'children' => []],
                        ['menu' => 102, 'children' => []],
                        ['menu' => 106, 'children' => []],
                    ]
                ],
                [
                    'menu' => 85,
                    'children' => [
                        ['menu' => 86, 'children' => [87, 88]],
                        ['menu' => 89, 'children' => [90, 91]],
                        ['menu' => 92, 'children' => [93, 94]],
                    ]
                ],
                [
                    'menu' => 107,
                    'children' => [
                        ['menu' => 108, 'children' => []],
                        ['menu' => 110, 'children' => []],
                    ]
                ],
                [
                    'menu' => 95,
                    'children' => [
                        ['menu' => 96, 'children' => []],
                        ['menu' => 97, 'children' => []],
                        ['menu' => 98, 'children' => []],
                        ['menu' => 99, 'children' => []],
                        ['menu' => 109, 'children' => []],
                        ['menu' => 111, 'children' => []],
                    ]
                ],
                [
                    'menu' => 104,
                    'children' => [
                        ['menu' => 103, 'children' => []],
                        ['menu' => 105, 'children' => []],
                    ]
                ]
            ]
        ];
    }

    /**
     * Get all menu IDs in order of the hierarchical structure
     */
    public static function getOrderedIds()
    {
        $config = self::getMenuStructureConfig();
        $ids = [];
        foreach ($config['mainMenus'] as $main) {
            if ($main['menu']) {
                $ids[] = $main['menu'];
            }
            foreach ($main['children'] as $sub) {
                if ($sub['menu']) {
                    $ids[] = $sub['menu'];
                }
                foreach ($sub['children'] as $childId) {
                    $ids[] = $childId;
                }
            }
        }
        return $ids;
    }
}
