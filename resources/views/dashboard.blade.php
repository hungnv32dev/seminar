@extends('layouts.app')

@section('title', 'Dashboard')

@section('toolbar')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
    <!-- Title -->
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Workshop Management Dashboard</h1>
    <!-- Breadcrumb -->
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">Dashboard</li>
    </ul>
</div>
<div class="d-flex align-items-center gap-2 gap-lg-3">
    <button id="refreshDashboard" class="btn btn-sm fw-bold btn-light-primary">
        <i class="ki-duotone ki-arrows-circle fs-2">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>Refresh
    </button>
    @can('create workshops')
    <a href="{{ route('workshops.create') }}" class="btn btn-sm fw-bold btn-primary">
        <i class="ki-duotone ki-plus fs-2"></i>New Workshop
    </a>
    @endcan
</div>
@endsection

@section('content')
<!-- Statistics Row -->
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <!-- Total Workshops -->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10" style="background-color: #F1416C;background-image:url('{{ asset('demo1/assets/media/patterns/vector-1.png') }}')">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2" id="totalWorkshops">{{ $data['totalWorkshops'] }}</span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Workshops</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0">
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                        <span class="fw-bolder fs-6 text-white opacity-75">Active</span>
                        <span class="fw-bold fs-6 text-white" id="activeWorkshops">{{ $data['activeWorkshops'] }}</span>
                    </div>
                    <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                        <div class="bg-white rounded h-8px" role="progressbar" id="activeWorkshopsProgress" style="width: {{ $data['totalWorkshops'] > 0 ? ($data['activeWorkshops'] / $data['totalWorkshops']) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Participants -->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10" style="background-color: #7239EA;background-image:url('{{ asset('demo1/assets/media/patterns/vector-2.png') }}')">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2" id="totalParticipants">{{ $data['totalParticipants'] }}</span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Participants</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0">
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                        <span class="fw-bolder fs-6 text-white opacity-75">Checked In</span>
                        <span class="fw-bold fs-6 text-white" id="checkInRate">{{ $data['checkInRate'] }}%</span>
                    </div>
                    <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                        <div class="bg-white rounded h-8px" role="progressbar" id="checkInProgress" style="width: {{ $data['checkInRate'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Revenue -->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10" style="background-color: #17C653;background-image:url('{{ asset('demo1/assets/media/patterns/vector-3.png') }}')">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2" id="totalRevenue">${{ number_format($data['totalRevenue'], 0) }}</span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Revenue</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0">
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                        <span class="fw-bolder fs-6 text-white opacity-75">Payment Rate</span>
                        <span class="fw-bold fs-6 text-white" id="paymentRate">{{ $data['paymentRate'] }}%</span>
                    </div>
                    <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                        <div class="bg-white rounded h-8px" role="progressbar" id="paymentProgress" style="width: {{ $data['paymentRate'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Workshops -->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10" style="background-color: #FFC700;background-image:url('{{ asset('demo1/assets/media/patterns/vector-4.png') }}')">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2" id="upcomingWorkshops">{{ $data['upcomingWorkshops'] }}</span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Upcoming Workshops</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0">
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                        <span class="fw-bolder fs-6 text-white opacity-75">Completed</span>
                        <span class="fw-bold fs-6 text-white" id="completedWorkshops">{{ $data['completedWorkshops'] }}</span>
                    </div>
                    <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                        <div class="bg-white rounded h-8px" role="progressbar" id="completedProgress" style="width: {{ $data['totalWorkshops'] > 0 ? ($data['completedWorkshops'] / $data['totalWorkshops']) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Analytics Row -->
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <!-- Workshop Trends Chart -->
    <div class="col-xl-8">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">Workshop Trends</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">Monthly workshop creation over the last 6 months</span>
                </h3>
                <div class="card-toolbar">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-20px symbol-circle me-3">
                            <div class="symbol-label bg-success"></div>
                        </div>
                        <span class="text-muted fs-7">Workshops Created</span>
                    </div>
                </div>
            </div>
            <div class="card-body pt-6">
                <div id="workshopTrendsChart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
    
    <!-- Workshop Status Distribution -->
    <div class="col-xl-4">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">Workshop Status</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">Distribution by status</span>
                </h3>
            </div>
            <div class="card-body d-flex flex-center pt-6">
                <div class="w-100">
                    <div id="workshopStatusChart" style="height: 250px;"></div>
                    
                    <!-- Status Legend -->
                    <div class="mt-5">
                        @php
                            $statusColors = [
                                'draft' => ['color' => '#FFC700', 'label' => 'Draft'],
                                'published' => ['color' => '#17C653', 'label' => 'Published'], 
                                'ongoing' => ['color' => '#7239EA', 'label' => 'Ongoing'],
                                'completed' => ['color' => '#009EF7', 'label' => 'Completed'],
                                'cancelled' => ['color' => '#F1416C', 'label' => 'Cancelled']
                            ];
                            $total = array_sum($data['workshopsByStatus']);
                        @endphp
                        
                        @if($total > 0)
                            @foreach($data['workshopsByStatus'] as $status => $count)
                                @if($count > 0)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="symbol symbol-20px me-3">
                                        <div class="symbol-label" style="background-color: {{ $statusColors[$status]['color'] ?? '#E4E6EF' }}"></div>
                                    </div>
                                    <span class="fw-semibold text-gray-600 fs-7 me-auto">{{ $statusColors[$status]['label'] ?? ucfirst($status) }}</span>
                                    <span class="fw-bold text-gray-800 fs-7">{{ $count }}</span>
                                </div>
                                @endif
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <div class="text-gray-500 fs-6">No data available</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <!-- Recent Workshops -->
    <div class="col-xl-8">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">Recent Workshops</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">Latest workshop activities</span>
                </h3>
                <div class="card-toolbar">
                    @can('view workshops')
                    <a href="{{ route('workshops.index') }}" class="btn btn-sm btn-light">View All</a>
                    @endcan
                </div>
            </div>
            <div class="card-body pt-6">
                @if($data['recentWorkshops']->count() > 0)
                    @foreach($data['recentWorkshops'] as $workshop)
                    <div class="d-flex flex-stack">
                        <div class="d-flex">
                            <div class="symbol symbol-40px me-4">
                                <div class="symbol-label fs-2 fw-semibold 
                                    @if($workshop->status == 'published') bg-light-success text-success
                                    @elseif($workshop->status == 'ongoing') bg-light-primary text-primary
                                    @elseif($workshop->status == 'completed') bg-light-info text-info
                                    @elseif($workshop->status == 'cancelled') bg-light-danger text-danger
                                    @else bg-light-warning text-warning
                                    @endif">
                                    {{ substr($workshop->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                                @can('view workshops')
                                <a href="{{ route('workshops.show', $workshop) }}" class="fs-6 text-gray-800 text-hover-primary fw-semibold">{{ $workshop->name }}</a>
                                @else
                                <span class="fs-6 text-gray-800 fw-semibold">{{ $workshop->name }}</span>
                                @endcan
                                <div class="fs-7 text-muted fw-semibold">
                                    <span>{{ $workshop->participants->count() }} participants</span>
                                    <span class="bullet bullet-dot mx-2"></span>
                                    <span>{{ $workshop->start_date->format('M d, Y') }}</span>
                                    @if($workshop->participants->where('is_checked_in', true)->count() > 0)
                                        <span class="bullet bullet-dot mx-2"></span>
                                        <span class="text-success">{{ $workshop->participants->where('is_checked_in', true)->count() }} checked in</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end">
                            <span class="badge badge-light-{{ $workshop->status == 'published' ? 'success' : ($workshop->status == 'ongoing' ? 'primary' : ($workshop->status == 'completed' ? 'info' : ($workshop->status == 'cancelled' ? 'danger' : 'warning'))) }}">
                                {{ ucfirst($workshop->status) }}
                            </span>
                            <span class="fs-7 text-muted fw-semibold mt-1">{{ $workshop->location }}</span>
                            @if($workshop->status === 'ongoing' || $workshop->status === 'published')
                                @can('view check-in')
                                <a href="{{ route('check-in.dashboard', $workshop) }}" class="btn btn-sm btn-light-primary mt-2">
                                    <i class="ki-duotone ki-scan-barcode fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                        <span class="path7"></span>
                                        <span class="path8"></span>
                                    </i>Check-In
                                </a>
                                @endcan
                            @endif
                        </div>
                    </div>
                    @if(!$loop->last)
                        <div class="separator separator-dashed my-5"></div>
                    @endif
                    @endforeach
                @else
                    <div class="text-center py-10">
                        <i class="ki-duotone ki-abstract-44 fs-3x text-muted mb-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="text-gray-500 fs-6 mb-3">No workshops found</div>
                        @can('create workshops')
                        <a href="{{ route('workshops.create') }}" class="btn btn-primary">Create Workshop</a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Today's Activity -->
    <div class="col-xl-4">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">Today's Activity</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ now()->format('l, F j, Y') }}</span>
                </h3>
            </div>
            <div class="card-body d-flex flex-column pt-6">
                <!-- Today's Stats -->
                <div class="d-flex align-items-center mb-5">
                    <div class="symbol symbol-40px me-4">
                        <div class="symbol-label bg-light-primary text-primary">
                            <i class="ki-duotone ki-abstract-44 fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-gray-900 fw-bold fs-6">Workshops Created</div>
                        <div class="text-muted fw-semibold fs-7">Today</div>
                    </div>
                    <span class="badge badge-light-primary fs-8" id="todayWorkshops">{{ $data['todayWorkshops'] }}</span>
                </div>
                
                <div class="d-flex align-items-center mb-5">
                    <div class="symbol symbol-40px me-4">
                        <div class="symbol-label bg-light-success text-success">
                            <i class="ki-duotone ki-profile-circle fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-gray-900 fw-bold fs-6">New Participants</div>
                        <div class="text-muted fw-semibold fs-7">Registered today</div>
                    </div>
                    <span class="badge badge-light-success fs-8" id="todayParticipants">{{ $data['todayParticipants'] }}</span>
                </div>
                
                <div class="d-flex align-items-center mb-5">
                    <div class="symbol symbol-40px me-4">
                        <div class="symbol-label bg-light-info text-info">
                            <i class="ki-duotone ki-scan-barcode fs-2">
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
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-gray-900 fw-bold fs-6">Check-Ins</div>
                        <div class="text-muted fw-semibold fs-7">Today's attendance</div>
                    </div>
                    <span class="badge badge-light-info fs-8" id="todayCheckIns">{{ $data['todayCheckIns'] }}</span>
                </div>
                
                <!-- Quick Links -->
                <div class="separator separator-dashed my-5"></div>
                <div class="d-flex flex-column gap-3">
                    @can('view check-in')
                    <a href="{{ route('check-in.index') }}" class="btn btn-sm btn-light-primary">
                        <i class="ki-duotone ki-scan-barcode fs-3 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                            <span class="path6"></span>
                            <span class="path7"></span>
                            <span class="path8"></span>
                        </i>Check-In Dashboard
                    </a>
                    @endcan
                    
                    @can('view participants')
                    <a href="{{ route('participants.index') }}" class="btn btn-sm btn-light-success">
                        <i class="ki-duotone ki-profile-circle fs-3 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>View Participants
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-5 g-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">Quick Actions</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">Common tasks and shortcuts</span>
                </h3>
            </div>
            <div class="card-body pt-6">
                <div class="row g-6 g-xl-9">
                    @can('create workshops')
                    <div class="col-sm-6 col-xl-4">
                        <a href="{{ route('workshops.create') }}" class="card bg-light-primary hoverable card-xl-stretch mb-xl-8">
                            <div class="card-body">
                                <i class="ki-duotone ki-abstract-44 text-primary fs-2x ms-n1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="text-gray-900 fw-bold fs-6 mt-5">Create Workshop</div>
                                <div class="text-gray-500 fw-semibold fs-7">Set up a new workshop</div>
                            </div>
                        </a>
                    </div>
                    @endcan
                    
                    @can('import participants')
                    <div class="col-sm-6 col-xl-4">
                        <a href="{{ route('participants.import') }}" class="card bg-light-success hoverable card-xl-stretch mb-xl-8">
                            <div class="card-body">
                                <i class="ki-duotone ki-file-up text-success fs-2x ms-n1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="text-gray-900 fw-bold fs-6 mt-5">Import Participants</div>
                                <div class="text-gray-500 fw-semibold fs-7">Upload participant data</div>
                            </div>
                        </a>
                    </div>
                    @endcan
                    
                    @can('view check-in')
                    <div class="col-sm-6 col-xl-4">
                        <a href="{{ route('check-in.scanner') }}" class="card bg-light-info hoverable card-xl-stretch mb-xl-8">
                            <div class="card-body">
                                <i class="ki-duotone ki-scan-barcode text-info fs-2x ms-n1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                    <span class="path7"></span>
                                    <span class="path8"></span>
                                </i>
                                <div class="text-gray-900 fw-bold fs-6 mt-5">QR Code Scanner</div>
                                <div class="text-gray-500 fw-semibold fs-7">Scan QR codes for attendance</div>
                            </div>
                        </a>
                    </div>
                    @endcan
                    
                    @can('view workshops')
                    <div class="col-sm-6 col-xl-4">
                        <a href="{{ route('workshops.index') }}" class="card bg-light-warning hoverable card-xl-stretch mb-xl-8">
                            <div class="card-body">
                                <i class="ki-duotone ki-abstract-44 text-warning fs-2x ms-n1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="text-gray-900 fw-bold fs-6 mt-5">Manage Workshops</div>
                                <div class="text-gray-500 fw-semibold fs-7">View and edit workshops</div>
                            </div>
                        </a>
                    </div>
                    @endcan
                    
                    @can('view participants')
                    <div class="col-sm-6 col-xl-4">
                        <a href="{{ route('participants.index') }}" class="card bg-light-secondary hoverable card-xl-stretch mb-xl-8">
                            <div class="card-body">
                                <i class="ki-duotone ki-profile-circle text-secondary fs-2x ms-n1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="text-gray-900 fw-bold fs-6 mt-5">View Participants</div>
                                <div class="text-gray-500 fw-semibold fs-7">Manage participant data</div>
                            </div>
                        </a>
                    </div>
                    @endcan
                    
                    @can('manage users')
                    <div class="col-sm-6 col-xl-4">
                        <a href="{{ route('users.index') }}" class="card bg-light-dark hoverable card-xl-stretch mb-xl-8">
                            <div class="card-body">
                                <i class="ki-duotone ki-people text-dark fs-2x ms-n1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                                <div class="text-gray-900 fw-bold fs-6 mt-5">User Management</div>
                                <div class="text-gray-500 fw-semibold fs-7">Manage users and permissions</div>
                            </div>
                        </a>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scri
pts')
<!-- Chart.js for dashboard charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let workshopTrendsChart;
let workshopStatusChart;
let refreshInterval;

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    setupRefreshFunctionality();
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function initializeCharts() {
    initializeWorkshopTrendsChart();
    initializeWorkshopStatusChart();
}

function initializeWorkshopTrendsChart() {
    const ctx = document.getElementById('workshopTrendsChart');
    if (!ctx) return;
    
    // Prepare data from Laravel
    const monthlyData = @json($data['monthlyWorkshops']);
    const labels = [];
    const data = [];
    
    // Generate last 6 months labels
    for (let i = 5; i >= 0; i--) {
        const date = new Date();
        date.setMonth(date.getMonth() - i);
        const monthYear = date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        labels.push(monthYear);
        
        // Find matching data or default to 0
        const monthData = monthlyData.find(item => {
            const itemDate = new Date(item.year, item.month - 1);
            return itemDate.getMonth() === date.getMonth() && itemDate.getFullYear() === date.getFullYear();
        });
        data.push(monthData ? monthData.count : 0);
    }
    
    workshopTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Workshops Created',
                data: data,
                borderColor: '#17C653',
                backgroundColor: 'rgba(23, 198, 83, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#17C653',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#17C653',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#A1A5B7',
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(161, 165, 183, 0.3)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#A1A5B7',
                        font: {
                            size: 12
                        },
                        stepSize: 1
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

function initializeWorkshopStatusChart() {
    const ctx = document.getElementById('workshopStatusChart');
    if (!ctx) return;
    
    const statusData = @json($data['workshopsByStatus']);
    const statusColors = {
        'draft': '#FFC700',
        'published': '#17C653',
        'ongoing': '#7239EA',
        'completed': '#009EF7',
        'cancelled': '#F1416C'
    };
    
    const labels = [];
    const data = [];
    const colors = [];
    
    Object.entries(statusData).forEach(([status, count]) => {
        if (count > 0) {
            labels.push(status.charAt(0).toUpperCase() + status.slice(1));
            data.push(count);
            colors.push(statusColors[status] || '#E4E6EF');
        }
    });
    
    if (data.length === 0) {
        document.getElementById('workshopStatusChart').innerHTML = '<div class="text-center py-10"><div class="text-gray-500 fs-6">No data available</div></div>';
        return;
    }
    
    workshopStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#ffffff',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

function setupRefreshFunctionality() {
    const refreshBtn = document.getElementById('refreshDashboard');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            refreshDashboardData();
        });
    }
    
    // Auto-refresh every 5 minutes
    refreshInterval = setInterval(function() {
        refreshDashboardData();
    }, 300000);
}

function refreshDashboardData() {
    const refreshBtn = document.getElementById('refreshDashboard');
    const originalText = refreshBtn.innerHTML;
    
    // Show loading state
    refreshBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Refreshing...';
    refreshBtn.disabled = true;
    
    // Simulate API call (you can replace this with actual AJAX call)
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateDashboardStats(data);
        showNotification('Dashboard refreshed successfully', 'success');
    })
    .catch(error => {
        console.error('Refresh error:', error);
        showNotification('Failed to refresh dashboard', 'error');
    })
    .finally(() => {
        // Restore button state
        refreshBtn.innerHTML = originalText;
        refreshBtn.disabled = false;
    });
}

