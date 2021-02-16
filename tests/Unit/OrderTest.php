<?php

namespace Tests\Unit;

use App\Billing\Charge;
use App\Models\Concert;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function creating_an_order_from_tickets_email_and_charge()
    {
        $tickets = Ticket::factory()->count(3)->create();
        $charge = new Charge([
           'amount' => 3600,
            'card_last_four' => '1234',
        ]);

        $order = Order::forTickets($tickets, 'john@example.com', $charge);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals('1234', $order->card_last_four);
    }

    /** @test */
    function retrieving_an_order_by_confirmation_number()
    {
        $order = Order::factory()->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234'
        ]);

        $foundOrder = Order::findByConfirmationNumber('ORDERCONFIRMATION1234');

        $this->assertEquals($order->id, $foundOrder->id);
    }

    /** @test */
    function retrieving_a_nonexistent_order_by_confirmation_number_throws_an_exception()
    {
        try {
            Order::findByConfirmationNumber('NONEXISTENTCONFIRMATIONNUMBER');
        }
        catch (ModelNotFoundException $e) {
            return;
        }

        $this->fail('No matching order was found for the specified confirmation number, but an exception was not thrown.');
    }

    /** @test */
    function converting_to_an_array()
    {
        $order = Order::factory()->create([
            'email' => 'jane@example.com',
            'amount' => 6000,
            'confirmation_number' => 'ORDERCONFIRMATION1234',
        ]);
        $order->tickets()->saveMany([
            Ticket::factory()->create(['code' => 'TICKETCODE1']),
            Ticket::factory()->create(['code' => 'TICKETCODE2']),
            Ticket::factory()->create(['code' => 'TICKETCODE3']),
        ]);

        $result = $order->toArray();

        $this->assertEquals([
            'email' => 'jane@example.com',
            'amount' => 6000,
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'tickets' => [
                ['code' => 'TICKETCODE1'],
                ['code' => 'TICKETCODE2'],
                ['code' => 'TICKETCODE3'],
            ],
        ], $result);
    }
}
