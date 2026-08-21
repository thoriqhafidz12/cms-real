<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['maKode', 'maNama', 'maAlamat', 'maNoTelp', 'maNoIdentitas', 'maTempatLahir', 'maTglLahir', 'maJnsKelamin', 'maPekerjaan', 'maTglGabung', 'maStatusPernikahan', 'maNamaIbuKandung', 'maStatus'])]
#[Table('ms_anggota')]
class MasterAnggota extends Model
{
    protected $primaryKey = 'maId';
    public const CREATED_AT = 'maCreatedAt';
    public const CREATED_BY = 'maCreatedBy';
    public const UPDATED_AT = 'maUpdatedAt';
    public const UPDATED_BY = 'maUpdatedBy';
}
