<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Exception;

class UserController extends Controller
{
    public function __construct()
    {
        // Apply middleware for permissions
        $this->middleware('can:manage users')->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
        $this->middleware('can:view users')->only(['index', 'show']);
        $this->middleware('can:create users')->only(['create', 'store']);
        $this->middleware('can:edit users')->only(['edit', 'update', 'toggleStatus']);
        $this->middleware('can:delete users')->only(['destroy']);
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $query = User::with('roles');

        // Apply filters
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(15)->withQueryString();

        // Get roles for filter dropdown
        $roles = Role::orderBy('name')->get();

        $filters = [
            'role' => $request->get('role'),
            'status' => $request->get('status'),
            'search' => $request->get('search'),
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ];

        return view('users.index', compact('users', 'roles', 'filters'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Assign roles
            if ($request->filled('roles')) {
                $user->assignRole($request->roles);
            }

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'User created successfully.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $user->load(['roles', 'permissions', 'createdWorkshops', 'organizedWorkshops']);

        // Get user statistics
        $stats = [
            'created_workshops' => $user->createdWorkshops->count(),
            'organized_workshops' => $user->organizedWorkshops->count(),
            'total_workshops' => $user->createdWorkshops->count() + $user->organizedWorkshops->count(),
            'active_workshops' => $user->createdWorkshops->whereIn('status', ['published', 'ongoing'])->count() +
                                 $user->organizedWorkshops->whereIn('status', ['published', 'ongoing'])->count(),
        ];

        return view('users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $user->load('roles');
        $roles = Role::orderBy('name')->get();
        
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => $request->boolean('is_active', true),
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Sync roles
            if ($request->has('roles')) {
                $user->syncRoles($request->roles ?? []);
            }

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'User updated successfully.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            // Prevent deleting the current user
            if ($user->id === auth()->id()) {
                return back()
                    ->with('error', 'You cannot delete your own account.');
            }

            // Check if user has created workshops
            $createdWorkshopsCount = $user->createdWorkshops()->count();
            if ($createdWorkshopsCount > 0) {
                return back()
                    ->with('error', "Cannot delete user who has created {$createdWorkshopsCount} workshops. Please reassign or delete the workshops first.");
            }

            // Detach from organized workshops
            $user->organizedWorkshops()->detach();

            // Remove all roles and permissions
            $user->syncRoles([]);
            $user->syncPermissions([]);

            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', 'User deleted successfully.');

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        try {
            // Prevent deactivating the current user
            if ($user->id === auth()->id() && $user->is_active) {
                return back()
                    ->with('error', 'You cannot deactivate your own account.');
            }

            $user->update([
                'is_active' => !$user->is_active
            ]);

            $status = $user->is_active ? 'activated' : 'deactivated';

            return back()
                ->with('success', "User {$status} successfully.");

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to update user status: ' . $e->getMessage());
        }
    }

    /**
     * Show roles and permissions management.
     */
    public function rolesPermissions(): View
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('users.roles-permissions', compact('roles', 'permissions'));
    }

    /**
     * Get users by role (AJAX endpoint).
     */
    public function getUsersByRole(Request $request)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        $users = User::role($request->role)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    /**
     * Get user statistics (AJAX endpoint).
     */
    public function getUserStats(User $user)
    {
        $user->loadCount(['createdWorkshops', 'organizedWorkshops']);

        $stats = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => $user->is_active,
            'roles' => $user->getRoleNames(),
            'created_workshops_count' => $user->created_workshops_count,
            'organized_workshops_count' => $user->organized_workshops_count,
            'total_workshops' => $user->created_workshops_count + $user->organized_workshops_count,
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            'last_login' => null, // You could track this if needed
        ];

        return response()->json($stats);
    }

    /**
     * Bulk update user status.
     */
    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'is_active' => 'required|boolean',
        ]);

        try {
            // Prevent deactivating the current user
            if (!$request->is_active && in_array(auth()->id(), $request->user_ids)) {
                return back()
                    ->with('error', 'You cannot deactivate your own account.');
            }

            $updated = User::whereIn('id', $request->user_ids)
                ->update(['is_active' => $request->is_active]);

            $status = $request->is_active ? 'activated' : 'deactivated';

            return back()
                ->with('success', "{$updated} users {$status} successfully.");

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to update user status: ' . $e->getMessage());
        }
    }

    /**
     * Assign role to multiple users.
     */
    public function bulkAssignRole(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|string',
            'role' => 'required|exists:roles,name',
        ]);

        try {
            $userIds = explode(',', $request->user_ids);
            $users = User::whereIn('id', $userIds)->get();
            
            foreach ($users as $user) {
                $user->assignRole($request->role);
            }

            return back()
                ->with('success', "Role '{$request->role}' assigned to {$users->count()} users successfully.");

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to assign role: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete users.
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            // Prevent deleting the current user
            $userIds = array_filter($request->user_ids, function($id) {
                return $id != auth()->id();
            });

            if (empty($userIds)) {
                return back()
                    ->with('error', 'Cannot delete your own account or no valid users selected.');
            }

            $users = User::whereIn('id', $userIds)->get();
            $deletedCount = 0;
            $errors = [];

            foreach ($users as $user) {
                // Check if user has created workshops
                $createdWorkshopsCount = $user->createdWorkshops()->count();
                if ($createdWorkshopsCount > 0) {
                    $errors[] = "Cannot delete {$user->name} who has created {$createdWorkshopsCount} workshops.";
                    continue;
                }

                // Detach from organized workshops
                $user->organizedWorkshops()->detach();

                // Remove all roles and permissions
                $user->syncRoles([]);
                $user->syncPermissions([]);

                $user->delete();
                $deletedCount++;
            }

            $message = "{$deletedCount} users deleted successfully.";
            if (!empty($errors)) {
                $message .= ' ' . implode(' ', $errors);
            }

            return back()
                ->with($deletedCount > 0 ? 'success' : 'warning', $message);

        } catch (Exception $e) {
            return back()
                ->with('error', 'Failed to delete users: ' . $e->getMessage());
        }
    }
}
