<?php

namespace App\Filament\Clusters\AccountSettings\Pages;

use App\BillingInterval;
use App\BillingPlan;
use App\Filament\Clusters\AccountSettings\AccountSettingsCluster;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class AccountBilling extends Page
{
    protected static ?string $cluster = AccountSettingsCluster::class;

    protected static ?string $slug = 'subscription';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.clusters.account-settings.pages.account-billing';

    public static function canAccess(): bool
    {
        return AccountSettingsCluster::canAccess();
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

    /**
     * @return array<int, array<string, mixed>>
     */
    public function plansForView(): array
    {
        /** @var User $user */
        $user = auth()->user();

        $hasSubscription = (bool) $user?->subscribed('default');
        $portalUrl = $hasSubscription ? route('billing.portal') : null;

        return [
            [
                'key' => 'free',
                'name' => 'Free',
                'badge' => null,
                'monthlyPrice' => '$0',
                'yearlyPrice' => '$0',
                'monthlyPeriod' => 'forever',
                'yearlyPeriod' => 'forever',
                'isCurrentMonthly' => ! $hasSubscription,
                'isCurrentYearly' => ! $hasSubscription,
                'monthlyUrl' => null,
                'yearlyUrl' => null,
                'portalUrl' => null,
                'features' => [
                    '1 team',
                    'Core SaaS features',
                    'Community support',
                ],
            ],
            [
                'key' => 'starter',
                'name' => 'Starter',
                'badge' => null,
                'monthlyPrice' => '$9',
                'yearlyPrice' => '$90',
                'monthlyPeriod' => 'per month',
                'yearlyPeriod' => 'per year',
                'isCurrentMonthly' => $this->isCurrentForInterval(BillingPlan::Starter, BillingInterval::Monthly),
                'isCurrentYearly' => $this->isCurrentForInterval(BillingPlan::Starter, BillingInterval::Yearly),
                'monthlyUrl' => $this->checkoutUrl(BillingPlan::Starter, BillingInterval::Monthly),
                'yearlyUrl' => $this->checkoutUrl(BillingPlan::Starter, BillingInterval::Yearly),
                'portalUrl' => $portalUrl,
                'features' => [
                    'Unlimited teams',
                    'Unlimited member invitations',
                    'Email support (48h)',
                ],
            ],
            [
                'key' => 'pro',
                'name' => 'Pro',
                'badge' => null,
                'monthlyPrice' => '$29',
                'yearlyPrice' => '$290',
                'monthlyPeriod' => 'per month',
                'yearlyPeriod' => 'per year',
                'isCurrentMonthly' => $this->isCurrentForInterval(BillingPlan::Pro, BillingInterval::Monthly),
                'isCurrentYearly' => $this->isCurrentForInterval(BillingPlan::Pro, BillingInterval::Yearly),
                'monthlyUrl' => $this->checkoutUrl(BillingPlan::Pro, BillingInterval::Monthly),
                'yearlyUrl' => $this->checkoutUrl(BillingPlan::Pro, BillingInterval::Yearly),
                'portalUrl' => $portalUrl,
                'features' => [
                    'Everything in Starter',
                    'Priority support',
                    'Advanced analytics',
                ],
            ],
        ];
    }

    protected function isCurrentForInterval(BillingPlan $plan, BillingInterval $interval): bool
    {
        /** @var User $user */
        $user = auth()->user();

        $priceId = $plan->priceId($interval);

        return $priceId && $user?->subscribedToPrice($priceId, 'default') === true;
    }

    protected function checkoutUrl(BillingPlan $plan, BillingInterval $interval): string
    {
        return route('billing.checkout', ['plan' => $plan->value, 'interval' => $interval->value]);
    }

    public function defaultInterval(): string
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user?->subscribed('default')) {
            return 'monthly';
        }

        foreach ([BillingPlan::Starter, BillingPlan::Pro] as $plan) {
            $yearlyPriceId = $plan->priceId(BillingInterval::Yearly);

            if ($yearlyPriceId && $user->subscribedToPrice($yearlyPriceId, 'default')) {
                return 'yearly';
            }
        }

        return 'monthly';
    }
}
