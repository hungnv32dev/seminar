@extends('layouts.app')

@section('title', 'Create Ticket Type')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Create Ticket Type</h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('ticket-types.index') }}" class="text-muted text-hover-primary">Ticket Types</a>
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
        <div class="row g-7">
            <!--begin::Form-->
            <div class="col-lg-8">
                <form id="kt_ticket_type_form" class="form" action="{{ route('ticket-types.store') }}" method="POST">
                    @csrf
                    
                    <!--begin::Card-->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Ticket Type Information</h2>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <label class="required form-label">Workshop</label>
                                <select class="form-select" data-control="select2" data-placeholder="Select workshop" name="workshop_id" id="workshop_select">
                                    <option></option>
                                    @foreach($workshops as $workshopOption)
                                    <option value="{{ $workshopOption->id }}" {{ old('workshop_id', $workshop?->id) == $workshopOption->id ? 'selected' : '' }}>
                                        {{ $workshopOption->name }}
                                        <span class="text-muted">({{ $workshopOption->start_date->format('M d, Y') }})</span>
                                    </option>
                                    @endforeach
                                </select>
                                @error('workshop_id')
                                <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                                <div class="text-muted fs-7">Select the workshop this ticket type belongs to.</div>
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <label class="required form-label">Ticket Type Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter ticket type name" value="{{ old('name') }}" />
                                @error('name')
                                <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                                <div class="text-muted fs-7">A descriptive name for this ticket type (e.g., "Early Bird", "Regular", "VIP").</div>
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <label class="required form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price" class="form-control" placeholder="0.00" step="0.01" min="0" value="{{ old('price') }}" />
                                </div>
                                @error('price')
                                <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                                <div class="text-muted fs-7">Set the price for this ticket type. Use 0 for free tickets.</div>
                            </div>
                            <!--end::Input group-->
                        </div>
                        
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('ticket-types.index', ['workshop_id' => $workshop?->id]) }}" class="btn btn-light me-5">Cancel</a>
                                <button type="submit" id="kt_ticket_type_submit" class="btn btn-primary">
                                    <span class="indicator-label">Save</span>
                                    <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--end::Card-->
                </form>
            </div>
            <!--end::Form-->
            
            <!--begin::Sidebar-->
            <div class="col-lg-4">
                <!--begin::Pricing card-->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Pricing Preview</h2>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!--begin::Pricing-->
                        <div class="text-center">
                            <div class="mb-5">
                                <i class="ki-duotone ki-price-tag fs-5x text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </div>
                            
                            <div class="mb-5">
                                <h1 class="display-4 fw-bold text-gray-800" id="price_preview">$0.00</h1>
                                <div class="fs-6 text-muted" id="ticket_name_preview">Ticket Type Name</div>
                            </div>
                            
                            <div class="separator separator-dashed my-5"></div>
                            
                            <div class="text-start">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-semibold text-gray-600">Workshop:</span>
                                    <span class="fw-bold text-gray-800" id="workshop_name_preview">Not selected</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-semibold text-gray-600">Price:</span>
                                    <span class="fw-bold text-gray-800" id="price_text_preview">$0.00</span>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold text-gray-600">Type:</span>
                                    <span class="fw-bold text-gray-800" id="price_type_preview">Free</span>
                                </div>
                            </div>
                        </div>
                        <!--end::Pricing-->
                    </div>
                </div>
                <!--end::Pricing card-->
                
                <!--begin::Tips card-->
                <div class="card mt-5">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Tips</h2>
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
                                    <h4 class="text-gray-900 fw-bold">Pricing Strategy</h4>
                                    <div class="fs-6 text-gray-700">Consider offering multiple ticket types with different pricing tiers to maximize attendance and revenue.</div>
                                </div>
                            </div>
                        </div>
                        <!--end::Notice-->
                        
                        <div class="mb-6">
                            <h5 class="fw-bold text-gray-800 mb-4">Common Ticket Types:</h5>
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bullet bullet-dot bg-success me-3"></span>
                                    <span class="fw-semibold text-gray-700">Early Bird - Lower price for early registrations</span>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bullet bullet-dot bg-primary me-3"></span>
                                    <span class="fw-semibold text-gray-700">Regular - Standard pricing</span>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bullet bullet-dot bg-warning me-3"></span>
                                    <span class="fw-semibold text-gray-700">Student - Discounted for students</span>
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <span class="bullet bullet-dot bg-info me-3"></span>
                                    <span class="fw-semibold text-gray-700">VIP - Premium with extra benefits</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--end::Tips card-->
            </div>
            <!--end::Sidebar-->
        </div>
    </div>
</div>
<!--end::Content-->
@endsection

@push('scripts')
<script>
"use strict";

// Class definition
var KTTicketTypeCreate = function () {
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
                    'name': {
                        validators: {
                            notEmpty: {
                                message: 'Ticket type name is required'
                            }
                        }
                    },
                    'price': {
                        validators: {
                            notEmpty: {
                                message: 'Price is required'
                            },
                            numeric: {
                                message: 'Price must be a valid number'
                            },
                            greaterThan: {
                                min: 0,
                                message: 'Price must be greater than or equal to 0'
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
    }

    // Handle preview updates
    var handlePreview = function() {
        const nameInput = document.querySelector('input[name="name"]');
        const priceInput = document.querySelector('input[name="price"]');
        const workshopSelect = document.querySelector('select[name="workshop_id"]');
        
        const pricePreview = document.getElementById('price_preview');
        const ticketNamePreview = document.getElementById('ticket_name_preview');
        const workshopNamePreview = document.getElementById('workshop_name_preview');
        const priceTextPreview = document.getElementById('price_text_preview');
        const priceTypePreview = document.getElementById('price_type_preview');

        // Update name preview
        nameInput.addEventListener('input', function() {
            const name = this.value || 'Ticket Type Name';
            ticketNamePreview.textContent = name;
        });

        // Update price preview
        priceInput.addEventListener('input', function() {
            const price = parseFloat(this.value) || 0;
            const formattedPrice = '$' + price.toFixed(2);
            
            pricePreview.textContent = formattedPrice;
            priceTextPreview.textContent = formattedPrice;
            priceTypePreview.textContent = price === 0 ? 'Free' : 'Paid';
        });

        // Update workshop preview
        workshopSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const workshopName = selectedOption.text || 'Not selected';
            workshopNamePreview.textContent = workshopName;
        });
    }

    // Public methods
    return {
        init: function () {
            // Elements
            form = document.querySelector('#kt_ticket_type_form');
            submitButton = document.querySelector('#kt_ticket_type_submit');

            handleForm();
            handlePreview();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTTicketTypeCreate.init();
});
</script>
@endpush