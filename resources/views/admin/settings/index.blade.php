@extends('layouts.app')

@section('title', 'Global Settings')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Global Settings</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">External Authentication</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.external-auth') }}" method="POST">
                        @csrf
                        <p class="text-muted small">Configure a bridge to authenticate users against an external database like XenForo or a custom website. Users will be automatically imported into this panel on successful login.</p>
                        
                        <div class="form-group custom-control custom-switch mb-4">
                            <input type="checkbox" class="custom-control-input" id="external_auth_enabled" name="external_auth_enabled" value="1" {{ $externalAuth['enabled'] ? 'checked' : '' }}>
                            <label class="custom-control-label" for="external_auth_enabled">Enable External Authentication</label>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bridge Type</label>
                                    <select class="form-control" name="external_auth_type">
                                        <option value="XenForo" {{ $externalAuth['type'] === 'XenForo' ? 'selected' : '' }}>XenForo</option>
                                        <option value="generic" {{ $externalAuth['type'] === 'generic' ? 'selected' : '' }}>Generic Database</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">Database Connection</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Host</label>
                                    <input type="text" class="form-control" name="external_auth_host" value="{{ $externalAuth['host'] }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Port</label>
                                    <input type="number" class="form-control" name="external_auth_port" value="{{ $externalAuth['port'] }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Database Name</label>
                                    <input type="text" class="form-control" name="external_auth_database" value="{{ $externalAuth['database'] }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-control" name="external_auth_username" value="{{ $externalAuth['username'] }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" class="form-control" name="external_auth_password" placeholder="Leave blank to keep current">
                                    <small class="text-muted">Only fill if changing.</small>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">Table Mappings</h5>
                        <p class="text-muted small">Define which columns in your external database map to our required fields. For XenForo, the defaults usually work perfectly.</p>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Table Name</label>
                                    <input type="text" class="form-control" name="external_auth_table" value="{{ $externalAuth['table'] }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>ID Column</label>
                                    <input type="text" class="form-control" name="external_auth_col_id" value="{{ $externalAuth['col_id'] }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Username Column</label>
                                    <input type="text" class="form-control" name="external_auth_col_username" value="{{ $externalAuth['col_username'] }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email Column</label>
                                    <input type="text" class="form-control" name="external_auth_col_email" value="{{ $externalAuth['col_email'] }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password Column</label>
                                    <input type="text" class="form-control" name="external_auth_col_password" value="{{ $externalAuth['col_password'] }}" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
