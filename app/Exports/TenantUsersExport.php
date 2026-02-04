<?php

namespace App\Exports;

use App\Models\TenantUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TenantUsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return TenantUser::with('roles')->get();
    }

    public function headings(): array
    {
        return [
            __('app.name'),
            __('app.email'),
            __('app.roles'),
            __('app.created_at'),
        ];
    }

    public function map($user): array
    {
        $roles = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->implode(', ')
            : '';

        return [
            $user->name,
            $user->email,
            $roles,
            optional($user->created_at)->toDateTimeString(),
        ];
    }
}
