<?php

namespace App\Billing;

use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\InvalidRequestException;

class StripePaymentGateway implements PaymentGateway
{
    private String $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function charge($amount, $token)
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
}
