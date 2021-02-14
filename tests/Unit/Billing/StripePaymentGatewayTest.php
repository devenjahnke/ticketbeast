<?php

namespace Tests\Unit\Billing;

use App\Billing\PaymentFailedException;
use App\Billing\StripePaymentGateway;
use Tests\TestCase;

/**
 * @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

    /** @test */
    function charges_with_an_invalid_payment_token_fail()
    {
        try {
            $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));
            $paymentGateway->charge(2500, 'invalid-payment-token');
        } catch (PaymentFailedException $e) {
            $this->assertCount(0, $this->newCharges());
            return;
        }

        $this->fail('Charging with an invalid payment token did not throw a PaymentFailedException.');
    }

    protected function getPaymentGateway(): StripePaymentGateway
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }
}
