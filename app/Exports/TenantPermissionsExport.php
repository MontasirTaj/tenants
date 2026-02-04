<?php

namespace App\Exports;

use App\Models\TenantPermission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TenantPermissionsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return TenantPermission::all();
    }

    public function headings(): array
    {
        return [
            __('app.permission_name'),
            __('app.created_at'),
        ];
    }

    public function map($permission): array
    {
        return [
            $permission->name,
            optional($permission->created_at)->toDateTimeString(),
        ];
    }
}
