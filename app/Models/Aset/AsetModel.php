<?php

namespace App\Models\Aset;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['asUserId', 'asNamaBarang', 'asTglTerima', 'asTahun', 'asHarga', 'asMasaManfaat', 'asKeterangan', 'asCreatedBy', 'asCreatedAt', 'asUpdatedBy', 'asUpdatedAt'])]
#[Table('tr_aset')]
class AsetModel extends Model
{
    protected $table = 'tr_aset';
    protected $primaryKey = 'asId';
    public $timestamps = false;
}
