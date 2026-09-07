@extends('layouts/contentLayoutMaster')

@section('title', 'Retention & Churn Analytics')

@section('page-style')
    @include('content.dashboards.partials.analytics-styles')
    <style>
        .retention-metric {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            border: 1px solid var(--card-border);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .retention-metric::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .retention-metric.positive::before {
            background: linear-gradient(135deg, #28c76f 0%, #48da89 100%);
        }

        .retention-metric.negative::before {
            background: linear-gradient(135deg, #ea5455 0%, #ff6b6b 100%);
        }

        .retention-metric.neutral::before {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .retention-metric .metric-value {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .retention-metric.positive .metric-value {
            color: #28c76f;
        }

        .retention-metric.negative .metric-value {
            color: #ea5455;
        }

        .retention-metric.neutral .metric-value {
            color: var(--text-primary);
        }

        .retention-metric .metric-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 8px;
        }

        .retention-metric:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .cohort-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
        }

        .cohort-table th {
            background: var(--bg-light);
            padding: 12px 8px;
            text-align: center;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
            border-radius: 6px;
        }

        .cohort-table td {
            padding: 12px 8px;
            text-align: center;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .cohort-table .cohort-month {
            background: var(--bg-light);
            color: var(--text-primary);
            font-weight: 600;
        }

        .cohort-table .cohort-users {
            background: var(--bg-light);
            color: var(--text-primary);
        }

        .cohort-cell {
            color: white;
        }

        .cohort-cell.high {
            background: #28c76f;
        }

        .cohort-cell.medium {
            background: #ff9f43;
        }

        .cohort-cell.low {
            background: #ea5455;
        }

        .cohort-cell.none {
            background: var(--bg-light);
            color: var(--text-muted);
        }

        .activity-segment {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px;
            background: var(--bg-light);
            border-radius: 12px;
            margin-bottom: 12px;
            border: 1px solid var(--card-border);
        }

        .activity-segment .segment-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .activity-segment .segment-icon.high {
            background: rgba(40, 199, 111, 0.15);
            color: #28c76f;
        }

        .activity-segment .segment-icon.moderate {
            background: rgba(255, 159, 67, 0.15);
            color: #ff9f43;
        }

        .activity-segment .segment-icon.low {
            background: rgba(234, 84, 85, 0.15);
            color: #ea5455;
        }

        .activity-segment .segment-icon.inactive {
            background: rgba(130, 134, 139, 0.15);
            color: #82868b;
        }

        .ltv-bar {
            display: flex;
            height: 32px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .ltv-bar>div {
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .ltv-high {
            background: linear-gradient(135deg, #28c76f 0%, #48da89 100%);
        }

        .ltv-medium {
            background: linear-gradient(135deg, #ff9f43 0%, #ffb976 100%);
        }

        .ltv-low {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .ltv-zero {
            background: linear-gradient(135deg, #82868b 0%, #a3a7ab 100%);
        }

        .risk-alert {
            background: linear-gradient(135deg, rgba(234, 84, 85, 0.1) 0%, rgba(255, 107, 107, 0.05) 100%);
            border: 1px solid rgba(234, 84, 85, 0.3);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }

        .risk-alert .risk-count {
            font-size: 2.5rem;
            font-weight: 700;
            color: #ea5455;
        }

        .days-distribution {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .days-badge {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            background: var(--bg-light);
            color: var(--text-primary);
            border: 1px solid var(--card-border);
        }

        .days-badge.active {
            border-color: #28c76f;
            background: rgba(40, 199, 111, 0.1);
        }

        .days-badge.warning {
            border-color: #ff9f43;
            background: rgba(255, 159, 67, 0.1);
        }

        .days-badge.danger {
            border-color: #ea5455;
            background: rgba(234, 84, 85, 0.1);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Header --}}
        <div class="dashboard-header pink">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1><span class="material-symbols-outlined me-2">verified_user</span> Retention & Churn Analytics</h1>
                    <p>Understand customer retention, identify at-risk users, and reduce churn</p>
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
                        <option value="">Last 90 Days</option>
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
                    <a href="{{ route('admin.analytics.retention') }}" class="btn btn-outline-secondary w-100">
                        <span class="material-symbols-outlined">refresh</span> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Key Metrics --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="retention-metric positive">
                    <div class="metric-value">{{ $retentionRate }}%</div>
                    <div class="metric-label">Retention Rate</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="retention-metric negative">
                    <div class="metric-value">{{ $churnRate }}%</div>
                    <div class="metric-label">Churn Rate</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="retention-metric neutral">
                    <div class="metric-value">{{ number_format($activeUsersCount) }}</div>
                    <div class="metric-label">Active Users</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="retention-metric negative">
                    <div class="metric-value">{{ number_format($churnedUsers) }}</div>
                    <div class="metric-label">Churned Users</div>
                </div>
            </div>
        </div>

        {{-- Retention Trend --}}
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">trending_up</span> Monthly Retention
                        Trend</h5>
                    <div class="chart-container">
                        <div id="retentionTrendChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">warning</span> At-Risk Users</h5>
                    <div class="risk-alert mb-3">
                        <div class="risk-count">{{ number_format($atRiskUsers) }}</div>
                        <div style="color: var(--text-secondary);">Inactive for 30+ days</div>
                    </div>
                    <p class="text-muted small">
                        These users haven't made any bookings or orders in the last 30 days. Consider sending them a
                        win-back campaign.
                    </p>
                    <button class="btn btn-outline-danger w-100 mt-2">
                        <span class="material-symbols-outlined">mail</span> Export At-Risk Users
                    </button>
                </div>
            </div>
        </div>

        {{-- User Segments & Cohort --}}
        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">group</span> Activity Segments</h5>

                    <div class="activity-segment">
                        <div class="segment-icon high">
                            <span class="material-symbols-outlined">bolt</span>
                        </div>
                        <div class="flex-grow-1">
                            <strong style="color: var(--text-primary);">Highly Active</strong>
                            <small class="d-block" style="color: var(--text-secondary);">5+ bookings</small>
                        </div>
                        <span class="badge bg-success">{{ number_format($highlyActive) }}</span>
                    </div>

                    <div class="activity-segment">
                        <div class="segment-icon moderate">
                            <span class="material-symbols-outlined">monitoring</span>
                        </div>
                        <div class="flex-grow-1">
                            <strong style="color: var(--text-primary);">Moderately Active</strong>
                            <small class="d-block" style="color: var(--text-secondary);">2-4 bookings</small>
                        </div>
                        <span class="badge bg-warning">{{ number_format($moderatelyActive) }}</span>
                    </div>

                    <div class="activity-segment">
                        <div class="segment-icon low">
                            <span class="material-symbols-outlined">battery_1_bar</span>
                        </div>
                        <div class="flex-grow-1">
                            <strong style="color: var(--text-primary);">Low Active</strong>
                            <small class="d-block" style="color: var(--text-secondary);">1 booking</small>
                        </div>
                        <span class="badge bg-danger">{{ number_format($lowActive) }}</span>
                    </div>

                    <div class="activity-segment">
                        <div class="segment-icon inactive">
                            <span class="material-symbols-outlined">dark_mode</span>
                        </div>
                        <div class="flex-grow-1">
                            <strong style="color: var(--text-primary);">Returning Users</strong>
                            <small class="d-block" style="color: var(--text-secondary);">Old users active again</small>
                        </div>
                        <span class="badge bg-secondary">{{ number_format($returningUsers) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">grid_view</span> Cohort Retention
                        Analysis</h5>
                    <div class="table-responsive">
                        <table class="cohort-table">
                            <thead>
                                <tr>
                                    <th>Cohort</th>
                                    <th>Users</th>
                                    <th>Month 0</th>
                                    <th>Month 1</th>
                                    <th>Month 2</th>
                                    <th>Month 3</th>
                                    <th>Month 4</th>
                                    <th>Month 5</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cohorts as $cohort)
                                    <tr>
                                        <td class="cohort-month">{{ $cohort['month'] }}</td>
                                        <td class="cohort-users">{{ $cohort['users'] }}</td>
                                        @foreach ($cohort['retention'] as $idx => $pct)
                                            @php
                                                $cellClass = 'none';
                                                if ($pct >= 50) {
                                                    $cellClass = 'high';
                                                } elseif ($pct >= 20) {
                                                    $cellClass = 'medium';
                                                } elseif ($pct > 0) {
                                                    $cellClass = 'low';
                                                }
                                            @endphp
                                            <td class="cohort-cell {{ $cellClass }}">
                                                {{ $pct > 0 ? $pct . '%' : '-' }}
                                            </td>
                                        @endforeach
                                        @for ($i = count($cohort['retention']); $i < 6; $i++)
                                            <td class="cohort-cell none">-</td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex gap-3 justify-content-center">
                        <small><span class="badge bg-success">●</span> High (50%+)</small>
                        <small><span class="badge bg-warning">●</span> Medium (20-50%)</small>
                        <small><span class="badge bg-danger">●</span> Low (&lt;20%)</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- LTV & Activity Distribution --}}
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">payments</span> Customer Lifetime Value
                        Segments</h5>
                    @php
                        $totalLTV = $highLTV + $mediumLTV + $lowLTV + $zeroLTV;
                        $highPct = $totalLTV > 0 ? ($highLTV / $totalLTV) * 100 : 0;
                        $medPct = $totalLTV > 0 ? ($mediumLTV / $totalLTV) * 100 : 0;
                        $lowPct = $totalLTV > 0 ? ($lowLTV / $totalLTV) * 100 : 0;
                        $zeroPct = $totalLTV > 0 ? ($zeroLTV / $totalLTV) * 100 : 0;
                    @endphp
                    <div class="ltv-bar">
                        @if ($highPct > 0)
                            <div class="ltv-high" style="width: {{ $highPct }}%">₹5K+</div>
                        @endif
                        @if ($medPct > 0)
                            <div class="ltv-medium" style="width: {{ $medPct }}%">₹1-5K</div>
                        @endif
                        @if ($lowPct > 0)
                            <div class="ltv-low" style="width: {{ $lowPct }}%">&lt;₹1K</div>
                        @endif
                        @if ($zeroPct > 0)
                            <div class="ltv-zero" style="width: {{ $zeroPct }}%">₹0</div>
                        @endif
                    </div>

                    <div class="row text-center">
                        <div class="col-3">
                            <h4 class="mb-0" style="color: #28c76f;">{{ number_format($highLTV) }}</h4>
                            <small style="color: var(--text-secondary);">High (₹5K+)</small>
                        </div>
                        <div class="col-3">
                            <h4 class="mb-0" style="color: #ff9f43;">{{ number_format($mediumLTV) }}</h4>
                            <small style="color: var(--text-secondary);">Medium</small>
                        </div>
                        <div class="col-3">
                            <h4 class="mb-0" style="color: #4facfe;">{{ number_format($lowLTV) }}</h4>
                            <small style="color: var(--text-secondary);">Low</small>
                        </div>
                        <div class="col-3">
                            <h4 class="mb-0" style="color: #82868b;">{{ number_format($zeroLTV) }}</h4>
                            <small style="color: var(--text-secondary);">No Purchase</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">schedule</span> Days Since Last
                        Activity</h5>
                    <div class="chart-container">
                        <div id="activityDistributionChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Stats --}}
        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">person_add</span> New vs Returning</h5>
                    <div class="chart-container">
                        <div id="newVsReturningChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">analytics</span> Monthly Churn vs
                        Retention</h5>
                    <div class="chart-container">
                        <div id="churnVsRetentionChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    @include('content.dashboards.partials.chart-utilities')
    <script>
        // Monthly Retention Trend
        const monthlyRetention = @json($monthlyRetention);
        const months = monthlyRetention.map(m => m.month);
        const retentionData = monthlyRetention.map(m => m.retention);
        const churnData = monthlyRetention.map(m => m.churned);

        if (months.length > 0) {
            var trendOptions = {
                series: [{
                    name: 'Retention Rate',
                    data: retentionData
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
                colors: [modernColors.success],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: isDarkMode ? 'dark' : 'light',
                        opacityFrom: 0.7,
                        opacityTo: 0.1
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: months,
                    labels: {
                        style: {
                            colors: chartTheme.foreColor
                        }
                    }
                },
                yaxis: {
                    max: 100,
                    labels: {
                        style: {
                            colors: chartTheme.foreColor
                        },
                        formatter: (val) => val + '%'
                    }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light'
                }
            };
            new ApexCharts(document.querySelector("#retentionTrendChart"), trendOptions).render();
        }

        // Activity Distribution Chart
        const activityData = @json(array_values($daysSinceActivity));
        const activityLabels = ['0-7 days', '8-30 days', '31-60 days', '61-90 days', '90+ days'];

        initModernBarChart({
            selector: '#activityDistributionChart',
            categories: activityLabels,
            data: activityData,
            label: 'Users',
            colors: [modernColors.success, modernColors.info]
        });

        // New vs Returning
        initModernDonutChart({
            selector: '#newVsReturningChart',
            labels: ['New Users', 'Returning Users'],
            data: [{{ $newUsersInPeriod }}, {{ $returningUsers }}],
            colors: [modernColors.primary, modernColors.success],
            centerLabel: 'Users'
        });

        // Churn vs Retention Stacked
        if (months.length > 0) {
            var stackedOptions = {
                series: [{
                    name: 'Retained',
                    data: retentionData
                }, {
                    name: 'Churned',
                    data: churnData
                }],
                chart: {
                    type: 'bar',
                    height: 280,
                    stacked: true,
                    stackType: '100%',
                    background: 'transparent',
                    fontFamily: 'inherit',
                    toolbar: {
                        show: false
                    }
                },
                theme: chartTheme,
                colors: [modernColors.success, modernColors.warning],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 6,
                        columnWidth: '50%'
                    }
                },
                xaxis: {
                    categories: months,
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
                        },
                        formatter: (val) => val + '%'
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
            new ApexCharts(document.querySelector("#churnVsRetentionChart"), stackedOptions).render();
        }

        // Date Range Picker
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: @json(request('date_range') ? explode(' to ', request('date_range')) : null),
        });
    </script>
@endsection
