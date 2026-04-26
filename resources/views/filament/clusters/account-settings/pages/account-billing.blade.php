<x-filament-panels::page>
    <div x-data="{ interval: @js($this->defaultInterval()) }" class="space-y-6">
        <div class="flex justify-center">
            <div class="inline-flex rounded-full border border-gray-200 bg-white p-1 dark:border-gray-800 dark:bg-gray-900">
                <button
                    type="button"
                    x-on:click="interval = 'monthly'"
                    x-bind:class="interval === 'monthly' ? 'bg-primary-600 text-white' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'"
                    class="rounded-full px-4 py-1.5 text-xs sm:text-sm font-semibold transition"
                    data-testid="billing-toggle-monthly"
                >
                    Monthly
                </button>
                <button
                    type="button"
                    x-on:click="interval = 'yearly'"
                    x-bind:class="interval === 'yearly' ? 'bg-primary-600 text-white' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'"
                    class="rounded-full px-4 py-1.5 text-xs sm:text-sm font-semibold transition"
                    data-testid="billing-toggle-yearly"
                >
                    Yearly
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->plansForView() as $plan)
                <div
                    x-bind:class="(interval === 'monthly' && {{ $plan['isCurrentMonthly'] ? 'true' : 'false' }}) || (interval === 'yearly' && {{ $plan['isCurrentYearly'] ? 'true' : 'false' }})
                        ? 'ring-2 ring-primary-500 shadow-lg shadow-primary-500/10'
                        : 'border border-gray-200 dark:border-gray-800'"
                    class="relative flex flex-col rounded-2xl bg-white p-5 sm:p-6 dark:bg-gray-900"
                    data-testid="plan-{{ $plan['key'] }}"
                >
                    @if ($plan['badge'])
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full bg-primary-600 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-white">
                            {{ $plan['badge'] }}
                        </span>
                    @endif

                    <h3 class="text-lg font-bold text-gray-950 dark:text-white">{{ $plan['name'] }}</h3>

                    <div class="mt-3 min-h-[3rem]">
                        <div x-show="interval === 'yearly'">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-3xl font-bold text-gray-950 dark:text-white">{{ $plan['yearlyPrice'] }}</span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $plan['yearlyPeriod'] }}</div>
                        </div>
                        <div x-show="interval === 'monthly'" x-cloak>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-3xl font-bold text-gray-950 dark:text-white">{{ $plan['monthlyPrice'] }}</span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $plan['monthlyPeriod'] }}</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div x-show="interval === 'monthly'">
                            @if ($plan['isCurrentMonthly'])
                                @if ($plan['portalUrl'])
                                    <a
                                        href="{{ $plan['portalUrl'] }}"
                                        data-testid="manage-subscription-monthly"
                                        class="block w-full rounded-lg border border-primary-600 bg-transparent px-4 py-2.5 text-center text-sm font-semibold text-primary-600 transition hover:bg-primary-50 dark:border-primary-500 dark:text-primary-400 dark:hover:bg-primary-500/10"
                                    >
                                        Manage subscription
                                    </a>
                                @else
                                    <div class="rounded-lg bg-primary-50 px-3 py-2.5 text-center text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-300">
                                        Your current plan
                                    </div>
                                @endif
                            @elseif ($plan['monthlyUrl'])
                                <a
                                    href="{{ $plan['monthlyUrl'] }}"
                                    data-testid="checkout-{{ $plan['key'] }}-monthly"
                                    class="block w-full rounded-lg bg-primary-600 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-primary-700"
                                >
                                    Choose plan
                                </a>
                            @endif
                        </div>

                        <div x-show="interval === 'yearly'" x-cloak>
                            @if ($plan['isCurrentYearly'])
                                @if ($plan['portalUrl'])
                                    <a
                                        href="{{ $plan['portalUrl'] }}"
                                        data-testid="manage-subscription-yearly"
                                        class="block w-full rounded-lg border border-primary-600 bg-transparent px-4 py-2.5 text-center text-sm font-semibold text-primary-600 transition hover:bg-primary-50 dark:border-primary-500 dark:text-primary-400 dark:hover:bg-primary-500/10"
                                    >
                                        Manage subscription
                                    </a>
                                @else
                                    <div class="rounded-lg bg-primary-50 px-3 py-2.5 text-center text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-300">
                                        Your current plan
                                    </div>
                                @endif
                            @elseif ($plan['yearlyUrl'])
                                <a
                                    href="{{ $plan['yearlyUrl'] }}"
                                    data-testid="checkout-{{ $plan['key'] }}-yearly"
                                    class="block w-full rounded-lg bg-primary-600 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-primary-700"
                                >
                                    Choose plan
                                </a>
                            @endif
                        </div>
                    </div>

                    <ul class="mt-5 flex flex-col gap-2 text-sm text-gray-700 dark:text-gray-300">
                        @foreach ($plan['features'] as $feature)
                            <li class="flex gap-2">
                                <svg class="mt-0.5 size-4 shrink-0 text-primary-600 dark:text-primary-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-8 8.084a1 1 0 0 1-1.42.006L3.29 10.79a1 1 0 0 1 1.42-1.408l3.29 3.316 7.29-7.36a1 1 0 0 1 1.414-.048Z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
