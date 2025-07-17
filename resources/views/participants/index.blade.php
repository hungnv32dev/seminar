@extends('layouts.app')

@section('title', 'Participants')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Participants</h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Participants</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @can('import participants')
            <a href="{{ route('participants.import') }}" class="btn btn-sm fw-bold btn-light-primary">
                <i class="ki-duotone ki-file-up fs-2"></i>Import Excel
            </a>
            @endcan
            @can('create participants')
            <a href="{{ route('participants.create') }}" class="btn btn-sm fw-bold btn-primary">
                <i class="ki-duotone ki-plus fs-2"></i>New Participant
            </a>
            @endcan
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        <!--begin::Card-->
        <div class="card">
            <!--begin::Card header-->
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" data-kt-participant-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search participants..." value="{{ $filters['search'] ?? '' }}" />
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-participant-table-toolbar="base">
                        <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            <i class="ki-duotone ki-filter fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>Filter
                        </button>
                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                            <div class="px-7 py-5">
                                <div class="fs-5 text-gray-900 fw-bold">Filter Options</div>
                            </div>
                            <div class="separator border-gray-200"></div>
                            <div class="px-7 py-5" data-kt-participant-table-filter="form">
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-semibold">Workshop:</label>
                                    <select class="form-select form-select-solid fw-bold" data-kt-select2="true" data-placeholder="Select workshop" data-allow-clear="true" data-kt-participant-table-filter="workshop">
                                        <option></option>
                                        @foreach($workshops as $workshop)
                                        <option value="{{ $workshop->id }}" {{ ($filters['workshop_id'] ?? '') == $workshop->id ? 'selected' : '' }}>{{ $workshop->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-semibold">Payment Status:</label>
                                    <select class="form-select form-select-solid fw-bold" data-kt-select2="true" data-placeholder="Select status" data-allow-clear="true" data-kt-participant-table-filter="payment" data-hide-search="true">
                                        <option></option>
                                        <option value="1" {{ ($filters['is_paid'] ?? '') === '1' ? 'selected' : '' }}>Paid</option>
                                        <option value="0" {{ ($filters['is_paid'] ?? '') === '0' ? 'selected' : '' }}>Unpaid</option>
                                    </select>
                                </div>
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-semibold">Check-in Status:</label>
                                    <select class="form-select form-select-solid fw-bold" data-kt-select2="true" data-placeholder="Select status" data-allow-clear="true" data-kt-participant-table-filter="checkin" data-hide-search="true">
                                        <option></option>
                                        <option value="1" {{ ($filters['is_checked_in'] ?? '') === '1' ? 'selected' : '' }}>Checked In</option>
                                        <option value="0" {{ ($filters['is_checked_in'] ?? '') === '0' ? 'selected' : '' }}>Not Checked In</option>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" data-kt-menu-dismiss="true" data-kt-participant-table-filter="reset">Reset</button>
                                    <button type="submit" class="btn btn-primary fw-semibold px-6" data-kt-menu-dismiss="true" data-kt-participant-table-filter="filter">Apply</button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            <i class="ki-duotone ki-exit-up fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>Export
                        </button>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ route('participants.export') }}" class="menu-link px-3">Export Excel</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end align-items-center d-none" data-kt-participant-table-toolbar="selected">
                        <div class="fw-bold me-5">
                        <span class="me-2" data-kt-participant-table-select="selected_count"></span>Selected</div>
                        <button type="button" class="btn btn-danger" data-kt-participant-table-select="delete_selected">Delete Selected</button>
                    </div>
                </div>
            </div>
            <!--end::Card header-->
            
            <!--begin::Card body-->
            <div class="card-body py-4">
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_participants_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">
                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_participants_table .form-check-input" value="1" />
                                </div>
                            </th>
                            <th class="min-w-125px">Participant</th>
                            <th class="min-w-125px">Workshop</th>
                            <th class="min-w-125px">Ticket Type</th>
                            <th class="min-w-125px">Payment</th>
                            <th class="min-w-125px">Check-in</th>
                            <th class="min-w-125px">Registration</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse($participants as $participant)
                        <tr>
                            <td>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="{{ $participant->id }}" />
                                </div>
                            </td>
                            <td class="d-flex align-items-center">
                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                    <div class="symbol-label fs-3 bg-light-primary text-primary">
                                        {{ strtoupper(substr($participant->name, 0, 2)) }}
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="{{ route('participants.show', $participant) }}" class="text-gray-800 text-hover-primary mb-1">{{ $participant->name }}</a>
                                    <span class="text-muted">{{ $participant->email }}</span>
                                    @if($participant->phone)
                                    <span class="text-muted">{{ $participant->phone }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <a href="{{ route('workshops.show', $participant->workshop) }}" class="text-gray-800 text-hover-primary mb-1">{{ $participant->workshop->name }}</a>
                                    <span class="text-muted">{{ $participant->workshop->start_date->format('M d, Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 mb-1">{{ $participant->ticketType->name }}</span>
                                    <span class="text-muted">${{ number_format($participant->ticketType->price, 2) }}</span>
                                </div>
                            </td>
                            <td>
                                @if($participant->is_paid)
                                    <span class="badge badge-light-success">Paid</span>
                                @else
                                    <span class="badge badge-light-danger">Unpaid</span>
                                @endif
                            </td>
                            <td>
                                @if($participant->is_checked_in)
                                    <div class="d-flex flex-column">
                                        <span class="badge badge-light-success mb-1">Checked In</span>
                                        <span class="text-muted fs-7">{{ $participant->checked_in_at->format('M d, H:i') }}</span>
                                    </div>
                                @else
                                    <span class="badge badge-light-warning">Not Checked In</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 mb-1">{{ $participant->created_at->format('M d, Y') }}</span>
                                    <span class="text-muted">{{ $participant->created_at->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <a href="{{ route('participants.show', $participant) }}" class="menu-link px-3">View</a>
                                    </div>
                                    @can('edit participants')
                                    <div class="menu-item px-3">
                                        <a href="{{ route('participants.edit', $participant) }}" class="menu-link px-3">Edit</a>
                                    </div>
                                    @endcan
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3" data-kt-participant-id="{{ $participant->id }}" data-kt-action="resend_ticket">Resend Ticket</a>
                                    </div>
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3" data-kt-participant-id="{{ $participant->id }}" data-kt-action="toggle_payment">
                                            {{ $participant->is_paid ? 'Mark Unpaid' : 'Mark Paid' }}
                                        </a>
                                    </div>
                                    @can('delete participants')
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 text-danger" data-kt-participant-id="{{ $participant->id }}" data-kt-action="delete_participant">Delete</a>
                                    </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-10">
                                <div class="text-gray-500 fs-6 mb-3">No participants found</div>
                                @can('create participants')
                                <a href="{{ route('participants.create') }}" class="btn btn-primary">Add First Participant</a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <!--end::Table-->
                
                <!--begin::Pagination-->
                @if($participants->hasPages())
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center py-3">
                        <span class="text-gray-700">
                            Showing {{ $participants->firstItem() }} to {{ $participants->lastItem() }} of {{ $participants->total() }} participants
                        </span>
                    </div>
                    {{ $participants->links() }}
                </div>
                @endif
                <!--end::Pagination-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
</div>
<!--end::Content-->
@endsection

@push('scripts')
<script>
"use strict";

// Class definition
var KTParticipantsList = function () {
    // Define shared variables
    var table = document.getElementById('kt_participants_table');
    var datatable;
    var toolbarBase;
    var toolbarSelected;
    var selectedCount;

    // Private functions
    var initParticipantTable = function () {
        // Set date data order
        const tableRows = table.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            if (dateRow.length > 6) {
                const realDate = moment(dateRow[6].innerHTML, "MMM DD, YYYY").format();
                dateRow[6].setAttribute('data-order', realDate);
            }
        });

        // Init datatable
        datatable = $(table).DataTable({
            "info": false,
            'order': [],
            'pageLength': 10,
            'columnDefs': [
                { orderable: false, targets: 0 }, // Disable ordering on column 0 (checkbox)
                { orderable: false, targets: 7 }, // Disable ordering on column 7 (actions)
            ]
        });
    }

    // Search Datatable
    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-participant-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Filter Datatable
    var handleFilterDatatable = function () {
        const filterForm = document.querySelector('[data-kt-participant-table-filter="form"]');
        const filterButton = filterForm.querySelector('[data-kt-participant-table-filter="filter"]');
        const selectOptions = filterForm.querySelectorAll('select');

        // Filter datatable on submit
        filterButton.addEventListener('click', function () {
            var filterString = '';

            // Get filter values
            selectOptions.forEach((item, index) => {
                if (item.value && item.value !== '') {
                    if (index !== 0) {
                        filterString += ' ';
                    }
                    filterString += item.value;
                }
            });

            // Filter datatable
            datatable.search(filterString).draw();
        });
    }

    // Reset Filter
    var handleResetForm = function () {
        const resetButton = document.querySelector('[data-kt-participant-table-filter="reset"]');

        resetButton.addEventListener('click', function () {
            const filterForm = document.querySelector('[data-kt-participant-table-filter="form"]');
            const selectOptions = filterForm.querySelectorAll('select');

            // Reset select2 values
            selectOptions.forEach(select => {
                $(select).val('').trigger('change');
            });

            // Reset datatable
            datatable.search('').draw();
        });
    }

    // Delete participant
    var handleDeleteRows = function () {
        const deleteButtons = table.querySelectorAll('[data-kt-action="delete_participant"]');

        deleteButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();

                const parent = e.target.closest('tr');
                const participantName = parent.querySelectorAll('td')[1].querySelector('a').innerText;

                Swal.fire({
                    text: "Are you sure you want to delete " + participantName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/participants/' + d.getAttribute('data-kt-participant-id');
                        
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = '{{ csrf_token() }}';
                        
                        form.appendChild(methodInput);
                        form.appendChild(tokenInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            })
        });
    }

    // Resend ticket
    var handleResendTicket = function () {
        const resendButtons = table.querySelectorAll('[data-kt-action="resend_ticket"]');

        resendButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/participants/' + d.getAttribute('data-kt-participant-id') + '/resend-ticket';
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            })
        });
    }

    // Toggle payment
    var handleTogglePayment = function () {
        const toggleButtons = table.querySelectorAll('[data-kt-action="toggle_payment"]');

        toggleButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/participants/' + d.getAttribute('data-kt-participant-id') + '/toggle-payment';
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            })
        });
    }

    // Public methods
    return {
        init: function () {
            if (!table) {
                return;
            }

            initParticipantTable();
            handleSearchDatatable();
            handleFilterDatatable();
            handleDeleteRows();
            handleResendTicket();
            handleTogglePayment();
            handleResetForm();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTParticipantsList.init();
});
</script>
@endpush