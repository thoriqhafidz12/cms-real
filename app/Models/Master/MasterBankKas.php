<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['msbkKode', 'msbkObjekKd', 'msbkObjekNm', 'msbkNoRek', 'msbkAtasNama', 'msbkSaldoSekarang', 'msbkStatus', 'msbkTanggal', 'msbkSaldoAwal', 'msbkCreatedBy', 'msbkUpdatedBy', 'msbkDeletedBy'])]
#[Table('ms_bankkas')]

class MasterBankKas extends Model
{
    protected $primaryKey = 'msbkId';
    public const CREATED_AT = 'msbkCreatedTime';
    public const CREATED_BY = 'msbkCreatedBy';
    public const UPDATED_AT = 'msbkUpdatedTime';
    public const UPDATED_BY = 'msbkUpdatedBy';
    public const DELETED_AT = 'msbkDeletedTime';
    public const DELETED_BY = 'msbkDeletedBy';
}
