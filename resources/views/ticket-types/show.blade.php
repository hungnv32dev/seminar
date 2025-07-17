@extends('layouts.app')

@section('title', $ticketType->name)

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ $ticketType->name }}</h1>
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
                <li class="breadcrumb-item text-muted">{{ $ticketType->name }}</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @can('edit ticket types')
            <a href="{{ route('ticket-types.edit', $ticketType) }}" class="btn btn-sm fw-bold btn-primary">
                <i class="ki-duotone ki-pencil fs-2"></i>Edit Ticket Type
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
                                <div class="symbol-label fs-2 fw-semibold text-primary bg-light-primary">
                                    <i class="ki-duotone ki-price-tag fs-2x">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </div>
                            </div>
                            <!--end::Avatar-->
                            
                            <!--begin::Name-->
                            <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">{{ $ticketType->name }}</a>
                            <!--end::Name-->
                            
                            <!--begin::Price-->
                            <div class="mb-9">
                                <div class="badge badge-lg badge-light-success d-inline">
                                    ${{ number_format($ticketType->price, 2) }}
                                </div>
                            </div>
                            <!--end::Price-->
                        </div>
                        <!--end::Summary-->
                        
                        <!--begin::Details toggle-->
                        <div class="d-flex flex-stack fs-4 py-3">
                            <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_ticket_type_details" role="button" aria-expanded="false" aria-controls="kt_ticket_type_details">Details
                            <span class="ms-2 rotate-180">
                                <i class="ki-duotone ki-down fs-3"></i>
                            </span></div>
                        </div>
                        <!--end::Details toggle-->
                        
                        <div class="separator"></div>
                        
                        <!--begin::Details content-->
                        <div id="kt_ticket_type_details" class="collapse show">
                            <div class="pb-5 fs-6">
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Workshop</div>
                                <div class="text-gray-600">
                                    <a href="{{ route('workshops.show', $ticketType->workshop) }}" class="text-primary">{{ $ticketType->workshop->name }}</a>
                                </div>
                                <!--begin::Details item-->
                                
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Price</div>
                                <div class="text-gray-600">${{ number_format($ticketType->price, 2) }}</div>
                                <!--begin::Details item-->
                                
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Type</div>
                                <div class="text-gray-600">{{ $ticketType->price == 0 ? 'Free Ticket' : 'Paid Ticket' }}</div>
                                <!--begin::Details item-->
                                
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Workshop Date</div>
                                <div class="text-gray-600">{{ $ticketType->workshop->start_date->format('M d, Y H:i') }}</div>
                                <!--begin::Details item-->
                                
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Workshop Location</div>
                                <div class="text-gray-600">{{ $ticketType->workshop->location }}</div>
                                <!--begin::Details item-->
                            </div>
                        </div>
                        <!--end::Details content-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
                
                <!--begin::Connected Accounts-->
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
                        <div class="d-flex flex-stack">
                            <div class="d-flex flex-column w-100">
                                <a href="{{ route('participants.index', ['ticket_type_id' => $ticketType->id]) }}" class="btn btn-sm btn-light-primary mb-2">
                                    <i class="ki-duotone ki-people fs-2"></i>View Participants
                                </a>
                                <a href="{{ route('participants.create', ['workshop_id' => $ticketType->workshop_id]) }}" class="btn btn-sm btn-light-success mb-2">
                                    <i class="ki-duotone ki-plus fs-2"></i>Add Participant
                                </a>
                                @can('create ticket types')
                                <form action="{{ route('ticket-types.duplicate', $ticketType) }}" method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light-info w-100">
                                        <i class="ki-duotone ki-copy fs-2"></i>Duplicate Ticket Type
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Connected Accounts-->
            </div>
            <!--end::Sidebar-->
            
            <!--begin::Content-->
            <div class="flex-lg-row-fluid ms-lg-15">
                <!--begin:::Tabs-->
                <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_ticket_type_overview">Overview</a>
                    </li>
                    <!--end:::Tab item-->
                    
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_ticket_type_participants">Participants</a>
                    </li>
                    <!--end:::Tab item-->
                </ul>
                <!--end:::Tabs-->
                
                <!--begin:::Tab content-->
                <div class="tab-content" id="myTabContent">
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade show active" id="kt_ticket_type_overview" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card mb-5 mb-xl-10">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Statistics</h3>
                                </div>
                            </div>
                            
                            <div class="card-body pt-9 pb-0">
                                <!--begin::Details-->
                                <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                                    <!--begin::Stats-->
                                    <div class="flex-grow-1">
                                        <!--begin::Stats-->
                                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                            <!--begin::User-->
                                            <div class="d-flex flex-column">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="text-gray-900 fs-2 fw-bold me-1">{{ $stats['total_participants'] }}</span>
                                                    <span class="text-gray-500 fs-6 fw-semibold">Total Participants</span>
                                                </div>
                                            </div>
                                            <!--end::User-->
                                        </div>
                                        <!--end::Stats-->
                                        
                                        <!--begin::Info-->
                                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                                <i class="ki-duotone ki-profile-circle fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>{{ $stats['paid_participants'] }} Paid
                                            </a>
                                            
                                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                                <i class="ki-duotone ki-check-circle fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>{{ $stats['checked_in_participants'] }} Checked In
                                            </a>
                                            
                                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary mb-2">
                                                <i class="ki-duotone ki-dollar fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>${{ number_format($stats['total_revenue'], 2) }} Revenue
                                            </a>
                                        </div>
                                        <!--end::Info-->
                                    </div>
                                    <!--end::Stats-->
                                </div>
                                <!--end::Details-->
                            </div>
                        </div>
                        <!--end::Card-->
                        
                        <!--begin::Row-->
                        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                            <!--begin::Col-->
                            <div class="col-lg-6">
                                <!--begin::Summary-->
                                <div class="card h-md-100">
                                    <!--begin::Header-->
                                    <div class="card-header border-0 pt-5">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold fs-3 mb-1">Payment Summary</span>
                                            <span class="text-muted fw-semibold fs-7">Payment status breakdown</span>
                                        </h3>
                                    </div>
                                    <!--end::Header-->
                                    
                                    <!--begin::Body-->
                                    <div class="card-body pt-5">
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Paid Participants</span>
                                            <span class="badge badge-light-success fs-8 fw-bold">{{ $stats['paid_participants'] }}</span>
                                        </div>
                                        <!--end::Item-->
                                        
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Unpaid Participants</span>
                                            <span class="badge badge-light-danger fs-8 fw-bold">{{ $stats['total_participants'] - $stats['paid_participants'] }}</span>
                                        </div>
                                        <!--end::Item-->
                                        
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center">
                                            <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Total Revenue</span>
                                            <span class="badge badge-light-primary fs-8 fw-bold">${{ number_format($stats['total_revenue'], 2) }}</span>
                                        </div>
                                        <!--end::Item-->
                                    </div>
                                    <!--end::Body-->
                                </div>
                                <!--end::Summary-->
                            </div>
                            <!--end::Col-->
                            
                            <!--begin::Col-->
                            <div class="col-lg-6">
                                <!--begin::Summary-->
                                <div class="card h-md-100">
                                    <!--begin::Header-->
                                    <div class="card-header border-0 pt-5">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold fs-3 mb-1">Check-in Summary</span>
                                            <span class="text-muted fw-semibold fs-7">Attendance status breakdown</span>
                                        </h3>
                                    </div>
                                    <!--end::Header-->
                                    
                                    <!--begin::Body-->
                                    <div class="card-body pt-5">
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Checked In</span>
                                            <span class="badge badge-light-success fs-8 fw-bold">{{ $stats['checked_in_participants'] }}</span>
                                        </div>
                                        <!--end::Item-->
                                        
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Not Checked In</span>
                                            <span class="badge badge-light-warning fs-8 fw-bold">{{ $stats['total_participants'] - $stats['checked_in_participants'] }}</span>
                                        </div>
                                        <!--end::Item-->
                                        
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center">
                                            <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Attendance Rate</span>
                                            <span class="badge badge-light-info fs-8 fw-bold">
                                                {{ $stats['total_participants'] > 0 ? round(($stats['checked_in_participants'] / $stats['total_participants']) * 100, 1) : 0 }}%
                                            </span>
                                        </div>
                                        <!--end::Item-->
                                    </div>
                                    <!--end::Body-->
                                </div>
                                <!--end::Summary-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end:::Tab pane-->
                    
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_ticket_type_participants" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card mb-5 mb-xl-10">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Recent Participants</h3>
                                </div>
                                <div class="card-toolbar">
                                    <a href="{{ route('participants.index', ['ticket_type_id' => $ticketType->id]) }}" class="btn btn-sm btn-light-primary">
                                        View All
                                    </a>
                                </div>
                            </div>
                            
                            <div class="card-body pt-9 pb-0">
                                @if($ticketType->participants->count() > 0)
                                    @foreach($ticketType->participants->take(10) as $participant)
                                    <div class="d-flex align-items-center mb-7">
                                        <div class="symbol symbol-50px me-5">
                                            <div class="symbol-label fs-2 fw-semibold text-primary bg-light-primary">
                                                {{ strtoupper(substr($participant->name, 0, 2)) }}
                                            </div>
                                        </div>
                                        
                                        <div class="flex-grow-1">
                                            <a href="{{ route('participants.show', $participant) }}" class="text-gray-900 fw-bold text-hover-primary fs-6">{{ $participant->name }}</a>
                                            <span class="text-muted d-block fw-semibold">{{ $participant->email }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-column align-items-end">
                                            <div class="d-flex mb-1">
                                                @if($participant->is_paid)
                                                    <span class="badge badge-light-success me-1">Paid</span>
                                                @else
                                                    <span class="badge badge-light-danger me-1">Unpaid</span>
                                                @endif
                                                
                                                @if($participant->is_checked_in)
                                                    <span class="badge badge-light-success">Checked In</span>
                                                @else
                                                    <span class="badge badge-light-warning">Not Checked In</span>
                                                @endif
                                            </div>
                                            <span class="text-muted fs-7">{{ $participant->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-10">
                                        <div class="text-gray-500 fs-6 mb-3">No participants registered yet</div>
                                        <a href="{{ route('participants.create', ['workshop_id' => $ticketType->workshop_id]) }}" class="btn btn-primary">Add First Participant</a>
                                    </div>
                                @endif
                            </div>
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
@endsection