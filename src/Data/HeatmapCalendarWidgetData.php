<?php

namespace LaravelDaily\FilaWidgets\Data;

readonly class HeatmapCalendarWidgetData
{
    /**
     * @param  array<string, float>  $entries  Date (Y-m-d) => value
     */
    public function __construct(
        public array $entries,
        public ?string $description = null,
        public array $entryUrls = [],
        public bool $openEntryUrlsInNewTab = false,
    ) {}

    /**
     * @return array{entries: array<string, float>, description: ?string, entryUrls: array<string, string>, openEntryUrlsInNewTab: bool}
     */
    public function toArray(): array
    {
        return [
            'entries' => $this->entries,
            'description' => $this->description,
            'entryUrls' => $this->entryUrls,
            'openEntryUrlsInNewTab' => $this->openEntryUrlsInNewTab,
        ];
    }

    /**
     * @param  array{entries: array<string, float>, description: ?string, entryUrls: array<string, string>, openEntryUrlsInNewTab: bool}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            entries: $data['entries'] ?? [],
            description: $data['description'] ?? null,
            entryUrls: $data['entryUrls'] ?? [],
            openEntryUrlsInNewTab: $data['openEntryUrlsInNewTab'] ?? false,
        );
    }
}
