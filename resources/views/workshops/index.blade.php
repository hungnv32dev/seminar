@extends('layouts.app')

@section('title', 'Workshops')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Workshops</h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Workshops</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @can('create workshops')
            <a href="{{ route('workshops.create') }}" class="btn btn-sm fw-bold btn-primary">
                <i class="ki-duotone ki-plus fs-2"></i>New Workshop
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
                        <input type="text" data-kt-workshop-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search workshops..." value="{{ $filters['search'] ?? '' }}" />
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-workshop-table-toolbar="base">
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
                            <div class="px-7 py-5" data-kt-workshop-table-filter="form">
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-semibold">Status:</label>
                                    <select class="form-select form-select-solid fw-bold" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-workshop-table-filter="status" data-hide-search="true">
                                        <option></option>
                                        <option value="draft" {{ ($filters['status'] ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ ($filters['status'] ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="ongoing" {{ ($filters['status'] ?? '') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                        <option value="completed" {{ ($filters['status'] ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ ($filters['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-semibold">Organizer:</label>
                                    <select class="form-select form-select-solid fw-bold" data-kt-select2="true" data-placeholder="Select organizer" data-allow-clear="true" data-kt-workshop-table-filter="organizer">
                                        <option></option>
                                        @foreach($organizers as $organizer)
                                        <option value="{{ $organizer->id }}" {{ ($filters['organizer_id'] ?? '') == $organizer->id ? 'selected' : '' }}>{{ $organizer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" data-kt-menu-dismiss="true" data-kt-workshop-table-filter="reset">Reset</button>
                                    <button type="submit" class="btn btn-primary fw-semibold px-6" data-kt-menu-dismiss="true" data-kt-workshop-table-filter="filter">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card header-->
            
            <!--begin::Card body-->
            <div class="card-body py-4">
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_workshops_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">Workshop</th>
                            <th class="min-w-125px">Date & Time</th>
                            <th class="min-w-125px">Location</th>
                            <th class="min-w-125px">Status</th>
                            <th class="min-w-125px">Participants</th>
                            <th class="min-w-125px">Revenue</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse($workshops as $workshop)
                        <tr>
                            <td class="d-flex align-items-center">
                                <div class="d-flex flex-column">
                                    <a href="{{ route('workshops.show', $workshop) }}" class="text-gray-800 text-hover-primary mb-1">{{ $workshop->name }}</a>
                                    <span class="text-muted">{{ Str::limit($workshop->description, 50) }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 mb-1">{{ $workshop->start_date->format('M d, Y') }}</span>
                                    <span class="text-muted">{{ $workshop->start_date->format('H:i') }} - {{ $workshop->end_date->format('H:i') }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-gray-800">{{ $workshop->location }}</span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'published' => 'primary',
                                        'ongoing' => 'success',
                                        'completed' => 'info',
                                        'cancelled' => 'danger'
                                    ];
                                @endphp
                                <span class="badge badge-light-{{ $statusColors[$workshop->status] ?? 'secondary' }}">{{ ucfirst($workshop->status) }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 mb-1">{{ $workshop->participants_count ?? 0 }}</span>
                                    <span class="text-muted">{{ $workshop->checked_in_count ?? 0 }} checked in</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-gray-800">${{ number_format($workshop->total_revenue ?? 0, 2) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <a href="{{ route('workshops.show', $workshop) }}" class="menu-link px-3">View</a>
                                    </div>
                                    @can('edit workshops')
                                    <div class="menu-item px-3">
                                        <a href="{{ route('workshops.edit', $workshop) }}" class="menu-link px-3">Edit</a>
                                    </div>
                                    @endcan
                                    @can('create workshops')
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3" data-kt-workshop-id="{{ $workshop->id }}" data-kt-action="duplicate_workshop">Duplicate</a>
                                    </div>
                                    @endcan
                                    @can('delete workshops')
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 text-danger" data-kt-workshop-id="{{ $workshop->id }}" data-kt-action="delete_workshop">Delete</a>
                                    </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-10">
                                <div class="text-gray-500 fs-6 mb-3">No workshops found</div>
                                @can('create workshops')
                                <a href="{{ route('workshops.create') }}" class="btn btn-primary">Create First Workshop</a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <!--end::Table-->
                
                <!--begin::Pagination-->
                @if($workshops->hasPages())
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center py-3">
                        <span class="text-gray-700">
                            Showing {{ $workshops->firstItem() }} to {{ $workshops->lastItem() }} of {{ $workshops->total() }} workshops
                        </span>
                    </div>
                    {{ $workshops->links() }}
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
var KTWorkshopsList = function () {
    // Define shared variables
    var table = document.getElementById('kt_workshops_table');
    var datatable;
    var toolbarBase;
    var toolbarSelected;
    var selectedCount;

    // Private functions
    var initWorkshopTable = function () {
        // Set date data order
        const tableRows = table.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            const realDate = moment(dateRow[1].innerHTML, "MMM DD, YYYY").format(); // select date from 2nd column in table
            dateRow[1].setAttribute('data-order', realDate);
        });

        // Init datatable --- more info on datatables: https://datatables.net/manual/
        datatable = $(table).DataTable({
            "info": false,
            'order': [],
            'pageLength': 10,
            'columnDefs': [
                { orderable: false, targets: 6 }, // Disable ordering on column 6 (actions)
            ]
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-workshop-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Filter Datatable
    var handleFilterDatatable = function () {
        // Select filter options
        const filterForm = document.querySelector('[data-kt-workshop-table-filter="form"]');
        const filterButton = filterForm.querySelector('[data-kt-workshop-table-filter="filter"]');
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

            // Filter datatable --- official docs reference: https://datatables.net/reference/api/search()
            datatable.search(filterString).draw();
        });
    }

    // Reset Filter
    var handleResetForm = function () {
        // Select reset button
        const resetButton = document.querySelector('[data-kt-workshop-table-filter="reset"]');

        // Reset datatable
        resetButton.addEventListener('click', function () {
            // Select filter options
            const filterForm = document.querySelector('[data-kt-workshop-table-filter="form"]');
            const selectOptions = filterForm.querySelectorAll('select');

            // Reset select2 values -- more info: https://select2.org/programmatic-control/add-select-clear-items
            selectOptions.forEach(select => {
                $(select).val('').trigger('change');
            });

            // Reset datatable --- official docs reference: https://datatables.net/reference/api/search()
            datatable.search('').draw();
        });
    }

    // Delete workshop
    var handleDeleteRows = function () {
        // Select all delete buttons
        const deleteButtons = table.querySelectorAll('[data-kt-action="delete_workshop"]');

        deleteButtons.forEach(d => {
            // Delete button on click
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Select parent row
                const parent = e.target.closest('tr');

                // Get workshop name
                const workshopName = parent.querySelectorAll('td')[0].innerText;

                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                Swal.fire({
                    text: "Are you sure you want to delete " + workshopName + "?",
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
                        // Create form and submit
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/workshops/' + d.getAttribute('data-kt-workshop-id');
                        
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

    // Duplicate workshop
    var handleDuplicateRows = function () {
        // Select all duplicate buttons
        const duplicateButtons = table.querySelectorAll('[data-kt-action="duplicate_workshop"]');

        duplicateButtons.forEach(d => {
            // Duplicate button on click
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/workshops/' + d.getAttribute('data-kt-workshop-id') + '/duplicate';
                
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

            initWorkshopTable();
            handleSearchDatatable();
            handleFilterDatatable();
            handleDeleteRows();
            handleDuplicateRows();
            handleResetForm();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTWorkshopsList.init();
});
</script>
@endpush