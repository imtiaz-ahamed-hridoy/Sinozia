
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
                <div><a class="logo" href="index.html"><img class="img-fluid for-light" src="{{ asset($systemSetting->logo ?? 'backend/assets/images/logo/logo.png') }}" alt="looginpage"><img class="img-fluid for-dark" src="{{ asset($systemSetting->logo_dark ?? 'backend/assets/images/logo/logo_dark.png') }}" alt="looginpage"></a></div>
                <div class="login-main">
                  <form class="theme-form" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <h4>Reset Your Password</h4>
                    <div class="form-group">
                      <label class="col-form-label">Enter Your Email</label>
                      <div class="row">
                        <div class="col-8 col-sm-12">
                          <input class="form-control mb-1 @error('email') is-invalid @enderror" type="email" placeholder="Enter Your Valid Email" name="email">
                          @error('email')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                        </div>
                        <div class="col-12">
                          <div class="text-end">
                            <button class="btn btn-primary btn-block m-t-10" type="submit">Sent</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <p class="mt-4 mb-0 text-center">Already have an password?<a class="ms-2" href="{{ route('login') }}">Sign in</a></p>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    @include('backend.partials.script')
  </body>
</html>