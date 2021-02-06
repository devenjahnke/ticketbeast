<?php

namespace App\Models;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $dates = ['date'];

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orderTickets($email, $ticketQuantity)
    {
        // Get tickets associated with the concert
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();

        if ($tickets->count() < $ticketQuantity) {
            throw new NotEnoughTicketsException;
        }

        // Create a new order for this concert
        $order = $this->orders()->create([
            'email' => $email,
        ]);

        // Associate each of the tickets with this order
        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public function addTickets($quantity)
    {
        // Create the tickets associated with this order
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }
    }

    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }
}
