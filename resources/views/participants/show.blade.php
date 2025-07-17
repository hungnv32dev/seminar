@extends('layouts.app')

@section('title', $participant->name)

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ $participant->name }}</h1>
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
                <li class="breadcrumb-item text-muted">{{ $participant->name }}</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @can('edit participants')
            <a href="{{ route('participants.edit', $participant) }}" class="btn btn-sm fw-bold btn-primary">
                <i class="ki-duotone ki-pencil fs-2"></i>Edit Participant
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
                                    {{ strtoupper(substr($participant->name, 0, 2)) }}
                                </div>
                            </div>
                            <!--end::Avatar-->
                            
                            <!--begin::Name-->
                            <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">{{ $participant->name }}</a>
                            <!--end::Name-->
                            
                            <!--begin::Position-->
                            @if($participant->position)
                            <div class="mb-9">
                                <div class="badge badge-lg badge-light-info d-inline">{{ $participant->position }}</div>
                            </div>
                            @endif
                            <!--end::Position-->
                        </div>
                        <!--end::Summary-->
                        
                        <!--begin::Details toggle-->
                        <div class="d-flex flex-stack fs-4 py-3">
                            <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_participant_details" role="button" aria-expanded="false" aria-controls="kt_participant_details">Details
                            <span class="ms-2 rotate-180">
                                <i class="ki-duotone ki-down fs-3"></i>
                            </span></div>
                        </div>
                        <!--end::Details toggle-->
                        
                        <div class="separator"></div>
                        
                        <!--begin::Details content-->
                        <div id="kt_participant_details" class="collapse show">
                            <div class="pb-5 fs-6">
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Email</div>
                                <div class="text-gray-600">{{ $participant->email }}</div>
                                <!--begin::Details item-->
                                
                                @if($participant->phone)
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Phone</div>
                                <div class="text-gray-600">{{ $participant->phone }}</div>
                                <!--begin::Details item-->
                                @endif
                                
                                @if($participant->company)
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Company</div>
                                <div class="text-gray-600">{{ $participant->company }}</div>
                                <!--begin::Details item-->
                                @endif
                                
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Workshop</div>
                                <div class="text-gray-600">
                                    <a href="{{ route('workshops.show', $participant->workshop) }}" class="text-primary">{{ $participant->workshop->name }}</a>
                                </div>
                                <!--begin::Details item-->
                                
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Ticket Type</div>
                                <div class="text-gray-600">{{ $participant->ticketType->name }} - ${{ number_format($participant->ticketType->price, 2) }}</div>
                                <!--begin::Details item-->
                                
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Registration Date</div>
                                <div class="text-gray-600">{{ $participant->created_at->format('M d, Y H:i') }}</div>
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
                                <form action="{{ route('participants.resend-ticket', $participant) }}" method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light-primary w-100">
                                        <i class="ki-duotone ki-sms fs-2"></i>Resend Ticket Email
                                    </button>
                                </form>
                                
                                <form action="{{ route('participants.toggle-payment', $participant) }}" method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light-{{ $participant->is_paid ? 'danger' : 'success' }} w-100">
                                        <i class="ki-duotone ki-dollar fs-2"></i>Mark as {{ $participant->is_paid ? 'Unpaid' : 'Paid' }}
                                    </button>
                                </form>
                                
                                @if(!$participant->is_checked_in)
                                <form action="{{ route('check-in.participant', $participant) }}" method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light-success w-100">
                                        <i class="ki-duotone ki-check fs-2"></i>Check In Now
                                    </button>
                                </form>
                                @endif
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
                        <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_participant_overview">Overview</a>
                    </li>
                    <!--end:::Tab item-->
                    
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_participant_ticket">Ticket Information</a>
                    </li>
                    <!--end:::Tab item-->
                </ul>
                <!--end:::Tabs-->
                
                <!--begin:::Tab content-->
                <div class="tab-content" id="myTabContent">
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade show active" id="kt_participant_overview" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card mb-5 mb-xl-10">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Status Overview</h3>
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
                                                    <span class="text-gray-900 fs-2 fw-bold me-1">
                                                        @if($participant->is_paid)
                                                            <span class="badge badge-light-success fs-base">PAID</span>
                                                        @else
                                                            <span class="badge badge-light-danger fs-base">UNPAID</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <!--end::User-->
                                        </div>
                                        <!--end::Stats-->
                                        
                                        <!--begin::Info-->
                                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                            <div class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                                <i class="ki-duotone ki-profile-circle fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                @if($participant->is_checked_in)
                                                    <span class="text-success">Checked In</span>
                                                    <span class="text-muted ms-1">({{ $participant->checked_in_at->format('M d, H:i') }})</span>
                                                @else
                                                    <span class="text-warning">Not Checked In</span>
                                                @endif
                                            </div>
                                            
                                            <div class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                                <i class="ki-duotone ki-dollar fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>${{ number_format($participant->ticketType->price, 2) }} Ticket Price
                                            </div>
                                            
                                            <div class="d-flex align-items-center text-gray-500 text-hover-primary mb-2">
                                                <i class="ki-duotone ki-calendar fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>{{ $participant->workshop->start_date->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                        <!--end::Info-->
                                    </div>
                                    <!--end::Stats-->
                                </div>
                                <!--end::Details-->
                            </div>
                        </div>
                        <!--end::Card-->
                        
                        @if($participant->special_requirements)
                        <!--begin::Card-->
                        <div class="card mb-5 mb-xl-10">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Special Requirements</h3>
                                </div>
                            </div>
                            
                            <div class="card-body pt-9 pb-0">
                                <div class="fs-6 text-gray-800">
                                    {{ $participant->special_requirements }}
                                </div>
                            </div>
                        </div>
                        <!--end::Card-->
                        @endif
                        
                        <!--begin::Card-->
                        <div class="card mb-5 mb-xl-10">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Workshop Information</h3>
                                </div>
                            </div>
                            
                            <div class="card-body pt-9 pb-0">
                                <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-md-100">
                                            <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                                <div class="symbol symbol-65px symbol-circle mb-5">
                                                    <div class="symbol-label fs-2 fw-semibold text-primary bg-light-primary">
                                                        <i class="ki-duotone ki-geolocation fs-2"></i>
                                                    </div>
                                                </div>
                                                <a href="#" class="fs-4 fw-bold text-gray-800 text-hover-primary mb-0">{{ $participant->workshop->location }}</a>
                                                <div class="fw-semibold text-gray-500">Location</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-md-100">
                                            <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                                <div class="symbol symbol-65px symbol-circle mb-5">
                                                    <div class="symbol-label fs-2 fw-semibold text-success bg-light-success">
                                                        <i class="ki-duotone ki-calendar fs-2"></i>
                                                    </div>
                                                </div>
                                                <a href="#" class="fs-4 fw-bold text-gray-800 text-hover-primary mb-0">{{ $participant->workshop->start_date->format('M d') }}</a>
                                                <div class="fw-semibold text-gray-500">Start Date</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-md-100">
                                            <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                                <div class="symbol symbol-65px symbol-circle mb-5">
                                                    <div class="symbol-label fs-2 fw-semibold text-info bg-light-info">
                                                        <i class="ki-duotone ki-time fs-2"></i>
                                                    </div>
                                                </div>
                                                <a href="#" class="fs-4 fw-bold text-gray-800 text-hover-primary mb-0">{{ $participant->workshop->start_date->format('H:i') }}</a>
                                                <div class="fw-semibold text-gray-500">Start Time</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-md-100">
                                            <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                                <div class="symbol symbol-65px symbol-circle mb-5">
                                                    @php
                                                        $statusColors = [
                                                            'draft' => 'secondary',
                                                            'published' => 'primary',
                                                            'ongoing' => 'success',
                                                            'completed' => 'info',
                                                            'cancelled' => 'danger'
                                                        ];
                                                    @endphp
                                                    <div class="symbol-label fs-2 fw-semibold text-{{ $statusColors[$participant->workshop->status] ?? 'secondary' }} bg-light-{{ $statusColors[$participant->workshop->status] ?? 'secondary' }}">
                                                        <i class="ki-duotone ki-flag fs-2"></i>
                                                    </div>
                                                </div>
                                                <a href="#" class="fs-4 fw-bold text-gray-800 text-hover-primary mb-0">{{ ucfirst($participant->workshop->status) }}</a>
                                                <div class="fw-semibold text-gray-500">Status</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end:::Tab pane-->
                    
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_participant_ticket" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card mb-5 mb-xl-10">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Ticket Details</h3>
                                </div>
                            </div>
                            
                            <div class="card-body pt-9 pb-0">
                                <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                                    <div class="col-md-6">
                                        <div class="card border border-2 border-dashed border-primary">
                                            <div class="card-body text-center py-12">
                                                <div class="mb-5">
                                                    <i class="ki-duotone ki-barcode fs-3x text-primary">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                        <span class="path5"></span>
                                                        <span class="path6"></span>
                                                        <span class="path7"></span>
                                                        <span class="path8"></span>
                                                    </i>
                                                </div>
                                                <h3 class="fs-2 text-gray-800 fw-bold mb-3">{{ $participant->ticket_code }}</h3>
                                                <div class="fs-6 fw-semibold text-gray-500 mb-6">Ticket Code</div>
                                                <button class="btn btn-primary" onclick="copyToClipboard('{{ $participant->ticket_code }}')">
                                                    <i class="ki-duotone ki-copy fs-2"></i>Copy Code
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column h-100">
                                            <div class="mb-7">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="fs-6 fw-bold text-gray-800 me-2">Ticket Type:</span>
                                                    <span class="badge badge-light-primary">{{ $participant->ticketType->name }}</span>
                                                </div>
                                                <div class="fs-7 text-gray-500">{{ $participant->ticketType->name }}</div>
                                            </div>
                                            
                                            <div class="mb-7">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="fs-6 fw-bold text-gray-800 me-2">Price:</span>
                                                    <span class="fs-6 text-gray-800">${{ number_format($participant->ticketType->price, 2) }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-7">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="fs-6 fw-bold text-gray-800 me-2">Payment Status:</span>
                                                    @if($participant->is_paid)
                                                        <span class="badge badge-light-success">Paid</span>
                                                    @else
                                                        <span class="badge badge-light-danger">Unpaid</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="mb-7">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="fs-6 fw-bold text-gray-800 me-2">Check-in Status:</span>
                                                    @if($participant->is_checked_in)
                                                        <span class="badge badge-light-success">Checked In</span>
                                                    @else
                                                        <span class="badge badge-light-warning">Not Checked In</span>
                                                    @endif
                                                </div>
                                                @if($participant->is_checked_in)
                                                <div class="fs-7 text-gray-500">{{ $participant->checked_in_at->format('M d, Y H:i') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

@push('scripts')
<script>
// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        toastr.success('Ticket code copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endpush
@endsection