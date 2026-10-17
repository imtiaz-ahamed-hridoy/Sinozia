@extends('backend.app')
@section('title', 'User Profile')
@push('style')
<style>
    .social-profile .social-img-wrap {
        position: relative;
        width: 130px;
        margin: 20px auto;
    }

    .social-profile .social-img {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        overflow: hidden;
        border: 5px solid #fff;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .social-profile .social-img:hover {
        transform: scale(1.03);
    }

    .social-profile .social-img img,
    .social-profile .social-img .initials-avatar {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        object-fit: cover;
        font-size: 38px;
        font-weight: 700;
        color: #1f2937;
        text-transform: uppercase;
    }

    .social-profile .active-badge {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #00ca72 0%, #00ab55 100%);
        border-radius: 50%;
        border: 3px solid #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
        box-shadow: 0 4px 15px rgba(0, 171, 85, 0.4);
        z-index: 5;
        animation: active-pulse 2s infinite;
    }

    @keyframes active-pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(0, 202, 114, 0.7);
        }

        70% {
            box-shadow: 0 0 0 12px rgba(0, 202, 114, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(0, 202, 114, 0);
        }
    }

    .card.social-profile {
        border-radius: 30px;
        overflow: hidden;
        border: none;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        background: #fff;
    }

    .social-details h5 a {
        color: #1a202c;
        font-weight: 800;
        font-size: 22px;
        text-decoration: none;
    }

</style>
@endpush
@section('page-content')

<x-breadcrumbs title="User Profile" subtitle="Details" />

<div class="container-fluid">
    <div class="row justify-content-left">
        <div class="col-xl-6 col-md-8">
            <div class="card social-profile">
                <div class="card-body text-center p-5">
                    <div class="social-img-wrap">
                        <div class="social-img">
                            @php
                            $name = trim($user->name);
                            $firstLetter = $name !== '' ? mb_substr($name, 0, 1) : '';
                            @endphp

                            @if ($user->avatar)
                            <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}">
                            @else
                            <div class="initials-avatar">{{ $firstLetter }}</div>
                            @endif
                        </div>
                        @if($user->is_active)
                        <div class="active-badge shadow-lg">
                            <i class="fa fa-check"></i>
                        </div>
                        @endif
                    </div>
                    <div class="social-details">
                        <h5 class="mb-1"><span>{{ $user->name }}</span></h5><span class="f-light">{{ $user->email }}</span>
                        {{-- <ul class="card-social">
                            <li><a href="{{  $user->facebook_url ? $user->facebook_url : 'https://www.facebook.com/' }}" target="_blank"><i class="fa fa-facebook"></i></a></li>

                        <li><a href="{{ $user->google_plus_url ? $user->google_plus_url : 'https://plus.google.com/' }}" target="_blank"><i class="fa fa-google-plus"></i></a></li>

                        <li><a href="{{ $user->twitter_url ? $user->twitter_url : 'https://twitter.com/' }}" target="_blank"><i class="fa fa-twitter"></i></a></li>

                        <li><a href="{{ $user->instagram_url ? $user->instagram_url : 'https://www.instagram.com/' }}" target="_blank"><i class="fa fa-instagram"></i></a></li>

                        <li><a href="{{ $user->rss_url ? $user->rss_url : 'https://rss.app/' }}" target="_blank"><i class="fa fa-rss"></i></a></li>

                        </ul> --}}
                        <ul class="social-follow">
                            <li>
                                <h5 class="mb-0">{{ $user->credits->credits ?? 0 }}</h5><span class="f-light">Total Credits</span>

                            </li>
                            <li>
                                <h5 class="mb-0">{{ $user->created_at->format('j M, Y') }}</h5><span class="f-light">Account Created</span>

                            </li>
                            <li>
                                <h5 class="mb-0">{{ ucfirst($user->plan_type ?? 'free') }}</h5><span class="f-light">Plan Type</span>
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-left">
        <div class="col-xl-12 col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">User Payments History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-scrollbar">
                        <div id="basic-1_wrapper" class="dataTables_wrapper no-footer">
                            <table class="dataTable no-footer hover" id="basic-1" role="grid" aria-describedby="basic-1_info">
                                <thead>
                                    <tr role="row" class="border-bottom">
                                        <th>#</th>
                                        <th>Credit</th>
                                        <th>Transaction ID</th>
                                        <th>Payment Method</th>
                                        <th>Currency</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Purchased At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->paymentHistories as $key => $payment)
                                    <tr role="row" class="odd">
                                        <td class="sorting_1">{{ $key + 1 }}</td>
                                        <td>{{ $payment->credit->credit ?? 0 }} Credit</td>
                                        <td>{{ $payment->stripe_charge_id }}</td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td>{{ $payment->currency }}</td>
                                        <td>{{ $payment->amount }} €</td>
                                        <td>
                                            <span class="badge bg-{{ $payment->status === 'paid' ? 'success' : 'danger' }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->created_at->format('j M, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mt-5 float-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->
@endsection
