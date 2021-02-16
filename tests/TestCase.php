<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        // Prevent Mockery from allowing methods not specified on the interface to be mocked.
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
    }
}
