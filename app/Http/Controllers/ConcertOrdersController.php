<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use App\Models\Order;
use App\Models\Reservation;

class ConcertOrdersController extends Controller
{
    private PaymentGateway $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $concert = Concert::published()->findOrFail($concertId);

        $this->validate(request(), [
            'email' => ['required', 'email'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
            'payment_token' => ['required'],
        ]);

        try {
            $tickets = $concert->reserveTickets(request('ticket_quantity'));
            $reservation = new Reservation($tickets);
            $this->paymentGateway->charge($reservation->totalCost(), request('payment_token'));
            $order = Order::forTickets($tickets, request('email'), $reservation->totalCost());

            return response()->json($order, 201);
        } catch (PaymentFailedException | NotEnoughTicketsException $e) {
            return response()->json([], 422);
        }
    }
}
