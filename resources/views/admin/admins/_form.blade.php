@csrf

<div class="form-group">
  <label for="name">Company / Owner Name</label>
  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
    value="{{ old('name', $admin->name ?? '') }}" required>
  @error('name') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="email">Email</label>
  <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
    value="{{ old('email', $admin->email ?? '') }}" required>
  @error('email') <span class="text-danger">{{ $message }}</span> @enderror
</div>

<div class="form-group">
  <label for="password">Password</label>
  <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
  @error('password') <span class="text-danger">{{ $message }}</span> @enderror
  @isset($admin)
    <small class="form-text text-muted">Leave blank to keep the current password.</small>
  @endisset
</div>

@isset($admin)
  <div class="form-group">
    <label class="d-block">Unique ID</label>
    <code>{{ $admin->unique_id }}</code>
  </div>
@endisset

<button type="submit" class="btn btn-primary">{{ isset($admin) ? 'Update' : 'Create' }} Company</button>
<a href="{{ route('crm.admins.index') }}" class="btn btn-secondary">Cancel</a>
