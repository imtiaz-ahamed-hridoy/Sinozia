@extends('backend.app')
@section('title', 'System Settings')
@push('style')
@endpush
@section('page-content')
<x-breadcrumbs title="System Settings" subtitle="System Setting Info" />

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="edit-profile">
        <div class="row">
            <div class="col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <x-table-header title="System Settings" subtitle="Configure your system settings" />

                    <div class="card-body">
                        <div class="card-wrapper border rounded-3">
                            <form method="POST" action="{{ route('system.update') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">System Name</label>
                                        <input class="form-control @error('system_name') is-invalid @enderror" type="text" name="system_name" placeholder="Enter your system name" value="{{ old('system_name', $setting->system_name ?? '') }}">
                                        @error('system_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Support Email</label>
                                        <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" placeholder="Enter your support email" value="{{ old('email', $setting->email ?? '') }}">
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Copyright Text</label>
                                        <input class="form-control @error('copyright_text') is-invalid @enderror" type="text" name="copyright_text" placeholder="Enter your copyright text" value="{{ old('copyright_text', $setting->copyright_text ?? '') }}">
                                        @error('copyright_text')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Logo</label>
                                        <input class="dropify form-control @error('logo') is-invalid @enderror" type="file" name="logo" placeholder="Enter your logo" data-default-file="{{ asset($setting->logo ?? 'backend/assets/images/image_placeholder.png') }}">
                                        @error('logo')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Logo Dark</label>
                                        <input class="dropify form-control @error('logo_dark') is-invalid @enderror" type="file" name="logo_dark" placeholder="Enter your dark logo" data-default-file="{{ asset($setting->logo_dark ?? 'backend/assets/images/image_placeholder.png') }}">
                                        @error('logo_dark')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Favicon</label>
                                        <input class="dropify form-control @error('favicon') is-invalid @enderror" type="file" name="favicon" placeholder="Enter your favicon" data-default-file="{{ asset($setting->favicon ?? 'backend/assets/images/image_placeholder.png') }}">
                                        @error('favicon')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-footer">
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</a>
                                    <button class="btn btn-primary btn-block"><i class="fa fa-save"></i> Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->

@endsection
