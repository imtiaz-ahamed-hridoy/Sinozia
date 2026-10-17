<!DOCTYPE html>
<html lang="en">

<head>
    @include('backend.partials.style')
    @vite(['resources/js/app.js'])
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
    <!-- page-wrapper Start   -->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- Page Header Start -->
        @include('backend.partials.header')
        <!-- Page Header Ends                              -->
        <!-- Page body Start -->
        <div class="page-body-wrapper">
            <!-- Page Sidebar Start-->

            @include('backend.partials.sidebar')

            <div class="page-body">

                @yield('page-content')

            </div>
            <!-- footer start-->
            @include('backend.partials.footer')
        </div>
    </div>
    @yield('modal')
    @include('modal._delete_confirm')

    @include('backend.partials.script')



    @yield('script')
    @stack('script')

    @if (session('success'))
        <script>
            successModal('{{ session('success') }}');
        </script>
    @elseif (session('error'))
        <script>
            errorModal('{{ session('error') }}');
        </script>
    @endif
    @if ($errors->any())
        <script>
            errorModal('{!! implode('<br>', $errors->all()) !!}');
        </script>
    @endif

   
</body>

</html>
