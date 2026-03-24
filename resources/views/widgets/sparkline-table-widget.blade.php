<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $colorClasses = match ($color) {
                'success' => [
                    'badge' => 'border-success-200/70 bg-success-50 text-success-700 dark:border-success-500/20 dark:bg-success-500/10 dark:text-success-300',
                    'link' => 'text-success-600 hover:text-success-500 dark:text-success-400 dark:hover:text-success-300',
                ],
                'warning' => [
                    'badge' => 'border-warning-200/70 bg-warning-50 text-warning-700 dark:border-warning-500/20 dark:bg-warning-500/10 dark:text-warning-300',
                    'link' => 'text-warning-600 hover:text-warning-500 dark:text-warning-400 dark:hover:text-warning-300',
                ],
                'danger' => [
                    'badge' => 'border-danger-200/70 bg-danger-50 text-danger-700 dark:border-danger-500/20 dark:bg-danger-500/10 dark:text-danger-300',
                    'link' => 'text-danger-600 hover:text-danger-500 dark:text-danger-400 dark:hover:text-danger-300',
                ],
                default => [
                    'badge' => 'border-primary-200/70 bg-primary-50 text-primary-700 dark:border-primary-500/20 dark:bg-primary-500/10 dark:text-primary-300',
                    'link' => 'text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300',
                ],
            };

            $rowTrendColors = [
                'success' => [
                    'up' => 'stroke-success-500 dark:stroke-success-400',
                    'down' => 'stroke-success-300 dark:stroke-success-600',
                    'neutral' => 'stroke-success-400 dark:stroke-success-500',
                    'badge_up' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                    'badge_down' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                    'badge_neutral' => 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400',
                ],
                'warning' => [
                    'up' => 'stroke-warning-500 dark:stroke-warning-400',
                    'down' => 'stroke-warning-300 dark:stroke-warning-600',
                    'neutral' => 'stroke-warning-400 dark:stroke-warning-500',
                    'badge_up' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
                    'badge_down' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
                    'badge_neutral' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400',
                ],
                'danger' => [
                    'up' => 'stroke-danger-500 dark:stroke-danger-400',
                    'down' => 'stroke-danger-300 dark:stroke-danger-600',
                    'neutral' => 'stroke-danger-400 dark:stroke-danger-500',
                    'badge_up' => 'bg-danger-50 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400',
                    'badge_down' => 'bg-danger-50 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400',
                    'badge_neutral' => 'bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-400',
                ],
            ];
        @endphp
        <div class="space-y-4">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        @if (filled($icon))
                            <x-filament::icon :icon="$icon" class="h-4 w-4 text-gray-400 dark:text-gray-500" />
                        @endif

                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $label }}
                        </p>

                        <span class="rounded-full border px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider {{ $colorClasses['badge'] }}">
                            {{ $rangeLabel }}
                        </span>
                    </div>

                    @if (filled($description))
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $description }}
                        </p>
                    @endif
                </div>

                @if (filled($actionLabel) && filled($actionUrl))
                    <x-filament::link
                        :href="$actionUrl"
                        :target="$actionNewTab ? '_blank' : null"
                        icon="heroicon-m-arrow-top-right-on-square"
                        icon-position="after"
                        @class([
                            'shrink-0 text-sm font-medium',
                            $colorClasses['link'],
                        ])
                    >
                        {{ $actionLabel }}
                    </x-filament::link>
                @endif
            </div>

            @if ($rows === [])
                <div class="rounded-xl border border-dashed border-gray-200 px-4 py-5 dark:border-white/10">
                    <p class="text-sm font-medium text-gray-950 dark:text-white">
                        {{ $emptyStateHeading }}
                    </p>

                    @if (filled($emptyStateDescription))
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ $emptyStateDescription }}
                        </p>
                    @endif
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach ($rows as $row)
                        @php
                            $rowColor = $row['color'] ?? null;
                            $hasCustomColor = filled($rowColor) && isset($rowTrendColors[$rowColor]);
                        @endphp
                        <{{ filled($row['actionUrl']) ? 'a' : 'div' }}
                            @if (filled($row['actionUrl']))
                                href="{{ $row['actionUrl'] }}"
                                target="{{ $row['actionNewTab'] ? '_blank' : '_self' }}"
                                rel="{{ $row['actionNewTab'] ? 'noreferrer noopener' : null }}"
                            @endif
                            @class([
                                'flex items-center gap-3 py-2.5 first:pt-0 last:pb-0',
                                'rounded-lg transition hover:bg-gray-50/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500/40 dark:hover:bg-white/5' => filled($row['actionUrl']),
                            ])
                        >
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-950 dark:text-white">
                                    {{ $row['label'] }}
                                </p>
                            </div>

                            @if ($row['showSparkline'] && count($row['sparkline']) > 1)
                                <div class="hidden w-24 shrink-0 sm:block">
                                    @php
                                        $points = $row['sparkline'];
                                        $count = count($points);
                                        $max = max($points) ?: 1;
                                        $min = min($points);
                                        $range = ($max - $min) ?: 1;
                                        $width = 96;
                                        $height = 28;
                                        $padding = 2;

                                        $coords = [];
                                        foreach ($points as $i => $point) {
                                            $x = $count > 1
                                                ? $padding + ($i / ($count - 1)) * ($width - 2 * $padding)
                                                : $width / 2;
                                            $y = $padding + (1 - (($point - $min) / $range)) * ($height - 2 * $padding);
                                            $coords[] = round($x, 1) . ',' . round($y, 1);
                                        }
                                        $polyline = implode(' ', $coords);

                                        if ($hasCustomColor) {
                                            $trendColor = $rowTrendColors[$rowColor][$row['trend']] ?? $rowTrendColors[$rowColor]['neutral'];
                                        } else {
                                            $trendColor = match($row['trend']) {
                                                'up' => 'stroke-emerald-500 dark:stroke-emerald-400',
                                                'down' => 'stroke-rose-500 dark:stroke-rose-400',
                                                default => 'stroke-gray-400 dark:stroke-gray-500',
                                            };
                                        }
                                    @endphp
                                    <svg
                                        viewBox="0 0 {{ $width }} {{ $height }}"
                                        class="h-7 w-full {{ $trendColor }}"
                                        fill="none"
                                        preserveAspectRatio="none"
                                    >
                                        <polyline
                                            points="{{ $polyline }}"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>
                            @endif

                            <div class="flex shrink-0 items-center gap-3 text-right">
                                <p class="text-sm font-semibold text-gray-950 dark:text-white">
                                    {{ $row['formattedValue'] }}
                                </p>

                                @if ($row['change'] !== null)
                                    @php
                                        if ($hasCustomColor) {
                                            $badgeClass = $rowTrendColors[$rowColor]['badge_' . $row['trend']] ?? $rowTrendColors[$rowColor]['badge_neutral'];
                                        } else {
                                            $badgeClass = match ($row['trend']) {
                                                'up' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                'down' => 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400',
                                                default => 'bg-gray-100 text-gray-600 dark:bg-white/5 dark:text-gray-400',
                                            };
                                        }
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                        {{ $row['change'] }}
                                    </span>
                                @endif
                            </div>
                        </{{ filled($row['actionUrl']) ? 'a' : 'div' }}>
                    @endforeach
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
