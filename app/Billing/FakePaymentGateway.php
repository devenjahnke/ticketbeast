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

    public function charge($amount, $token): void
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

    public function getValidTestToken(): string
    {
        return "valid-token";
    }

    public function newChargesDuring($callback): \Illuminate\Support\Collection
    {
        $chargesFrom = $this->charges->count();
        $callback($this);
        return $this->charges->slice($chargesFrom)->reverse()->values();
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
