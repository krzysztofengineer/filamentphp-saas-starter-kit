<?php

namespace App\Filament\Home\Pages;

use App\Filament\Schemas\Components\Marketing\FaqSection;
use App\Filament\Schemas\Components\Marketing\FeaturesSection;
use App\Filament\Schemas\Components\Marketing\FinalCtaSection;
use App\Filament\Schemas\Components\Marketing\HeroSection;
use App\Filament\Schemas\Components\Marketing\HowItWorksSection;
use App\Filament\Schemas\Components\Marketing\MarketingFooter;
use App\Filament\Schemas\Components\Marketing\PricingSection;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class Home extends Page
{
    protected static ?string $slug = '/';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.home.pages.home';

    public function mount(): void
    {
        if (auth()->check()) {
            $this->redirect(Filament::getPanel('app')->getUrl());
        }
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function getTitle(): string|Htmlable
    {
        return 'SAAS — Build your next product faster';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public function content(Schema $schema): Schema
    {
        $registerUrl = route('filament.app.auth.register');

        return $schema->components([
            HeroSection::make()
                ->heading('Build your SaaS faster')
                ->description('A reusable, generic SaaS template with authentication, multi-tenant teams, Stripe billing and member invitations — ready out of the box.')
                ->primaryCta('Get started for free', $registerUrl)
                ->secondaryCta('See features', '#features'),

            FeaturesSection::make()
                ->tagline('Features')
                ->heading('Everything you need on day one.')
                ->description('Stop reinventing the basics. Focus on what makes your product unique.')
                ->features([
                    ['icon' => 'heroicon-o-lock-closed', 'title' => 'Authentication', 'description' => 'Email + password sign-in, sign-up, and password reset out of the box.'],
                    ['icon' => 'heroicon-o-users', 'title' => 'Multi-tenant teams', 'description' => 'Each user can own and belong to multiple teams. Switch between tenants seamlessly.'],
                    ['icon' => 'heroicon-o-credit-card', 'title' => 'Stripe billing', 'description' => 'Stripe Checkout, subscriptions and the customer portal — wired through Laravel Cashier.'],
                    ['icon' => 'heroicon-o-envelope', 'title' => 'Team invitations', 'description' => 'Invite teammates by email and have them join with a single click.'],
                    ['icon' => 'heroicon-o-cog-6-tooth', 'title' => 'Account & team settings', 'description' => 'Profile, password, billing, advanced settings — and the same for each team.'],
                    ['icon' => 'heroicon-o-shield-check', 'title' => 'Filament admin', 'description' => 'Built on top of the Filament panel framework — easy to extend.'],
                ]),

            HowItWorksSection::make()
                ->tagline('How it works')
                ->heading('Three steps to get started.')
                ->steps([
                    ['icon' => 'heroicon-o-user-plus', 'title' => 'Sign up', 'description' => 'Create your account with email and password. No credit card required.'],
                    ['icon' => 'heroicon-o-building-office-2', 'title' => 'Create a team', 'description' => 'Each team is an isolated tenant with its own data, members and settings.'],
                    ['icon' => 'heroicon-o-rocket-launch', 'title' => 'Invite & ship', 'description' => 'Invite teammates by email and start building your product.'],
                ]),

            PricingSection::make()
                ->tagline('Pricing')
                ->heading('Simple, transparent pricing.')
                ->description('Start for free, upgrade when you need more.')
                ->plans([
                    [
                        'name' => 'Free',
                        'badge' => null,
                        'price' => '$0',
                        'period' => 'forever',
                        'description' => 'Everything to try out the template.',
                        'cta' => 'Sign up free',
                        'href' => $registerUrl,
                        'featured' => false,
                        'features' => ['1 team', 'Core SaaS features', 'Community support'],
                    ],
                    [
                        'name' => 'Starter',
                        'badge' => 'Most popular',
                        'price' => '$9',
                        'period' => 'per month',
                        'description' => 'For growing teams that need more flexibility.',
                        'cta' => 'Choose Starter',
                        'href' => $registerUrl,
                        'featured' => true,
                        'features' => ['Unlimited teams', 'Unlimited member invitations', 'Email support (48h)'],
                    ],
                    [
                        'name' => 'Pro',
                        'badge' => null,
                        'price' => '$29',
                        'period' => 'per month',
                        'description' => 'For teams that need priority support and advanced features.',
                        'cta' => 'Choose Pro',
                        'href' => $registerUrl,
                        'featured' => false,
                        'features' => ['Everything in Starter', 'Priority support', 'Advanced analytics'],
                    ],
                ]),

            FaqSection::make()
                ->tagline('FAQ')
                ->heading('Frequently asked questions.')
                ->items([
                    ['question' => 'Is there a free plan?', 'answer' => 'Yes — the Free plan is free forever and includes the full set of core features for a single team.'],
                    ['question' => 'What is included in the template?', 'answer' => 'Authentication, multi-tenant teams, Stripe billing, account and team settings, member invitations, and a Filament admin panel.'],
                    ['question' => 'How does multi-tenancy work?', 'answer' => 'Each Team is an isolated tenant. Users can belong to multiple teams and switch between them from the topbar.'],
                    ['question' => 'Can I cancel anytime?', 'answer' => 'Yes — you can manage your subscription and cancel at any time from the Stripe Customer Portal.'],
                ]),

            FinalCtaSection::make()
                ->heading('Ready to get started?')
                ->description('Sign up in seconds. No credit card required.')
                ->primaryCta('Create your free account', $registerUrl)
                ->secondaryCta('Compare plans', '#pricing'),

            MarketingFooter::make()
                ->links([
                    ['label' => 'Privacy', 'url' => '/privacy'],
                    ['label' => 'Terms', 'url' => '/terms'],
                ]),
        ]);
    }
}
