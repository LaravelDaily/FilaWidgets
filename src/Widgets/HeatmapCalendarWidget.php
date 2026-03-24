<?php

namespace LaravelDaily\FilaWidgets\Widgets;

use Carbon\CarbonImmutable;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use LaravelDaily\FilaWidgets\Contracts\ResolvesHeatmapCalendarWidgetData;
use LaravelDaily\FilaWidgets\Data\HeatmapCalendarWidgetData;
use LaravelDaily\FilaWidgets\Definitions\HeatmapCalendarWidgetDefinition;
use LaravelDaily\FilaWidgets\Support\DateRangeFilter;
use LaravelDaily\FilaWidgets\Support\WidgetDataCache;
use LaravelDaily\FilaWidgets\Support\WidgetValueFormatter;
use LaravelDaily\FilaWidgets\Widgets\Concerns\InteractsWithWidgetConfiguration;

class HeatmapCalendarWidget extends Widget
{
    use InteractsWithPageFilters;
    use InteractsWithWidgetConfiguration;

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    protected string $view = 'filawidgets::widgets.heatmap-calendar-widget';

    public function getColumnSpan(): int|string|array
    {
        $definition = $this->getDefinition();

        return $definition->columnSpanValue() ?? $this->columnSpan;
    }

    protected function getViewData(): array
    {
        $definition = $this->getDefinition();
        $data = $this->resolveData();

        [$startDate, $days] = $this->resolveDisplayWindow($definition);
        $entries = $data->entries;
        $total = array_sum($entries);

        $maxValue = $entries !== [] ? max($entries) : 0;

        $cells = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->addDays($i);
            $dateKey = $date->format('Y-m-d');
            $value = $entries[$dateKey] ?? 0.0;

            $cells[] = [
                'actionNewTab' => $data->openEntryUrlsInNewTab,
                'actionUrl' => $data->entryUrls[$dateKey] ?? null,
                'date' => $dateKey,
                'dayLabel' => $date->format('M j'),
                'intensity' => $maxValue > 0 ? min((int) ceil(($value / $maxValue) * 4), 4) : 0,
                'formattedValue' => WidgetValueFormatter::format(
                    $value,
                    $definition->formatName(),
                    $definition->currencyCode(),
                    $definition->precisionValue(),
                ),
            ];
        }

        $grid = $this->buildGrid($cells, $startDate);

        $monthLabels = $this->buildMonthLabels($startDate, $days);

        return [
            'actionLabel' => $definition->actionLabel(),
            'actionNewTab' => $definition->actionOpensInNewTab(),
            'actionUrl' => $definition->actionUrl(),
            'color' => $definition->colorName(),
            'colorScheme' => $definition->colorSchemeName(),
            'description' => $data->description,
            'emptyStateDescription' => $definition->emptyStateDescription() ?? 'Daily activity will appear here once the selected period has data.',
            'emptyStateHeading' => $definition->emptyStateHeading() ?? 'No daily activity yet.',
            'grid' => $grid,
            'hasEntries' => $total > 0,
            'helpText' => $definition->helpTextValue(),
            'icon' => $definition->iconName(),
            'label' => $definition->labelText(),
            'monthLabels' => $monthLabels,
            'total' => WidgetValueFormatter::format(
                $total,
                $definition->formatName(),
                $definition->currencyCode(),
                $definition->precisionValue(),
            ),
        ];
    }

    /**
     * @return array{0: CarbonImmutable, 1: int}
     */
    protected function resolveDisplayWindow(HeatmapCalendarWidgetDefinition $definition): array
    {
        if ($this->getRangeFilter() !== null) {
            $dateRange = DateRangeFilter::fromFilter($this->getRangeFilter());
            [$startDate] = $dateRange->currentPeriod();

            return [$startDate, $dateRange->days()];
        }

        $days = $definition->weeksToShow() * 7;

        return [now()->subDays($days - 1)->startOfDay()->toImmutable(), $days];
    }

    /**
     * @param  array<int, array<string, mixed>>  $cells
     * @return array<int, array<int, array<string, mixed>>>
     */
    protected function buildGrid(array $cells, CarbonImmutable $startDate): array
    {
        $rows = array_fill(0, 7, []);
        $startDow = (int) $startDate->dayOfWeekIso - 1;

        foreach ($cells as $index => $cell) {
            $dow = ($startDow + $index) % 7;
            $rows[$dow][] = $cell;
        }

        return $rows;
    }

    /**
     * @return array<int, array{label: string, offset: int}>
     */
    protected function buildMonthLabels(CarbonImmutable $startDate, int $days): array
    {
        $labels = [];
        $startDow = (int) $startDate->dayOfWeekIso - 1;
        $seen = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->addDays($i);
            $month = $date->format('M');
            $week = intdiv($startDow + $i, 7);

            if (! isset($seen[$month])) {
                $seen[$month] = true;
                $labels[] = ['label' => $month, 'offset' => $week];
            }
        }

        return $labels;
    }

    protected function resolveData(): HeatmapCalendarWidgetData
    {
        if ($this->getWidgetResolver() !== null) {
            return $this->resolveConfiguredData($this->getDefinition());
        }

        return WidgetDataCache::remember(
            widget: static::class,
            resolver: static::class,
            filters: $this->pageFilters ?? [],
            options: $this->getWidgetCacheContext(),
            ttl: $this->getWidgetCacheTtl(),
            key: $this->getWidgetCacheKey(),
            callback: fn () => $this->getData(),
        );
    }

    protected function getData(): HeatmapCalendarWidgetData
    {
        throw new \BadMethodCallException(
            static::class.' must implement getData() or set a $dataResolver.',
        );
    }

    protected function getDefinition(): HeatmapCalendarWidgetDefinition
    {
        if ($this->usesLegacyDefinition()) {
            return HeatmapCalendarWidgetDefinition::fromArray($this->definition ?? []);
        }

        $definition = HeatmapCalendarWidgetDefinition::make($this->getWidgetLabel());
        $this->configureBaseDefinition($definition);
        $definition
            ->weeks($this->getWeeksToShow())
            ->colorScheme($this->getColorScheme());

        return $definition;
    }

    protected function getColorScheme(): string
    {
        return 'green';
    }

    protected function getWeeksToShow(): int
    {
        return 12;
    }

    protected function resolveConfiguredData(HeatmapCalendarWidgetDefinition $definition): HeatmapCalendarWidgetData
    {
        $resolver = app($definition->resolverClass());

        if (! $resolver instanceof ResolvesHeatmapCalendarWidgetData) {
            throw new \InvalidArgumentException('Heatmap calendar widget resolver must implement ResolvesHeatmapCalendarWidgetData.');
        }

        return WidgetDataCache::remember(
            widget: static::class,
            resolver: $definition->resolverClass(),
            filters: [
                ...($this->pageFilters ?? []),
                'weeks' => $definition->weeksToShow(),
            ],
            options: $definition->optionsData(),
            ttl: $definition->cacheTtl(),
            key: $definition->cacheKey(),
            callback: fn () => $resolver->resolve(
                [
                    ...($this->pageFilters ?? []),
                    'weeks' => $definition->weeksToShow(),
                ],
                [...$definition->optionsData(), 'weeks' => $definition->weeksToShow()],
            ),
        );
    }
}
