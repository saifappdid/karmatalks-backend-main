@extends('layouts/contentLayoutMaster')

@section('title', 'Real-Time Analytics Dashboard')

@section('page-style')
    @include('content.dashboards.partials.analytics-styles')
    <style>
        .pulse-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #28c76f;
            animation: pulse 2s infinite;
            display: inline-block;
            margin-right: 8px;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(40, 199, 111, 0.7);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(40, 199, 111, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(40, 199, 111, 0);
            }
        }

        .live-badge {
            background: linear-gradient(135deg, #ea5455 0%, #ff9f43 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            animation: glow 2s ease-in-out infinite;
        }

        @keyframes glow {

            0%,
            100% {
                box-shadow: 0 0 5px rgba(234, 84, 85, 0.5);
            }

            50% {
                box-shadow: 0 0 20px rgba(234, 84, 85, 0.8);
            }
        }

        .realtime-stat {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            border: 1px solid var(--card-border);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .realtime-stat::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .realtime-stat.users::before {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .realtime-stat.bookings::before {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .realtime-stat.orders::before {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .realtime-stat.revenue::before {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .realtime-stat .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .realtime-stat .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 8px;
        }

        .realtime-stat:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-icon.booking {
            background: rgba(102, 126, 234, 0.15);
            color: #667eea;
        }

        .activity-icon.order {
            background: rgba(17, 153, 142, 0.15);
            color: #11998e;
        }

        .activity-content h6 {
            margin: 0 0 4px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .activity-content p {
            margin: 0;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .activity-time {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-left: auto;
            white-space: nowrap;
        }

        .astrologer-status {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: var(--bg-light);
            border-radius: 10px;
            margin-bottom: 10px;
            border: 1px solid var(--card-border);
        }

        .astrologer-status img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
        }

        .astrologer-status .placeholder-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .ongoing-session {
            background: linear-gradient(135deg, rgba(40, 199, 111, 0.1) 0%, rgba(40, 199, 111, 0.05) 100%);
            border: 1px solid rgba(40, 199, 111, 0.3);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
        }

        .ongoing-session .count {
            font-size: 2rem;
            font-weight: 700;
            color: #28c76f;
        }

        .availability-bar {
            display: flex;
            height: 24px;
            border-radius: 12px;
            overflow: hidden;
            background: var(--bg-light);
        }

        .availability-bar .available {
            background: linear-gradient(135deg, #28c76f 0%, #48da89 100%);
        }

        .availability-bar .busy {
            background: linear-gradient(135deg, #ea5455 0%, #ff6b6b 100%);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Header --}}
        <div class="dashboard-header orange">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1>
                        <span class="live-badge me-2">
                            <span class="pulse-dot" style="animation-delay: 0s;"></span>
                            LIVE
                        </span>
                        Real-Time Analytics
                    </h1>
                    <p>Monitor your business metrics in real-time • Last updated: {{ now()->format('h:i:s A') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-light" onclick="location.reload()">
                        <span class="material-symbols-outlined">refresh</span> Refresh
                    </button>
                    <a href="{{ route('admin.home.index') }}" class="btn btn-light">
                        <span class="material-symbols-outlined">arrow_back</span> Back
                    </a>
                </div>
            </div>
        </div>

        {{-- Live Stats --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="realtime-stat users">
                    <div class="stat-value">{{ number_format($activeUsersNow) }}</div>
                    <div class="stat-label">
                        <span class="pulse-dot"></span>
                        Active Users Now
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="realtime-stat bookings">
                    <div class="stat-value">{{ number_format($ongoingInstantConsultations) }}</div>
                    <div class="stat-label">
                        <span class="pulse-dot"></span>
                        Ongoing Consultations
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="realtime-stat orders">
                    <div class="stat-value">{{ $availableAstrologers }}/{{ $totalAstrologers }}</div>
                    <div class="stat-label">Astrologers Available</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="realtime-stat revenue">
                    <div class="stat-value">₹{{ number_format($todayTotalRevenue, 0) }}</div>
                    <div class="stat-label">Today's Revenue</div>
                </div>
            </div>
        </div>

        {{-- Today's Stats Row --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card primary">
                    <div class="stat-icon"><span class="material-symbols-outlined">person_add</span></div>
                    <div class="stat-value">{{ number_format($todayUsers) }}</div>
                    <div class="stat-label">New Users Today</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card success">
                    <div class="stat-icon"><span class="material-symbols-outlined">calendar_month</span></div>
                    <div class="stat-value">{{ number_format($todayBookings) }}</div>
                    <div class="stat-label">Bookings Today</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card info">
                    <div class="stat-icon"><span class="material-symbols-outlined">shopping_cart</span></div>
                    <div class="stat-value">{{ number_format($todayOrders) }}</div>
                    <div class="stat-label">Orders Today</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card warning">
                    <div class="stat-icon"><span class="material-symbols-outlined">call</span></div>
                    <div class="stat-value">{{ number_format($todayInstantCalls) }}</div>
                    <div class="stat-label">Instant Calls Today</div>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">monitoring</span> Today's Hourly Activity
                    </h5>
                    <div class="chart-container">
                        <div id="hourlyActivityChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">group</span> Astrologer Status</h5>
                    <div class="availability-bar mb-3">
                        @php
                            $availablePct =
                                $totalAstrologers > 0 ? ($availableAstrologers / $totalAstrologers) * 100 : 0;
                        @endphp
                        <div class="available" style="width: {{ $availablePct }}%"></div>
                        <div class="busy" style="width: {{ 100 - $availablePct }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-success"><span class="material-symbols-outlined"
                                style="font-size:16px;vertical-align:middle;">check_circle</span>
                            Available: {{ $availableAstrologers }}</span>
                        <span class="text-danger"><span class="material-symbols-outlined"
                                style="font-size:16px;vertical-align:middle;">call</span> Busy:
                            {{ $busyAstrologers }}</span>
                    </div>

                    <h6 class="mb-3" style="color: var(--text-primary);">Top Performers Today</h6>
                    @forelse($topAstrologersToday as $astro)
                        <div class="astrologer-status">
                            @if ($astro->image)
                                <img src="{{ asset('storage/' . $astro->image) }}" alt="{{ $astro->first_name }}">
                            @else
                                <div class="placeholder-avatar">{{ strtoupper(substr($astro->first_name, 0, 1)) }}</div>
                            @endif
                            <div class="flex-grow-1">
                                <strong style="color: var(--text-primary);">{{ $astro->first_name }}
                                    {{ $astro->last_name }}</strong>
                                <small class="d-block" style="color: var(--text-secondary);">{{ $astro->session_count }}
                                    sessions</small>
                            </div>
                            <span class="badge bg-success">₹{{ number_format($astro->total_revenue, 0) }}</span>
                        </div>
                    @empty
                        <p class="text-muted text-center">No sessions today yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Revenue & Activity --}}
        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">payments</span> Today's Revenue Breakdown
                    </h5>
                    <div class="chart-container">
                        <div id="revenueBreakdownChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">trending_up</span> Hourly Revenue</h5>
                    <div class="chart-container">
                        <div id="hourlyRevenueChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">notifications</span> Recent Activity
                    </h5>
                    <div style="max-height: 320px; overflow-y: auto;">
                        @forelse($recentActivities as $activity)
                            <div class="activity-item">
                                <div class="activity-icon {{ $activity['type'] }}">
                                    <span class="material-symbols-outlined"
                                        style="font-size: 18px;">{{ $activity['icon'] == 'calendar' ? 'calendar_month' : ($activity['icon'] == 'shopping-cart' ? 'shopping_cart' : ($activity['icon'] == 'phone' ? 'call' : $activity['icon'])) }}</span>
                                </div>
                                <div class="activity-content">
                                    <h6>{{ $activity['title'] }}</h6>
                                    <p>{{ $activity['description'] }}</p>
                                </div>
                                <span class="activity-time">{{ $activity['time']->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4">No recent activity</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">schedule</span> Pending Today</h5>
                    <div class="stat-box highlight">
                        <div class="value">{{ $pendingBookingsToday }}</div>
                        <div class="label">Scheduled Consultations</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">visibility</span> Users Online Today
                    </h5>
                    <div class="stat-box highlight">
                        <div class="value">{{ number_format($usersOnlineToday) }}</div>
                        <div class="label">Unique Visitors</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">call</span> Ongoing by Type</h5>
                    <div class="d-flex gap-3 mt-3">
                        <div class="flex-fill text-center">
                            <div class="h4 mb-0" style="color: #667eea;">{{ $ongoingByType['voice'] ?? 0 }}</div>
                            <small style="color: var(--text-secondary);">Voice</small>
                        </div>
                        <div class="flex-fill text-center">
                            <div class="h4 mb-0" style="color: #f5576c;">{{ $ongoingByType['video'] ?? 0 }}</div>
                            <small style="color: var(--text-secondary);">Video</small>
                        </div>
                        <div class="flex-fill text-center">
                            <div class="h4 mb-0" style="color: #11998e;">{{ $ongoingByType['chat'] ?? 0 }}</div>
                            <small style="color: var(--text-secondary);">Chat</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    @include('content.dashboards.partials.chart-utilities')
    <script>
        // Hourly Activity Chart
        const hourLabels = @json($hourLabels);
        const hourlyUsersData = @json($hourlyUsersData);
        const hourlyBookingsData = @json($hourlyBookingsData);

        if (hourLabels.length > 0) {
            var hourlyOptions = {
                series: [{
                    name: 'New Users',
                    data: hourlyUsersData
                }, {
                    name: 'Bookings',
                    data: hourlyBookingsData
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
                    categories: hourLabels,
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
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light'
                },
                legend: {
                    labels: {
                        colors: chartTheme.foreColor
                    }
                }
            };
            new ApexCharts(document.querySelector("#hourlyActivityChart"), hourlyOptions).render();
        }

        // Revenue Breakdown Donut
        const revenueData = [
            {{ $todayBookingRevenue }},
            {{ $todayOrderRevenue }},
            {{ $todayInstantRevenue }},
            {{ $todayWalletRecharges }}
        ];
        const revenueLabels = ['Bookings', 'Orders', 'Instant Calls', 'Wallet'];

        if (revenueData.some(v => v > 0)) {
            initModernDonutChart({
                selector: '#revenueBreakdownChart',
                labels: revenueLabels,
                data: revenueData,
                colors: [modernColors.primary, modernColors.success, modernColors.warning, modernColors.info],
                centerLabel: 'Revenue'
            });
        }

        // Hourly Revenue Chart
        const hourlyRevenueData = @json($hourlyRevenueData);

        if (hourlyRevenueData.length > 0) {
            initModernAreaChart({
                selector: '#hourlyRevenueChart',
                categories: hourLabels,
                data: hourlyRevenueData,
                label: 'Revenue',
                color: modernColors.success
            });
        }

        // Auto refresh every 60 seconds
        setTimeout(function() {
            location.reload();
        }, 60000);
    </script>
@endsection
