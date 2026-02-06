<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithColumnFormatting
};
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanExport implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithColumnFormatting
{
    /**
     * @var string|null
     */
    protected ?string $tanggalMulai;

    /**
     * @var string|null
     */
    protected ?string $tanggalSelesai;

    /**
     * Constructor
     *
     * @param string|null $tanggalMulai
     * @param string|null $tanggalSelesai
     */
    public function __construct(?string $tanggalMulai, ?string $tanggalSelesai)
    {
        $this->tanggalMulai   = $tanggalMulai;
        $this->tanggalSelesai = $tanggalSelesai;
    }

    /**
     * Ambil data laporan
     */
    public function collection()
    {
        $query = Order::query();

        if ($this->tanggalMulai && $this->tanggalSelesai) {
            $query->whereBetween('created_at', [
                $this->tanggalMulai . ' 00:00:00',
                $this->tanggalSelesai . ' 23:59:59',
            ]);
        }

        return $query->orderBy('created_at', 'asc')
            ->get()
            ->map(function (Order $order) {
                return [
                    $order->order_csname,
                    $order->order_meja ?? '-',
                    $order->order_total,
                    strtoupper($order->order_status), // ENUM langsung dari DB
                    Date::dateTimeToExcel(Carbon::parse($order->created_at)),
                ];
            });
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'Nama Customer',
            'Meja',
            'Total Harga',
            'Status',
            'Tanggal Transaksi',
        ];
    }

    /**
     * Format kolom Excel
     */
    public function columnFormats(): array
    {
        return [
            'C' => '"Rp" #,##0',                         // Total Harga
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,  // Tanggal
        ];
    }
}
