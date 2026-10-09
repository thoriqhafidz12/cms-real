<?php

namespace App\Models\Pinjaman;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['tppAngsuranId', 'tppPinjamanId', 'tppAnggotaId', 'tppAnggotaNama', 'tppKodePinjaman', 'tppMetodeBayarId', 'tppNoBukti', 'tppTanggalBayar', 'tppNominalBayar', 'tppBayarPokok', 'tppBayarBunga', 'tppBayarDenda', 'tppKeterangan', 'tppStatus', 'tppCreatedBy', 'tppUpdatedBy'])]
#[Table('tr_pembayaranpinjaman')]
class PembayaranPinjaman extends Model
{
    protected $primaryKey = 'tppId';
    public const CREATED_BY = 'tppCreatedBy';
    public const CREATED_AT = 'tppCreatedAt';
    public const UPDATED_BY = 'tppUpdatedBy';
    public const UPDATED_AT = 'tppUpdatedAt';
}
