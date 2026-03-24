<?php

namespace LaravelDaily\FilaWidgets\Widgets\Concerns;

use BackedEnum;
use Filament\Support\Icons\Heroicon;
use LaravelDaily\FilaWidgets\Definitions\WidgetDefinition;
use Livewire\Attributes\Reactive;

trait InteractsWithWidgetConfiguration
{
    /**
     * @var array<string, mixed>
     */
    #[Reactive]
    public ?array $definition = [];

    public ?string $range = null;

    protected ?string $widgetLabel = null;

    protected string $widgetFormat = 'currency';

    protected string $widgetCurrency = 'USD';

    protected int $widgetPrecision = 2;

    protected ?int $widgetCacheTtl = null;

    protected ?string $widgetCacheKey = null;

    protected Heroicon|string|null $widgetIcon = null;

    protected ?string $widgetHelpText = null;

    protected ?string $widgetEmptyStateHeading = null;

    protected ?string $widgetEmptyStateDescription = null;

    protected ?string $widgetActionLabel = null;

    protected ?string $widgetActionUrl = null;

    protected bool $widgetActionOpenInNewTab = false;

    protected string $widgetColor = 'primary';

    protected static ?string $dataResolver = null;

    /**
     * @var array<string, mixed>
     */
    protected array $resolverOptions = [];

    protected function usesLegacyDefinition(): bool
    {
        return ($this->definition !== null) && ($this->definition !== []);
    }

    protected function configureBaseDefinition(WidgetDefinition $definition): void
    {
        $definition
            ->label($this->getWidgetLabel())
            ->format($this->getWidgetFormat())
            ->currency($this->getWidgetCurrency())
            ->precision($this->getWidgetPrecision())
            ->color($this->getWidgetColor());

        $columnSpan = $this->getConfiguredColumnSpan();

        if ($columnSpan !== null) {
            $definition->columnSpan($columnSpan);
        }

        if (($actionLabel = $this->getWidgetActionLabel()) !== null && ($actionUrl = $this->getWidgetActionUrl()) !== null) {
            $definition->action($actionLabel, $actionUrl, $this->widgetActionOpensInNewTab());
        }

        if (($icon = $this->getWidgetIcon()) !== null) {
            $definition->icon($icon);
        }

        if (($helpText = $this->getWidgetHelpText()) !== null) {
            $definition->helpText($helpText);
        }

        if ($this->getWidgetEmptyStateHeading() !== null || $this->getWidgetEmptyStateDescription() !== null) {
            $definition->emptyState(
                $this->getWidgetEmptyStateHeading(),
                $this->getWidgetEmptyStateDescription(),
            );
        }

        if ($this->getWidgetCacheTtl() !== null || $this->getWidgetCacheKey() !== null) {
            $definition->cache($this->getWidgetCacheTtl(), $this->getWidgetCacheKey());
        }

        if (($resolver = $this->getWidgetResolver()) !== null) {
            $definition->resolver($resolver, $this->getWidgetResolverOptions());
        }
    }

    protected function getConfiguredColumnSpan(): int|string|array|null
    {
        return property_exists($this, 'columnSpan') ? $this->columnSpan : null;
    }

    protected function getWidgetActionLabel(): ?string
    {
        return $this->widgetActionLabel;
    }

    protected function getWidgetActionUrl(): ?string
    {
        return $this->widgetActionUrl;
    }

    protected function widgetActionOpensInNewTab(): bool
    {
        return $this->widgetActionOpenInNewTab;
    }

    protected function getWidgetCacheKey(): ?string
    {
        return $this->widgetCacheKey;
    }

    protected function getWidgetCacheTtl(): ?int
    {
        return $this->widgetCacheTtl;
    }

    protected function getWidgetColor(): string
    {
        return $this->widgetColor;
    }

    protected function getWidgetCurrency(): string
    {
        return $this->widgetCurrency;
    }

    protected function getWidgetEmptyStateDescription(): ?string
    {
        return $this->widgetEmptyStateDescription;
    }

    protected function getWidgetEmptyStateHeading(): ?string
    {
        return $this->widgetEmptyStateHeading;
    }

    protected function getWidgetFormat(): string
    {
        return $this->widgetFormat;
    }

    protected function getWidgetHelpText(): ?string
    {
        return $this->widgetHelpText;
    }

    protected function getWidgetIcon(): ?string
    {
        return $this->normalizeIcon($this->widgetIcon);
    }

    protected function getWidgetLabel(): string
    {
        return $this->widgetLabel ?? 'Widget';
    }

    protected function getWidgetPrecision(): int
    {
        return $this->widgetPrecision;
    }

    protected function getWidgetResolver(): ?string
    {
        return static::$dataResolver;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getWidgetResolverOptions(): array
    {
        return $this->resolverOptions;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getWidgetCacheContext(): array
    {
        return [];
    }

    protected function getRangeFilter(): ?string
    {
        return $this->range ?? $this->pageFilters['range'] ?? null;
    }

    protected function normalizeIcon(Heroicon|string|null $icon): ?string
    {
        if ($icon instanceof BackedEnum) {
            return 'heroicon-'.$icon->value;
        }

        return $icon;
    }
}
