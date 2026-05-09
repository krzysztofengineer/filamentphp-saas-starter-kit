<?php

namespace App\Filament\Clusters\TeamSettings\Pages;

use App\Enums\BillingInterval;
use App\Enums\BillingPlan;
use App\Filament\Clusters\TeamSettings\TeamSettingsCluster;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class TeamBilling extends Page
{
    protected static ?string $cluster = TeamSettingsCluster::class;

    protected static ?string $slug = 'subscription';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.clusters.team-settings.pages.team-billing';

    public static function canAccess(): bool
    {
        return TeamSettingsCluster::canManage();
    }

    public function getTitle(): string
    {
        return 'Subscription';
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Subscription';
    }

    public function plansForView(): array
    {
        $team = Filament::getTenant();
        $hasSubscription = (bool) $team?->subscribed('default');
        $portalUrl = $hasSubscription ? route('billing.portal', ['team' => $team]) : null;

        return collect(config('billing.plans', []))
            ->map(function (array $plan, string $key) use ($team, $portalUrl): array {
                $isFree = (bool) ($plan['is_free'] ?? false);
                $billingPlan = $isFree ? null : BillingPlan::tryFrom($key);

                $prices = collect($plan['prices'] ?? [])
                    ->map(function (array $price, string $intervalKey) use ($team, $billingPlan, $isFree): array {
                        $interval = BillingInterval::tryFrom($intervalKey);
                        $priceId = $billingPlan?->priceId($interval);
                        $isOnThisPrice = $team && $priceId && $team->subscribedToPrice($priceId, 'default') === true;

                        return [
                            ...$price,
                            'is_current' => $isFree
                                ? ! ($team?->subscribed('default') ?? false)
                                : $isOnThisPrice,
                            'checkout_url' => $isFree || ! $billingPlan || ! $interval || ! $team
                                ? null
                                : route('billing.checkout', [
                                    'team' => $team,
                                    'plan' => $billingPlan->value,
                                    'interval' => $interval->value,
                                ]),
                        ];
                    })
                    ->all();

                return [
                    'key' => $key,
                    ...$plan,
                    'prices' => $prices,
                    'portal_url' => $isFree ? null : $portalUrl,
                ];
            })
            ->values()
            ->all();
    }

    public function defaultInterval(): string
    {
        $team = Filament::getTenant();

        if ($team !== null && $team->subscribed('default')) {
            foreach (BillingInterval::cases() as $interval) {
                foreach (config('billing.plans', []) as $plan) {
                    $stripeId = $plan['prices'][$interval->value]['stripe_id'] ?? null;
                    if ($stripeId && $team->subscribedToPrice($stripeId, 'default')) {
                        return $interval->value;
                    }
                }
            }
        }

        return BillingInterval::Monthly->value;
    }
}
