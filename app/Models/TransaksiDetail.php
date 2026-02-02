<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    use HasFactory;

    protected $table = 'tb_transaksi_detail';
    protected $primaryKey = 'trans_id';
    public $timestamps = false;

    protected $fillable = [
        'trans_name',
        'trans_qty',
        'trans_price',
        'trans_subtotal',
        'trans_code'
    ];

    // SETIAP detail MILIK 1 transaksi
    public function transaksi()
    {
        return $this->belongsTo(
            Transaksi::class,
            'trans_code',        // FK di tb_transaksi_detail
            'transaksi_code'     // UNIQUE key di tb_transaksi
        );
    }
}
