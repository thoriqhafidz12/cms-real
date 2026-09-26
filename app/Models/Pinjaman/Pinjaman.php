<?php

namespace App\Models\Pinjaman;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['trpjId', 'trpPengajuanId', 'trpAnggotaId', 'trpAnggotaNama', 'trpMetodBayarId', 'trpMetodBayarNama', 'trpNoPinjaman', 'trpTanggalCair', 'trpNominalPinjaman', 'trpTenor', 'trpBunga', 'trpBiayaAdmin', 'trpCicilanPokok', 'trpCicilanBunga', 'trpTotalCicilan', 'trpKeterangan', 'trpStatusPinjaman', 'trpCreatedUser', 'trpUpdatedUser'])]
#[Table('tr_pinjaman')]

class Pinjaman extends Model
{
    protected $primaryKey = 'trpjId';
    public const CREATED_BY = 'trpCreatedUser';
    public const CREATED_AT = 'trpCreatedAt';
    public const UPDATED_BY = 'trpUpdatedUser';
    public const UPDATED_AT = 'trpUpdatedAt';
}
