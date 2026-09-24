<?php

namespace App\Models\Pinjaman;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tpId', 'tpKode', 'tpAnggotaId', 'tpAnggotaNama', 'tpTanggalPinjam', 'tpJaminanId', 'tpJJaminanNama','tpTujuanId','tpTujuanNama', 'tpJumlahPinjam', 'tpJumlahAngsuran', 'tpJumlahAngsuranBulan', 'tpBunga', 'tpTotalPinjam', 'tpStatus', 'tpKeterangan', 'tpCreatedBy', 'tpUpdatedBy', 'tpDeletedBy'])]
#[Table('tr_pengajuan')]
class PengajuanPinjaman extends Model
{
    protected $primaryKey = 'tpId';
    public const CREATED_AT = 'tpCreatedAt';
    public const UPDATED_AT = 'tpUpdatedAt';
    public const DELETED_AT = 'tpDeletedAt';
    public const CREATED_BY = 'tpCreatedBy';
    public const UPDATED_BY = 'tpUpdatedBy';
    public const DELETED_BY = 'tpDeletedBy';
}
