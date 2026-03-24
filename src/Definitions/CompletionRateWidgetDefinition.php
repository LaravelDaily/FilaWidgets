<?php

namespace LaravelDaily\FilaWidgets\Definitions;

/**
 * @internal Legacy compatibility layer for definition-array based completion-rate widgets.
 */
class CompletionRateWidgetDefinition extends WidgetDefinition
{
    protected string $format = 'number';

    protected int $precision = 1;

    /**
     * @var array<int, array{threshold: float, color: string, label: string}>
     */
    protected array $thresholds = [];

    protected string $unit = '%';

    /**
     * @param  array<int, array{threshold: float, color: string, label: string}>  $thresholds
     */
    public function thresholds(array $thresholds): static
    {
        $this->thresholds = $thresholds;

        return $this;
    }

    /**
     * @return array<int, array{threshold: float, color: string, label: string}>
     */
    public function thresholdsData(): array
    {
        return $this->thresholds;
    }

    public function unit(string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function unitLabel(): string
    {
        return $this->unit;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        $definition = static::fillBase(new static, $data);
        $definition->thresholds = $data['thresholds'] ?? [];
        $definition->unit = $data['unit'] ?? '%';

        return $definition;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...$this->baseArray(),
            'thresholds' => $this->thresholds,
            'unit' => $this->unit,
        ];
    }
}
