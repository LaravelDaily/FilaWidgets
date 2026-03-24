<?php

namespace LaravelDaily\FilaWidgets\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Illuminate\Support\Number;
use LaravelDaily\FilaWidgets\Contracts\ResolvesProgressWidgetData;
use LaravelDaily\FilaWidgets\Data\ProgressWidgetData;
use LaravelDaily\FilaWidgets\Definitions\ProgressWidgetDefinition;
use LaravelDaily\FilaWidgets\Support\DateRangeFilter;
use LaravelDaily\FilaWidgets\Support\WidgetDataCache;
use LaravelDaily\FilaWidgets\Support\WidgetMetricCalculator;
use LaravelDaily\FilaWidgets\Support\WidgetValueFormatter;
use LaravelDaily\FilaWidgets\Widgets\Concerns\InteractsWithWidgetConfiguration;

class ProgressWidget extends Widget
{
    use InteractsWithPageFilters;
    use InteractsWithWidgetConfiguration;

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 2,
    ];

    protected string $view = 'filawidgets::widgets.progress-widget';

    protected float $goal = 0;

    protected int $goalRangeDays = 30;

    protected bool $showProjection = true;

    public function getColumnSpan(): int|string|array
    {
        $definition = $this->getDefinition();

        return $definition->columnSpanValue() ?? $this->columnSpan;
    }

    protected function getViewData(): array
    {
        $definition = $this->getDefinition();
        $data = $this->resolveData();
        $metric = WidgetMetricCalculator::progress($data->currentValue, $data->goalValue);
        $dateRange = DateRangeFilter::fromFilter($this->getRangeFilter());

        return [
            'actionLabel' => $definition->actionLabel(),
            'actionNewTab' => $definition->actionOpensInNewTab(),
            'actionUrl' => $definition->actionUrl(),
            'barClasses' => $this->getBarClasses($metric['status']),
            'barPercentage' => $metric['barPercentage'],
            'color' => $definition->colorName(),
            'description' => $data->description ?? $this->getDefaultDescription($dateRange),
            'emptyStateDescription' => $definition->emptyStateDescription() ?? 'The selected period has not recorded any progress yet.',
            'emptyStateHeading' => $definition->emptyStateHeading() ?? 'No progress recorded.',
            'formattedCurrentValue' => WidgetValueFormatter::format(
                $data->currentValue,
                $definition->formatName(),
                $definition->currencyCode(),
                $definition->precisionValue(),
            ),
            'formattedGoalValue' => WidgetValueFormatter::format(
                $data->goalValue,
                $definition->formatName(),
                $definition->currencyCode(),
                $definition->precisionValue(),
            ),
            'formattedProjectionValue' => ($data->projectionValue !== null) && $this->showProjection
                ? WidgetValueFormatter::format(
                    $data->projectionValue,
                    $definition->formatName(),
                    $definition->currencyCode(),
                    $definition->precisionValue(),
                )
                : null,
            'hasCurrentValue' => $data->currentValue > 0,
            'hasGoalValue' => $data->goalValue > 0,
            'icon' => $definition->iconName(),
            'label' => $definition->labelText(),
            'percentageLabel' => Number::format($metric['percentage'], maxPrecision: 1).'% complete',
            'projectionLabel' => $data->projectionLabel ?? $this->getDefaultProjectionLabel(),
            'rangeLabel' => $dateRange->shortLabel(),
            'statusClasses' => $this->getStatusClasses($metric['status']),
        ];
    }

    protected function getDefaultDescription(DateRangeFilter $dateRange): ?string
    {
        if ($this->goal <= 0) {
            return null;
        }

        return 'Goal scaled to '.strtolower($dateRange->label());
    }

    protected function getDefaultProjectionLabel(): string
    {
        if ($this->goalRangeDays > 0) {
            return "Projected {$this->goalRangeDays}-day pace";
        }

        return 'Projected pace';
    }

    protected function getBarClasses(string $status): string
    {
        return match ($status) {
            'success' => 'bg-success-500',
            'normal' => 'bg-primary-500',
            default => 'bg-warning-500',
        };
    }

    protected function getStatusClasses(string $status): string
    {
        return match ($status) {
            'success' => 'text-success-600 dark:text-success-400',
            'normal' => 'text-primary-600 dark:text-primary-400',
            default => 'text-warning-600 dark:text-warning-400',
        };
    }

    protected function resolveData(): ProgressWidgetData
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

    protected function getData(): ProgressWidgetData
    {
        $currentValue = $this->getCurrentValue();

        $dateRange = DateRangeFilter::fromFilter($this->getRangeFilter());

        $goalValue = $this->goalRangeDays > 0
            ? round($this->goal * ($dateRange->days() / $this->goalRangeDays), 2)
            : $this->goal;

        $projectionValue = $dateRange->days() > 0 && $this->goalRangeDays > 0
            ? round(($currentValue / $dateRange->days()) * $this->goalRangeDays, 2)
            : null;

        return new ProgressWidgetData(
            currentValue: $currentValue,
            goalValue: $goalValue,
            projectionValue: $projectionValue,
        );
    }

    protected function getCurrentValue(): float
    {
        throw new \BadMethodCallException(
            static::class.' must implement getCurrentValue() or override getData().',
        );
    }

    protected function getDefinition(): ProgressWidgetDefinition
    {
        if ($this->usesLegacyDefinition()) {
            return ProgressWidgetDefinition::fromArray($this->definition ?? []);
        }

        $definition = ProgressWidgetDefinition::make($this->getWidgetLabel());
        $this->configureBaseDefinition($definition);
        $definition->showProjection($this->showProjection);

        return $definition;
    }

    protected function resolveConfiguredData(ProgressWidgetDefinition $definition): ProgressWidgetData
    {
        $resolver = app($definition->resolverClass());

        if (! $resolver instanceof ResolvesProgressWidgetData) {
            throw new \InvalidArgumentException('Progress widget resolver must implement ResolvesProgressWidgetData.');
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
