@extends('layouts/contentLayoutMaster')

@section('title', 'Consultation Analytics Dashboard')

@section('page-style')
    @include('content.dashboards.partials.analytics-styles')
    <style>
        .section-title-box {
            background: var(--bg-light);
            border-radius: 14px;
            padding: 18px 22px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 14px;
            border: 1px solid var(--card-border);
        }

        .section-title-box h4 {
            margin: 0;
            font-weight: 600;
            color: var(--text-primary);
        }

        .section-title-box .icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .section-title-box .icon.booking {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .section-title-box .icon.instant {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .today-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
        }

        .today-stat-item {
            background: var(--bg-light);
            border-radius: 10px;
            padding: 16px;
            text-align: center;
            border: 1px solid var(--card-border);
            transition: all 0.3s ease;
        }

        .today-stat-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .today-stat-item .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .today-stat-item .label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .today-stat-item.voice {
            border-left: 4px solid #667eea;
        }

        .today-stat-item.video {
            border-left: 4px solid #f5576c;
        }

        .today-stat-item.chat {
            border-left: 4px solid #11998e;
        }

        .growth-indicator {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .growth-indicator.up {
            background: #e8f5e9;
            color: #28a745;
        }

        .growth-indicator.down {
            background: #ffebee;
            color: #dc3545;
        }

        .metric-box {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .metric-box:last-child {
            border-bottom: none;
        }

        .heatmap-container {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .heatmap-cell {
            aspect-ratio: 1;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Header --}}
        <div class="dashboard-header pink">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1><span class="material-symbols-outlined me-2">call</span> Consultation Analytics Dashboard</h1>
                    <p>Detailed insights into scheduled bookings and instant consultations</p>
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
                    <a href="{{ route('admin.analytics.consultations') }}" class="btn btn-outline-secondary w-100">
                        <span class="material-symbols-outlined">refresh</span> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Today's Quick Stats --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="analytics-card">
                    <h5 class="card-title">
                        <span class="material-symbols-outlined">light_mode</span> Today's Overview
                    </h5>
                    <div class="today-stats">
                        <div class="today-stat-item">
                            <div class="value">{{ $todayBookings }}</div>
                            <div class="label">Bookings Today</div>
                        </div>
                        <div class="today-stat-item voice">
                            <div class="value">{{ $todayInstantVoice }}</div>
                            <div class="label">Voice Calls</div>
                        </div>
                        <div class="today-stat-item video">
                            <div class="value">{{ $todayInstantVideo }}</div>
                            <div class="label">Video Calls</div>
                        </div>
                        <div class="today-stat-item chat">
                            <div class="value">{{ $todayInstantChat }}</div>
                            <div class="label">Chat Sessions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SCHEDULED BOOKINGS SECTION --}}
        <div class="section-title">
            <div class="icon booking">
                <span class="material-symbols-outlined">calendar_month</span>
            </div>
            <h4>Scheduled Bookings Analytics</h4>
        </div>

        {{-- Booking Stats Cards --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card primary">
                    <span class="material-symbols-outlined">calendar_month</span>
                    <div class="stat-value">{{ number_format($totalBookings) }}</div>
                    <div class="stat-label">Total Bookings</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card warning">
                    <span class="material-symbols-outlined">check_circle</span>
                    <div class="stat-value">{{ number_format($confirmedBookings) }}</div>
                    <div class="stat-label">Confirmed</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card info">
                    <span class="material-symbols-outlined">play_circle</span>
                    <div class="stat-value">{{ number_format($startedBookings) }}</div>
                    <div class="stat-label">In Progress</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card success">
                    <span class="material-symbols-outlined">task_alt</span>
                    <div class="stat-value">{{ number_format($endedBookings) }}</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>

        {{-- Booking Charts --}}
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">trending_up</span> Daily Bookings Trend
                    </h5>
                    <div class="chart-container">
                        <div id="dailyBookingsChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">pie_chart</span> Bookings by Type</h5>
                    <div class="chart-container">
                        <div id="bookingTypeChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Booking Patterns --}}
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">calendar_month</span> Bookings by Day of
                        Week</h5>
                    <div class="chart-container">
                        <div id="bookingDayChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">schedule</span> Bookings by Hour</h5>
                    <div class="chart-container">
                        <div id="bookingHourChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- INSTANT CONSULTATIONS SECTION --}}
        <div class="section-title">
            <div class="icon instant">
                <span class="material-symbols-outlined">bolt</span>
            </div>
            <h4>Instant Consultation Analytics</h4>
        </div>

        {{-- Instant Stats Cards --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card success">
                    <span class="material-symbols-outlined">call</span>
                    <div class="stat-value">{{ number_format($totalInstantConsultations) }}</div>
                    <div class="stat-label">Total Instant Consultations</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card info">
                    <span class="material-symbols-outlined">hourglass_empty</span>
                    <div class="stat-value">{{ number_format($ongoingConsultations) }}</div>
                    <div class="stat-label">Ongoing</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card success">
                    <span class="material-symbols-outlined">check_circle</span>
                    <div class="stat-value">{{ number_format($endedConsultations) }}</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card"
                    style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white;">
                    <span class="material-symbols-outlined">history</span>
                    <div class="stat-value">{{ number_format($refundedConsultations) }}</div>
                    <div class="stat-label">Refunded</div>
                </div>
            </div>
        </div>

        {{-- Instant Consultation Charts --}}
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">monitoring</span> Daily Instant
                        Consultations</h5>
                    <div class="chart-container">
                        <div id="dailyInstantChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">pie_chart</span> By Session Type</h5>
                    <div class="chart-container">
                        <div id="instantTypeChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Instant Consultation Patterns & Monthly Trends --}}
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">schedule</span> Peak Hours</h5>
                    <div class="chart-container">
                        <div id="instantHourChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">analytics</span> Monthly Trend</h5>
                    <div class="chart-container">
                        <div id="monthlyInstantChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Averages & Summary --}}
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">schedule</span> Average Duration</h5>
                    <div class="row">
                        <div class="col-6">
                            <div class="today-stat-item">
                                <div class="value">{{ number_format($avgBookingDuration, 0) }}</div>
                                <div class="label">Avg. Booking Duration (min)</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="today-stat-item">
                                <div class="value">{{ number_format($avgInstantDuration, 0) }}</div>
                                <div class="label">Avg. Instant Duration (min)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">bar_chart</span> Session Type Revenue
                    </h5>
                    @foreach ($instantByType as $type)
                        <div class="metric-box">
                            <span>
                                <span
                                    class="badge bg-{{ $type->session_type == 'voice' ? 'primary' : ($type->session_type == 'video' ? 'danger' : 'success') }}">
                                    {{ ucfirst($type->session_type) }}
                                </span>
                            </span>
                            <span>
                                <strong>{{ $type->count }}</strong> sessions
                                <span class="text-muted">•</span>
                                <strong>₹{{ number_format($type->revenue, 2) }}</strong>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    @include('content.dashboards.partials.chart-utilities')
    <script>
        // Daily Bookings Chart
        const dailyBookingDates = @json($dailyBookings->pluck('date')->toArray());
        const dailyBookingCounts = @json($dailyBookings->pluck('count')->toArray());

        if (dailyBookingDates.length > 0) {
            initModernAreaChart({
                selector: '#dailyBookingsChart',
                categories: dailyBookingDates,
                data: dailyBookingCounts,
                label: 'Bookings',
                color: modernColors.primary
            });
        }



        // Booking Type Chart
        const bookingTypes = @json($bookingsByType->pluck('type')->map(fn($t) => ucfirst($t ?? 'Unknown'))->toArray());
        const bookingTypeCounts = @json($bookingsByType->pluck('count')->toArray());

        if (bookingTypes.length > 0) {
            initModernDonutChart({
                selector: '#bookingTypeChart',
                labels: bookingTypes,
                data: bookingTypeCounts,
                colors: [modernColors.primary, modernColors.warning, modernColors.success],
                centerLabel: 'Bookings'
            });
        }

        // Bookings by Day of Week
        const dayLabels = @json($bookingsByDayOfWeek->pluck('day_name')->toArray());
        const dayCounts = @json($bookingsByDayOfWeek->pluck('count')->toArray());

        if (dayLabels.length > 0) {
            initModernBarChart({
                selector: '#bookingDayChart',
                categories: dayLabels,
                data: dayCounts,
                label: 'Bookings',
                colors: [modernColors.purple, modernColors.primary]
            });
        }

        // Bookings by Hour
        const hourLabels = @json($bookingsByHour->pluck('hour')->map(fn($h) => $h . ':00')->toArray());
        const hourCounts = @json($bookingsByHour->pluck('count')->toArray());

        if (hourLabels.length > 0) {
            initModernBarChart({
                selector: '#bookingHourChart',
                categories: hourLabels,
                data: hourCounts,
                label: 'Bookings',
                colors: [modernColors.info, modernColors.cyan]
            });
        }

        // Daily Instant Consultations
        const dailyInstantDates = @json($dailyInstant->pluck('date')->toArray());
        const dailyInstantCounts = @json($dailyInstant->pluck('count')->toArray());

        if (dailyInstantDates.length > 0) {
            initModernAreaChart({
                selector: '#dailyInstantChart',
                categories: dailyInstantDates,
                data: dailyInstantCounts,
                label: 'Consultations',
                color: modernColors.warning
            });
        }

        // Instant Type Chart
        const instantTypes = @json($instantByType->pluck('session_type')->map(fn($t) => ucfirst($t))->toArray());
        const instantCounts = @json($instantByType->pluck('count')->toArray());

        if (instantTypes.length > 0) {
            initModernDonutChart({
                selector: '#instantTypeChart',
                labels: instantTypes,
                data: instantCounts,
                colors: [modernColors.primary, modernColors.warning, modernColors.success],
                centerLabel: 'Sessions'
            });
        }

        // Instant by Hour
        const instantHourLabels = @json($instantByHour->pluck('hour')->map(fn($h) => $h . ':00')->toArray());
        const instantHourCounts = @json($instantByHour->pluck('count')->toArray());

        if (instantHourLabels.length > 0) {
            initModernBarChart({
                selector: '#instantHourChart',
                categories: instantHourLabels,
                data: instantHourCounts,
                label: 'Sessions',
                colors: [modernColors.pink, modernColors.warning]
            });
        }

        // Monthly Instant Chart
        const monthlyInstantLabels = @json($monthlyInstant->pluck('month')->toArray());
        const monthlyInstantCounts = @json($monthlyInstant->pluck('count')->toArray());

        if (monthlyInstantLabels.length > 0) {
            initModernBarChart({
                selector: '#monthlyInstantChart',
                categories: monthlyInstantLabels,
                data: monthlyInstantCounts,
                label: 'Consultations',
                colors: [modernColors.success, modernColors.green]
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
