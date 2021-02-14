<?php

namespace Tests\Unit\Billing;

use App\Billing\StripePaymentGateway;
use Stripe\Charge;
use Stripe\Token;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{
    private $lastCharge;

    private function lastCharge()
    {
        $charge = Charge::all(
            ['limit' => 1],
            ['api_key' => config('services.stripe.secret')],
        )['data'];
        
        return empty($charge[0]) ? null : $charge[0];
    }

    private function validToken(): string
    {
        return Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123',
            ],
        ], ['api_key' => config('services.stripe.secret')])->id;
    }

    private function newCharges()
    {
        return Charge::all(
            ['ending_before' => $this->lastCharge ? $this->lastCharge->id : null],
            ['api_key' => config('services.stripe.secret')],
        )['data'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->lastCharge = $this->lastCharge();
    }

    /** @test */
    function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));

        $paymentGateway->charge(2500, $this->validToken());

        $this->assertCount(1, $this->newCharges());
        $this->assertEquals(2500, $this->lastCharge()->amount);
    }

}
