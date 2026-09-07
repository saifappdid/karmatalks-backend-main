@extends('layouts/contentLayoutMaster')

@section('title', 'Revenue Analytics Dashboard')

@section('page-style')
    @include('content.dashboards.partials.analytics-styles')
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Header --}}
        <div class="dashboard-header green">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1><span class="material-symbols-outlined me-2">payments</span> Revenue Analytics Dashboard</h1>
                    <p>Track financial performance, revenue streams, and transaction insights</p>
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
                    <button type="submit" class="btn btn-success w-100">
                        <span class="material-symbols-outlined">filter_alt</span> Apply
                    </button>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('admin.analytics.revenue') }}" class="btn btn-outline-secondary w-100">
                        <span class="material-symbols-outlined">refresh</span> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Total Revenue Cards --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="revenue-card purple">
                    <div class="revenue-label">Total Booking Revenue</div>
                    <div class="revenue-value">₹{{ number_format($totalBookingRevenue, 2) }}</div>
                    <span class="revenue-icon"><span class="material-symbols-outlined">calendar_month</span></span>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="revenue-card green">
                    <div class="revenue-label">Total Order Revenue</div>
                    <div class="revenue-value">₹{{ number_format($totalOrderRevenue, 2) }}</div>
                    <span class="revenue-icon"><span class="material-symbols-outlined">shopping_cart</span></span>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="revenue-card orange">
                    <div class="revenue-label">Instant Consultation Revenue</div>
                    <div class="revenue-value">₹{{ number_format($totalInstantRevenue, 2) }}</div>
                    <span class="revenue-icon"><span class="material-symbols-outlined">call</span></span>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="revenue-card blue">
                    <div class="revenue-label">Total Wallet Recharges</div>
                    <div class="revenue-value">₹{{ number_format($totalWalletRecharges, 2) }}</div>
                    <span class="revenue-icon"><span class="material-symbols-outlined">credit_card</span></span>
                </div>
            </div>
        </div>

        {{-- Today's & Period Stats --}}
        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">light_mode</span> Today's Revenue</h5>
                    <div class="stat-box highlight">
                        <div class="value">
                            ₹{{ number_format($todayBookingRevenue + $todayOrderRevenue + $todayInstantRevenue, 2) }}</div>
                        <div class="label">Total Today</div>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Booking Revenue</span>
                        <span class="metric-value">₹{{ number_format($todayBookingRevenue, 2) }}</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Order Revenue</span>
                        <span class="metric-value">₹{{ number_format($todayOrderRevenue, 2) }}</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Instant Consultation</span>
                        <span class="metric-value">₹{{ number_format($todayInstantRevenue, 2) }}</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Wallet Recharges</span>
                        <span class="metric-value">₹{{ number_format($todayWalletRecharges, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">calendar_month</span> This Week</h5>
                    <div class="stat-box highlight">
                        <div class="value">₹{{ number_format($weekBookingRevenue + $weekOrderRevenue, 2) }}</div>
                        <div class="label">Week Revenue</div>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Booking Revenue</span>
                        <span class="metric-value">₹{{ number_format($weekBookingRevenue, 2) }}</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Order Revenue</span>
                        <span class="metric-value">₹{{ number_format($weekOrderRevenue, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">bar_chart</span> This Month</h5>
                    <div class="stat-box highlight">
                        <div class="value">₹{{ number_format($monthBookingRevenue + $monthOrderRevenue, 2) }}</div>
                        <div class="label">Month Revenue</div>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Booking Revenue</span>
                        <span class="metric-value">₹{{ number_format($monthBookingRevenue, 2) }}</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Order Revenue</span>
                        <span class="metric-value">₹{{ number_format($monthOrderRevenue, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revenue Charts --}}
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">trending_up</span> Daily Revenue Trend
                    </h5>
                    <div class="chart-container">
                        <div id="dailyRevenueChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">pie_chart</span> Revenue by Booking
                        Type</h5>
                    <div class="chart-container">
                        <div id="bookingTypeChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Trend & Instant Type --}}
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">analytics</span> Monthly Revenue Trend
                    </h5>
                    <div class="chart-container">
                        <div id="monthlyRevenueChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">call</span> Instant Consultation by
                        Type</h5>
                    <div class="chart-container">
                        <div id="instantTypeChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Average Values & Wallet Summary --}}
        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">target</span> Average Values</h5>
                    <div class="stat-box">
                        <div class="value">₹{{ number_format($avgBookingValue, 2) }}</div>
                        <div class="label">Avg. Booking Value</div>
                    </div>
                    <div class="stat-box">
                        <div class="value">₹{{ number_format($avgOrderValue, 2) }}</div>
                        <div class="label">Avg. Order Value</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">credit_card</span> Wallet Transactions
                    </h5>
                    @foreach ($walletTransactionSummary as $transaction)
                        <div class="metric-row">
                            <span class="metric-label">{{ ucfirst($transaction->type) }}</span>
                            <span class="metric-value">
                                ₹{{ number_format($transaction->total, 2) }}
                                <small class="text-muted">({{ $transaction->count }})</small>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">label</span> Promo Discounts</h5>
                    <div class="stat-box highlight">
                        <div class="value">₹{{ number_format($totalPromoDiscount, 2) }}</div>
                        <div class="label">Total Discount Given</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Promocode Usage Table --}}
        @if ($promocodeUsage->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="analytics-card">
                        <h5 class="card-title"><span class="material-symbols-outlined">percent</span> Top Promocodes</h5>
                        <div class="table-responsive">
                            <table class="summary-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Promocode</th>
                                        <th>Times Used</th>
                                        <th>Total Discount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($promocodeUsage as $index => $promo)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><span class="badge bg-info">{{ $promo->promocode }}</span></td>
                                            <td>{{ $promo->usage_count }}</td>
                                            <td>₹{{ number_format($promo->total_discount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('page-script')
    @include('content.dashboards.partials.chart-utilities')
    <script>
        // Daily Revenue Chart
        const dailyDates = @json($dailyRevenue->pluck('date')->toArray());
        const dailyBookingRevenue = @json($dailyRevenue->pluck('booking_revenue')->toArray());
        const dailyOrderRevenueData = @json($dailyOrderRevenue->pluck('order_revenue')->toArray());
        const dailyOrderDates = @json($dailyOrderRevenue->pluck('date')->toArray());

        if (dailyDates.length > 0) {
            var dailyOptions = {
                series: [{
                    name: 'Booking Revenue',
                    data: dailyBookingRevenue
                }],
                chart: {
                    type: 'area',
                    height: 280,
                    background: 'transparent',
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: true
                    }
                },
                theme: chartTheme,
                colors: [modernColors.primary],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: isDarkMode ? 'dark' : 'light',
                        shadeIntensity: 0.5,
                        opacityFrom: 0.7,
                        opacityTo: 0.1,
                        stops: [0, 100]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: dailyDates,
                    labels: {
                        rotate: -45,
                        style: {
                            colors: chartTheme.foreColor,
                            fontSize: '11px'
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    title: {
                        text: 'Revenue (₹)',
                        style: {
                            color: chartTheme.foreColor
                        }
                    },
                    labels: {
                        style: {
                            colors: chartTheme.foreColor
                        },
                        formatter: (value) => '₹' + value.toLocaleString()
                    }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light',
                    y: {
                        formatter: (value) => '₹' + value.toLocaleString()
                    }
                }
            };
            new ApexCharts(document.querySelector("#dailyRevenueChart"), dailyOptions).render();
        }

        // Revenue by Booking Type
        const bookingTypes = @json($revenueByBookingType->pluck('type')->map(fn($t) => ucfirst($t ?? 'Unknown'))->toArray());
        const bookingTypeRevenue = @json($revenueByBookingType->pluck('revenue')->toArray());

        if (bookingTypes.length > 0) {
            initModernDonutChart({
                selector: '#bookingTypeChart',
                labels: bookingTypes,
                data: bookingTypeRevenue,
                colors: [modernColors.primary, modernColors.warning, modernColors.success, modernColors.info],
                centerLabel: 'Revenue'
            });
        }

        // Monthly Revenue Chart
        const monthlyLabels = @json($monthlyRevenue->pluck('month')->toArray());
        const monthlyRevenueData = @json($monthlyRevenue->pluck('revenue')->toArray());

        if (monthlyLabels.length > 0) {
            initModernBarChart({
                selector: '#monthlyRevenueChart',
                categories: monthlyLabels,
                data: monthlyRevenueData,
                label: 'Revenue',
                colors: [modernColors.success, modernColors.green]
            });
        }

        // Instant Consultation Type Chart
        const instantTypes = @json($revenueByInstantType->pluck('session_type')->map(fn($t) => ucfirst($t))->toArray());
        const instantRevenue = @json($revenueByInstantType->pluck('revenue')->toArray());

        if (instantTypes.length > 0) {
            initModernBarChart({
                selector: '#instantTypeChart',
                categories: instantTypes,
                data: instantRevenue,
                label: 'Revenue',
                colors: [modernColors.warning, modernColors.pink],
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
