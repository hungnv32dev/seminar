@extends('layouts.app')

@section('title', 'Dashboard')

@section('toolbar')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
    <!-- Title -->
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Dashboard</h1>
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
@endsection

@section('content')
<!-- Statistics Row -->
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <!-- Total Workshops -->
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10" style="background-color: #F1416C;background-image:url('{{ asset('demo1/assets/media/patterns/vector-1.png') }}')">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $data['totalWorkshops'] }}</span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Workshops</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0">
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                        <span class="fw-bolder fs-6 text-white opacity-75">Active</span>
                        <span class="fw-bold fs-6 text-white">{{ $data['activeWorkshops'] }}</span>
                    </div>
                    <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                        <div class="bg-white rounded h-8px" role="progressbar" style="width: {{ $data['totalWorkshops'] > 0 ? ($data['activeWorkshops'] / $data['totalWorkshops']) * 100 : 0 }}%"></div>
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
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $data['totalParticipants'] }}</span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Participants</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0">
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                        <span class="fw-bolder fs-6 text-white opacity-75">Checked In</span>
                        <span class="fw-bold fs-6 text-white">{{ $data['checkInRate'] }}%</span>
                    </div>
                    <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                        <div class="bg-white rounded h-8px" role="progressbar" style="width: {{ $data['checkInRate'] }}%"></div>
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
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">${{ number_format($data['totalRevenue'], 0) }}</span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Revenue</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0">
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                        <span class="fw-bolder fs-6 text-white opacity-75">Payment Rate</span>
                        <span class="fw-bold fs-6 text-white">{{ $data['paymentRate'] }}%</span>
                    </div>
                    <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                        <div class="bg-white rounded h-8px" role="progressbar" style="width: {{ $data['paymentRate'] }}%"></div>
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
                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $data['upcomingWorkshops'] }}</span>
                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Upcoming Workshops</span>
                </div>
            </div>
            <div class="card-body d-flex align-items-end pt-0">
                <div class="d-flex align-items-center flex-column mt-3 w-100">
                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                        <span class="fw-bolder fs-6 text-white opacity-75">Completed</span>
                        <span class="fw-bold fs-6 text-white">{{ $data['completedWorkshops'] }}</span>
                    </div>
                    <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                        <div class="bg-white rounded h-8px" role="progressbar" style="width: {{ $data['totalWorkshops'] > 0 ? ($data['completedWorkshops'] / $data['totalWorkshops']) * 100 : 0 }}%"></div>
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
                    <a href="#" class="btn btn-sm btn-light">View All</a>
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
                                <a href="#" class="fs-6 text-gray-800 text-hover-primary fw-semibold">{{ $workshop->name }}</a>
                                <div class="fs-7 text-muted fw-semibold">
                                    <span>{{ $workshop->participants->count() }} participants</span>
                                    <span class="bullet bullet-dot mx-2"></span>
                                    <span>{{ $workshop->start_date->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end">
                            <span class="badge badge-light-{{ $workshop->status == 'published' ? 'success' : ($workshop->status == 'ongoing' ? 'primary' : ($workshop->status == 'completed' ? 'info' : ($workshop->status == 'cancelled' ? 'danger' : 'warning'))) }}">
                                {{ ucfirst($workshop->status) }}
                            </span>
                            <span class="fs-7 text-muted fw-semibold mt-1">{{ $workshop->location }}</span>
                        </div>
                    </div>
                    @if(!$loop->last)
                        <div class="separator separator-dashed my-5"></div>
                    @endif
                    @endforeach
                @else
                    <div class="text-center py-10">
                        <div class="text-gray-500 fs-6">No workshops found</div>
                        <a href="#" class="btn btn-primary mt-3">Create Workshop</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Workshop Status Chart -->
    <div class="col-xl-4">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">Workshop Status</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">Distribution by status</span>
                </h3>
            </div>
            <div class="card-body d-flex align-items-center pt-6">
                <div class="w-100">
                    @php
                        $statusColors = [
                            'draft' => 'warning',
                            'published' => 'success', 
                            'ongoing' => 'primary',
                            'completed' => 'info',
                            'cancelled' => 'danger'
                        ];
                        $total = array_sum($data['workshopsByStatus']);
                    @endphp
                    
                    @if($total > 0)
                        @foreach($data['workshopsByStatus'] as $status => $count)
                            @php $percentage = ($count / $total) * 100; @endphp
                            <div class="d-flex flex-stack mb-3">
                                <div class="me-5">
                                    <div class="d-flex align-items-center me-2">
                                        <div class="symbol symbol-20px me-3">
                                            <div class="symbol-label bg-light-{{ $statusColors[$status] ?? 'secondary' }}">
                                                <i class="ki-duotone ki-abstract-26 fs-6 text-{{ $statusColors[$status] ?? 'secondary' }}">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </div>
                                        </div>
                                        <span class="fw-bold text-gray-600 fs-7">{{ ucfirst($status) }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-gray-800 fs-7">{{ $count }}</span>
                                    <div class="w-50px h-6px bg-light-{{ $statusColors[$status] ?? 'secondary' }} rounded ms-3">
                                        <div class="bg-{{ $statusColors[$status] ?? 'secondary' }} rounded h-6px" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-10">
                            <div class="text-gray-500 fs-6">No data available</div>
                        </div>
                    @endif
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
                        <a href="#" class="card bg-light-primary hoverable card-xl-stretch mb-xl-8">
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
                        <a href="#" class="card bg-light-success hoverable card-xl-stretch mb-xl-8">
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
                        <a href="#" class="card bg-light-info hoverable card-xl-stretch mb-xl-8">
                            <div class="card-body">
                                <i class="ki-duotone ki-scan-barcode text-info fs-2x ms-n1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                </i>
                                <div class="text-gray-900 fw-bold fs-6 mt-5">Check-in Participants</div>
                                <div class="text-gray-500 fw-semibold fs-7">Scan QR codes for attendance</div>
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