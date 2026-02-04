<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('app.users') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
<h3>{{ __('app.users') }}</h3>
<table>
    <thead>
    <tr>
        <th>{{ __('app.name') }}</th>
        <th>{{ __('app.email') }}</th>
        <th>{{ __('app.roles') }}</th>
        <th>{{ __('app.created_at') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ method_exists($user, 'getRoleNames') ? $user->getRoleNames()->implode(', ') : '' }}</td>
            <td>{{ optional($user->created_at)->toDateTimeString() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
