<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('app.activity_log_title') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
<h3>{{ __('app.activity_log_title') }}</h3>
<table>
    <thead>
    <tr>
        <th>{{ __('app.activity_when') }}</th>
        <th>{{ __('app.activity_user') }}</th>
        <th>{{ __('app.activity_event') }}</th>
        <th>{{ __('app.activity_action') }}</th>
        <th>{{ __('app.activity_description') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($logs as $log)
        <tr>
            <td>{{ optional($log->created_at)->toDateTimeString() }}</td>
            <td>{{ optional($log->user)->name }}</td>
            <td>{{ $log->event }}</td>
            <td>{{ $log->action }}</td>
            <td>{{ $log->description }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
