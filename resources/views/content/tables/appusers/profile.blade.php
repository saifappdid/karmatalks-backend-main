@extends('layouts/contentLayoutMaster')

@section('title', 'User Profile')

@section('page-style')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .profile-card {
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            background: #fff;
            padding: 25px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .profile-header img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
        }

        .info-value {
            color: #212529;
            font-size: 15px;
        }
    </style>
@endsection

@section('content')
    <section>
        <div class="row">
            <div class="col-lg-12 col-md-112 col-12 mx-auto">
                <div class="profile-card">
                    <div class="profile-header">
                        <img src="{{ $user->image ?? asset('images/avatar-default.png') }}" alt="User Avatar">
                        <div>
                            <h4 class="mb-0">{{ $user->name }}</h4>
                            <p class="text-muted mb-0">{{ $user->email }}</p>
                            <span class="badge bg-success text-capitalize">{{ $user->status }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-label">Phone</div>
                            <div class="info-value">{{ $user->phone ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Gender</div>
                            <div class="info-value">{{ ucfirst($user->gender ?? '-') }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-label">Date of Birth</div>
                            <div class="info-value">{{ $user->dob ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Wallet Balance</div>
                            <div class="info-value">₹{{ number_format($user->wallet, 2) }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="info-label">Address</div>
                        <div class="info-value">{{ $user->address ?? '-' }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-label">Device ID</div>
                            <div class="info-value">{{ $user->device_id ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Last Updated</div>
                            <div class="info-value">{{ $user->updated_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mt-1">
                <div class="profile-card">
                    <h4 class="text-center">Bookings</h4>
                    <livewire:booking-table user="{{ $user->id }}" />
                </div>
            </div>
            <div class="col-lg-12 mt-1">
                <div class="profile-card">
                    <h4 class="text-center">Wallet Transctions</h4>
                    <livewire:wallet-transaction-table user="{{ $user->id }}" />
                </div>
            </div>
            <div class="col-lg-12 mt-1">
                <div class="profile-card">
                    <h4 class="text-center">Instant Consultation History</h4>
                    <livewire:instant-consultation-history-table user="{{ $user->id }}" />
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page-script')
    <script></script>
@endsection
