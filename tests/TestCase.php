<?php

namespace Rahmanramsi\LivewirePageGroup\Tests;

use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Rahmanramsi\LivewirePageGroup\LivewirePageGroupServiceProvider;
use Rahmanramsi\LivewirePageGroup\Tests\Fixtures\TestPageGroupServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        TestPageGroupServiceProvider::$bootCount = 0;
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            LivewirePageGroupServiceProvider::class,
            TestPageGroupServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        config()->set('database.default', 'testing');
    }
}
