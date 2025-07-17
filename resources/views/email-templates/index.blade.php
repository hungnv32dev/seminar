@extends('layouts.app')

@section('title', 'Email Templates - ' . $workshop->name)

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Email Templates</h1>
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
                <li class="breadcrumb-item text-muted">Email Templates</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('workshops.show', $workshop) }}" class="btn btn-sm fw-bold btn-secondary">
                <i class="ki-duotone ki-arrow-left fs-2"></i>Back to Workshop
            </a>
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <!--begin::Workshop Info-->
        <div class="card mb-5 mb-xl-8">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="me-7 mb-4">
                        <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                            <div class="symbol-label fs-2 fw-semibold text-success bg-light-success">
                                {{ strtoupper(substr($workshop->name, 0, 2)) }}
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ $workshop->name }}</a>
                                    <span class="badge badge-light-{{ $workshop->status === 'published' ? 'success' : ($workshop->status === 'draft' ? 'warning' : 'primary') }} fs-8 fw-bold">{{ ucfirst($workshop->status) }}</span>
                                </div>
                                <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                    <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                        <i class="ki-duotone ki-geolocation fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>{{ $workshop->location }}
                                    </a>
                                    <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                        <i class="ki-duotone ki-calendar fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>{{ $workshop->start_date->format('M j, Y') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Workshop Info-->

        <!--begin::Row-->
        <div class="row g-6 g-xl-9">
            @foreach($availableTypes as $type => $label)
            <!--begin::Col-->
            <div class="col-md-6 col-xl-4">
                <!--begin::Card-->
                <div class="card h-100">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-9">
                        <div class="card-title m-0">
                            <div class="symbol symbol-50px w-50px bg-light-{{ in_array($type, $templates->pluck('type')->toArray()) ? 'success' : 'warning' }}">
                                <i class="ki-duotone ki-{{ $this->getTemplateIcon($type) }} fs-2x text-{{ in_array($type, $templates->pluck('type')->toArray()) ? 'success' : 'warning' }}">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        <div class="card-toolbar">
                            @if(in_array($type, $templates->pluck('type')->toArray()))
                                <span class="badge badge-light-success">Active</span>
                            @else
                                <span class="badge badge-light-warning">Missing</span>
                            @endif
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body p-9">
                        <!--begin::Name-->
                        <div class="fs-3 fw-bold text-gray-900">{{ $label }}</div>
                        <!--end::Name-->
                        <!--begin::Description-->
                        <p class="text-gray-500 fw-semibold fs-5 mt-1 mb-7">
                            {{ $this->getTemplateDescription($type) }}
                        </p>
                        <!--end::Description-->
                        <!--begin::Info-->
                        @php
                            $template = $templates->where('type', $type)->first();
                        @endphp
                        @if($template)
                            <div class="d-flex flex-wrap mb-5">
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-7 mb-3">
                                    <div class="fs-6 text-gray-800 fw-bold">Created</div>
                                    <div class="fw-semibold text-gray-500">{{ $template->created_at->format('M j, Y') }}</div>
                                </div>
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
                                    <div class="fs-6 text-gray-800 fw-bold">Updated</div>
                                    <div class="fw-semibold text-gray-500">{{ $template->updated_at->format('M j, Y') }}</div>
                                </div>
                            </div>
                        @endif
                        <!--end::Info-->
                    </div>
                    <!--end::Card body-->
                    <!--begin::Card footer-->
                    <div class="card-footer d-flex flex-stack pt-0">
                        @if($template)
                            <div class="d-flex align-items-center">
                                <a href="{{ route('email-templates.show', [$workshop, $template]) }}" class="btn btn-sm btn-light-primary me-2">View</a>
                                <a href="{{ route('email-templates.edit', [$workshop, $template]) }}" class="btn btn-sm btn-light">Edit</a>
                            </div>
                            <div class="d-flex">
                                <button class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_preview_template" data-template-id="{{ $template->id }}" title="Preview">
                                    <i class="ki-duotone ki-eye fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </button>
                            </div>
                        @else
                            <a href="{{ route('email-templates.create', [$workshop, 'type' => $type]) }}" class="btn btn-sm btn-primary">Create Template</a>
                        @endif
                    </div>
                    <!--end::Card footer-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Col-->
            @endforeach
        </div>
        <!--end::Row-->

        @if($templates->count() > 0)
        <!--begin::Templates Table-->
        <div class="card mt-10">
            <!--begin::Card header-->
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bold m-0">All Templates</h3>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_template_variables">
                        <i class="ki-duotone ki-information fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>Template Variables
                    </button>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th>Type</th>
                                <th>Subject</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th class="text-end min-w-100px">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @foreach($templates as $template)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-3">
                                            <div class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-{{ $this->getTemplateIcon($template->type) }} fs-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold">{{ $template->type_label }}</span>
                                            <span class="text-muted fs-7">{{ $template->type }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-gray-800">{{ Str::limit($template->subject, 50) }}</span>
                                </td>
                                <td>{{ $template->created_at->format('M j, Y g:i A') }}</td>
                                <td>{{ $template->updated_at->format('M j, Y g:i A') }}</td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                    <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                    <!--begin::Menu-->
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                        <div class="menu-item px-3">
                                            <a href="{{ route('email-templates.show', [$workshop, $template]) }}" class="menu-link px-3">View</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="{{ route('email-templates.edit', [$workshop, $template]) }}" class="menu-link px-3">Edit</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_preview_template" data-template-id="{{ $template->id }}">Preview</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_duplicate_template" data-template-id="{{ $template->id }}">Duplicate</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_test_send" data-template-id="{{ $template->id }}">Test Send</a>
                                        </div>
                                        <div class="separator"></div>
                                        <div class="menu-item px-3">
                                            <form action="{{ route('email-templates.destroy', [$workshop, $template]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="menu-link px-3 btn btn-link p-0 text-start w-100 text-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this template?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <!--end::Menu-->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Templates Table-->
        @endif

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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\EmailTemplate::getAvailableVariables() as $variable => $description)
                            <tr>
                                <td><code>{{ "{{ " . $variable . " }}" }}</code></td>
                                <td>{{ $description }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                <form id="kt_modal_duplicate_template_form" method="POST">
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
                <form id="kt_modal_test_send_form" method="POST">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle preview modal
    const previewModal = document.getElementById('kt_modal_preview_template');
    if (previewModal) {
        previewModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const templateId = button.getAttribute('data-template-id');
            
            // Find template in the templates array
            const templates = @json($templates);
            const template = templates.find(t => t.id == templateId);
            
            if (template) {
                // Load preview via AJAX
                fetch(`{{ route('email-templates.preview', [$workshop, ':template']) }}`.replace(':template', templateId))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('preview_content').innerHTML = `
                            <div class="mb-5">
                                <label class="fw-bold fs-6 mb-2">Subject:</label>
                                <div class="p-3 bg-light rounded">${data.subject}</div>
                            </div>
                            <div class="mb-5">
                                <label class="fw-bold fs-6 mb-2">Content:</label>
                                <div class="p-3 bg-light rounded" style="white-space: pre-wrap;">${data.content}</div>
                            </div>
                        `;
                    })
                    .catch(error => {
                        document.getElementById('preview_content').innerHTML = '<div class="alert alert-danger">Failed to load preview</div>';
                    });
            }
        });
    }

    // Handle duplicate modal
    const duplicateModal = document.getElementById('kt_modal_duplicate_template');
    if (duplicateModal) {
        duplicateModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const templateId = button.getAttribute('data-template-id');
            
            // Set form action
            const form = document.getElementById('kt_modal_duplicate_template_form');
            form.action = `{{ route('email-templates.duplicate', [$workshop, ':template']) }}`.replace(':template', templateId);
            
            // Load workshops for duplication
            fetch(`{{ route('email-templates.workshops', $workshop) }}`)
                .then(response => response.json())
                .then(workshops => {
                    const select = form.querySelector('select[name="target_workshop_id"]');
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

    // Handle test send modal
    const testSendModal = document.getElementById('kt_modal_test_send');
    if (testSendModal) {
        testSendModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const templateId = button.getAttribute('data-template-id');
            
            // Set form action
            const form = document.getElementById('kt_modal_test_send_form');
            form.action = `{{ route('email-templates.test-send', [$workshop, ':template']) }}`.replace(':template', templateId);
        });
    }
});
</script>
@endpush

@php
function getTemplateIcon($type) {
    $icons = [
        'invite' => 'send',
        'confirm' => 'check-circle',
        'ticket' => 'ticket',
        'reminder' => 'notification-bing',
        'thank_you' => 'heart',
    ];
    return $icons[$type] ?? 'sms';
}

function getTemplateDescription($type) {
    $descriptions = [
        'invite' => 'Send invitations to potential participants',
        'confirm' => 'Confirm registration and provide details',
        'ticket' => 'Send tickets with QR codes to participants',
        'reminder' => 'Remind participants about upcoming workshop',
        'thank_you' => 'Thank participants after workshop completion',
    ];
    return $descriptions[$type] ?? 'Email template for workshop communication';
}
@endphp