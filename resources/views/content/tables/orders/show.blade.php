@extends('layouts/contentLayoutMaster')

@section('title', 'Order #' . $order->order_uid)

@section('page-style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-status {
            font-size: 14px;
            padding: 4px 12px;
            border-radius: 8px;
            font-weight: 600;
        }

        .status-paid {
            background: #dcfce7;
            color: #166534;
        }

        .status-unpaid {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-unfulfilled {
            background: #fef9c3;
            color: #854d0e;
        }

        .timeline {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }

        .timeline-step {
            text-align: center;
            flex: 1;
            position: relative;
        }

        .timeline-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 12px;
            right: -50%;
            width: 100%;
            height: 3px;
            background: #e5e7eb;
            z-index: -1;
        }

        .timeline-step.active span {
            color: #2563eb;
            font-weight: 600;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .product-item img {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            margin-right: 12px;
        }

        .summary-box p {
            margin: 6px 0;
        }

        .summary-box strong {
            font-size: 15px;
        }

        .side-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            background: #fff;
        }

        .side-box h6 {
            font-weight: 600;
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')
    @php
        $address = json_decode($order->address);
    @endphp

    <section>
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8 col-md-7">
                <x-card>
                    <div class="order-header mb-1 d-flex justify-content-between">
                        <h4>Order #{{ $order->order_uid }}</h4>
                        <span class="order-status status-{{ strtolower($order->status) }}" style="color: orange !important;">
                            {{ ucfirst($order->status) }}
                            @if ($order->status != 'cancelled' && $order->status != 'delivered')
                                <a href="#" class="text-muted" style="color: orange !important;"
                                    data-target="#updateStatus" data-toggle="modal" title="Edit Order">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            @endif
                        </span>
                    </div>
                    <p><small>Placed on <span
                                class="badge badge-success">{{ $order->created_at->format('M d, Y h:i A') }}</span></small>
                    </p>

                    <!-- Timeline -->
                    <div class="timeline">
                        <div class="timeline-step {{ $order->confirmed_at ? 'active' : '' }}">
                            <span>Confirmed</span>
                            <br>
                            {{ isset($order->confirmed_at) ? $order->confirmed_at->format('M d, Y h:i A') : '' }}
                        </div>
                        <div class="timeline-step {{ $order->shipped_at ? 'active' : '' }}">
                            <span>Shipped</span>
                            <br>
                            {{ isset($order->shipped_at) ? $order->shipped_at->format('M d, Y h:i A') : '' }}
                        </div>
                        <div class="timeline-step {{ $order->delivered_at ? 'active' : '' }}">
                            <span>Delivered</span>
                            <br>
                            {{ isset($order->delivered_at) ? $order->delivered_at->format('M d, Y h:i A') : '' }}
                        </div>
                    </div>

                    <!-- Products -->
                    @foreach ($order->orderDetails as $item)
                        @php $product = json_decode($item->product); @endphp
                        <div class="product-item">
                            @if (!empty($product->image))
                                <img src="{{ asset($product->image) }}" class="view-on-click" alt="{{ $product->title }}">
                            @endif
                            <div>
                                <strong>{{ $product->title ?? 'Product' }}</strong><br>
                                <small>SKU: {{ $product->sku ?? 'N/A' }}</small><br>
                                <small>Qty: {{ $item->quantity }}</small>
                            </div>
                            <div class="ms-auto text-end">
                                <p class="mb-0">₹{{ number_format($item->total, 2) }}</p>
                            </div>
                        </div>
                    @endforeach

                    <!-- Order Summary -->
                    <div class="summary-box mt-3 p-3 rounded border ">
                        <h6 class="mb-3 fw-bold">Order Summary</h6>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>₹{{ number_format($order->sub_total, 2) }}</span>
                        </div>

                        @if ($order->promocode)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Promo ({{ $order->promocode }})</span>
                                <span>-₹{{ number_format($order->promocode_discount, 2) }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <span>
                                @if ($order->shipping_amount == 0)
                                    Free
                                @else
                                    ₹{{ number_format($order->shipping_amount, 2) }}
                                @endif
                            </span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2 fw-bold fs-6">
                            <span>Total</span>
                            <span class="badge badge-primary">₹{{ number_format($order->total, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between text-muted small">
                            <span>Payment Method</span>
                            <span>{{ ucfirst($order->payment_mode) }}</span>
                        </div>
                    </div>

                </x-card>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4 col-md-5">
                <div class="side-box card">
                    <h6>Customer Info</h6>
                    <p><strong>{{ $order->user->name }}</strong></p>
                    <p>Email: {{ $order->email ?? $order->user->email }}</p>
                    <p>Mobile: {{ $order->mobile ?? $order->user->mobile }}</p>
                </div>

                <div class="side-box card">
                    <h6>Shipping Address</h6>
                    @if ($address)
                        <p>
                            {{ $address->address_one ?? '' }}<br>
                            {{ $address->address_two ?? '' }}<br>
                            {{ $address->city ?? '' }}, {{ $address->state ?? '' }} - {{ $address->pincode ?? '' }}<br>
                            India
                        </p>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($address->city . ', ' . $address->state . ' ' . $address->pincode) }}"
                            target="_blank" class="text-primary">View on Map</a>
                    @else
                        <p class="text-muted">No shipping address available</p>
                    @endif
                </div>

                <div class="side-box card">
                    <h6>Status Timeline</h6>
                    <ul class="list-unstyled">
                        @if ($order->confirmed_at)
                            <li>✅ Confirmed - {{ $order->confirmed_at->format('M d, Y h:i A') }}</li>
                        @endif
                        @if ($order->shipped_at)
                            <li>📦 Shipped - {{ $order->shipped_at->format('M d, Y h:i A') }}</li>
                        @endif
                        @if ($order->delivered_at)
                            <li>🚚 Delivered - {{ $order->delivered_at->format('M d, Y h:i A') }}</li>
                        @endif
                        @if ($order->cancelled_at)
                            <li>❌ Cancelled - {{ $order->cancelled_at->format('M d, Y h:i A') }}
                                <br><small>{{ $order->cancel_reason }}</small>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <x-modal id="updateStatus" :footer="false">
        <x-form id="statusUpdate" :route="route('admin.orders.updateStatus')">
            <div class="col-lg-12 col-12">
                <x-input name="id" id="id" value="{{ $order->id }}" type="hidden" />
                {{-- <x-select name="status" id="status" :options="['confirmed', 'shipped', 'delivered', 'cancelled']" :select="$order->status" /> --}}
                <label for="">Status</label>
                <select name="status" id="status" class="form-control select2">
                    <option value="">Select Status</option>
                    @if ($order->status == 'confirmed')
                        {{-- <option value="confirmed">Confirmed</option> --}}
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    @elseif ($order->status == 'shipped')
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    @elseif ($order->status == 'delivered')
                        <option value="cancelled">Cancelled</option>
                    @endif
                </select>
            </div>
        </x-form>
    </x-modal>
@endsection
