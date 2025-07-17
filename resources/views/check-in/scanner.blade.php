@extends('layouts.app')

@section('title', 'QR Code Scanner')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">QR Code Scanner</h1>
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
                <li class="breadcrumb-item text-muted">QR Scanner</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('check-in.index', request()->only('workshop_id')) }}" class="btn btn-sm fw-bold btn-light">
                <i class="ki-duotone ki-arrow-left fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>Back to Check-In
            </a>
            <a href="{{ route('check-in.manual', request()->only('workshop_id')) }}" class="btn btn-sm fw-bold btn-light-primary">
                <i class="ki-duotone ki-magnifier fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>Manual Check-In
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
                    <span class="text-muted mt-1 fw-semibold fs-7">Select a workshop to filter check-ins</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <form method="GET" action="{{ route('check-in.scanner') }}" class="d-flex align-items-center">
                    <select name="workshop_id" class="form-select form-select-solid me-3" style="max-width: 400px;" onchange="this.form.submit()">
                        <option value="">All Active Workshops</option>
                        @foreach($workshops as $ws)
                            <option value="{{ $ws->id }}" {{ request('workshop_id') == $ws->id ? 'selected' : '' }}>
                                {{ $ws->name }} - {{ $ws->start_date->format('M d, Y') }}
                            </option>
                        @endforeach
                    </select>
                    @if(request('workshop_id'))
                        <a href="{{ route('check-in.scanner') }}" class="btn btn-sm btn-light-danger">
                            <i class="ki-duotone ki-cross fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>Clear Filter
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="row g-5 g-xl-8">
            <!-- QR Scanner -->
            <div class="col-xl-8">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">QR Code Scanner</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Point your camera at a QR code to check-in participants</span>
                        </h3>
                        <div class="card-toolbar">
                            <button id="toggleCamera" class="btn btn-sm btn-light-primary">
                                <i class="ki-duotone ki-camera fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>Start Camera
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Camera Preview -->
                        <div id="cameraContainer" class="text-center mb-5" style="display: none;">
                            <div class="position-relative d-inline-block">
                                <video id="cameraPreview" width="100%" height="400" style="max-width: 500px; border-radius: 10px; background: #000;"></video>
                                <div class="position-absolute top-50 start-50 translate-middle" style="border: 2px solid #fff; width: 200px; height: 200px; border-radius: 10px; pointer-events: none;">
                                    <div class="position-absolute top-0 start-0 bg-primary" style="width: 20px; height: 20px; border-top-left-radius: 8px;"></div>
                                    <div class="position-absolute top-0 end-0 bg-primary" style="width: 20px; height: 20px; border-top-right-radius: 8px;"></div>
                                    <div class="position-absolute bottom-0 start-0 bg-primary" style="width: 20px; height: 20px; border-bottom-left-radius: 8px;"></div>
                                    <div class="position-absolute bottom-0 end-0 bg-primary" style="width: 20px; height: 20px; border-bottom-right-radius: 8px;"></div>
                                </div>
                            </div>
                            <canvas id="qrCanvas" style="display: none;"></canvas>
                        </div>

                        <!-- Camera Instructions -->
                        <div id="cameraInstructions" class="text-center py-10">
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
                            <h4 class="fw-bold text-gray-800 mb-3">Ready to Scan QR Codes</h4>
                            <p class="text-gray-600 fs-6 mb-5">Click "Start Camera" to begin scanning participant QR codes</p>
                            <div class="alert alert-primary d-flex align-items-center p-5">
                                <i class="ki-duotone ki-information-5 fs-2hx text-primary me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <h5 class="mb-1">Instructions:</h5>
                                    <span>1. Allow camera access when prompted</span>
                                    <span>2. Point camera at participant's QR code</span>
                                    <span>3. Wait for automatic detection and check-in</span>
                                </div>
                            </div>
                        </div>

                        <!-- Manual QR Input -->
                        <div class="separator separator-dashed my-8"></div>
                        <div class="text-center">
                            <h5 class="fw-bold text-gray-800 mb-3">Manual QR Code Entry</h5>
                            <p class="text-gray-600 fs-7 mb-5">If camera is not available, enter QR code data manually</p>
                            <form id="manualQrForm" class="d-flex justify-content-center align-items-center gap-3">
                                <input type="text" id="manualQrInput" class="form-control form-control-solid" placeholder="Enter QR code or ticket code" style="max-width: 300px;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-duotone ki-check fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>Check In
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Check-in Results -->
            <div class="col-xl-4">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Check-In Status</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Real-time check-in results</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Status Display -->
                        <div id="checkInStatus" class="text-center py-5">
                            <i class="ki-duotone ki-questionnaire-tablet fs-3x text-muted mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <p class="text-muted fs-6">Waiting for QR code scan...</p>
                        </div>

                        <!-- Participant Info -->
                        <div id="participantInfo" style="display: none;">
                            <div class="d-flex align-items-center mb-5">
                                <div class="symbol symbol-50px me-5">
                                    <div id="participantAvatar" class="symbol-label bg-light-success text-success fw-bold fs-2">
                                        ?
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div id="participantName" class="fw-bold text-gray-900 fs-6 mb-1">-</div>
                                    <div id="participantEmail" class="text-muted fs-7">-</div>
                                </div>
                            </div>
                            
                            <div class="separator separator-dashed my-5"></div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-semibold text-gray-600">Workshop:</span>
                                <span id="participantWorkshop" class="fw-bold text-gray-800">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-semibold text-gray-600">Ticket Type:</span>
                                <span id="participantTicketType" class="badge badge-light-primary">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-semibold text-gray-600">Ticket Code:</span>
                                <span id="participantTicketCode" class="fw-bold text-gray-800 font-monospace">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-5">
                                <span class="fw-semibold text-gray-600">Status:</span>
                                <span id="participantStatus" class="badge">-</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div id="actionButtons" class="text-center" style="display: none;">
                            <button id="continueScanning" class="btn btn-primary btn-sm">
                                <i class="ki-duotone ki-scan-barcode fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                    <span class="path7"></span>
                                    <span class="path8"></span>
                                </i>Continue Scanning
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Scans -->
                <div class="card card-xl-stretch">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Recent Scans</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Last 5 check-ins</span>
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div id="recentScans">
                            <div class="text-center text-muted py-5">
                                <i class="ki-duotone ki-file-deleted fs-2x mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <p class="fs-7">No recent scans</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!--end::Content-->
