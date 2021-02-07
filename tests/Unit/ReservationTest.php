<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function calculating_the_total_cost()
    {
        $concert = Concert::factory([
            'ticket_price' => 1200,
        ])->create()->addTickets(3);
        $tickets = $concert->findTickets(3);

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }

}
