<?php

namespace App\Models\Master\Coa;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['msjAkunKode', 'msjKelompokKode', 'msjKode', 'msjNama', 'msjCreatedBy', 'msjUpdatedBy', 'msjDeletedBy'])]
#[Table('ms_jenis')]

class MasterJenis extends Model
{
    protected $primaryKey = 'msjId';
    public const CREATED_AT = 'msjCreatedAt';
    public const CREATED_BY = 'msjCreatedBy';
    public const UPDATED_AT = 'msjUpdatedAt';
    public const UPDATED_BY = 'msjUpdatedBy';
    public const DELETED_AT = 'msjDeletedAt';
    public const DELETED_BY = 'msjDeletedBy';
}
