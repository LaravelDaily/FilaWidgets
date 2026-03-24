<?php

namespace LaravelDaily\FilaWidgets\Data;

readonly class CompletionRateWidgetData
{
    public function __construct(
        public float $value,
        public float $min = 0,
        public float $max = 100,
        public ?string $description = null,
        public bool $isEmpty = false,
    ) {}

    /**
     * @return array{value: float, min: float, max: float, description: ?string, isEmpty: bool}
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'min' => $this->min,
            'max' => $this->max,
            'description' => $this->description,
            'isEmpty' => $this->isEmpty,
        ];
    }

    /**
     * @param  array{value: float, min: float, max: float, description: ?string, isEmpty: bool}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            value: $data['value'],
            min: $data['min'] ?? 0,
            max: $data['max'] ?? 100,
            description: $data['description'] ?? null,
            isEmpty: $data['isEmpty'] ?? false,
        );
    }
}
