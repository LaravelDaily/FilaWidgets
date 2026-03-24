<?php

namespace LaravelDaily\FilaWidgets\Support;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class SparklineSeries
{
    /**
     * Build a zero-filled daily float array from a query and date range.
     *
     * @param  string  $aggregate  Raw SQL aggregate expression (e.g., 'SUM(amount)', 'COUNT(*)', 'AVG(amount)')
     * @param  string  $dateColumn  The column to group by date
     * @return array<int, float>
     */
    public static function daily(
        CarbonInterface $start,
        CarbonInterface $end,
        Builder $query,
        string $aggregate,
        string $dateColumn = 'created_at',
        int $precision = 2,
    ): array {
        $results = (clone $query)
            ->whereBetween($dateColumn, [$start, $end])
            ->selectRaw("DATE({$dateColumn}) as date, {$aggregate} as value")
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('value', 'date');

        return collect($start->daysUntil($end))
            ->map(fn ($day): float => round((float) ($results[$day->format('Y-m-d')] ?? 0), $precision))
            ->values()
            ->all();
    }
}
