@extends('layouts.app')

@section('title', 'Import Participants')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Import Participants</h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('participants.index') }}" class="text-muted text-hover-primary">Participants</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Import</li>
            </ul>
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        <div class="row g-7">
            <!--begin::Import Form-->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Import Excel File</h2>
                        </div>
                    </div>
                    
                    <form id="kt_import_form" class="form" action="{{ route('participants.process-import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <label class="required form-label">Workshop</label>
                                <select class="form-select" data-control="select2" data-placeholder="Select workshop" name="workshop_id" id="workshop_select">
                                    <option></option>
                                    @foreach($workshops as $workshopOption)
                                    <option value="{{ $workshopOption->id }}" {{ old('workshop_id', $workshop?->id) == $workshopOption->id ? 'selected' : '' }}>{{ $workshopOption->name }}</option>
                                    @endforeach
                                </select>
                                @error('workshop_id')
                                <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                                <div class="text-muted fs-7">Select the workshop to import participants for.</div>
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <label class="required form-label">Default Ticket Type</label>
                                <select class="form-select" data-control="select2" data-placeholder="Select default ticket type" name="default_ticket_type_id" id="ticket_type_select">
                                    <option></option>
                                    @if($ticketTypes->count() > 0)
                                        @foreach($ticketTypes as $ticketType)
                                        <option value="{{ $ticketType->id }}" {{ old('default_ticket_type_id') == $ticketType->id ? 'selected' : '' }}>
                                            {{ $ticketType->name }} - ${{ number_format($ticketType->price, 2) }}
                                        </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('default_ticket_type_id')
                                <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                                <div class="text-muted fs-7">This ticket type will be assigned to participants if not specified in the Excel file.</div>
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <label class="required form-label">Excel File</label>
                                <input type="file" class="form-control" name="excel_file" accept=".xlsx,.xls,.csv" />
                                @error('excel_file')
                                <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                                <div class="text-muted fs-7">Upload Excel file (.xlsx, .xls) or CSV file. Maximum file size: 10MB.</div>
                            </div>
                            <!--end::Input group-->
                        </div>
                        
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('participants.index') }}" class="btn btn-light me-5">Cancel</a>
                                <button type="submit" id="kt_import_submit" class="btn btn-primary">
                                    <span class="indicator-label">
                                        <i class="ki-duotone ki-file-up fs-2"></i>Import Participants
                                    </span>
                                    <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--end::Import Form-->
            
            <!--begin::Instructions-->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Import Instructions</h2>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!--begin::Notice-->
                        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-6">
                            <i class="ki-duotone ki-information fs-2tx text-primary me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">Excel Format Requirements</h4>
                                    <div class="fs-6 text-gray-700">Your Excel file should contain the following columns:</div>
                                </div>
                            </div>
                        </div>
                        <!--end::Notice-->
                        
                        <!--begin::Required columns-->
                        <div class="mb-6">
                            <h5 class="fw-bold text-gray-800 mb-4">Required Columns:</h5>
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bullet bullet-dot bg-success me-3"></span>
                                    <span class="fw-semibold text-gray-700">name</span>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bullet bullet-dot bg-success me-3"></span>
                                    <span class="fw-semibold text-gray-700">email</span>
                                </li>
                            </ul>
                        </div>
                        <!--end::Required columns-->
                        
                        <!--begin::Optional columns-->
                        <div class="mb-6">
                            <h5 class="fw-bold text-gray-800 mb-4">Optional Columns:</h5>
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bullet bullet-dot bg-info me-3"></span>
                                    <span class="fw-semibold text-gray-700">phone</span>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bullet bullet-dot bg-info me-3"></span>
                                    <span class="fw-semibold text-gray-700">company</span>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bullet bullet-dot bg-info me-3"></span>
                                    <span class="fw-semibold text-gray-700">position</span>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bullet bullet-dot bg-info me-3"></span>
                                    <span class="fw-semibold text-gray-700">special_requirements</span>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bullet bullet-dot bg-info me-3"></span>
                                    <span class="fw-semibold text-gray-700">is_paid</span> <span class="text-muted ms-2">(1 or 0)</span>
                                </li>
                            </ul>
                        </div>
                        <!--end::Optional columns-->
                        
                        <!--begin::Sample-->
                        <div class="mb-6">
                            <h5 class="fw-bold text-gray-800 mb-4">Sample Excel Format:</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="fs-7 fw-bold">name</th>
                                            <th class="fs-7 fw-bold">email</th>
                                            <th class="fs-7 fw-bold">phone</th>
                                            <th class="fs-7 fw-bold">company</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="fs-7">John Doe</td>
                                            <td class="fs-7">john@example.com</td>
                                            <td class="fs-7">+1234567890</td>
                                            <td class="fs-7">ABC Corp</td>
                                        </tr>
                                        <tr>
                                            <td class="fs-7">Jane Smith</td>
                                            <td class="fs-7">jane@example.com</td>
                                            <td class="fs-7">+0987654321</td>
                                            <td class="fs-7">XYZ Ltd</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--end::Sample-->
                        
                        <!--begin::Notes-->
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                            <i class="ki-duotone ki-information fs-2tx text-warning me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <div class="fs-6 text-gray-700">
                                        <strong>Important Notes:</strong><br>
                                        • Duplicate emails will be skipped<br>
                                        • Invalid email formats will be rejected<br>
                                        • Ticket emails will be sent automatically<br>
                                        • The first row should contain column headers
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Notes-->
                    </div>
                </div>
            </div>
            <!--end::Instructions-->
        </div>
    </div>
