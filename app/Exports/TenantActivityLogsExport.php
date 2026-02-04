<?php

namespace App\Exports;

use App\Models\TenantActivityLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TenantActivityLogsExport implements FromCollection, WithHeadings, WithMapping
{
    protected ?int $userId;
    protected ?string $event;

    public function __construct(?int $userId = null, ?string $event = null)
    {
        $this->userId = $userId;
        $this->event = $event;
    }

    public function collection()
    {
        $query = TenantActivityLog::with('user')->orderByDesc('created_at');

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        if ($this->event) {
            $query->where('event', 'like', '%'.$this->event.'%');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            __('app.activity_when'),
            __('app.activity_user'),
            __('app.activity_event'),
            __('app.activity_action'),
            __('app.activity_description'),
        ];
    }

    public function map($log): array
    {
        return [
            optional($log->created_at)->toDateTimeString(),
            optional($log->user)->name,
            $log->event,
            $log->action,
            $log->description,
        ];
    }
}
