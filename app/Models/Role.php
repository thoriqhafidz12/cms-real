<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['rNama', 'rCreatedBy', 'rUpdatedBy'])]
#[Table('role')]
class Role extends Model
{
     /** @use HasFactory<RoleFactory> */
    use HasFactory;
    protected $table = 'role';
    protected $primaryKey = 'rId';

    public const CREATED_AT = 'rCreatedAt';
    public const CREATED_BY = 'rCreatedBy';
    public const UPDATED_AT = 'rUpdatedAt';
    public const UPDATED_BY = 'rUpdatedBy';

    /**
     * Relasi many-to-many ke menu via role_menu.
     */
    public function menus(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Menu::class,
            'role_menu',
            'rmRoleId',
            'rmMenuId',
            'rId',
            'mId'
        );
    }
}