</div>
<!--end::Content-->
@endsection

@push('scripts')
<script>
"use strict";

// Class definition
var KTParticipantImport = function () {
    // Elements
    var form;
    var submitButton;
    var validator;

    // Handle form
    var handleForm = function() {
        // Init form validation rules
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'workshop_id': {
                        validators: {
                            notEmpty: {
                                message: 'Workshop selection is required'
                            }
                        }
                    },
                    'default_ticket_type_id': {
                        validators: {
                            notEmpty: {
                                message: 'Default ticket type selection is required'
                            }
                        }
                    },
                    'excel_file': {
                        validators: {
                            notEmpty: {
                                message: 'Excel file is required'
                            },
                            file: {
                                extension: 'xlsx,xls,csv',
                                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv',
                                maxSize: 10485760, // 10MB
                                message: 'Please select a valid Excel or CSV file (max 10MB)'
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );

        // Handle form submit
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            // Validate form before submit
            if (validator) {
                validator.validate().then(function (status) {
                    console.log('validated!');

                    if (status == 'Valid') {
                        // Show confirmation dialog
                        Swal.fire({
                            text: "Are you sure you want to import participants from this Excel file?",
                            icon: "warning",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "Yes, import!",
                            cancelButtonText: "No, cancel",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                                cancelButton: "btn fw-bold btn-active-light-primary"
                            }
                        }).then(function (result) {
                            if (result.value) {
                                submitButton.setAttribute('data-kt-indicator', 'on');
                                submitButton.disabled = true;
                                form.submit();
                            }
                        });
                    }
                });
            }
        });
    }

    // Handle workshop selection change
    var handleWorkshopChange = function() {
        const workshopSelect = document.getElementById('workshop_select');
        const ticketTypeSelect = document.getElementById('ticket_type_select');

        workshopSelect.addEventListener('change', function() {
            const workshopId = this.value;
            
            // Clear ticket type options
            ticketTypeSelect.innerHTML = '<option></option>';
            
            if (workshopId) {
                // Fetch ticket types for selected workshop
                fetch(`/participants/workshop/${workshopId}/ticket-types`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(ticketType => {
                            const option = document.createElement('option');
                            option.value = ticketType.id;
                            option.textContent = `${ticketType.name} - $${parseFloat(ticketType.price).toFixed(2)}`;
                            ticketTypeSelect.appendChild(option);
                        });
                        
                        // Refresh Select2
                        $(ticketTypeSelect).trigger('change');
                    })
                    .catch(error => {
                        console.error('Error fetching ticket types:', error);
                    });
            }
        });
    }

    // Public methods
    return {
        init: function () {
            // Elements
            form = document.querySelector('#kt_import_form');
            submitButton = document.querySelector('#kt_import_submit');

            handleForm();
            handleWorkshopChange();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTParticipantImport.init();
});
</script>
@endpush