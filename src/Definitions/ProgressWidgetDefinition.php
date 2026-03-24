<?php

namespace LaravelDaily\FilaWidgets\Definitions;

/**
 * @internal Legacy compatibility layer for definition-array based progress widgets.
 */
class ProgressWidgetDefinition extends WidgetDefinition
{
    protected bool $showProjection = true;

    public function showProjection(bool $showProjection = true): static
    {
        $this->showProjection = $showProjection;

        return $this;
    }

    public function shouldShowProjection(): bool
    {
        return $this->showProjection;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        $definition = static::fillBase(new static, $data);
        $definition->showProjection = $data['showProjection'] ?? true;

        return $definition;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...$this->baseArray(),
            'showProjection' => $this->showProjection,
        ];
    }
}
