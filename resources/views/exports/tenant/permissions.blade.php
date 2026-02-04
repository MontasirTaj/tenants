<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('app.permissions') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
<h3>{{ __('app.permissions') }}</h3>
<table>
    <thead>
    <tr>
        <th>{{ __('app.permission_name') }}</th>
        <th>{{ __('app.created_at') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($permissions as $permission)
        <tr>
            <td>{{ $permission->name }}</td>
            <td>{{ optional($permission->created_at)->toDateTimeString() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
