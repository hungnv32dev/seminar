@extends('layouts.app')

@section('title', 'Check-In Dashboard - ' . $workshop->name)

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Check-In Dashboard</h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('check-in.index') }}" class="text-muted text-hover-primary">Check-In</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">{{ $workshop->name }}</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <button id="refreshDashboard" class="btn btn-sm fw-bold btn-light-primary">
                <i class="ki-duotone ki-arrows-circle fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>Refresh
            </button>
            <a href="{{ route('check-in.scanner', ['workshop_id' => $workshop->id]) }}" class="btn btn-sm fw-bold btn-primary">
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
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <!-- Workshop Info -->
        <div class="card mb-5 mb-xl-8">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-60px me-5">
                        <div class="symbol-label bg-light-primary text-primary fw-bold fs-2">
                            {{ strtoupper(substr($workshop->name, 0, 2)) }}
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h2 class="text-gray-900 fw-bold fs-2 mb-2">{{ $workshop->name }}</h2>
                                <div class="d-flex align-items-center text-gray-600 fs-6">
                                    <i class="ki-duotone ki-calendar fs-2 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ $workshop->start_date->format('l, F j, Y \a\t g:i A') }}
                                    <span class="mx-3">•</span>
                                    <i class="ki-duotone ki-geolocation fs-2 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ $workshop->location }}
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge badge-light-{{ $workshop->status === 'ongoing' ? 'success' : ($workshop->status === 'published' ? 'primary' : 'secondary') }} fs-7 fw-semibold">
                                    {{ ucfirst($workshop->status) }}
                                </span>
                                <div class="text-muted fs-7 mt-1">
                                    Last updated: <span id="lastUpdated">{{ now()->format('H:i:s') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
            <div class="col-xl-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #F1416C;background-image:url('{{ asset('demo1/assets/media/patterns/vector-1.png') }}')">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span id="totalParticipants" class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $stats['total_participants'] }}</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Registered</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                <span>Capacity</span>
                                <span>{{ $stats['total_participants'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #17C653;background-image:url('{{ asset('demo1/assets/media/patterns/vector-2.png') }}')">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span id="checkedInCount" class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $stats['checked_in'] }}</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Checked In</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                <span>Attendance</span>
                                <span id="attendanceRate">{{ $stats['attendance_rate'] }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #FFC700;background-image:url('{{ asset('demo1/assets/media/patterns/vector-3.png') }}')">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span id="pendingCount" class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $stats['pending'] }}</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Pending</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                <span>Remaining</span>
                                <span id="remainingCount">{{ $stats['pending'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #7239EA;background-image:url('{{ asset('demo1/assets/media/patterns/vector-4.png') }}')">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span id="revenueAmount" class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">${{ number_format($stats['total_revenue'], 0) }}</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Revenue</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                <span>Paid</span>
                                <span id="paidCount">{{ $stats['paid_participants'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5 g-xl-8">
            <!-- Recent Check-ins -->
            <div class="col-xl-8">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Live Check-Ins</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Real-time participant check-ins</span>
                        </h3>
                        <div class="card-toolbar">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-20px symbol-circle me-3">
                                    <div id="liveIndicator" class="symbol-label bg-success"></div>
                                </div>
                                <span class="text-muted fs-7">Auto-refresh every 10s</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-3">
                        <div id="recentCheckInsContainer">
                            @if($recentCheckIns->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bold text-muted">
                                            <th class="min-w-200px">Participant</th>
                                            <th class="min-w-120px">Ticket Type</th>
                                            <th class="min-w-120px">Check-In Time</th>
                                            <th class="min-w-100px text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentCheckInsTable">
                                        @foreach($recentCheckIns as $participant)
                                        <tr data-participant-id="{{ $participant->id }}">
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
                            @else
                            <div class="text-center py-10">
                                <i class="ki-duotone ki-questionnaire-tablet fs-3x text-muted mb-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <h4 class="fw-bold text-gray-800 mb-3">No Check-Ins Yet</h4>
                                <p class="text-gray-600 fs-6 mb-5">Participants will appear here as they check in</p>
                                <a href="{{ route('check-in.scanner', ['workshop_id' => $workshop->id]) }}" class="btn btn-primary">
                                    <i class="ki-duotone ki-scan-barcode fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                        <span class="path7"></span>
                                        <span class="path8"></span>
                                    </i>Start Checking In
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Stats -->
            <div class="col-xl-4">
                <!-- Progress Chart -->
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Check-In Progress</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Real-time attendance tracking</span>
                        </h3>
                    </div>
                    <div class="card-body d-flex flex-center">
                        <div class="text-center">
                            <div class="d-flex justify-content-center">
                                <div class="position-relative">
                                    <canvas id="progressChart" width="150" height="150"></canvas>
                                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                                        <span id="progressPercentage" class="fs-2hx fw-bold text-gray-800">{{ $stats['attendance_rate'] }}%</span>
                                        <div class="fs-7 text-muted">Checked In</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-5">
                                <div class="d-flex align-items-center me-5">
                                    <div class="symbol symbol-20px symbol-circle me-2">
                                        <div class="symbol-label bg-success"></div>
                                    </div>
                                    <span class="fs-7 text-muted">Checked In</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-20px symbol-circle me-2">
                                        <div class="symbol-label bg-light-muted"></div>
                                    </div>
                                    <span class="fs-7 text-muted">Pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card card-xl-stretch">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Quick Actions</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Common check-in tasks</span>
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="d-flex flex-column gap-3">
                            @can('check-in participants')
                            <a href="{{ route('check-in.scanner', ['workshop_id' => $workshop->id]) }}" class="btn btn-light-primary text-start">
                                <i class="ki-duotone ki-scan-barcode fs-2 me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                    <span class="path7"></span>
                                    <span class="path8"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">QR Scanner</span>
                                    <span class="text-muted fs-7">Scan participant QR codes</span>
                                </div>
                            </a>
                            
                            <a href="{{ route('check-in.manual', ['workshop_id' => $workshop->id]) }}" class="btn btn-light-success text-start">
                                <i class="ki-duotone ki-magnifier fs-2 me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">Manual Check-In</span>
                                    <span class="text-muted fs-7">Search and check-in manually</span>
                                </div>
                            </a>
                            @endcan
                            
                            <a href="{{ route('participants.index', ['workshop_id' => $workshop->id]) }}" class="btn btn-light-info text-start">
                                <i class="ki-duotone ki-people fs-2 me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">Participant List</span>
                                    <span class="text-muted fs-7">View all participants</span>
                                </div>
                            </a>
                            
                            <button id="exportReport" class="btn btn-light-warning text-start">
                                <i class="ki-duotone ki-file-down fs-2 me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">Export Report</span>
                                    <span class="text-muted fs-7">Download check-in report</span>
                                </div>
                            </button>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let refreshInterval;
let progressChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeProgressChart();
    startAutoRefresh();
    
    // Manual refresh button
    document.getElementById('refreshDashboard').addEventListener('click', function() {
        refreshDashboard();
    });
    
    // Export report button
    document.getElementById('exportReport').addEventListener('click', function() {
        exportReport();
    });
});

function initializeProgressChart() {
    const ctx = document.getElementById('progressChart').getContext('2d');
    const checkedIn = {{ $stats['checked_in'] }};
    const total = {{ $stats['total_participants'] }};
    const pending = total - checkedIn;
    
    progressChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [checkedIn, pending],
                backgroundColor: ['#17C653', '#E4E6EF'],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: false
                }
            }
        }
    });
}

function startAutoRefresh() {
    refreshInterval = setInterval(function() {
        refreshDashboard();
    }, 10000); // Refresh every 10 seconds
}

function refreshDashboard() {
    // Show loading indicator
    const indicator = document.getElementById('liveIndicator');
    indicator.classList.remove('bg-success');
    indicator.classList.add('bg-warning');
    
    fetch('{{ route("check-in.workshop-stats", $workshop) }}')
        .then(response => response.json())
        .then(data => {
            updateStatistics(data.stats);
            updateLastUpdated();
            
            // Update live indicator
            indicator.classList.remove('bg-warning');
            indicator.classList.add('bg-success');
        })
        .catch(error => {
            console.error('Refresh error:', error);
            indicator.classList.remove('bg-warning');
            indicator.classList.add('bg-danger');
            
            setTimeout(() => {
                indicator.classList.remove('bg-danger');
                indicator.classList.add('bg-success');
            }, 2000);
        });
    
    // Refresh recent check-ins
    fetch('{{ route("check-in.recent-checkins", $workshop) }}')
        .then(response => response.json())
        .then(data => {
            updateRecentCheckIns(data.recent_check_ins);
        })
        .catch(error => {
            console.error('Recent check-ins refresh error:', error);
        });
}

function updateStatistics(stats) {
    document.getElementById('totalParticipants').textContent = stats.total_participants;
    document.getElementById('checkedInCount').textContent = stats.checked_in;
    document.getElementById('pendingCount').textContent = stats.pending;
    document.getElementById('attendanceRate').textContent = stats.attendance_rate + '%';
    document.getElementById('remainingCount').textContent = stats.pending;
    document.getElementById('revenueAmount').textContent = '$' + new Intl.NumberFormat().format(stats.total_revenue);
    document.getElementById('paidCount').textContent = stats.paid_participants;
    document.getElementById('progressPercentage').textContent = stats.attendance_rate + '%';
    
    // Update progress chart
    if (progressChart) {
        progressChart.data.datasets[0].data = [stats.checked_in, stats.pending];
        progressChart.update('none');
    }
}

function updateRecentCheckIns(checkIns) {
    const container = document.getElementById('recentCheckInsContainer');
    
    if (checkIns.length === 0) {
        container.innerHTML = `
            <div class="text-center py-10">
                <i class="ki-duotone ki-questionnaire-tablet fs-3x text-muted mb-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <h4 class="fw-bold text-gray-800 mb-3">No Check-Ins Yet</h4>
                <p class="text-gray-600 fs-6 mb-5">Participants will appear here as they check in</p>
                <a href="{{ route('check-in.scanner', ['workshop_id' => $workshop->id]) }}" class="btn btn-primary">
                    <i class="ki-duotone ki-scan-barcode fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                        <span class="path6"></span>
                        <span class="path7"></span>
                        <span class="path8"></span>
                    </i>Start Checking In
                </a>
            </div>
        `;
        return;
    }
    
    let tableHtml = `
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted">
                        <th class="min-w-200px">Participant</th>
                        <th class="min-w-120px">Ticket Type</th>
                        <th class="min-w-120px">Check-In Time</th>
                        <th class="min-w-100px text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    checkIns.forEach(participant => {
        tableHtml += `
            <tr data-participant-id="${participant.id}">
                <td>
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-45px me-5">
                            <div class="symbol-label bg-light-success text-success fw-bold">
                                ${participant.name.charAt(0).toUpperCase()}
                            </div>
                        </div>
                        <div class="d-flex justify-content-start flex-column">
                            <span class="text-gray-900 fw-bold fs-6">${participant.name}</span>
                            <span class="text-muted fw-semibold text-muted d-block fs-7">${participant.email}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge badge-light-primary fs-7 fw-semibold">${participant.ticket_type}</span>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <span class="text-gray-900 fw-bold fs-6">${new Date(participant.checked_in_at).toLocaleTimeString()}</span>
                        <span class="text-muted fw-semibold fs-7">${participant.checked_in_at_human}</span>
                    </div>
                </td>
                <td class="text-end">
                    @can('check-in participants')
                    <button class="btn btn-sm btn-light-warning" onclick="undoCheckIn(${participant.id})">
                        <i class="ki-duotone ki-arrow-left fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>Undo
                    </button>
                    @endcan
                </td>
            </tr>
        `;
    });
    
    tableHtml += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = tableHtml;
}

function updateLastUpdated() {
    document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString();
}

function exportReport() {
    fetch('{{ route("check-in.export-report", $workshop) }}')
        .then(response => response.json())
        .then(data => {
            // Create and download CSV
            const csv = generateCSV(data);
            downloadCSV(csv, `${data.workshop.name}_checkin_report_${new Date().toISOString().split('T')[0]}.csv`);
        })
        .catch(error => {
            console.error('Export error:', error);
            alert('Failed to export report. Please try again.');
        });
}

function generateCSV(data) {
    const headers = ['Name', 'Email', 'Ticket Code', 'Ticket Type', 'Checked In', 'Check-In Time'];
    const rows = data.participants.map(p => [
        p.name,
        p.email,
        p.ticket_code,
        p.ticket_type,
        p.is_checked_in,
        p.checked_in_at || ''
    ]);
    
    const csvContent = [headers, ...rows]
        .map(row => row.map(field => `"${field}"`).join(','))
        .join('\n');
    
    return csvContent;
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function undoCheckIn(participantId) {
    if (!confirm('Are you sure you want to undo this check-in?')) {
        return;
    }
    
    // Implementation would require participant route
    alert('Undo check-in functionality would be implemented here');
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>
@endpush