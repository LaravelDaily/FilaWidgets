<?php

namespace LaravelDaily\FilaWidgets\Contracts;

use LaravelDaily\FilaWidgets\Data\SparklineTableWidgetData;

interface ResolvesSparklineTableWidgetData
{
    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, mixed>  $options
     */
    public function resolve(array $filters, array $options): SparklineTableWidgetData;
}
