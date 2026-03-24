<?php

namespace LaravelDaily\FilaWidgets\Tests;

use LaravelDaily\FilaWidgets\FilaWidgetsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            FilaWidgetsServiceProvider::class,
        ];
    }
}
