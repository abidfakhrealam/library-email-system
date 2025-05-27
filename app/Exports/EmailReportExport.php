<?php

namespace App\Exports;

use App\Models\AssignedEmail;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmailReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private Carbon $startDate,
        private Carbon $endDate
    ) {}

    public function collection()
    {
        return AssignedEmail::with(['assignee', 'tags'])
            ->whereBetween('received_at', [$this->startDate, $this->endDate])
            ->orderBy('received_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Subject',
            'From',
            'Received At',
            'Status',
            'Assigned To',
            'Tags',
            'Response Time (hours)'
        ];
    }

    public function map($email): array
    {
        return [
            $email->id,
            $email->subject,
            "{$email->from_name} <{$email->from_email}>",
            $email->received_at->format('Y-m-d H:i:s'),
            ucfirst(str_replace('_', ' ', $email->status)),
            $email->assignee->name ?? 'Unassigned',
            $email->tags->pluck('name')->join(', '),
            $email->status === 'completed' 
                ? round($email->received_at->diffInHours($email->updated_at), 2)
                : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:H' => ['alignment' => ['wrapText' => true]],
        ];
    }
}
