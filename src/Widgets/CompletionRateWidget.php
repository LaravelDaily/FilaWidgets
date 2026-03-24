<?php

namespace LaravelDaily\FilaWidgets\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use LaravelDaily\FilaWidgets\Contracts\ResolvesCompletionRateWidgetData;
use LaravelDaily\FilaWidgets\Data\CompletionRateWidgetData;
use LaravelDaily\FilaWidgets\Definitions\CompletionRateWidgetDefinition;
use LaravelDaily\FilaWidgets\Support\DateRangeFilter;
use LaravelDaily\FilaWidgets\Support\WidgetDataCache;
use LaravelDaily\FilaWidgets\Support\WidgetValueFormatter;
use LaravelDaily\FilaWidgets\Widgets\Concerns\InteractsWithWidgetConfiguration;

class CompletionRateWidget extends Widget
{
    use InteractsWithPageFilters;
    use InteractsWithWidgetConfiguration;

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 2,
    ];

    protected string $view = 'filawidgets::widgets.completion-rate-widget';

    protected string $unit = '%';

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

        $range = $data->max - $data->min;
        $normalizedValue = $range > 0
            ? max(0, min(100, (($data->value - $data->min) / $range) * 100))
            : 0;

        $thresholds = $this->getThresholds();
        $status = $data->isEmpty
            ? ['color' => 'gray', 'label' => '']
            : $this->resolveStatus($data->value, $thresholds);

        return [
            'actionLabel' => $definition->actionLabel(),
            'actionNewTab' => $definition->actionOpensInNewTab(),
            'actionUrl' => $definition->actionUrl(),
            'arcPercentage' => $normalizedValue,
            'color' => $definition->colorName(),
            'description' => $data->description,
            'emptyStateDescription' => $definition->emptyStateDescription() ?? 'Try changing the selected period or wait for incoming activity.',
            'emptyStateHeading' => $definition->emptyStateHeading() ?? 'No rate to show yet.',
            'formattedValue' => WidgetValueFormatter::format(
                $data->value,
                $definition->formatName(),
                $definition->currencyCode(),
                $definition->precisionValue(),
            ),
            'helpText' => $definition->helpTextValue(),
            'icon' => $definition->iconName(),
            'isEmpty' => $data->isEmpty,
            'label' => $definition->labelText(),
            'rangeLabel' => $dateRange->shortLabel(),
            'statusColor' => $status['color'],
            'statusLabel' => $status['label'],
            'unit' => $this->unit,
        ];
    }

    /**
     * @param  array<int, array{threshold: float, color: string, label: string}>  $thresholds
     * @return array{color: string, label: string}
     */
    protected function resolveStatus(float $value, array $thresholds): array
    {
        usort($thresholds, fn (array $a, array $b): int => $a['threshold'] <=> $b['threshold']);

        foreach ($thresholds as $threshold) {
            if ($value <= $threshold['threshold']) {
                return ['color' => $threshold['color'], 'label' => $threshold['label']];
            }
        }

        if ($thresholds !== []) {
            $last = end($thresholds);

            return ['color' => $last['color'], 'label' => $last['label']];
        }

        return ['color' => 'primary', 'label' => ''];
    }

    protected function resolveData(): CompletionRateWidgetData
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

    protected function getData(): CompletionRateWidgetData
    {
        $counts = $this->getCounts();
        $completed = $counts['completed'];
        $total = $counts['total'];

        if ($total === 0) {
            return new CompletionRateWidgetData(
                value: 0,
                isEmpty: true,
            );
        }

        return new CompletionRateWidgetData(
            value: round(($completed / $total) * 100, 1),
            description: "{$completed} of {$total} orders fulfilled",
        );
    }

    /**
     * @return array{completed: int, total: int}
     */
    protected function getCounts(): array
    {
        throw new \BadMethodCallException(
            static::class.' must implement getCounts() or override getData().',
        );
    }

    /**
     * @return array<int, array{threshold: float, color: string, label: string}>
     */
    protected function getThresholds(): array
    {
        return [];
    }

    protected function getWidgetFormat(): string
    {
        return 'number';
    }

    protected function getWidgetPrecision(): int
    {
        return 1;
    }

    protected function getDefinition(): CompletionRateWidgetDefinition
    {
        if ($this->usesLegacyDefinition()) {
            return CompletionRateWidgetDefinition::fromArray($this->definition ?? []);
        }

        $definition = CompletionRateWidgetDefinition::make($this->getWidgetLabel());
        $this->configureBaseDefinition($definition);
        $definition
            ->unit($this->unit)
            ->thresholds($this->getThresholds());

        return $definition;
    }

    protected function resolveConfiguredData(CompletionRateWidgetDefinition $definition): CompletionRateWidgetData
    {
        $resolver = app($definition->resolverClass());

        if (! $resolver instanceof ResolvesCompletionRateWidgetData) {
            throw new \InvalidArgumentException('Completion rate widget resolver must implement ResolvesCompletionRateWidgetData.');
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
