<?php

namespace App\Exports;

use App\Models\TenantAttachment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TenantAttachmentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function collection()
    {
        return TenantAttachment::where('uploaded_by', $this->userId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            __('app.name'),
            __('app.type'),
            __('app.extension'),
            __('app.size_kb'),
            __('app.page_count'),
            __('app.created_at'),
        ];
    }

    public function map($attachment): array
    {
        $sizeKb = $attachment->size_bytes
            ? round($attachment->size_bytes / 1024, 1)
            : null;

        return [
            $attachment->original_name,
            $attachment->type,
            $attachment->extension,
            $sizeKb,
            $attachment->page_count,
            optional($attachment->created_at)->toDateTimeString(),
        ];
    }
}
