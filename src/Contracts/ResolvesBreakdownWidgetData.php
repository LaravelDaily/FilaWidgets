<?php

namespace LaravelDaily\FilaWidgets\Contracts;

use LaravelDaily\FilaWidgets\Data\BreakdownWidgetData;

interface ResolvesBreakdownWidgetData
{
    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, mixed>  $options
     */
    public function resolve(array $filters, array $options): BreakdownWidgetData;
}
