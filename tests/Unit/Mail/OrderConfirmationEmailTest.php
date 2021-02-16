<?php

namespace Tests\Unit\Mail;

use App\Mail\OrderConfirmationEmail;
use App\Models\Order;
use Tests\TestCase;

class OrderConfirmationEmailTest extends TestCase
{
    /** @test */
    function email_contains_a_link_to_the_order_confirmation_page()
    {
        $order = Order::factory()->make([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
        ]);

        $email = new OrderConfirmationEmail($order);
        $rendered = $email->render();

        $this->assertStringContainsString(url('/orders/ORDERCONFIRMATION1234'), $rendered);
    }

    /** @test */
    function email_has_a_subject()
    {
        $order = Order::factory()->make();

        $email = new OrderConfirmationEmail($order);

        $this->assertEquals('Your TicketBeast Order', $email->build()->subject);
    }
}
