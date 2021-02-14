<?php

namespace App\Billing;

interface PaymentGateway
{
    public function charge($amount, $token): void;
    public function getValidTestToken(): string;
    public function newChargesDuring($callback): \Illuminate\Support\Collection;
}
