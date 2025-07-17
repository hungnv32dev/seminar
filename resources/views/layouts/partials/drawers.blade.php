{{-- Drawers --}}
<!-- Activities drawer -->
<div id="kt_activities" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'300px', 'lg': '900px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activities_toggle" data-kt-drawer-close="#kt_activities_close">
    <div class="card shadow-none border-0 rounded-0">
        <!-- Header -->
        <div class="card-header" id="kt_activities_header">
            <h3 class="card-title fw-bold text-gray-900">Activity Logs</h3>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activities_close">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </button>
            </div>
        </div>
        
        <!-- Body -->
        <div class="card-body position-relative" id="kt_activities_body">
            <div id="kt_activities_scroll" class="position-relative scroll-y me-n5 pe-5" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
                
                <!-- Timeline -->
                <div class="timeline timeline-border-dashed">
                    <div class="timeline-item">
                        <div class="timeline-line"></div>
                        <div class="timeline-icon">
                            <i class="ki-duotone ki-message-text-2 fs-2 text-gray-500">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </div>
                        <div class="timeline-content mb-10 mt-n1">
                            <div class="pe-3 mb-5">
                                <div class="fs-5 fw-semibold mb-2">There are 2 new tasks for you in "AirPlus Mobile App" project:</div>
                                <div class="d-flex align-items-center mt-1 fs-6">
                                    <div class="text-muted me-2 fs-7">Added at 4:23 PM by</div>
                                    <div class="symbol symbol-circle symbol-25px" data-bs-toggle="tooltip" data-bs-boundary="window" data-bs-placement="top" title="Nina Nilson">
                                        <img src="{{ asset('assets/media/avatars/300-14.jpg') }}" alt="img" />
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-auto pb-5">
                                <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-750px px-7 py-3 mb-5">
                                    <a href="#" class="fs-5 text-gray-900 text-hover-primary fw-semibold w-375px min-w-200px">Meeting with customer</a>
                                    <div class="min-w-175px pe-2">
                                        <span class="badge badge-light text-muted">Application Design</span>
                                    </div>
                                    <div class="symbol-group symbol-hover flex-nowrap flex-grow-1 min-w-100px pe-2">
                                        <div class="symbol symbol-circle symbol-25px">
                                            <img src="{{ asset('assets/media/avatars/300-2.jpg') }}" alt="img" />
                                        </div>
                                        <div class="symbol symbol-circle symbol-25px">
                                            <img src="{{ asset('assets/media/avatars/300-14.jpg') }}" alt="img" />
                                        </div>
                                    </div>
                                    <div class="min-w-125px pe-2">
                                        <span class="badge badge-light-primary">In Progress</span>
                                    </div>
                                    <a href="#" class="btn btn-sm btn-light btn-active-light-primary">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
