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
        return 'FilamentPHP/Laravel SaaS starter kit';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public function content(Schema $schema): Schema
    {
        $appName = (string) config('app.name');
        $command = 'composer create-project filaascom/filaas my-app';
        $registerUrl = route('filament.app.auth.register');
        $githubUrl = 'https://github.com/filaascom/filaas';

        return $schema->components([
            HeroSection::make()
                ->eyebrow('Filament v5  ·  Laravel 13  ·  Livewire v4  ·  v1.0')
                ->heading('The FilamentPHP')
                ->headingAccent('SAAS starter kit ')
                ->description('Ready to deploy SAAS starter kit with landing page, multi-tenancy and billing.')
                ->primaryCta('Get started for free', $registerUrl)
                ->secondaryCta('Star on GitHub', $githubUrl)
                ->command($command)
                ->host('filaas.com')
                ->stackPills([
                    'Laravel 13',
                    'Filament v5',
                    'Livewire v4',
                    'Web Push',
                    'PWA',
                    'Pest 4',
                ])
                ->showInception(),

            FeaturesSection::make()
                ->eyebrow("/ what's inside")
                ->heading('Already wired up.')
                ->description('Every Laravel SAAS rebuilds the same stuff: auth, teams, billing, settings, tests. We built it once on top of Filament so you can spend your time on the product.')
                ->cards([
                    [
                        'title' => 'Multi-tenancy',
                        'body' => 'Personal and company teams with owners and roles. Invitations, ownership transfer, billing and scoped data.',
                        'visual' => 'org-chart',
                        'span' => 'b-1',
                    ],
                    [
                        'title' => 'Stripe subscriptions',
                        'body' => 'Monthly and yearly plans, hosted checkout, the customer portal, and signed webhook handling. Drop your Stripe price IDs into <code>.env</code> and you\'re done.',
                        'visual' => 'billing',
                        'span' => 'b-2',
                    ],
                    [
                        'title' => 'Based on FilamentPHP',
                        'body' => 'Account, Team and Billing settings are Filament v5 clusters. Even this landing page is built from FilamentPHP-style components you can configure for your use case.',
                        'visual' => 'filament',
                        'span' => 'b-3',
                    ],
                    [
                        'title' => 'PWA and Web Push',
                        'body' => 'Service worker, manifest, install prompt, offline page, and VAPID push subscriptions. ',
                        'visual' => 'pwa',
                        'span' => 'b-4',
                        'data' => [
                            'app_host' => 'filaas.com',
                        ],
                    ],
                    [
                        'title' => 'Profile settings',
                        'body' => 'Members manage their own profile, password and avatar. Account deletion for GDPR compatibility.',
                        'visual' => 'realtime',
                        'span' => 'b-5',
                    ],
                    [
                        'title' => 'A Pest browser test for every flow',
                        'body' => 'Pest 4 drives a real Chromium through registration, login, account settings, team flows, invitations, ownership transfer and billing, with a smoke pass over every page. When you change something, the suite tells you what broke.',
                        'visual' => 'terminal',
                        'span' => 'b-6',
                    ],
                    [
                        'title' => 'Laravel Boost',
                        'body' => 'You can ask your AI agent to quickly customize the whole app for your SAAS.',
                        'visual' => 'ai',
                        'span' => 'b-7',
                    ],
                    [
                        'title' => 'Action pattern',
                        'body' => 'Most actions are customizable through single use case actions.',
                        'visual' => 'actions',
                        'span' => 'b-8',
                    ],
                ]),

            HowItWorksSection::make()
                ->eyebrow('/ how it works')
                ->heading('Quickest way to market')
                ->steps([
                    [
                        'kicker' => 'scaffold',
                        'title' => 'Create the project',
                        'description' => 'Pull via composer',
                        'command' => $command,
                    ],
                    [
                        'kicker' => 'install',
                        'title' => 'Migrate and build',
                        'description' => 'Run the migrations and start a dev environment',
                        'command' => 'php artisan migrate && make',
                    ],
                    [
                        'kicker' => 'ship',
                        'title' => 'Open <code class="inline">/</code>',
                        'description' => 'Ready to develop',
                        'command' => 'herd open',
                    ],
                ]),

            PricingSection::make()
                ->eyebrow('/ pricing')
                ->heading('Free until you make money.')
                ->description('Free under $1k/month')
                ->footnote('prices in USD · taxes added at checkout · cancel anytime')
                ->plans(collect(config('billing.plans', []))
                    ->map(fn (array $plan, string $key): array => ['key' => $key, ...$plan])
                    ->values()
                    ->all())
                ->freeCta('Get started for free', $registerUrl),

            FaqSection::make()
                ->eyebrow('/ faq')
                ->heading('Things devs actually ask')
                ->items([
                    [
                        'question' => 'Why does the demo look like the kit?',
                        'answer' => 'Because it <em>is</em> the kit. The page you\'re reading is the same Filament home page that ships with '.$appName.', so what you see in the demo is what you get on install.',
                    ],
                    [
                        'question' => 'What does the revenue cap mean?',
                        'answer' => 'Free is for projects under $1k/month, Pro covers you up to $10k MRR, and Studio removes the cap. The code is the same on every tier; only the license changes.',
                    ],
                    [
                        'question' => 'Do I get updates?',
                        'answer' => 'Paid tiers get continuous updates while the subscription is active. Updates ship via Composer with semver and a changelog.',
                    ],
                    [
                        'question' => 'Can I remove the '.$appName.' branding?',
                        'answer' => 'Yes, it\'s your codebase. Rename it, restyle it, swap the logo. There\'s no attribution requirement on any tier.',
                    ],
                    [
                        'question' => 'What if my project crosses the cap later?',
                        'answer' => 'Upgrade in place. The code stays put; you just move to the tier that matches your revenue. No reinstall, no migration.',
                    ],
                    [
                        'question' => "What's the license?",
                        'answer' => 'MIT-style up to your tier\'s cap, with a commercial addendum on paid tiers. The plain-English version is in the repo, and it\'s worth a read before you buy.',
                    ],
                ]),

            FinalCtaSection::make()
                ->heading('Ship the page ')
                ->headingAccent("you're reading.")
                ->description('One Composer command, SQLite by default. Your own copy is running locally in about a minute.')
                ->command($command)
                ->primaryCta('Get started for free', $registerUrl)
                ->secondaryCta('Star on GitHub', $githubUrl),

            MarketingFooter::make()
                ->brand($appName)
                ->tagline('A Filament-powered Laravel SaaS starter kit. Replace it with your product.')
                ->linkColumns([
                    [
                        'heading' => 'Product',
                        'links' => [
                            ['label' => 'Features', 'url' => '#features'],
                            ['label' => 'Pricing', 'url' => '#pricing'],
                            ['label' => 'How it works', 'url' => '#how-it-works'],
                            ['label' => 'FAQ', 'url' => '#faq'],
                        ],
                    ],
                    [
                        'heading' => 'Resources',
                        'links' => [
                            ['label' => 'GitHub', 'url' => $githubUrl],
                        ],
                    ],
                    [
                        'heading' => 'Legal',
                        'links' => [
                            ['label' => 'Terms', 'url' => '/terms'],
                            ['label' => 'Privacy', 'url' => '/privacy'],
                        ],
                    ],
                ])
                ->copyright('© '.date('Y').' '.$appName),
        ]);
    }
}
