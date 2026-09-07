@extends('layouts/contentLayoutMaster')

@section('title', 'Conversion Funnel Analytics')

@section('page-style')
    @include('content.dashboards.partials.analytics-styles')
    <style>
        .funnel-container {
            position: relative;
            padding: 20px 0;
        }

        .funnel-stage {
            display: flex;
            align-items: center;
            margin-bottom: 0;
            position: relative;
        }

        .funnel-bar {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            margin: 0 auto;
        }

        .funnel-bar:hover {
            transform: scaleX(1.02);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .funnel-bar.stage-1 {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .funnel-bar.stage-2 {
            width: 75%;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .funnel-bar.stage-3 {
            width: 50%;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .funnel-bar.stage-4 {
            width: 30%;
            background: linear-gradient(135deg, #28c76f 0%, #48da89 100%);
        }

        .funnel-label {
            position: absolute;
            left: 20px;
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .funnel-count {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-left: 8px;
        }

        .funnel-arrow {
            text-align: center;
            padding: 10px 0;
            color: var(--text-muted);
        }

        .funnel-arrow .conversion-rate {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
            background: var(--bg-light);
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid var(--card-border);
        }

        .conversion-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .conversion-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .conversion-card.purple::before {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .conversion-card.blue::before {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .conversion-card.pink::before {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .conversion-card.green::before {
            background: linear-gradient(135deg, #28c76f 0%, #48da89 100%);
        }

        .conversion-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .conversion-card .rate-value {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .conversion-card .rate-label {
            color: var(--text-secondary);
            margin-top: 8px;
        }

        .dropoff-item {
            display: flex;
            align-items: center;
            padding: 16px;
            background: var(--bg-light);
            border-radius: 12px;
            margin-bottom: 12px;
            border-left: 4px solid;
        }

        .dropoff-item.critical {
            border-left-color: #ea5455;
            background: rgba(234, 84, 85, 0.08);
        }

        .dropoff-item.warning {
            border-left-color: #ff9f43;
            background: rgba(255, 159, 67, 0.08);
        }

        .dropoff-item.normal {
            border-left-color: #28c76f;
            background: rgba(40, 199, 111, 0.08);
        }

        .dropoff-item .dropoff-stage {
            flex-grow: 1;
        }

        .dropoff-item .dropoff-pct {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .dropoff-item.critical .dropoff-pct {
            color: #ea5455;
        }

        .dropoff-item.warning .dropoff-pct {
            color: #ff9f43;
        }

        .dropoff-item.normal .dropoff-pct {
            color: #28c76f;
        }

        .source-item {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            background: var(--bg-light);
            border-radius: 12px;
            margin-bottom: 10px;
            border: 1px solid var(--card-border);
        }

        .source-item .source-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 14px;
        }

        .source-item .source-icon.organic {
            background: rgba(40, 199, 111, 0.15);
            color: #28c76f;
        }

        .source-item .source-icon.referral {
            background: rgba(102, 126, 234, 0.15);
            color: #667eea;
        }

        .source-item .source-icon.social {
            background: rgba(240, 147, 251, 0.15);
            color: #f093fb;
        }

        .source-item .source-icon.direct {
            background: rgba(79, 172, 254, 0.15);
            color: #4facfe;
        }

        .source-item .conversion-bar {
            height: 6px;
            background: var(--bg-light);
            border-radius: 3px;
            flex-grow: 1;
            margin: 0 14px;
            overflow: hidden;
        }

        .source-item .conversion-fill {
            height: 100%;
            border-radius: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .time-metric {
            text-align: center;
            padding: 20px;
            background: var(--bg-light);
            border-radius: 12px;
            border: 1px solid var(--card-border);
        }

        .time-metric .time-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .time-metric .time-label {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-top: 4px;
        }

        .funnel-summary {
            display: flex;
            justify-content: space-around;
            padding: 20px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.05) 100%);
            border-radius: 12px;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }

        .funnel-summary .summary-stat {
            text-align: center;
        }

        .funnel-summary .summary-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .funnel-summary .summary-label {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Header --}}
        <div class="dashboard-header blue">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1><span class="material-symbols-outlined me-2">filter_alt</span> Conversion Funnel Analytics</h1>
                    <p>Track user journey from registration to loyal customer</p>
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
                        <option value="">Last 30 Days</option>
                        <option value="7days" {{ request('period') == '7days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30days" {{ request('period') == '30days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="90days" {{ request('period') == '90days' ? 'selected' : '' }}>Last 90 Days</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <span class="material-symbols-outlined">filter_alt</span> Apply
                    </button>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('admin.analytics.funnel') }}" class="btn btn-outline-secondary w-100">
                        <span class="material-symbols-outlined">refresh</span> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Conversion Rate Cards --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="conversion-card purple">
                    <div class="rate-value">{{ number_format($totalRegistrations) }}</div>
                    <div class="rate-label">Total Registrations</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="conversion-card blue">
                    <div class="rate-value">{{ $regToFirstBooking }}%</div>
                    <div class="rate-label">Reg → First Booking</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="conversion-card pink">
                    <div class="rate-value">{{ $firstToRepeat }}%</div>
                    <div class="rate-label">First → Repeat</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="conversion-card green">
                    <div class="rate-value">{{ $repeatToLoyal }}%</div>
                    <div class="rate-label">Repeat → Loyal</div>
                </div>
            </div>
        </div>

        {{-- Visual Funnel --}}
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">layers</span> User Conversion Funnel</h5>
                    <div class="funnel-container">
                        {{-- Stage 1: Registrations --}}
                        <div class="funnel-stage">
                            <span class="funnel-label">Registration</span>
                            <div class="funnel-bar stage-1">
                                Registered Users <span
                                    class="funnel-count">({{ number_format($totalRegistrations) }})</span>
                            </div>
                        </div>
                        <div class="funnel-arrow">
                            <span class="material-symbols-outlined">expand_more</span>
                            <span class="conversion-rate">{{ $regToFirstBooking }}% conversion</span>
                        </div>

                        {{-- Stage 2: First Booking --}}
                        <div class="funnel-stage">
                            <span class="funnel-label">First Booking</span>
                            <div class="funnel-bar stage-2">
                                First Booking <span class="funnel-count">({{ number_format($usersWithBooking) }})</span>
                            </div>
                        </div>
                        <div class="funnel-arrow">
                            <span class="material-symbols-outlined">expand_more</span>
                            <span class="conversion-rate">{{ $firstToRepeat }}% conversion</span>
                        </div>

                        {{-- Stage 3: Repeat Customer --}}
                        <div class="funnel-stage">
                            <span class="funnel-label">Repeat Customer</span>
                            <div class="funnel-bar stage-3">
                                Repeat Customers <span class="funnel-count">({{ number_format($repeatCustomers) }})</span>
                            </div>
                        </div>
                        <div class="funnel-arrow">
                            <span class="material-symbols-outlined">expand_more</span>
                            <span class="conversion-rate">{{ $repeatToLoyal }}% conversion</span>
                        </div>

                        {{-- Stage 4: Loyal Customer --}}
                        <div class="funnel-stage">
                            <span class="funnel-label">Loyal Customer</span>
                            <div class="funnel-bar stage-4">
                                Loyal Customers <span class="funnel-count">({{ number_format($loyalCustomers) }})</span>
                            </div>
                        </div>
                    </div>

                    <div class="funnel-summary mt-4">
                        <div class="summary-stat">
                            <div class="summary-value">
                                {{ $totalRegistrations > 0 ? number_format(($loyalCustomers / $totalRegistrations) * 100, 1) : 0 }}%
                            </div>
                            <div class="summary-label">Overall Conversion</div>
                        </div>
                        <div class="summary-stat">
                            <div class="summary-value">{{ number_format($avgTimeToFirstBooking, 1) }}</div>
                            <div class="summary-label">Avg Days to First Booking</div>
                        </div>
                        <div class="summary-stat">
                            <div class="summary-value">{{ number_format($loyalCustomers) }}</div>
                            <div class="summary-label">Total Loyal Customers</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card h-100">
                    <h5 class="card-title"><span class="material-symbols-outlined">error</span> Drop-off Analysis</h5>
                    @foreach ($dropOffPoints as $dropoff)
                        @php
                            $dropClass = 'normal';
                            if ($dropoff['percentage'] > 50) {
                                $dropClass = 'critical';
                            } elseif ($dropoff['percentage'] > 30) {
                                $dropClass = 'warning';
                            }
                        @endphp
                        <div class="dropoff-item {{ $dropClass }}">
                            <div class="dropoff-stage">
                                <strong style="color: var(--text-primary);">{{ $dropoff['stage'] }}</strong>
                                <small class="d-block"
                                    style="color: var(--text-secondary);">{{ number_format($dropoff['count']) }} users
                                    dropped</small>
                            </div>
                            <div class="dropoff-pct">{{ $dropoff['percentage'] }}%</div>
                        </div>
                    @endforeach

                    <div class="mt-3 p-3 bg-light rounded">
                        <small class="text-muted">
                            <strong>💡 Tip:</strong> Focus on reducing drop-off at the highest percentage stage first for
                            maximum impact.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Time Metrics & Daily Trend --}}
        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">schedule</span> Time to Conversion</h5>
                    <div class="time-metric mb-3">
                        <div class="time-value">{{ number_format($avgTimeToFirstBooking, 1) }}</div>
                        <div class="time-label">Days to First Booking</div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="time-metric">
                                <div class="time-value" style="font-size: 1.5rem;">{{ number_format($usersWithBooking) }}
                                </div>
                                <div class="time-label">Converted Users</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="time-metric">
                                <div class="time-value" style="font-size: 1.5rem;">
                                    {{ number_format($totalRegistrations - $usersWithBooking) }}</div>
                                <div class="time-label">Not Yet Converted</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">trending_up</span> Daily Conversion
                        Trend</h5>
                    <div class="chart-container">
                        <div id="dailyConversionChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Conversion by Source & Funnel Distribution --}}
        <div class="row mb-4">
            <div class="col-lg-5 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">share</span> Conversion by Source</h5>
                    @foreach ($conversionBySource as $source)
                        @php
                            $iconClass = 'direct';
                            if (str_contains(strtolower($source['source']), 'organic')) {
                                $iconClass = 'organic';
                            } elseif (str_contains(strtolower($source['source']), 'referral')) {
                                $iconClass = 'referral';
                            } elseif (str_contains(strtolower($source['source']), 'social')) {
                                $iconClass = 'social';
                            }
                        @endphp
                        <div class="source-item">
                            <div class="source-icon {{ $iconClass }}">
                                <span
                                    class="material-symbols-outlined">{{ $iconClass == 'organic' ? 'search' : ($iconClass == 'referral' ? 'group' : ($iconClass == 'social' ? 'share' : 'public')) }}</span>
                            </div>
                            <div>
                                <strong style="color: var(--text-primary);">{{ $source['source'] }}</strong>
                                <small class="d-block"
                                    style="color: var(--text-secondary);">{{ number_format($source['count']) }}
                                    users</small>
                            </div>
                            <div class="conversion-bar">
                                <div class="conversion-fill" style="width: {{ $source['conversion_rate'] }}%"></div>
                            </div>
                            <span class="badge bg-primary">{{ $source['conversion_rate'] }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-7 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">pie_chart</span> Funnel Stage
                        Distribution</h5>
                    <div class="chart-container">
                        <div id="funnelDistributionChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Booking & Order Funnels --}}
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">calendar_month</span> Booking Funnel
                    </h5>
                    <div class="chart-container">
                        <div id="bookingFunnelChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">shopping_bag</span> Order Funnel</h5>
                    <div class="chart-container">
                        <div id="orderFunnelChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    @include('content.dashboards.partials.chart-utilities')
    <script>
        // Daily Conversion Trend
        const dailyFunnel = @json($dailyFunnel);
        const dates = dailyFunnel.map(d => d.date);
        const registrations = dailyFunnel.map(d => d.registrations);
        const conversions = dailyFunnel.map(d => d.conversions);

        if (dates.length > 0) {
            var dailyOptions = {
                series: [{
                    name: 'Registrations',
                    data: registrations
                }, {
                    name: 'Converted to Booking',
                    data: conversions
                }],
                chart: {
                    type: 'area',
                    height: 280,
                    background: 'transparent',
                    fontFamily: 'inherit',
                    toolbar: {
                        show: true
                    }
                },
                theme: chartTheme,
                colors: [modernColors.primary, modernColors.success],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: isDarkMode ? 'dark' : 'light',
                        opacityFrom: 0.6,
                        opacityTo: 0.1
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                xaxis: {
                    categories: dates,
                    labels: {
                        style: {
                            colors: chartTheme.foreColor
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: chartTheme.foreColor
                        }
                    }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4
                },
                legend: {
                    labels: {
                        colors: chartTheme.foreColor
                    }
                },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light'
                }
            };
            new ApexCharts(document.querySelector("#dailyConversionChart"), dailyOptions).render();
        }

        // Funnel Distribution Chart
        initModernDonutChart({
            selector: '#funnelDistributionChart',
            labels: ['Registered Only', 'First Booking', 'Repeat Customer', 'Loyal Customer'],
            data: [
                {{ $totalRegistrations - $usersWithBooking }},
                {{ $usersWithBooking - $repeatCustomers }},
                {{ $repeatCustomers - $loyalCustomers }},
                {{ $loyalCustomers }}
            ],
            colors: [modernColors.secondary, modernColors.info, modernColors.warning, modernColors.success],
            centerLabel: 'Users'
        });

        // Booking Funnel Chart
        const bookingFunnel = @json($bookingFunnel);
        if (bookingFunnel.length > 0) {
            var bookingOptions = {
                series: [{
                    name: 'Count',
                    data: bookingFunnel.map(b => b.count)
                }],
                chart: {
                    type: 'bar',
                    height: 280,
                    background: 'transparent',
                    fontFamily: 'inherit',
                    toolbar: {
                        show: false
                    }
                },
                theme: chartTheme,
                colors: [modernColors.primary],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 6,
                        barHeight: '60%',
                        distributed: true
                    }
                },
                xaxis: {
                    categories: bookingFunnel.map(b => b.stage),
                    labels: {
                        style: {
                            colors: chartTheme.foreColor
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: chartTheme.foreColor
                        }
                    }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4
                },
                colors: [modernColors.primary, modernColors.info, modernColors.success],
                legend: {
                    show: false
                },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light'
                }
            };
            new ApexCharts(document.querySelector("#bookingFunnelChart"), bookingOptions).render();
        }

        // Order Funnel Chart
        const orderFunnel = @json($orderFunnel);
        if (orderFunnel.length > 0) {
            var orderOptions = {
                series: [{
                    name: 'Count',
                    data: orderFunnel.map(o => o.count)
                }],
                chart: {
                    type: 'bar',
                    height: 280,
                    background: 'transparent',
                    fontFamily: 'inherit',
                    toolbar: {
                        show: false
                    }
                },
                theme: chartTheme,
                colors: [modernColors.warning],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 6,
                        barHeight: '60%',
                        distributed: true
                    }
                },
                xaxis: {
                    categories: orderFunnel.map(o => o.stage),
                    labels: {
                        style: {
                            colors: chartTheme.foreColor
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: chartTheme.foreColor
                        }
                    }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4
                },
                colors: [modernColors.warning, modernColors.info, modernColors.success],
                legend: {
                    show: false
                },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light'
                }
            };
            new ApexCharts(document.querySelector("#orderFunnelChart"), orderOptions).render();
        }

        // Date Range Picker
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: @json(request('date_range') ? explode(' to ', request('date_range')) : null),
        });
    </script>
@endsection
