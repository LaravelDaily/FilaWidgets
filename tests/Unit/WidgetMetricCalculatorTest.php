<?php

use LaravelDaily\FilaWidgets\Support\WidgetMetricCalculator;

it('calculates comparison with upward trend', function () {
    $result = WidgetMetricCalculator::comparison(150, 100);

    expect($result['difference'])->toBe(50.0)
        ->and($result['percentageChange'])->toBe(50.0)
        ->and($result['trend'])->toBe('up');
});

it('calculates comparison with downward trend', function () {
    $result = WidgetMetricCalculator::comparison(80, 100);

    expect($result['difference'])->toBe(-20.0)
        ->and($result['percentageChange'])->toBe(-20.0)
        ->and($result['trend'])->toBe('down');
});

it('calculates comparison with neutral trend', function () {
    $result = WidgetMetricCalculator::comparison(100, 100);

    expect($result['difference'])->toBe(0.0)
        ->and($result['percentageChange'])->toBe(0.0)
        ->and($result['trend'])->toBe('neutral');
});

it('returns null percentage change when comparison value is zero and current is nonzero', function () {
    $result = WidgetMetricCalculator::comparison(100, 0);

    expect($result['percentageChange'])->toBeNull()
        ->and($result['trend'])->toBe('up');
});

it('returns zero percentage change when both values are zero', function () {
    $result = WidgetMetricCalculator::comparison(0, 0);

    expect($result['percentageChange'])->toBe(0.0)
        ->and($result['trend'])->toBe('neutral');
});

it('calculates progress with success status above 100%', function () {
    $result = WidgetMetricCalculator::progress(120, 100);

    expect($result['percentage'])->toBe(120.0)
        ->and($result['barPercentage'])->toBe(100.0)
        ->and($result['status'])->toBe('success');
});

it('calculates progress with normal status at 50% or above', function () {
    $result = WidgetMetricCalculator::progress(60, 100);

    expect($result['percentage'])->toBe(60.0)
        ->and($result['barPercentage'])->toBe(60.0)
        ->and($result['status'])->toBe('normal');
});

it('calculates progress with warning status below 50%', function () {
    $result = WidgetMetricCalculator::progress(30, 100);

    expect($result['percentage'])->toBe(30.0)
        ->and($result['barPercentage'])->toBe(30.0)
        ->and($result['status'])->toBe('warning');
});

it('handles zero goal in progress calculation', function () {
    $result = WidgetMetricCalculator::progress(100, 0);

    expect($result['percentage'])->toBe(0.0)
        ->and($result['barPercentage'])->toBe(0.0)
        ->and($result['status'])->toBe('warning');
});

it('calculates breakdown with contribution percentages', function () {
    $items = [
        ['label' => 'A', 'value' => 60],
        ['label' => 'B', 'value' => 40],
    ];

    $result = WidgetMetricCalculator::breakdown($items);

    expect($result)->toHaveCount(2)
        ->and($result[0]['label'])->toBe('A')
        ->and($result[0]['contributionPercentage'])->toBe(60.0)
        ->and($result[1]['contributionPercentage'])->toBe(40.0);
});

it('sorts breakdown items by value descending by default', function () {
    $items = [
        ['label' => 'Small', 'value' => 10],
        ['label' => 'Large', 'value' => 100],
        ['label' => 'Medium', 'value' => 50],
    ];

    $result = WidgetMetricCalculator::breakdown($items);

    expect($result[0]['label'])->toBe('Large')
        ->and($result[1]['label'])->toBe('Medium')
        ->and($result[2]['label'])->toBe('Small');
});

it('limits breakdown items and groups overflow into Other', function () {
    $items = [
        ['label' => 'A', 'value' => 50],
        ['label' => 'B', 'value' => 30],
        ['label' => 'C', 'value' => 20],
        ['label' => 'D', 'value' => 10],
    ];

    $result = WidgetMetricCalculator::breakdown($items, limit: 3, groupOther: true);

    expect($result)->toHaveCount(3)
        ->and($result[2]['label'])->toBe('Other')
        ->and($result[2]['value'])->toBe(30.0);
});

it('calculates delta percentages in breakdown when previous values are provided', function () {
    $items = [
        ['label' => 'A', 'value' => 120, 'previousValue' => 100],
    ];

    $result = WidgetMetricCalculator::breakdown($items);

    expect($result[0]['deltaPercentage'])->toBe(20.0);
});

it('returns null delta when no previous value is provided', function () {
    $items = [
        ['label' => 'A', 'value' => 100],
    ];

    $result = WidgetMetricCalculator::breakdown($items);

    expect($result[0]['deltaPercentage'])->toBeNull();
});
