<?php

use LaravelDaily\FilaWidgets\Data\BreakdownItemData;
use LaravelDaily\FilaWidgets\Data\BreakdownWidgetData;
use LaravelDaily\FilaWidgets\Data\CompletionRateWidgetData;
use LaravelDaily\FilaWidgets\Data\HeatmapCalendarWidgetData;
use LaravelDaily\FilaWidgets\Data\ProgressWidgetData;
use LaravelDaily\FilaWidgets\Data\SparklineTableRowData;
use LaravelDaily\FilaWidgets\Data\SparklineTableWidgetData;

it('roundtrips ProgressWidgetData through array serialization', function () {
    $original = new ProgressWidgetData(
        currentValue: 1500.50,
        goalValue: 5000.00,
        projectionValue: 3200.75,
        description: 'On track',
        projectionLabel: '30-day pace',
    );

    $restored = ProgressWidgetData::fromArray($original->toArray());

    expect($restored->currentValue)->toBe(1500.50)
        ->and($restored->goalValue)->toBe(5000.00)
        ->and($restored->projectionValue)->toBe(3200.75)
        ->and($restored->description)->toBe('On track')
        ->and($restored->projectionLabel)->toBe('30-day pace');
});

it('roundtrips ProgressWidgetData with nullable fields', function () {
    $original = new ProgressWidgetData(currentValue: 100, goalValue: 200);

    $restored = ProgressWidgetData::fromArray($original->toArray());

    expect($restored->projectionValue)->toBeNull()
        ->and($restored->description)->toBeNull()
        ->and($restored->projectionLabel)->toBeNull();
});

it('roundtrips CompletionRateWidgetData through array serialization', function () {
    $original = new CompletionRateWidgetData(
        value: 71.2,
        min: 0,
        max: 100,
        description: '307 of 431 fulfilled',
        isEmpty: false,
    );

    $restored = CompletionRateWidgetData::fromArray($original->toArray());

    expect($restored->value)->toBe(71.2)
        ->and($restored->min)->toBe(0.0)
        ->and($restored->max)->toBe(100.0)
        ->and($restored->description)->toBe('307 of 431 fulfilled')
        ->and($restored->isEmpty)->toBeFalse();
});

it('roundtrips HeatmapCalendarWidgetData through array serialization', function () {
    $original = new HeatmapCalendarWidgetData(
        entries: ['2026-03-20' => 500.0, '2026-03-21' => 750.0],
        description: 'Daily revenue',
        entryUrls: ['2026-03-20' => '/orders?date=2026-03-20'],
        openEntryUrlsInNewTab: true,
    );

    $restored = HeatmapCalendarWidgetData::fromArray($original->toArray());

    expect($restored->entries)->toBe(['2026-03-20' => 500.0, '2026-03-21' => 750.0])
        ->and($restored->description)->toBe('Daily revenue')
        ->and($restored->entryUrls)->toBe(['2026-03-20' => '/orders?date=2026-03-20'])
        ->and($restored->openEntryUrlsInNewTab)->toBeTrue();
});

it('roundtrips SparklineTableWidgetData through array serialization', function () {
    $original = SparklineTableWidgetData::fromRows(
        new SparklineTableRowData(
            label: 'Revenue',
            value: 79702.64,
            previousValue: 63836.95,
            sparkline: [100.0, 200.0, 150.0],
            format: 'currency',
        ),
        new SparklineTableRowData(
            label: 'Orders',
            value: 307,
            previousValue: 268,
            sparkline: [10.0, 12.0, 8.0],
            format: 'number',
            precision: 0,
        ),
    );

    $restored = SparklineTableWidgetData::fromArray($original->toArray());

    expect($restored->rows)->toHaveCount(2)
        ->and($restored->rows[0]->label)->toBe('Revenue')
        ->and($restored->rows[0]->value)->toBe(79702.64)
        ->and($restored->rows[0]->previousValue)->toBe(63836.95)
        ->and($restored->rows[0]->sparkline)->toBe([100.0, 200.0, 150.0])
        ->and($restored->rows[0]->format)->toBe('currency')
        ->and($restored->rows[1]->label)->toBe('Orders')
        ->and($restored->rows[1]->precision)->toBe(0);
});

it('roundtrips BreakdownWidgetData through array serialization', function () {
    $original = new BreakdownWidgetData(
        items: [
            new BreakdownItemData('US', 27000, previousValue: 23000, color: 'success', icon: 'heroicon-o-flag', url: '/us'),
            new BreakdownItemData('DE', 17000, previousValue: 13000),
        ],
        description: 'Country mix',
    );

    $restored = BreakdownWidgetData::fromArray($original->toArray());

    expect($restored->items)->toHaveCount(2)
        ->and($restored->description)->toBe('Country mix')
        ->and($restored->items[0]->label)->toBe('US')
        ->and($restored->items[0]->value)->toBe(27000.0)
        ->and($restored->items[0]->previousValue)->toBe(23000.0)
        ->and($restored->items[0]->color)->toBe('success')
        ->and($restored->items[0]->icon)->toBe('heroicon-o-flag')
        ->and($restored->items[0]->url)->toBe('/us')
        ->and($restored->items[1]->color)->toBeNull();
});

it('builds BreakdownWidgetData from collection with string keys', function () {
    $data = BreakdownWidgetData::fromCollection(
        items: [
            ['name' => 'Electronics', 'total' => 45000, 'prev' => 38000],
            ['name' => 'Clothing', 'total' => 32000, 'prev' => 35000],
        ],
        labelKey: 'name',
        valueKey: 'total',
        previousValueKey: 'prev',
        description: 'By category',
    );

    expect($data->items)->toHaveCount(2)
        ->and($data->description)->toBe('By category')
        ->and($data->items[0]->label)->toBe('Electronics')
        ->and($data->items[0]->value)->toBe(45000.0)
        ->and($data->items[0]->previousValue)->toBe(38000.0)
        ->and($data->items[1]->label)->toBe('Clothing');
});

it('builds BreakdownWidgetData from collection with closure keys', function () {
    $data = BreakdownWidgetData::fromCollection(
        items: collect(['US' => 15000, 'DE' => 8000]),
        labelKey: fn ($value, $key) => $key,
        valueKey: fn ($value) => (float) $value,
    );

    expect($data->items)->toHaveCount(2)
        ->and($data->items[0]->label)->toBe('US')
        ->and($data->items[0]->value)->toBe(15000.0)
        ->and($data->items[1]->label)->toBe('DE')
        ->and($data->items[1]->value)->toBe(8000.0)
        ->and($data->items[0]->previousValue)->toBeNull();
});

it('builds BreakdownItemData with fluent builder methods', function () {
    $item = (new BreakdownItemData('Test', 100))
        ->withColor('success')
        ->withIcon('heroicon-o-star')
        ->withUrl('/test')
        ->withPreviousValue(80);

    expect($item->label)->toBe('Test')
        ->and($item->value)->toBe(100.0)
        ->and($item->color)->toBe('success')
        ->and($item->icon)->toBe('heroicon-o-star')
        ->and($item->url)->toBe('/test')
        ->and($item->previousValue)->toBe(80.0);
});
