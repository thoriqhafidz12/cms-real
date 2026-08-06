<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['mNama', 'mRoute', 'mParentId', 'mIcon', 'mOrder', 'mIsActive', 'mCreatedBy', 'mUpdatedBy'])]
#[Table('menu')]
class Menu extends Model
{
    /** @use HasFactory<MenuFactory> */
    use HasFactory;

    protected $table = 'menu';
    protected $primaryKey = 'mId';

    public const CREATED_AT = 'mCreatedAt';
    public const CREATED_BY = 'mCreatedBy';
    public const UPDATED_AT = 'mUpdatedAt';
    public const UPDATED_BY = 'mUpdatedBy';

    /**
     * Relasi many-to-many ke role via role_menu.
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_menu',
            'rmMenuId',
            'rmRoleId',
            'mId',
            'rId'
        );
    }
}
