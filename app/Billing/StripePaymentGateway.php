<?php

namespace App\Billing;

use Stripe\Exception\ApiErrorException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Token;

class StripePaymentGateway implements PaymentGateway
{
    const TEST_CARD_NUMBER = '4242424242424242';
    private String $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function charge($amount, $token): Charge
    {
        try {
            $stripeCharge = \Stripe\Charge::create([
                'amount' => $amount,
                'source' => $token,
                'currency' => 'usd',
            ], ['api_key' => $this->apiKey]);

            return new Charge([
                'card_last_four' => $stripeCharge['source']['last4'],
                'amount' => $stripeCharge['amount'],
            ]);
        } catch (ApiErrorException | InvalidRequestException $e) {
            throw new PaymentFailedException;
        }
    }

    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER): string
    {
        return Token::create([
            'card' => [
                'number' => $cardNumber,
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
        return $this->newChargesSince($latestCharge)->map(function ($stripeCharge) {
            return new Charge([
                'card_last_four' => $stripeCharge['source']['last4'],
                'amount' => $stripeCharge['amount'],
            ]);
        });
    }

    private function lastCharge(): \Stripe\Charge | null
    {
        $charge = \Stripe\Charge::all(
            ['limit' => 1],
            ['api_key' => $this->apiKey],
        )['data'];

        return empty($charge[0]) ? null : $charge[0];
    }

    private function newChargesSince($charge = null): \Illuminate\Support\Collection
    {
        $newCharges = \Stripe\Charge::all(
            ['ending_before' => $charge ? $charge->id : null],
            ['api_key' => $this->apiKey],
        )['data'];

        return collect($newCharges);
    }
}
