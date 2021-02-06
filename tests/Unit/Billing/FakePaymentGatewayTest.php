<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use PHPUnit\Framework\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    function charges_with_a_valid_payment_token_are_successful()
    {
        // Create payment gateway
        $paymentGateway = new FakePaymentGateway;

        // Execute charge using payment gateway
        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        // Assert charge was successfully executed
        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

    /** @test */
    function charges_with_an_invalid_payment_token_fail()
    {
        try {
            $paymentGateway = new FakePaymentGateway;
            $paymentGateway->charge(2500, 'invalid-payment-token');
        } catch (PaymentFailedException $e) {
            return;
        }

        // Fail the test if an exception is thrown
        $this->fail();
    }


}
