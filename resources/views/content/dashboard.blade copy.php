@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard Analytics')

@section('page-style')
    <style>
        body {
            background: url('{{ asset('images/bg-pattern.png') }}') no-repeat center center fixed;
            background-size: cover;
        }

        .card {
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            padding: 15px;
        }

        .earning-card {
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            text-align: left;
        }

        .earning-card h2 {
            font-weight: 700;
            margin: 0;
            color: #d35400;
        }

        .earning-card h6 {
            font-weight: 600;
            margin-bottom: 6px;
        }

        .stats-card {
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            transition: all .3s ease;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
        }

        .stats-card h2 {
            font-weight: 700;
            margin: 0;
        }

        .stats-card h6 {
            margin: 0;
            font-weight: 600;
            color: #666;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Top Row --}}
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <h6 class="mb-1">Total Bookings</h6>
                    <div id="totalBookings"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <h6 class="mb-1">Total Orders</h6>
                    <div id="totalOrders"></div>
                </div>
            </div>
        </div>

        {{-- Second Row --}}
        <div class="row mt-2">
            <div class="col-lg-6">
                <div class="card">
                    <h6 class="mb-1">Total Instant Calls</h6>
                    <div id="instantCalls"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="earning-card">
                    <h6>Today's Earnings</h6>
                    <h2>{{ $todaysRevenue ?? 400 }}</h2>
                </div>
                <div class="earning-card">
                    <h6>This Month's Earnings</h6>
                    <h2>{{ $thisMonthRevenue ?? 1000 }}</h2>
                </div>
                <div class="earning-card">
                    <h6>Lifetime's Earnings</h6>
                    <h2>{{ $lifetimeRevenue ?? 200000 }}</h2>
                </div>
            </div>
        </div>

        {{-- Bottom Grid --}}
        <div class="row mt-3">
            <div class="col-lg-3 col-sm-6">
                <div class="stats-card cursor-pointer" onclick="location.href='{{ route('admin.users.index') }}'">
                    <div>
                        <h2>{{ $totalUsers ?? 0 }}</h2>
                        <h6>Total Users</h6>
                    </div>
                    <div><i data-feather="users"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="stats-card cursor-pointer" onclick="location.href='{{ route('admin.banners.index') }}'">
                    <div>
                        <h2>{{ $totalBanners ?? 0 }}</h2>
                        <h6>Banners</h6>
                    </div>
                    <div><i data-feather="image"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="stats-card cursor-pointer" onclick="location.href='{{ route('admin.astros.index') }}'">
                    <div>
                        <h2>{{ $totalAstros ?? 0 }}</h2>
                        <h6>Total Astros</h6>
                    </div>
                    <div><i data-feather="star"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="stats-card cursor-pointer" onclick="location.href='{{ route('admin.bookings.all') }}'">
                    <div>
                        <h2>{{ $totalBookings ?? 0 }}</h2>
                        <h6>Astro Bookings</h6>
                    </div>
                    <div><i data-feather="calendar"></i></div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('page-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.2.0/apexcharts.min.js"></script>
    <script>
        const bookingCount = @json($bookingsMonthly->pluck('count')->toArray());
        const bookingDate = @json($bookingLabels->toArray());
        new ApexCharts(document.querySelector("#totalBookings"), {
            chart: {
                type: 'bar',
                height: 250,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: "Bookings",
                data: bookingCount
            }],
            xaxis: {
                categories: bookingDate
            },
            colors: ['#ff7675'],
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '45%'
                }
            },
            dataLabels: {
                enabled: false
            }
        }).render();


        const orderCount = @json($ordersMonthly->pluck('count')->toArray());
        const orderDate = @json($ordersLabels->toArray());
        new ApexCharts(document.querySelector("#totalOrders"), {
            chart: {
                type: 'bar',
                height: 250,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: "Orders",
                data: orderCount
            }],
            xaxis: {
                categories: orderDate
            },
            colors: ['#f6ad55'],
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '45%'
                }
            },
            dataLabels: {
                enabled: false
            }
        }).render();


        new ApexCharts(document.querySelector("#instantCalls"), {
            chart: {
                type: 'donut',
                height: 320
            },
            series: [52.1, 22.8, 13.9, 11.2],
            labels: ["Jan 01 2025", "Jan 02 2025", "Jan 03 2025", "Jan 04 2025"],
            colors: ['#2E86DE', '#FF6B6B', '#FFD43B', '#FF922B'],
            legend: {
                position: 'right',
                fontSize: '14px'
            },
            dataLabels: {
                style: {
                    fontSize: '14px'
                }
            }
        }).render();
    </script>
@endsection
