<?php

namespace App\Billing;

use phpDocumentor\Reflection\Types\Callable_;

class FakePaymentGateway implements PaymentGateway
{
    private \Illuminate\Support\Collection $charges;
    private $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
    }

    public function getValidTestToken(): string
    {
        return "valid-token";
    }

    public function charge($amount, $token)
    {
        if ($this->beforeFirstChargeCallback !== null) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }

        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException;
        }

        $this->charges[] = $amount;
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }

    public function beforeFirstCharge(callable $callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }
}