function updateDashboardStats(data) {
    // Update statistics cards
    document.getElementById('totalWorkshops').textContent = data.totalWorkshops;
    document.getElementById('activeWorkshops').textContent = data.activeWorkshops;
    document.getElementById('totalParticipants').textContent = data.totalParticipants;
    document.getElementById('checkInRate').textContent = data.checkInRate + '%';
    document.getElementById('totalRevenue').textContent = '$' + new Intl.NumberFormat().format(data.totalRevenue);
    document.getElementById('paymentRate').textContent = data.paymentRate + '%';
    document.getElementById('upcomingWorkshops').textContent = data.upcomingWorkshops;
    document.getElementById('completedWorkshops').textContent = data.completedWorkshops;
    
    // Update progress bars
    const activeProgress = data.totalWorkshops > 0 ? (data.activeWorkshops / data.totalWorkshops) * 100 : 0;
    const completedProgress = data.totalWorkshops > 0 ? (data.completedWorkshops / data.totalWorkshops) * 100 : 0;
    
    document.getElementById('activeWorkshopsProgress').style.width = activeProgress + '%';
    document.getElementById('checkInProgress').style.width = data.checkInRate + '%';
    document.getElementById('paymentProgress').style.width = data.paymentRate + '%';
    document.getElementById('completedProgress').style.width = completedProgress + '%';
    
    // Update charts if needed
    if (data.monthlyWorkshops && workshopTrendsChart) {
        updateWorkshopTrendsChart(data.monthlyWorkshops);
    }
    
    if (data.workshopsByStatus && workshopStatusChart) {
        updateWorkshopStatusChart(data.workshopsByStatus);
    }
}

