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
                            {{ $hasEntries ? 'Live' : 'Awaiting Data' }}
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

                <div class="flex items-start gap-3">
                    <p class="text-lg font-semibold tracking-tight text-gray-950 dark:text-white">
                        {{ $total }}
                    </p>

                    @if (filled($actionLabel) && filled($actionUrl))
                        <x-filament::link
                            :href="$actionUrl"
                            :target="$actionNewTab ? '_blank' : null"
                            icon="heroicon-m-arrow-top-right-on-square"
                            icon-position="after"
                            @class([
                                'shrink-0 pt-0.5 text-sm font-medium',
                                $colorClasses['link'],
                            ])
                        >
                            {{ $actionLabel }}
                        </x-filament::link>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                @if (! $hasEntries)
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
                    <div class="flex text-xs text-gray-400 dark:text-gray-500" style="padding-left: 32px;">
                        @foreach ($monthLabels as $month)
                            <span
                                class="shrink-0"
                                style="position: relative; left: {{ $month['offset'] * 14 }}px; width: 0; white-space: nowrap;"
                            >
                                {{ $month['label'] }}
                            </span>
                        @endforeach
                    </div>

                    <div class="mt-1 flex gap-0.5">
                        <div class="flex shrink-0 flex-col gap-0.5 pr-1.5 text-[10px] leading-none text-gray-400 dark:text-gray-500">
                            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $index => $dayName)
                                <div class="flex h-[12px] items-center">
                                    @if ($index % 2 === 0)
                                        {{ $dayName }}
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @foreach ($grid[0] ?? [] as $colIndex => $cell)
                            <div class="flex flex-col gap-0.5">
                                @foreach ($grid as $rowIndex => $row)
                                    @if (isset($row[$colIndex]))
                                        @php
                                            $cell = $row[$colIndex];
                                        @endphp
                                        <{{ filled($cell['actionUrl']) ? 'a' : 'div' }}
                                            @if (filled($cell['actionUrl']))
                                                href="{{ $cell['actionUrl'] }}"
                                                target="{{ $cell['actionNewTab'] ? '_blank' : '_self' }}"
                                                rel="{{ $cell['actionNewTab'] ? 'noreferrer noopener' : null }}"
                                            @endif
                                            class="block h-[12px] w-[12px] rounded-[2px] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500/40 {{ match($colorScheme) {
                                                'green' => match($cell['intensity']) {
                                                    0 => 'bg-gray-100 dark:bg-white/[0.06]',
                                                    1 => 'bg-emerald-200 dark:bg-emerald-900/60',
                                                    2 => 'bg-emerald-400 dark:bg-emerald-700/80',
                                                    3 => 'bg-emerald-500 dark:bg-emerald-500',
                                                    default => 'bg-emerald-700 dark:bg-emerald-400',
                                                },
                                                'blue' => match($cell['intensity']) {
                                                    0 => 'bg-gray-100 dark:bg-white/[0.06]',
                                                    1 => 'bg-blue-200 dark:bg-blue-900/60',
                                                    2 => 'bg-blue-400 dark:bg-blue-700/80',
                                                    3 => 'bg-blue-500 dark:bg-blue-500',
                                                    default => 'bg-blue-700 dark:bg-blue-400',
                                                },
                                                default => match($cell['intensity']) {
                                                    0 => 'bg-gray-100 dark:bg-white/[0.06]',
                                                    1 => 'bg-primary-200 dark:bg-primary-900/60',
                                                    2 => 'bg-primary-400 dark:bg-primary-700/80',
                                                    3 => 'bg-primary-500 dark:bg-primary-500',
                                                    default => 'bg-primary-700 dark:bg-primary-400',
                                                },
                                            } }}"
                                            title="{{ $cell['dayLabel'] }}: {{ $cell['formattedValue'] }}"
                                        ></{{ filled($cell['actionUrl']) ? 'a' : 'div' }}>
                                    @else
                                        <div class="h-[12px] w-[12px]"></div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-2 flex items-center justify-end gap-1.5 text-[10px] text-gray-400 dark:text-gray-500">
                        <span>Less</span>
                        @for ($i = 0; $i <= 4; $i++)
                            <div class="h-[10px] w-[10px] rounded-[2px] {{ match($colorScheme) {
                                'green' => match($i) {
                                    0 => 'bg-gray-100 dark:bg-white/[0.06]',
                                    1 => 'bg-emerald-200 dark:bg-emerald-900/60',
                                    2 => 'bg-emerald-400 dark:bg-emerald-700/80',
                                    3 => 'bg-emerald-500 dark:bg-emerald-500',
                                    default => 'bg-emerald-700 dark:bg-emerald-400',
                                },
                                'blue' => match($i) {
                                    0 => 'bg-gray-100 dark:bg-white/[0.06]',
                                    1 => 'bg-blue-200 dark:bg-blue-900/60',
                                    2 => 'bg-blue-400 dark:bg-blue-700/80',
                                    3 => 'bg-blue-500 dark:bg-blue-500',
                                    default => 'bg-blue-700 dark:bg-blue-400',
                                },
                                default => match($i) {
                                    0 => 'bg-gray-100 dark:bg-white/[0.06]',
                                    1 => 'bg-primary-200 dark:bg-primary-900/60',
                                    2 => 'bg-primary-400 dark:bg-primary-700/80',
                                    3 => 'bg-primary-500 dark:bg-primary-500',
                                    default => 'bg-primary-700 dark:bg-primary-400',
                                },
                            } }}"></div>
                        @endfor
                        <span>More</span>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
