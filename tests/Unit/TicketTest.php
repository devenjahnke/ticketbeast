<?php

namespace Tests\Unit;

use App\Facades\TicketCode;
use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_ticket_can_be_reserved()
    {
        $ticket = Ticket::factory()->create();
        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();

        $this->assertNotNull($ticket->fresh()->reserved_at);
    }

    /** @test */
    function a_ticket_can_be_released()
    {
        $ticket = Ticket::factory()->reserved()->create();
        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_at);
    }

    /** @test */
    function a_ticket_can_be_claimed_for_an_order()
    {
        $order = Order::factory()->create();
        $ticket = Ticket::factory()->create(['code' => null]);
        TicketCode::shouldReceive('generate')->andReturn('TICKETCODE1');

        $ticket->claimFor($order);

        $this->assertContains($ticket->id, $order->tickets->pluck('id'));
        $this->assertEquals('TICKETCODE1', $ticket->code);
    }


}
