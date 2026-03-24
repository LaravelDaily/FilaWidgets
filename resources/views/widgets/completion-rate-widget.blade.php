<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $headerColors = match ($color) {
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

            $colorMap = [
                'success' => ['stroke' => '#22c55e', 'text' => 'text-success-600 dark:text-success-400'],
                'warning' => ['stroke' => '#f59e0b', 'text' => 'text-warning-600 dark:text-warning-400'],
                'danger' => ['stroke' => '#ef4444', 'text' => 'text-danger-600 dark:text-danger-400'],
                'gray' => ['stroke' => '#94a3b8', 'text' => 'text-gray-500 dark:text-gray-400'],
                'primary' => ['stroke' => '#f59e0b', 'text' => 'text-primary-600 dark:text-primary-400'],
            ];
            $colors = $colorMap[$statusColor] ?? $colorMap['primary'];

            $radius = 50;
            $strokeWidth = 8;
            $circumference = $radius * M_PI;
            $arcLength = ($arcPercentage / 100) * $circumference;
            $cx = 70;
            $cy = 62;
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

                        <span class="rounded-full border px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider {{ $headerColors['badge'] }}">
                            {{ $rangeLabel }}
                        </span>
                    </div>

                    @if (filled($description))
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $description }}
                        </p>
                    @endif

                    @if (filled($helpText))
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            {{ $helpText }}
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
                            $headerColors['link'],
                        ])
                    >
                        {{ $actionLabel }}
                    </x-filament::link>
                @endif
            </div>

            <div class="flex flex-col items-center gap-2">
                <div class="relative">
                    <svg width="140" height="72" viewBox="0 0 140 72" class="overflow-visible">
                        <path
                            d="M {{ $cx - $radius }} {{ $cy }} A {{ $radius }} {{ $radius }} 0 0 1 {{ $cx + $radius }} {{ $cy }}"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="{{ $strokeWidth }}"
                            stroke-linecap="round"
                            class="text-gray-100 dark:text-white/10"
                        />

                        @if ($arcPercentage > 0)
                            <path
                                d="M {{ $cx - $radius }} {{ $cy }} A {{ $radius }} {{ $radius }} 0 0 1 {{ $cx + $radius }} {{ $cy }}"
                                fill="none"
                                stroke="{{ $colors['stroke'] }}"
                                stroke-width="{{ $strokeWidth }}"
                                stroke-linecap="round"
                                stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $circumference - $arcLength }}"
                            />
                        @endif
                    </svg>

                    <div class="absolute inset-x-0 bottom-0 text-center">
                        @if ($isEmpty)
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $emptyStateHeading }}
                            </p>
                        @else
                            <p class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                                {{ $formattedValue }}<span class="text-xs font-medium text-gray-400">{{ $unit }}</span>
                            </p>
                        @endif
                    </div>
                </div>

                @if (filled($statusLabel))
                    <p class="text-center text-xs font-medium {{ $colors['text'] }}">
                        {{ $statusLabel }}
                    </p>
                @endif

                @if ($isEmpty && filled($emptyStateDescription))
                    <p class="max-w-[18rem] text-center text-xs text-gray-400 dark:text-gray-500">
                        {{ $emptyStateDescription }}
                    </p>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
