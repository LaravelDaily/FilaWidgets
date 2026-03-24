<?php

namespace LaravelDaily\FilaWidgets\Data;

readonly class ProgressWidgetData
{
    public function __construct(
        public float $currentValue,
        public float $goalValue,
        public ?float $projectionValue = null,
        public ?string $description = null,
        public ?string $projectionLabel = null,
    ) {}

    /**
     * @return array{currentValue: float, goalValue: float, projectionValue: ?float, description: ?string, projectionLabel: ?string}
     */
    public function toArray(): array
    {
        return [
            'currentValue' => $this->currentValue,
            'goalValue' => $this->goalValue,
            'projectionValue' => $this->projectionValue,
            'description' => $this->description,
            'projectionLabel' => $this->projectionLabel,
        ];
    }

    /**
     * @param  array{currentValue: float, goalValue: float, projectionValue: ?float, description: ?string, projectionLabel: ?string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            currentValue: $data['currentValue'],
            goalValue: $data['goalValue'],
            projectionValue: $data['projectionValue'] ?? null,
            description: $data['description'] ?? null,
            projectionLabel: $data['projectionLabel'] ?? null,
        );
    }
}
