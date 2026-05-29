@php
    /** @var array<string, list<array{name: string, impressions: int, hovers: int, clicks: int}>> $groups */
    $groups = $this->getGroupedEvents();
@endphp

<x-filament-widgets::widget>
    <x-filament::section :heading="__('Pan events')">
        @if (count($groups) === 0)
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('No events tracked yet.') }}
            </p>
        @else
            <div class="space-y-6">
                @foreach ($groups as $label => $rows)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                            {{ $label }}
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        <th class="py-2 pr-4">{{ __('Event') }}</th>
                                        <th class="py-2 pr-4 text-right">{{ __('Impressions') }}</th>
                                        <th class="py-2 pr-4 text-right">{{ __('Hovers') }}</th>
                                        <th class="py-2 text-right">{{ __('Clicks') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                    @foreach ($rows as $row)
                                        <tr>
                                            <td class="py-2 pr-4 font-mono text-xs">{{ $row['name'] }}</td>
                                            <td class="py-2 pr-4 text-right">{{ number_format($row['impressions']) }}</td>
                                            <td class="py-2 pr-4 text-right">{{ number_format($row['hovers']) }}</td>
                                            <td class="py-2 text-right">{{ number_format($row['clicks']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
