@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Roles & Permissions</h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('users.index') }}" class="text-muted text-hover-primary">User Management</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Roles & Permissions</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('users.index') }}" class="btn btn-sm fw-bold btn-secondary">
                <i class="ki-duotone ki-arrow-left fs-2"></i>Back to Users
            </a>
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
            <div class="flex-column flex-lg-row-auto w-100 w-lg-600px mb-10 mb-lg-0">
                <!--begin::Card-->
                <div class="card card-flush">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <h2>Roles</h2>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-125px">Role</th>
                                    <th class="min-w-125px">Users</th>
                                    <th class="min-w-125px">Permissions</th>
                                    <th class="text-end min-w-100px">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @forelse($roles as $role)
                                <tr>
                                    <td class="d-flex align-items-center">
                                        <div class="symbol symbol-50px me-5">
                                            <span class="symbol-label bg-light-primary text-primary fw-bold">
                                                {{ strtoupper(substr($role->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <a href="#" class="text-gray-800 text-hover-primary mb-1">{{ ucfirst($role->name) }}</a>
                                            <span class="text-muted">{{ $role->users->count() }} users assigned</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary">{{ $role->users->count() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-info">{{ $role->permissions->count() }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                        <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_view_role" data-role-id="{{ $role->id }}">View</a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_role" data-role-id="{{ $role->id }}">Edit</a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10">
                                        <div class="text-gray-500 fs-6">No roles found</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Sidebar-->

            <!--begin::Content-->
            <div class="flex-lg-row-fluid ms-lg-10">
                <!--begin::Card-->
                <div class="card card-flush mb-6 mb-xl-9">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <h2>All Permissions</h2>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Permissions-->
                        <div class="d-flex flex-wrap gap-5">
                            @forelse($permissions as $permission)
                            <div class="d-flex flex-stack">
                                <div class="d-flex">
                                    <div class="d-flex flex-column">
                                        <span class="badge badge-light-info fs-7 fw-semibold">{{ $permission->name }}</span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-10 w-100">
                                <div class="text-gray-500 fs-6">No permissions found</div>
                            </div>
                            @endforelse
                        </div>
                        <!--end::Permissions-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->

                <!--begin::Card-->
                <div class="card card-flush">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <h2>Role Distribution</h2>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Chart-->
                        <div id="kt_role_distribution_chart" style="height: 350px"></div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Layout-->

    </div>
</div>
<!--end::Content-->

<!-- View Role Modal -->
<div class="modal fade" id="kt_modal_view_role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold" id="view_role_title">Role Details</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <div id="view_role_content">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="kt_modal_edit_role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Edit Role</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_edit_role_form">
                    <div id="edit_role_content">
                        <!-- Content will be loaded dynamically -->
                    </div>
                    <div class="text-center pt-10">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Save Changes</span>
                            <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Role data for chart
    const roleData = @json($roles->map(function($role) {
        return [
            'name' => ucfirst($role->name),
            'count' => $role->users->count()
        ];
    }));

    // Initialize role distribution chart
    if (document.getElementById('kt_role_distribution_chart')) {
        const options = {
            series: roleData.map(role => role.count),
            chart: {
                type: 'donut',
                height: 350
            },
            labels: roleData.map(role => role.name),
            colors: ['#009EF7', '#50CD89', '#F1416C', '#7239EA', '#FFC700'],
            legend: {
                position: 'bottom'
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        const chart = new ApexCharts(document.querySelector("#kt_role_distribution_chart"), options);
        chart.render();
    }

    // Handle view role modal
    const viewRoleModal = document.getElementById('kt_modal_view_role');
    if (viewRoleModal) {
        viewRoleModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const roleId = button.getAttribute('data-role-id');
            
            // Find role data
            const roles = @json($roles);
            const role = roles.find(r => r.id == roleId);
            
            if (role) {
                document.getElementById('view_role_title').textContent = ucfirst(role.name) + ' Role';
                
                let content = `
                    <div class="mb-7">
                        <label class="fw-bold fs-6 mb-2">Role Name</label>
                        <div class="fs-6 text-gray-800">${ucfirst(role.name)}</div>
                    </div>
                    <div class="mb-7">
                        <label class="fw-bold fs-6 mb-2">Users Assigned</label>
                        <div class="fs-6 text-gray-800">${role.users ? role.users.length : 0} users</div>
                    </div>
                    <div class="mb-7">
                        <label class="fw-bold fs-6 mb-2">Permissions</label>
                        <div class="d-flex flex-wrap gap-2">
                `;
                
                if (role.permissions && role.permissions.length > 0) {
                    role.permissions.forEach(permission => {
                        content += `<span class="badge badge-light-info">${permission.name}</span>`;
                    });
                } else {
                    content += '<span class="text-muted">No permissions assigned</span>';
                }
                
                content += `
                        </div>
                    </div>
                `;
                
                document.getElementById('view_role_content').innerHTML = content;
            }
        });
    }

    // Handle edit role modal
    const editRoleModal = document.getElementById('kt_modal_edit_role');
    if (editRoleModal) {
        editRoleModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const roleId = button.getAttribute('data-role-id');
            
            // Find role data
            const roles = @json($roles);
            const role = roles.find(r => r.id == roleId);
            
            if (role) {
                let content = `
                    <div class="fv-row mb-10">
                        <label class="fs-5 fw-bold form-label mb-2">Role Name</label>
                        <input type="text" class="form-control form-control-solid" name="name" value="${role.name}" readonly />
                        <div class="form-text">Role names cannot be changed</div>
                    </div>
                    <div class="fv-row">
                        <label class="fs-5 fw-bold form-label mb-2">Permissions</label>
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <tbody class="text-gray-600 fw-semibold">
                `;
                
                const allPermissions = @json($permissions);
                const rolePermissions = role.permissions ? role.permissions.map(p => p.name) : [];
                
                allPermissions.forEach(permission => {
                    const isChecked = rolePermissions.includes(permission.name) ? 'checked' : '';
                    content += `
                        <tr>
                            <td>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="${permission.name}" name="permissions[]" ${isChecked} />
                                </div>
                            </td>
                            <td class="text-gray-800">${permission.name}</td>
                        </tr>
                    `;
                });
                
                content += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                
                document.getElementById('edit_role_content').innerHTML = content;
            }
        });
    }

    // Utility function
    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
});
</script>
@endpush