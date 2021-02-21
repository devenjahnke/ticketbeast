<?php

namespace Database\Seeders;

use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create([
            'email' => 'adam@example.com',
            'password' => bcrypt('secret'),
        ]);

        $concert = Concert::factory()->published()->create();

        $order = Order::factory()->create();

        Ticket::factory()->count(10)->create([
            'concert_id' => $concert->id,
        ]);
    }
}
