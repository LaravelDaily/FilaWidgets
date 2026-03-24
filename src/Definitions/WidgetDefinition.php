<?php

namespace LaravelDaily\FilaWidgets\Definitions;

use BackedEnum;
use Filament\Support\Icons\Heroicon;

/**
 * @internal Legacy compatibility layer for the pre-refactor array-definition API.
 *
 * @deprecated Use widget subclasses and protected configuration methods instead of definition builders.
 */
abstract class WidgetDefinition
{
    protected string $label = 'Widget';

    protected string $resolver = '';

    /**
     * @var array<string, mixed>
     */
    protected array $options = [];

    protected string $format = 'currency';

    protected string $currency = 'USD';

    protected int $precision = 2;

    protected ?int $cacheTtl = null;

    protected ?string $cacheKey = null;

    protected ?string $icon = null;

    protected ?string $helpText = null;

    protected ?string $emptyStateHeading = null;

    protected ?string $emptyStateDescription = null;

    protected ?string $actionLabel = null;

    protected ?string $actionUrl = null;

    protected bool $actionOpenInNewTab = false;

    protected string $color = 'primary';

    protected int|string|array|null $columnSpan = null;

    public static function make(?string $label = null): static
    {
        $definition = new static;

        if ($label !== null) {
            $definition->label($label);
        }

        return $definition;
    }

    public function action(string $label, string $url, bool $openInNewTab = false): static
    {
        $this->actionLabel = $label;
        $this->actionUrl = $url;
        $this->actionOpenInNewTab = $openInNewTab;

        return $this;
    }

    public function cache(?int $ttl = 300, ?string $key = null): static
    {
        $this->cacheTtl = $ttl;
        $this->cacheKey = $key;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function columnSpan(int|string|array $columnSpan): static
    {
        $this->columnSpan = $columnSpan;

        return $this;
    }

    public function currency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function emptyState(?string $heading, ?string $description = null): static
    {
        $this->emptyStateHeading = $heading;
        $this->emptyStateDescription = $description;

        return $this;
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function helpText(?string $helpText): static
    {
        $this->helpText = $helpText;

        return $this;
    }

    public function icon(Heroicon|string|null $icon): static
    {
        $this->icon = $this->normalizeIcon($icon);

        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function precision(int $precision): static
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function resolver(string $resolver, array $options = []): static
    {
        $this->resolver = $resolver;

        if ($options !== []) {
            $this->options = $options;
        }

        return $this;
    }

    public function actionLabel(): ?string
    {
        return $this->actionLabel;
    }

    public function actionOpensInNewTab(): bool
    {
        return $this->actionOpenInNewTab;
    }

    public function actionUrl(): ?string
    {
        return $this->actionUrl;
    }

    public function cacheKey(): ?string
    {
        return $this->cacheKey;
    }

    public function cacheTtl(): ?int
    {
        return $this->cacheTtl;
    }

    public function colorName(): string
    {
        return $this->color;
    }

    public function columnSpanValue(): int|string|array|null
    {
        return $this->columnSpan;
    }

    public function currencyCode(): string
    {
        return $this->currency;
    }

    public function emptyStateDescription(): ?string
    {
        return $this->emptyStateDescription;
    }

    public function emptyStateHeading(): ?string
    {
        return $this->emptyStateHeading;
    }

    public function formatName(): string
    {
        return $this->format;
    }

    public function helpTextValue(): ?string
    {
        return $this->helpText;
    }

    public function iconName(): ?string
    {
        return $this->icon;
    }

    public function labelText(): string
    {
        return $this->label;
    }

    /**
     * @return array<string, mixed>
     */
    public function optionsData(): array
    {
        return $this->options;
    }

    public function precisionValue(): int
    {
        return $this->precision;
    }

    public function resolverClass(): string
    {
        return $this->resolver;
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseArray(): array
    {
        return [
            'actionLabel' => $this->actionLabel,
            'actionOpenInNewTab' => $this->actionOpenInNewTab,
            'actionUrl' => $this->actionUrl,
            'cacheKey' => $this->cacheKey,
            'cacheTtl' => $this->cacheTtl,
            'color' => $this->color,
            'columnSpan' => $this->columnSpan,
            'currency' => $this->currency,
            'emptyStateDescription' => $this->emptyStateDescription,
            'emptyStateHeading' => $this->emptyStateHeading,
            'format' => $this->format,
            'helpText' => $this->helpText,
            'icon' => $this->icon,
            'label' => $this->label,
            'options' => $this->options,
            'precision' => $this->precision,
            'resolver' => $this->resolver,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected static function fillBase(self $definition, array $data): static
    {
        $definition->actionLabel = $data['actionLabel'] ?? null;
        $definition->actionOpenInNewTab = $data['actionOpenInNewTab'] ?? false;
        $definition->actionUrl = $data['actionUrl'] ?? null;
        $definition->label = $data['label'] ?? $definition->label;
        $definition->resolver = $data['resolver'] ?? '';
        $definition->options = $data['options'] ?? [];
        $definition->icon = $data['icon'] ?? null;
        $definition->helpText = $data['helpText'] ?? null;
        $definition->emptyStateHeading = $data['emptyStateHeading'] ?? null;
        $definition->emptyStateDescription = $data['emptyStateDescription'] ?? null;
        $definition->color = $data['color'] ?? $definition->color;
        $definition->columnSpan = $data['columnSpan'] ?? null;
        $definition->format = $data['format'] ?? $definition->format;
        $definition->currency = $data['currency'] ?? $definition->currency;
        $definition->precision = $data['precision'] ?? $definition->precision;
        $definition->cacheTtl = $data['cacheTtl'] ?? null;
        $definition->cacheKey = $data['cacheKey'] ?? null;

        return $definition;
    }

    protected function normalizeIcon(Heroicon|string|null $icon): ?string
    {
        if ($icon instanceof BackedEnum) {
            return 'heroicon-'.$icon->value;
        }

        return $icon;
    }
}
