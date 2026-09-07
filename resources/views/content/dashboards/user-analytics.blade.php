@extends('layouts/contentLayoutMaster')

@section('title', 'User Analytics Dashboard')

@section('page-style')
    @include('content.dashboards.partials.analytics-styles')
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Header --}}
        <div class="dashboard-header purple">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1><span class="material-symbols-outlined me-2">group</span> User Analytics Dashboard</h1>
                    <p>Comprehensive insights into user behavior, growth, and engagement</p>
                </div>
                <a href="{{ route('admin.home.index') }}" class="btn btn-light">
                    <span class="material-symbols-outlined">arrow_back</span> Back to Main Dashboard
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <div class="filter-section">
            <form action="" method="GET" class="row align-items-end">
                <div class="col-md-4 mb-2">
                    <label class="form-label fw-bold">Date Range</label>
                    <input type="text" id="date_range" name="date_range" class="form-control"
                        placeholder="Select date range" value="{{ request('date_range') }}" readonly>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-bold">Quick Select</label>
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="">All Time</option>
                        <option value="7days" {{ request('period') == '7days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30days" {{ request('period') == '30days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="90days" {{ request('period') == '90days' ? 'selected' : '' }}>Last 90 Days</option>
                        <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Last Year</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <span class="material-symbols-outlined">filter_alt</span> Apply
                    </button>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('admin.analytics.users') }}" class="btn btn-outline-secondary w-100">
                        <span class="material-symbols-outlined">refresh</span> Reset Filters
                    </a>
                </div>
            </form>
        </div>

        {{-- Key Metrics --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card primary">
                    <div class="stat-icon"><span class="material-symbols-outlined">group</span></div>
                    <div class="stat-value">{{ number_format($totalUsers) }}</div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card success">
                    <div class="stat-icon"><span class="material-symbols-outlined">verified_user</span></div>
                    <div class="stat-value">{{ number_format($activeUsers) }}</div>
                    <div class="stat-label">Active Users</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card info">
                    <div class="stat-icon"><span class="material-symbols-outlined">person_add</span></div>
                    <div class="stat-value">{{ number_format($newUsersToday) }}</div>
                    <div class="stat-label">New Users Today</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card warning">
                    <div class="stat-icon"><span class="material-symbols-outlined">trending_up</span></div>
                    <div class="stat-value">{{ $retentionRate }}%</div>
                    <div class="stat-label">Retention Rate</div>
                </div>
            </div>
        </div>

        {{-- New User Stats Row --}}
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="analytics-card">
                    <div class="quick-stat">
                        <div class="icon" style="background: #e3f2fd; color: #1976d2;">
                            <span class="material-symbols-outlined">calendar_month</span>
                        </div>
                        <div>
                            <div class="fw-bold h4 mb-0">{{ number_format($newUsersThisWeek) }}</div>
                            <small class="text-muted">New Users This Week</small>
                        </div>
                    </div>
                    <div class="quick-stat">
                        <div class="icon" style="background: #f3e5f5; color: #7b1fa2;">
                            <span class="material-symbols-outlined">calendar_month</span>
                        </div>
                        <div>
                            <div class="fw-bold h4 mb-0">{{ number_format($newUsersThisMonth) }}</div>
                            <small class="text-muted">New Users This Month</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="analytics-card">
                    <div class="quick-stat">
                        <div class="icon" style="background: #e8f5e9; color: #388e3c;">
                            <span class="material-symbols-outlined">shopping_bag</span>
                        </div>
                        <div>
                            <div class="fw-bold h4 mb-0">{{ number_format($usersWithBookings) }}</div>
                            <small class="text-muted">Users with Bookings</small>
                        </div>
                    </div>
                    <div class="quick-stat">
                        <div class="icon" style="background: #fff3e0; color: #f57c00;">
                            <span class="material-symbols-outlined">inventory_2</span>
                        </div>
                        <div>
                            <div class="fw-bold h4 mb-0">{{ number_format($usersWithOrders) }}</div>
                            <small class="text-muted">Users with Orders</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="analytics-card">
                    <div class="quick-stat">
                        <div class="icon" style="background: #fce4ec; color: #c2185b;">
                            <span class="material-symbols-outlined">sync</span>
                        </div>
                        <div>
                            <div class="fw-bold h4 mb-0">{{ number_format($returningUsers) }}</div>
                            <small class="text-muted">Returning Users</small>
                        </div>
                    </div>
                    <div class="quick-stat">
                        <div class="icon" style="background: #e0f7fa; color: #0097a7;">
                            <span class="material-symbols-outlined">percent</span>
                        </div>
                        <div>
                            <div class="fw-bold h4 mb-0">{{ $retentionRate }}%</div>
                            <small class="text-muted">Conversion Rate</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">trending_up</span> User Growth Trend
                    </h5>
                    <div class="chart-container">
                        <div id="userGrowthChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">pie_chart</span> Gender Distribution
                    </h5>
                    <div class="chart-container">
                        <div id="genderChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Growth & Country Distribution --}}
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">analytics</span> Monthly User Growth
                    </h5>
                    <div class="chart-container">
                        <div id="monthlyGrowthChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">public</span> Users by Country</h5>
                    <div class="chart-container">
                        <div id="countryChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Users Table --}}
        <div class="row">
            <div class="col-12">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">emoji_events</span> Top Users by
                        Bookings</h5>
                    <div class="table-responsive">
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Total Bookings</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topUsersByBookings as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span>{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $user->email ?? '-' }}</td>
                                        <td>{{ $user->phone ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $user->bookings_count }} bookings</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $user->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($user->status ?? 'N/A') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    @include('content.dashboards.partials.chart-utilities')
    <script>
        // User Growth Chart
        const userGrowthDates = @json($userGrowth->pluck('date')->toArray());
        const userGrowthCounts = @json($userGrowth->pluck('count')->toArray());

        if (userGrowthDates.length > 0) {
            initModernAreaChart({
                selector: '#userGrowthChart',
                categories: userGrowthDates,
                data: userGrowthCounts,
                label: 'New Users',
                color: modernColors.primary
            });
        }

        // Gender Distribution Chart
        const genderLabels = @json($genderDistribution->pluck('gender')->map(fn($g) => ucfirst($g ?? 'Unknown'))->toArray());
        const genderCounts = @json($genderDistribution->pluck('count')->toArray());

        if (genderLabels.length > 0) {
            initModernDonutChart({
                selector: '#genderChart',
                labels: genderLabels,
                data: genderCounts,
                colors: [modernColors.primary, modernColors.pink, modernColors.info, modernColors.success],
                centerLabel: 'Gender'
            });
        }

        // Monthly Growth Chart
        const monthlyLabels = @json($monthlyUserGrowth->pluck('month')->toArray());
        const monthlyCounts = @json($monthlyUserGrowth->pluck('count')->toArray());

        if (monthlyLabels.length > 0) {
            initModernBarChart({
                selector: '#monthlyGrowthChart',
                categories: monthlyLabels,
                data: monthlyCounts,
                label: 'Users',
                colors: [modernColors.purple, modernColors.primary]
            });
        }

        // Country Distribution Chart
        const countryLabels = @json($usersByCountry->pluck('country')->map(fn($c) => $c ?? 'Unknown')->toArray());
        const countryCounts = @json($usersByCountry->pluck('count')->toArray());

        if (countryLabels.length > 0) {
            initModernBarChart({
                selector: '#countryChart',
                categories: countryLabels,
                data: countryCounts,
                label: 'Users',
                colors: [modernColors.success, modernColors.green],
                horizontal: true
            });
        }

        // Date Range Picker
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: @json(request('date_range') ? explode(' to ', request('date_range')) : null),
        });
    </script>
@endsection
