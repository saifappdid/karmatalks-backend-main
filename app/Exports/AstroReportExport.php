<?php
namespace App\Exports;

use App\Models\Astro;
use App\Models\InstantConsultationHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AstroReportExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public $selectedIds = [];
    public $modelData;
    public $start_date;
    public $end_date;
    public function __construct($selectedIds, $modelData, $start_date, $end_date)
    {
        $this->selectedIds = $selectedIds;
        $this->modelData   = $modelData;
        $this->start_date  = $start_date;
        $this->end_date    = $end_date;
    }

    public function collection()
    {
         $query = Astro::with([
            'instantConsultationHistories' => function ($q) {
                $q->whereBetween('created_at', ['2025-11-01', '2025-11-28']);
            },
            'bookings'                     => function ($q) {
                $q->whereBetween('session_date', ['2025-11-01', '2025-11-28']);
            },
        ]);
        
        if (! empty($this->selectedIds)) {
            $query->instantConsultationHistories->whereBetween('created_at', [$this->start_date, $this->end_date]);
        }
        $srNo = 1;
        return  $query->get()->map(function ($data) use ($srNo) {
            $chatAmount = $data?->instantConsultationHistories?->where('session_type', 'chat');
            $voiceAmount = $data?->instantConsultationHistories?->where('session_type', 'voice');
            $videoAmount = $data?->instantConsultationHistories?->where('session_type', 'video');
            $consultationAmount = $data?->bookings;
            return [
                'sr_no'                     => $srNo++,
                'astro_name'                => $data->first_name ?? $data->middle_name ?? $data->last_name ?? '-',
                'instant_chat_earning'      => $chatAmount->sum('amount') ?? 0,
                'instant_chat_duration'     => $chatAmount->sum('duration') >= 3600 ? $chatAmount->sum('duration') / 3600 : $chatAmount->sum('duration'),
                'instant_voicall_earning'   => $voiceAmount->sum('amount') ?? 0,
                'instant_voicall_duration'   => $voiceAmount->sum('duration') >= 3600 ? $voiceAmount->sum('duration') / 3600 : $voiceAmount->sum('duration'),
                'instant_videocall_earning' => $videoAmount->sum('amount') ?? 0,
                'instant_videocall_duration' => $videoAmount->sum('duration') >= 3600 ? $videoAmount->sum('duration') / 3600 : $videoAmount->sum('duration'),
                'consultation_earning'      => $consultationAmount->sum('final_total') ?? 0,
                'consultation_duration'      => $consultationAmount->sum('duration') >= 3600 ? $consultationAmount->sum('duration') / 3600 : $consultationAmount->sum('duration'),
                'from'                      => date("M jS, Y", strtotime($this->start_date)),
                'to'                        => date("M jS, Y", strtotime($this->end_date)),
            ];
        });

    }

    public function headings(): array
    {
        return [
            'Sr No',
            'Astro Name',
            'Instant Chat Earning',
            'Instant Chat Duration',
            'Instant Voicall Earning',
            'Instant Voicall Duration',
            'Instant Videocall Earning',
            'Instant Videocall Duration',
            'Consultation Earning',
            'Consultation Duration',
            'From',
            'To',
        ];
    }

}
