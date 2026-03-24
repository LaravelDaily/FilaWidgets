<?php

use LaravelDaily\FilaWidgets\Support\WidgetValueFormatter;

it('formats values as currency', function () {
    $result = WidgetValueFormatter::format(1234.56, 'currency', 'USD', 2);

    expect($result)->toContain('1,234.56');
});

it('formats values as plain numbers', function () {
    $result = WidgetValueFormatter::format(1234.5, 'number', 'USD', 1);

    expect($result)->toBe('1,234.5');
});

it('formats values as percentages', function () {
    $result = WidgetValueFormatter::format(85.67, 'percentage', 'USD', 2);

    expect($result)->toBe('85.67%');
});

it('formats positive signed percentages with plus prefix', function () {
    $result = WidgetValueFormatter::formatSignedPercentage(24.8);

    expect($result)->toBe('+24.8%');
});

it('formats negative signed percentages with minus prefix', function () {
    $result = WidgetValueFormatter::formatSignedPercentage(-12.5);

    expect($result)->toBe('-12.5%');
});

it('formats zero signed percentage without prefix', function () {
    $result = WidgetValueFormatter::formatSignedPercentage(0);

    expect($result)->toBe('0%');
});

it('respects precision in signed percentages', function () {
    $result = WidgetValueFormatter::formatSignedPercentage(33.333, precision: 2);

    expect($result)->toBe('+33.33%');
});
