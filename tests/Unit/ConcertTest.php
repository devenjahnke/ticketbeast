<?php

namespace Tests\Unit;

use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function can_get_formatted_date()
    {
        $concert = Concert::factory()->make([
            'date' => Carbon::parse('2021-12-01 8:00pm'),
        ]);

        $this->assertEquals('December 1, 2021', $concert->formatted_date);
    }

    /** @test */
    function can_get_formatted_start_time()
    {
        $concert = Concert::factory()->make([
            'date' => Carbon::parse('2021-12-01 17:00:00'),
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /** @test */
    function can_get_ticket_price_in_dollars()
    {
        $concert = Concert::factory()->make([
            'ticket_price' => 6750,
        ]);

        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    /** @test */
    function concerts_with_a_published_at_date_are_published()
    {
        $publishedConcertA = Concert::factory()->create([
            'published_at' => Carbon::parse('-1 week'),
        ]);
        $publishedConcertB = Concert::factory()->create([
            'published_at' => Carbon::parse('-1 week'),
        ]);
        $unpublishedConcert = Concert::factory()->create([
            'published_at' => null,
        ]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    /** @test */
    function can_order_concert_tickets()
    {
        $concert = Concert::factory()->create()->addTickets(3);

        $order = $concert->orderTickets('jane@example.com', 3);

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
    }

    /** @test */
    function can_add_tickets()
    {
        $concert = Concert::factory()->create();

        $concert->addTickets(50);

        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    function tickets_remaining_does_not_include_tickets_associated_with_an_order()
    {
        $concert = Concert::factory()->create()->addTickets(50);
        $concert->orderTickets('jane@example.com', 30);

        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    /** @test */
    function trying_to_purchase_more_tickets_than_remain_throws_an_exception()
    {
        $concert = Concert::factory()->create()->addTickets(10);

        try {
            $concert->orderTickets('jane@example.com', 11);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('jane@example.com'));
            $this->assertEquals(10, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Order succeeded even though there were not enough tickets remaining.');
    }

    /** @test */
    function cannot_order_tickets_that_have_already_been_purchased()
    {
        $concert = Concert::factory()->create()->addTickets(10);
        $concert->orderTickets('jane@example.com', 8);

        try {
            $concert->orderTickets('john@example.com', 3);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('john@example.com'));
            $this->assertEquals(2, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Order succeeded even though there were not enough tickets remaining.');
    }

    /** @test */
    function can_reserve_available_tickets()
    {
        $concert = Concert::factory()->create()->addTickets(3);
        $this->assertEquals(3, $concert->ticketsRemaining());

        $reservedTickets = $concert->reserveTickets(2);

        $this->assertCount(2, $reservedTickets);
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /** @test */
    function cannot_reserve_tickets_that_have_already_been_purchased()
    {
        $concert = Concert::factory()->create()->addTickets(3);
        $concert->orderTickets('jane@example.com', 2);

        try {
            $concert->reserveTickets(2);
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Reserving tickets succeeded even though the tickets were already sold.");
    }

    /** @test */
    function cannot_reserve_tickets_that_have_already_been_reserved()
    {
        $concert = Concert::factory()->create()->addTickets(3);
        $concert->reserveTickets(2);

        try {
            $concert->reserveTickets(2);
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Reserving tickets succeeded even though the tickets were already reserved.");
    }


}
