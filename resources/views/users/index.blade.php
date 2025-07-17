@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">User Management</h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">User Management</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @can('create users')
            <a href="{{ route('users.create') }}" class="btn btn-sm fw-bold btn-primary">
                <i class="ki-duotone ki-plus fs-2"></i>Add User
            </a>
            @endcan
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        
        <!-- Users List -->
        <div class="card">
            <!--begin::Card header-->
            <div class="card-header border-0 pt-6">
                <!--begin::Card title-->
                <div class="card-title">
                    <!--begin::Search-->
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search user" value="{{ $filters['search'] }}" />
                    </div>
                    <!--end::Search-->
                </div>
                <!--begin::Card title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <!--begin::Toolbar-->
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        <!--begin::Filter-->
                        <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            <i class="ki-duotone ki-filter fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>Filter
                        </button>
                        <!--begin::Menu 1-->
                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                            <!--begin::Header-->
                            <div class="px-7 py-5">
                                <div class="fs-5 text-gray-900 fw-bold">Filter Options</div>
                            </div>
                            <!--end::Header-->
                            <!--begin::Separator-->
                            <div class="separator border-gray-200"></div>
                            <!--end::Separator-->
                            <!--begin::Content-->
                            <form method="GET" action="{{ route('users.index') }}">
                                <div class="px-7 py-5" data-kt-user-table-filter="form">
                                    <!--begin::Input group-->
                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-semibold">Role:</label>
                                        <select class="form-select form-select-solid fw-bold" name="role" data-control="select2" data-placeholder="Select role" data-allow-clear="true" data-kt-user-table-filter="role" data-hide-search="true">
                                            <option></option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ $filters['role'] == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-semibold">Status:</label>
                                        <select class="form-select form-select-solid fw-bold" name="status" data-control="select2" data-placeholder="Select status" data-allow-clear="true" data-kt-user-table-filter="status" data-hide-search="true">
                                            <option></option>
                                            <option value="active" {{ $filters['status'] == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $filters['status'] == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Actions-->
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('users.index') }}" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" data-kt-menu-dismiss="true">Reset</a>
                                        <button type="submit" class="btn btn-primary fw-semibold px-6" data-kt-menu-dismiss="true">Apply</button>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                            </form>
                            <!--end::Content-->
                        </div>
                        <!--end::Menu 1-->
                        <!--end::Filter-->
                        <!--begin::Export-->
                        <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_export_users">
                            <i class="ki-duotone ki-exit-up fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>Export
                        </button>
                        <!--end::Export-->
                        <!--begin::Add user-->
                        @can('create users')
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="ki-duotone ki-plus fs-2"></i>Add User
                        </a>
                        @endcan
                        <!--end::Add user-->
                    </div>
                    <!--end::Toolbar-->
                    <!--begin::Group actions-->
                    <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
                        <div class="fw-bold me-5">
                            <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected
                        </div>
                        <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_assign_role">Assign Role</button>
                        <button type="button" class="btn btn-light-warning me-3" data-kt-user-table-select="activate_selected">Activate</button>
                        <button type="button" class="btn btn-light-danger me-3" data-kt-user-table-select="deactivate_selected">Deactivate</button>
                        <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
                    </div>
                    <!--end::Group actions-->
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body py-4">
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">
                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                                </div>
                            </th>
                            <th class="min-w-125px">User</th>
                            <th class="min-w-125px">Role</th>
                            <th class="min-w-125px">Last login</th>
                            <th class="min-w-125px">Two-step</th>
                            <th class="min-w-125px">Joined Date</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="{{ $user->id }}" />
                                </div>
                            </td>
                            <td class="d-flex align-items-center">
                                <!--begin:: Avatar -->
                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                    <a href="{{ route('users.show', $user) }}">
                                        <div class="symbol-label">
                                            <div class="symbol-label fs-3 {{ $user->is_active ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <!--end::Avatar-->
                                <!--begin::User details-->
                                <div class="d-flex flex-column">
                                    <a href="{{ route('users.show', $user) }}" class="text-gray-800 text-hover-primary mb-1">{{ $user->name }}</a>
                                    <span>{{ $user->email }}</span>
                                </div>
                                <!--begin::User details-->
                            </td>
                            <td>
                                @if($user->roles->count() > 0)
                                    @foreach($user->roles as $role)
                                        <span class="badge badge-light-primary fs-7 fw-semibold me-1">{{ ucfirst($role->name) }}</span>
                                    @endforeach
                                @else
                                    <span class="badge badge-light-secondary fs-7 fw-semibold">No Role</span>
                                @endif
                            </td>
                            <td>
                                <div class="badge badge-light fw-semibold">{{ $user->updated_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div class="badge badge-light-success fw-semibold">Enabled</div>
                            </td>
                            <td>{{ $user->created_at->format('d M Y, H:i') }}</td>
                            <td class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                <!--begin::Menu-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                    <!--begin::Menu item-->
                                    @can('view users')
                                    <div class="menu-item px-3">
                                        <a href="{{ route('users.show', $user) }}" class="menu-link px-3">View</a>
                                    </div>
                                    @endcan
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    @can('edit users')
                                    <div class="menu-item px-3">
                                        <a href="{{ route('users.edit', $user) }}" class="menu-link px-3">Edit</a>
                                    </div>
                                    @endcan
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    @can('edit users')
                                    <div class="menu-item px-3">
                                        <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="menu-link px-3 btn btn-link p-0 text-start w-100" 
                                                    onclick="return confirm('Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this user?')">
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                    @endcan
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    @can('delete users')
                                    <div class="menu-item px-3">
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="menu-link px-3 btn btn-link p-0 text-start w-100 text-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                    @endcan
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu-->
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-10">
                                <div class="text-gray-500 fs-6">No users found</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <!--end::Table-->
                
                <!-- Pagination -->
                @if($users->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-5">
                    <div class="text-gray-700">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                    </div>
                    {{ $users->links() }}
                </div>
                @endif
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Users List-->

    </div>
</div>
<!--end::Content-->

<!-- Export Modal -->
<div class="modal fade" id="kt_modal_export_users" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Export Users</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_export_users_form" class="form" action="#">
                    <div class="fv-row mb-10">
                        <label class="fs-6 fw-semibold form-label mb-2">Select Roles:</label>
                        <select name="role" data-control="select2" data-placeholder="Select a role" data-hide-search="true" class="form-select form-select-solid fw-bold">
                            <option></option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fv-row mb-10">
                        <label class="required fs-6 fw-semibold form-label mb-2">Select Export Format:</label>
                        <select name="format" data-control="select2" data-placeholder="Select a format" data-hide-search="true" class="form-select form-select-solid fw-bold">
                            <option></option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                            <option value="csv">CSV</option>
                            <option value="zip">ZIP</option>
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Discard</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Assign Role Modal -->
<div class="modal fade" id="kt_modal_assign_role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Assign Role to Selected Users</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_assign_role_form" method="POST" action="{{ route('users.bulk-assign-role') }}">
                    @csrf
                    <input type="hidden" name="user_ids" id="bulk_user_ids" />
                    
                    <div class="fv-row mb-10">
                        <label class="fs-5 fw-bold form-label mb-2">Select Role</label>
                        <div class="mb-5">
                            <div class="text-muted fs-7">Choose a role to assign to the selected users. This will add the role to their existing roles.</div>
                        </div>
                        @foreach($roles as $role)
                        <!--begin::Input row-->
                        <div class="d-flex fv-row mb-7">
                            <!--begin::Radio-->
                            <div class="form-check form-check-custom form-check-solid">
                                <!--begin::Input-->
                                <input class="form-check-input me-3" name="role" type="radio" value="{{ $role->name }}" id="kt_modal_assign_role_option_{{ $loop->index }}" />
                                <!--end::Input-->
                                <!--begin::Label-->
                                <label class="form-check-label" for="kt_modal_assign_role_option_{{ $loop->index }}">
                                    <div class="fw-bold text-gray-800">{{ ucfirst($role->name) }}</div>
                                    <div class="text-gray-600">{{ $role->permissions->count() }} permissions assigned</div>
                                </label>
                                <!--end::Label-->
                            </div>
                            <!--end::Radio-->
                        </div>
                        <!--end::Input row-->
                        @endforeach
                    </div>
                    
                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Assign Role</span>
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
    // Initialize search functionality
    const searchInput = document.querySelector('[data-kt-user-table-filter="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const url = new URL(window.location);
                if (this.value) {
                    url.searchParams.set('search', this.value);
                } else {
                    url.searchParams.delete('search');
                }
                window.location.href = url.toString();
            }, 500);
        });
    }

    // Initialize select all functionality
    const selectAllCheckbox = document.querySelector('[data-kt-check="true"]');
    const rowCheckboxes = document.querySelectorAll('#kt_table_users .form-check-input[type="checkbox"]:not([data-kt-check="true"])');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }

    // Update selected count
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('#kt_table_users .form-check-input[type="checkbox"]:checked:not([data-kt-check="true"])').length;
        const selectedCountElement = document.querySelector('[data-kt-user-table-select="selected_count"]');
        const selectedToolbar = document.querySelector('[data-kt-user-table-toolbar="selected"]');
        const baseToolbar = document.querySelector('[data-kt-user-table-toolbar="base"]');
        
        if (selectedCountElement) {
            selectedCountElement.textContent = selectedCount;
        }
        
        if (selectedCount > 0) {
            selectedToolbar?.classList.remove('d-none');
            baseToolbar?.classList.add('d-none');
        } else {
            selectedToolbar?.classList.add('d-none');
            baseToolbar?.classList.remove('d-none');
        }
    }

    // Handle bulk actions
    function getSelectedUserIds() {
        return Array.from(document.querySelectorAll('#kt_table_users .form-check-input[type="checkbox"]:checked:not([data-kt-check="true"])'))
            .map(checkbox => checkbox.value);
    }

    // Handle bulk delete
    const deleteSelectedBtn = document.querySelector('[data-kt-user-table-select="delete_selected"]');
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            const selectedIds = getSelectedUserIds();
            
            if (selectedIds.length > 0 && confirm(`Are you sure you want to delete ${selectedIds.length} selected users? This action cannot be undone.`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("users.bulk-delete") }}';
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add user IDs
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Handle bulk activate
    const activateSelectedBtn = document.querySelector('[data-kt-user-table-select="activate_selected"]');
    if (activateSelectedBtn) {
        activateSelectedBtn.addEventListener('click', function() {
            const selectedIds = getSelectedUserIds();
            
            if (selectedIds.length > 0 && confirm(`Are you sure you want to activate ${selectedIds.length} selected users?`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("users.bulk-update-status") }}';
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add status
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'is_active';
                statusInput.value = '1';
                form.appendChild(statusInput);
                
                // Add user IDs
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Handle bulk deactivate
    const deactivateSelectedBtn = document.querySelector('[data-kt-user-table-select="deactivate_selected"]');
    if (deactivateSelectedBtn) {
        deactivateSelectedBtn.addEventListener('click', function() {
            const selectedIds = getSelectedUserIds();
            
            if (selectedIds.length > 0 && confirm(`Are you sure you want to deactivate ${selectedIds.length} selected users?`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("users.bulk-update-status") }}';
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add status
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'is_active';
                statusInput.value = '0';
                form.appendChild(statusInput);
                
                // Add user IDs
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Handle role assignment modal
    const assignRoleModal = document.getElementById('kt_modal_assign_role');
    if (assignRoleModal) {
        assignRoleModal.addEventListener('show.bs.modal', function() {
            const selectedIds = getSelectedUserIds();
            document.getElementById('bulk_user_ids').value = selectedIds.join(',');
        });
    }
});
</script>
@endpush