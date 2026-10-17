@extends('backend.app')
@section('title', 'Dashboard')
@section('page-content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-xl-4 col-sm-7 box-col-3">
                    <h3>Dashboard</h3>
                </div>
                <div class="col-5 d-none d-xl-block">

                </div>
                <div class="col-xl-3 col-sm-5 box-col-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">

                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                    fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                    <path d="M10 12h4v4h-4z" />
                                </svg>
                            </a></li>
                        <li class="breadcrumb-item">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid default-dashboard">
        <div class="row">
            <div class="col-xl-6 box-col-7 proorder-md-1">
                <div class="card">
                    <div class="card-body premium-card">
                        <div class="row premium-courses-card">
                            <div class="col-md-5 premium-course">
                                <h1 class="f-w-700">Welcome to admin dashboard mr, {{ auth()->user()->name }}.</h1><span
                                    class="f-light f-w-400 f-14">Upgrade to watch, play, learn, make and discover.
                                </span><a class="btn btn-square btn-primary f-w-700"
                                    href="{{ route('profile.setting') }}">Go Profile</a>
                            </div>
                            <div class="col-md-7 premium-course-img">
                                <div class="premium-message"><img class="img-fluid"
                                        src="{{ asset('backend/assets/images/dashboard/massage.gif') }}" alt="massage">
                                </div>
                                <div class="premium-books"><img class="img-fluid"
                                        src="{{ asset('backend/assets/images/dashboard/books.gif') }}" alt="books">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--  <div class="col-xl-3 box-col-5 col-md-6 proorder-md-2">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="header-top">
                            <h4>Recent Activity</h4>
                            <div class="dropdown icon-dropdown setting-menu">
                                <button class="btn dropdown-toggle" id="userdropdown1" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                        viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                        <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                    </svg>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userdropdown1"><a
                                        class="dropdown-item" href="#">Weekly </a><a class="dropdown-item"
                                        href="#">Monthly </a><a class="dropdown-item" href="#">Yearly </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="activity-day">
                            <h6>Today</h6>
                        </div>
                        <div class="recent-activity-card">
                            <ul>
                                <li class="activity-removed">
                                    <div class="recent-activity-data">
                                        <div class="activity-name"><span>

                                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                                    viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 11v.01" />
                                                    <path d="M8 11v.01" />
                                                    <path d="M16 11v.01" />
                                                    <path
                                                        d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3z" />
                                                </svg>
                                            </span><a href="letter-box.html">Nick Mark mentioned Sara</a></div>
                                        <div class="view-btn"><a href="#!">View
                                                <i class="fa fa-angle-right ms-2"></i></a></div>
                                    </div>
                                </li>
                                <li>
                                    <div class="recent-activity-data">
                                        <div class="activity-name"> <span>

                                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                                    viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                    <path d="M7 11l5 5l5 -5" />
                                                    <path d="M12 4l0 12" />
                                                </svg>
                                            </span><a href="letter-box.html">The Post name was removed from the
                                                blog.</a>
                                        </div>
                                        <div class="view-btn"><a href="#!">View
                                                <i class="fa fa-angle-right ms-2"></i></a></div>
                                    </div>
                                </li>
                                <li>
                                    <div class="recent-activity-data">
                                        <div class="activity-name"><span>

                                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                    viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 8l4 -4l4 4" />
                                                    <path d="M7 4l0 9" />
                                                    <path d="M13 16l4 4l4 -4" />
                                                    <path d="M17 10l0 10" />
                                                </svg>
                                            </span><a href="letter-box.html">Patrick Sullivan published a new
                                                post.</a>
                                        </div>
                                        <div class="view-btn"><a href="#!">View
                                                <i class="fa fa-angle-right ms-2"></i></a></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="activity-day mt-4">
                            <h6>Yesterday</h6>
                        </div>
                        <div class="recent-activity-card">
                            <ul>
                                <li>
                                    <div class="recent-activity-data">
                                        <div class="activity-name"><span>

                                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                                    viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M10 14l11 -11" />
                                                    <path
                                                        d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                                </svg>
                                            </span><a href="letter-box.html">Nick Mark mentioned Sara</a></div>
                                        <div class="view-btn"><a href="#!">View
                                                <i class="fa fa-angle-right ms-2"></i></a></div>
                                    </div>
                                </li>
                                <li>
                                    <div class="recent-activity-data">
                                        <div class="activity-name"><span>

                                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                                    viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path
                                                        d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                                    <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                                                    <path d="M12 12l0 .01" />
                                                    <path d="M3 13a20 20 0 0 0 18 0" />
                                                </svg>
                                            </span><a href="letter-box.html">The Post name was removed from the
                                                blog.</a>
                                        </div>
                                        <div class="view-btn"><a href="#!">View
                                                <i class="fa fa-angle-right ms-2"></i></a></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 proorder-md-3 box-col-6">
                <div class="card">
                    <div class="card-header custom-border-bottom">
                        <div class="header-top">
                            <h4>Sales Stats</h4>
                            <div class="dropdown icon-dropdown setting-menu">
                                <button class="btn dropdown-toggle" id="userdropdown2" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                        viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                        <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                    </svg>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userdropdown2"><a
                                        class="dropdown-item" href="#">Weekly</a><a class="dropdown-item"
                                        href="#">Monthly </a><a class="dropdown-item" href="#">Yearly </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div id="salesStatsRadialChart"> </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-6 proorder-md-5">
                <div class="card latest-sales">
                    <div class="card-header d-flex justify-content-between align-items-center custom-border-bottom">
                        <h4>Latest Sales</h4>
                        <div class="d-flex align-items-center justify-content-center latest-sales-btn">
                            <button class="btn btn-square btn-outline-primary btn-sm" type="button"
                                title="btn btn-square btn-outline-primary btn-sm">Yearly</button>
                            <button class="btn btn-square btn-outline-primary btn-sm" type="button"
                                title="btn btn-square btn-outline-primary btn-sm">Monthly</button>
                            <button class="btn btn-square btn-outline-primary btn-sm active" type="button"
                                title="btn btn-square btn-outline-primary btn-sm">Weekly</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="lastOrdersChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-12 box-col-12 proorder-md-4">
                <div class="card">
                    <div class="card-header card-no-border">
                        <div class="header-top">
                            <h4>Last Orders </h4>
                            <div class="dropdown icon-dropdown setting-menu">
                                <button class="btn dropdown-toggle" id="userdropdown3" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                        viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                        <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                    </svg>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userdropdown3"><a
                                        class="dropdown-item" href="#">Weekly</a><a class="dropdown-item"
                                        href="#">Monthly </a><a class="dropdown-item" href="#">Yearly</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive custom-scrollbar">
                            <table class="last-orders-table table" id="last-orders">
                                <thead>
                                    <tr>
                                        <th>Name </th>
                                        <th>Order No. </th>
                                        <th>Amount</th>
                                        <th>Payment Type </th>
                                        <th>Date</th>
                                        <th>Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="user-data">
                                                <div><img src="{{ asset('backend/assets/images/dashboard/avtar/2.jpg') }}"
                                                        alt="avatar">
                                                </div>
                                                <div> <a href="user-profile.html">
                                                        <h4>Dmitriy Groshev</h4>
                                                    </a><span>Switzerland</span></div>
                                            </div>
                                        </td>
                                        <td>#790841</td>
                                        <td>$2.499</td>
                                        <td>Credit Card</td>
                                        <td>1 Oct, 14:43</td>
                                        <td>
                                            <div class="dropdown icon-dropdown">
                                                <button class="btn dropdown-toggle" id="userdropdownes4" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <div class="drop-menu"><i class="icon-more-alt"></i></div>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="userdropdownes4"><a class="dropdown-item"
                                                        href="#">Weekly</a><a class="dropdown-item"
                                                        href="#">Monthly</a><a class="dropdown-item"
                                                        href="#">Yearly</a></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="user-data">
                                                <div><img src="{{ asset('backend/assets/images/dashboard/avtar/17.jpg') }}"
                                                        alt="avatar">
                                                </div>
                                                <div><a href="user-profile.html">
                                                        <h4>Patrick Beverley</h4>
                                                    </a><span> Germany</span></div>
                                            </div>
                                        </td>
                                        <td>#454489</td>
                                        <td>$2.499</td>
                                        <td>Paypal</td>
                                        <td>30 Sep, 23:01</td>
                                        <td>
                                            <div class="dropdown icon-dropdown">
                                                <button class="btn dropdown-toggle" id="userdropdownes1" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <div class="drop-menu"><i class="icon-more-alt"></i></div>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="userdropdownes1"><a class="dropdown-item"
                                                        href="#">Weekly</a><a class="dropdown-item"
                                                        href="#">Monthly</a><a class="dropdown-item"
                                                        href="#">Yearly</a></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="user-data">
                                                <div><img src="{{ asset('backend/assets/images/avtar/18.jpg') }}"
                                                        alt="avatar"></div>
                                                <div><a href="user-profile.html">
                                                        <h4>Kevin Greem</h4>
                                                    </a><span> Canada</span></div>
                                            </div>
                                        </td>
                                        <td>#594579</td>
                                        <td>$2.499</td>
                                        <td>Credit Card</td>
                                        <td>29 Sep,09:31</td>
                                        <td>
                                            <div class="dropdown icon-dropdown">
                                                <button class="btn dropdown-toggle" id="userdropdownes2" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <div class="drop-menu"><i class="icon-more-alt"></i></div>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="userdropdownes2"><a class="dropdown-item"
                                                        href="#">Weekly</a><a class="dropdown-item"
                                                        href="#">Monthly</a><a class="dropdown-item"
                                                        href="#">Yearly</a></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="user-data">
                                                <div><img src="{{ asset('backend/assets/images/dashboard/avtar/19.jpg') }}"
                                                        alt="avatar">
                                                </div>
                                                <div><a href="user-profile.html">
                                                        <h4>William Barton</h4>
                                                    </a><span>United States</span></div>
                                            </div>
                                        </td>
                                        <td>#478495</td>
                                        <td>$2.499</td>
                                        <td>Credit Card</td>
                                        <td>28 Sep, 04:34</td>
                                        <td>
                                            <div class="dropdown icon-dropdown">
                                                <button class="btn dropdown-toggle" id="userdropdownes3" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <div class="drop-menu"><i class="icon-more-alt"></i></div>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="userdropdownes3"><a class="dropdown-item"
                                                        href="#">Weekly</a><a class="dropdown-item"
                                                        href="#">Monthly</a><a class="dropdown-item"
                                                        href="#">Yearly </a></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>  --}}
        </div>
    </div>


@endsection
@push('script')
    <script>
        $(document).ready(function() {

            console.log(window.Echo);

            window.Echo.private('user.1')
                .listen('.PreviewReady', (e) => {
                    console.log('PreviewReady:', e);
                })
                .listen('.ProjectCompleted', (e) => {
                    console.log('ProjectCompleted:', e);
                })
                .listen('.ProjectFailed', (e) => {
                    console.log('ProjectFailed:', e);
                });

        });
    </script>
@endpush
