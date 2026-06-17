<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserMenuPermission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't100_user_menus_permission';

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
        'id_user',
        'id_menus',
        'is_view',
        'is_delete',
    ];

    /**
     * Check if a user can view a specific menu ID
     *
     * @param int $menuId
     * @param int|null $userId
     * @return bool
     */
    public static function canView(int $menuId, ?int $userId = null): bool
    {
        $uid = $userId ?? (Auth::check() ? Auth::user()->id : null);
        if (!$uid) {
            return false;
        }

        return self::where('id_user', $uid)
            ->where('id_menus', $menuId)
            ->where('is_view', 1)
            ->exists();
    }

    /**
     * Check if a user can delete on a specific menu ID
     *
     * @param int $menuId
     * @param int|null $userId
     * @return bool
     */
    public static function canDelete(int $menuId, ?int $userId = null): bool
    {
        $uid = $userId ?? (Auth::check() ? Auth::user()->id : null);
        if (!$uid) {
            return false;
        }

        return self::where('id_user', $uid)
            ->where('id_menus', $menuId)
            ->where('is_delete', 1)
            ->exists();
    }
}
