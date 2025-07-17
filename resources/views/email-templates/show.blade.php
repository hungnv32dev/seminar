@extends('layouts.app')

@section('title', 'Email Template - ' . $emailTemplate->type_label)

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ $emailTemplate->type_label }} Template</h1>
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
                <li class="breadcrumb-item text-muted">{{ $emailTemplate->type_label }}</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @can('edit workshops')
            <a href="{{ route('email-templates.edit', [$workshop, $emailTemplate]) }}" class="btn btn-sm fw-bold btn-primary">
                <i class="ki-duotone ki-pencil fs-2"></i>Edit Template
            </a>
            @endcan
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <!--begin::Layout-->
        <div class="d-flex flex-column flex-lg-row">
            <!--begin::Sidebar-->
            <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
                <!--begin::Card-->
                <div class="card mb-5 mb-xl-8">
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Summary-->
                        <div class="d-flex flex-center flex-column py-5">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-100px symbol-circle mb-7">
                                <div class="symbol-label fs-1 bg-light-primary text-primary">
                                    <i class="ki-duotone ki-{{ $this->getTemplateIcon($emailTemplate->type) }} fs-2x">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                            <!--end::Avatar-->
                            <!--begin::Name-->
                            <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">{{ $emailTemplate->type_label }}</a>
                            <!--end::Name-->
                            <!--begin::Position-->
                            <div class="mb-9">
                                <div class="badge badge-lg badge-light-success d-inline">Active Template</div>
                            </div>
                            <!--end::Position-->
                        </div>
                        <!--end::Summary-->
                        
                        <!--begin::Details toggle-->
                        <div class="d-flex flex-stack fs-4 py-3">
                            <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_template_view_details" role="button" aria-expanded="false" aria-controls="kt_template_view_details">Details
                            <span class="ms-2 rotate-180">
                                <i class="ki-duotone ki-down fs-3"></i>
                            </span></div>
                            @can('edit workshops')
                            <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Edit template">
                                <a href="{{ route('email-templates.edit', [$workshop, $emailTemplate]) }}" class="btn btn-sm btn-light-primary">Edit</a>
                            </span>
                            @endcan
                        </div>
                        <!--end::Details toggle-->
                        <div class="separator"></div>
                        <!--begin::Details content-->
                        <div id="kt_template_view_details" class="collapse show">
                            <div class="pb-5 fs-6">
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Template Type</div>
                                <div class="text-gray-600">{{ $emailTemplate->type_label }}</div>
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Workshop</div>
                                <div class="text-gray-600">
                                    <a href="{{ route('workshops.show', $workshop) }}" class="text-gray-600 text-hover-primary">{{ $workshop->name }}</a>
                                </div>
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Subject</div>
                                <div class="text-gray-600">{{ $emailTemplate->subject }}</div>
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Created</div>
                                <div class="text-gray-600">{{ $emailTemplate->created_at->format('d M Y, H:i') }}</div>
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Last Updated</div>
                                <div class="text-gray-600">{{ $emailTemplate->updated_at->format('d M Y, H:i') }}</div>
                                <!--begin::Details item-->
                            </div>
                        </div>
                        <!--end::Details content-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->

                <!--begin::Quick Actions-->
                <div class="card mb-5 mb-xl-8">
                    <!--begin::Card header-->
                    <div class="card-header border-0">
                        <div class="card-title">
                            <h3 class="fw-bold m-0">Quick Actions</h3>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-2">
                        <div class="d-flex flex-column gap-3">
                            <!--begin::Action-->
                            <button type="button" class="btn btn-light-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#kt_modal_preview_template">
                                <i class="ki-duotone ki-eye fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Preview Template
                            </button>
                            <!--end::Action-->

                            <!--begin::Action-->
                            <button type="button" class="btn btn-light-info btn-sm w-100" data-bs-toggle="modal" data-bs-target="#kt_modal_test_send">
                                <i class="ki-duotone ki-send fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Send Test Email
                            </button>
                            <!--end::Action-->

                            <!--begin::Action-->
                            <button type="button" class="btn btn-light-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#kt_modal_duplicate_template">
                                <i class="ki-duotone ki-copy fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                                Duplicate Template
                            </button>
                            <!--end::Action-->

                            @can('delete workshops')
                            <!--begin::Action-->
                            <form action="{{ route('email-templates.destroy', [$workshop, $emailTemplate]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light-danger btn-sm w-100" 
                                        onclick="return confirm('Are you sure you want to delete this template? This action cannot be undone.')">
                                    <i class="ki-duotone ki-trash fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                    Delete Template
                                </button>
                            </form>
                            <!--end::Action-->
                            @endcan
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Quick Actions-->

                <!--begin::Template Variables-->
                <div class="card mb-5 mb-xl-8">
                    <!--begin::Card header-->
                    <div class="card-header border-0">
                        <div class="card-title">
                            <h3 class="fw-bold m-0">Available Variables</h3>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-2">
                        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-5">
                            <i class="ki-duotone ki-information fs-2tx text-primary me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <div class="fs-7 text-gray-700">Variables are replaced with actual data when emails are sent</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex flex-column gap-2">
                            @foreach($availableVariables as $variable => $description)
                            <div class="d-flex flex-column">
                                <code class="fs-7 text-primary">{{ "{{ " . $variable . " }}" }}</code>
                                <span class="text-muted fs-8">{{ $description }}</span>
                            </div>
                            @if(!$loop->last)
                            <div class="separator separator-dashed my-2"></div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Template Variables-->
            </div>
            <!--end::Sidebar-->

            <!--begin::Content-->
            <div class="flex-lg-row-fluid ms-lg-15">
                <!--begin:::Tabs-->
                <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_template_view_content_tab">Template Content</a>
                    </li>
                    <!--end:::Tab item-->
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_template_view_preview_tab">Live Preview</a>
                    </li>
                    <!--end:::Tab item-->
                </ul>
                <!--end:::Tabs-->

                <!--begin:::Tab content-->
                <div class="tab-content" id="myTabContent">
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade show active" id="kt_template_view_content_tab" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card card-flush mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header mt-6">
                                <!--begin::Card title-->
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Email Subject</h2>
                                    <div class="fs-6 fw-semibold text-muted">The subject line recipients will see</div>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body p-9 pt-4">
                                <div class="p-5 bg-light-primary rounded border border-primary border-dashed">
                                    <div class="fs-5 fw-bold text-gray-800">{{ $emailTemplate->subject }}</div>
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->

                        <!--begin::Card-->
                        <div class="card card-flush mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header mt-6">
                                <!--begin::Card title-->
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Email Content</h2>
                                    <div class="fs-6 fw-semibold text-muted">The main body of the email template</div>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body p-9 pt-4">
                                <div class="p-5 bg-light rounded border border-gray-300 border-dashed">
                                    <div class="fs-6 text-gray-800" style="white-space: pre-wrap;">{!! $emailTemplate->content !!}</div>
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end:::Tab pane-->

                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_template_view_preview_tab" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card card-flush mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header mt-6">
                                <!--begin::Card title-->
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Live Preview</h2>
                                    <div class="fs-6 fw-semibold text-muted">Preview with sample data</div>
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <button type="button" class="btn btn-sm btn-light-primary" id="refresh_preview_btn">
                                        <i class="ki-duotone ki-arrows-circle fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Refresh Preview
                                    </button>
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body p-9 pt-4">
                                <div id="live_preview_content">
                                    <div class="d-flex align-items-center justify-content-center" style="min-height: 200px;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end:::Tab pane-->
                </div>
                <!--end:::Tab content-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Layout-->

    </div>
