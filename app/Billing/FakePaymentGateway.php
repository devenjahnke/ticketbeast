<?php

namespace App\Billing;

use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Callable_;

class FakePaymentGateway implements PaymentGateway
{
    const TEST_CARD_NUMBER = '4242424242424242';
    private \Illuminate\Support\Collection $charges;
    private \Illuminate\Support\Collection $tokens;
    private $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
        $this->tokens = collect();
    }

    public function charge($amount, $token): Charge
    {
        if ($this->beforeFirstChargeCallback !== null) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }

        if (! $this->tokens->has($token)) {
            throw new PaymentFailedException;
        }

        return $this->charges[] = new Charge([
            'amount' => $amount,
            'card_last_four' => substr($this->tokens[$token], -4),
        ]);
    }

    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER): string
    {
        $token = 'fake-tok_' . Str::random(24);
        $this->tokens[$token] = $cardNumber;
        return $token;
    }

    public function newChargesDuring($callback): \Illuminate\Support\Collection
    {
        $chargesFrom = $this->charges->count();
        $callback($this);
        return $this->charges->slice($chargesFrom)->reverse()->values();
    }

    public function totalCharges()
    {
        return $this->charges->map->amount()->sum();
    }

    public function beforeFirstCharge(callable $callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }

}
