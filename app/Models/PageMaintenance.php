<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageMaintenance extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't100_page_maintenance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'menu_id',
        'is_maintenance',
        'updated_by',
    ];

    /**
     * Get the menu associated with this maintenance record.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
}
