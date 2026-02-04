@extends('layout.master')

@section('content')
@php
  $sub = $subdomain ?? request()->route('subdomain');
@endphp

<div class="row tenant-page-header">
  <div class="col-xl-10 mx-auto">
    <div class="card tenant-page-header-card">
      <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
          <h2 class="tenant-page-header-title">{{ __('app.attachments') }}</h2>
          <p class="tenant-page-header-subtitle">{{ __('app.attachments_subtitle') }}</p>
        </div>
        <div class="tenant-page-header-actions mt-3 mt-md-0">
          <a href="{{ route('tenant.subdomain.dashboard', ['subdomain' => $sub]) }}" class="btn btn-outline-primary">
            <i class="mdi mdi-view-dashboard-outline"></i>
            <span>{{ __('app.tenant_panel') }}</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-11 col-lg-11 col-xl-10 mx-auto">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.attachments') }}</h4>
        <p class="text-muted mb-4">{{ __('app.attachments_guard_hint') }}</p>

        @if(session('status'))
          <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form id="attachment-upload-form" method="POST" action="{{ route('tenant.subdomain.attachments.store', ['subdomain' => $subdomain]) }}" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label for="attachment" class="form-label">{{ __('app.attachments') }}</label>
            <div class="custom-file-upload d-flex align-items-center border rounded bg-light p-2">
              <button type="button" class="btn btn-outline-primary btn-sm me-2" onclick="document.getElementById('attachment').click()">{{ __('app.browse') }}</button>
              <span id="attachment-label" class="text-muted small flex-grow-1">{{ __('app.no_file_selected') }}</span>
            </div>
            <input type="file" name="attachment" id="attachment" class="d-none @error('attachment') is-invalid @enderror" required>
            @error('attachment')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>
          <input type="hidden" name="page_count" id="page_count" value="{{ old('page_count') }}">
          <button type="submit" class="btn btn-primary tenant-action-btn">
            <i class="mdi mdi-upload"></i>
            <span>{{ __('app.send') }}</span>
          </button>
        </form>

        @if(isset($attachments) && $attachments->count())
          <hr>
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-4 mb-3">
            <h5 class="mb-0">{{ __('app.attachments_recent_title') }}</h5>
            <div class="mt-3 mt-md-0">
              <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('app.export') }}">
                <a href="{{ route('tenant.subdomain.attachments.export.excel', ['subdomain' => $subdomain]) }}" class="btn btn-outline-success">
                  <i class="mdi mdi-file-excel-outline"></i>
                  <span>{{ __('app.export_excel') }}</span>
                </a>
                <a href="{{ route('tenant.subdomain.attachments.export.pdf', ['subdomain' => $subdomain]) }}" class="btn btn-outline-danger">
                  <i class="mdi mdi-file-pdf-box"></i>
                  <span>{{ __('app.export_pdf') }}</span>
                </a>
              </div>
            </div>
          </div>
          <div class="table-responsive tenant-table-wrapper">
            <table class="table table-striped table-hover align-middle tenant-table" id="tenant-attachments-table">
              <thead>
                <tr>
                  <th>{{ __('app.name') }}</th>
                  <th>{{ __('app.type') }}</th>
                  <th>{{ __('app.extension') }}</th>
                  <th>{{ __('app.size_kb') }}</th>
                  <th>{{ __('app.page_count') }}</th>
                  <th>{{ __('app.created_at') }}</th>
                  <th>{{ __('app.actions') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($attachments as $att)
                  <tr>
                    <td>{{ $att->original_name }}</td>
                    <td>{{ $att->type === 'image' ? __('app.attachment_type_image') : __('app.attachment_type_file') }}</td>
                    <td>{{ $att->extension }}</td>
                    <td>{{ $att->size_bytes ? number_format($att->size_bytes / 1024, 1) : '-' }}</td>
                    <td>{{ $att->type === 'file' && $att->page_count ? $att->page_count : '-' }}</td>
                    <td>{{ $att->created_at?->toDateString() }}</td>
                    <td>
                      <a href="{{ route('tenant.subdomain.attachments.edit', ['subdomain' => $subdomain, 'attachment' => $att->id]) }}" class="btn btn-sm btn-outline-primary tenant-action-btn">
                        <i class="mdi mdi-pencil-outline"></i>
                        <span>{{ __('app.edit') }}</span>
                      </a>
                      <form method="POST" action="{{ route('tenant.subdomain.attachments.destroy', ['subdomain' => $subdomain, 'attachment' => $att->id]) }}" style="display:inline-block" onsubmit="return confirm('{{ __('app.confirm_delete_attachment') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger tenant-action-btn">
                          <i class="mdi mdi-delete-outline"></i>
                          <span>{{ __('app.delete') }}</span>
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('custom-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" integrity="sha512-D0QDt4xQMBmQAs3YOzYqSY+fhbQEiyXMSOC+FsuZyl6wAVoVU/SS5xInROMM59ByY4zJIcYWsUTQCMYXAs3kPg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
  if (window['pdfjsLib']) {
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
  }

  document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('attachment');
    const pageCountInput = document.getElementById('page_count');
    const labelSpan = document.getElementById('attachment-label');

    if (!fileInput || !pageCountInput) {
      return;
    }

    fileInput.addEventListener('change', function () {
      const file = this.files && this.files[0] ? this.files[0] : null;
      pageCountInput.value = '';

      if (labelSpan) {
        labelSpan.textContent = file ? file.name : '{{ __('app.no_file_selected') }}';
      }

      if (!file) return;

      // نحاول حساب عدد الصفحات لملفات PDF فقط
      if (!window['pdfjsLib'] || (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf'))) {
        return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        const typedArray = new Uint8Array(e.target.result);
        pdfjsLib.getDocument({ data: typedArray }).promise
          .then(function (pdf) {
            pageCountInput.value = pdf.numPages;
          })
          .catch(function () {
            // في حالة الفشل نترك الحقل فارغاً
            pageCountInput.value = '';
          });
      };
      reader.readAsArrayBuffer(file);
    });
  });
</script>
@endpush

@push('plugin-styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@push('plugin-scripts')
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('custom-scripts')
<script>
  $(function () {
    $('#tenant-attachments-table').DataTable({
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, '{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}']],
      ordering: true,
      language: {
        url: '{{ app()->getLocale() === 'ar' ? 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json' : 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/en-GB.json' }}'
      }
    });
  });
</script>
@endpush
