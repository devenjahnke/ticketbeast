<?php

namespace Database\Factories;

use App\Models\Concert;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'concert_id' => Concert::factory(),
        ];
    }

    /**
     * Mark the ticket as reserved.
     *
     * @return Factory
     */
    public function reserved()
    {
        return $this->state([
            'reserved_at' => Carbon::now(),
        ]);
    }
}
