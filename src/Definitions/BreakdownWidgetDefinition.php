<?php

namespace LaravelDaily\FilaWidgets\Definitions;

/**
 * @internal Legacy compatibility layer for definition-array based breakdown widgets.
 */
class BreakdownWidgetDefinition extends WidgetDefinition
{
    protected ?int $limit = null;

    protected bool $groupOther = false;

    protected string $sortBy = 'value';

    protected string $sortDirection = 'desc';

    protected bool $showContribution = true;

    protected bool $showDelta = true;

    /**
     * @var array<int, array{threshold: float, color: string}>
     */
    protected array $deltaThresholds = [];

    public function groupOther(bool $groupOther = true): static
    {
        $this->groupOther = $groupOther;

        return $this;
    }

    public function groupingOverflowIntoOther(): bool
    {
        return $this->groupOther;
    }

    public function limit(?int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function limitValue(): ?int
    {
        return $this->limit;
    }

    public function sortBy(string $sortBy): static
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    public function sortByValue(): string
    {
        return $this->sortBy;
    }

    public function sortDirection(string $direction): static
    {
        $this->sortDirection = $direction;

        return $this;
    }

    public function sortDirectionValue(): string
    {
        return $this->sortDirection;
    }

    public function showContribution(bool $show = true): static
    {
        $this->showContribution = $show;

        return $this;
    }

    public function showingContribution(): bool
    {
        return $this->showContribution;
    }

    public function showDelta(bool $show = true): static
    {
        $this->showDelta = $show;

        return $this;
    }

    public function showingDelta(): bool
    {
        return $this->showDelta;
    }

    /**
     * @param  array<int, array{threshold: float, color: string}>  $thresholds
     */
    public function deltaThresholds(array $thresholds): static
    {
        $this->deltaThresholds = $thresholds;

        return $this;
    }

    /**
     * @return array<int, array{threshold: float, color: string}>
     */
    public function deltaThresholdsValue(): array
    {
        return $this->deltaThresholds;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        $definition = static::fillBase(new static, $data);
        $definition->limit = $data['limit'] ?? null;
        $definition->groupOther = $data['groupOther'] ?? false;
        $definition->sortBy = $data['sortBy'] ?? 'value';
        $definition->sortDirection = $data['sortDirection'] ?? 'desc';
        $definition->showContribution = $data['showContribution'] ?? true;
        $definition->showDelta = $data['showDelta'] ?? true;
        $definition->deltaThresholds = $data['deltaThresholds'] ?? [];

        return $definition;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...$this->baseArray(),
            'deltaThresholds' => $this->deltaThresholds,
            'groupOther' => $this->groupOther,
            'limit' => $this->limit,
            'showContribution' => $this->showContribution,
            'showDelta' => $this->showDelta,
            'sortBy' => $this->sortBy,
            'sortDirection' => $this->sortDirection,
        ];
    }
}
