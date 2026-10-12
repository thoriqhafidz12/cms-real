<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nrKode', 'nrNama', 'nrPosisi', 'nrJenis', 'nrKodeAkun', 'nrSaldoNormal', 'nrUrut', 'nrCreatedBy', 'nrUpdatedBy', 'nrDeletedBy'])]
#[Table('ms_neraca')]

class Neraca extends Model
{
    protected $primaryKey = 'nrId';
    public const CREATED_AT = 'nrCreatedAt';
    public const CREATED_BY = 'nrCreatedBy';
    public const UPDATED_AT = 'nrUpdatedAt';
    public const UPDATED_BY = 'nrUpdatedBy';
    public const DELETED_AT = 'nrDeletedAt';
    public const DELETED_BY = 'nrDeletedBy';
}
