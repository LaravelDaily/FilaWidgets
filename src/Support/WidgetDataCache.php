<?php

namespace LaravelDaily\FilaWidgets\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

class WidgetDataCache
{
    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, mixed>  $options
     */
    public static function remember(
        string $widget,
        string $resolver,
        array $filters,
        array $options,
        ?int $ttl,
        ?string $key,
        Closure $callback,
    ): mixed {
        if (($ttl === null) || ($ttl <= 0)) {
            return $callback();
        }

        $cacheKey = static::key($widget, $resolver, $filters, $options, $key);

        $cached = Cache::remember(
            $cacheKey,
            now()->addSeconds($ttl),
            fn () => static::toStorable($callback()),
        );

        return static::fromStorable($cached);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, mixed>  $options
     */
    public static function key(
        string $widget,
        string $resolver,
        array $filters,
        array $options,
        ?string $key = null,
    ): string {
        $prefix = $key ?? implode(':', [$widget, class_basename($resolver)]);

        return 'dashboard_widgets:'.$prefix.':'.sha1(json_encode([
            'filters' => $filters,
            'options' => $options,
            'resolver' => $resolver,
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * Convert a widget data object to a cache-safe array to avoid PHP serialization issues with readonly classes.
     *
     * @return array{__class: class-string, __data: array<string, mixed>}
     */
    protected static function toStorable(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            return [
                '__class' => $value::class,
                '__data' => $value->toArray(),
            ];
        }

        return ['__raw' => $value];
    }

    protected static function fromStorable(mixed $cached): mixed
    {
        if (! is_array($cached)) {
            return $cached;
        }

        if (isset($cached['__class'], $cached['__data']) && method_exists($cached['__class'], 'fromArray')) {
            return $cached['__class']::fromArray($cached['__data']);
        }

        if (array_key_exists('__raw', $cached)) {
            return $cached['__raw'];
        }

        return $cached;
    }
}
