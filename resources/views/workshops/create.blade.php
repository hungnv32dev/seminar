@extends('layouts.app')

@section('title', 'Create Workshop')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Create Workshop</h1>
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
                <li class="breadcrumb-item text-muted">Create</li>
            </ul>
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        <form id="kt_workshop_form" class="form d-flex flex-column flex-lg-row" action="{{ route('workshops.store') }}" method="POST">
            @csrf
            
            <!--begin::Aside column-->
            <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                <!--begin::Thumbnail settings-->
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Workshop Status</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <select class="form-select mb-2" data-control="select2" data-hide-search="true" data-placeholder="Select status" name="status">
                            <option></option>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', 'draft') == 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                        @error('status')
                        <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                        <div class="text-muted fs-7">Set the workshop status.</div>
                    </div>
                </div>
                <!--end::Thumbnail settings-->

                <!--begin::Category & tags-->
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Organizers</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <select class="form-select" data-control="select2" data-placeholder="Select organizers" data-allow-clear="true" multiple="multiple" name="organizer_ids[]">
                            @foreach($organizers as $organizer)
                            <option value="{{ $organizer->id }}" {{ in_array($organizer->id, old('organizer_ids', [])) ? 'selected' : '' }}>{{ $organizer->name }}</option>
                            @endforeach
                        </select>
                        @error('organizer_ids')
                        <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                        <div class="text-muted fs-7 mt-2">Add organizers to help manage this workshop.</div>
                    </div>
                </div>
                <!--end::Category & tags-->
            </div>
            <!--end::Aside column-->

            <!--begin::Main column-->
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                <!--begin::General options-->
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>General</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <label class="required form-label">Workshop Name</label>
                            <input type="text" name="name" class="form-control mb-2" placeholder="Workshop name" value="{{ old('name') }}" />
                            @error('name')
                            <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-7">A workshop name is required and recommended to be unique.</div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="mb-10">
                            <label class="form-label">Description</label>
                            <div id="kt_workshop_description" name="description" class="min-h-200px mb-2">{{ old('description') }}</div>
                            <textarea name="description" style="display: none;">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-7">Set a description to the workshop for better visibility.</div>
                        </div>
                        <!--end::Input group-->
                    </div>
                </div>
                <!--end::General options-->

                <!--begin::Workshop details-->
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Workshop Details</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-9 mb-8">
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Start Date & Time</label>
                                <input class="form-control" placeholder="Pick date & time" name="start_date" id="kt_workshop_start_date" value="{{ old('start_date') }}" />
                                @error('start_date')
                                <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Col-->

                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">End Date & Time</label>
                                <input class="form-control" placeholder="Pick date & time" name="end_date" id="kt_workshop_end_date" value="{{ old('end_date') }}" />
                                @error('end_date')
                                <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Col-->
                        </div>

                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <label class="required form-label">Location</label>
                            <input type="text" name="location" class="form-control mb-2" placeholder="Workshop location" value="{{ old('location') }}" />
                            @error('location')
                            <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-7">Enter the workshop location or venue.</div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <label class="form-label">Maximum Participants</label>
                            <input type="number" name="max_participants" class="form-control mb-2" placeholder="Enter maximum participants" value="{{ old('max_participants') }}" min="1" />
                            @error('max_participants')
                            <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-7">Set the maximum number of participants (optional).</div>
                        </div>
                        <!--end::Input group-->
                    </div>
                </div>
                <!--end::Workshop details-->

                <div class="d-flex justify-content-end">
                    <a href="{{ route('workshops.index') }}" id="kt_workshop_cancel" class="btn btn-light me-5">Cancel</a>
                    <button type="submit" id="kt_workshop_submit" class="btn btn-primary">
                        <span class="indicator-label">Save</span>
                        <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
            <!--end::Main column-->
        </form>
    </div>
</div>
<!--end::Content-->
@endsection

@push('scripts')
<script>
"use strict";

// Class definition
var KTWorkshopCreate = function () {
    // Elements
    var form;
    var submitButton;
    var cancelButton;
    var validator;

    // Handle form
    var handleForm = function() {
        // Init form validation rules
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'name': {
                        validators: {
                            notEmpty: {
                                message: 'Workshop name is required'
                            }
                        }
                    },
                    'start_date': {
                        validators: {
                            notEmpty: {
                                message: 'Start date is required'
                            }
                        }
                    },
                    'end_date': {
                        validators: {
                            notEmpty: {
                                message: 'End date is required'
                            }
                        }
                    },
                    'location': {
                        validators: {
                            notEmpty: {
                                message: 'Location is required'
                            }
                        }
                    },
                    'status': {
                        validators: {
                            notEmpty: {
                                message: 'Status is required'
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
                        submitButton.setAttribute('data-kt-indicator', 'on');
                        submitButton.disabled = true;

                        // Get description from Quill editor
                        const description = quill.root.innerHTML;
                        document.querySelector('textarea[name="description"]').value = description;

                        setTimeout(function() {
                            form.submit();
                        }, 2000);
                    }
                });
            }
        });

        // Handle cancel button
        cancelButton.addEventListener('click', function (e) {
            e.preventDefault();

            Swal.fire({
                text: "Are you sure you would like to cancel?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, cancel it!",
                cancelButtonText: "No, return",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.value) {
                    form.reset(); // Reset form
                    window.location = form.getAttribute("data-kt-redirect-url");
                }
            });
        });
    }

    // Handle date pickers
    var handleDatePickers = function() {
        $("#kt_workshop_start_date").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
        });

        $("#kt_workshop_end_date").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
        });
    }

    // Handle Quill editor
    var quill;
    var handleQuill = function() {
        var options = {
            modules: {
                toolbar: [
                    [{
                        header: [1, 2, false]
                    }],
                    ['bold', 'italic', 'underline'],
                    ['image', 'code-block']
                ]
            },
            placeholder: 'Type workshop description...',
            theme: 'snow' // or 'bubble'
        };

        quill = new Quill('#kt_workshop_description', options);
    }

    // Public methods
    return {
        init: function () {
            // Elements
            form = document.querySelector('#kt_workshop_form');
            submitButton = document.querySelector('#kt_workshop_submit');
            cancelButton = document.querySelector('#kt_workshop_cancel');

            handleForm();
            handleDatePickers();
            handleQuill();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTWorkshopCreate.init();
});
</script>
@endpush