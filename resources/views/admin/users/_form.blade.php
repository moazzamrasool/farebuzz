@csrf

<div class="form-group">
  <label for="name">Name</label>
  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
    value="{{ old('name', $user->name ?? '') }}" required>
  @error('name') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="email">Email</label>
  <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
    value="{{ old('email', $user->email ?? '') }}" required>
  @error('email') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="password">Password</label>
  <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
  @error('password') <span class="text-danger">{{ $message }}</span> @enderror
  @isset($user)
    <small class="form-text text-muted">Leave blank to keep the current password.</small>
  @endisset
</div>

@php
  $selectedRoleId = old('role_id', isset($user) ? optional($user->roles->first())->id : null);
@endphp
<div class="form-group">
  <label for="role_id">Role</label>
  <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror">
    <option value="">Select a role</option>
    @foreach($roles as $role)
      <option value="{{ $role->id }}" {{ (int) $selectedRoleId === $role->id ? 'selected' : '' }}>
        {{ $role->name }}
      </option>
    @endforeach
  </select>
  @error('role_id') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<button type="submit" class="btn btn-primary">{{ isset($user) ? 'Update' : 'Create' }} User</button>
<a href="{{ route('crm.users.index') }}" class="btn btn-secondary">Cancel</a>
