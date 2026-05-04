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
                ->eyebrow('v1.0 — Laravel 13 · Filament v5 · Livewire v4')
                ->heading("The SaaS starter kit you're ")
                ->headingAccent('already looking at.')
                ->description('Multi-tenant teams, per-team Stripe subscriptions, Filament-powered admin — and this landing page, built from the same Filament components. Every flow browser-tested with Pest 4.')
                ->primaryCta('Get started for free', $registerUrl)
                ->secondaryCta('Star on GitHub', $githubUrl)
                ->command($command, '↑ this is a real command. it works.')
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
                ->heading('Three weeks of plumbing, already plumbed.')
                ->description('Every Laravel SaaS needs the same boring foundation. We built it once, tested it, and put your name on it. You bring the product.')
                ->cards([
                    [
                        'title' => 'Multi-tenant teams, done properly',
                        'body' => 'Teams as tenants. Owners, administrators, managers, invitations by email, ownership transfer. Each team has its own data, its own members, its own billing — no leaks across boundaries.',
                        'visual' => 'org-chart',
                        'span' => 'b-1',
                    ],
                    [
                        'title' => 'Per-team subscriptions on Stripe',
                        'body' => 'Cashier 16 attached to the team — not the user. Monthly + yearly toggle, hosted checkout, customer portal, signed webhooks. Plug your price IDs into <code>.env</code>, ship.',
                        'visual' => 'billing',
                        'span' => 'b-2',
                    ],
                    [
                        'title' => 'Filament admin — and Filament landing',
                        'body' => 'Account, Team, and Billing pages live as Filament v5 clusters. The page you\'re on right now is built from the same <code>Component</code> classes — Hero, Features, Pricing, FAQ. One mental model for app and marketing.',
                        'visual' => 'filament',
                        'span' => 'b-3',
                    ],
                    [
                        'title' => 'PWA + Web Push, no plumbing',
                        'body' => 'Service worker, manifest, install prompt, offline page, VAPID push subscriptions. Ship the bell icon, not the boilerplate.',
                        'visual' => 'pwa',
                        'span' => 'b-4',
                        'data' => [
                            'app_host' => 'filaas.com',
                        ],
                    ],
                    [
                        'title' => 'Account self-service that respects user data',
                        'body' => 'Members manage their own profile, password, and avatar. Account deletion is soft + scheduled — a daily job purges accounts past the 30-day grace window. Cancel anytime before the timer runs out.',
                        'visual' => 'realtime',
                        'span' => 'b-5',
                    ],
                    [
                        'title' => 'Browser-tested with Pest 4. Every flow.',
                        'body' => 'Pest 4 browser tests drive a real Chromium across registration, login, account settings, team details, members, invitations, ownership transfer, billing, and a full smoke pass. Customize anything — the suite tells you the moment something breaks.',
                        'visual' => 'terminal',
                        'span' => 'b-6',
                    ],
                    [
                        'title' => 'AI-ready out of the box',
                        'body' => 'Laravel Boost (MCP), <code>CLAUDE.md</code>, and Pest/Filament/Tailwind skills are wired up. Ask Claude Code to retune a flow, change an Action, or add a feature — it has the schema, the docs, and the test runner on hand.',
                        'visual' => 'ai',
                        'span' => 'b-7',
                    ],
                    [
                        'title' => 'Action pattern. One purpose per class. Yours to edit.',
                        'body' => 'Every write — invite a member, transfer ownership, schedule deletion — is a single class in <code>app/Actions/</code>. Open one, change it, you\'re done. No service-layer scavenger hunt, no hidden orchestration. Pair it with Boost + the Pest suite and any tweak is a five-minute job.',
                        'visual' => 'actions',
                        'span' => 'b-8',
                    ],
                ]),

            HowItWorksSection::make()
                ->eyebrow('/ how it works')
                ->heading("Three commands. Then you're on your product.")
                ->steps([
                    [
                        'kicker' => 'scaffold',
                        'title' => 'Create the project',
                        'description' => 'Composer pulls the kit, copies <code class="inline">.env</code>, and runs the post-install hooks.',
                        'command' => $command,
                    ],
                    [
                        'kicker' => 'install',
                        'title' => 'Migrate &amp; build',
                        'description' => 'Database, seeders, frontend assets. SQLite by default — zero config to look around.',
                        'command' => 'php artisan migrate && npm run dev',
                    ],
                    [
                        'kicker' => 'ship',
                        'title' => 'Open <code class="inline">/</code>',
                        'description' => "You'll see this page. Edit <code class=\"inline\">resources/views/filament/home/pages/home.blade.php</code>. Start building the actual product.",
                        'command' => 'open http://localhost:8000',
                    ],
                ]),

            PricingSection::make()
                ->eyebrow('/ pricing')
                ->tagline('priced by your project, not by ours.')
                ->heading('Free until you make money. Pay when it works.')
                ->description('All tiers ship the same code. Pricing scales with the revenue your project earns — not with seats or apps.')
                ->footnote('prices in USD · taxes added at checkout · cancel anytime')
                ->plans(collect(config('billing.plans', []))
                    ->map(fn (array $plan, string $key): array => ['key' => $key, ...$plan])
                    ->values()
                    ->all())
                ->freeCta('Get started for free', $registerUrl),

            FaqSection::make()
                ->eyebrow('/ faq')
                ->heading('Things devs actually ask.')
                ->items([
                    [
                        'question' => 'Why does the demo look like the kit?',
                        'answer' => 'Because it <em>is</em> the kit. The page you\'re reading is the same Filament home page that ships with '.$appName.'. Fewer surprises after install.',
                    ],
                    [
                        'question' => 'What does the revenue cap mean?',
                        'answer' => 'Free is for projects under $1k/month. Pro covers you up to $10k MRR. Studio removes the cap. Same code on every tier — only the license changes.',
                    ],
                    [
                        'question' => 'Do I get updates?',
                        'answer' => 'All paid tiers get continuous updates as long as your subscription is active. Updates ship via Composer, semver, with a changelog.',
                    ],
                    [
                        'question' => 'Can I remove the '.$appName.' branding?',
                        'answer' => 'Yes — it\'s your codebase. Rename, restyle, swap the logo, ship it as your own. There\'s no attribution requirement on any tier.',
                    ],
                    [
                        'question' => 'What if my project crosses the cap later?',
                        'answer' => 'Upgrade in place. The code stays put — you just move to the tier that matches your revenue. No reinstall, no migration.',
                    ],
                    [
                        'question' => "What's the license?",
                        'answer' => 'MIT-style up to your tier\'s cap, plus a commercial addendum for paid tiers. Plain English version in the repo. Read it before you buy — we mean it.',
                    ],
                ]),

            FinalCtaSection::make()
                ->heading('Ship the page ')
                ->headingAccent("you're reading.")
                ->description("One command. SQLite by default. You'll be looking at your own copy of this in under a minute.")
                ->command($command)
                ->primaryCta('Get started for free', $registerUrl)
                ->secondaryCta('Star on GitHub', $githubUrl),

            MarketingFooter::make()
                ->brand($appName)
                ->tagline('The Laravel SaaS starter kit you\'re already looking at. Built to be replaced by your product.')
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
                ->copyright('© '.date('Y').' '.$appName.'. ships as-is, ships well.'),
        ]);
    }
}
