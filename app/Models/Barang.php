<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasAuditColumns;

class Barang extends Model
{
    use HasFactory;
    use HasAuditColumns;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan',
        'stok',
        'keterangan',
        'min_stok',
        'supplier_email'
    ];

    public function barangMasukDetails()
    {
        return $this->hasMany(BarangMasukDetail::class);
    }

    public function barangKeluarDetails()
    {
        return $this->hasMany(BarangKeluarDetail::class);
    }
}
