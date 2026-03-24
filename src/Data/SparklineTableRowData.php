<?php

namespace LaravelDaily\FilaWidgets\Data;

readonly class SparklineTableRowData
{
    /**
     * @param  array<int, float>  $sparkline  Ordered data points for the sparkline
     */
    public function __construct(
        public string $label,
        public float $value,
        public ?float $previousValue = null,
        public array $sparkline = [],
        public ?string $format = null,
        public ?int $precision = null,
        public ?string $url = null,
        public bool $openUrlInNewTab = false,
        public ?string $color = null,
        public bool $showSparkline = true,
    ) {}

    /**
     * @return array{label: string, value: float, previousValue: ?float, sparkline: array<int, float>, format: ?string, precision: ?int, url: ?string, openUrlInNewTab: bool, color: ?string, showSparkline: bool}
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'value' => $this->value,
            'previousValue' => $this->previousValue,
            'sparkline' => $this->sparkline,
            'format' => $this->format,
            'precision' => $this->precision,
            'url' => $this->url,
            'openUrlInNewTab' => $this->openUrlInNewTab,
            'color' => $this->color,
            'showSparkline' => $this->showSparkline,
        ];
    }

    /**
     * @param  array{label: string, value: float, previousValue: ?float, sparkline: array<int, float>, format: ?string, precision: ?int, url: ?string, openUrlInNewTab: bool, color?: ?string, showSparkline?: bool}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            label: $data['label'],
            value: $data['value'],
            previousValue: $data['previousValue'] ?? null,
            sparkline: $data['sparkline'] ?? [],
            format: $data['format'] ?? null,
            precision: $data['precision'] ?? null,
            url: $data['url'] ?? null,
            openUrlInNewTab: $data['openUrlInNewTab'] ?? false,
            color: $data['color'] ?? null,
            showSparkline: $data['showSparkline'] ?? true,
        );
    }
}
