<?php

namespace LaravelDaily\FilaWidgets\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Illuminate\Support\Number;
use LaravelDaily\FilaWidgets\Contracts\ResolvesBreakdownWidgetData;
use LaravelDaily\FilaWidgets\Data\BreakdownWidgetData;
use LaravelDaily\FilaWidgets\Definitions\BreakdownWidgetDefinition;
use LaravelDaily\FilaWidgets\Support\DateRangeFilter;
use LaravelDaily\FilaWidgets\Support\WidgetDataCache;
use LaravelDaily\FilaWidgets\Support\WidgetMetricCalculator;
use LaravelDaily\FilaWidgets\Support\WidgetValueFormatter;
use LaravelDaily\FilaWidgets\Widgets\Concerns\InteractsWithWidgetConfiguration;

class BreakdownWidget extends Widget
{
    use InteractsWithPageFilters;
    use InteractsWithWidgetConfiguration;

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 3,
    ];

    protected string $view = 'filawidgets::widgets.breakdown-widget';

    public function getColumnSpan(): int|string|array
    {
        $definition = $this->getDefinition();

        return $definition->columnSpanValue() ?? $this->columnSpan;
    }

    protected function getViewData(): array
    {
        $definition = $this->getDefinition();
        $data = $this->resolveData();
        $items = WidgetMetricCalculator::breakdown(
            items: array_map(fn ($item): array => [
                'label' => $item->label,
                'value' => $item->value,
                'previousValue' => $item->previousValue,
                'color' => $item->color,
                'icon' => $item->icon,
                'url' => $item->url,
            ], $data->items),
            limit: $definition->limitValue(),
            groupOther: $definition->groupingOverflowIntoOther(),
            sortBy: $definition->sortByValue(),
            sortDirection: $definition->sortDirectionValue(),
        );
        $dateRange = DateRangeFilter::fromFilter($this->getRangeFilter());
        $showDelta = $definition->showingDelta();
        $deltaThresholds = $definition->deltaThresholdsValue();

        return [
            'actionLabel' => $definition->actionLabel(),
            'actionNewTab' => $definition->actionOpensInNewTab(),
            'actionUrl' => $definition->actionUrl(),
            'color' => $definition->colorName(),
            'deltaThresholds' => $deltaThresholds,
            'description' => $data->description,
            'emptyStateDescription' => $definition->emptyStateDescription() ?? 'Try changing the selected period or removing filters.',
            'emptyStateHeading' => $definition->emptyStateHeading() ?? 'No breakdown data yet.',
            'icon' => $definition->iconName(),
            'label' => $definition->labelText(),
            'rangeLabel' => $dateRange->shortLabel(),
            'rows' => array_map(fn (array $item): array => [
                'color' => $item['color'],
                'contribution' => Number::format($item['contributionPercentage'], maxPrecision: 1).'%',
                'delta' => ($item['deltaPercentage'] === null || ! $showDelta)
                    ? null
                    : WidgetValueFormatter::formatSignedPercentage($item['deltaPercentage']),
                'deltaColor' => ($item['deltaPercentage'] !== null && $deltaThresholds !== [])
                    ? self::resolveDeltaColor($item['deltaPercentage'], $deltaThresholds)
                    : null,
                'formattedValue' => WidgetValueFormatter::format(
                    $item['value'],
                    $definition->formatName(),
                    $definition->currencyCode(),
                    $definition->precisionValue(),
                ),
                'icon' => $item['icon'],
                'label' => $item['label'],
                'url' => $item['url'],
            ], $items),
            'showContribution' => $definition->showingContribution(),
            'showDelta' => $showDelta,
            'total' => WidgetValueFormatter::format(
                array_sum(array_map(fn ($item): float => $item->value, $data->items)),
                $definition->formatName(),
                $definition->currencyCode(),
                $definition->precisionValue(),
            ),
        ];
    }

    protected function resolveData(): BreakdownWidgetData
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

    protected function getData(): BreakdownWidgetData
    {
        throw new \BadMethodCallException(
            static::class.' must implement getData() or set a $dataResolver.',
        );
    }

    protected function getDefinition(): BreakdownWidgetDefinition
    {
        if ($this->usesLegacyDefinition()) {
            return BreakdownWidgetDefinition::fromArray($this->definition ?? []);
        }

        $definition = BreakdownWidgetDefinition::make($this->getWidgetLabel());
        $this->configureBaseDefinition($definition);
        $definition
            ->limit($this->getItemLimit())
            ->groupOther($this->shouldGroupOther())
            ->sortBy($this->getSortBy())
            ->sortDirection($this->getSortDirection())
            ->showContribution($this->getShowContribution())
            ->showDelta($this->getShowDelta())
            ->deltaThresholds($this->getDeltaThresholds());

        return $definition;
    }

    protected ?int $itemLimit = null;

    protected bool $groupOther = false;

    protected string $sortBy = 'value';

    protected string $sortDirection = 'desc';

    protected bool $showContribution = true;

    protected bool $showDelta = true;

    /**
     * @var array<int, array{threshold: float, color: string}>
     */
    protected array $deltaThresholds = [];

    protected function getItemLimit(): ?int
    {
        return $this->itemLimit;
    }

    protected function shouldGroupOther(): bool
    {
        return $this->groupOther;
    }

    protected function getSortBy(): string
    {
        return $this->sortBy;
    }

    protected function getSortDirection(): string
    {
        return $this->sortDirection;
    }

    protected function getShowContribution(): bool
    {
        return $this->showContribution;
    }

    protected function getShowDelta(): bool
    {
        return $this->showDelta;
    }

    /**
     * @return array<int, array{threshold: float, color: string}>
     */
    protected function getDeltaThresholds(): array
    {
        return $this->deltaThresholds;
    }

    /**
     * @param  array<int, array{threshold: float, color: string}>  $thresholds
     */
    protected static function resolveDeltaColor(float $deltaPercentage, array $thresholds): ?string
    {
        usort($thresholds, fn (array $a, array $b): int => $a['threshold'] <=> $b['threshold']);

        $resolved = null;

        foreach ($thresholds as $threshold) {
            if ($deltaPercentage <= $threshold['threshold']) {
                return $threshold['color'];
            }

            $resolved = $threshold['color'];
        }

        return $resolved;
    }

    protected function resolveConfiguredData(BreakdownWidgetDefinition $definition): BreakdownWidgetData
    {
        $resolver = app($definition->resolverClass());

        if (! $resolver instanceof ResolvesBreakdownWidgetData) {
            throw new \InvalidArgumentException('Breakdown widget resolver must implement ResolvesBreakdownWidgetData.');
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
