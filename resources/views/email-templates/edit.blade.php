@extends('layouts.app')

@section('title', 'Edit Email Template - ' . $workshop->name)

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Edit {{ $templateType }} Template</h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('workshops.index') }}" class="text-muted text-hover-primary">Workshops</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('workshops.show', $workshop) }}" class="text-muted text-hover-primary">{{ $workshop->name }}</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('email-templates.index', $workshop) }}" class="text-muted text-hover-primary">Email Templates</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Edit</li>
            </ul>
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <form method="POST" action="{{ route('email-templates.update', [$workshop, $emailTemplate]) }}" class="form d-flex flex-column flex-lg-row">
            @csrf
            @method('PUT')
            
            <!--begin::Aside column-->
            <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                <!--begin::Template info-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Template Info</h2>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-5">
                            <div class="fv-row">
                                <label class="form-label">Template Type</label>
                                <input type="text" class="form-control form-control-solid" value="{{ $templateType }}" readonly />
                            </div>
                            <div class="fv-row">
                                <label class="form-label">Workshop</label>
                                <input type="text" class="form-control form-control-solid" value="{{ $workshop->name }}" readonly />
                            </div>
                            <div class="fv-row">
                                <label class="form-label">Created</label>
                                <input type="text" class="form-control form-control-solid" value="{{ $emailTemplate->created_at->format('M j, Y g:i A') }}" readonly />
                            </div>
                            <div class="fv-row">
                                <label class="form-label">Last Updated</label>
                                <input type="text" class="form-control form-control-solid" value="{{ $emailTemplate->updated_at->format('M j, Y g:i A') }}" readonly />
                            </div>
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Template info-->

                <!--begin::Template variables-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Available Variables</h2>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-5">
                            <i class="ki-duotone ki-information fs-2tx text-primary me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <div class="fs-7 text-gray-700">Click on any variable to insert it into your template</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex flex-column gap-2">
                            @foreach($availableVariables as $variable => $description)
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-sm btn-light-primary variable-btn me-2" data-variable="{{ $variable }}">
                                    <i class="ki-duotone ki-plus fs-4"></i>
                                </button>
                                <div class="d-flex flex-column">
                                    <code class="fs-7">{{ "{{ " . $variable . " }}" }}</code>
                                    <span class="text-muted fs-8">{{ $description }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Template variables-->

                <!--begin::Quick Actions-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Quick Actions</h2>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-3">
                            <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_preview_template">
                                <i class="ki-duotone ki-eye fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>Preview Template
                            </button>
                            <button type="button" class="btn btn-light-info btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_test_send">
                                <i class="ki-duotone ki-send fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>Send Test Email
                            </button>
                            <button type="button" class="btn btn-light-warning btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_duplicate_template">
                                <i class="ki-duotone ki-copy fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>Duplicate Template
                            </button>
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Quick Actions-->
            </div>
            <!--end::Aside column-->

            <!--begin::Main column-->
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                <!--begin::General options-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Template Content</h2>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Email Subject</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" name="subject" id="email_subject" class="form-control mb-2 @error('subject') is-invalid @enderror" placeholder="Enter email subject" value="{{ old('subject', $emailTemplate->subject) }}" />
                            <!--end::Input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">The subject line that recipients will see in their inbox</div>
                            <!--end::Description-->
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Email Content</label>
                            <!--end::Label-->
                            <!--begin::Editor-->
                            <div class="card card-flush">
                                <div class="card-header">
                                    <div class="card-title">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-code fs-2 text-primary me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                            <span class="fs-6 fw-bold">Rich Text Editor</span>
                                        </div>
                                    </div>
                                    <div class="card-toolbar">
                                        <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_template_variables">
                                            <i class="ki-duotone ki-information fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>Variables
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="kt_docs_tinymce_basic" class="min-h-300px mb-2">
                                        {!! old('content', $emailTemplate->content) !!}
                                    </div>
                                    <textarea name="content" id="email_content" class="d-none @error('content') is-invalid @enderror">{{ old('content', $emailTemplate->content) }}</textarea>
                                </div>
                            </div>
                            <!--end::Editor-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7 mt-2">The main content of your email. You can use HTML formatting and template variables.</div>
                            <!--end::Description-->
                            @error('content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::General options-->

                <!--begin::Actions-->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('email-templates.show', [$workshop, $emailTemplate]) }}" class="btn btn-light me-5">Cancel</a>
                    <button type="button" id="preview_btn" class="btn btn-light-primary me-5">Preview</button>
                    <button type="submit" id="kt_template_submit" class="btn btn-primary">
                        <span class="indicator-label">Update Template</span>
                        <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Main column-->
        </form>
        
    </div>
