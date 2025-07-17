{{-- Header Menu --}}
<div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
    
    <!-- Menu -->
    <div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0" id="kt_app_header_menu" data-kt-menu="true">
        
        <!-- Pages -->
        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item menu-here-bg menu-lg-down-accordion me-0 me-lg-2">
            <span class="menu-link">
                <span class="menu-title">Pages</span>
                <span class="menu-arrow d-lg-none"></span>
            </span>
            
            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown p-0 w-100 w-lg-850px">
                <div class="menu-state-bg menu-extended overflow-hidden overflow-lg-visible" data-kt-menu-dismiss="true">
                    <div class="row">
                        <div class="col-lg-8 mb-3 mb-lg-0 py-3 px-3 py-lg-6 px-lg-6">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <div class="menu-item p-0 m-0">
                                        <a href="{{ route('dashboard') }}" class="menu-link">
                                            <span class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                <i class="ki-duotone ki-element-11 text-primary fs-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                </i>
                                            </span>
                                            <span class="d-flex flex-column">
                                                <span class="fs-6 fw-bold text-gray-800">Dashboard</span>
                                                <span class="fs-7 fw-semibold text-muted">Reports & statistics</span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 py-3 px-3 py-lg-6 px-lg-6 rounded-end">
                            <img src="{{ asset('assets/media/stock/600x600/img-1.jpg') }}" class="rounded mw-100" alt="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Apps -->
        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion me-0 me-lg-2">
            <span class="menu-link">
                <span class="menu-title">Apps</span>
                <span class="menu-arrow d-lg-none"></span>
            </span>
            
            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown p-0">
                <div class="menu-state-bg menu-extended overflow-hidden" data-kt-menu-dismiss="true">
                    <div class="row">
                        <div class="col-lg-8 mb-3 mb-lg-0 py-3 px-3 py-lg-6 px-lg-6">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <div class="menu-item p-0 m-0">
                                        <a href="#" class="menu-link">
                                            <span class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                <i class="ki-duotone ki-abstract-26 text-danger fs-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                            <span class="d-flex flex-column">
                                                <span class="fs-6 fw-bold text-gray-800">Projects</span>
                                                <span class="fs-7 fw-semibold text-muted">Tasts, graphs & charts</span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Help -->
        <div class="menu-item">
            <a class="menu-link" href="#">
                <span class="menu-title">Help</span>
            </a>
        </div>
    </div>
</div>
