<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Admin\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        // TenantScope (via BelongsToTenant on Admin) already restricts this to the
        // current company automatically.
        $users = Admin::where('tier', 'user')->with('roles')->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = $this->tenantRoles();

        return view('admin.users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $user = Admin::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'tier'       => 'user',
            'created_by' => auth('admin')->id(),
            'status'     => 'active',
        ]);

        $user->assignRole(Role::find($request->role_id));

        return redirect()->route('crm.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(Admin $user)
    {
        $this->ensureTenantUser($user);

        $roles = $this->tenantRoles();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, Admin $user)
    {
        $this->ensureTenantUser($user);

        $data = $request->only('name', 'email');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles([Role::find($request->role_id)]);

        return redirect()->route('crm.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(Admin $user)
    {
        $this->ensureTenantUser($user);

        $user->delete();

        return redirect()->route('crm.users.index')
            ->with('success', 'User deleted successfully.');
    }

    private function ensureTenantUser(Admin $user): void
    {
        abort_unless($user->tier === 'user', 404);
    }

    private function tenantRoles()
    {
        return Role::where('unique_id', Auth::guard('admin')->user()->unique_id)
            ->orderBy('name')
            ->get();
    }
}