</div>
<!--end::Content-->

<!-- Template Variables Modal -->
<div class="modal fade" id="kt_modal_template_variables" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Available Template Variables</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-5">
                    <i class="ki-duotone ki-information fs-2tx text-primary me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <div class="fs-6 text-gray-700">Use variables in your templates by wrapping them in double curly braces, e.g., <code>{{ "{{ name }}" }}</code></div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 gy-7">
                        <thead>
                            <tr class="fw-bold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                <th>Variable</th>
                                <th>Description</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availableVariables as $variable => $description)
                            <tr>
                                <td><code>{{ "{{ " . $variable . " }}" }}</code></td>
                                <td>{{ $description }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-light-primary variable-insert-btn" data-variable="{{ $variable }}">
                                        <i class="ki-duotone ki-plus fs-4"></i>Insert
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="kt_modal_preview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Template Preview</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <div id="preview_content">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Template Modal -->
<div class="modal fade" id="kt_modal_preview_template" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Current Template Preview</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <div id="current_preview_content">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Send Modal -->
<div class="modal fade" id="kt_modal_test_send" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Send Test Email</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form method="POST" action="{{ route('email-templates.test-send', [$workshop, $emailTemplate]) }}">
                    @csrf
                    <div class="fv-row mb-10">
                        <label class="required fs-6 fw-semibold form-label mb-2">Test Email Address</label>
                        <input type="email" name="test_email" class="form-control form-control-solid" placeholder="Enter email address" required />
                        <div class="form-text">A test email will be sent with sample data</div>
                    </div>
                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Send Test Email</span>
                            <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Duplicate Template Modal -->
<div class="modal fade" id="kt_modal_duplicate_template" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Duplicate Template</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form method="POST" action="{{ route('email-templates.duplicate', [$workshop, $emailTemplate]) }}">
                    @csrf
                    <div class="fv-row mb-10">
                        <label class="fs-6 fw-semibold form-label mb-2">Select Target Workshop</label>
                        <select name="target_workshop_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Select a workshop">
                            <option></option>
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Duplicate Template</span>
                            <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/custom/tinymce/tinymce.bundle.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE
    tinymce.init({
        selector: '#kt_docs_tinymce_basic',
        height: 400,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        setup: function(editor) {
            editor.on('change', function() {
                document.getElementById('email_content').value = editor.getContent();
            });
        }
    });

    // Handle variable insertion from sidebar
    document.querySelectorAll('.variable-btn').forEach(button => {
        button.addEventListener('click', function() {
            insertVariable(this.getAttribute('data-variable'));
        });
    });

    // Handle variable insertion from modal
    document.querySelectorAll('.variable-insert-btn').forEach(button => {
        button.addEventListener('click', function() {
            insertVariable(this.getAttribute('data-variable'));
            // Close the modal
            bootstrap.Modal.getInstance(document.getElementById('kt_modal_template_variables')).hide();
        });
    });

    // Function to insert variable
    function insertVariable(variable) {
        const variableText = '{{ ' + variable + ' }}';
        
        // Insert into subject field if focused
        const subjectField = document.getElementById('email_subject');
        if (document.activeElement === subjectField) {
            const cursorPos = subjectField.selectionStart;
            const textBefore = subjectField.value.substring(0, cursorPos);
            const textAfter = subjectField.value.substring(cursorPos);
            subjectField.value = textBefore + variableText + textAfter;
            subjectField.focus();
            subjectField.setSelectionRange(cursorPos + variableText.length, cursorPos + variableText.length);
        } else {
            // Insert into TinyMCE editor
            tinymce.get('kt_docs_tinymce_basic').insertContent(variableText);
        }
    }

    // Handle preview
    document.getElementById('preview_btn').addEventListener('click', function() {
        const subject = document.getElementById('email_subject').value;
        const content = tinymce.get('kt_docs_tinymce_basic').getContent();
        
        if (!subject || !content) {
            alert('Please fill in both subject and content before previewing.');
            return;
        }

        // Generate sample preview
        const sampleData = {
            'name': 'John Doe',
            'email': 'john.doe@example.com',
            'phone': '+1234567890',
            'company': 'Example Corp',
            'position': 'Software Engineer',
            'ticket_code': 'WS-' + Math.random().toString(36).substr(2, 8).toUpperCase(),
            'qr_code_url': '{{ url("/qr-code/sample") }}',
            'workshop_name': '{{ $workshop->name }}',
            'workshop_location': '{{ $workshop->location }}',
            'workshop_start_date': '{{ $workshop->start_date->format("F j, Y g:i A") }}',
            'workshop_end_date': '{{ $workshop->end_date->format("F j, Y g:i A") }}',
            'ticket_type_name': 'Standard Ticket',
            'ticket_type_price': '$99.00'
        };

        // Replace variables in subject and content
        let previewSubject = subject;
        let previewContent = content;
        
        Object.keys(sampleData).forEach(key => {
            const regex = new RegExp('\\{\\{\\s*' + key + '\\s*\\}\\}', 'g');
            previewSubject = previewSubject.replace(regex, sampleData[key]);
            previewContent = previewContent.replace(regex, sampleData[key]);
        });

        // Show preview modal
        document.getElementById('preview_content').innerHTML = `
            <div class="mb-5">
                <label class="fw-bold fs-6 mb-2">Subject:</label>
                <div class="p-3 bg-light rounded">${previewSubject}</div>
            </div>
            <div class="mb-5">
                <label class="fw-bold fs-6 mb-2">Content:</label>
                <div class="p-3 bg-light rounded">${previewContent}</div>
            </div>
            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                <i class="ki-duotone ki-information fs-2tx text-info me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <div class="d-flex flex-stack flex-grow-1">
                    <div class="fw-semibold">
                        <div class="fs-7 text-gray-700">This is a preview with sample data. Actual emails will use real participant and workshop data.</div>
                    </div>
                </div>
            </div>
        `;
        
        new bootstrap.Modal(document.getElementById('kt_modal_preview')).show();
    });

    // Handle current template preview
    document.querySelector('[data-bs-target="#kt_modal_preview_template"]').addEventListener('click', function() {
        // Load current template preview via AJAX
        fetch('{{ route("email-templates.preview", [$workshop, $emailTemplate]) }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('current_preview_content').innerHTML = `
                    <div class="mb-5">
                        <label class="fw-bold fs-6 mb-2">Subject:</label>
                        <div class="p-3 bg-light rounded">${data.subject}</div>
                    </div>
                    <div class="mb-5">
                        <label class="fw-bold fs-6 mb-2">Content:</label>
                        <div class="p-3 bg-light rounded">${data.content}</div>
                    </div>
                    <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-6">
                        <i class="ki-duotone ki-information fs-2tx text-success me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-7 text-gray-700">This is the current saved template with sample data.</div>
                            </div>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                document.getElementById('current_preview_content').innerHTML = '<div class="alert alert-danger">Failed to load preview</div>';
            });
    });

    // Handle duplicate modal
    const duplicateModal = document.getElementById('kt_modal_duplicate_template');
    if (duplicateModal) {
        duplicateModal.addEventListener('show.bs.modal', function() {
            // Load workshops for duplication
            fetch('{{ route("email-templates.workshops", $workshop) }}')
                .then(response => response.json())
                .then(workshops => {
                    const select = duplicateModal.querySelector('select[name="target_workshop_id"]');
                    select.innerHTML = '<option></option>';
                    workshops.forEach(workshop => {
                        const option = document.createElement('option');
                        option.value = workshop.id;
                        option.textContent = `${workshop.name} (${new Date(workshop.start_date).toLocaleDateString()})`;
                        select.appendChild(option);
                    });
                });
        });
    }

    // Handle form submission
    document.querySelector('form').addEventListener('submit', function() {
        // Update hidden textarea with TinyMCE content
        document.getElementById('email_content').value = tinymce.get('kt_docs_tinymce_basic').getContent();
    });
});
</script>
@endpush