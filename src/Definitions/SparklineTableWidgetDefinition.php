<?php

namespace LaravelDaily\FilaWidgets\Definitions;

/**
 * @internal Legacy compatibility layer for definition-array based sparkline widgets.
 */
class SparklineTableWidgetDefinition extends WidgetDefinition
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        return static::fillBase(new static, $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->baseArray();
    }
}
