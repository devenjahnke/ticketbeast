<?php

namespace App\Billing;

interface PaymentGateway
{
    public function charge($amount, $token): Charge;
    public function getValidTestToken(): string;
    public function newChargesDuring($callback): \Illuminate\Support\Collection;
}
