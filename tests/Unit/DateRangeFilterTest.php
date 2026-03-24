<?php

use LaravelDaily\FilaWidgets\Support\DateRangeFilter;

it('resolves last 7 days filter', function () {
    $filter = DateRangeFilter::fromFilter('last_7_days');

    expect($filter->value())->toBe('last_7_days')
        ->and($filter->days())->toBe(7)
        ->and($filter->label())->toBe('Last 7 days')
        ->and($filter->shortLabel())->toBe('7D');
});

it('resolves last 30 days filter', function () {
    $filter = DateRangeFilter::fromFilter('last_30_days');

    expect($filter->value())->toBe('last_30_days')
        ->and($filter->days())->toBe(30)
        ->and($filter->label())->toBe('Last 30 days')
        ->and($filter->shortLabel())->toBe('30D');
});

it('resolves last 60 days filter', function () {
    $filter = DateRangeFilter::fromFilter('last_60_days');

    expect($filter->value())->toBe('last_60_days')
        ->and($filter->days())->toBe(60)
        ->and($filter->label())->toBe('Last 60 days')
        ->and($filter->shortLabel())->toBe('60D');
});

it('defaults to 30 days for null filter', function () {
    $filter = DateRangeFilter::fromFilter(null);

    expect($filter->value())->toBe('last_30_days')
        ->and($filter->days())->toBe(30);
});

it('defaults to 30 days for unknown filter value', function () {
    $filter = DateRangeFilter::fromFilter('invalid');

    expect($filter->value())->toBe('last_30_days')
        ->and($filter->days())->toBe(30);
});

it('returns correct current period date range', function () {
    $filter = DateRangeFilter::fromFilter('last_7_days');
    [$start, $end] = $filter->currentPeriod();

    expect((int) $start->diffInDays($end))->toBe(6);
});
