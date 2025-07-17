@extends('layouts.app')

@section('title', 'Check-In Management')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Check-In Management</h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Check-In</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @can('check-in participants')
            <a href="{{ route('check-in.scanner', request()->only('workshop_id')) }}" class="btn btn-sm fw-bold btn-primary">
                <i class="ki-duotone ki-scan-barcode fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                    <span class="path5"></span>
                    <span class="path6"></span>
                    <span class="path7"></span>
                    <span class="path8"></span>
                </i>QR Scanner
            </a>
            <a href="{{ route('check-in.manual', request()->only('workshop_id')) }}" class="btn btn-sm fw-bold btn-light-primary">
                <i class="ki-duotone ki-magnifier fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>Manual Check-In
            </a>
            @endcan
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <!-- Workshop Selection -->
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Workshop Selection</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Select a workshop to manage check-ins</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <form method="GET" action="{{ route('check-in.index') }}" class="d-flex align-items-center">
                    <select name="workshop_id" class="form-select form-select-solid me-3" style="max-width: 400px;" onchange="this.form.submit()">
                        <option value="">All Active Workshops</option>
                        @foreach($workshops as $ws)
                            <option value="{{ $ws->id }}" {{ request('workshop_id') == $ws->id ? 'selected' : '' }}>
                                {{ $ws->name }} - {{ $ws->start_date->format('M d, Y') }}
                                <span class="badge badge-{{ $ws->status === 'ongoing' ? 'success' : 'primary' }}">{{ ucfirst($ws->status) }}</span>
                            </option>
                        @endforeach
                    </select>
                    @if(request('workshop_id'))
                        <a href="{{ route('check-in.index') }}" class="btn btn-sm btn-light-danger">
                            <i class="ki-duotone ki-cross fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>Clear Filter
                        </a>
                    @endif
                </form>
            </div>
        </div>

        @if($workshop)
        <!-- Workshop Stats -->
        <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
            <div class="col-xl-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #F1416C;background-image:url('{{ asset('demo1/assets/media/patterns/vector-1.png') }}')">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $workshop->participants->count() }}</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Participants</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                <span>Registered</span>
                                <span>{{ $workshop->participants->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #7239EA;background-image:url('{{ asset('demo1/assets/media/patterns/vector-2.png') }}')">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $workshop->participants->where('is_checked_in', true)->count() }}</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Checked In</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                <span>Attendance Rate</span>
                                <span>{{ $workshop->participants->count() > 0 ? round(($workshop->participants->where('is_checked_in', true)->count() / $workshop->participants->count()) * 100, 1) : 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #17C653;background-image:url('{{ asset('demo1/assets/media/patterns/vector-3.png') }}')">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $workshop->participants->where('is_checked_in', false)->count() }}</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Pending Check-In</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                <span>Remaining</span>
                                <span>{{ $workshop->participants->where('is_checked_in', false)->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #FFC700;background-image:url('{{ asset('demo1/assets/media/patterns/vector-4.png') }}')">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">${{ number_format($workshop->participants->where('is_paid', true)->sum(function($p) { return $p->ticketType->price; }), 2) }}</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Revenue</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                <span>Paid</span>
                                <span>{{ $workshop->participants->where('is_paid', true)->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Check-ins -->
        @if($recentCheckIns->count() > 0)
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Recent Check-Ins</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Latest participants who checked in</span>
                </h3>
                <div class="card-toolbar">
                    <a href="{{ route('check-in.dashboard', $workshop) }}" class="btn btn-sm btn-light-primary">
                        <i class="ki-duotone ki-chart-simple fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>Live Dashboard
                    </a>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-150px">Participant</th>
                                <th class="min-w-140px">Ticket Type</th>
                                <th class="min-w-120px">Check-In Time</th>
                                <th class="min-w-100px text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentCheckIns as $participant)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-45px me-5">
                                            <div class="symbol-label bg-light-success text-success fw-bold">
                                                {{ strtoupper(substr($participant->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <a href="{{ route('participants.show', $participant) }}" class="text-gray-900 fw-bold text-hover-primary fs-6">{{ $participant->name }}</a>
                                            <span class="text-muted fw-semibold text-muted d-block fs-7">{{ $participant->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary fs-7 fw-semibold">{{ $participant->ticketType->name }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-900 fw-bold fs-6">{{ $participant->checked_in_at->format('H:i:s') }}</span>
                                        <span class="text-muted fw-semibold fs-7">{{ $participant->checked_in_at->diffForHumans() }}</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    @can('check-in participants')
                                    <form action="{{ route('check-in.undo', $participant) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-light-warning" onclick="return confirm('Are you sure you want to undo this check-in?')">
                                            <i class="ki-duotone ki-arrow-left fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>Undo
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        @endif

        <!-- Quick Actions -->
        <div class="row g-5 g-xl-8">
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">QR Code Scanner</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Scan participant QR codes for quick check-in</span>
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="text-center py-10">
                            <i class="ki-duotone ki-scan-barcode fs-3x text-primary mb-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                                <span class="path7"></span>
                                <span class="path8"></span>
                            </i>
                            <p class="text-gray-600 fs-6 mb-5">Use your device camera to scan QR codes from participant tickets</p>
                            @can('check-in participants')
                            <a href="{{ route('check-in.scanner', request()->only('workshop_id')) }}" class="btn btn-primary">
                                <i class="ki-duotone ki-scan-barcode fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                    <span class="path7"></span>
                                    <span class="path8"></span>
                                </i>Open Scanner
                            </a>
                            @else
                            <span class="text-muted">You don't have permission to check-in participants</span>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Manual Check-In</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Search and check-in participants manually</span>
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="text-center py-10">
                            <i class="ki-duotone ki-magnifier fs-3x text-success mb-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <p class="text-gray-600 fs-6 mb-5">Search by name, email, or ticket code to manually check-in participants</p>
                            @can('check-in participants')
                            <a href="{{ route('check-in.manual', request()->only('workshop_id')) }}" class="btn btn-success">
                                <i class="ki-duotone ki-magnifier fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>Manual Search
                            </a>
                            @else
                            <span class="text-muted">You don't have permission to check-in participants</span>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!--end::Content-->
@endsection

@push('scripts')
<script>
// Auto-refresh recent check-ins every 30 seconds if workshop is selected
@if($workshop)
setInterval(function() {
    // You can implement AJAX refresh here if needed
}, 30000);
@endif
</script>
@endpush