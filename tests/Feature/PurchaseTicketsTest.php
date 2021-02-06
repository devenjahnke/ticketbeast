<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Models\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    private FakePaymentGateway $paymentGateway;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    /** @test */
    function customer_can_purchase_concert_tickets()
    {
        $concert = Concert::factory()->create([
            'ticket_price' => 3250,
        ]);

        $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        // Assert HTTP request was successful
        $response->assertStatus(201);
        // Assert the customer was charged the correct amount
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());
        // Get the order for the customer
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        // Assert the order exists for this customer
        $this->assertNotNull($order);
        // Assert the order quantity is correct
        $this->assertEquals(3, $order->tickets()->count());
    }

    /** @test */
    function email_is_required_to_purchase_tickets()
    {
        $concert = Concert::factory()->create();

        $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        // Assert validation error is handled
        $response->assertStatus(422);
        // Assert email is the cause of validation error
        $this->assertArrayHasKey('email', $response->decodeResponseJson()['errors']);
    }


}
