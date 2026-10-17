@extends('backend.app')
@section('title', 'Mail Settings')
@push('style')
<style>
    .show-hide {
        position: absolute;
        right: 20px;
        top: 19px;
        transform: translateY(-50%);
    }

</style>
@endpush
@section('page-content')

<x-breadcrumbs title="Mail Settings" subtitle="" />

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <x-table-header title="Mail Settings" subtitle="Configure your mail settings" />

                <div class="card-body">
                    <div class="card-wrapper border rounded-3">
                        <form class="row g-3" method="POST" action="{{ route('mail.update') }}">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label" for="mail_mailer">MAIL MAILER:</label>
                                <input class="form-control input-air-primary @error('mail_mailer') is-invalid @enderror" id="mail_mailer" type="text" placeholder="Enter Your Mail Mailer" name="mail_mailer" value="{{ config('mail.mailers.smtp.transport') }}">

                                @error('mail_mailer')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail_host">MAIL HOST:</label>
                                <input class="form-control input-air-primary @error('mail_host') is-invalid @enderror" id="mail_host" type="text" placeholder="Enter Your Mail Host" name="mail_host" value="{{ config('mail.mailers.smtp.host') }}">

                                @error('mail_host')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail_port">MAIL PORT:</label>
                                <input class="form-control input-air-primary @error('mail_port') is-invalid @enderror" id="mail_port" type="text" placeholder="Enter Your Mail Port" name="mail_port" value="{{ config('mail.mailers.smtp.port') }}">

                                @error('mail_port')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail_username">MAIL USERNAME:</label>
                                <input class="form-control input-air-primary @error('mail_username') is-invalid @enderror" id="mail_username" type="email" placeholder="Enter Your Mail Username" name="mail_username" value="{{ config('mail.mailers.smtp.username') }}">

                                @error('mail_username')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail_password">MAIL PASSWORD:</label>
                                <input class="form-control input-air-primary @error('mail_password') is-invalid @enderror" id="mail_password" type="password" placeholder="Enter Your Mail Password" name="mail_password" value="{{ config('mail.mailers.smtp.password') }}">

                                @error('mail_password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail_encryption">MAIL ENCRYPTION:</label>
                                <input class="form-control input-air-primary @error('mail_encryption') is-invalid @enderror" id="mail_encryption" type="text" placeholder="Enter Your Mail Encryption" name="mail_encryption" value="{{ config('mail.mailers.smtp.encryption') }}">

                                @error('mail_encryption')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="mail_from_address">MAIL FROM ADDRESS:</label>
                                <input class="form-control input-air-primary @error('mail_from_address') is-invalid @enderror" id="mail_from_address" type="email" placeholder="Enter Your Mail From Address" name="mail_from_address" value="{{ config('mail.from.address') }}">

                                @error('mail_from_address')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</a>
                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Update Settings</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<!-- Container-fluid Ends-->
@endsection

@push('script')
@endpush
