<?php

namespace App\Models\Master\Coa;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['msaKode', 'msaNama', 'msaCreatedBy', 'msaCreatedBy'])]
#[Table('ms_akun')]
class MasterAkun extends Model
{
    protected $primaryKey = 'msaId';
    public const CREATED_AT = 'msaCreatedAt';
    public const CREATED_BY = 'msaCreatedBy';
    public const UPDATED_AT = 'msaUpdatedAt';
    public const UPDATED_BY = 'msaCreatedBy';
}