function updateWorkshopTrendsChart(monthlyData) {
    const data = [];
    
    // Generate last 6 months data
    for (let i = 5; i >= 0; i--) {
        const date = new Date();
        date.setMonth(date.getMonth() - i);
        
        const monthData = monthlyData.find(item => {
            const itemDate = new Date(item.year, item.month - 1);
            return itemDate.getMonth() === date.getMonth() && itemDate.getFullYear() === date.getFullYear();
        });
        data.push(monthData ? monthData.count : 0);
    }
    
    workshopTrendsChart.data.datasets[0].data = data;
    workshopTrendsChart.update('none');
}

function updateWorkshopStatusChart(statusData) {
    const data = [];
    const labels = [];
    
    Object.entries(statusData).forEach(([status, count]) => {
        if (count > 0) {
            labels.push(status.charAt(0).toUpperCase() + status.slice(1));
            data.push(count);
        }
    });
    
    workshopStatusChart.data.labels = labels;
    workshopStatusChart.data.datasets[0].data = data;
    workshopStatusChart.update('none');
}

function showNotification(message, type) {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});

// Add some interactive features
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to statistics cards
    const statCards = document.querySelectorAll('.card-flush');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Add click handlers for quick actions
    const quickActionCards = document.querySelectorAll('.card.hoverable');
    quickActionCards.forEach(card => {
        card.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            if (href && href !== '#') {
                window.location.href = href;
            }
        });
    });
});
</script>
@endpush