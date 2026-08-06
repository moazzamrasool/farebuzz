@extends('layouts.user_dashboard')

@section('title', 'My Profile – FareBuzzer')

@section('content')
<div class="dash-page-title">My Profile</div>

<div class="dash-card">
  <h5 style="font-weight:800;font-size:15px;margin-bottom:16px;">Profile Details</h5>
  <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Avatar</label>
        <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
        @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>
    <button type="submit" class="btn mt-3" style="background:#005fcc;color:#fff;">Save Changes</button>
  </form>
</div>

<div class="dash-card">
  <h5 style="font-weight:800;font-size:15px;margin-bottom:16px;">Change Password</h5>
  <form action="{{ route('user.profile.password') }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Current Password</label>
        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label">New Password</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label">Confirm New Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
      </div>
    </div>
    <button type="submit" class="btn mt-3" style="background:#005fcc;color:#fff;">Change Password</button>
  </form>
</div>
@endsection
