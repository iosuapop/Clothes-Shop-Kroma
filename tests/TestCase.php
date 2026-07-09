<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Configurează sesiunea pentru teste
        $this->app['session']->setDefaultDriver('array');
        $this->app['session']->start();
    }
}
