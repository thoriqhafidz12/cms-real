<?php

namespace App\Models\Master\Coa;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['mskAkunKode', 'mskKode', 'mskNama'])]
#[Table('ms_kelompok')]
class MasterKelompok extends Model
{
    protected $primaryKey = 'mskId';
    public const CREATED_AT = 'mskCreatedAt';
    public const CREATED_BY = 'mskCreatedBy';
    public const UPDATED_AT = 'mskUpdatedAt';
    public const UPDATED_BY = 'mskUpdatedBy';
}