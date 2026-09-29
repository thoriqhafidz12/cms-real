<?php

namespace App\Models\Pinjaman;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['tjaPinjamanId', 'tjaTanggalAngsuran', 'tjaCicilanKe', 'tjaNominalAngsuran', 'tjaNominalBunga', 'tjaTotalTagihan', 'tjaSisaTagihan', 'tjaStatus'])]
#[Table('tr_jadwalangsuran')]

class JadwalAngsuran extends Model
{
    protected $primaryKey = 'tjaId';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
}
