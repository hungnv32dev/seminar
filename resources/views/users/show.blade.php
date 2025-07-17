@extends('layouts.app')

@section('title', 'User Details - ' . $user->name)

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">User Details</h1>
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
                <li class="breadcrumb-item text-muted">{{ $user->name }}</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @can('edit users')
            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm fw-bold btn-primary">
                <i class="ki-duotone ki-pencil fs-2"></i>Edit User
            </a>
            @endcan
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
            <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
                <!--begin::Card-->
                <div class="card mb-5 mb-xl-8">
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Summary-->
                        <!--begin::User Info-->
                        <div class="d-flex flex-center flex-column py-5">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-100px symbol-circle mb-7">
                                <div class="symbol-label fs-1 {{ $user->is_active ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            </div>
                            <!--end::Avatar-->
                            <!--begin::Name-->
                            <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">{{ $user->name }}</a>
                            <!--end::Name-->
                            <!--begin::Position-->
                            <div class="mb-9">
                                @if($user->roles->count() > 0)
                                    @foreach($user->roles as $role)
                                        <div class="badge badge-lg badge-light-primary d-inline me-2">{{ ucfirst($role->name) }}</div>
                                    @endforeach
                                @else
                                    <div class="badge badge-lg badge-light-secondary d-inline">No Role Assigned</div>
                                @endif
                            </div>
                            <!--end::Position-->
                            <!--begin::Info-->
                            <!--begin::Info heading-->
                            <div class="fw-bold mb-3">Workshop Statistics
                            <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="Number of workshops created and organized by this user.">
                                <i class="ki-duotone ki-information fs-7">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span></div>
                            <!--end::Info heading-->
                            <div class="d-flex flex-wrap flex-center">
                                <!--begin::Stats-->
                                <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                                    <div class="fs-4 fw-bold text-gray-700">
                                        <span class="w-75px">{{ $stats['total_workshops'] }}</span>
                                        @if($stats['total_workshops'] > 0)
                                        <i class="ki-duotone ki-arrow-up fs-3 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        @endif
                                    </div>
                                    <div class="fw-semibold text-muted">Total</div>
                                </div>
                                <!--end::Stats-->
                                <!--begin::Stats-->
                                <div class="border border-gray-300 border-dashed rounded py-3 px-3 mx-4 mb-3">
                                    <div class="fs-4 fw-bold text-gray-700">
                                        <span class="w-50px">{{ $stats['created_workshops'] }}</span>
                                    </div>
                                    <div class="fw-semibold text-muted">Created</div>
                                </div>
                                <!--end::Stats-->
                                <!--begin::Stats-->
                                <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                                    <div class="fs-4 fw-bold text-gray-700">
                                        <span class="w-50px">{{ $stats['organized_workshops'] }}</span>
                                    </div>
                                    <div class="fw-semibold text-muted">Organized</div>
                                </div>
                                <!--end::Stats-->
                            </div>
                            <!--end::Info-->
                        </div>
                        <!--end::User Info-->
                        <!--end::Summary-->
                        <!--begin::Details toggle-->
                        <div class="d-flex flex-stack fs-4 py-3">
                            <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_user_view_details" role="button" aria-expanded="false" aria-controls="kt_user_view_details">Details
                            <span class="ms-2 rotate-180">
                                <i class="ki-duotone ki-down fs-3"></i>
                            </span></div>
                            @can('edit users')
                            <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Edit user details">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-light-primary">Edit</a>
                            </span>
                            @endcan
                        </div>
                        <!--end::Details toggle-->
                        <div class="separator"></div>
                        <!--begin::Details content-->
                        <div id="kt_user_view_details" class="collapse show">
                            <div class="pb-5 fs-6">
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">User ID</div>
                                <div class="text-gray-600">ID-{{ $user->id }}</div>
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Email</div>
                                <div class="text-gray-600">
                                    <a href="mailto:{{ $user->email }}" class="text-gray-600 text-hover-primary">{{ $user->email }}</a>
                                </div>
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Status</div>
                                <div class="text-gray-600">
                                    @if($user->is_active)
                                        <span class="badge badge-light-success">Active</span>
                                    @else
                                        <span class="badge badge-light-danger">Inactive</span>
                                    @endif
                                </div>
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Roles</div>
                                <div class="text-gray-600">
                                    @if($user->roles->count() > 0)
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-light-primary me-1">{{ ucfirst($role->name) }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No roles assigned</span>
                                    @endif
                                </div>
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Permissions</div>
                                <div class="text-gray-600">
                                    @if($user->permissions->count() > 0)
                                        @foreach($user->permissions as $permission)
                                            <span class="badge badge-light-info me-1 mb-1">{{ $permission->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No direct permissions</span>
                                    @endif
                                </div>
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Joined Date</div>
                                <div class="text-gray-600">{{ $user->created_at->format('d M Y, H:i') }}</div>
                                <!--begin::Details item-->
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Last Updated</div>
                                <div class="text-gray-600">{{ $user->updated_at->format('d M Y, H:i') }}</div>
                                <!--begin::Details item-->
                            </div>
                        </div>
                        <!--end::Details content-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->

                <!--begin::Quick Actions-->
                <div class="card mb-5 mb-xl-8">
                    <!--begin::Card header-->
                    <div class="card-header border-0">
                        <div class="card-title">
                            <h3 class="fw-bold m-0">Quick Actions</h3>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-2">
                        <div class="d-flex flex-column gap-3">
                            @can('edit users')
                            <!--begin::Action-->
                            <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-light-{{ $user->is_active ? 'warning' : 'success' }} btn-sm w-100" 
                                        onclick="return confirm('Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this user?')">
                                    <i class="ki-duotone ki-{{ $user->is_active ? 'cross-circle' : 'check-circle' }} fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                                </button>
                            </form>
                            <!--end::Action-->
                            @endcan

                            @can('delete users')
                            <!--begin::Action-->
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light-danger btn-sm w-100" 
                                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    <i class="ki-duotone ki-trash fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                    Delete User
                                </button>
                            </form>
                            <!--end::Action-->
                            @endcan
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Quick Actions-->
            </div>
            <!--end::Sidebar-->

            <!--begin::Content-->
            <div class="flex-lg-row-fluid ms-lg-15">
                <!--begin:::Tabs-->
                <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_user_view_overview_tab">Overview</a>
                    </li>
                    <!--end:::Tab item-->
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_user_view_workshops_tab">Workshops</a>
                    </li>
                    <!--end:::Tab item-->
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_user_view_permissions_tab">Permissions</a>
                    </li>
                    <!--end:::Tab item-->
                </ul>
                <!--end:::Tabs-->

                <!--begin:::Tab content-->
                <div class="tab-content" id="myTabContent">
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade show active" id="kt_user_view_overview_tab" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card card-flush mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header mt-6">
                                <!--begin::Card title-->
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">User Overview</h2>
                                    <div class="fs-6 fw-semibold text-muted">User account information and statistics</div>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body p-9 pt-4">
                                <!--begin::Row-->
                                <div class="row mb-7">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 fw-semibold text-muted">Full Name</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8">
                                        <span class="fw-bold fs-6 text-gray-800">{{ $user->name }}</span>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Row-->
                                <!--begin::Input group-->
                                <div class="row mb-7">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 fw-semibold text-muted">Email</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 fv-row">
                                        <span class="fw-semibold text-gray-800 fs-6">{{ $user->email }}</span>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="row mb-7">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 fw-semibold text-muted">Status</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8 d-flex align-items-center">
                                        @if($user->is_active)
                                            <span class="badge badge-light-success me-1">Active</span>
                                        @else
                                            <span class="badge badge-light-danger me-1">Inactive</span>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="row mb-7">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 fw-semibold text-muted">Roles</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8">
                                        @if($user->roles->count() > 0)
                                            @foreach($user->roles as $role)
                                                <span class="badge badge-light-primary me-1">{{ ucfirst($role->name) }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No roles assigned</span>
                                        @endif
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="row mb-7">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 fw-semibold text-muted">Joined Date</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8">
                                        <span class="fw-semibold fs-6 text-gray-800">{{ $user->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="row mb-7">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 fw-semibold text-muted">Last Updated</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8">
                                        <span class="fw-semibold fs-6 text-gray-800">{{ $user->updated_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end:::Tab pane-->

                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_user_view_workshops_tab" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card card-flush mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header mt-6">
                                <!--begin::Card title-->
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Created Workshops</h2>
                                    <div class="fs-6 fw-semibold text-muted">Workshops created by this user</div>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body p-9 pt-4">
                                @if($user->createdWorkshops->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                                            <thead>
                                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                    <th>Workshop</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Participants</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-600 fw-semibold">
                                                @foreach($user->createdWorkshops as $workshop)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <a href="{{ route('workshops.show', $workshop) }}" class="text-gray-800 text-hover-primary mb-1">{{ $workshop->name }}</a>
                                                            <span class="text-muted">{{ Str::limit($workshop->description, 50) }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light-{{ $workshop->status === 'published' ? 'success' : ($workshop->status === 'draft' ? 'warning' : 'primary') }}">
                                                            {{ ucfirst($workshop->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $workshop->start_date->format('d M Y') }}</td>
                                                    <td>{{ $workshop->participants->count() }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('workshops.show', $workshop) }}" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-10">
                                        <div class="text-gray-500 fs-6">No workshops created yet</div>
                                    </div>
                                @endif
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->

                        <!--begin::Card-->
                        <div class="card card-flush mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header mt-6">
                                <!--begin::Card title-->
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Organized Workshops</h2>
                                    <div class="fs-6 fw-semibold text-muted">Workshops organized by this user</div>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body p-9 pt-4">
                                @if($user->organizedWorkshops->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                                            <thead>
                                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                    <th>Workshop</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Participants</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-600 fw-semibold">
                                                @foreach($user->organizedWorkshops as $workshop)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <a href="{{ route('workshops.show', $workshop) }}" class="text-gray-800 text-hover-primary mb-1">{{ $workshop->name }}</a>
                                                            <span class="text-muted">{{ Str::limit($workshop->description, 50) }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light-{{ $workshop->status === 'published' ? 'success' : ($workshop->status === 'draft' ? 'warning' : 'primary') }}">
                                                            {{ ucfirst($workshop->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $workshop->start_date->format('d M Y') }}</td>
                                                    <td>{{ $workshop->participants->count() }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('workshops.show', $workshop) }}" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-10">
                                        <div class="text-gray-500 fs-6">No workshops organized yet</div>
                                    </div>
                                @endif
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end:::Tab pane-->

                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_user_view_permissions_tab" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card card-flush mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header mt-6">
                                <!--begin::Card title-->
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Role Permissions</h2>
                                    <div class="fs-6 fw-semibold text-muted">Permissions granted through assigned roles</div>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body p-9 pt-4">
                                @if($user->roles->count() > 0)
                                    @foreach($user->roles as $role)
                                    <div class="mb-8">
                                        <h4 class="fw-bold text-gray-800 mb-4">{{ ucfirst($role->name) }} Role</h4>
                                        @if($role->permissions->count() > 0)
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($role->permissions as $permission)
                                                    <span class="badge badge-light-info">{{ $permission->name }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted">No permissions assigned to this role</div>
                                        @endif
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-10">
                                        <div class="text-gray-500 fs-6">No roles assigned</div>
                                    </div>
                                @endif
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->

                        @if($user->permissions->count() > 0)
                        <!--begin::Card-->
                        <div class="card card-flush mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header mt-6">
                                <!--begin::Card title-->
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Direct Permissions</h2>
                                    <div class="fs-6 fw-semibold text-muted">Permissions directly assigned to this user</div>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body p-9 pt-4">
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($user->permissions as $permission)
                                        <span class="badge badge-light-primary">{{ $permission->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                        @endif
                    </div>
                    <!--end:::Tab pane-->
                </div>
                <!--end:::Tab content-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Layout-->
    </div>
</div>
<!--end::Content-->
@endsection