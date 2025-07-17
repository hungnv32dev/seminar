@extends('layouts.app')

@section('title', 'Create Participant')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Create Participant</h1>
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
                <li class="breadcrumb-item text-muted">Create</li>
            </ul>
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        <form id="kt_participant_form" class="form d-flex flex-column flex-lg-row" action="{{ route('participants.store') }}" method="POST">
            @csrf
            
            <!--begin::Aside column-->
            <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                <!--begin::Workshop selection-->
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Workshop & Ticket</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="mb-10 fv-row">
                            <label class="required form-label">Workshop</label>
                            <select class="form-select mb-2" data-control="select2" data-placeholder="Select workshop" name="workshop_id" id="workshop_select">
                                <option></option>
                                @foreach($workshops as $workshopOption)
                                <option value="{{ $workshopOption->id }}" {{ old('workshop_id', $workshop?->id) == $workshopOption->id ? 'selected' : '' }}>{{ $workshopOption->name }}</option>
                                @endforeach
                            </select>
                            @error('workshop_id')
                            <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-10 fv-row">
                            <label class="required form-label">Ticket Type</label>
                            <select class="form-select mb-2" data-control="select2" data-placeholder="Select ticket type" name="ticket_type_id" id="ticket_type_select">
                                <option></option>
                                @if($ticketTypes->count() > 0)
                                    @foreach($ticketTypes as $ticketType)
                                    <option value="{{ $ticketType->id }}" data-price="{{ $ticketType->price }}" {{ old('ticket_type_id') == $ticketType->id ? 'selected' : '' }}>
                                        {{ $ticketType->name }} - ${{ number_format($ticketType->price, 2) }}
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('ticket_type_id')
                            <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <!--end::Workshop selection-->

                <!--begin::Payment status-->
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Payment Status</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="1" name="is_paid" id="is_paid" {{ old('is_paid') ? 'checked' : '' }} />
                            <label class="form-check-label" for="is_paid">
                                Mark as Paid
                            </label>
                        </div>
                        @error('is_paid')
                        <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                        <div class="text-muted fs-7 mt-2">Check if participant has already paid for the workshop.</div>
                    </div>
                </div>
                <!--end::Payment status-->
            </div>
            <!--end::Aside column-->

            <!--begin::Main column-->
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                <!--begin::General options-->
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Personal Information</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-9 mb-8">
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Full Name</label>
                                <input type="text" class="form-control" placeholder="Enter full name" name="name" value="{{ old('name') }}" />
                                @error('name')
                                <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Col-->

                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Email Address</label>
                                <input type="email" class="form-control" placeholder="Enter email address" name="email" value="{{ old('email') }}" />
                                @error('email')
                                <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Col-->
                        </div>

                        <div class="row g-9 mb-8">
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Phone Number</label>
                                <input type="text" class="form-control" placeholder="Enter phone number" name="phone" value="{{ old('phone') }}" />
                                @error('phone')
                                <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Col-->

                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Company</label>
                                <input type="text" class="form-control" placeholder="Enter company name" name="company" value="{{ old('company') }}" />
                                @error('company')
                                <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Col-->
                        </div>

                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Position/Title</label>
                            <input type="text" name="position" class="form-control mb-2" placeholder="Enter position or job title" value="{{ old('position') }}" />
                            @error('position')
                            <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="mb-10">
                            <label class="form-label">Special Requirements</label>
                            <textarea name="special_requirements" class="form-control" rows="3" placeholder="Any dietary restrictions, accessibility needs, or other special requirements...">{{ old('special_requirements') }}</textarea>
                            @error('special_requirements')
                            <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-7">Optional field for any special needs or requirements.</div>
                        </div>
                        <!--end::Input group-->
                    </div>
                </div>
                <!--end::General options-->

                <div class="d-flex justify-content-end">
                    <a href="{{ route('participants.index') }}" id="kt_participant_cancel" class="btn btn-light me-5">Cancel</a>
                    <button type="submit" id="kt_participant_submit" class="btn btn-primary">
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
var KTParticipantCreate = function () {
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
                                message: 'Full name is required'
                            }
                        }
                    },
                    'email': {
                        validators: {
                            notEmpty: {
                                message: 'Email address is required'
                            },
                            emailAddress: {
                                message: 'The value is not a valid email address'
                            }
                        }
                    },
                    'workshop_id': {
                        validators: {
                            notEmpty: {
                                message: 'Workshop selection is required'
                            }
                        }
                    },
                    'ticket_type_id': {
                        validators: {
                            notEmpty: {
                                message: 'Ticket type selection is required'
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
                    form.reset();
                    window.location = cancelButton.getAttribute("href");
                }
            });
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
                            option.setAttribute('data-price', ticketType.price);
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
            form = document.querySelector('#kt_participant_form');
            submitButton = document.querySelector('#kt_participant_submit');
            cancelButton = document.querySelector('#kt_participant_cancel');

            handleForm();
            handleWorkshopChange();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTParticipantCreate.init();
});
</script>
@endpush