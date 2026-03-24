<?php

namespace LaravelDaily\FilaWidgets\Data;

readonly class BreakdownItemData
{
    public function __construct(
        public string $label,
        public float $value,
        public ?float $previousValue = null,
        public ?string $color = null,
        public ?string $icon = null,
        public ?string $url = null,
    ) {}

    public static function make(string $label, float $value): self
    {
        return new self(label: $label, value: $value);
    }

    public function withPreviousValue(?float $previousValue): self
    {
        return new self($this->label, $this->value, $previousValue, $this->color, $this->icon, $this->url);
    }

    public function withColor(?string $color): self
    {
        return new self($this->label, $this->value, $this->previousValue, $color, $this->icon, $this->url);
    }

    public function withIcon(?string $icon): self
    {
        return new self($this->label, $this->value, $this->previousValue, $this->color, $icon, $this->url);
    }

    public function withUrl(?string $url): self
    {
        return new self($this->label, $this->value, $this->previousValue, $this->color, $this->icon, $url);
    }

    /**
     * @return array{label: string, value: float, previousValue: ?float, color: ?string, icon: ?string, url: ?string}
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'value' => $this->value,
            'previousValue' => $this->previousValue,
            'color' => $this->color,
            'icon' => $this->icon,
            'url' => $this->url,
        ];
    }

    /**
     * @param  array{label: string, value: float, previousValue: ?float, color?: ?string, icon?: ?string, url?: ?string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            label: $data['label'],
            value: $data['value'],
            previousValue: $data['previousValue'] ?? null,
            color: $data['color'] ?? null,
            icon: $data['icon'] ?? null,
            url: $data['url'] ?? null,
        );
    }
}
