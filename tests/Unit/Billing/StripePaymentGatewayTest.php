<?php

namespace Tests\Unit\Billing;

use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Token;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{
    /** @test
     * @throws ApiErrorException
     */
    function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new StripePaymentGateway;
        $token = Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123',
            ],
        ], ['api_key' => config('services.stripe.secret')])->id;

        $paymentGateway->charge(2500, $token);

        $lastCharge = Charge::all(
            ['limit' => 1],
            ['api_key' => config('services.stripe.secret')],
        )['data'][0];

        $this->assertEquals(2500, $lastCharge->amount);
    }

}
