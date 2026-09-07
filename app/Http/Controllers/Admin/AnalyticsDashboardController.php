<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astro;
use App\Models\Booking;
use App\Models\BookingTransaction;
use App\Models\InstantConsultationHistory;
use App\Models\Order;
use App\Models\OrderTransaction;
use App\Models\Product;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class AnalyticsDashboardController extends Controller
{
    /**
     * Parse date range from request
     */
    private function parseDateRange(Request $request)
    {
        $startDate = null;
        $endDate = null;
        $useDateFilter = false;

        if ($request->filled('date_range')) {
            $parts = array_map('trim', explode(' to ', $request->date_range));
            if (count($parts) === 2) {
                $startDate = $parts[0];
                $endDate = $parts[1];
                $useDateFilter = true;
            }
        }

        // Default to last 30 days if no filter
        if (!$useDateFilter && $request->filled('period')) {
            $period = $request->period;
            $endDate = Carbon::now()->format('Y-m-d');
            
            switch ($period) {
                case '7days':
                    $startDate = Carbon::now()->subDays(7)->format('Y-m-d');
                    break;
                case '30days':
                    $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
                    break;
                case '90days':
                    $startDate = Carbon::now()->subDays(90)->format('Y-m-d');
                    break;
                case 'year':
                    $startDate = Carbon::now()->subYear()->format('Y-m-d');
                    break;
                default:
                    $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            }
            $useDateFilter = true;
        }

        return compact('startDate', 'endDate', 'useDateFilter');
    }

    /**
     * Apply date filter to query
     */
    private function applyDateFilter($query, $useDateFilter, $startDate, $endDate, $column = 'created_at')
    {
        if ($useDateFilter) {
            $query->whereBetween(DB::raw("DATE($column)"), [$startDate, $endDate]);
        }
        return $query;
    }

    /**
     * User Analytics Dashboard
     */
    public function userAnalytics(Request $request)
    {
        $pageConfigs = ['has_chart' => true];
        $dateParams = $this->parseDateRange($request);
        extract($dateParams);

        // Total users
        $totalUsers = User::withoutAdmin()
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->count();

        // Active users
        $activeUsers = User::withoutAdmin()->active()
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->count();

        // New users today
        $newUsersToday = User::withoutAdmin()
            ->whereDate('created_at', Carbon::today())
            ->count();

        // New users this week
        $newUsersThisWeek = User::withoutAdmin()
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        // New users this month
        $newUsersThisMonth = User::withoutAdmin()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // User growth over time (daily)
        $userGrowth = User::withoutAdmin()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // User growth monthly
        $monthlyUserGrowth = User::withoutAdmin()
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', '>=', Carbon::now()->subYear()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Gender distribution
        $genderDistribution = User::withoutAdmin()
            ->select('gender', DB::raw('COUNT(*) as count'))
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('gender')
            ->get();

        // Users by country
        $usersByCountry = User::withoutAdmin()
            ->select('countries.name as country', DB::raw('COUNT(users.id) as count'))
            ->leftJoin('countries', 'users.country_id', '=', 'countries.id')
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate, 'users.created_at'))
            ->groupBy('countries.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Users with bookings
        $usersWithBookings = User::withoutAdmin()
            ->whereHas('bookings')
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->count();

        // Users with orders
        $usersWithOrders = User::withoutAdmin()
            ->whereHas('orders')
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->count();

        // Most active users (by bookings)
        $topUsersByBookings = User::withoutAdmin()
            ->withCount(['bookings' => function($q) use ($useDateFilter, $startDate, $endDate) {
                if ($useDateFilter) {
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
                }
            }])
            ->orderByDesc('bookings_count')
            ->limit(10)
            ->get();

        // User retention - returning users (users who made more than 1 booking)
        $returningUsers = User::withoutAdmin()
            ->has('bookings', '>', 1)
            ->count();

        $retentionRate = $usersWithBookings > 0 
            ? round(($returningUsers / $usersWithBookings) * 100, 2) 
            : 0;

        return view('content.dashboards.user-analytics', compact(
            'pageConfigs',
            'totalUsers',
            'activeUsers',
            'newUsersToday',
            'newUsersThisWeek',
            'newUsersThisMonth',
            'userGrowth',
            'monthlyUserGrowth',
            'genderDistribution',
            'usersByCountry',
            'usersWithBookings',
            'usersWithOrders',
            'topUsersByBookings',
            'returningUsers',
            'retentionRate',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Revenue Analytics Dashboard
     */
    public function revenueAnalytics(Request $request)
    {
        $pageConfigs = ['has_chart' => true];
        $dateParams = $this->parseDateRange($request);
        extract($dateParams);

        // Total Booking Revenue
        $totalBookingRevenue = Booking::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->sum('final_total');

        // Total Order Revenue
        $totalOrderRevenue = Order::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->sum('total');

        // Total Instant Consultation Revenue
        $totalInstantRevenue = InstantConsultationHistory::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->sum('amount');

        // Total Wallet Recharges
        $totalWalletRecharges = WalletTransaction::where('type', 'credit')
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->sum('amount');

        // Today's Revenue
        $todayBookingRevenue = Booking::whereDate('created_at', Carbon::today())->sum('final_total');
        $todayOrderRevenue = Order::whereDate('created_at', Carbon::today())->sum('total');
        $todayInstantRevenue = InstantConsultationHistory::whereDate('created_at', Carbon::today())->sum('amount');
        $todayWalletRecharges = WalletTransaction::where('type', 'credit')->whereDate('created_at', Carbon::today())->sum('amount');

        // This week's revenue
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $weekBookingRevenue = Booking::whereBetween('created_at', [$weekStart, $weekEnd])->sum('final_total');
        $weekOrderRevenue = Order::whereBetween('created_at', [$weekStart, $weekEnd])->sum('total');

        // This month's revenue
        $monthBookingRevenue = Booking::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('final_total');
        $monthOrderRevenue = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total');

        // Daily revenue trend
        $dailyRevenue = Booking::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(final_total) as booking_revenue')
            )
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dailyOrderRevenue = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as order_revenue')
            )
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dailyInstantRevenue = InstantConsultationHistory::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as instant_revenue')
            )
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Monthly revenue trend
        $monthlyRevenue = Booking::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(final_total) as revenue')
            )
            ->whereYear('created_at', '>=', Carbon::now()->subYear()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Revenue by booking type
        $revenueByBookingType = Booking::select('type', DB::raw('SUM(final_total) as revenue'), DB::raw('COUNT(*) as count'))
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('type')
            ->get();

        // Revenue by instant consultation type
        $revenueByInstantType = InstantConsultationHistory::select('session_type', DB::raw('SUM(amount) as revenue'), DB::raw('COUNT(*) as count'))
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('session_type')
            ->get();

        // Wallet transaction types
        $walletTransactionSummary = WalletTransaction::select('type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('type')
            ->get();

        // Average order value
        $avgBookingValue = Booking::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->avg('final_total') ?? 0;

        $avgOrderValue = Order::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->avg('total') ?? 0;

        // Promocode usage
        $promocodeUsage = Booking::whereNotNull('promocode')
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->select('promocode', DB::raw('COUNT(*) as usage_count'), DB::raw('SUM(promocode_discount) as total_discount'))
            ->groupBy('promocode')
            ->orderByDesc('usage_count')
            ->limit(10)
            ->get();

        // Total discount given
        $totalPromoDiscount = Booking::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->sum('promocode_discount');

        return view('content.dashboards.revenue-analytics', compact(
            'pageConfigs',
            'totalBookingRevenue',
            'totalOrderRevenue',
            'totalInstantRevenue',
            'totalWalletRecharges',
            'todayBookingRevenue',
            'todayOrderRevenue',
            'todayInstantRevenue',
            'todayWalletRecharges',
            'weekBookingRevenue',
            'weekOrderRevenue',
            'monthBookingRevenue',
            'monthOrderRevenue',
            'dailyRevenue',
            'dailyOrderRevenue',
            'dailyInstantRevenue',
            'monthlyRevenue',
            'revenueByBookingType',
            'revenueByInstantType',
            'walletTransactionSummary',
            'avgBookingValue',
            'avgOrderValue',
            'promocodeUsage',
            'totalPromoDiscount',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Astrologer Analytics Dashboard
     */
    public function astrologerAnalytics(Request $request)
    {
        $pageConfigs = ['has_chart' => true];
        $dateParams = $this->parseDateRange($request);
        extract($dateParams);

        // Total astrologers
        $totalAstrologers = Astro::count();
        $activeAstrologers = Astro::active()->count();

        // New astrologers this month
        $newAstrologersThisMonth = Astro::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Top astrologers by bookings
        $topAstrosByBookings = Astro::withCount(['bookings' => function($q) use ($useDateFilter, $startDate, $endDate) {
                if ($useDateFilter) {
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
                }
            }])
            ->withSum(['bookings' => function($q) use ($useDateFilter, $startDate, $endDate) {
                if ($useDateFilter) {
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
                }
            }], 'final_total')
            ->orderByDesc('bookings_count')
            ->limit(10)
            ->get();

        // Top astrologers by revenue
        $topAstrosByRevenue = Astro::withSum(['bookings' => function($q) use ($useDateFilter, $startDate, $endDate) {
                if ($useDateFilter) {
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
                }
            }], 'final_total')
            ->withSum(['instantConsultationHistories' => function($q) use ($useDateFilter, $startDate, $endDate) {
                if ($useDateFilter) {
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
                }
            }], 'amount')
            ->orderByDesc('bookings_sum_final_total')
            ->limit(10)
            ->get();

        // Top astrologers by ratings
        $topAstrosByRating = Astro::withAvg('review', 'rating')
            ->withCount('review')
            ->having('review_count', '>', 0)
            ->orderByDesc('review_avg_rating')
            ->limit(10)
            ->get();

        // Astrologer performance summary
        $astroPerformance = Astro::select('astros.id', 'astros.first_name', 'astros.last_name', 'astros.profile', 'astros.status')
            ->withCount(['bookings' => function($q) use ($useDateFilter, $startDate, $endDate) {
                if ($useDateFilter) {
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
                }
            }])
            ->withCount(['instantConsultationHistories' => function($q) use ($useDateFilter, $startDate, $endDate) {
                if ($useDateFilter) {
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
                }
            }])
            ->withSum(['bookings' => function($q) use ($useDateFilter, $startDate, $endDate) {
                if ($useDateFilter) {
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
                }
            }], 'final_total')
            ->withSum(['instantConsultationHistories' => function($q) use ($useDateFilter, $startDate, $endDate) {
                if ($useDateFilter) {
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
                }
            }], 'amount')
            ->withAvg('review', 'rating')
            ->orderByDesc('bookings_count')
            ->limit(20)
            ->get();

        // Astrologers by service
        $astrosByService = Service::active()
            ->withCount('astros')
            ->orderByDesc('astros_count')
            ->get();

        // Followers distribution
        $topAstrosByFollowers = Astro::withCount('followers')
            ->orderByDesc('followers_count')
            ->limit(10)
            ->get();

        // Average ratings
        $avgRating = Review::avg('rating') ?? 0;

        // Rating distribution
        $ratingDistribution = Review::select('rating', DB::raw('COUNT(*) as count'))
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();

        // Monthly consultations per astrologer (average)
        $monthlyConsultationAvg = Booking::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) / COUNT(DISTINCT astro_id) as avg_consultations')
            )
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('content.dashboards.astrologer-analytics', compact(
            'pageConfigs',
            'totalAstrologers',
            'activeAstrologers',
            'newAstrologersThisMonth',
            'topAstrosByBookings',
            'topAstrosByRevenue',
            'topAstrosByRating',
            'astroPerformance',
            'astrosByService',
            'topAstrosByFollowers',
            'avgRating',
            'ratingDistribution',
            'monthlyConsultationAvg',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Consultation Analytics Dashboard
     */
    public function consultationAnalytics(Request $request)
    {
        $pageConfigs = ['has_chart' => true];
        $dateParams = $this->parseDateRange($request);
        extract($dateParams);

        // === SCHEDULED BOOKINGS ===
        $totalBookings = Booking::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->count();
        $startedBookings = Booking::where('status', 'started')->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->count();
        $endedBookings = Booking::where('status', 'ended')->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->count();

        // Bookings by type
        $bookingsByType = Booking::select('type', DB::raw('COUNT(*) as count'))
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('type')
            ->get();

        // Daily bookings trend
        $dailyBookings = Booking::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Monthly bookings
        $monthlyBookings = Booking::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(final_total) as revenue')
            )
            ->whereYear('created_at', '>=', Carbon::now()->subYear()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Bookings by day of week
        $bookingsByDayOfWeek = Booking::select(
                DB::raw('DAYNAME(created_at) as day_name'),
                DB::raw('DAYOFWEEK(created_at) as day_num'),
                DB::raw('COUNT(*) as count')
            )
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('day_name', 'day_num')
            ->orderBy('day_num')
            ->get();

        // Bookings by hour
        $bookingsByHour = Booking::select(
                DB::raw('HOUR(start_time) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Average booking duration
        $avgBookingDuration = Booking::whereNotNull('duration')
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->avg('duration') ?? 0;

        // === INSTANT CONSULTATIONS ===
        $totalInstantConsultations = InstantConsultationHistory::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->count();
        $ongoingConsultations = InstantConsultationHistory::where('status', 'ongoing')->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->count();
        $endedConsultations = InstantConsultationHistory::where('status', 'ended')->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->count();
        $refundedConsultations = InstantConsultationHistory::where('status', 'refunded')->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->count();

        // Instant consultations by type
        $instantByType = InstantConsultationHistory::select('session_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as revenue'))
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('session_type')
            ->get();

        // Daily instant consultations
        $dailyInstant = InstantConsultationHistory::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as revenue')
            )
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Monthly instant consultations
        $monthlyInstant = InstantConsultationHistory::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as revenue')
            )
            ->whereYear('created_at', '>=', Carbon::now()->subYear()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Instant consultations by hour
        $instantByHour = InstantConsultationHistory::select(
                DB::raw('HOUR(started_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->whereNotNull('started_at')
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Average instant consultation duration
        $avgInstantDuration = InstantConsultationHistory::whereNotNull('duration')
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->avg('duration') ?? 0;

        // Today's stats
        $todayBookings = Booking::whereDate('created_at', Carbon::today())->count();
        $todayInstantVoice = InstantConsultationHistory::whereDate('created_at', Carbon::today())->where('session_type', 'voice')->count();
        $todayInstantVideo = InstantConsultationHistory::whereDate('created_at', Carbon::today())->where('session_type', 'video')->count();
        $todayInstantChat = InstantConsultationHistory::whereDate('created_at', Carbon::today())->where('session_type', 'chat')->count();

        // Comparison with previous period
        $currentPeriodBookings = Booking::whereBetween(DB::raw('DATE(created_at)'), [Carbon::now()->subDays(30), Carbon::now()])->count();
        $previousPeriodBookings = Booking::whereBetween(DB::raw('DATE(created_at)'), [Carbon::now()->subDays(60), Carbon::now()->subDays(30)])->count();
        $bookingGrowth = $previousPeriodBookings > 0 
            ? round((($currentPeriodBookings - $previousPeriodBookings) / $previousPeriodBookings) * 100, 2) 
            : 0;

        return view('content.dashboards.consultation-analytics', compact(
            'pageConfigs',
            'totalBookings',
            'confirmedBookings',
            'startedBookings',
            'endedBookings',
            'bookingsByType',
            'dailyBookings',
            'monthlyBookings',
            'bookingsByDayOfWeek',
            'bookingsByHour',
            'avgBookingDuration',
            'totalInstantConsultations',
            'ongoingConsultations',
            'endedConsultations',
            'refundedConsultations',
            'instantByType',
            'dailyInstant',
            'monthlyInstant',
            'instantByHour',
            'avgInstantDuration',
            'todayBookings',
            'todayInstantVoice',
            'todayInstantVideo',
            'todayInstantChat',
            'bookingGrowth',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Order Analytics Dashboard
     */
    public function orderAnalytics(Request $request)
    {
        $pageConfigs = ['has_chart' => true];
        $dateParams = $this->parseDateRange($request);
        extract($dateParams);

        // Order counts by status
        $totalOrders = Order::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->count();
        $confirmedOrders = Order::where('status', 'confirmed')->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->count();
        $shippedOrders = Order::where('status', 'shipped')->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->count();
        $deliveredOrders = Order::where('status', 'delivered')->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->count();
        $cancelledOrders = Order::where('status', 'cancelled')->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->count();

        // Total revenue
        $totalOrderRevenue = Order::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->sum('total');

        // Today's orders
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $todayOrderRevenue = Order::whereDate('created_at', Carbon::today())->sum('total');

        // Average order value
        $avgOrderValue = Order::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))->avg('total') ?? 0;

        // Daily orders
        $dailyOrders = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue')
            )
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Monthly orders
        $monthlyOrders = Order::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue')
            )
            ->whereYear('created_at', '>=', Carbon::now()->subYear()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Orders by status
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->groupBy('status')
            ->get();

        // Top products (would need to join with order_details)
        $topProducts = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select(
                'products.id',
                'products.title',
                DB::raw('COUNT(order_details.id) as order_count'),
                DB::raw('SUM(order_details.quantity) as total_quantity'),
                DB::raw('SUM(order_details.total) as total_revenue')
            )
            ->when($useDateFilter, function($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw('DATE(orders.created_at)'), [$startDate, $endDate]);
            })
            ->groupBy('products.id', 'products.title')
            ->orderByDesc('order_count')
            ->limit(10)
            ->get();

        // Fulfillment rate
        $fulfillmentRate = $totalOrders > 0 
            ? round(($deliveredOrders / $totalOrders) * 100, 2) 
            : 0;

        // Cancellation rate
        $cancellationRate = $totalOrders > 0 
            ? round(($cancelledOrders / $totalOrders) * 100, 2) 
            : 0;

        // Average delivery time (days between order and delivery)
        $avgDeliveryDays = Order::whereNotNull('delivered_at')
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->selectRaw('AVG(DATEDIFF(delivered_at, created_at)) as avg_days')
            ->value('avg_days') ?? 0;

        return view('content.dashboards.order-analytics', compact(
            'pageConfigs',
            'totalOrders',
            'confirmedOrders',
            'shippedOrders',
            'deliveredOrders',
            'cancelledOrders',
            'totalOrderRevenue',
            'todayOrders',
            'todayOrderRevenue',
            'avgOrderValue',
            'dailyOrders',
            'monthlyOrders',
            'ordersByStatus',
            'topProducts',
            'fulfillmentRate',
            'cancellationRate',
            'avgDeliveryDays',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Real-Time Analytics Dashboard
     */
    public function realtimeAnalytics(Request $request)
    {
        $pageConfigs = ['has_chart' => true];
        
        $now = Carbon::now();
        $today = Carbon::today();
        $thisHour = $now->copy()->startOfHour();
        
        // Active users (users who have been active in last 30 minutes based on updated_at)
        $activeUsersNow = User::withoutAdmin()
            ->where('updated_at', '>=', $now->copy()->subMinutes(30))
            ->count();
        
        // Users online today
        $usersOnlineToday = User::withoutAdmin()
            ->whereDate('updated_at', $today)
            ->count();
        
        // Ongoing consultations (instant consultations that started but haven't ended)
        $ongoingInstantConsultations = InstantConsultationHistory::whereDate('created_at', $today)
            ->whereNull('ended_at')
            ->orWhere(function($q) use ($now) {
                $q->where('ended_at', '>=', $now->copy()->subMinutes(5));
            })
            ->count();
        
        // Ongoing consultations by type
        $ongoingByType = InstantConsultationHistory::select('session_type', DB::raw('COUNT(*) as count'))
            ->whereDate('created_at', $today)
            ->whereNull('ended_at')
            ->groupBy('session_type')
            ->pluck('count', 'session_type')
            ->toArray();
        
        // Today's stats
        $todayUsers = User::withoutAdmin()->whereDate('created_at', $today)->count();
        $todayBookings = Booking::whereDate('created_at', $today)->count();
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $todayInstantCalls = InstantConsultationHistory::whereDate('created_at', $today)->count();
        
        // Today's revenue
        $todayBookingRevenue = BookingTransaction::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('amount');
        $todayOrderRevenue = OrderTransaction::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('amount');
        $todayInstantRevenue = InstantConsultationHistory::whereDate('created_at', $today)
            ->sum('total_amount');
        $todayWalletRecharges = WalletTransaction::where('type', 'credit')
            ->whereDate('created_at', $today)
            ->sum('amount');
        $todayTotalRevenue = $todayBookingRevenue + $todayOrderRevenue + $todayInstantRevenue + $todayWalletRecharges;
        
        // Hourly breakdown for today
        $hourlyUsers = User::withoutAdmin()
            ->whereDate('created_at', $today)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->pluck('count', 'hour')
            ->toArray();
        
        $hourlyBookings = Booking::whereDate('created_at', $today)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->pluck('count', 'hour')
            ->toArray();
        
        $hourlyRevenue = [];
        for ($i = 0; $i <= $now->hour; $i++) {
            $hourStart = $today->copy()->addHours($i);
            $hourEnd = $hourStart->copy()->addHour();
            
            $bookingRev = BookingTransaction::where('status', 'paid')
                ->whereBetween('created_at', [$hourStart, $hourEnd])
                ->sum('amount');
            $orderRev = OrderTransaction::where('status', 'paid')
                ->whereBetween('created_at', [$hourStart, $hourEnd])
                ->sum('amount');
            $instantRev = InstantConsultationHistory::whereBetween('created_at', [$hourStart, $hourEnd])
                ->sum('total_amount');
            
            $hourlyRevenue[$i] = $bookingRev + $orderRev + $instantRev;
        }
        
        // Astrologer availability
        $totalAstrologers = Astro::active()->count();
        $availableAstrologers = Astro::active()
            ->where('is_available', true)
            ->count();
        $busyAstrologers = $totalAstrologers - $availableAstrologers;
        
        // Top performing astrologers today
        $topAstrologersToday = DB::table('instant_consultation_histories')
            ->join('astros', 'instant_consultation_histories.astro_id', '=', 'astros.id')
            ->whereDate('instant_consultation_histories.created_at', $today)
            ->select(
                'astros.id',
                'astros.first_name',
                'astros.last_name',
                'astros.image',
                DB::raw('COUNT(*) as session_count'),
                DB::raw('SUM(instant_consultation_histories.total_amount) as total_revenue')
            )
            ->groupBy('astros.id', 'astros.first_name', 'astros.last_name', 'astros.image')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
        
        // Pending bookings for today
        $pendingBookingsToday = Booking::whereDate('date', $today)
            ->where('status', 'confirmed')
            ->count();
        
        // Recent activities (last 10)
        $recentBookings = Booking::with(['user', 'astro'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($b) {
                return [
                    'type' => 'booking',
                    'title' => 'New Booking',
                    'description' => ($b->user->first_name ?? 'User') . ' booked with ' . ($b->astro->first_name ?? 'Astrologer'),
                    'time' => $b->created_at,
                    'icon' => 'calendar'
                ];
            });
        
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($o) {
                return [
                    'type' => 'order',
                    'title' => 'New Order',
                    'description' => ($o->user->first_name ?? 'User') . ' placed order #' . $o->id,
                    'time' => $o->created_at,
                    'icon' => 'shopping-cart'
                ];
            });
        
        $recentActivities = $recentBookings->concat($recentOrders)
            ->sortByDesc('time')
            ->take(10)
            ->values();
        
        // Format hourly data for charts
        $hourLabels = [];
        $hourlyUsersData = [];
        $hourlyBookingsData = [];
        $hourlyRevenueData = [];
        
        for ($i = 0; $i <= 23; $i++) {
            $hourLabels[] = sprintf('%02d:00', $i);
            $hourlyUsersData[] = $hourlyUsers[$i] ?? 0;
            $hourlyBookingsData[] = $hourlyBookings[$i] ?? 0;
            $hourlyRevenueData[] = $hourlyRevenue[$i] ?? 0;
        }
        
        return view('content.dashboards.realtime-analytics', compact(
            'pageConfigs',
            'activeUsersNow',
            'usersOnlineToday',
            'ongoingInstantConsultations',
            'ongoingByType',
            'todayUsers',
            'todayBookings',
            'todayOrders',
            'todayInstantCalls',
            'todayBookingRevenue',
            'todayOrderRevenue',
            'todayInstantRevenue',
            'todayWalletRecharges',
            'todayTotalRevenue',
            'totalAstrologers',
            'availableAstrologers',
            'busyAstrologers',
            'topAstrologersToday',
            'pendingBookingsToday',
            'recentActivities',
            'hourLabels',
            'hourlyUsersData',
            'hourlyBookingsData',
            'hourlyRevenueData'
        ));
    }

    /**
     * Retention & Churn Analytics Dashboard
     */
    public function retentionAnalytics(Request $request)
    {
        $pageConfigs = ['has_chart' => true];
        $dateParams = $this->parseDateRange($request);
        extract($dateParams);
        
        // Default to last 90 days if no filter
        if (!$useDateFilter) {
            $startDate = Carbon::now()->subDays(90)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
            $useDateFilter = true;
        }
        
        // Total users
        $totalUsers = User::withoutAdmin()->count();
        
        // Active users (made a booking or order in the period)
        $activeUserIds = collect();
        
        $bookingUserIds = Booking::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->pluck('user_id');
        $orderUserIds = Order::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->pluck('user_id');
        $instantUserIds = InstantConsultationHistory::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->pluck('user_id');
        
        $activeUserIds = $bookingUserIds->merge($orderUserIds)->merge($instantUserIds)->unique();
        $activeUsersCount = $activeUserIds->count();
        
        // Churned users (registered before period but not active in period)
        $registeredBefore = User::withoutAdmin()
            ->whereDate('created_at', '<', $startDate)
            ->pluck('id');
        $churnedUsers = $registeredBefore->diff($activeUserIds)->count();
        
        // Churn rate
        $churnRate = $registeredBefore->count() > 0 
            ? round(($churnedUsers / $registeredBefore->count()) * 100, 2) 
            : 0;
        
        // Retention rate
        $retentionRate = 100 - $churnRate;
        
        // New users in period
        $newUsersInPeriod = User::withoutAdmin()
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->count();
        
        // Returning users (active users who registered before period)
        $returningUsers = $registeredBefore->intersect($activeUserIds)->count();
        
        // Users by activity level
        $highlyActive = User::withoutAdmin()
            ->whereIn('id', function($q) use ($startDate, $endDate) {
                $q->select('user_id')
                    ->from('bookings')
                    ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
                    ->groupBy('user_id')
                    ->havingRaw('COUNT(*) >= 5');
            })
            ->count();
        
        $moderatelyActive = User::withoutAdmin()
            ->whereIn('id', function($q) use ($startDate, $endDate) {
                $q->select('user_id')
                    ->from('bookings')
                    ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
                    ->groupBy('user_id')
                    ->havingRaw('COUNT(*) >= 2 AND COUNT(*) < 5');
            })
            ->count();
        
        $lowActive = $activeUsersCount - $highlyActive - $moderatelyActive;
        if ($lowActive < 0) $lowActive = 0;
        
        // At-risk users (last activity > 30 days ago)
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $atRiskUsers = User::withoutAdmin()
            ->where('updated_at', '<', $thirtyDaysAgo)
            ->whereNotIn('id', $activeUserIds)
            ->count();
        
        // Cohort analysis - monthly cohorts
        $cohorts = [];
        for ($i = 5; $i >= 0; $i--) {
            $cohortMonth = Carbon::now()->subMonths($i);
            $cohortStart = $cohortMonth->copy()->startOfMonth();
            $cohortEnd = $cohortMonth->copy()->endOfMonth();
            
            // Users who registered in this month
            $cohortUsers = User::withoutAdmin()
                ->whereBetween('created_at', [$cohortStart, $cohortEnd])
                ->pluck('id');
            
            $cohortData = [
                'month' => $cohortMonth->format('M Y'),
                'users' => $cohortUsers->count(),
                'retention' => []
            ];
            
            // Calculate retention for subsequent months
            for ($j = 0; $j <= (5 - $i); $j++) {
                $checkMonth = $cohortMonth->copy()->addMonths($j);
                $checkStart = $checkMonth->copy()->startOfMonth();
                $checkEnd = $checkMonth->copy()->endOfMonth();
                
                // Active users from cohort in this check month
                $activeFromCohort = Booking::whereIn('user_id', $cohortUsers)
                    ->whereBetween('created_at', [$checkStart, $checkEnd])
                    ->distinct('user_id')
                    ->count('user_id');
                
                $retentionPct = $cohortUsers->count() > 0 
                    ? round(($activeFromCohort / $cohortUsers->count()) * 100, 1) 
                    : 0;
                
                $cohortData['retention'][] = $retentionPct;
            }
            
            $cohorts[] = $cohortData;
        }
        
        // Monthly retention trend
        $monthlyRetention = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth()->format('Y-m-d');
            $monthEnd = $month->copy()->endOfMonth()->format('Y-m-d');
            
            $prevMonthStart = $month->copy()->subMonth()->startOfMonth()->format('Y-m-d');
            $prevMonthEnd = $month->copy()->subMonth()->endOfMonth()->format('Y-m-d');
            
            // Users active in previous month
            $prevMonthActiveIds = Booking::whereBetween(DB::raw('DATE(created_at)'), [$prevMonthStart, $prevMonthEnd])
                ->pluck('user_id')
                ->unique();
            
            // How many of them were active in current month
            $retained = Booking::whereIn('user_id', $prevMonthActiveIds)
                ->whereBetween(DB::raw('DATE(created_at)'), [$monthStart, $monthEnd])
                ->distinct('user_id')
                ->count('user_id');
            
            $retentionPct = $prevMonthActiveIds->count() > 0 
                ? round(($retained / $prevMonthActiveIds->count()) * 100, 1) 
                : 0;
            
            $monthlyRetention[] = [
                'month' => $month->format('M Y'),
                'retention' => $retentionPct,
                'churned' => 100 - $retentionPct
            ];
        }
        
        // User lifetime value segments
        $userLTVSegments = DB::table('users')
            ->leftJoin('bookings', 'users.id', '=', 'bookings.user_id')
            ->leftJoin('booking_transactions', function($join) {
                $join->on('bookings.id', '=', 'booking_transactions.booking_id')
                    ->where('booking_transactions.status', '=', 'paid');
            })
            ->select(
                'users.id',
                DB::raw('COALESCE(SUM(booking_transactions.amount), 0) as total_spent')
            )
            ->groupBy('users.id')
            ->get();
        
        $highLTV = $userLTVSegments->where('total_spent', '>=', 5000)->count();
        $mediumLTV = $userLTVSegments->where('total_spent', '>=', 1000)->where('total_spent', '<', 5000)->count();
        $lowLTV = $userLTVSegments->where('total_spent', '>', 0)->where('total_spent', '<', 1000)->count();
        $zeroLTV = $userLTVSegments->where('total_spent', '=', 0)->count();
        
        // Days since last activity distribution
        $daysSinceActivity = [
            '0-7' => User::withoutAdmin()->where('updated_at', '>=', Carbon::now()->subDays(7))->count(),
            '8-30' => User::withoutAdmin()->whereBetween('updated_at', [Carbon::now()->subDays(30), Carbon::now()->subDays(7)])->count(),
            '31-60' => User::withoutAdmin()->whereBetween('updated_at', [Carbon::now()->subDays(60), Carbon::now()->subDays(30)])->count(),
            '61-90' => User::withoutAdmin()->whereBetween('updated_at', [Carbon::now()->subDays(90), Carbon::now()->subDays(60)])->count(),
            '90+' => User::withoutAdmin()->where('updated_at', '<', Carbon::now()->subDays(90))->count(),
        ];
        
        return view('content.dashboards.retention-analytics', compact(
            'pageConfigs',
            'totalUsers',
            'activeUsersCount',
            'churnedUsers',
            'churnRate',
            'retentionRate',
            'newUsersInPeriod',
            'returningUsers',
            'highlyActive',
            'moderatelyActive',
            'lowActive',
            'atRiskUsers',
            'cohorts',
            'monthlyRetention',
            'highLTV',
            'mediumLTV',
            'lowLTV',
            'zeroLTV',
            'daysSinceActivity',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Conversion Funnel Analytics Dashboard
     */
    public function funnelAnalytics(Request $request)
    {
        $pageConfigs = ['has_chart' => true];
        $dateParams = $this->parseDateRange($request);
        extract($dateParams);
        
        // Default to last 30 days if no filter
        if (!$useDateFilter) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');
            $useDateFilter = true;
        }
        
        // Main Funnel: Registration → First Booking → Repeat Booking
        $totalRegistrations = User::withoutAdmin()
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->count();
        
        // Users who made at least one booking
        $usersWithBooking = User::withoutAdmin()
            ->when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->whereHas('bookings')
            ->count();
        
        // Users who made 2+ bookings (repeat customers)
        $repeatCustomers = DB::table('users')
            ->join('bookings', 'users.id', '=', 'bookings.user_id')
            ->when($useDateFilter, function($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw('DATE(users.created_at)'), [$startDate, $endDate]);
            })
            ->select('users.id')
            ->groupBy('users.id')
            ->havingRaw('COUNT(bookings.id) >= 2')
            ->count();
        
        // Users who made 5+ bookings (loyal customers)
        $loyalCustomers = DB::table('users')
            ->join('bookings', 'users.id', '=', 'bookings.user_id')
            ->when($useDateFilter, function($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw('DATE(users.created_at)'), [$startDate, $endDate]);
            })
            ->select('users.id')
            ->groupBy('users.id')
            ->havingRaw('COUNT(bookings.id) >= 5')
            ->count();
        
        // Conversion rates
        $regToFirstBooking = $totalRegistrations > 0 
            ? round(($usersWithBooking / $totalRegistrations) * 100, 2) 
            : 0;
        $firstToRepeat = $usersWithBooking > 0 
            ? round(($repeatCustomers / $usersWithBooking) * 100, 2) 
            : 0;
        $repeatToLoyal = $repeatCustomers > 0 
            ? round(($loyalCustomers / $repeatCustomers) * 100, 2) 
            : 0;
        
        // Time to first booking
        $avgTimeToFirstBooking = DB::table('users')
            ->join('bookings', 'users.id', '=', 'bookings.user_id')
            ->when($useDateFilter, function($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw('DATE(users.created_at)'), [$startDate, $endDate]);
            })
            ->selectRaw('AVG(DATEDIFF(bookings.created_at, users.created_at)) as avg_days')
            ->value('avg_days') ?? 0;
        
        // Booking completion funnel
        $totalBookingsCreated = Booking::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->count();
        $confirmedBookings = Booking::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->where('status', 'confirmed')
            ->count();
        $completedBookings = Booking::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->where('status', 'completed')
            ->count();
        $cancelledBookings = Booking::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->where('status', 'cancelled')
            ->count();
        
        // Order conversion funnel
        $totalOrdersCreated = Order::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->count();
        $paidOrders = Order::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->where('payment_status', 'paid')
            ->count();
        $shippedOrders = Order::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->where('status', 'shipped')
            ->count();
        $deliveredOrders = Order::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->where('status', 'delivered')
            ->count();
        
        // Daily conversion trend
        $dailyFunnel = DB::table('users')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as registrations')
            )
            ->where('isAdmin', '!=', 1)
            ->when($useDateFilter, function($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            })
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
        
        // Add booking counts for each day's registrations
        foreach ($dailyFunnel as $day) {
            $dayUsers = User::withoutAdmin()
                ->whereDate('created_at', $day->date)
                ->pluck('id');
            
            $day->first_bookings = Booking::whereIn('user_id', $dayUsers)->count() > 0 
                ? Booking::whereIn('user_id', $dayUsers)
                    ->select('user_id')
                    ->distinct()
                    ->count('user_id')
                : 0;
        }
        
        // Conversion by source (if you track utm_source)
        $conversionBySource = DB::table('users')
            ->leftJoin('bookings', 'users.id', '=', 'bookings.user_id')
            ->select(
                DB::raw("COALESCE(users.source, 'Direct') as source"),
                DB::raw('COUNT(DISTINCT users.id) as users'),
                DB::raw('COUNT(DISTINCT bookings.user_id) as converted')
            )
            ->when($useDateFilter, function($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw('DATE(users.created_at)'), [$startDate, $endDate]);
            })
            ->groupBy(DB::raw("COALESCE(users.source, 'Direct')"))
            ->get()
            ->map(function($item) {
                $item->conversion_rate = $item->users > 0 
                    ? round(($item->converted / $item->users) * 100, 2) 
                    : 0;
                return $item;
            });
        
        // Drop-off analysis
        $dropOffPoints = [
            [
                'stage' => 'Registration',
                'count' => $totalRegistrations,
                'dropoff' => 0,
                'dropoff_pct' => 0
            ],
            [
                'stage' => 'First Booking',
                'count' => $usersWithBooking,
                'dropoff' => $totalRegistrations - $usersWithBooking,
                'dropoff_pct' => $totalRegistrations > 0 
                    ? round((($totalRegistrations - $usersWithBooking) / $totalRegistrations) * 100, 2) 
                    : 0
            ],
            [
                'stage' => 'Repeat Booking',
                'count' => $repeatCustomers,
                'dropoff' => $usersWithBooking - $repeatCustomers,
                'dropoff_pct' => $usersWithBooking > 0 
                    ? round((($usersWithBooking - $repeatCustomers) / $usersWithBooking) * 100, 2) 
                    : 0
            ],
            [
                'stage' => 'Loyal Customer',
                'count' => $loyalCustomers,
                'dropoff' => $repeatCustomers - $loyalCustomers,
                'dropoff_pct' => $repeatCustomers > 0 
                    ? round((($repeatCustomers - $loyalCustomers) / $repeatCustomers) * 100, 2) 
                    : 0
            ]
        ];
        
        // Wallet funnel
        $usersWithWallet = WalletTransaction::when($useDateFilter, fn($q) => $this->applyDateFilter($q, $useDateFilter, $startDate, $endDate))
            ->distinct('user_id')
            ->count('user_id');
        $walletUsedForBooking = DB::table('booking_transactions')
            ->where('payment_method', 'wallet')
            ->when($useDateFilter, function($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            })
            ->distinct('booking_id')
            ->count('booking_id');
        
        return view('content.dashboards.funnel-analytics', compact(
            'pageConfigs',
            'totalRegistrations',
            'usersWithBooking',
            'repeatCustomers',
            'loyalCustomers',
            'regToFirstBooking',
            'firstToRepeat',
            'repeatToLoyal',
            'avgTimeToFirstBooking',
            'totalBookingsCreated',
            'confirmedBookings',
            'completedBookings',
            'cancelledBookings',
            'totalOrdersCreated',
            'paidOrders',
            'shippedOrders',
            'deliveredOrders',
            'dailyFunnel',
            'conversionBySource',
            'dropOffPoints',
            'usersWithWallet',
            'walletUsedForBooking',
            'startDate',
            'endDate'
        ));
    }
}
