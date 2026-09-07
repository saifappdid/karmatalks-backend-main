<?php

namespace App\Exports;

use App\Models\InstantConsultationHistory;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class InstantConsultationExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public $selectedIds = [];
    public $modelData;
    public function __construct($selectedIds, $modelData)
    {
        $this->selectedIds = $selectedIds;
        $this->modelData = $modelData;
    }
    public function collection()
    {
        $query = InstantConsultationHistory::with(['user:id,name', 'astro:id,first_name,middle_name,last_name']);
        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        }
        $srNo = 1;
        return $query->get()->map(function ($booking) use (&$srNo) {
            return [
                'sr_no' => $srNo++,
                'user_name' => $booking->user->name ?? '-',
                'astro_name' => trim(
                    ($booking->astro->first_name ?? '') . ' ' .
                        ($booking->astro->middle_name ?? '') . ' ' .
                        ($booking->astro->last_name ?? '')
                ) ?: '-',
                'amount' => $booking->amount,
                'session_type' => $booking->session_type ?? '-',
                'started_at' => date("M jS, Y h:i A", strtotime($booking->started_at)) ?? '-',
                'ended_at' => date("M jS, Y h:i A", strtotime($booking->ended_at)) ?? '-',
                'duration' => $booking->duration ?? '-',
                'session_rate' => $booking->session_rate ?? '-',
                'remark' => $booking->remark ?? '-',
                // 'created_at' => date("M jS, Y h:i A", strtotime($booking->created_at)),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Sr No',
            'User Name',
            'Astro Name',
            'Amount',
            'Session Type',
            'Started at',
            'Ended at',
            'Duration',
            'Session Rate',
            'Remark',
            // 'Created At',
        ];
    }
}
