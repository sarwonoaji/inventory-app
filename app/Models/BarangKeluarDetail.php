<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasAuditColumns;

class BarangKeluarDetail extends Model
{
    use HasFactory;
    use HasAuditColumns;

    protected $fillable = [
        'barang_keluar_id',
        'barang_id',
        'qty'
    ];

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
