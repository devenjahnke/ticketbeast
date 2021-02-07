<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Models\Reservation;
use App\Models\Ticket;
use Mockery;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    /** @test */
    function calculating_the_total_cost()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }

}
