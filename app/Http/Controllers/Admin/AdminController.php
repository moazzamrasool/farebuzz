<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Http\Requests\Admin\BlockCompanyRequest;
use App\Models\Admin\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminController extends Controller
{
    // Companies list — Super Admin only. Each row here (tier=admin) is one company.
    public function index()
    {
        $admins = Admin::where('tier', 'admin')
            ->with(['blockedBy', 'unblockedBy'])
            ->latest()
            ->paginate(15);

        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(AdminRequest $request)
    {
        $admin = Admin::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'unique_id'  => (string) Str::uuid(),
            'tier'       => 'admin',
            'created_by' => auth('admin')->id(),
            'status'     => 'active',
        ]);

        $this->bootstrapOwnerRole($admin);

        return redirect()->route('crm.admins.index')
            ->with('success', "Company created successfully. Unique ID: {$admin->unique_id}");
    }

    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(AdminRequest $request, Admin $admin)
    {
        $data = $request->only('name', 'email');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('crm.admins.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Admin $admin)
    {
        $hasUsers = Admin::withoutTenantScope()
            ->where('unique_id', $admin->unique_id)
            ->where('tier', 'user')
            ->exists();

        if ($hasUsers) {
            return redirect()->route('crm.admins.index')
                ->with('error', 'Cannot delete this company — it still has users linked to it.');
        }

        Role::where('unique_id', $admin->unique_id)->delete();
        Admin::forgetBlockedStatusCache($admin->unique_id);
        $admin->delete();

        return redirect()->route('crm.admins.index')
            ->with('success', 'Company deleted successfully.');
    }

    // Takes the company's entire public site and CRM access down immediately.
    // See App\Http\Middleware\EnsureSiteNotBlocked (frontend) and
    // Admin::isCompanySuspended() / AdminAuthenticate (CRM) for enforcement.
    public function block(BlockCompanyRequest $request, Admin $admin)
    {
        $admin->is_blocked        = true;
        $admin->blocked_at        = now();
        $admin->blocked_by        = $request->user('admin')->id;
        $admin->blocked_reason    = $request->reason;
        $admin->unblocked_at      = null;
        $admin->unblocked_by      = null;
        $admin->unblocked_reason  = null;
        $admin->save();

        Admin::forgetBlockedStatusCache($admin->unique_id);

        Log::info('Company blocked', [
            'company_id' => $admin->id,
            'unique_id'  => $admin->unique_id,
            'blocked_by' => $request->user('admin')->id,
            'reason'     => $request->reason,
        ]);

        return redirect()->route('crm.admins.index')
            ->with('success', "{$admin->name} has been blocked. Its site and CRM access are now suspended.");
    }

    public function unblock(BlockCompanyRequest $request, Admin $admin)
    {
        $admin->is_blocked       = false;
        $admin->unblocked_at     = now();
        $admin->unblocked_by     = $request->user('admin')->id;
        $admin->unblocked_reason = $request->reason;
        $admin->save();

        Admin::forgetBlockedStatusCache($admin->unique_id);

        Log::info('Company unblocked', [
            'company_id'  => $admin->id,
            'unique_id'   => $admin->unique_id,
            'unblocked_by' => $request->user('admin')->id,
            'reason'      => $request->reason,
        ]);

        return redirect()->route('crm.admins.index')
            ->with('success', "{$admin->name} has been unblocked. Its site and CRM access are restored.");
    }

    // Every new company starts with a full-access "Owner" role assigned to its Admin —
    // otherwise nobody could ever grant that Admin permission to manage their own
    // company's roles/users. Roles/Users routes separately let an Admin bypass Spatie
    // checks entirely via tier (see EnsureHasModulePermission), so this role mainly
    // documents the Admin's access and lets them see it listed alongside other roles.
    private function bootstrapOwnerRole(Admin $admin): void
    {
        $registrar = app(PermissionRegistrar::class);
        $previousTeamId = $registrar->getPermissionsTeamId();

        $registrar->setPermissionsTeamId($admin->unique_id);

        $role = Role::create(['name' => 'Owner', 'guard_name' => 'admin']);
        $role->syncPermissions(Permission::where('guard_name', 'admin')->get());
        $admin->assignRole($role);

        $registrar->setPermissionsTeamId($previousTeamId);
    }
}
