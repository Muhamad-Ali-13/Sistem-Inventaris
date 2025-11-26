<?php

namespace App\Exports;

use App\Models\VehicleUsage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VehicleUsageExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        return VehicleUsage::with(['vehicle', 'user', 'approver'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Vehicle',
            'License Plate',
            'User',
            'Start Date',
            'End Date',
            'Purpose',
            'Status',
            'Approved By',
            'Request Date'
        ];
    }

    public function map($usage): array
    {
        return [
            $usage->id,
            $usage->vehicle->name,
            $usage->vehicle->license_plate,
            $usage->user->name,
            $usage->start_date->format('d/m/Y'),
            $usage->end_date->format('d/m/Y'),
            $usage->purpose,
            strtoupper($usage->status),
            $usage->approver ? $usage->approver->name : '-',
            $usage->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}