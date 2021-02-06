<?php

namespace App\Billing;

class FakePaymentGateway implements PaymentGateway
{
    private \Illuminate\Support\Collection $charges;

    public function __construct()
    {
        $this->charges = collect();
    }

    public function getValidTestToken()
    {
        return "valid-token";
    }

    public function charge($amount, $token)
    {
        $this->charges[] = $amount;
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }
}
