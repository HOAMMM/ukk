<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanExport implements FromCollection, WithHeadings
{
    protected $tgl_mulai;
    protected $tgl_selesai;

    // Ubah constructor agar hanya menerima 2 argumen
    public function __construct($tgl_mulai, $tgl_selesai)
    {
        $this->tgl_mulai = $tgl_mulai;
        $this->tgl_selesai = $tgl_selesai;
    }

    public function collection()
    {
        $query = Order::query();

        if ($this->tgl_mulai && $this->tgl_selesai) {
            $query->whereBetween('created_at', [$this->tgl_mulai . " 00:00:00", $this->tgl_selesai . " 23:59:59"]);
        }

        return $query->where('order_status', 'paid')
            ->get(['order_csname', 'order_meja', 'order_total', 'created_at']);
    }

    public function headings(): array
    {
        return ["Nama Customer", "Meja", "Total Harga", "Status", "Tanggal"];
    }
}
