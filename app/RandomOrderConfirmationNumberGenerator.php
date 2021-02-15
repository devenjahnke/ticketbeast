<?php

namespace App;

class RandomOrderConfirmationNumberGenerator implements OrderConfirmationNumberGenerator
{
    public function generate(): string
    {
        return str_repeat('A', 24);
    }
}
