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

                    <p class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">
                        {{ $formattedCurrentValue }}
                        <span class="text-base font-medium text-gray-500 dark:text-gray-400">
                            / {{ $formattedGoalValue }}
                        </span>
                    </p>

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

            <div class="space-y-1.5">
                <div class="h-2.5 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                    <div
                        class="h-full rounded-full transition-all {{ $barClasses }}"
                        style="width: {{ $barPercentage }}%;"
                    ></div>
                </div>

                <p class="text-xs font-medium {{ $statusClasses }}">
                    {{ $percentageLabel }}
                </p>
            </div>

            @if (! $hasGoalValue)
                <div class="rounded-xl border border-dashed border-gray-200 px-4 py-4 dark:border-white/10">
                    <p class="text-sm font-medium text-gray-950 dark:text-white">{{ $emptyStateHeading }}</p>
                    @if (filled($emptyStateDescription))
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $emptyStateDescription }}</p>
                    @endif
                </div>
            @elseif (! $hasCurrentValue)
                <div class="rounded-xl border border-dashed border-gray-200 px-4 py-4 dark:border-white/10">
                    <p class="text-sm font-medium text-gray-950 dark:text-white">{{ $emptyStateHeading }}</p>
                    @if (filled($emptyStateDescription))
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $emptyStateDescription }}</p>
                    @endif
                </div>
            @endif

            @if ($formattedProjectionValue !== null)
                <div class="rounded-xl border border-dashed border-primary-200/70 bg-primary-50/70 px-3 py-2 text-xs text-primary-900 dark:border-primary-500/20 dark:bg-primary-500/10 dark:text-primary-100">
                    {{ $projectionLabel }}: <span class="font-semibold">{{ $formattedProjectionValue }}</span>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
