<?php

namespace LaravelDaily\FilaWidgets\Data;

use Illuminate\Support\Collection;

readonly class BreakdownWidgetData
{
    /**
     * @param  array<int, BreakdownItemData>  $items
     */
    public function __construct(
        public array $items,
        public ?string $description = null,
    ) {}

    /**
     * @param  Collection<array-key, mixed>|array<array-key, mixed>  $items
     * @param  string|\Closure(mixed, mixed): string  $labelKey
     * @param  string|\Closure(mixed, mixed): float  $valueKey
     * @param  (string|\Closure(mixed, mixed): (float|null))|null  $previousValueKey
     */
    public static function fromCollection(
        Collection|array $items,
        string|\Closure $labelKey = 'label',
        string|\Closure $valueKey = 'value',
        string|\Closure|null $previousValueKey = null,
        ?string $description = null,
    ): self {
        $collection = $items instanceof Collection ? $items : collect($items);

        $mapped = $collection->map(function (mixed $item, mixed $key) use ($labelKey, $valueKey, $previousValueKey): BreakdownItemData {
            $label = $labelKey instanceof \Closure ? $labelKey($item, $key) : data_get($item, $labelKey);
            $value = $valueKey instanceof \Closure ? $valueKey($item, $key) : (float) data_get($item, $valueKey);
            $previousValue = null;

            if ($previousValueKey !== null) {
                $previousValue = $previousValueKey instanceof \Closure
                    ? $previousValueKey($item, $key)
                    : (float) data_get($item, $previousValueKey);
            }

            return new BreakdownItemData(
                label: (string) $label,
                value: (float) $value,
                previousValue: $previousValue,
            );
        })->values()->all();

        return new self(items: $mapped, description: $description);
    }

    /**
     * @return array{items: array<int, array{label: string, value: float, previousValue: ?float, color: ?string, icon: ?string, url: ?string}>, description: ?string}
     */
    public function toArray(): array
    {
        return [
            'items' => array_map(fn (BreakdownItemData $item) => $item->toArray(), $this->items),
            'description' => $this->description,
        ];
    }

    /**
     * @param  array{items: array<int, array{label: string, value: float, previousValue: ?float}>, description: ?string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            items: array_map(fn (array $item) => BreakdownItemData::fromArray($item), $data['items']),
            description: $data['description'] ?? null,
        );
    }
}
