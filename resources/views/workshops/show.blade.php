@extends('layouts.app')

@section('title', $workshop->name)

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ $workshop->name }}</h1>
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
                <li class="breadcrumb-item text-muted">{{ $workshop->name }}</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @can('edit workshops')
            <a href="{{ route('workshops.edit', $workshop) }}" class="btn btn-sm fw-bold btn-primary">
                <i class="ki-duotone ki-pencil fs-2"></i>Edit Workshop
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
                                <div class="symbol-label fs-2 fw-semibold text-success bg-light-success">
                                    {{ strtoupper(substr($workshop->name, 0, 2)) }}
                                </div>
                            </div>
                            <!--end::Avatar-->
                            
                            <!--begin::Name-->
                            <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">{{ $workshop->name }}</a>
                            <!--end::Name-->
                            
                            <!--begin::Status-->
                            @php
                                $statusColors = [
                                    'draft' => 'secondary',
                                    'published' => 'primary',
                                    'ongoing' => 'success',
                                    'completed' => 'info',
                                    'cancelled' => 'danger'
                                ];
                            @endphp
                            <div class="mb-9">
                                <div class="badge badge-lg badge-light-{{ $statusColors[$workshop->status] ?? 'secondary' }} d-inline">{{ ucfirst($workshop->status) }}</div>
                            </div>
                            <!--end::Status-->
                        </div>
                        <!--end::Summary-->
                        
                        <!--begin::Details toggle-->
                        <div class="d-flex flex-stack fs-4 py-3">
                            <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_workshop_details" role="button" aria-expanded="false" aria-controls="kt_workshop_details">Details
                            <span class="ms-2 rotate-180">
                                <i class="ki-duotone ki-down fs-3"></i>
                            </span></div>
                        </div>
                        <!--end::Details toggle-->
                        
                        <div class="separator"></div>
                        
                        <!--begin::Details content-->
                        <div id="kt_workshop_details" class="collapse show">
                            <div class="pb-5 fs-6">
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Start Date</div>
                                <div class="text-gray-600">{{ $workshop->start_date->format('M d, Y H:i') }}</div>
                                <!--begin::Details item-->
                                
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">End Date</div>
                                <div class="text-gray-600">{{ $workshop->end_date->format('M d, Y H:i') }}</div>
                                <!--begin::Details item-->
                                
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Location</div>
                                <div class="text-gray-600">{{ $workshop->location }}</div>
                                <!--begin::Details item-->
                                
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Created By</div>
                                <div class="text-gray-600">{{ $workshop->creator->name }}</div>
                                <!--begin::Details item-->
                                
                                @if($workshop->organizers->count() > 0)
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Organizers</div>
                                <div class="text-gray-600">
                                    @foreach($workshop->organizers as $organizer)
                                        <span class="badge badge-light-primary me-1">{{ $organizer->name }}</span>
                                    @endforeach
                                </div>
                                <!--begin::Details item-->
                                @endif
                                
                                @if($workshop->max_participants)
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Max Participants</div>
                                <div class="text-gray-600">{{ $workshop->max_participants }}</div>
                                <!--begin::Details item-->
                                @endif
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
                        <!--begin::Notice-->
                        <div class="d-flex flex-stack">
                            <div class="d-flex">
                                <div class="d-flex flex-column">
                                    <a href="{{ route('participants.index', ['workshop_id' => $workshop->id]) }}" class="btn btn-sm btn-light-primary mb-2">
                                        <i class="ki-duotone ki-people fs-2"></i>View Participants
                                    </a>
                                    <a href="{{ route('ticket-types.index', ['workshop_id' => $workshop->id]) }}" class="btn btn-sm btn-light-info mb-2">
                                        <i class="ki-duotone ki-price-tag fs-2"></i>Manage Tickets
                                    </a>
                                    <a href="{{ route('check-in.dashboard', $workshop) }}" class="btn btn-sm btn-light-success mb-2">
                                        <i class="ki-duotone ki-scan-barcode fs-2"></i>Check-in Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!--end::Notice-->
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
                        <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_workshop_overview">Overview</a>
                    </li>
                    <!--end:::Tab item-->
                    
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_workshop_participants">Participants</a>
                    </li>
                    <!--end:::Tab item-->
                    
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_workshop_tickets">Ticket Types</a>
                    </li>
                    <!--end:::Tab item-->
                </ul>
                <!--end:::Tabs-->
                
                <!--begin:::Tab content-->
                <div class="tab-content" id="myTabContent">
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade show active" id="kt_workshop_overview" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card mb-5 mb-xl-10">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Workshop Statistics</h3>
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
                                                    <span class="text-gray-900 fs-2 fw-bold me-1">{{ $statistics['total_participants'] ?? 0 }}</span>
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
                                                </i>{{ $statistics['checked_in_participants'] ?? 0 }} Checked In
                                            </a>
                                            
                                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                                <i class="ki-duotone ki-dollar fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>${{ number_format($statistics['total_revenue'] ?? 0, 2) }} Revenue
                                            </a>
                                            
                                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary mb-2">
                                                <i class="ki-duotone ki-sms fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>{{ $statistics['paid_participants'] ?? 0 }} Paid
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
                        
                        <!--begin::Card-->
                        <div class="card mb-5 mb-xl-10">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Description</h3>
                                </div>
                            </div>
                            
                            <div class="card-body pt-9 pb-0">
                                @if($workshop->description)
                                    <div class="fs-6 text-gray-800">
                                        {!! $workshop->description !!}
                                    </div>
                                @else
                                    <div class="text-gray-500 fs-6">No description provided.</div>
                                @endif
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end:::Tab pane-->
                    
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_workshop_participants" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card mb-5 mb-xl-10">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Recent Participants</h3>
                                </div>
                                <div class="card-toolbar">
                                    <a href="{{ route('participants.index', ['workshop_id' => $workshop->id]) }}" class="btn btn-sm btn-light-primary">
                                        View All
                                    </a>
                                </div>
                            </div>
                            
                            <div class="card-body pt-9 pb-0">
                                @if($workshop->participants->count() > 0)
                                    @foreach($workshop->participants->take(5) as $participant)
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
                                            @if($participant->is_checked_in)
                                                <span class="badge badge-light-success">Checked In</span>
                                            @else
                                                <span class="badge badge-light-warning">Not Checked In</span>
                                            @endif
                                            @if($participant->is_paid)
                                                <span class="text-success fs-7 mt-1">Paid</span>
                                            @else
                                                <span class="text-danger fs-7 mt-1">Unpaid</span>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-10">
                                        <div class="text-gray-500 fs-6 mb-3">No participants registered yet</div>
                                        <a href="{{ route('participants.create', ['workshop_id' => $workshop->id]) }}" class="btn btn-primary">Add First Participant</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end:::Tab pane-->
                    
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_workshop_tickets" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card mb-5 mb-xl-10">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Ticket Types</h3>
                                </div>
                                <div class="card-toolbar">
                                    <a href="{{ route('ticket-types.create', ['workshop_id' => $workshop->id]) }}" class="btn btn-sm btn-light-primary">
                                        Add Ticket Type
                                    </a>
                                </div>
                            </div>
                            
                            <div class="card-body pt-9 pb-0">
                                @if($workshop->ticketTypes->count() > 0)
                                    @foreach($workshop->ticketTypes as $ticketType)
                                    <div class="d-flex align-items-center mb-7">
                                        <div class="symbol symbol-50px me-5">
                                            <div class="symbol-label fs-2 fw-semibold text-info bg-light-info">
                                                <i class="ki-duotone ki-price-tag fs-2"></i>
                                            </div>
                                        </div>
                                        
                                        <div class="flex-grow-1">
                                            <a href="{{ route('ticket-types.show', $ticketType) }}" class="text-gray-900 fw-bold text-hover-primary fs-6">{{ $ticketType->name }}</a>
                                            <span class="text-muted d-block fw-semibold">${{ number_format($ticketType->price, 2) }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="text-gray-900 fw-bold fs-6">{{ $ticketType->participants->count() }}</span>
                                            <span class="text-muted fs-7">Participants</span>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-10">
                                        <div class="text-gray-500 fs-6 mb-3">No ticket types created yet</div>
                                        <a href="{{ route('ticket-types.create', ['workshop_id' => $workshop->id]) }}" class="btn btn-primary">Create First Ticket Type</a>
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