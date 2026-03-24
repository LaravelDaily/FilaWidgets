<?php

namespace LaravelDaily\FilaWidgets\Support;

class DateRangeFilter
{
    public function __construct(
        protected string $value,
        protected int $days,
        protected string $label,
        protected string $shortLabel,
    ) {}

    public static function fromFilter(?string $value): self
    {
        return match ($value) {
            'last_7_days' => new self('last_7_days', 7, 'Last 7 days', '7D'),
            'last_60_days' => new self('last_60_days', 60, 'Last 60 days', '60D'),
            default => new self('last_30_days', 30, 'Last 30 days', '30D'),
        };
    }

    public function currentPeriod(): array
    {
        return [
            now()->subDays($this->days - 1)->startOfDay()->toImmutable(),
            now()->endOfDay()->toImmutable(),
        ];
    }

    public function days(): int
    {
        return $this->days;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function shortLabel(): string
    {
        return $this->shortLabel;
    }

    public function value(): string
    {
        return $this->value;
    }
}
