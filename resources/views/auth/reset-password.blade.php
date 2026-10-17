<!DOCTYPE html>
<html lang="en">

<head>
    @include('backend.partials.style')
   <style>
        .login-card{
            background-image: url("{{ asset('backend/assets/images/coming-soon-bg.jpg') }}") !important;
        }
    </style>
</head>

<body>
    <!-- loader starts-->
    <div class="loader-wrapper">
        <div class="theme-loader">
            <div class="loader-p"></div>
        </div>
    </div>
    <!-- loader ends-->
    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="login-card login-dark">
                        <div>
                            <div><a class="logo" href="index.html"><img class="img-fluid for-light"
                                        src="{{ asset($systemSetting->logo ?? 'backend/assets/images/logo/logo.png') }}" alt="looginpage"><img
                                        class="img-fluid for-dark"
                                        src="{{ asset($systemSetting->logo_dark ?? 'backend/assets/images/logo/logo_dark.png') }}" alt="looginpage"></a>
                            </div>
                            <div class="login-main">
                                <form class="theme-form" method="POST" action="{{ route('password.store') }}">
                                    @csrf
                                    <h4>Create Your Password</h4>
                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                    <div class="form-group">
                                        <label class="col-form-label">Email</label>
                                        <div class="form-input position-relative">
                                            <input class="form-control @error('email') is-invalid @enderror"
                                                type="email" name="email" value="{{ $request->email }}"
                                                placeholder="Email" readonly>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-form-label">New Password</label>
                                        <div class="form-input position-relative">
                                            <input class="form-control @error('password') is-invalid @enderror"
                                                type="password" name="password" placeholder="*********">
                                            <div class="show-hide"><span class="show"></span></div>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-form-label">Retype Password</label>
                                        <input class="form-control @error('password_confirmation') is-invalid @enderror"
                                            type="password" name="password_confirmation" placeholder="*********">
                                        @error('password_confirmation')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-0">
                                        <button class="btn btn-primary btn-block w-100" type="submit">Done </button>
                                    </div>
                                    <p class="mt-4 mb-0">Already have an password?<a class="ms-2"
                                            href="{{ route('login') }}">Sign in</a></p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- latest jquery-->
    @include('backend.partials.script')
</body>

</html>
