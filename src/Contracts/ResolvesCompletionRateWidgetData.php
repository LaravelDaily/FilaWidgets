<?php

namespace LaravelDaily\FilaWidgets\Contracts;

use LaravelDaily\FilaWidgets\Data\CompletionRateWidgetData;

interface ResolvesCompletionRateWidgetData
{
    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, mixed>  $options
     */
    public function resolve(array $filters, array $options): CompletionRateWidgetData;
}
