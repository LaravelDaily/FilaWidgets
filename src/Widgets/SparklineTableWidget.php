<?php

namespace LaravelDaily\FilaWidgets\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use LaravelDaily\FilaWidgets\Contracts\ResolvesSparklineTableWidgetData;
use LaravelDaily\FilaWidgets\Data\SparklineTableWidgetData;
use LaravelDaily\FilaWidgets\Definitions\SparklineTableWidgetDefinition;
use LaravelDaily\FilaWidgets\Support\DateRangeFilter;
use LaravelDaily\FilaWidgets\Support\WidgetDataCache;
use LaravelDaily\FilaWidgets\Support\WidgetMetricCalculator;
use LaravelDaily\FilaWidgets\Support\WidgetValueFormatter;
use LaravelDaily\FilaWidgets\Widgets\Concerns\InteractsWithWidgetConfiguration;

class SparklineTableWidget extends Widget
{
    use InteractsWithPageFilters;
    use InteractsWithWidgetConfiguration;

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 3,
    ];

    protected string $view = 'filawidgets::widgets.sparkline-table-widget';

    public function getColumnSpan(): int|string|array
    {
        $definition = $this->getDefinition();

        return $definition->columnSpanValue() ?? $this->columnSpan;
    }

    protected function getViewData(): array
    {
        $definition = $this->getDefinition();
        $data = $this->resolveData();
        $dateRange = DateRangeFilter::fromFilter($this->getRangeFilter());

        $rows = array_map(function ($row) use ($definition): array {
            $metric = WidgetMetricCalculator::comparison($row->value, $row->previousValue ?? 0);

            return [
                'actionNewTab' => $row->openUrlInNewTab,
                'actionUrl' => $row->url,
                'color' => $row->color,
                'label' => $row->label,
                'formattedValue' => WidgetValueFormatter::format(
                    $row->value,
                    $row->format ?? $definition->formatName(),
                    $definition->currencyCode(),
                    $row->precision ?? $definition->precisionValue(),
                ),
                'change' => $metric['percentageChange'] !== null
                    ? WidgetValueFormatter::formatSignedPercentage($metric['percentageChange'])
                    : null,
                'showSparkline' => $row->showSparkline,
                'trend' => $metric['trend'],
                'sparkline' => $row->sparkline,
            ];
        }, $data->rows);

        return [
            'actionLabel' => $definition->actionLabel(),
            'actionNewTab' => $definition->actionOpensInNewTab(),
            'actionUrl' => $definition->actionUrl(),
            'color' => $definition->colorName(),
            'description' => $data->description,
            'emptyStateDescription' => $definition->emptyStateDescription() ?? 'Try widening the selected period or adjusting your filters.',
            'emptyStateHeading' => $definition->emptyStateHeading() ?? 'No metrics available.',
            'icon' => $definition->iconName(),
            'label' => $definition->labelText(),
            'rangeLabel' => $dateRange->shortLabel(),
            'rows' => $rows,
        ];
    }

    protected function resolveData(): SparklineTableWidgetData
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

    protected function getData(): SparklineTableWidgetData
    {
        throw new \BadMethodCallException(
            static::class.' must implement getData() or set a $dataResolver.',
        );
    }

    protected function getDefinition(): SparklineTableWidgetDefinition
    {
        if ($this->usesLegacyDefinition()) {
            return SparklineTableWidgetDefinition::fromArray($this->definition ?? []);
        }

        $definition = SparklineTableWidgetDefinition::make($this->getWidgetLabel());
        $this->configureBaseDefinition($definition);

        return $definition;
    }

    protected function resolveConfiguredData(SparklineTableWidgetDefinition $definition): SparklineTableWidgetData
    {
        $resolver = app($definition->resolverClass());

        if (! $resolver instanceof ResolvesSparklineTableWidgetData) {
            throw new \InvalidArgumentException('Sparkline table widget resolver must implement ResolvesSparklineTableWidgetData.');
        }

        return WidgetDataCache::remember(
            widget: static::class,
            resolver: $definition->resolverClass(),
            filters: $this->pageFilters ?? [],
            options: $definition->optionsData(),
            ttl: $definition->cacheTtl(),
            key: $definition->cacheKey(),
            callback: fn () => $resolver->resolve($this->pageFilters ?? [], $definition->optionsData()),
        );
    }
}
