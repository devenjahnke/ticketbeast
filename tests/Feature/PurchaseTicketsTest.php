<?php

namespace Tests\Feature;

use App\Models\Concert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    /** @test */
    function customer_can_purchase_concert_tickets()
    {
        $concert = Concert::factory()->create([
            'ticket_price' => 3250,
        ]);

        $this->json('POST', "/concerts/{ $concert->id }/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $paymentGateway->getValidTestToken(),
        ]);

        // Assert the customer was charged the correct amount
        $this->assertEquals(9750, $paymentGateway->totalCharges());

        // Get the order for the customer
        $order = $concert->orders()->where('email', 'john@example.com')->first();

        // Assert the order exists for this customer
        $this->assertNotNull($order);

        // Assert the order quantity is correct
        $this->assertEquals(3, $order->tickets->count());
    }

}
