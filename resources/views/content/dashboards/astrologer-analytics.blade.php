@extends('layouts/contentLayoutMaster')

@section('title', 'Astrologer Analytics Dashboard')

@section('page-style')
    @include('content.dashboards.partials.analytics-styles')
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .astro-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: var(--bg-light);
            border-radius: 12px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            border: 1px solid var(--card-border);
        }

        .astro-card:hover {
            background: var(--bg-hover);
            transform: translateX(8px);
        }

        .astro-avatar {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--card-bg);
            box-shadow: var(--shadow-sm);
        }

        .astro-avatar.placeholder {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .astro-info h6 {
            margin: 0;
            font-weight: 600;
            color: var(--text-primary);
        }

        .astro-info small {
            color: var(--text-secondary);
        }

        .astro-stats {
            margin-left: auto;
            text-align: right;
        }

        .performance-table {
            width: 100%;
        }

        .performance-table th {
            background: var(--bg-light);
            font-weight: 600;
            padding: 14px 18px;
            text-align: left;
            border-bottom: 2px solid var(--border-color);
            font-size: 0.85rem;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .performance-table td {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            color: var(--text-primary);
        }

        .performance-table tr:hover td {
            background: var(--bg-hover);
        }

        .badge-service {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .progress-bar-wrapper {
            background: var(--bg-light);
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .rank-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .rank-badge.gold {
            background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
            color: white;
        }

        .rank-badge.silver {
            background: linear-gradient(135deg, #bdc3c7 0%, #95a5a6 100%);
            color: white;
        }

        .rank-badge.bronze {
            background: linear-gradient(135deg, #cd7f32 0%, #b87333 100%);
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Header --}}
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1><span class="material-symbols-outlined me-2">star</span> Astrologer Analytics Dashboard</h1>
                    <p>Monitor astrologer performance, ratings, and consultation metrics</p>
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
                    <button type="submit" class="btn btn-danger w-100">
                        <span class="material-symbols-outlined">filter_alt</span> Apply
                    </button>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('admin.analytics.astrologers') }}" class="btn btn-outline-secondary w-100">
                        <span class="material-symbols-outlined">refresh</span> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Key Metrics --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card primary">
                    <div><span class="material-symbols-outlined">group</span></div>
                    <div class="stat-value">{{ number_format($totalAstrologers) }}</div>
                    <div class="stat-label">Total Astrologers</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card success">
                    <div><span class="material-symbols-outlined">verified_user</span></div>
                    <div class="stat-value">{{ number_format($activeAstrologers) }}</div>
                    <div class="stat-label">Active Astrologers</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card warning">
                    <div><span class="material-symbols-outlined">person_add</span></div>
                    <div class="stat-value">{{ number_format($newAstrologersThisMonth) }}</div>
                    <div class="stat-label">New This Month</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card info">
                    <div><span class="material-symbols-outlined">star</span></div>
                    <div class="stat-value">{{ number_format($avgRating, 1) }}</div>
                    <div class="stat-label">Avg. Rating</div>
                </div>
            </div>
        </div>

        {{-- Top Astrologers Section --}}
        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">emoji_events</span> Top by Bookings</h5>
                    @forelse($topAstrosByBookings->take(5) as $index => $astro)
                        <div class="astro-card">
                            <div class="rank-badge {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}"
                                style="{{ $index > 2 ? 'background: #6c757d; color: white;' : '' }}">
                                {{ $index + 1 }}
                            </div>
                            @if ($astro->profile)
                                <img src="{{ $astro->profile }}" alt="" class="astro-avatar">
                            @else
                                <div class="astro-avatar placeholder">
                                    {{ substr($astro->first_name ?? 'A', 0, 1) }}
                                </div>
                            @endif
                            <div class="astro-info">
                                <h6>{{ $astro->first_name }} {{ $astro->last_name }}</h6>
                                <small>{{ $astro->bookings_count }} bookings</small>
                            </div>
                            <div class="astro-stats">
                                <strong>₹{{ number_format($astro->bookings_sum_final_total ?? 0) }}</strong>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No data available</p>
                    @endforelse
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">star</span> Top by Rating</h5>
                    @forelse($topAstrosByRating->take(5) as $index => $astro)
                        <div class="astro-card">
                            <div class="rank-badge {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}"
                                style="{{ $index > 2 ? 'background: #6c757d; color: white;' : '' }}">
                                {{ $index + 1 }}
                            </div>
                            @if ($astro->profile)
                                <img src="{{ $astro->profile }}" alt="" class="astro-avatar">
                            @else
                                <div class="astro-avatar placeholder">
                                    {{ substr($astro->first_name ?? 'A', 0, 1) }}
                                </div>
                            @endif
                            <div class="astro-info">
                                <h6>{{ $astro->first_name }} {{ $astro->last_name }}</h6>
                                <small>{{ $astro->review_count }} reviews</small>
                            </div>
                            <div class="astro-stats">
                                <strong>{{ number_format($astro->review_avg_rating ?? 0, 1) }}</strong>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No data available</p>
                    @endforelse
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">favorite</span> Top by Followers</h5>
                    @forelse($topAstrosByFollowers->take(5) as $index => $astro)
                        <div class="astro-card">
                            <div class="rank-badge {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}"
                                style="{{ $index > 2 ? 'background: #6c757d; color: white;' : '' }}">
                                {{ $index + 1 }}
                            </div>
                            @if ($astro->profile)
                                <img src="{{ $astro->profile }}" alt="" class="astro-avatar">
                            @else
                                <div class="astro-avatar placeholder">
                                    {{ substr($astro->first_name ?? 'A', 0, 1) }}
                                </div>
                            @endif
                            <div class="astro-info">
                                <h6>{{ $astro->first_name }} {{ $astro->last_name }}</h6>
                                <small>{{ $astro->followers_count }} followers</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">analytics</span> Astrologers by Service
                    </h5>
                    <div class="chart-container">
                        <div id="serviceChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">pie_chart</span> Rating Distribution
                    </h5>
                    <div class="chart-container">
                        <div id="ratingChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Consultation Average --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">monitoring</span> Monthly Avg.
                        Consultations per Astrologer</h5>
                    <div class="chart-container">
                        <div id="monthlyAvgChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed Performance Table --}}
        <div class="row">
            <div class="col-12">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">list</span> Astrologer Performance
                        Summary</h5>
                    <div class="table-responsive">
                        <table class="performance-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Astrologer</th>
                                    <th>Status</th>
                                    <th>Bookings</th>
                                    <th>Instant Consultations</th>
                                    <th>Booking Revenue</th>
                                    <th>Instant Revenue</th>
                                    <th>Avg. Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($astroPerformance as $index => $astro)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span>{{ $astro->first_name }} {{ $astro->last_name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $astro->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($astro->status ?? 'N/A') }}
                                            </span>
                                        </td>
                                        <td><span class="badge bg-primary">{{ $astro->bookings_count }}</span></td>
                                        <td><span
                                                class="badge bg-info">{{ $astro->instant_consultation_histories_count }}</span>
                                        </td>
                                        <td>₹{{ number_format($astro->bookings_sum_final_total ?? 0, 2) }}</td>
                                        <td>₹{{ number_format($astro->instant_consultation_histories_sum_amount ?? 0, 2) }}
                                        </td>
                                        <td>
                                            <span class="rating-stars">
                                                <span class="material-symbols-outlined"
                                                    style="font-size: 14px; color: #ffc107;">star</span>
                                            </span>
                                            {{ number_format($astro->review_avg_rating ?? 0, 1) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">No data available</td>
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
        // Astrologers by Service Chart
        const serviceLabels = @json($astrosByService->pluck('name')->toArray());
        const serviceCounts = @json($astrosByService->pluck('astros_count')->toArray());

        if (serviceLabels.length > 0) {
            initModernBarChart({
                selector: '#serviceChart',
                categories: serviceLabels,
                data: serviceCounts,
                label: 'Astrologers',
                colors: [modernColors.warning, modernColors.pink],
                horizontal: true
            });
        }

        // Rating Distribution Chart
        const ratingLabels = @json($ratingDistribution->pluck('rating')->map(fn($r) => $r . ' Star')->toArray());
        const ratingCounts = @json($ratingDistribution->pluck('count')->toArray());

        if (ratingLabels.length > 0) {
            initModernDonutChart({
                selector: '#ratingChart',
                labels: ratingLabels,
                data: ratingCounts,
                colors: ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#28a745'],
                centerLabel: 'Reviews'
            });
        }

        // Monthly Average Consultations Chart
        const monthlyAvgLabels = @json($monthlyConsultationAvg->pluck('month')->toArray());
        const monthlyAvgData = @json($monthlyConsultationAvg->pluck('avg_consultations')->toArray());

        if (monthlyAvgLabels.length > 0) {
            initModernAreaChart({
                selector: '#monthlyAvgChart',
                categories: monthlyAvgLabels,
                data: monthlyAvgData,
                label: 'Avg. Consultations',
                color: modernColors.warning
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
