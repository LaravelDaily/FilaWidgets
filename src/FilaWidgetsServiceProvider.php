<?php

namespace LaravelDaily\FilaWidgets;

use Illuminate\Support\ServiceProvider;

class FilaWidgetsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filawidgets');
    }
}
