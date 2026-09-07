<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Order;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;

class InvoiceController extends Controller
{
    use ResponseTrait;

    public function bookingInvoice(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:bookings,id',
        ]);
        $booking = Booking::with(['transaction', 'astro', 'user'])
            ->where(['id' => $request->id, 'user_id' => auth('sanctum')->id()])
            ->first();
        if (!$booking) {
            return $this->error('No booking found', 404);
        }
        $pdf = Pdf::loadView('pdf.booking-invoice', ['booking' => $booking]);
        $fileName = 'Invoice-' . ($booking->booking_uid ?? $booking->id) . '.pdf';
        return $pdf->download($fileName);
    }

    public function orderInvoice(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:orders,id',
        ]);
        $order = Order::with(['orderDetails', 'transaction', 'user'])
            ->where(['id' => $request->id, 'user_id' => auth('sanctum')->id()])
            ->first();
        if (!$order) {
            return $this->error('No order found', 404);
        }
        $pdf = Pdf::loadView('pdf.order-invoice', ['order' => $order]);
        $fileName = 'Invoice-' . ($order->order_uid ?? $order->id) . '.pdf';
        return $pdf->download($fileName);
    }

    public function walletTransactionInvoice(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:wallet_transactions,id',
        ]);
        $walletTransaction = WalletTransaction::with(['user', 'package'])
            ->where(['id' => $request->id, 'user_id' => auth('sanctum')->id()])
            ->first();
        if (!$walletTransaction) {
            return $this->error('No wallet transaction found', 404);
        }
        $pdf = Pdf::loadView('pdf.wallet-invoice', ['transaction' => $walletTransaction]);
        $fileName = 'Invoice-WLT-' . str_pad($walletTransaction->id, 5, '0', STR_PAD_LEFT) . '.pdf';
        return $pdf->download($fileName);
    }
}
