@extends('layouts/contentLayoutMaster')

@section('title', 'Order Analytics Dashboard')

@section('page-style')
    @include('content.dashboards.partials.analytics-styles')
    <style>
        /* Order-specific styles */
        .order-stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .order-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .order-stat-card.primary::before {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .order-stat-card.warning::before {
            background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
        }

        .order-stat-card.info::before {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .order-stat-card.success::before {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .order-stat-card.danger::before {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .order-stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .order-stat-card.primary .stat-icon {
            background: linear-gradient(135deg, rgba(79, 172, 254, 0.15) 0%, rgba(0, 242, 254, 0.15) 100%);
            color: #4facfe;
        }

        .order-stat-card.success .stat-icon {
            background: linear-gradient(135deg, rgba(17, 153, 142, 0.15) 0%, rgba(56, 239, 125, 0.15) 100%);
            color: #11998e;
        }

        .order-stat-card.info .stat-icon {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
            color: #667eea;
        }

        .order-stat-card.warning .stat-icon {
            background: linear-gradient(135deg, rgba(247, 151, 30, 0.15) 0%, rgba(255, 210, 0, 0.15) 100%);
            color: #f7971e;
        }

        .order-stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 8px 0;
        }

        .order-stat-card .stat-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .order-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .kpi-card {
            background: var(--hover-bg);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 12px;
            border: 1px solid var(--border-color);
        }

        .kpi-card .value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .kpi-card .label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .kpi-card.highlight {
            background: linear-gradient(135deg, rgba(79, 172, 254, 0.1) 0%, rgba(0, 242, 254, 0.1) 100%);
            border-left: 4px solid #4facfe;
        }

        .dark-layout .kpi-card.highlight {
            background: linear-gradient(135deg, rgba(79, 172, 254, 0.15) 0%, rgba(0, 242, 254, 0.15) 100%);
        }

        .progress-mini {
            height: 8px;
            background: var(--hover-bg);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 8px;
        }

        .progress-mini .fill {
            height: 100%;
            border-radius: 4px;
        }

        .order-funnel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 24px 0;
        }

        .funnel-step {
            flex: 1;
            text-align: center;
            position: relative;
            padding: 20px 10px;
            background: var(--hover-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .funnel-step:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .funnel-step::after {
            content: '→';
            position: absolute;
            right: -20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1.5rem;
            z-index: 1;
        }

        .funnel-step:last-child::after {
            display: none;
        }

        .funnel-count {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .funnel-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .funnel-step.delivered .funnel-count {
            color: #28a745;
        }

        .funnel-step.cancelled .funnel-count {
            color: #dc3545;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-badge.confirmed {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }

        .status-badge.shipped {
            background: rgba(23, 162, 184, 0.15);
            color: #17a2b8;
        }

        .status-badge.delivered {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .status-badge.cancelled {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        @media (max-width: 768px) {
            .order-funnel {
                flex-wrap: wrap;
            }

            .funnel-step {
                flex: 0 0 calc(50% - 8px);
                margin-bottom: 16px;
            }

            .funnel-step::after {
                display: none;
            }
        }

        /* Product Table Styles */
        .product-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .product-table thead th {
            background: var(--bg-light);
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-secondary);
            border-bottom: 2px solid var(--border-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-table tbody tr {
            transition: all 0.2s ease;
        }

        .product-table tbody tr:hover {
            background: var(--bg-light);
        }

        .product-table tbody td {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            vertical-align: middle;
        }

        .product-table tbody tr:last-child td {
            border-bottom: none;
        }

        .dark-layout .product-table thead th {
            background: var(--bg-light);
        }

        .dark-layout .product-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
    </style>
@endsection

{{-- Flatpickr CSS and JS are already loaded globally via panels/styles.blade.php and panels/scripts.blade.php --}}

@section('content')
    <div class="container-fluid">
        {{-- Header --}}
        <div class="dashboard-header blue">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1><span class="material-symbols-outlined me-2">shopping_cart</span> Order Analytics Dashboard</h1>
                    <p>Track order performance, product sales, and fulfillment metrics</p>
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
                    <input type="text" id="date_range" name="date_range" class="form-control flatpickr-range"
                        placeholder="Select date range" value="{{ request('date_range') }}">
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
                    <button type="submit" class="btn btn-info w-100">
                        <span class="material-symbols-outlined">filter_alt</span> Apply
                    </button>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('admin.analytics.orders') }}" class="btn btn-outline-secondary w-100">
                        <span class="material-symbols-outlined">refresh</span> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Key Metrics --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="order-stat-card primary">
                    <div class="stat-icon">
                        <span class="material-symbols-outlined">shopping_bag</span>
                    </div>
                    <div class="stat-value">{{ number_format($totalOrders) }}</div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="order-stat-card success">
                    <div class="stat-icon">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <div class="stat-value">₹{{ number_format($totalOrderRevenue, 0) }}</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="order-stat-card info">
                    <div class="stat-icon">
                        <span class="material-symbols-outlined">target</span>
                    </div>
                    <div class="stat-value">₹{{ number_format($avgOrderValue, 0) }}</div>
                    <div class="stat-label">Avg. Order Value</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="order-stat-card warning">
                    <div class="stat-icon">
                        <span class="material-symbols-outlined">local_shipping</span>
                    </div>
                    <div class="stat-value">{{ number_format($avgDeliveryDays, 1) }}</div>
                    <div class="stat-label">Avg. Delivery Days</div>
                </div>
            </div>
        </div>

        {{-- Order Funnel --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">filter_alt</span> Order Funnel</h5>
                    <div class="order-funnel">
                        <div class="funnel-step">
                            <div class="funnel-count">{{ $totalOrders }}</div>
                            <div class="funnel-label">Total Orders</div>
                        </div>
                        <div class="funnel-step">
                            <div class="funnel-count">{{ $confirmedOrders }}</div>
                            <div class="funnel-label">Confirmed</div>
                        </div>
                        <div class="funnel-step">
                            <div class="funnel-count">{{ $shippedOrders }}</div>
                            <div class="funnel-label">Shipped</div>
                        </div>
                        <div class="funnel-step delivered">
                            <div class="funnel-count">{{ $deliveredOrders }}</div>
                            <div class="funnel-label">Delivered</div>
                        </div>
                        <div class="funnel-step cancelled">
                            <div class="funnel-count">{{ $cancelledOrders }}</div>
                            <div class="funnel-label">Cancelled</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Today's Stats & Rates --}}
        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">light_mode</span> Today's Performance
                    </h5>
                    <div class="kpi-card highlight">
                        <div class="value">{{ $todayOrders }}</div>
                        <div class="label">Orders Today</div>
                    </div>
                    <div class="kpi-card">
                        <div class="value">₹{{ number_format($todayOrderRevenue, 0) }}</div>
                        <div class="label">Today's Revenue</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">check_circle</span> Fulfillment Rate</h5>
                    <div class="kpi-card highlight">
                        <div class="value">{{ $fulfillmentRate }}%</div>
                        <div class="label">Orders Delivered</div>
                    </div>
                    <div class="progress-mini">
                        <div class="fill"
                            style="width: {{ $fulfillmentRate }}%; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">cancel</span> Cancellation Rate</h5>
                    <div class="kpi-card" style="{{ $cancellationRate > 10 ? 'background: #ffebee;' : '' }}">
                        <div class="value" style="{{ $cancellationRate > 10 ? 'color: #dc3545;' : '' }}">
                            {{ $cancellationRate }}%</div>
                        <div class="label">Orders Cancelled</div>
                    </div>
                    <div class="progress-mini">
                        <div class="fill"
                            style="width: {{ $cancellationRate }}%; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">trending_up</span> Daily Orders Trend
                    </h5>
                    <div class="chart-container">
                        <div id="dailyOrdersChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">pie_chart</span> Orders by Status</h5>
                    <div class="chart-container">
                        <div id="orderStatusChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Trend --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="analytics-card">
                    <h5 class="card-title"><span class="material-symbols-outlined">analytics</span> Monthly Order &
                        Revenue Trend</h5>
                    <div class="chart-container">
                        <div id="monthlyOrdersChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Products --}}
        @if ($topProducts->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="analytics-card">
                        <h5 class="card-title"><span class="material-symbols-outlined">emoji_events</span> Top Selling
                            Products</h5>
                        <div class="table-responsive">
                            <table class="product-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Orders</th>
                                        <th>Quantity Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topProducts as $index => $product)
                                        <tr>
                                            <td>
                                                <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }}"
                                                    style="{{ $index < 3 ? 'color: #333;' : '' }}">
                                                    {{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td><strong>{{ $product->title }}</strong></td>
                                            <td><span class="badge bg-primary">{{ $product->order_count }}</span></td>
                                            <td>{{ $product->total_quantity }} units</td>
                                            <td><strong>₹{{ number_format($product->total_revenue, 2) }}</strong></td>
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
        // Initialize Flatpickr Date Range Picker
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: @json(request('date_range') ? explode(' to ', request('date_range')) : null),
        });

        // Use theme variables from chart-utilities partial (isDarkMode, chartTheme, gridColor are already defined)
        const textColor = isDarkMode ? '#b4b7bd' : '#6e6b7b';

        // Modern color palette for this page
        const colors = {
            primary: '#4facfe',
            success: '#28c76f',
            info: '#667eea',
            warning: '#ff9f43',
            danger: '#ea5455',
            secondary: '#82868b'
        };

        // Daily Orders Chart (Mixed: Column + Line)
        const dailyDates = @json($dailyOrders->pluck('date')->toArray());
        const dailyCounts = @json($dailyOrders->pluck('count')->toArray());
        const dailyRevenue = @json($dailyOrders->pluck('revenue')->toArray());

        if (dailyDates.length > 0) {
            var dailyOptions = {
                series: [{
                    name: 'Orders',
                    type: 'column',
                    data: dailyCounts
                }, {
                    name: 'Revenue',
                    type: 'line',
                    data: dailyRevenue
                }],
                chart: {
                    height: 280,
                    type: 'line',
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: false,
                            reset: true
                        }
                    },
                    background: 'transparent',
                    fontFamily: 'inherit'
                },
                theme: chartTheme,
                colors: [colors.primary, colors.success],
                stroke: {
                    width: [0, 3],
                    curve: 'smooth'
                },
                fill: {
                    type: ['solid', 'gradient'],
                    gradient: {
                        shade: isDarkMode ? 'dark' : 'light',
                        type: 'vertical',
                        opacityFrom: 0.7,
                        opacityTo: 0.2
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '50%'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4
                },
                xaxis: {
                    categories: dailyDates,
                    labels: {
                        rotate: -45,
                        style: {
                            colors: textColor
                        }
                    },
                    axisBorder: {
                        color: gridColor
                    },
                    axisTicks: {
                        color: gridColor
                    }
                },
                yaxis: [{
                    title: {
                        text: 'Orders',
                        style: {
                            color: textColor
                        }
                    },
                    labels: {
                        style: {
                            colors: textColor
                        }
                    }
                }, {
                    opposite: true,
                    title: {
                        text: 'Revenue (₹)',
                        style: {
                            color: textColor
                        }
                    },
                    labels: {
                        style: {
                            colors: textColor
                        },
                        formatter: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }],
                tooltip: {
                    theme: chartTheme.mode,
                    shared: true,
                    intersect: false,
                    y: [{
                        formatter: function(value) {
                            return value + ' orders';
                        }
                    }, {
                        formatter: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }]
                },
                legend: {
                    labels: {
                        colors: textColor
                    }
                }
            };
            new ApexCharts(document.querySelector("#dailyOrdersChart"), dailyOptions).render();
        }

        // Order Status Chart (Donut)
        const statusLabels = @json($ordersByStatus->pluck('status')->map(fn($s) => ucfirst($s))->toArray());
        const statusCounts = @json($ordersByStatus->pluck('count')->toArray());

        if (statusLabels.length > 0) {
            initModernDonutChart(
                '#orderStatusChart',
                statusCounts,
                statusLabels,
                [colors.warning, colors.info, colors.success, colors.danger, colors.secondary]
            );
        }

        // Monthly Orders Chart (Mixed: Column + Line)
        const monthlyLabels = @json($monthlyOrders->pluck('month')->toArray());
        const monthlyCounts = @json($monthlyOrders->pluck('count')->toArray());
        const monthlyRevenue = @json($monthlyOrders->pluck('revenue')->toArray());

        if (monthlyLabels.length > 0) {
            var monthlyOptions = {
                series: [{
                    name: 'Orders',
                    type: 'column',
                    data: monthlyCounts
                }, {
                    name: 'Revenue',
                    type: 'line',
                    data: monthlyRevenue
                }],
                chart: {
                    height: 280,
                    type: 'line',
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: false,
                            reset: true
                        }
                    },
                    background: 'transparent',
                    fontFamily: 'inherit'
                },
                theme: chartTheme,
                colors: [colors.info, colors.danger],
                stroke: {
                    width: [0, 3],
                    curve: 'smooth'
                },
                fill: {
                    type: ['solid', 'gradient'],
                    gradient: {
                        shade: isDarkMode ? 'dark' : 'light',
                        type: 'vertical',
                        opacityFrom: 0.7,
                        opacityTo: 0.2
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        columnWidth: '45%'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4
                },
                xaxis: {
                    categories: monthlyLabels,
                    labels: {
                        style: {
                            colors: textColor
                        }
                    },
                    axisBorder: {
                        color: gridColor
                    },
                    axisTicks: {
                        color: gridColor
                    }
                },
                yaxis: [{
                    title: {
                        text: 'Orders',
                        style: {
                            color: textColor
                        }
                    },
                    labels: {
                        style: {
                            colors: textColor
                        }
                    }
                }, {
                    opposite: true,
                    title: {
                        text: 'Revenue (₹)',
                        style: {
                            color: textColor
                        }
                    },
                    labels: {
                        style: {
                            colors: textColor
                        },
                        formatter: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }],
                tooltip: {
                    theme: chartTheme.mode,
                    shared: true,
                    intersect: false
                },
                legend: {
                    labels: {
                        colors: textColor
                    }
                }
            };
            new ApexCharts(document.querySelector("#monthlyOrdersChart"), monthlyOptions).render();
        }
    </script>
@endsection
