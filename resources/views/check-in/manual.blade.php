@extends('layouts.app')

@section('title', 'Manual Check-In')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Manual Check-In</h1>
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
                <li class="breadcrumb-item text-muted">Manual Check-In</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('check-in.index', request()->only('workshop_id')) }}" class="btn btn-sm fw-bold btn-light">
                <i class="ki-duotone ki-arrow-left fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>Back to Check-In
            </a>
            <a href="{{ route('check-in.scanner', request()->only('workshop_id')) }}" class="btn btn-sm fw-bold btn-light-primary">
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
        
        <!-- Workshop Selection -->
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Workshop Filter</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Select a workshop to filter participants</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <form method="GET" action="{{ route('check-in.manual') }}" class="d-flex align-items-center">
                    <select name="workshop_id" class="form-select form-select-solid me-3" style="max-width: 400px;" onchange="this.form.submit()">
                        <option value="">All Active Workshops</option>
                        @foreach($workshops as $ws)
                            <option value="{{ $ws->id }}" {{ request('workshop_id') == $ws->id ? 'selected' : '' }}>
                                {{ $ws->name }} - {{ $ws->start_date->format('M d, Y') }}
                            </option>
                        @endforeach
                    </select>
                    @if(request('workshop_id'))
                        <a href="{{ route('check-in.manual') }}" class="btn btn-sm btn-light-danger">
                            <i class="ki-duotone ki-cross fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>Clear Filter
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Search Form -->
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Search Participants</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Search by name, email, or ticket code</span>
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('check-in.manual.process') }}" class="d-flex align-items-end gap-3">
                    @csrf
                    @if(request('workshop_id'))
                        <input type="hidden" name="workshop_id" value="{{ request('workshop_id') }}">
                    @endif
                    
                    <div class="flex-grow-1">
                        <label class="form-label fw-semibold">Search Term</label>
                        <input type="text" name="search" class="form-control form-control-solid" 
                               placeholder="Enter name, email, or ticket code..." 
                               value="{{ old('search') }}" 
                               required 
                               minlength="2">
                        @error('search')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="ki-duotone ki-magnifier fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>Search
                    </button>
                </form>

                <!-- Search Instructions -->
                <div class="alert alert-primary d-flex align-items-center p-5 mt-5">
                    <i class="ki-duotone ki-information-5 fs-2hx text-primary me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Search Tips:</h5>
                        <span>• Enter at least 2 characters to search</span>
                        <span>• Search by participant name (e.g., "John Smith")</span>
                        <span>• Search by email address (e.g., "john@example.com")</span>
                        <span>• Search by ticket code (e.g., "WS2024-ABC123")</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        @if(session('search_results'))
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Search Results</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">{{ session('search_results')->count() }} participant(s) found</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-200px">Participant</th>
                                <th class="min-w-150px">Workshop</th>
                                <th class="min-w-120px">Ticket Type</th>
                                <th class="min-w-120px">Ticket Code</th>
                                <th class="min-w-100px">Status</th>
                                <th class="min-w-100px text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('search_results') as $participant)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-45px me-5">
                                            <div class="symbol-label bg-light-{{ $participant->is_checked_in ? 'success' : 'primary' }} text-{{ $participant->is_checked_in ? 'success' : 'primary' }} fw-bold">
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
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-900 fw-bold fs-6">{{ $participant->workshop->name }}</span>
                                        <span class="text-muted fw-semibold fs-7">{{ $participant->workshop->start_date->format('M d, Y H:i') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary fs-7 fw-semibold">{{ $participant->ticketType->name }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-gray-800 font-monospace fs-7">{{ $participant->ticket_code }}</span>
                                </td>
                                <td>
                                    @if($participant->is_checked_in)
                                        <div class="d-flex flex-column">
                                            <span class="badge badge-light-success fs-7 fw-semibold mb-1">Checked In</span>
                                            <span class="text-muted fs-8">{{ $participant->checked_in_at->format('M d, H:i') }}</span>
                                        </div>
                                    @else
                                        <span class="badge badge-light-warning fs-7 fw-semibold">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($participant->is_checked_in)
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
                                    @else
                                        <form action="{{ route('check-in.participant', $participant) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light-success" onclick="return confirm('Check in {{ $participant->name }}?')">
                                                <i class="ki-duotone ki-check fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>Check In
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="row g-5 g-xl-8">
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Quick Search</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Common search patterns</span>
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="d-flex flex-column gap-3">
                            <button class="btn btn-light-primary text-start" onclick="document.querySelector('input[name=search]').value = '@'; document.querySelector('input[name=search]').focus();">
                                <i class="ki-duotone ki-sms fs-2 me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">Search by Email</span>
                                    <span class="text-muted fs-7">Start typing @ to search emails</span>
                                </div>
                            </button>
                            
                            <button class="btn btn-light-success text-start" onclick="document.querySelector('input[name=search]').value = 'WS'; document.querySelector('input[name=search]').focus();">
                                <i class="ki-duotone ki-barcode fs-2 me-3">
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
                                    <span class="fw-bold">Search by Ticket Code</span>
                                    <span class="text-muted fs-7">Ticket codes usually start with WS</span>
                                </div>
                            </button>
                            
                            <button class="btn btn-light-info text-start" onclick="document.querySelector('input[name=search]').focus();">
                                <i class="ki-duotone ki-profile-user fs-2 me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">Search by Name</span>
                                    <span class="text-muted fs-7">Enter first or last name</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Alternative Methods</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Other ways to check-in participants</span>
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="d-flex flex-column gap-3">
                            <a href="{{ route('check-in.scanner', request()->only('workshop_id')) }}" class="btn btn-light-primary text-start">
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
                                    <span class="fw-bold">QR Code Scanner</span>
                                    <span class="text-muted fs-7">Use camera to scan QR codes</span>
                                </div>
                            </a>
                            
                            @if($workshop)
                            <a href="{{ route('participants.index', ['workshop_id' => $workshop->id]) }}" class="btn btn-light-success text-start">
                                <i class="ki-duotone ki-people fs-2 me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">Participant List</span>
                                    <span class="text-muted fs-7">View all workshop participants</span>
                                </div>
                            </a>
                            @endif
                            
                            <a href="{{ route('check-in.index', request()->only('workshop_id')) }}" class="btn btn-light-info text-start">
                                <i class="ki-duotone ki-chart-simple fs-2 me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">Check-In Dashboard</span>
                                    <span class="text-muted fs-7">View check-in statistics</span>
                                </div>
                            </a>
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
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus search input
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
        
        // Escape to clear search
        if (e.key === 'Escape' && document.activeElement === searchInput) {
            searchInput.value = '';
        }
    });

    // Add search suggestions/autocomplete if needed
    searchInput.addEventListener('input', function() {
        // You can implement live search suggestions here
        const value = this.value.toLowerCase();
        
        // Show search hints based on input
        if (value.includes('@')) {
            // Email search hint
        } else if (value.startsWith('ws')) {
            // Ticket code search hint
        }
    });
});
</script>
@endpush