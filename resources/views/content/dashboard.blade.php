@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard Analytics')

@section('page-style')
    <style>
        :root {
            --card-bg: #ffffff;
            --card-border: rgba(0, 0, 0, 0.04);
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            --text-muted: #a0aec0;
            --bg-light: #f8fafc;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.12);
            --gradient-purple: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-green: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --gradient-orange: linear-gradient(135deg, #ff6b6b 0%, #feca57 100%);
            --gradient-blue: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --gradient-pink: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .dark-layout {
            --card-bg: #283046;
            --card-border: rgba(255, 255, 255, 0.06);
            --text-primary: #d0d2d6;
            --text-secondary: #b4b7bd;
            --text-muted: #676d7d;
            --bg-light: #161d31;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.4);
        }

        /* Modern Dashboard Hero Header */
        .dashboard-hero {
            background: var(--gradient-purple);
            border-radius: 20px;
            padding: 28px 32px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }

        .dashboard-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .dashboard-hero::after {
            content: '';
            position: absolute;
            bottom: -60%;
            left: 10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .dashboard-hero h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: white;
            margin: 0 0 8px;
            position: relative;
            z-index: 1;
        }

        .dashboard-hero p {
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
        }

        /* Modern Card Styles */
        .modern-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--card-border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .modern-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .modern-card .card-header {
            background: transparent;
            border-bottom: 1px solid var(--card-border);
            padding: 18px 22px;
        }

        .modern-card .card-header h5 {
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            font-size: 1rem;
        }

        .modern-card .card-body {
            padding: 22px;
        }

        /* Stat Cards with Glassmorphism */
        .stat-glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 22px;
            border: 1px solid var(--card-border);
            box-shadow: var(--shadow-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .stat-glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--stat-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-glass-card:hover::before {
            opacity: 1;
        }

        .stat-glass-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
        }

        .stat-glass-card .stat-icon-wrapper {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .stat-glass-card .stat-icon-wrapper .material-symbols-outlined {
            font-size: 26px;
        }

        .stat-glass-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .stat-glass-card .stat-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-top: 6px;
            font-weight: 500;
        }

        .stat-glass-card.orange {
            --stat-gradient: var(--gradient-orange);
        }

        .stat-glass-card.orange .stat-icon-wrapper {
            background: rgba(255, 107, 107, 0.15);
            color: #ff6b6b;
        }

        .stat-glass-card.blue {
            --stat-gradient: var(--gradient-blue);
        }

        .stat-glass-card.blue .stat-icon-wrapper {
            background: rgba(79, 172, 254, 0.15);
            color: #4facfe;
        }

        .stat-glass-card.green {
            --stat-gradient: var(--gradient-green);
        }

        .stat-glass-card.green .stat-icon-wrapper {
            background: rgba(17, 153, 142, 0.15);
            color: #11998e;
        }

        .stat-glass-card.purple {
            --stat-gradient: var(--gradient-purple);
        }

        .stat-glass-card.purple .stat-icon-wrapper {
            background: rgba(102, 126, 234, 0.15);
            color: #667eea;
        }

        /* Analytics Quick Links */
        .analytics-link-card {
            background: var(--gradient-purple);
            border-radius: 16px;
            padding: 18px 22px;
            color: white;
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }

        .analytics-link-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .analytics-link-card:hover::before {
            transform: scale(1.5);
        }

        .analytics-link-card:hover {
            transform: translateY(-4px) scale(1.02);
            color: white;
            text-decoration: none;
        }

        .analytics-link-card.green {
            background: var(--gradient-green);
        }

        .analytics-link-card.orange {
            background: var(--gradient-pink);
        }

        .analytics-link-card.blue {
            background: var(--gradient-blue);
        }

        .analytics-link-card.blue {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .analytics-link-card.blue:hover {
            box-shadow: 0 8px 20px rgba(79, 172, 254, 0.4);
        }

        .analytics-link-card.purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .analytics-link-card .link-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        .analytics-link-card .link-icon svg {
            width: 20px;
            height: 20px;
        }

        .analytics-link-card .link-text {
            position: relative;
            z-index: 1;
        }

        .analytics-link-card .link-text h6 {
            margin: 0;
            color: white !important;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .analytics-link-card .link-text small {
            opacity: 0.85;
            font-size: 0.8rem;
        }

        /* Earning Cards Modern */
        .earning-card-modern {
            background: var(--card-bg);
            border-radius: 14px;
            padding: 20px 22px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--card-border);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .earning-card-modern:hover {
            transform: translateX(8px);
            box-shadow: var(--shadow-md);
        }

        .earning-card-modern a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            text-decoration: none;
        }

        .earning-card-modern .earning-info h6 {
            font-weight: 600;
            color: var(--text-secondary);
            margin: 0 0 6px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .earning-card-modern .earning-info h2 {
            font-weight: 700;
            margin: 0;
            font-size: 1.5rem;
        }

        .earning-card-modern .earning-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .earning-card-modern.voice .earning-info h2 {
            color: #667eea;
        }

        .earning-card-modern.voice .earning-icon {
            background: rgba(102, 126, 234, 0.15);
            color: #667eea;
        }

        .earning-card-modern.video .earning-info h2 {
            color: #11998e;
        }

        .earning-card-modern.video .earning-icon {
            background: rgba(17, 153, 142, 0.15);
            color: #11998e;
        }

        .earning-card-modern.chat .earning-info h2 {
            color: #f5576c;
        }

        .earning-card-modern.chat .earning-icon {
            background: rgba(245, 87, 108, 0.15);
            color: #f5576c;
        }

        /* Filter Section */
        .filter-section {
            background: var(--card-bg);
            border-radius: 14px;
            padding: 18px 22px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--card-border);
        }

        .filter-section .form-control {
            background: var(--bg-light);
            border: 1px solid var(--card-border);
            border-radius: 10px;
            padding: 10px 16px;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .filter-section .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }

        /* Chart Cards */
        .chart-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--card-border);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .chart-card .chart-header {
            padding: 20px 24px 0;
        }

        .chart-card .chart-header h6 {
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            font-size: 1rem;
        }

        .chart-card .chart-body {
            padding: 10px 16px 16px;
        }

        /* Section Titles */
        .section-title {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 1.1rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title .title-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--gradient-purple);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .section-title .title-icon .material-symbols-outlined {
            font-size: 18px;
        }

        /* Material Icons Global Styles */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }

        .analytics-link-card .link-icon .material-symbols-outlined {
            font-size: 22px;
        }

        .earning-card-modern .earning-icon .material-symbols-outlined {
            font-size: 24px;
        }

        .filter-section .material-symbols-outlined {
            font-size: 16px;
            vertical-align: text-bottom;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Dashboard Hero Header --}}
        <div class="dashboard-hero">
            <h1>📊 Dashboard Overview</h1>
            <p>Welcome back! Here's what's happening with your platform today.</p>
        </div>

        {{-- Quick Analytics Links --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="section-title">
                    <div class="title-icon">
                        <span class="material-symbols-outlined">analytics</span>
                    </div>
                    <span>Detailed Analytics Dashboards</span>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <a href="{{ route('admin.analytics.users') }}" class="analytics-link-card purple">
                    <div class="link-icon">
                        <span class="material-symbols-outlined">home</span>
                    </div>
                    <div class="link-text">
                        <h6>User Analytics</h6>
                        <small>Growth & Demographics</small>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <a href="{{ route('admin.analytics.revenue') }}" class="analytics-link-card green">
                    <div class="link-icon">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <div class="link-text">
                        <h6>Revenue Analytics</h6>
                        <small>Financial Insights</small>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <a href="{{ route('admin.analytics.astrologers') }}" class="analytics-link-card orange">
                    <div class="link-icon">
                        <span class="material-symbols-outlined">star</span>
                    </div>
                    <div class="link-text">
                        <h6>Astrologer Analytics</h6>
                        <small>Performance & Ratings</small>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <a href="{{ route('admin.analytics.consultations') }}" class="analytics-link-card blue">
                    <div class="link-icon">
                        <span class="material-symbols-outlined">call</span>
                    </div>
                    <div class="link-text">
                        <h6>Consultation Analytics</h6>
                        <small>Bookings & Sessions</small>
                    </div>
                </a>
            </div>
        </div>

        {{-- Date Filter --}}
        <div class="filter-section">
            <form action="" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4 col-sm-8">
                        <label class="form-label"
                            style="color: var(--text-secondary); font-weight: 500; font-size: 0.85rem;">
                            <span class="material-symbols-outlined">calendar_month</span> Date Range
                        </label>
                        <input type="text" id="date_range" name="date_range" class="form-control"
                            placeholder="Select date range" value="{{ request('date_range') }}" readonly>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <button type="submit" class="btn btn-primary w-100"
                            style="border-radius: 10px; padding: 10px 20px;">
                            <span class="material-symbols-outlined"
                                style="font-size: 18px; margin-right: 6px;">search</span> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Charts Row --}}
        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h6>📈 Daily App Users</h6>
                    </div>
                    <div class="chart-body">
                        <div id="users"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h6>💰 Daily Wallet Transactions</h6>
                    </div>
                    <div class="chart-body">
                        <div id="wallet-graph"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h6>📅 Total Bookings Trend</h6>
                    </div>
                    <div class="chart-body">
                        <div id="totalBookings"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h6>🛒 Total Orders Trend</h6>
                    </div>
                    <div class="chart-body">
                        <div id="totalOrders"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Instant Consultation Row --}}
        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h6>📞 Instant Consultation Breakdown</h6>
                    </div>
                    <div class="chart-body">
                        <div id="instantCalls"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="section-title mt-0">
                    <div class="title-icon" style="background: var(--gradient-blue);">
                        <span class="material-symbols-outlined">call</span>
                    </div>
                    <span>Today's Consultations</span>
                </div>
                <div class="earning-card-modern voice">
                    <a href="{{ route('admin.instant-consultation.index') }}?type=voice">
                        <div class="earning-info">
                            <h6>Voice Calls</h6>
                            <h2>{{ $todaysVoiceCall ?? 0 }}</h2>
                        </div>
                        <div class="earning-icon">
                            <span class="material-symbols-outlined">call</span>
                        </div>
                    </a>
                </div>
                <div class="earning-card-modern video">
                    <a href="{{ route('admin.instant-consultation.index') }}?type=video">
                        <div class="earning-info">
                            <h6>Video Calls</h6>
                            <h2>{{ $todaysVideoCall ?? 0 }}</h2>
                        </div>
                        <div class="earning-icon">
                            <span class="material-symbols-outlined">videocam</span>
                        </div>
                    </a>
                </div>
                <div class="earning-card-modern chat">
                    <a href="{{ route('admin.instant-consultation.index') }}?type=chat">
                        <div class="earning-info">
                            <h6>Chat Sessions</h6>
                            <h2>{{ $todaysChatCall ?? 0 }}</h2>
                        </div>
                        <div class="earning-icon">
                            <span class="material-symbols-outlined">chat</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Bookings Stats --}}
        <div class="row mt-3">
            <div class="col-12">
                <div class="section-title">
                    <div class="title-icon" style="background: var(--gradient-orange);">
                        <span class="material-symbols-outlined">calendar_month</span>
                    </div>
                    <span>Booking Statistics</span>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="stat-glass-card orange" onclick="location.href='{{ route('admin.bookings.all') }}'">
                    <div class="stat-icon-wrapper">
                        <span class="material-symbols-outlined">layers</span>
                    </div>
                    <div class="stat-value">{{ $totalBookings ?? 0 }}</div>
                    <div class="stat-label">All Bookings</div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="stat-glass-card orange" onclick="location.href='{{ route('admin.bookings.confirmed') }}'">
                    <div class="stat-icon-wrapper">
                        <span class="material-symbols-outlined">check_circle</span>
                    </div>
                    <div class="stat-value">{{ $confirmedBookings ?? 0 }}</div>
                    <div class="stat-label">Confirmed</div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="stat-glass-card orange" onclick="location.href='{{ route('admin.bookings.started') }}'">
                    <div class="stat-icon-wrapper">
                        <span class="material-symbols-outlined">play_circle</span>
                    </div>
                    <div class="stat-value">{{ $ongoingBookings ?? 0 }}</div>
                    <div class="stat-label">Ongoing</div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="stat-glass-card orange" onclick="location.href='{{ route('admin.bookings.ended') }}'">
                    <div class="stat-icon-wrapper">
                        <span class="material-symbols-outlined">emoji_events</span>
                    </div>
                    <div class="stat-value">{{ $completedBookings ?? 0 }}</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>

        {{-- Orders Stats --}}
        <div class="row mt-2">
            <div class="col-12">
                <div class="section-title">
                    <div class="title-icon" style="background: var(--gradient-blue);">
                        <span class="material-symbols-outlined">shopping_bag</span>
                    </div>
                    <span>Order Statistics</span>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="stat-glass-card blue" onclick="location.href='{{ route('admin.orders.all') }}'">
                    <div class="stat-icon-wrapper">
                        <span class="material-symbols-outlined">inventory_2</span>
                    </div>
                    <div class="stat-value">{{ $allOrders ?? 0 }}</div>
                    <div class="stat-label">All Orders</div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="stat-glass-card blue" onclick="location.href='{{ route('admin.orders.confirmed') }}'">
                    <div class="stat-icon-wrapper">
                        <span class="material-symbols-outlined">task_alt</span>
                    </div>
                    <div class="stat-value">{{ $confirmedOrders ?? 0 }}</div>
                    <div class="stat-label">Confirmed</div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="stat-glass-card blue" onclick="location.href='{{ route('admin.orders.shipped') }}'">
                    <div class="stat-icon-wrapper">
                        <span class="material-symbols-outlined">local_shipping</span>
                    </div>
                    <div class="stat-value">{{ $shippedOrders ?? 0 }}</div>
                    <div class="stat-label">Shipped</div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="stat-glass-card blue" onclick="location.href='{{ route('admin.orders.delivered') }}'">
                    <div class="stat-icon-wrapper">
                        <span class="material-symbols-outlined">redeem</span>
                    </div>
                    <div class="stat-value">{{ $deliveredOrders ?? 0 }}</div>
                    <div class="stat-label">Delivered</div>
                </div>
            </div>
        </div>

        {{-- Platform Stats --}}
        <div class="row mt-2">
            <div class="col-12">
                <div class="section-title">
                    <div class="title-icon" style="background: var(--gradient-green);">
                        <span class="material-symbols-outlined">public</span>
                    </div>
                    <span>Platform Overview</span>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="stat-glass-card green" onclick="location.href='{{ route('admin.astros.index') }}'">
                    <div class="stat-icon-wrapper">
                        <span class="material-symbols-outlined">star</span>
                    </div>
                    <div class="stat-value">{{ $totalastros ?? 0 }}</div>
                    <div class="stat-label">Total Astrologers</div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="stat-glass-card green" onclick="location.href='{{ route('admin.users.index') }}'">
                    <div class="stat-icon-wrapper">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                    <div class="stat-value">{{ $totalUsers ?? 0 }}</div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="stat-glass-card green" onclick="location.href='{{ route('admin.banners.index') }}'">
                    <div class="stat-icon-wrapper">
                        <span class="material-symbols-outlined">image</span>
                    </div>
                    <div class="stat-value">{{ $totalBanners ?? 0 }}</div>
                    <div class="stat-label">Total Banners</div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="stat-glass-card green" onclick="location.href='{{ route('admin.products.index') }}'">
                    <div class="stat-icon-wrapper">
                        <span class="material-symbols-outlined">category</span>
                    </div>
                    <div class="stat-value">{{ $totalProducts ?? 0 }}</div>
                    <div class="stat-label">Total Products</div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('page-script')
    <script>
        // Detect dark mode for charts
        const isDarkMode = document.documentElement.classList.contains('dark-layout');
        const chartTheme = {
            mode: isDarkMode ? 'dark' : 'light',
            background: 'transparent',
            foreColor: isDarkMode ? '#b4b7bd' : '#6c757d'
        };

        // Modern chart color palette
        const modernColors = {
            primary: '#667eea',
            success: '#11998e',
            warning: '#f5576c',
            info: '#4facfe',
            purple: '#764ba2',
            gradient: ['#667eea', '#764ba2']
        };

        // Enhanced Bar Chart Function
        function initModernBarChart({
            selector,
            categories,
            data,
            label = 'label',
            colors = [modernColors.primary, modernColors.purple]
        }) {
            const chartEl = document.querySelector(selector);
            if (!chartEl) return;

            const options = {
                chart: {
                    height: 320,
                    type: 'bar',
                    toolbar: {
                        show: false
                    },
                    background: 'transparent',
                    fontFamily: 'Inter, sans-serif'
                },
                theme: chartTheme,
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        columnWidth: '50%',
                        distributed: false,
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                colors: colors,
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: isDarkMode ? 'dark' : 'light',
                        type: 'vertical',
                        shadeIntensity: 0.3,
                        gradientToColors: [colors[1] || colors[0]],
                        opacityFrom: 1,
                        opacityTo: 0.8,
                    }
                },
                series: [{
                    name: label,
                    data: data
                }],
                xaxis: {
                    categories: categories,
                    labels: {
                        style: {
                            colors: chartTheme.foreColor,
                            fontSize: '11px'
                        },
                        rotate: -45,
                        rotateAlways: false
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: chartTheme.foreColor,
                            fontSize: '11px'
                        }
                    }
                },
                grid: {
                    borderColor: isDarkMode ? '#3b4253' : '#e9ecef',
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: false
                        }
                    }
                },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light',
                    y: {
                        formatter: (val) => val.toLocaleString()
                    }
                }
            };

            new ApexCharts(chartEl, options).render();
        }

        // Enhanced Donut Chart Function
        function initModernDonutChart({
            selector,
            categories,
            data,
            label = 'label'
        }) {
            const chartEl = document.querySelector(selector);
            if (!chartEl) return;

            const options = {
                chart: {
                    height: 350,
                    type: 'donut',
                    background: 'transparent',
                    fontFamily: 'Inter, sans-serif'
                },
                theme: chartTheme,
                labels: categories.map(cat => cat || 'Unknown'),
                series: data,
                colors: [modernColors.primary, modernColors.success, modernColors.info, modernColors.warning],
                stroke: {
                    width: 3,
                    colors: [isDarkMode ? '#283046' : '#ffffff']
                },
                dataLabels: {
                    enabled: true,
                    formatter: (val) => Math.round(val) + '%',
                    style: {
                        fontSize: '12px',
                        fontWeight: 600
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: {
                                    fontSize: '16px',
                                    fontWeight: 600,
                                    color: chartTheme.foreColor
                                },
                                value: {
                                    fontSize: '24px',
                                    fontWeight: 700,
                                    color: chartTheme.foreColor,
                                    formatter: (val) => parseInt(val).toLocaleString()
                                },
                                total: {
                                    show: true,
                                    label: label,
                                    fontSize: '14px',
                                    fontWeight: 500,
                                    color: chartTheme.foreColor,
                                    formatter: () => data.reduce((a, b) => a + b, 0).toLocaleString()
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '13px',
                    fontWeight: 500,
                    labels: {
                        colors: chartTheme.foreColor
                    },
                    itemMargin: {
                        horizontal: 12,
                        vertical: 8
                    }
                },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light',
                    y: {
                        formatter: (val) => val.toLocaleString()
                    }
                },
                responsive: [{
                    breakpoint: 576,
                    options: {
                        chart: {
                            height: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            new ApexCharts(chartEl, options).render();
        }

        // Initialize Charts
        const bookingAmount = @json($monthlyBookings->pluck('amount')->toArray());
        const bookingDate = @json($monthlyBookings->pluck('date')->toArray());
        initModernBarChart({
            selector: '#totalBookings',
            categories: bookingDate,
            data: bookingAmount,
            label: 'Bookings',
            colors: ['#ff6b6b', '#feca57']
        });

        const orderAmount = @json($monthlyOrders->pluck('amount')->toArray());
        const orderDate = @json($monthlyOrders->pluck('date')->toArray());
        initModernBarChart({
            selector: '#totalOrders',
            categories: orderDate,
            data: orderAmount,
            label: 'Orders',
            colors: ['#4facfe', '#00f2fe']
        });

        const instantType = @json($monthlyInstantConsultation->pluck('session_type')->toArray());
        const InstantCount = @json($monthlyInstantConsultation->pluck('count')->toArray());
        initModernDonutChart({
            selector: '#instantCalls',
            categories: instantType,
            data: InstantCount,
            label: 'Consultations'
        });

        const usersCount = @json($monthlyUsers->pluck('count')->toArray());
        const userDate = @json($monthlyUsers->pluck('date')->toArray());
        initModernBarChart({
            selector: '#users',
            categories: userDate,
            data: usersCount,
            label: 'New Users',
            colors: ['#667eea', '#764ba2']
        });

        const monthlyAmount = @json($monthlyWallet->pluck('amount')->toArray());
        const monthlyDate = @json($monthlyWallet->pluck('date')->toArray());
        initModernBarChart({
            selector: '#wallet-graph',
            categories: monthlyDate,
            data: monthlyAmount,
            label: 'Transactions',
            colors: ['#11998e', '#38ef7d']
        });
    </script>
    <script>
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: @json(request('date_range') ? explode(' to ', request('date_range')) : null),
        });
    </script>
@endsection
