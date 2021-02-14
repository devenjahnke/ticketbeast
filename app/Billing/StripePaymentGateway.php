<?php

namespace App\Billing;

use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Token;

class StripePaymentGateway implements PaymentGateway
{
    private String $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function charge($amount, $token): void
    {
        try {
            Charge::create([
                'amount' => $amount,
                'source' => $token,
                'currency' => 'usd',
            ], ['api_key' => $this->apiKey]);
        } catch (ApiErrorException | InvalidRequestException $e) {
            throw new PaymentFailedException;
        }
    }

    public function getValidTestToken(): string
    {
        return Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123',
            ],
        ], ['api_key' => $this->apiKey])->id;
    }

    public function newChargesDuring($callback): \Illuminate\Support\Collection
    {
        $latestCharge = $this->lastCharge();
        $callback($this);
        return $this->newChargesSince($latestCharge)->pluck('amount');
    }

    private function lastCharge(): \Stripe\Charge | null
    {
        $charge = Charge::all(
            ['limit' => 1],
            ['api_key' => $this->apiKey],
        )['data'];

        return empty($charge[0]) ? null : $charge[0];
    }

    private function newChargesSince($charge = null): \Illuminate\Support\Collection
    {
        $newCharges = Charge::all(
            ['ending_before' => $charge ? $charge->id : null],
            ['api_key' => $this->apiKey],
        )['data'];

        return collect($newCharges);
    }
}
