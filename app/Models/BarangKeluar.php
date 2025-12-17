<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasAuditColumns;

class BarangKeluar extends Model
{
    use HasFactory;
    use HasAuditColumns;

    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'keterangan'
    ];

    public function details()
    {
        return $this->hasMany(BarangKeluarDetail::class);
    }

}
