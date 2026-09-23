<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['mjPinjamanKode', 'mjPinjamanNama', 'mjSukuBunga', 'mjTipeBunga', 'mjPlafonMaksimal', 'mjTenorMaksimal', 'mjBiayaAdmin', 'mjBiayaProvisi', 'mjDendaKeterlambatan', 'mjAkunPiutang', 'mjAkunBunga', 'mjAkunAdmin', 'mjAkunDenda', 'mjKeterangan', 'mjStatus', 'mjCreateUser', 'mjUpdateUser', 'mjDeleteUser'])]
#[Table('ms_jnspinjaman')]
class JenisPinjaman extends Model
{
    protected $primaryKey = 'mjPinjamanId';
    public const CREATED_AT = 'mjCreateTime';
    public const UPDATED_AT = 'mjUpdateTime';
    public const DELETED_AT = 'mjDeleteTime';
    public const CREATED_BY = 'mjCreateBy';
    public const UPDATED_BY = 'mjUpdateBy';
    public const DELETED_BY = 'mjDeleteBy';

}
