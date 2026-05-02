<x-filament-panels::page>
    <div x-data="{ interval: @js($this->defaultInterval()) }" class="space-y-6">
        <div class="flex justify-center">
            <div class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-white p-1 dark:border-gray-700 dark:bg-gray-900">
                <button
                    type="button"
                    x-on:click="interval = 'monthly'"
                    x-bind:class="interval === 'monthly'
                        ? 'bg-(--primary-600) text-white shadow-sm'
                        : 'text-gray-600 hover:text-gray-950 dark:text-gray-400 dark:hover:text-white'"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition"
                    data-testid="billing-toggle-monthly"
                >Monthly</button>
                <button
                    type="button"
                    x-on:click="interval = 'yearly'"
                    x-bind:class="interval === 'yearly'
                        ? 'bg-(--primary-600) text-white shadow-sm'
                        : 'text-gray-600 hover:text-gray-950 dark:text-gray-400 dark:hover:text-white'"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition"
                    data-testid="billing-toggle-yearly"
                >Yearly</button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->plansForView() as $plan)
                @php
                    $monthlyPrice = $plan['prices']['monthly'] ?? [];
                    $yearlyPrice = $plan['prices']['yearly'] ?? [];
                @endphp
                <div
                    x-bind:class="(interval === 'monthly' && {{ ($monthlyPrice['is_current'] ?? false) ? 'true' : 'false' }}) || (interval === 'yearly' && {{ ($yearlyPrice['is_current'] ?? false) ? 'true' : 'false' }})
                        ? 'ring-2 ring-(--primary-500) shadow-lg'
                        : 'border border-gray-200 dark:border-gray-800'"
                    class="relative flex flex-col rounded-2xl bg-white p-5 sm:p-6 dark:bg-gray-900"
                    data-testid="plan-{{ $plan['key'] }}"
                >
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white">{{ $plan['name'] }}</h3>

                    @if (! empty($plan['description']))
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{!! $plan['description'] !!}</p>
                    @endif

                    <div class="mt-3 min-h-[3rem]">
                        <div x-show="interval === 'yearly'" x-cloak>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-3xl font-bold text-gray-950 dark:text-white">{{ $yearlyPrice['label'] ?? '' }}</span>
                                @if (! empty($yearlyPrice['savings']))
                                    <x-filament::badge color="primary" size="xs">{{ $yearlyPrice['savings'] }}</x-filament::badge>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $yearlyPrice['period'] ?? '' }}</div>
                        </div>
                        <div x-show="interval === 'monthly'">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-3xl font-bold text-gray-950 dark:text-white">{{ $monthlyPrice['label'] ?? '' }}</span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $monthlyPrice['period'] ?? '' }}</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div x-show="interval === 'monthly'">
                            @if ($monthlyPrice['is_current'] ?? false)
                                @if ($plan['portal_url'] ?? null)
                                    <x-filament::button tag="a" :href="$plan['portal_url']" color="primary" outlined data-testid="manage-subscription-monthly" class="w-full">
                                        Manage subscription
                                    </x-filament::button>
                                @else
                                    <div class="rounded-lg bg-(--primary-50) px-3 py-2.5 text-center text-xs font-semibold text-(--primary-700) dark:bg-(--primary-500)/10 dark:text-(--primary-300)">
                                        Your current plan
                                    </div>
                                @endif
                            @elseif ($monthlyPrice['checkout_url'] ?? null)
                                <x-filament::button tag="a" :href="$monthlyPrice['checkout_url']" color="primary" data-testid="checkout-{{ $plan['key'] }}-monthly" class="w-full">
                                    Choose plan
                                </x-filament::button>
                            @endif
                        </div>

                        <div x-show="interval === 'yearly'" x-cloak>
                            @if ($yearlyPrice['is_current'] ?? false)
                                @if ($plan['portal_url'] ?? null)
                                    <x-filament::button tag="a" :href="$plan['portal_url']" color="primary" outlined data-testid="manage-subscription-yearly" class="w-full">
                                        Manage subscription
                                    </x-filament::button>
                                @else
                                    <div class="rounded-lg bg-(--primary-50) px-3 py-2.5 text-center text-xs font-semibold text-(--primary-700) dark:bg-(--primary-500)/10 dark:text-(--primary-300)">
                                        Your current plan
                                    </div>
                                @endif
                            @elseif ($yearlyPrice['checkout_url'] ?? null)
                                <x-filament::button tag="a" :href="$yearlyPrice['checkout_url']" color="primary" data-testid="checkout-{{ $plan['key'] }}-yearly" class="w-full">
                                    Choose plan
                                </x-filament::button>
                            @endif
                        </div>
                    </div>

                    <ul class="mt-5 flex flex-col gap-2 text-sm text-gray-700 dark:text-gray-300">
                        @foreach (($plan['features'] ?? []) as $feature)
                            <li class="flex gap-2">
                                <x-filament::icon icon="heroicon-m-check" class="mt-0.5 size-4 shrink-0 text-(--primary-600) dark:text-(--primary-400)" />
                                <span>{!! $feature !!}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
