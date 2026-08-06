@extends('layouts.admin.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
          <div class="col-12">

            @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">WhatsApp Bot Settings</h3>
              </div>
              <div class="card-body">
                <p class="text-muted">
                  Connect your own WhatsApp Business Cloud API credentials from the
                  <a href="https://developers.facebook.com/apps" target="_blank">Meta Developer dashboard</a>
                  to let the AI bot collect leads on WhatsApp for your company.
                </p>

                <form action="{{ route('crm.whatsapp-settings.update') }}" method="POST">
                  @csrf
                  @method('PUT')

                  <div class="form-group">
                    <label for="phone_number_id">Phone Number ID</label>
                    <input type="text" name="phone_number_id" id="phone_number_id"
                      class="form-control @error('phone_number_id') is-invalid @enderror"
                      value="{{ old('phone_number_id', $setting->phone_number_id) }}">
                    @error('phone_number_id') <span class="text-danger">{{ $message }}</span> @enderror
                  </div>

                  <div class="form-group">
                    <label for="business_account_id">WhatsApp Business Account ID</label>
                    <input type="text" name="business_account_id" id="business_account_id"
                      class="form-control @error('business_account_id') is-invalid @enderror"
                      value="{{ old('business_account_id', $setting->business_account_id) }}">
                    @error('business_account_id') <span class="text-danger">{{ $message }}</span> @enderror
                  </div>

                  <div class="form-group">
                    <label for="access_token">Access Token</label>
                    <input type="password" name="access_token" id="access_token"
                      class="form-control @error('access_token') is-invalid @enderror"
                      placeholder="{{ $setting->access_token ? '•••••••• (saved — leave blank to keep)' : '' }}"
                      autocomplete="off">
                    @error('access_token') <span class="text-danger">{{ $message }}</span> @enderror
                  </div>

                  <div class="form-group form-check">
                    <input type="checkbox" name="enabled" id="enabled" class="form-check-input" value="1"
                      {{ old('enabled', $setting->enabled) ? 'checked' : '' }}>
                    <label class="form-check-label" for="enabled">Bot enabled</label>
                  </div>

                  <button type="submit" class="btn btn-primary">Save</button>
                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
</div>
@endsection