</div>
<!--end::Content-->

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
                <div id="modal_preview_content">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load live preview on tab switch
    const previewTab = document.querySelector('a[href="#kt_template_view_preview_tab"]');
    if (previewTab) {
        previewTab.addEventListener('shown.bs.tab', function() {
            loadLivePreview();
        });
    }

    // Refresh preview button
    const refreshBtn = document.getElementById('refresh_preview_btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            loadLivePreview();
        });
    }

    // Load preview for modal
    const previewModal = document.getElementById('kt_modal_preview_template');
    if (previewModal) {
        previewModal.addEventListener('show.bs.modal', function() {
            loadModalPreview();
        });
    }

    // Load workshops for duplication
    const duplicateModal = document.getElementById('kt_modal_duplicate_template');
    if (duplicateModal) {
        duplicateModal.addEventListener('show.bs.modal', function() {
            loadWorkshopsForDuplication();
        });
    }

    function loadLivePreview() {
        const previewContent = document.getElementById('live_preview_content');
        previewContent.innerHTML = `
            <div class="d-flex align-items-center justify-content-center" style="min-height: 200px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        fetch('{{ route("email-templates.preview", [$workshop, $emailTemplate]) }}')
            .then(response => response.json())
            .then(data => {
                previewContent.innerHTML = `
                    <div class="mb-5">
                        <label class="fw-bold fs-6 mb-2">Subject:</label>
                        <div class="p-3 bg-light-primary rounded border border-primary border-dashed">${data.subject}</div>
                    </div>
                    <div class="mb-5">
                        <label class="fw-bold fs-6 mb-2">Content:</label>
                        <div class="p-3 bg-light rounded border border-gray-300 border-dashed">${data.content}</div>
                    </div>
                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                        <i class="ki-duotone ki-information fs-2tx text-info me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-7 text-gray-700">This preview uses sample data. Actual emails will use real participant and workshop data.</div>
                            </div>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                previewContent.innerHTML = '<div class="alert alert-danger">Failed to load preview</div>';
            });
    }

    function loadModalPreview() {
        const modalContent = document.getElementById('modal_preview_content');
        modalContent.innerHTML = `
            <div class="d-flex align-items-center justify-content-center" style="min-height: 200px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        fetch('{{ route("email-templates.preview", [$workshop, $emailTemplate]) }}')
            .then(response => response.json())
            .then(data => {
                modalContent.innerHTML = `
                    <div class="mb-5">
                        <label class="fw-bold fs-6 mb-2">Subject:</label>
                        <div class="p-3 bg-light-primary rounded border border-primary border-dashed">${data.subject}</div>
                    </div>
                    <div class="mb-5">
                        <label class="fw-bold fs-6 mb-2">Content:</label>
                        <div class="p-3 bg-light rounded border border-gray-300 border-dashed">${data.content}</div>
                    </div>
                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                        <i class="ki-duotone ki-information fs-2tx text-info me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-7 text-gray-700">This preview uses sample data. Actual emails will use real participant and workshop data.</div>
                            </div>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                modalContent.innerHTML = '<div class="alert alert-danger">Failed to load preview</div>';
            });
    }

    function loadWorkshopsForDuplication() {
        fetch('{{ route("email-templates.workshops", $workshop) }}')
            .then(response => response.json())
            .then(workshops => {
                const select = document.querySelector('#kt_modal_duplicate_template select[name="target_workshop_id"]');
                select.innerHTML = '<option></option>';
                workshops.forEach(workshop => {
                    const option = document.createElement('option');
                    option.value = workshop.id;
                    option.textContent = `${workshop.name} (${new Date(workshop.start_date).toLocaleDateString()})`;
                    select.appendChild(option);
                });
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
@endphp