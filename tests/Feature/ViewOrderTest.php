<?php

namespace Tests\Feature;

use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ViewOrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function user_can_view_their_order_confirmation()
    {
        $concert = Concert::factory()->create();
        $order = Order::factory()->create();
        $ticket = Ticket::factory()->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
        ]);

        $response = $this->get("/orders/{$order->id}");
    }
}
