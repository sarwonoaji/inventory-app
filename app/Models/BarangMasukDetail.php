<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasAuditColumns;

class BarangMasukDetail extends Model
{
    use HasFactory;
    use HasAuditColumns;

    protected $fillable = [
        'barang_masuk_id',
        'barang_id',
        'qty'
    ];

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}

