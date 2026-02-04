@extends('layout.master')

@section('content')
<div class="row">
  <div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.edit') }} - {{ __('app.attachments') }}</h4>
        <p class="text-muted mb-4">يمكنك تعديل اسم المرفق أو استبدال الملف نفسه. عدد الصفحات يُحتسب تلقائياً لملفات PDF.</p>

        @if($errors->any())
          <div class="alert alert-danger">
            {{ $errors->first() }}
          </div>
        @endif

        <form id="attachment-edit-form" method="POST" action="{{ route('tenant.subdomain.attachments.update', ['subdomain' => $subdomain, 'attachment' => $attachment->id]) }}" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label class="form-label">{{ __('app.name') }}</label>
            <input type="text" name="original_name" value="{{ old('original_name', $attachment->original_name) }}" class="form-control @error('original_name') is-invalid @enderror" required>
            @error('original_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('app.attachments') }} ({{ __('app.optional') ?? 'اختياري' }})</label>
            <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
            @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">اتركه فارغًا إذا كنت لا تريد تغيير الملف.</small>
          </div>

          <input type="hidden" name="page_count" id="page_count" value="{{ old('page_count', $attachment->page_count) }}">

          <button type="submit" class="btn btn-primary">{{ __('app.save_changes') }}</button>
          <a href="{{ route('tenant.subdomain.attachments.index', ['subdomain' => $subdomain]) }}" class="btn btn-secondary ms-2">{{ __('app.cancel') }}</a>
        </form>
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
    const fileInput = document.querySelector('input[name="attachment"]');
    const pageCountInput = document.getElementById('page_count');

    if (!fileInput || !pageCountInput || !window['pdfjsLib']) {
      return;
    }

    fileInput.addEventListener('change', function () {
      const file = this.files && this.files[0] ? this.files[0] : null;

      if (!file) return;

      // نعيد ضبط عدد الصفحات، وسيتم احتسابه تلقائياً لملفات PDF
      pageCountInput.value = '';

      if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
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
            pageCountInput.value = '';
          });
      };
      reader.readAsArrayBuffer(file);
    });
  });
</script>
@endpush
