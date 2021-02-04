<?php

namespace Tests\Unit;

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
        // Arrange
        // Create a concert with a known date
        $concert = Concert::factory()->make([
            'date' => Carbon::parse('2021-12-01 8:00pm'),
        ]);

        // Act
        // Retrieve the formatted date
        $date = $concert->formatted_date;

        // Assert
        // Verify the date is formatted as expected
        $this->assertEquals('December 1, 2021', $date);
    }

}
