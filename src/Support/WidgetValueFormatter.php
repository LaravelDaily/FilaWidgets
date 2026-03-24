<?php

namespace LaravelDaily\FilaWidgets\Support;

use Illuminate\Support\Number;

class WidgetValueFormatter
{
    public static function format(
        float $value,
        string $format = 'currency',
        string $currency = 'USD',
        int $precision = 2,
    ): string {
        return match ($format) {
            'number' => number_format($value, $precision),
            'percentage' => number_format($value, $precision).'%',
            default => Number::currency($value, in: $currency, precision: $precision),
        };
    }

    public static function formatSignedPercentage(float $value, int $precision = 1): string
    {
        $formattedValue = Number::format(abs($value), maxPrecision: $precision).'%';

        if ($value > 0) {
            return '+'.$formattedValue;
        }

        if ($value < 0) {
            return '-'.$formattedValue;
        }

        return $formattedValue;
    }
}
