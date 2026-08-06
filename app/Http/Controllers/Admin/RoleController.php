<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Admin\Admin;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $tenantId = Auth::guard('admin')->user()->unique_id;

        $roles = Role::where('unique_id', $tenantId)->latest()->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissionGroups = $this->groupedPermissions();

        return view('admin.roles.create', compact('permissionGroups'));
    }

    public function store(RoleRequest $request)
    {
        // ResolveTenant middleware already set the active team id for this request,
        // so Role::create() auto-fills unique_id from context.
        $role = Role::create(['name' => $request->name, 'guard_name' => 'admin']);
        $role->syncPermissions($this->permissionIds($request));

        return redirect()->route('crm.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $this->ensureTenantRole($role);

        $permissionGroups = $this->groupedPermissions();
        $assignedPermissionIds = $role->permissions->pluck('id')->all();

        return view('admin.roles.edit', compact('role', 'permissionGroups', 'assignedPermissionIds'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        $this->ensureTenantRole($role);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($this->permissionIds($request));

        return redirect()->route('crm.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $this->ensureTenantRole($role);

        if (Admin::role($role)->exists()) {
            return redirect()->route('crm.roles.index')
                ->with('error', 'Cannot delete this role — it is still assigned to one or more users.');
        }

        $role->delete();

        return redirect()->route('crm.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    private function ensureTenantRole(Role $role): void
    {
        abort_unless($role->unique_id === Auth::guard('admin')->user()->unique_id, 404);
    }

    // Checkbox values POST as numeric strings ("70"), but Spatie's syncPermissions()
    // only resolves permissions by ID for true PHP integers (is_int()) — a numeric
    // string instead falls through to its find-by-name lookup and throws
    // PermissionDoesNotExist. Casting here is what makes ID-based sync actually work.
    private function permissionIds(RoleRequest $request): array
    {
        return array_map('intval', $request->input('permissions', []));
    }

    private function groupedPermissions()
    {
        return Permission::where('guard_name', 'admin')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($permission) => explode('.', $permission->name)[0]);
    }
}
