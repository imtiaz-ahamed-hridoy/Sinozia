 <!-- latest jquery-->
 {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>  --}}

 <script src="{{ asset('backend/assets/js/jquery.min.js') }}"></script>
 <!-- Bootstrap js-->
 <script src="{{ asset('backend/assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
 <!-- feather icon js-->
 <script src="{{ asset('backend/assets/js/icons/feather-icon/feather.min.js') }}"></script>
 <script src="{{ asset('backend/assets/js/icons/feather-icon/feather-icon.js') }}"></script>
 <!-- scrollbar js-->
 <script src="{{ asset('backend/assets/js/scrollbar/simplebar.js') }}"></script>
 <script src="{{ asset('backend/assets/js/scrollbar/custom.js') }}"></script>
 <!-- Sidebar jquery-->
 <script src="{{ asset('backend/assets/js/config.js') }}"></script>
 <!-- Plugins JS start-->
 <script src="{{ asset('backend/assets/js/sidebar-menu.js') }}"></script>
 <script src="{{ asset('backend/assets/js/sidebar-pin.js') }}"></script>
 <script src="{{ asset('backend/assets/js/slick/slick.min.js') }}"></script>
 <script src="{{ asset('backend/assets/js/slick/slick.js') }}"></script>
 <script src="{{ asset('backend/assets/js/header-slick.js') }}"></script>
 <script src="{{ asset('backend/assets/js/chart/morris-chart/raphael.js') }}"></script>
 <script src="{{ asset('backend/assets/js/chart/morris-chart/morris.js') }}"></script>
 <script src="{{ asset('backend/assets/js/chart/morris-chart/prettify.min.js') }}"></script>
 <script src="{{ asset('backend/assets/js/chart/apex-chart/apex-chart.js') }}"></script>
 <script src="{{ asset('backend/assets/js/chart/apex-chart/stock-prices.js') }}"></script>
 <script src="{{ asset('backend/assets/js/chart/apex-chart/moment.min.js') }}"></script>
 <script src="{{ asset('backend/assets/js/notify/bootstrap-notify.min.js') }}"></script>
 <script src="{{ asset('backend/assets/js/dashboard/default.js') }}"></script>
 <script src="{{ asset('backend/assets/js/notify/index.js') }}"></script>
 <script src="{{ asset('backend/assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
 <script src="{{ asset('backend/assets/js/datatable/datatables/datatable.custom.js') }}"></script>
 <script src="{{ asset('backend/assets/js/datatable/datatables/datatable.custom1.js') }}"></script>
 <script src="{{ asset('backend/assets/js/owlcarousel/owl.carousel.js') }}"></script>
 <script src="{{ asset('backend/assets/js/owlcarousel/owl-custom.js') }}"></script>
 {{-- <script src="{{ asset('backend/assets/js/typeahead/handlebars.js') }}"></script>
 <script src="{{ asset('backend/assets/js/typeahead/typeahead.bundle.js') }}"></script>
 <script src="{{ asset('backend/assets/js/typeahead/typeahead.custom.js') }}"></script>
 <script src="{{ asset('backend/assets/js/typeahead-search/handlebars.js') }}"></script>
 <script src="{{ asset('backend/assets/js/typeahead-search/typeahead-custom.js') }}"></script> --}}
 <script src="{{ asset('backend/assets/js/height-equal.js') }}"></script>

 <!-- Theme js-->
 <script src="{{ asset('backend/assets/js/toastr.min.js') }}"></script>
 <script src="{{ asset('backend/assets/js/dropify.min.js') }}"></script>
 <script src="{{ asset('backend/assets/js/script.js') }}"></script>
 <script src="{{ asset('backend/assets/js/summernote.js') }}"></script>
 {{-- <script src="{{ asset('backend/assets/js/theme-customizer/customizer.js') }}"></script> --}}
 <!-- Plugin used-->

 {{-- For ajax --}}
 @include('backend.ajax.masterAjax')


 {{-- toastr start --}}
 <script>
     $(document).ready(function() {
         toastr.options.timeOut = 10000;
         toastr.options.positionClass = 'toast-top-right';

         @if(Session::has('t-success'))
         toastr.options = {
             'closeButton': true
             , 'debug': false
             , 'newestOnTop': true
             , 'progressBar': true
             , 'positionClass': 'toast-top-right'
             , 'preventDuplicates': false
             , 'showDuration': '1000'
             , 'hideDuration': '1000'
             , 'timeOut': '5000'
             , 'extendedTimeOut': '1000'
             , 'showEasing': 'swing'
             , 'hideEasing': 'linear'
             , 'showMethod': 'fadeIn'
             , 'hideMethod': 'fadeOut'
         , };
         toastr.success("{{ session('t-success') }}");
         @endif

         @if(Session::has('t-error'))
         toastr.options = {
             'closeButton': true
             , 'debug': false
             , 'newestOnTop': true
             , 'progressBar': true
             , 'positionClass': 'toast-top-right'
             , 'preventDuplicates': false
             , 'showDuration': '1000'
             , 'hideDuration': '1000'
             , 'timeOut': '5000'
             , 'extendedTimeOut': '1000'
             , 'showEasing': 'swing'
             , 'hideEasing': 'linear'
             , 'showMethod': 'fadeIn'
             , 'hideMethod': 'fadeOut'
         , };
         toastr.error("{{ session('t-error') }}");
         @endif

         @if(Session::has('t-info'))
         toastr.options = {
             'closeButton': true
             , 'debug': false
             , 'newestOnTop': true
             , 'progressBar': true
             , 'positionClass': 'toast-top-right'
             , 'preventDuplicates': false
             , 'showDuration': '1000'
             , 'hideDuration': '1000'
             , 'timeOut': '5000'
             , 'extendedTimeOut': '1000'
             , 'showEasing': 'swing'
             , 'hideEasing': 'linear'
             , 'showMethod': 'fadeIn'
             , 'hideMethod': 'fadeOut'
         , };
         toastr.info("{{ session('t-info') }}");
         @endif

         @if(Session::has('t-warning'))
         toastr.options = {
             'closeButton': true
             , 'debug': false
             , 'newestOnTop': true
             , 'progressBar': true
             , 'positionClass': 'toast-top-right'
             , 'preventDuplicates': false
             , 'showDuration': '1000'
             , 'hideDuration': '1000'
             , 'timeOut': '5000'
             , 'extendedTimeOut': '1000'
             , 'showEasing': 'swing'
             , 'hideEasing': 'linear'
             , 'showMethod': 'fadeIn'
             , 'hideMethod': 'fadeOut'
         , };
         toastr.warning("{{ session('t-warning') }}");
         @endif
     });

 </script>
 {{-- toastr end --}}

 {{-- dropify start --}}
 <script>
     $(document).ready(function() {
         $('.dropify').dropify();
     });

 </script>
 {{-- dropify end --}}

 {{-- summernote start --}}
 <script>
     $('#summernote').summernote({
         placeholder: 'Enter your content here...'
         , tabsize: 2
         , height: 200
         , toolbar: [
             ['style', ['style']]
             , ['font', ['bold', 'underline', 'clear']]
             , ['color', ['color']]
             , ['para', ['ul', 'ol', 'paragraph']]
             , ['table', ['table']]
             , ['insert', ['link', 'picture', 'video']]
             , ['view', ['fullscreen', 'codeview', 'help']]
         ]
     });

 </script>
 {{-- summernote end --}}


 {{-- stack('script') removed from here as it is handled by the main app layout. --}}
