<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['lrKode', 'lrNama', 'lrPosisi', 'lrJenis', 'lrKodeAkun', 'lrSaldoNormal', 'lrUrut', 'lrCreatedBy', 'lrUpdatedBy', 'lrDeletedBy'])]
#[Table('ms_labarugi')]

class LabaRugi extends Model
{
    protected $primaryKey = 'lrId';
    public const CREATED_AT = 'lrCreatedAt';
    public const CREATED_BY = 'lrCreatedBy';
    public const UPDATED_AT = 'lrUpdatedAt';
    public const UPDATED_BY = 'lrUpdatedBy';
    public const DELETED_AT = 'lrDeletedAt';
    public const DELETED_BY = 'lrDeletedBy';
}
