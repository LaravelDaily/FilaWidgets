<?php

namespace LaravelDaily\FilaWidgets\Data;

readonly class SparklineTableWidgetData
{
    /**
     * @param  array<int, SparklineTableRowData>  $rows
     */
    public function __construct(
        public array $rows,
        public ?string $description = null,
    ) {}

    public static function fromRows(SparklineTableRowData ...$rows): self
    {
        return new self(rows: array_values($rows));
    }

    /**
     * @return array{rows: array<int, array<string, mixed>>, description: ?string}
     */
    public function toArray(): array
    {
        return [
            'rows' => array_map(fn (SparklineTableRowData $row) => $row->toArray(), $this->rows),
            'description' => $this->description,
        ];
    }

    /**
     * @param  array{rows: array<int, array<string, mixed>>, description: ?string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            rows: array_map(fn (array $row) => SparklineTableRowData::fromArray($row), $data['rows'] ?? []),
            description: $data['description'] ?? null,
        );
    }
}
