<?php

namespace LaravelDaily\FilaWidgets\Support;

class WidgetMetricCalculator
{
    /**
     * @return array{difference: float, percentageChange: ?float, trend: string}
     */
    public static function comparison(float $currentValue, float $comparisonValue): array
    {
        $difference = $currentValue - $comparisonValue;

        return [
            'difference' => $difference,
            'percentageChange' => static::percentageChange($currentValue, $comparisonValue),
            'trend' => match (true) {
                $difference > 0 => 'up',
                $difference < 0 => 'down',
                default => 'neutral',
            },
        ];
    }

    /**
     * @param  array<int, array{label: string, value: int|float, previousValue?: int|float|null, color?: ?string, icon?: ?string, url?: ?string}>  $items
     * @return array<int, array{label: string, value: float, contributionPercentage: float, deltaPercentage: ?float, color: ?string, icon: ?string, url: ?string}>
     */
    public static function breakdown(
        array $items,
        ?int $limit = null,
        bool $groupOther = false,
        string $sortBy = 'value',
        string $sortDirection = 'desc',
    ): array {
        $normalized = array_map(fn (array $item): array => [
            'label' => $item['label'],
            'value' => (float) $item['value'],
            'previousValue' => array_key_exists('previousValue', $item) ? (float) $item['previousValue'] : null,
            'color' => $item['color'] ?? null,
            'icon' => $item['icon'] ?? null,
            'url' => $item['url'] ?? null,
        ], $items);

        usort($normalized, function (array $first, array $second) use ($sortBy, $sortDirection): int {
            $a = $first[$sortBy] ?? $first['value'];
            $b = $second[$sortBy] ?? $second['value'];

            return $sortDirection === 'asc'
                ? ($a <=> $b)
                : ($b <=> $a);
        });

        if (($limit !== null) && ($limit > 0) && (count($normalized) > $limit)) {
            if ($groupOther && ($limit > 1)) {
                $visibleItems = array_slice($normalized, 0, $limit - 1);
                $remainingItems = array_slice($normalized, $limit - 1);

                $visibleItems[] = [
                    'label' => 'Other',
                    'value' => array_sum(array_column($remainingItems, 'value')),
                    'previousValue' => static::sumOptionalValues(array_column($remainingItems, 'previousValue')),
                    'color' => null,
                    'icon' => null,
                    'url' => null,
                ];

                $normalized = $visibleItems;
            } else {
                $normalized = array_slice($normalized, 0, $limit);
            }
        }

        $total = array_sum(array_column($normalized, 'value'));

        return array_map(fn (array $item): array => [
            'color' => $item['color'],
            'contributionPercentage' => $total > 0
                ? round(($item['value'] / $total) * 100, 2)
                : 0.0,
            'deltaPercentage' => ($item['previousValue'] === null)
                ? null
                : static::percentageChange($item['value'], $item['previousValue']),
            'icon' => $item['icon'],
            'label' => $item['label'],
            'url' => $item['url'],
            'value' => $item['value'],
        ], $normalized);
    }

    /**
     * @return array{barPercentage: float, percentage: float, status: string}
     */
    public static function progress(float $currentValue, float $goalValue): array
    {
        if ($goalValue <= 0) {
            return [
                'barPercentage' => 0.0,
                'percentage' => 0.0,
                'status' => 'warning',
            ];
        }

        $percentage = round(($currentValue / $goalValue) * 100, 2);

        return [
            'barPercentage' => min(max($percentage, 0.0), 100.0),
            'percentage' => $percentage,
            'status' => match (true) {
                $percentage > 100 => 'success',
                $percentage >= 50 => 'normal',
                default => 'warning',
            },
        ];
    }

    public static function percentageChange(float $currentValue, float $comparisonValue): ?float
    {
        if ($comparisonValue == 0.0) {
            return $currentValue == 0.0 ? 0.0 : null;
        }

        return round((($currentValue - $comparisonValue) / abs($comparisonValue)) * 100, 2);
    }

    /**
     * @param  array<int, float|null>  $values
     */
    protected static function sumOptionalValues(array $values): ?float
    {
        $filteredValues = array_values(array_filter($values, fn (?float $value): bool => $value !== null));

        if ($filteredValues === []) {
            return null;
        }

        return array_sum($filteredValues);
    }
}
