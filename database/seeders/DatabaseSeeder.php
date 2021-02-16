<?php

namespace Database\Seeders;

use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
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
        $concert = Concert::factory()->published()->create();
        $order = Order::factory()->create();
        Ticket::factory()->count(10)->create([
            'concert_id' => $concert->id,
        ]);
        Ticket::factory()->reserved()->count(3)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
        ]);

    }
}
