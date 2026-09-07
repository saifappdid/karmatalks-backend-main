<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astro;
use App\Models\Banner;
use App\Models\Booking;
use App\Models\InstantConsultationHistory;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\WalletTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function home(Request $request)
    {
        $pageConfigs = ['has_chart' => true, 'has_icons' => true];
        // ensure these variables always exist
        $startDate = null;
        $endDate   = null;

        if ($request->filled('date_range')) {
            // robust parsing: trim, split and fallback
            $parts = array_map('trim', explode(' to ', $request->date_range));
            if (count($parts) === 2) {
                $startDate     = $parts[0];
                $endDate       = $parts[1];
                $useDateFilter = true;
            } else {
                $useDateFilter = false;
            }
        } else {
            $useDateFilter = false;
        }
        // $firebaseService = new SendNotification();
        // $firebaseService->sendNotification('f4jdMi6nRmq57a_hUjbc8J:APA91bFLuYXlC6FOC8otXLpywtcNuwtyQuWgZtQiLTX3hn7V2yL6YfvpFkQbuPaWxCaNl_mUTuJ_CR4SZX1PdcS__iXPGzQ2SjsdtkYv1Hfl0gQda5mfdWY', '$title', ' $body', $data ?? []);
        // When date filter is applied, use that range; otherwise default to current month
        $monthlyBookings = Booking::select(DB::raw('DATE_FORMAT(DATE(created_at), "%Y-%m-%d") as date '), DB::raw('SUM(final_total) as amount'))
            ->when($useDateFilter, function ($query) use ($startDate, $endDate) {
                $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            }, function ($query) {
                $query->where(DB::raw('DATE_FORMAT(created_at,"%Y-%m")'), Carbon::now()->format('Y-m'));
            })
            ->groupBy('date')
            ->get();

        $monthlyOrders = Order::select(DB::raw('DATE_FORMAT(DATE(created_at), "%Y-%m-%d") as date '), DB::raw('SUM(total) as amount'))
            ->when($useDateFilter, function ($query) use ($startDate, $endDate) {
                $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            }, function ($query) {
                $query->where(DB::raw('DATE_FORMAT(created_at,"%Y-%m")'), Carbon::now()->format('Y-m'));
            })
            ->groupBy('date')
            ->get();

        $monthlyUsers = User::select(DB::raw('DATE_FORMAT(DATE(created_at), "%Y-%m-%d") as date '), DB::raw('count(*) as count'))
            ->when($useDateFilter, function ($query) use ($startDate, $endDate) {
                $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            }, function ($query) {
                $query->where(DB::raw('DATE_FORMAT(created_at,"%Y-%m")'), Carbon::now()->format('Y-m'));
            })
            ->groupBy('date')
            ->get();

        $monthlyWallet = WalletTransaction::select(DB::raw('DATE_FORMAT(DATE(created_at), "%Y-%m-%d") as date '), DB::raw('count(*) as count'), DB::raw('SUM(amount) as amount'))
            ->where('type', 'credit')
            ->when($useDateFilter, function ($query) use ($startDate, $endDate) {
                $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            }, function ($query) {
                $query->where(DB::raw('DATE_FORMAT(created_at,"%Y-%m")'), Carbon::now()->format('Y-m'));
            })
            ->groupBy('date')
            ->get();

        $monthlyInstantConsultation = InstantConsultationHistory::select(
            'session_type',
            DB::raw('COUNT(*) as count')
        )
            ->when($useDateFilter, function ($query) use ($startDate, $endDate) {
                $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            }, function ($query) {
                $query->whereDate('created_at', Carbon::now());
            })
            ->groupBy('session_type')
            ->get();

        $todaysVoiceCall = InstantConsultationHistory::when($useDateFilter, function ($query) use ($startDate, $endDate) {
                $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            }, function ($query) {
                $query->whereDate('created_at', now());
            })->where('session_type', 'voice')->count();

        $todaysChatCall = InstantConsultationHistory::when($useDateFilter, function ($query) use ($startDate, $endDate) {
                $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            }, function ($query) {
                $query->whereDate('created_at', now());
            })->where('session_type', 'chat')->count();

        $todaysVideoCall = InstantConsultationHistory::when($useDateFilter, function ($query) use ($startDate, $endDate) {
                $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            }, function ($query) {
                $query->whereDate('created_at', now());
            })->where('session_type', 'video')->count();
        $totalUsers      = User::when($useDateFilter, function ($query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        })->count();
        $totalBanners = Banner::when($useDateFilter, function ($query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        })->count();

        $totalastros = Astro::when($useDateFilter, function ($query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        })->count();

        $totalProducts = Product::when($useDateFilter, function ($query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        })->count();

        $totalBookings = Booking::when($useDateFilter, function ($query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        })->count();

        $completedBookings = Booking::when($useDateFilter, function ($query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        })
            ->where('status', 'ended')
            ->count();

        $confirmedBookings = Booking::when($useDateFilter, function ($query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        })
            ->where('status', 'confirmed')
            ->count();

        $ongoingBookings = Booking::when($useDateFilter, function ($query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        })
            ->where('status', 'started')
            ->count();

        $allOrders = Order::when($useDateFilter, function ($query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        })->count();

        $confirmedOrders = Order::when($useDateFilter, function ($query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        })
            ->where('status', 'confirmed')
            ->count();

        $shippedOrders = Order::when($useDateFilter, function ($query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        })
            ->where('status', 'shipped')
            ->count();

        $deliveredOrders = Order::when($useDateFilter, function ($query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        })
            ->where('status', 'delivered')
            ->count();
        return view('content.dashboard', compact([
            'monthlyInstantConsultation',
            'pageConfigs',
            'monthlyWallet',
            'monthlyUsers',
            'monthlyBookings',
            'monthlyOrders',
            'totalUsers',
            'totalBanners',
            'totalastros',
            'totalBookings',
            'completedBookings',
            'confirmedBookings',
            'ongoingBookings',
            'allOrders',
            'confirmedOrders',
            'shippedOrders',
            'deliveredOrders',
            'totalProducts',
            'todaysVoiceCall',
            'todaysChatCall',
            'todaysVideoCall',
        ]));
    }

    public function downloadPdf()
    {
        $data = [
            [
                'quantity'    => 1,
                'description' => '1 Year Subscription',
                'price'       => '129.00',
            ],
        ];
        // return view('content.pdf.invoice', compact('data'));
        $pdf = Pdf::loadView('content.pdf.invoice', ['data' => $data]);
        // return $pdf->download('test.pdf');
        return $pdf->stream('test.pdf');
    }
}
