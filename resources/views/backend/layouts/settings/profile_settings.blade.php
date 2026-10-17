@extends('backend.app')
@section('title', 'Edit Profile')
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

<x-breadcrumbs title="Profile" subtitle="Edit Profile" />


<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="edit-profile">
        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h4 class="card-title mb-0">My Profile</h4>
                        <div class="card-options"><a class="card-options-collapse" href="#" data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a class="card-options-remove" href="#" data-bs-toggle="card-remove"><i class="fe fe-x"></i></a></div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="profile-title">
                                <div class="d-flex align-items-center gap-3">
                                    <!-- Profile Image -->
                                    <div class="position-relative">
                                        <img class="rounded-circle profile-picture" alt="" src="{{ $userDetails->avatar ? asset($userDetails->avatar) : asset('backend/assets/images/user/profile.jpeg') }}" style="width: 70px; height: 70px; object-fit: cover;">

                                        <!-- Upload Button Icon on Image -->
                                        <div class="position-absolute bottom-0 end-0">
                                            <input type="file" name="profile_picture" id="profile_picture_input" hidden>
                                            <label for="profile_picture_input" class="bg-light rounded-circle shadow-sm" style="cursor: pointer;  width: 23px; display: flex; height: 23px; justify-content: center; align-items: center;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b9294" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cloud-up">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M12 18.004h-5.343c-2.572 -.004 -4.657 -2.011 -4.657 -4.487c0 -2.475 2.085 -4.482 4.657 -4.482c.393 -1.762 1.794 -3.2 3.675 -3.773c1.88 -.572 3.956 -.193 5.444 1c1.488 1.19 2.162 3.007 1.77 4.769h.99c1.38 0 2.57 .811 3.128 1.986" />
                                                    <path d="M19 22v-6" />
                                                    <path d="M22 19l-3 -3l-3 3" />
                                                </svg>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- User Details -->
                                    <div class="flex-grow-1">
                                        <h3 class="mb-1 f-w-600">{{ $userDetails->name }}</h3>
                                        <p class="mb-0 text-muted">{{ $userDetails->role }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-wrapper border rounded-3">
                            <form method="POST" action="{{ route('update.profile') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" placeholder="Enter your name" value="{{ $userDetails->name }}">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email-Address</label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" placeholder="Enter your Email" value="{{ $userDetails->email }}">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
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
            <div class="col-xl-8 card">
                <div class="card-header pb-0">
                    <h4 class="card-title mb-0">Update Password</h4>
                    <div class="card-options"><a class="card-options-collapse" href="#" data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a class="card-options-remove" href="#" data-bs-toggle="card-remove"><i class="fe fe-x"></i></a></div>
                </div>

                <div class="card-body">
                    <div class="card-wrapper border rounded-3">
                        <form method="POST" action="{{ route('update.password') }}">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Current Password</label>
                                            <div class="form-input position-relative">
                                                <input class="form-control @error('password') is-invalid @enderror" type="password" placeholder="Enter your current password" name="password">
                                                @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                                <div class="show-hide"><span class="show"> </span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <div class="form-input position-relative">
                                                <input class="form-control @error('new_password') is-invalid @enderror" type="password" placeholder="Enter your new password" name="new_password">
                                                @error('new_password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                                {{-- <div class="show-hide"><span class="show"> </span></div>  --}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Confirm Password</label>
                                            <div class="form-input position-relative">
                                                <input class="form-control @error('new_password_confirmation') is-invalid @enderror" type="password" placeholder="Enter your confirm password" name="new_password_confirmation">
                                                @error('new_password_confirmation')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                                {{-- <div class="show-hide hide-show"><span class="show"> </span></div>  --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-edit"></i> Update Password</button>
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
<script>
    $(document).ready(function() {
        $('#profile_picture_input').change(function() {

            const file = $(this)[0].files[0];
            if (!file) {
                toastr.error('Please select an image first.');
                return;
            }

            const formData = new FormData();
            formData.append('profile_picture', file);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route('update.profile.picture') }}'
                , type: 'POST'
                , data: formData
                , processData: false
                , contentType: false
                , success: function(data) {
                    if (data.success) {
                        // Prevent image caching
                        let newImageUrl = data.image_url + '?v=' + new Date().getTime();
                        $('.profile-picture').attr('src', newImageUrl);

                        toastr.success('Profile picture updated successfully.');
                    } else {
                        toastr.error(data.message);
                    }
                }
                , error: function(xhr) {
                    toastr.error('Something went wrong!');
                    console.log(xhr.responseText);
                }
            });
        });
    });

</script>
@endpush
