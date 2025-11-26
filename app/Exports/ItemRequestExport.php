<?php

namespace App\Exports;

use App\Models\ItemRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemRequestsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        return ItemRequest::with(['item', 'user', 'approver'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Request ID',
            'Item Name',
            'User',
            'Quantity',
            'Purpose',
            'Status',
            'Approved By',
            'Rejection Reason',
            'Request Date'
        ];
    }

    public function map($request): array
    {
        return [
            $request->id,
            $request->item->name,
            $request->user->name,
            $request->quantity,
            $request->purpose,
            strtoupper($request->status),
            $request->approver ? $request->approver->name : '-',
            $request->rejection_reason ?: '-',
            $request->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}