<?php

namespace Database\Factories;

use App\Models\Concert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConcertFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Concert::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'title' => 'Example Band',
            'subtitle' => 'with The Fake Openers',
            'date' => Carbon::parse('+2 weeks'),
            'ticket_price' => 2000,  // Stored as cents
            'venue' => 'The Example Theater',
            'venue_address' => '123 Example Lane',
            'city' => 'Fakeville',
            'state' => 'ON',
            'zip' => '90210',
            'additional_information' => 'Some sample additional information.',
        ];
    }

    /**
     * Mark the concert as published.
     *
     * @return Factory
     */
    public function published()
    {
        return $this->state([
            'published_at' => Carbon::parse('-1 week'),
        ]);
    }

    /**
     * Mark the concert as unpublished.
     *
     * @return Factory
     */
    public function unpublished()
    {
        return $this->state([
            'published_at' => null,
        ]);
    }
}
