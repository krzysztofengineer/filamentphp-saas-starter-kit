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
                ->heading('The Filament SaaS starter kit ')
                ->headingAccent("you're already looking at.")
                ->description('Built on Filament v5 end to end, from the team workspace to this landing page. Multi-tenant teams, per-team Stripe billing, and a Pest browser test for every flow.')
                ->primaryCta('Get started for free', $registerUrl)
                ->secondaryCta('Star on GitHub', $githubUrl)
                ->command($command)
                ->host('filaas.com')
                ->stackPills([
                    'Laravel 13',
                    'Filament v5',
                    'Livewire v4',
                    'Cashier 16',
                    'Web Push',
                    'PWA',
                    'Pest 4',
                    'Tailwind v4',
                ])
                ->showInception(),

            FeaturesSection::make()
                ->eyebrow("/ what's inside")
                ->heading('The boring half, already wired up.')
                ->description('Every Laravel SaaS rebuilds the same plumbing: auth, teams, billing, settings, tests. We built it once on top of Filament so you can spend your time on the product.')
                ->cards([
                    [
                        'title' => 'Multi-tenant teams',
                        'body' => 'Teams are the tenant. Owner, admin and manager roles, email invitations, ownership transfer. Each team has its own data, members, and billing, scoped through Filament tenancy.',
                        'visual' => 'org-chart',
                        'span' => 'b-1',
                    ],
                    [
                        'title' => 'Per-team Stripe subscriptions',
                        'body' => 'Cashier 16 lives on the team model, so each workspace owns its own subscription. Monthly and yearly plans, hosted checkout, the customer portal, and signed webhook handling. Drop your price IDs into <code>.env</code> and you\'re done.',
                        'visual' => 'billing',
                        'span' => 'b-2',
                    ],
                    [
                        'title' => 'Filament, top to bottom',
                        'body' => 'Account, Team and Billing settings are Filament v5 clusters in the team workspace. So is this landing page, built from the same <code>Component</code> classes (Hero, Features, Pricing, FAQ). The whole frontend runs on one stack.',
                        'visual' => 'filament',
                        'span' => 'b-3',
                    ],
                    [
                        'title' => 'PWA and Web Push',
                        'body' => 'Service worker, manifest, install prompt, offline page, and VAPID push subscriptions. The bits that usually take a weekend to wire up are already wired up.',
                        'visual' => 'pwa',
                        'span' => 'b-4',
                        'data' => [
                            'app_host' => 'filaas.com',
                        ],
                    ],
                    [
                        'title' => 'Account self-service',
                        'body' => 'Members manage their own profile, password and avatar. Account deletion is soft and scheduled with a 30-day grace window; a daily job purges accounts after that, and users can cancel before it fires.',
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
                        'title' => 'Friendly to AI coding tools',
                        'body' => 'Laravel Boost is configured as an MCP server, with a project <code>CLAUDE.md</code> and the Pest, Filament and Tailwind skills already in place. Claude Code can read your schema, search the docs, and run your tests, so reshaping a flow is mostly prompting.',
                        'visual' => 'ai',
                        'span' => 'b-7',
                    ],
                    [
                        'title' => 'Single-purpose Actions',
                        'body' => 'Every write operation lives in its own class under <code>app/Actions/</code>: <code>InviteToTeam</code>, <code>TransferTeamOwnership</code>, <code>ScheduleAccountDeletion</code>, and so on. Find the file, change the code, run the tests. There\'s no service-layer maze to dig through.',
                        'visual' => 'actions',
                        'span' => 'b-8',
                    ],
                ]),

            HowItWorksSection::make()
                ->eyebrow('/ how it works')
                ->heading("Three commands and you're on your product.")
                ->steps([
                    [
                        'kicker' => 'scaffold',
                        'title' => 'Create the project',
                        'description' => 'Composer pulls the kit and runs the post-install hooks.',
                        'command' => $command,
                    ],
                    [
                        'kicker' => 'install',
                        'title' => 'Migrate and build',
                        'description' => 'Run the migrations and build the frontend. SQLite by default, so you can poke around with no config.',
                        'command' => 'php artisan migrate && npm run dev',
                    ],
                    [
                        'kicker' => 'ship',
                        'title' => 'Open <code class="inline">/</code>',
                        'description' => "You'll land on this page. Edit <code class=\"inline\">app/Filament/Home/Pages/Home.php</code> to make it yours, then start building the rest.",
                        'command' => 'open http://localhost:8000',
                    ],
                ]),

            PricingSection::make()
                ->eyebrow('/ pricing')
                ->tagline('priced by what your project earns')
                ->heading('Free until you make money.')
                ->description('Every tier ships the same code; only the license changes. Free covers projects under $1k/month, paid tiers extend the cap as your revenue grows.')
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
