<?php

namespace App\Models;

interface OrderConfirmationNumberGenerator
{
    public function generate(): String;
}
