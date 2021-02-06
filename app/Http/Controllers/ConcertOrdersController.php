<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Models\Concert;

class ConcertOrdersController extends Controller
{
    private PaymentGateway $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        // Find this concert
        $concert = Concert::find($concertId);

        // Get ticket quantity from HTTP request
        $ticketQuantity = request('ticket_quantity');

        // Calculate amount to charge
        $amount = $ticketQuantity * $concert->ticket_price;

        // Get the payment token from HTTP request
        $token = request('payment_token');

        // Process charge through payment gateway
        $this->paymentGateway->charge($amount, $token);

        // Create a new order for this concert
        $order = $concert->orders()->create([
            'email' => request('email'),
        ]);

        foreach (range(1, $ticketQuantity) as $i) {
            $order->tickets()->create([]);
        }

        return response()->json([], 201);
    }
}