@endsection

@push('styles')
<style>
#cameraPreview {
    transform: scaleX(-1); /* Mirror the camera preview */
}

.scan-line {
    position: absolute;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #00ff00, transparent);
    animation: scan 2s linear infinite;
}

@keyframes scan {
    0% { top: 0; }
    100% { top: 100%; }
}

.qr-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 200px;
    height: 200px;
    border: 2px solid rgba(255, 255, 255, 0.8);
    border-radius: 10px;
    pointer-events: none;
}

.qr-corner {
    position: absolute;
    width: 20px;
    height: 20px;
    border: 3px solid #007bff;
}

.qr-corner.top-left {
    top: -3px;
    left: -3px;
    border-right: none;
    border-bottom: none;
    border-top-left-radius: 8px;
}

.qr-corner.top-right {
    top: -3px;
    right: -3px;
    border-left: none;
    border-bottom: none;
    border-top-right-radius: 8px;
}

.qr-corner.bottom-left {
    bottom: -3px;
    left: -3px;
    border-right: none;
    border-top: none;
    border-bottom-left-radius: 8px;
}

.qr-corner.bottom-right {
    bottom: -3px;
    right: -3px;
    border-left: none;
    border-top: none;
    border-bottom-right-radius: 8px;
}
</style>
@endpush

@push('scripts')
<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/qr-scanner@1.4.2/qr-scanner.umd.min.js"></script>

<script>
let qrScanner = null;
let isScanning = false;
let recentScans = [];

