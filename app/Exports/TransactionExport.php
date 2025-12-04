<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Transaksi::with(['item', 'user'])
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Transaction Code',
            'Item Name',
            'Item Code',
            'User',
            'Quantity',
            'Type',
            'Notes',
            'Transaction Date'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->code,
            $transaction->item->name,
            $transaction->item->code,
            $transaction->user->name,
            $transaction->quantity,
            strtoupper($transaction->type),
            $transaction->notes,
            $transaction->transaction_date->format('d/m/Y')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}