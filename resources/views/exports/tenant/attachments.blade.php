<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('app.attachments') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
<h3>{{ __('app.attachments') }}</h3>
<table>
    <thead>
    <tr>
        <th>{{ __('app.name') }}</th>
        <th>{{ __('app.type') }}</th>
        <th>{{ __('app.extension') }}</th>
        <th>{{ __('app.size_kb') }}</th>
        <th>{{ __('app.page_count') }}</th>
        <th>{{ __('app.created_at') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($attachments as $att)
        <tr>
            <td>{{ $att->original_name }}</td>
            <td>{{ $att->type }}</td>
            <td>{{ $att->extension }}</td>
            <td>{{ $att->size_bytes ? number_format($att->size_bytes / 1024, 1) : '-' }}</td>
            <td>{{ $att->page_count }}</td>
            <td>{{ optional($att->created_at)->toDateTimeString() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
