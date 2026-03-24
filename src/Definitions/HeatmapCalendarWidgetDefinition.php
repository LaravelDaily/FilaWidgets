<?php

namespace LaravelDaily\FilaWidgets\Definitions;

/**
 * @internal Legacy compatibility layer for definition-array based heatmap widgets.
 */
class HeatmapCalendarWidgetDefinition extends WidgetDefinition
{
    protected int $weeks = 12;

    protected string $colorScheme = 'green';

    public function weeks(int $weeks): static
    {
        $this->weeks = $weeks;

        return $this;
    }

    public function weeksToShow(): int
    {
        return $this->weeks;
    }

    public function colorScheme(string $colorScheme): static
    {
        $this->colorScheme = $colorScheme;

        return $this;
    }

    public function colorSchemeName(): string
    {
        return $this->colorScheme;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        $definition = static::fillBase(new static, $data);
        $definition->weeks = $data['weeks'] ?? 12;
        $definition->colorScheme = $data['colorScheme'] ?? 'green';

        return $definition;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...$this->baseArray(),
            'weeks' => $this->weeks,
            'colorScheme' => $this->colorScheme,
        ];
    }
}