document.addEventListener('DOMContentLoaded', function() {
    const toggleCameraBtn = document.getElementById('toggleCamera');
    const cameraContainer = document.getElementById('cameraContainer');
    const cameraInstructions = document.getElementById('cameraInstructions');
    const cameraPreview = document.getElementById('cameraPreview');
    const manualQrForm = document.getElementById('manualQrForm');
    const manualQrInput = document.getElementById('manualQrInput');

    // Toggle camera
    toggleCameraBtn.addEventListener('click', function() {
        if (isScanning) {
            stopCamera();
        } else {
            startCamera();
        }
    });

    // Manual QR form
    manualQrForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const qrData = manualQrInput.value.trim();
        if (qrData) {
            processQrCode(qrData);
            manualQrInput.value = '';
        }
    });

    // Continue scanning button
    document.getElementById('continueScanning').addEventListener('click', function() {
        resetStatus();
        if (qrScanner) {
            qrScanner.start();
        }
    });

    function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showAlert('Camera not supported on this device', 'error');
            return;
        }

        QrScanner.hasCamera().then(hasCamera => {
            if (!hasCamera) {
                showAlert('No camera found on this device', 'error');
                return;
            }

            cameraInstructions.style.display = 'none';
            cameraContainer.style.display = 'block';
            
            qrScanner = new QrScanner(
                cameraPreview,
                result => {
                    qrScanner.stop();
                    processQrCode(result.data);
                },
                {
                    returnDetailedScanResult: true,
                    highlightScanRegion: true,
                    highlightCodeOutline: true,
                }
            );

            qrScanner.start().then(() => {
                isScanning = true;
                toggleCameraBtn.innerHTML = '<i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>Stop Camera';
                toggleCameraBtn.classList.remove('btn-light-primary');
                toggleCameraBtn.classList.add('btn-light-danger');
            }).catch(err => {
                console.error('Camera start error:', err);
                showAlert('Failed to start camera: ' + err.message, 'error');
                stopCamera();
            });
        });
    }

    function stopCamera() {
        if (qrScanner) {
            qrScanner.stop();
            qrScanner.destroy();
            qrScanner = null;
        }
        
        isScanning = false;
        cameraContainer.style.display = 'none';
        cameraInstructions.style.display = 'block';
        
        toggleCameraBtn.innerHTML = '<i class="ki-duotone ki-camera fs-2"><span class="path1"></span><span class="path2"></span></i>Start Camera';
        toggleCameraBtn.classList.remove('btn-light-danger');
        toggleCameraBtn.classList.add('btn-light-primary');
        
        resetStatus();
    }

    function processQrCode(qrData) {
        showLoading();
        
        const workshopId = {{ request('workshop_id') ?: 'null' }};
        
        fetch('{{ route("check-in.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                qr_data: qrData,
                workshop_id: workshopId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showParticipantInfo(data.participant, data.already_checked_in);
                addToRecentScans(data.participant, data.already_checked_in);
                
                if (data.already_checked_in) {
                    showAlert(data.message, 'warning');
                } else {
                    showAlert(data.message, 'success');
                }
            } else {
                showAlert(data.message, 'error');
                if (data.participant) {
                    showParticipantInfo(data.participant, false, true);
                }
            }
        })
        .catch(error => {
            console.error('Check-in error:', error);
            showAlert('Network error occurred. Please try again.', 'error');
        })
        .finally(() => {
            hideLoading();
        });
    }

    function showParticipantInfo(participant, alreadyCheckedIn = false, hasError = false) {
        document.getElementById('checkInStatus').style.display = 'none';
        document.getElementById('participantInfo').style.display = 'block';
        document.getElementById('actionButtons').style.display = 'block';

        // Update participant info
        document.getElementById('participantAvatar').textContent = participant.name.charAt(0).toUpperCase();
        document.getElementById('participantName').textContent = participant.name;
        document.getElementById('participantEmail').textContent = participant.email;
        document.getElementById('participantWorkshop').textContent = participant.workshop ? participant.workshop.name : 'N/A';
        document.getElementById('participantTicketType').textContent = participant.ticket_type ? participant.ticket_type.name : 'N/A';
        document.getElementById('participantTicketCode').textContent = participant.ticket_code;

        // Update status badge
        const statusElement = document.getElementById('participantStatus');
        if (hasError) {
            statusElement.textContent = 'Error';
            statusElement.className = 'badge badge-light-danger';
        } else if (alreadyCheckedIn) {
            statusElement.textContent = 'Already Checked In';
            statusElement.className = 'badge badge-light-warning';
        } else {
            statusElement.textContent = 'Checked In Successfully';
            statusElement.className = 'badge badge-light-success';
        }

        // Update avatar color
        const avatarElement = document.getElementById('participantAvatar');
        if (hasError) {
            avatarElement.className = 'symbol-label bg-light-danger text-danger fw-bold fs-2';
        } else if (alreadyCheckedIn) {
            avatarElement.className = 'symbol-label bg-light-warning text-warning fw-bold fs-2';
        } else {
            avatarElement.className = 'symbol-label bg-light-success text-success fw-bold fs-2';
        }
    }

    function addToRecentScans(participant, alreadyCheckedIn) {
        const scan = {
            participant: participant,
            timestamp: new Date(),
            alreadyCheckedIn: alreadyCheckedIn
        };
        
        recentScans.unshift(scan);
        if (recentScans.length > 5) {
            recentScans.pop();
        }
        
        updateRecentScansDisplay();
    }

    function updateRecentScansDisplay() {
        const container = document.getElementById('recentScans');
        
        if (recentScans.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="ki-duotone ki-file-deleted fs-2x mb-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <p class="fs-7">No recent scans</p>
                </div>
            `;
            return;
        }

        let html = '';
        recentScans.forEach(scan => {
            const statusClass = scan.alreadyCheckedIn ? 'warning' : 'success';
            const statusText = scan.alreadyCheckedIn ? 'Already In' : 'Checked In';
            
            html += `
                <div class="d-flex align-items-center mb-3">
                    <div class="symbol symbol-35px me-3">
                        <div class="symbol-label bg-light-${statusClass} text-${statusClass} fw-bold fs-7">
                            ${scan.participant.name.charAt(0).toUpperCase()}
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold text-gray-800 fs-7">${scan.participant.name}</div>
                        <div class="text-muted fs-8">${scan.timestamp.toLocaleTimeString()}</div>
                    </div>
                    <span class="badge badge-light-${statusClass} fs-8">${statusText}</span>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }

    function resetStatus() {
        document.getElementById('checkInStatus').style.display = 'block';
        document.getElementById('participantInfo').style.display = 'none';
        document.getElementById('actionButtons').style.display = 'none';
    }

    function showLoading() {
        document.getElementById('checkInStatus').innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted fs-6 mt-3">Processing check-in...</p>
        `;
    }

    function hideLoading() {
        // Loading will be replaced by participant info or reset
    }

    function showAlert(message, type) {
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
        if (qrScanner) {
            qrScanner.destroy();
        }
    });
});
</script>
@endpush