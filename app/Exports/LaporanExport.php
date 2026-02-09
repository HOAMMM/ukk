<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithEvents
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Border, Alignment, NumberFormat};
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithEvents
{
    protected array $filters;
    protected int $totalRows = 0;
    protected float $grandTotal = 0; // Simpan total yang sudah dihitung

    /**
     * Constructor
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Ambil data laporan dengan filter
     */
    public function collection()
    {
        $query = Order::query()
            ->leftJoin('tb_meja', 'tb_meja.meja_id', '=', 'tb_order.order_meja')
            ->select(
                'tb_order.order_csname',
                'tb_meja.meja_nama',
                'tb_order.order_total',
                'tb_order.order_status',
                'tb_order.created_at'
            )
            ->where('tb_order.order_status', 'paid');

        // Terapkan filter yang sama dengan controller
        if (!empty($this->filters['tgl_mulai']) && !empty($this->filters['tgl_selesai'])) {
            $query->whereBetween('tb_order.created_at', [
                Carbon::parse($this->filters['tgl_mulai'])->startOfDay(),
                Carbon::parse($this->filters['tgl_selesai'])->endOfDay(),
            ]);
        } else {
            if (!empty($this->filters['bulan'])) {
                $query->whereMonth('tb_order.created_at', $this->filters['bulan']);
            }
            if (!empty($this->filters['tahun'])) {
                $query->whereYear('tb_order.created_at', $this->filters['tahun']);
            }
        }

        $orders = $query->orderBy('tb_order.created_at', 'desc')->get();

        // Hitung grand total
        $this->grandTotal = $orders->sum('order_total');

        $data = $orders->map(function ($order, $index) {
            return [
                $index + 1, // Nomor urut
                $order->order_csname,
                $order->meja_nama ?? 'Takeaway',
                $order->order_total,
                strtoupper($order->order_status),
                Carbon::parse($order->created_at)->format('d/m/Y H:i'),
            ];
        });

        $this->totalRows = $data->count();

        return $data;
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Customer',
            'Meja',
            'Total (Rp)',
            'Status',
            'Tanggal Transaksi',
        ];
    }

    /**
     * Styling untuk worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lebar kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 25,  // Nama Customer
            'C' => 15,  // Meja
            'D' => 18,  // Total
            'E' => 12,  // Status
            'F' => 20,  // Tanggal
        ];
    }

    /**
     * Nama sheet
     */
    public function title(): string
    {
        return 'Laporan Keuangan';
    }

    /**
     * Event untuk styling tambahan
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->totalRows + 1;

                // Style untuk semua data
                $sheet->getStyle('A2:F' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D0D0D0'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Center align untuk kolom No dan Status
                $sheet->getStyle('A2:A' . $lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E2:E' . $lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Format currency untuk kolom Total
                $sheet->getStyle('D2:D' . $lastRow)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // Tambahkan row total di bawah
                $totalRow = $lastRow + 1;
                $sheet->setCellValue('C' . $totalRow, 'TOTAL:');

                // PENTING: Gunakan nilai yang sudah dihitung, bukan formula
                $sheet->setCellValue('D' . $totalRow, $this->grandTotal);

                // Style untuk row total
                $sheet->getStyle('C' . $totalRow . ':D' . $totalRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E7E6E6'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->getStyle('D' . $totalRow)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // Freeze pane di header
                $sheet->freezePane('A2');

                // DIHAPUS: Auto-filter (yang bikin dropdown arrow)
                // $sheet->setAutoFilter('A1:F' . $lastRow);

                // Tambahkan info periode di atas tabel
                $this->addReportHeader($sheet);
            },
        ];
    }

    /**
     * Tambahkan header laporan
     */
    private function addReportHeader(Worksheet $sheet)
    {
        // Insert 3 rows di atas
        $sheet->insertNewRowBefore(1, 3);

        // Judul laporan
        $sheet->setCellValue('A1', 'LAPORAN KEUANGAN');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Info periode
        $periode = $this->getPeriodeText();
        $sheet->setCellValue('A2', 'Periode: ' . $periode);
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Tanggal export
        $sheet->setCellValue('A3', 'Dicetak pada: ' . now()->format('d/m/Y H:i:s'));
        $sheet->mergeCells('A3:F3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => [
                'size' => 9,
                'italic' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }

    /**
     * Generate text periode
     */
    private function getPeriodeText(): string
    {
        if (!empty($this->filters['tgl_mulai']) && !empty($this->filters['tgl_selesai'])) {
            return Carbon::parse($this->filters['tgl_mulai'])->format('d/m/Y') .
                ' - ' .
                Carbon::parse($this->filters['tgl_selesai'])->format('d/m/Y');
        }

        $parts = [];
        if (!empty($this->filters['bulan'])) {
            $parts[] = Carbon::create()->month($this->filters['bulan'])->translatedFormat('F');
        }
        if (!empty($this->filters['tahun'])) {
            $parts[] = $this->filters['tahun'];
        }

        return !empty($parts) ? implode(' ', $parts) : 'Semua Data';
    }
}
