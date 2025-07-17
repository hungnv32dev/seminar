@extends('layouts.app')

@section('title', 'Ticket Types')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                Ticket Types
                @if($workshop)
                    <span class="fs-6 text-muted fw-normal mt-1">for {{ $workshop->name }}</span>
                @endif
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Ticket Types</li>
                @if($workshop)
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">{{ $workshop->name }}</li>
                @endif
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @can('create ticket types')
            <a href="{{ route('ticket-types.create', ['workshop_id' => $workshop?->id]) }}" class="btn btn-sm fw-bold btn-primary">
                <i class="ki-duotone ki-plus fs-2"></i>New Ticket Type
            </a>
            @endcan
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        @if(!$workshop)
        <!--begin::Workshop Filter-->
        <div class="card mb-5">
            <div class="card-body py-4">
                <div class="d-flex align-items-center">
                    <div class="position-relative w-md-400px me-md-2">
                        <i class="ki-duotone ki-magnifier fs-3 text-gray-500 position-absolute top-50 translate-middle ms-6">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <select class="form-select form-select-solid ps-10" data-control="select2" data-placeholder="Filter by workshop" id="workshop_filter">
                            <option value="">All Workshops</option>
                            @foreach($workshops as $workshopOption)
                            <option value="{{ $workshopOption->id }}">{{ $workshopOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" class="btn btn-light-primary" onclick="filterByWorkshop()">Filter</button>
                </div>
            </div>
        </div>
        <!--end::Workshop Filter-->
        @endif

        @if($ticketTypes->count() > 0)
        <!--begin::Row-->
        <div class="row g-6 g-xl-9">
            @foreach($ticketTypes as $ticketType)
            <!--begin::Col-->
            <div class="col-md-6 col-xl-4">
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-9">
                        <!--begin::Card Title-->
                        <div class="card-title m-0">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-50px w-50px bg-light">
                                <i class="ki-duotone ki-price-tag fs-2x text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </div>
                            <!--end::Avatar-->
                        </div>
                        <!--end::Card Title-->
                        
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-category fs-6">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </button>
                            <!--begin::Menu-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3" data-kt-menu="true">
                                <!--begin::Heading-->
                                <div class="menu-item px-3">
                                    <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Actions</div>
                                </div>
                                <!--end::Heading-->
                                
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="{{ route('ticket-types.show', $ticketType) }}" class="menu-link px-3">View Details</a>
                                </div>
                                <!--end::Menu item-->
                                
                                @can('edit ticket types')
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="{{ route('ticket-types.edit', $ticketType) }}" class="menu-link px-3">Edit</a>
                                </div>
                                <!--end::Menu item-->
                                @endcan
                                
                                @can('create ticket types')
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3" data-kt-ticket-type-id="{{ $ticketType->id }}" data-kt-action="duplicate_ticket_type">Duplicate</a>
                                </div>
                                <!--end::Menu item-->
                                @endcan
                                
                                @can('delete ticket types')
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 text-danger" data-kt-ticket-type-id="{{ $ticketType->id }}" data-kt-action="delete_ticket_type">Delete</a>
                                </div>
                                <!--end::Menu item-->
                                @endcan
                            </div>
                            <!--end::Menu-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    
                    <!--begin::Card body-->
                    <div class="card-body p-9">
                        <!--begin::Name-->
                        <div class="fs-3 fw-bold text-gray-900">{{ $ticketType->name }}</div>
                        <!--end::Name-->
                        
                        <!--begin::Description-->
                        <p class="text-gray-500 fw-semibold fs-5 mt-1 mb-7">
                            <a href="{{ route('workshops.show', $ticketType->workshop) }}" class="text-primary text-hover-primary">{{ $ticketType->workshop->name }}</a>
                        </p>
                        <!--end::Description-->
                        
                        <!--begin::Info-->
                        <div class="d-flex flex-wrap mb-5">
                            <!--begin::Due-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-7 mb-3">
                                <div class="fs-6 text-gray-800 fw-bold">${{ number_format($ticketType->price, 2) }}</div>
                                <div class="fw-semibold text-gray-500">Price</div>
                            </div>
                            <!--end::Due-->
                            
                            <!--begin::Budget-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
                                <div class="fs-6 text-gray-800 fw-bold">{{ $ticketType->participants_count ?? 0 }}</div>
                                <div class="fw-semibold text-gray-500">Participants</div>
                            </div>
                            <!--end::Budget-->
                        </div>
                        <!--end::Info-->
                        
                        <!--begin::Progress-->
                        <div class="h-4px w-100 bg-light mb-5" data-bs-toggle="tooltip" title="Paid participants">
                            @php
                                $paidCount = $ticketType->paid_participants_count ?? 0;
                                $totalCount = $ticketType->participants_count ?? 0;
                                $percentage = $totalCount > 0 ? ($paidCount / $totalCount) * 100 : 0;
                            @endphp
                            <div class="bg-success rounded h-4px" style="width: {{ $percentage }}%"></div>
                        </div>
                        <!--end::Progress-->
                        
                        <!--begin::Users-->
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex">
                                <div class="fs-7 text-muted">
                                    {{ $paidCount }}/{{ $totalCount }} paid
                                </div>
                            </div>
                            
                            <div class="d-flex">
                                <div class="fs-7 fw-bold text-gray-800">
                                    ${{ number_format(($ticketType->paid_participants_count ?? 0) * $ticketType->price, 2) }}
                                </div>
                            </div>
                        </div>
                        <!--end::Users-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Col-->
            @endforeach
        </div>
        <!--end::Row-->
        
        <!--begin::Pagination-->
        @if($ticketTypes->hasPages())
        <div class="d-flex justify-content-center mt-10">
            {{ $ticketTypes->links() }}
        </div>
        @endif
        <!--end::Pagination-->
        
        @else
        <!--begin::Empty state-->
        <div class="card">
            <div class="card-body text-center py-20">
                <div class="mb-10">
                    <i class="ki-duotone ki-price-tag fs-5x text-muted">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </div>
                
                <h1 class="fs-2x fw-bold text-gray-800 mb-4">No Ticket Types Found</h1>
                
                <div class="fs-6 text-gray-600 mb-8">
                    @if($workshop)
                        No ticket types have been created for this workshop yet.
                    @else
                        No ticket types have been created yet. Create your first ticket type to get started.
                    @endif
                </div>
                
                @can('create ticket types')
                <a href="{{ route('ticket-types.create', ['workshop_id' => $workshop?->id]) }}" class="btn btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>Create First Ticket Type
                </a>
                @endcan
            </div>
        </div>
        <!--end::Empty state-->
        @endif
    </div>
</div>
<!--end::Content-->
@endsection

@push('scripts')
<script>
"use strict";

// Filter by workshop
function filterByWorkshop() {
    const workshopId = document.getElementById('workshop_filter').value;
    const url = new URL(window.location);
    
    if (workshopId) {
        url.searchParams.set('workshop_id', workshopId);
    } else {
        url.searchParams.delete('workshop_id');
    }
    
    window.location.href = url.toString();
}

// Class definition
var KTTicketTypesList = function () {
    // Handle delete ticket type
    var handleDeleteRows = function () {
        const deleteButtons = document.querySelectorAll('[data-kt-action="delete_ticket_type"]');

        deleteButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();

                const ticketTypeId = d.getAttribute('data-kt-ticket-type-id');
                const ticketTypeName = d.closest('.card').querySelector('.fs-3').innerText;

                // Check if ticket type can be deleted
                fetch(`/ticket-types/${ticketTypeId}/check-deletion`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.can_delete) {
                            Swal.fire({
                                text: "Cannot delete this ticket type:\n" + data.reasons.join('\n'),
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                            return;
                        }

                        // Show delete confirmation
                        Swal.fire({
                            text: "Are you sure you want to delete " + ticketTypeName + "?",
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
                                form.action = '/ticket-types/' + ticketTypeId;
                                
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
                    .catch(error => {
                        console.error('Error checking deletion:', error);
                        Swal.fire({
                            text: "Error checking if ticket type can be deleted.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    });
            })
        });
    }

    // Handle duplicate ticket type
    var handleDuplicateRows = function () {
        const duplicateButtons = document.querySelectorAll('[data-kt-action="duplicate_ticket_type"]');

        duplicateButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/ticket-types/' + d.getAttribute('data-kt-ticket-type-id') + '/duplicate';
                
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
            handleDeleteRows();
            handleDuplicateRows();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTTicketTypesList.init();
});
</script>
@endpush